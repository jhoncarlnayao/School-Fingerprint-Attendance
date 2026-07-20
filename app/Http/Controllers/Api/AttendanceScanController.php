<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SendGuardianAttendanceEmail;
use App\Models\AttendanceLog;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AttendanceScanController extends Controller
{
    /**
     * POST /api/attendance/scan
     *
     * Called by the fingerprint scanner's local app/SDK every time someone
     * puts a finger on the sensor. Body: { "fingerprint_id": "...", "scanned_at": "2026-07-20 07:12:00" (optional) }
     *
     * First scan of the day for a student = time in.
     * Second scan of the day = time out.
     * Anything within the dedupe window of the last scan is ignored so a
     * double-tap doesn't create duplicate logs or duplicate guardian emails.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'fingerprint_id' => ['required', 'string', 'max:100'],
            'scanned_at' => ['nullable', 'date'],
        ]);

        $student = Student::where('fingerprint_id', $validated['fingerprint_id'])->first();

        if (! $student) {
            return response()->json([
                'message' => 'No student is enrolled with this fingerprint ID.',
            ], 404);
        }

        $scannedAt = isset($validated['scanned_at']) ? Carbon::parse($validated['scanned_at']) : now();
        $today = $scannedAt->copy()->startOfDay();
        $gapMinutes = (int) config('attendance.scan_gap_minutes', 2);

        $log = AttendanceLog::firstOrNew([
            'student_id' => $student->id,
            'date' => $today->toDateString(),
        ]);

        // Duplicate-tap guard — same student, same sensor, scanned again
        // within the configured gap. Acknowledge but do nothing further.
        if ($log->exists && $log->last_scan_at && $scannedAt->diffInMinutes($log->last_scan_at) < $gapMinutes) {
            return response()->json([
                'message' => 'Duplicate scan ignored.',
                'student' => $student->fullName(),
                'status' => $log->status,
            ]);
        }

        $event = null;

        if (! $log->exists || ! $log->time_in) {
            // First scan today = time in.
            $cutoff = Carbon::parse($today->toDateString().' '.config('attendance.late_cutoff', '07:30'));

            $log->fill([
                'student_id' => $student->id,
                'date' => $today->toDateString(),
                'time_in' => $scannedAt,
                'status' => $scannedAt->greaterThan($cutoff) ? AttendanceLog::STATUS_LATE : AttendanceLog::STATUS_PRESENT,
                'source' => AttendanceLog::SOURCE_FINGERPRINT,
                'last_scan_at' => $scannedAt,
            ]);
            $log->save();

            $event = 'in';
        } elseif (! $log->time_out) {
            // Second scan today = time out.
            $log->fill([
                'time_out' => $scannedAt,
                'last_scan_at' => $scannedAt,
            ]);
            $log->save();

            $event = 'out';
        } else {
            // Third+ tap in one day — just bump the last-scan marker, no new event.
            $log->update(['last_scan_at' => $scannedAt]);
        }

        if ($event && $student->guardian_email) {
            $alreadyNotified = $event === 'out' ? $log->guardian_notified_out_at : $log->guardian_notified_in_at;

            if (! $alreadyNotified) {
                SendGuardianAttendanceEmail::dispatch($log->id, $event);
            }
        }

        return response()->json([
            'message' => 'Scan recorded.',
            'student' => $student->fullName(),
            'event' => $event ?? 'ignored',
            'status' => $log->status,
            'time_in' => $log->timeInFormatted(),
            'time_out' => $log->timeOutFormatted(),
        ]);
    }
}
