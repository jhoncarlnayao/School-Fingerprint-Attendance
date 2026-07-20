<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    /**
     * Show the "Add Student" form + recent students list.
     * Sections must already exist (admin creates them on the Sections page first).
     */
    public function create()
    {
        $students = Student::latest()->take(20)->get();
        $sections = Section::orderBy('grade_level')->orderBy('name')->get();
        $gradeLevels = Section::gradeLevels();

        return view('admin.students.create', compact('students', 'sections', 'gradeLevels'));
    }

    /**
     * Save a new student.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateStudent($request);

        $section = Section::where('grade_level', $validated['grade_level'])
            ->where('name', $validated['section'])
            ->first();

        if ($section && $section->isFull()) {
            return back()->withErrors(['section' => "\"{$section->label()}\" is already at full capacity ({$section->capacity} students)."])->withInput();
        }

        $profilePicturePath = $request->hasFile('profile_picture')
            ? $request->file('profile_picture')->store('profiles/students', 'public')
            : null;

        $validated['profile_picture'] = $profilePicturePath;

        $student = Student::create($validated);

        ActivityLog::record(
            'created',
            'Student',
            $student->id,
            auth()->user()->name." enrolled {$student->fullName()} (Gr.{$student->grade_level} - {$student->section})."
        );

        return back()->with('status', "{$validated['first_name']} {$validated['last_name']} was added.");
    }

    /**
     * Update an existing student (used by the "Edit" button/modal on the Add Student page).
     */
    public function update(Request $request, Student $student): RedirectResponse
    {
        $validated = $this->validateStudent($request, $student);

        $section = Section::where('grade_level', $validated['grade_level'])
            ->where('name', $validated['section'])
            ->first();

        if ($section && $section->isFull() && ! ($student->grade_level === $validated['grade_level'] && $student->section === $validated['section'])) {
            return back()->withErrors(['section' => "\"{$section->label()}\" is already at full capacity ({$section->capacity} students)."])->withInput();
        }

        if ($request->hasFile('profile_picture')) {
            $validated['profile_picture'] = $request->file('profile_picture')->store('profiles/students', 'public');
        }

        $student->update($validated);

        ActivityLog::record(
            'updated',
            'Student',
            $student->id,
            auth()->user()->name." edited {$student->fullName()}'s record (Gr.{$student->grade_level} - {$student->section})."
        );

        return back()->with('status', "{$student->fullName()} was updated.");
    }

    /**
     * Shared validation rules for both store() and update().
     */
    private function validateStudent(Request $request, ?Student $student = null): array
    {
        $studentNoRule = $student
            ? Rule::unique('students', 'student_no')->ignore($student->id)
            : Rule::unique('students', 'student_no');

        $fingerprintRule = $student
            ? Rule::unique('students', 'fingerprint_id')->ignore($student->id)
            : Rule::unique('students', 'fingerprint_id');

        return $request->validate([
            'student_no' => ['required', 'string', 'max:50', $studentNoRule],
            'fingerprint_id' => ['nullable', 'string', 'max:100', $fingerprintRule],
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'grade_level' => ['required', Rule::in(Section::gradeLevels())],
            'section' => [
                'required',
                'string',
                'max:100',
                Rule::exists('sections', 'name')->where(fn ($q) => $q->where('grade_level', $request->grade_level)),
            ],
            'profile_picture' => ['nullable', 'image', 'max:2048'],
            'guardian_name' => ['nullable', 'string', 'max:150'],
            'guardian_contact' => ['nullable', 'string', 'max:50'],
            'guardian_email' => ['nullable', 'email', 'max:150'],
        ]);
    }
}
