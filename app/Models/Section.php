<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'grade_level',
        'name',
        'capacity',
    ];

    /**
     * Grade levels the system supports, Junior + Senior High (7–12).
     */
    public static function gradeLevels(): array
    {
        return ['7', '8', '9', '10', '11', '12'];
    }

    /**
     * How many students are currently enrolled in this section.
     */
    public function studentCount(): int
    {
        return Student::where('grade_level', $this->grade_level)
            ->where('section', $this->name)
            ->count();
    }

    /**
     * How many teachers are currently assigned to this section.
     */
    public function teacherCount(): int
    {
        return User::where('role', 'teacher')
            ->where('assigned_grade_level', $this->grade_level)
            ->where('assigned_section', $this->name)
            ->count();
    }

    /**
     * Teachers currently assigned to this section (for the "view" modal).
     */
    public function assignedTeachers()
    {
        return User::where('role', 'teacher')
            ->where('assigned_grade_level', $this->grade_level)
            ->where('assigned_section', $this->name)
            ->orderBy('name')
            ->get();
    }

    /**
     * Students currently enrolled in this section (for the "view" modal).
     */
    public function enrolledStudents()
    {
        return Student::where('grade_level', $this->grade_level)
            ->where('section', $this->name)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
    }

    /**
     * Whether this section can safely be deleted — no students enrolled and
     * no teacher currently assigned to it.
     */
    public function canBeDeleted(): bool
    {
        return $this->studentCount() === 0 && $this->teacherCount() === 0;
    }

    /**
     * Whether this section still has room (null capacity = unlimited).
     */
    public function isFull(): bool
    {
        if (is_null($this->capacity)) {
            return false;
        }

        return $this->studentCount() >= $this->capacity;
    }

    public function label(): string
    {
        return "Grade {$this->grade_level} - {$this->name}";
    }
}
