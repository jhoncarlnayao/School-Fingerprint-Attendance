@extends('layouts.admin')

@section('title', 'Sections')

@section('head')
<script src="https://cdn.tailwindcss.com"></script>
@endsection

@section('content')
<div class="page-head">
  <div class="page-title">Sections</div>
  <div class="page-sub">Create sections per grade level before assigning teachers or enrolling students.</div>
</div>

<div style="display:grid; grid-template-columns:380px 1fr; gap:20px; align-items:start;">
  <div class="card" style="padding:22px;">
    <form method="POST" action="{{ route('admin.sections.store') }}">
      @csrf
      <div class="form-field">
        <label for="grade_level">Grade level</label>
        <select id="grade_level" name="grade_level" required autofocus>
          <option value="" disabled {{ old('grade_level') ? '' : 'selected' }}>Select grade level</option>
          @foreach ($gradeLevels as $level)
            <option value="{{ $level }}" {{ old('grade_level') == $level ? 'selected' : '' }}>Grade {{ $level }}</option>
          @endforeach
        </select>
        @error('grade_level') <div class="field-error">{{ $message }}</div> @enderror
      </div>

      <div class="form-field">
        <label for="name">Section name</label>
        <input id="name" name="name" value="{{ old('name') }}" placeholder="e.g. Rizal" required>
        @error('name') <div class="field-error">{{ $message }}</div> @enderror
      </div>

      <div class="form-field">
        <label for="capacity">Student capacity</label>
        <input id="capacity" type="number" min="1" max="500" name="capacity" value="{{ old('capacity') }}" placeholder="e.g. 40 (leave blank for unlimited)">
        <div class="form-hint">Maximum number of students that can be enrolled in this section.</div>
        @error('capacity') <div class="field-error">{{ $message }}</div> @enderror
      </div>

      <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">Create Section</button>
      <div class="form-hint">Once created, this section becomes available in the "Add Student" and "Assign Teacher" dropdowns.</div>
    </form>
  </div>

  <div class="card">
    <div style="padding:16px 18px; border-bottom:1px solid var(--border); font-family:'Poppins',sans-serif; font-weight:700; font-size:15px;">
      All Sections ({{ $sections->count() }})
    </div>
    <table style="width:100%; border-collapse:collapse; font-size:13px;">
      <thead>
        <tr style="background:var(--dark); color:#fff;">
          <th style="text-align:left; padding:10px 16px; font-size:11.5px;">Grade</th>
          <th style="text-align:left; padding:10px 16px; font-size:11.5px;">Section</th>
          <th style="text-align:left; padding:10px 16px; font-size:11.5px;">Enrolled</th>
          <th style="text-align:left; padding:10px 16px; font-size:11.5px;">Capacity</th>
          <th style="text-align:left; padding:10px 16px; font-size:11.5px;"></th>
        </tr>
      </thead>
      <tbody>
        @forelse ($sections as $section)
          @php
            $count = $section->studentCount();
            $teacherCount = $section->teacherCount();
            $blocked = $count > 0 || $teacherCount > 0;
          @endphp
          <tr style="border-bottom:1px solid #F1F2F0;">
            <td style="padding:11px 16px; font-weight:600;">Grade {{ $section->grade_level }}</td>
            <td style="padding:11px 16px;">{{ $section->name }}</td>
            <td style="padding:11px 16px;">{{ $count }}</td>
            <td style="padding:11px 16px; color:var(--sub);">
              @if($section->capacity)
                {{ $section->capacity }}
                @if($section->isFull())
                  <span class="badge badge-red" style="margin-left:6px;">Full</span>
                @endif
              @else
                Unlimited
              @endif
            </td>
            <td style="padding:11px 16px; text-align:right; white-space:nowrap;">
              <div style="display:inline-flex; gap:6px;">
                <button type="button" class="btn" style="padding:6px 12px; font-size:12px;"
                  onclick='openViewSectionModal({{ json_encode([
                    "label" => $section->label(),
                    "grade_level" => $section->grade_level,
                    "name" => $section->name,
                    "capacity" => $section->capacity,
                    "count" => $count,
                    "teachers" => $section->assignedTeachers()->map(fn ($t) => [
                      "name" => $t->name,
                      "email" => $t->email,
                      "subject" => $t->assigned_subject,
                      "photo" => $t->profilePictureUrl(),
                      "initials" => $t->initials(),
                    ])->values(),
                    "students" => $section->enrolledStudents()->map(fn ($s) => [
                      "name" => $s->fullName(),
                      "student_no" => $s->student_no,
                      "photo" => $s->profilePictureUrl(),
                      "initials" => $s->initials(),
                    ])->values(),
                  ], JSON_HEX_APOS | JSON_HEX_QUOT) }})'>
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"/><circle cx="12" cy="12" r="3"/></svg>
                  View
                </button>
                <button type="button" class="btn" style="padding:6px 12px; font-size:12px;"
                  onclick='openEditSectionModal({{ json_encode([
                    "label" => $section->label(),
                    "grade_level" => $section->grade_level,
                    "name" => $section->name,
                    "capacity" => $section->capacity,
                    "update_url" => route("admin.sections.update", $section),
                  ], JSON_HEX_APOS | JSON_HEX_QUOT) }})'>
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                  Edit
                </button>
                @if($blocked)
                  <button type="button" class="btn" style="padding:6px 12px; font-size:12px; color:#B4B9B4; border-color:#E5E7EB; cursor:not-allowed;"
                    title="Can't delete — this section still has {{ $count > 0 ? 'students' : '' }}{{ $count > 0 && $teacherCount > 0 ? ' and a ' : '' }}{{ $teacherCount > 0 ? 'teacher' : '' }} assigned.">
                    Delete
                  </button>
                @else
                  <form method="POST" action="{{ route('admin.sections.destroy', $section) }}" onsubmit="return confirm('Delete {{ $section->label() }}?');" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn" style="padding:6px 12px; font-size:12px; color:var(--danger); border-color:#F3D2D2;">Delete</button>
                  </form>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" style="padding:26px 16px; text-align:center; color:var(--sub);">No sections yet — create one on the left.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- ===== View Section modal ===== --}}
<div
  id="view-section-overlay"
  class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm opacity-0 transition-opacity duration-300 ease-out"
  onclick="if (event.target === this) closeViewSectionModal()"
>
  <div
    id="view-section-panel"
    class="w-full max-w-lg mx-4 max-h-[88vh] overflow-y-auto rounded-2xl bg-white p-6 shadow-2xl ring-1 ring-black/5 scale-95 opacity-0 transition-all duration-300 ease-out"
  >
    <div class="flex items-center justify-between mb-1">
      <h2 id="view-section-title" class="text-lg font-bold text-slate-900"></h2>
      <button type="button" onclick="closeViewSectionModal()" class="grid h-8 w-8 place-items-center rounded-full text-slate-400 transition hover:bg-slate-100 hover:text-slate-700">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div id="view-section-meta" class="mb-5 text-xs text-slate-500"></div>

    <div class="mb-5">
      <div class="mb-2 flex items-center justify-between">
        <h3 class="text-xs font-bold uppercase tracking-wide text-slate-500">Teacher(s) assigned</h3>
      </div>
      <div id="view-section-teachers" class="space-y-2"></div>
    </div>

    <div>
      <div class="mb-2 flex items-center justify-between">
        <h3 class="text-xs font-bold uppercase tracking-wide text-slate-500">Students enrolled</h3>
      </div>
      <div id="view-section-students" class="space-y-2 max-h-60 overflow-y-auto"></div>
    </div>

    <button type="button" onclick="closeViewSectionModal()"
      class="mt-6 w-full rounded-lg border border-slate-200 bg-white py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
      Close
    </button>
  </div>
</div>

{{-- ===== Edit Section modal ===== --}}
<div
  id="edit-section-overlay"
  class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm opacity-0 transition-opacity duration-300 ease-out"
  onclick="if (event.target === this) closeEditSectionModal()"
>
  <div
    id="edit-section-panel"
    class="w-full max-w-md mx-4 max-h-[88vh] overflow-y-auto rounded-2xl bg-white p-6 shadow-2xl ring-1 ring-black/5 scale-95 opacity-0 transition-all duration-300 ease-out"
  >
    <div class="flex items-center justify-between mb-5">
      <h2 class="text-lg font-bold text-slate-900">Edit Section</h2>
      <button type="button" onclick="closeEditSectionModal()" class="grid h-8 w-8 place-items-center rounded-full text-slate-400 transition hover:bg-slate-100 hover:text-slate-700">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>

    <form id="edit-section-form" method="POST" class="space-y-4">
      @csrf
      @method('PATCH')

      <div>
        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Grade level</label>
        <input id="edit_section_grade_display" disabled
          class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-500">
        <div class="mt-1.5 text-xs text-slate-400">Grade level can't be changed here — delete and re-create the section if it's wrong.</div>
      </div>

      <div>
        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Section name</label>
        <input name="name" id="edit_section_name" required
          class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:ring-4 focus:ring-slate-100">
      </div>

      <div>
        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Student capacity</label>
        <input type="number" min="1" max="500" name="capacity" id="edit_section_capacity" placeholder="Leave blank for unlimited"
          class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:ring-4 focus:ring-slate-100">
      </div>

      <div class="flex gap-3 pt-2">
        <button type="button" onclick="closeEditSectionModal()"
          class="flex-1 rounded-lg border border-slate-200 bg-white py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
          Cancel
        </button>
        <button type="submit"
          class="flex-1 rounded-lg bg-slate-900 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800 active:scale-[0.98]">
          Save Changes
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  function personRow(person) {
    const photo = person.photo
      ? `<img src="${person.photo}" style="width:32px;height:32px;border-radius:50%;object-fit:cover;">`
      : `<div style="width:32px;height:32px;border-radius:50%;background:#F3F6F2;border:1px solid #E5E7EB;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#6B7280;">${person.initials || '?'}</div>`;
    return `<div style="display:flex;align-items:center;gap:10px;padding:8px 10px;border:1px solid #F1F2F0;border-radius:10px;">
      ${photo}
      <div style="min-width:0;">
        <div style="font-size:13px;font-weight:600;color:#1F2937;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${person.name}</div>
        <div style="font-size:11.5px;color:#6B7280;">${person.sub || ''}</div>
      </div>
    </div>`;
  }

  // ===== View Section modal =====
  function openViewSectionModal(data) {
    document.getElementById('view-section-title').textContent = data.label;
    const capacityText = data.capacity ? `${data.count} / ${data.capacity} enrolled` : `${data.count} enrolled (unlimited capacity)`;
    document.getElementById('view-section-meta').textContent = capacityText;

    const teachersEl = document.getElementById('view-section-teachers');
    if (data.teachers && data.teachers.length) {
      teachersEl.innerHTML = data.teachers.map(t => personRow({ name: t.name, sub: t.subject ? `${t.subject} · ${t.email}` : t.email, photo: t.photo, initials: t.initials })).join('');
    } else {
      teachersEl.innerHTML = '<div style="font-size:12.5px;color:#6B7280;padding:10px 0;">No teacher assigned to this section yet.</div>';
    }

    const studentsEl = document.getElementById('view-section-students');
    if (data.students && data.students.length) {
      studentsEl.innerHTML = data.students.map(s => personRow({ name: s.name, sub: s.student_no, photo: s.photo, initials: s.initials })).join('');
    } else {
      studentsEl.innerHTML = '<div style="font-size:12.5px;color:#6B7280;padding:10px 0;">No students enrolled in this section yet.</div>';
    }

    const overlay = document.getElementById('view-section-overlay');
    const panel = document.getElementById('view-section-panel');

    overlay.classList.remove('hidden');
    overlay.classList.add('flex');
    document.body.classList.add('overflow-hidden');

    requestAnimationFrame(() => {
      requestAnimationFrame(() => {
        overlay.classList.remove('opacity-0');
        overlay.classList.add('opacity-100');
        panel.classList.remove('opacity-0', 'scale-95');
        panel.classList.add('opacity-100', 'scale-100');
      });
    });

    document.addEventListener('keydown', closeViewSectionOnEscape);
  }

  function closeViewSectionModal() {
    const overlay = document.getElementById('view-section-overlay');
    const panel = document.getElementById('view-section-panel');

    overlay.classList.remove('opacity-100');
    overlay.classList.add('opacity-0');
    panel.classList.remove('opacity-100', 'scale-100');
    panel.classList.add('opacity-0', 'scale-95');

    document.removeEventListener('keydown', closeViewSectionOnEscape);

    setTimeout(() => {
      overlay.classList.add('hidden');
      overlay.classList.remove('flex');
      document.body.classList.remove('overflow-hidden');
    }, 300);
  }

  function closeViewSectionOnEscape(e) {
    if (e.key === 'Escape') closeViewSectionModal();
  }

  // ===== Edit Section modal =====
  function openEditSectionModal(data) {
    document.getElementById('edit-section-form').action = data.update_url;
    document.getElementById('edit_section_grade_display').value = `Grade ${data.grade_level}`;
    document.getElementById('edit_section_name').value = data.name || '';
    document.getElementById('edit_section_capacity').value = data.capacity || '';

    const overlay = document.getElementById('edit-section-overlay');
    const panel = document.getElementById('edit-section-panel');

    overlay.classList.remove('hidden');
    overlay.classList.add('flex');
    document.body.classList.add('overflow-hidden');

    requestAnimationFrame(() => {
      requestAnimationFrame(() => {
        overlay.classList.remove('opacity-0');
        overlay.classList.add('opacity-100');
        panel.classList.remove('opacity-0', 'scale-95');
        panel.classList.add('opacity-100', 'scale-100');
      });
    });

    document.addEventListener('keydown', closeEditSectionOnEscape);
  }

  function closeEditSectionModal() {
    const overlay = document.getElementById('edit-section-overlay');
    const panel = document.getElementById('edit-section-panel');

    overlay.classList.remove('opacity-100');
    overlay.classList.add('opacity-0');
    panel.classList.remove('opacity-100', 'scale-100');
    panel.classList.add('opacity-0', 'scale-95');

    document.removeEventListener('keydown', closeEditSectionOnEscape);

    setTimeout(() => {
      overlay.classList.add('hidden');
      overlay.classList.remove('flex');
      document.body.classList.remove('overflow-hidden');
    }, 300);
  }

  function closeEditSectionOnEscape(e) {
    if (e.key === 'Escape') closeEditSectionModal();
  }
</script>
@endsection
