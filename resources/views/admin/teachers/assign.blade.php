@extends('layouts.admin')

@section('title', 'Assign Teacher')

@section('head')
<script src="https://cdn.tailwindcss.com"></script>
@endsection

@section('content')
<div class="page-head">
  <div class="page-title">Assign Teacher</div>
  <div class="page-sub">Give each teacher a grade level, section, and subject to handle. Click <strong>Edit</strong> to change a row.</div>
</div>

@if ($sections->isEmpty())
  <div class="alert alert-error">
    No sections have been created yet. <a href="{{ route('admin.sections.index') }}" style="color:var(--danger); font-weight:700; text-decoration:underline;">Create a section first</a> before assigning teachers.
  </div>
@endif

<div class="card">
  <table style="width:100%; border-collapse:collapse; font-size:13px;">
    <thead>
      <tr style="background:var(--dark); color:#fff;">
        <th style="text-align:left; padding:12px 18px; font-size:11.5px;">Teacher</th>
        <th style="text-align:left; padding:12px 18px; font-size:11.5px;">Grade level</th>
        <th style="text-align:left; padding:12px 18px; font-size:11.5px;">Section</th>
        <th style="text-align:left; padding:12px 18px; font-size:11.5px;">Subject</th>
        <th style="text-align:left; padding:12px 18px; font-size:11.5px;"></th>
      </tr>
    </thead>
    <tbody>
      @forelse ($teachers as $teacher)
        <tr style="border-bottom:1px solid #F1F2F0;">
          <td style="padding:12px 18px;">
            <div style="display:flex; align-items:center; gap:10px;">
              @if ($teacher->profilePictureUrl())
                <img src="{{ $teacher->profilePictureUrl() }}" alt="" style="width:32px; height:32px; border-radius:50%; object-fit:cover;">
              @else
                <div class="user-avatar" style="width:32px; height:32px; font-size:12px;">{{ $teacher->initials() }}</div>
              @endif
              <div>
                <div style="font-weight:600;">{{ $teacher->name }}</div>
                <div style="font-size:11.5px; color:var(--sub);">{{ $teacher->email }}</div>
              </div>
            </div>
          </td>
          <td style="padding:12px 18px; color:var(--sub);">{{ $teacher->assigned_grade_level ? 'Grade '.$teacher->assigned_grade_level : '—' }}</td>
          <td style="padding:12px 18px; color:var(--sub);">{{ $teacher->assigned_section ?: '—' }}</td>
          <td style="padding:12px 18px; color:var(--sub);">{{ $teacher->assigned_subject ?: '—' }}</td>
          <td style="padding:12px 18px; white-space:nowrap;">
            <button type="button" class="btn" style="padding:6px 12px; font-size:12px;"
              onclick='openEditModal({{ json_encode([
                "id" => $teacher->id,
                "name" => $teacher->name,
                "assigned_grade_level" => $teacher->assigned_grade_level,
                "assigned_section" => $teacher->assigned_section,
                "assigned_subject" => $teacher->assigned_subject,
                "update_url" => route("admin.teachers.assign.update", $teacher),
              ], JSON_HEX_APOS | JSON_HEX_QUOT) }})'>
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
              Edit
            </button>
          </td>
        </tr>
      @empty
        <tr><td colspan="5" style="padding:26px 18px; text-align:center; color:var(--sub);">No teachers to assign yet — <a href="{{ route('admin.teachers.register') }}" style="color:var(--primary); font-weight:600;">register one first</a>.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

{{-- ===== Edit Assignment modal ===== --}}
<div
  id="edit-assign-overlay"
  class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm opacity-0 transition-opacity duration-300 ease-out"
  onclick="if (event.target === this) closeEditModal()"
>
  <div
    id="edit-assign-panel"
    class="w-full max-w-md mx-4 max-h-[88vh] overflow-y-auto rounded-2xl bg-white p-6 shadow-2xl ring-1 ring-black/5 scale-95 opacity-0 transition-all duration-300 ease-out"
  >
    <div class="flex items-center justify-between mb-5">
      <h2 class="text-lg font-bold text-slate-900">Assign Teacher</h2>
      <button type="button" onclick="closeEditModal()" class="grid h-8 w-8 place-items-center rounded-full text-slate-400 transition hover:bg-slate-100 hover:text-slate-700">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>

    <div id="edit-assign-teacher-name" class="mb-4 text-sm font-semibold text-slate-500"></div>

    <form id="edit-assign-form" method="POST" class="space-y-4">
      @csrf
      @method('PATCH')

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Grade level</label>
          <select name="assigned_grade_level" id="edit_assigned_grade_level"
            class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:ring-4 focus:ring-slate-100">
            <option value="">— None —</option>
            @foreach ($gradeLevels as $level)
              <option value="{{ $level }}">Grade {{ $level }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Section</label>
          <select name="assigned_section" id="edit_assigned_section"
            class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:ring-4 focus:ring-slate-100">
            <option value="">— None —</option>
          </select>
        </div>
      </div>

      <div>
        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Subject</label>
        <input name="assigned_subject" id="edit_assigned_subject" placeholder="e.g. Science"
          class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:ring-4 focus:ring-slate-100">
      </div>

      <div class="flex gap-3 pt-2">
        <button type="button" onclick="closeEditModal()"
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
  const SECTIONS_BY_GRADE = {};
  @foreach ($sections as $section)
    (SECTIONS_BY_GRADE["{{ $section->grade_level }}"] ??= []).push("{{ $section->name }}");
  @endforeach

  const editGradeSelect = document.getElementById('edit_assigned_grade_level');
  const editSectionSelect = document.getElementById('edit_assigned_section');

  function populateSections(keepValue) {
    const grade = editGradeSelect.value;
    editSectionSelect.innerHTML = '<option value="">— None —</option>';

    (SECTIONS_BY_GRADE[grade] || []).forEach(name => {
      const opt = document.createElement('option');
      opt.value = name;
      opt.textContent = name;
      if (name === keepValue) opt.selected = true;
      editSectionSelect.appendChild(opt);
    });
  }

  editGradeSelect.addEventListener('change', () => populateSections(null));

  function openEditModal(data) {
    document.getElementById('edit-assign-form').action = data.update_url;
    document.getElementById('edit-assign-teacher-name').textContent = data.name || '';
    editGradeSelect.value = data.assigned_grade_level || '';
    populateSections(data.assigned_section || null);
    document.getElementById('edit_assigned_subject').value = data.assigned_subject || '';

    const overlay = document.getElementById('edit-assign-overlay');
    const panel = document.getElementById('edit-assign-panel');

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

    document.addEventListener('keydown', closeOnEscape);
  }

  function closeEditModal() {
    const overlay = document.getElementById('edit-assign-overlay');
    const panel = document.getElementById('edit-assign-panel');

    overlay.classList.remove('opacity-100');
    overlay.classList.add('opacity-0');
    panel.classList.remove('opacity-100', 'scale-100');
    panel.classList.add('opacity-0', 'scale-95');

    document.removeEventListener('keydown', closeOnEscape);

    setTimeout(() => {
      overlay.classList.add('hidden');
      overlay.classList.remove('flex');
      document.body.classList.remove('overflow-hidden');
    }, 300);
  }

  function closeOnEscape(e) {
    if (e.key === 'Escape') closeEditModal();
  }
</script>
@endsection
