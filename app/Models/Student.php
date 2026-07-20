<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_no',
        'fingerprint_id',
        'first_name',
        'middle_name',
        'last_name',
        'birth_date',
        'grade_level',
        'section',
        'profile_picture',
        'guardian_name',
        'guardian_contact',
        'guardian_email',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    public function fullName(): string
    {
        $middleInitial = $this->middle_name ? ' ' . strtoupper(substr($this->middle_name, 0, 1)) . '.' : '';

        return "{$this->last_name}, {$this->first_name}{$middleInitial}";
    }

    public function profilePictureUrl(): ?string
    {
        return $this->profile_picture ? asset('storage/' . $this->profile_picture) : null;
    }

    public function initials(): string
    {
        return strtoupper(substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1));
    }

    /**
     * Current age in whole years, computed from birth_date. Null if no birthday on file.
     */
    public function age(): ?int
    {
        return $this->birth_date ? Carbon::parse($this->birth_date)->age : null;
    }

    public function birthDateFormatted(): ?string
    {
        return $this->birth_date ? Carbon::parse($this->birth_date)->format('M d, Y') : null;
    }

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function subjectAttendanceLogs()
    {
        return $this->hasMany(SubjectAttendanceLog::class);
    }

    /**
     * Whether this student has a fingerprint template ID on file.
     * (Enrollment itself happens on the scanner's own software — BANTAY
     * just needs the ID so scans can be matched back to this record.)
     */
    public function hasFingerprintOnFile(): bool
    {
        return ! empty($this->fingerprint_id);
    }

    /**
     * Today's attendance record, if one exists yet.
     */
    public function todaysAttendance(): ?AttendanceLog
    {
        return $this->attendanceLogs()->forDate(Carbon::today())->first();
    }
}
