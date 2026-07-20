<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SectionController extends Controller
{
    /**
     * Show the "Sections" page — create sections + list existing ones.
     * Admin must set these up first before teachers/students can be assigned to them.
     */
    public function index()
    {
        $sections = Section::orderBy('grade_level')->orderBy('name')->get();
        $gradeLevels = Section::gradeLevels();

        return view('admin.sections.index', compact('sections', 'gradeLevels'));
    }

    /**
     * Create a new section under a grade level, with an optional student capacity.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'grade_level' => ['required', Rule::in(Section::gradeLevels())],
            'name' => ['required', 'string', 'max:100'],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:500'],
        ]);

        $exists = Section::where('grade_level', $validated['grade_level'])
            ->where('name', $validated['name'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['name' => 'That section already exists for this grade level.'])->withInput();
        }

        Section::create($validated);

        ActivityLog::record(
            'created',
            'Section',
            null,
            auth()->user()->name." created section \"Grade {$validated['grade_level']} - {$validated['name']}\"."
        );

        return back()->with('status', "Section \"Grade {$validated['grade_level']} - {$validated['name']}\" was created.");
    }

    /**
     * Update a section's name and/or capacity.
     * Grade level is intentionally not editable here — changing it would
     * change the section's identity; delete and re-create instead.
     * Renaming cascades to any students/teachers already pointing at the
     * old name so their records stay in sync.
     */
    public function update(Request $request, Section $section): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('sections', 'name')
                    ->where(fn ($q) => $q->where('grade_level', $section->grade_level))
                    ->ignore($section->id),
            ],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:500'],
        ]);

        $currentEnrolled = $section->studentCount();

        if (! empty($validated['capacity']) && $validated['capacity'] < $currentEnrolled) {
            return back()->withErrors([
                'capacity' => "Capacity can't be less than the {$currentEnrolled} student(s) already enrolled in \"{$section->label()}\".",
            ])->withInput();
        }

        $oldName = $section->name;
        $oldLabel = $section->label();

        $section->update($validated);

        if ($oldName !== $validated['name']) {
            Student::where('grade_level', $section->grade_level)
                ->where('section', $oldName)
                ->update(['section' => $validated['name']]);

            User::where('role', 'teacher')
                ->where('assigned_grade_level', $section->grade_level)
                ->where('assigned_section', $oldName)
                ->update(['assigned_section' => $validated['name']]);
        }

        ActivityLog::record(
            'updated',
            'Section',
            $section->id,
            auth()->user()->name." updated section \"{$oldLabel}\" to \"{$section->label()}\"."
        );

        return back()->with('status', "Section \"{$section->label()}\" was updated.");
    }

    /**
     * Remove a section. Blocked if it still has students enrolled or a
     * teacher currently assigned to it.
     */
    public function destroy(Section $section): RedirectResponse
    {
        if ($section->studentCount() > 0) {
            return back()->withErrors(['name' => "Can't delete \"{$section->label()}\" — it still has students enrolled."]);
        }

        if ($section->teacherCount() > 0) {
            return back()->withErrors(['name' => "Can't delete \"{$section->label()}\" — it still has a teacher assigned to it."]);
        }

        $label = $section->label();
        $section->delete();

        ActivityLog::record(
            'deleted',
            'Section',
            null,
            auth()->user()->name." deleted section \"{$label}\"."
        );

        return back()->with('status', "Section \"{$label}\" was deleted.");
    }
}
