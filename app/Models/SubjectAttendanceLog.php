<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubjectAttendanceLog extends Model
{
    use HasFactory;

    public const STATUS_PRESENT = 'present';
    public const STATUS_LATE = 'late';
    public const STATUS_ABSENT = 'absent';
    public const STATUS_EXCUSED = 'excused';

    protected $fillable = [
        'student_id',
        'teacher_id',
        'subject',
        'grade_level',
        'section',
        'date',
        'status',
        'note',
        'guardian_notified_at',
        'guardian_notify_error',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'guardian_notified_at' => 'datetime',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_PRESENT => 'Present',
            self::STATUS_LATE => 'Late',
            self::STATUS_ABSENT => 'Absent',
            self::STATUS_EXCUSED => 'Excused',
        ];
    }

    public function statusLabel(): string
    {
        return self::statuses()[$this->status] ?? ucfirst($this->status);
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

    /**
     * Whether a guardian email should still be sent for this record
     * (only for present/late — an absence here just means "not marked
     * present in this subject", not a school-wide absence).
     */
    public function shouldNotifyGuardian(): bool
    {
        return in_array($this->status, [self::STATUS_PRESENT, self::STATUS_LATE], true);
    }
}
