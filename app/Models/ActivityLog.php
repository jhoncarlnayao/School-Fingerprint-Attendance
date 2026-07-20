<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'subject_type',
        'subject_id',
        'description',
        'is_warning',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'is_warning' => 'boolean',
            'read_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Record what the currently logged-in admin (or system) just did.
     * This is what powers the notification bell + the dashboard's
     * "Recent Activity" panel — a real audit trail instead of guesses.
     *
     * Example: ActivityLog::record('updated', 'Student', $student->id, "Admin edited Juan Dela Cruz's enrollment details.");
     */
    public static function record(string $action, ?string $subjectType, ?int $subjectId, string $description, bool $isWarning = false): self
    {
        return static::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'description' => $description,
            'is_warning' => $isWarning,
        ]);
    }

    public function icon(): string
    {
        return match ($this->action) {
            'created' => 'plus',
            'updated' => 'pencil',
            'deleted' => 'trash',
            'assigned' => 'link',
            'emailed' => 'mail',
            'disabled' => 'lock',
            'enabled' => 'unlock',
            default => 'bell',
        };
    }

    public function timeAgo(): string
    {
        return $this->created_at?->diffForHumans() ?? '';
    }
}
