<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    use HasFactory;

    public const STATUS_PRESENT = 'present';
    public const STATUS_LATE = 'late';
    public const STATUS_ABSENT = 'absent';
    public const STATUS_EXCUSED = 'excused';

    public const SOURCE_FINGERPRINT = 'fingerprint';
    public const SOURCE_MANUAL = 'manual';

    protected $fillable = [
        'student_id',
        'date',
        'time_in',
        'time_out',
        'status',
        'source',
        'note',
        'marked_by',
        'last_scan_at',
        'guardian_notified_in_at',
        'guardian_notified_out_at',
        'guardian_notify_error',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'time_in' => 'datetime',
            'time_out' => 'datetime',
            'last_scan_at' => 'datetime',
            'guardian_notified_in_at' => 'datetime',
            'guardian_notified_out_at' => 'datetime',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function markedBy()
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PRESENT => 'Present',
            self::STATUS_LATE => 'Late',
            self::STATUS_ABSENT => 'Absent',
            self::STATUS_EXCUSED => 'Excused',
            default => ucfirst($this->status),
        };
    }

    public function badgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_PRESENT => 'badge-green',
            self::STATUS_LATE => 'badge-orange',
            self::STATUS_ABSENT, self::STATUS_EXCUSED => 'badge-red',
            default => 'badge-green',
        };
    }

    public function timeInFormatted(): ?string
    {
        return $this->time_in?->format('g:i A');
    }

    public function timeOutFormatted(): ?string
    {
        return $this->time_out?->format('g:i A');
    }
}
