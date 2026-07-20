@extends('layouts.admin')

@section('title', 'Add Student')

@section('head')
<script src="https://cdn.tailwindcss.com"></script>
@endsection

@section('content')
<div class="page-head">
  <div class="page-title">Add Student</div>
  <div class="page-sub">Enroll a new student into the system.</div>
</div>

@if ($sections->isEmpty())
  <div class="alert alert-error">
    No sections have been created yet. <a href="{{ route('admin.sections.index') }}" style="color:var(--danger); font-weight:700; text-decoration:underline;">Create a section first</a> before enrolling students.
  </div>
@endif

<div style="display:grid; grid-template-columns:380px 1fr; gap:20px; align-items:start;">
  <div class="card" style="padding:22px;">
    <form method="POST" action="{{ route('admin.students.store') }}" enctype="multipart/form-data">
      @csrf

      <div class="form-field" style="text-align:center;">
        <label>Profile picture</label>
        <div id="student-photo-preview" style="width:84px; height:84px; border-radius:50%; background:#F3F6F2; border:1px solid var(--border); margin:6px auto; display:flex; align-items:center; justify-content:center; overflow:hidden; color:var(--sub); font-size:11px;">No photo</div>
        <input id="profile_picture" type="file" name="profile_picture" accept="image/*" onchange="previewPhoto(this, 'student-photo-preview')">
        @error('profile_picture') <div class="field-error">{{ $message }}</div> @enderror
      </div>

      <div class="form-field">
        <label for="student_no">Student ID</label>
        <input id="student_no" name="student_no" value="{{ old('student_no') }}" placeholder="e.g. 2026-0001" required>
        @error('student_no') <div class="field-error">{{ $message }}</div> @enderror
      </div>

      <div class="form-field">
        <label for="fingerprint_id">Fingerprint ID</label>
        <input id="fingerprint_id" name="fingerprint_id" value="{{ old('fingerprint_id') }}" placeholder="ID from the scanner device (optional)">
        <div class="form-hint">Enroll the fingerprint on the scanner's own software first, then paste the template/user ID it assigns here.</div>
        @error('fingerprint_id') <div class="field-error">{{ $message }}</div> @enderror
      </div>

      <div class="form-field">
        <label for="first_name">First name</label>
        <input id="first_name" name="first_name" value="{{ old('first_name') }}" required>
        @error('first_name') <div class="field-error">{{ $message }}</div> @enderror
      </div>

      <div class="form-field">
        <label for="middle_name">Middle name</label>
        <input id="middle_name" name="middle_name" value="{{ old('middle_name') }}" placeholder="Optional">
        @error('middle_name') <div class="field-error">{{ $message }}</div> @enderror
      </div>

      <div class="form-field">
        <label for="last_name">Last name</label>
        <input id="last_name" name="last_name" value="{{ old('last_name') }}" required>
        @error('last_name') <div class="field-error">{{ $message }}</div> @enderror
      </div>

      <div class="form-field">
        <label for="birth_date">Birthday</label>
        <input id="birth_date" type="date" name="birth_date" value="{{ old('birth_date') }}" max="{{ now()->subDay()->format('Y-m-d') }}" oninput="updateAgePreview(this, 'age-preview')">
        <div id="age-preview" style="font-size:12px; color:var(--sub); margin-top:6px;"></div>
        @error('birth_date') <div class="field-error">{{ $message }}</div> @enderror
      </div>

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
        <div class="form-field">
          <label for="grade_level">Grade level</label>
          <select id="grade_level" name="grade_level" required>
            <option value="" disabled {{ old('grade_level') ? '' : 'selected' }}>Select</option>
            @foreach ($gradeLevels as $level)
              <option value="{{ $level }}" {{ old('grade_level') == $level ? 'selected' : '' }}>Grade {{ $level }}</option>
            @endforeach
          </select>
          @error('grade_level') <div class="field-error">{{ $message }}</div> @enderror
        </div>
        <div class="form-field">
          <label for="section">Section</label>
          <select id="section" name="section" required>
            <option value="" disabled selected>Select grade first</option>
          </select>
          @error('section') <div class="field-error">{{ $message }}</div> @enderror
        </div>
      </div>

      <div class="form-field">
        <label for="guardian_name">Guardian name</label>
        <input id="guardian_name" name="guardian_name" value="{{ old('guardian_name') }}" placeholder="Optional">
      </div>

      <div class="form-field">
        <label for="guardian_contact">Guardian contact</label>
        <input id="guardian_contact" name="guardian_contact" value="{{ old('guardian_contact') }}" placeholder="Optional">
      </div>

      <div class="form-field">
        <label for="guardian_email">Guardian email</label>
        <input id="guardian_email" type="email" name="guardian_email" value="{{ old('guardian_email') }}" placeholder="For attendance notifications">
        <div class="form-hint">If set, the guardian gets an email every time this student scans in/out, and if they're marked absent.</div>
        @error('guardian_email') <div class="field-error">{{ $message }}</div> @enderror
      </div>

      <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">Add Student</button>
    </form>
  </div>

  <div class="card">
    <div style="padding:16px 18px; border-bottom:1px solid var(--border); font-family:'Poppins',sans-serif; font-weight:700; font-size:15px;">
      Recently Added ({{ $students->count() }})
    </div>
    <table style="width:100%; border-collapse:collapse; font-size:13px;">
      <thead>
        <tr style="background:var(--dark); color:#fff;">
          <th style="text-align:left; padding:10px 16px; font-size:11.5px;">Student ID</th>
          <th style="text-align:left; padding:10px 16px; font-size:11.5px;">Name</th>
          <th style="text-align:left; padding:10px 16px; font-size:11.5px;">Age</th>
          <th style="text-align:left; padding:10px 16px; font-size:11.5px;">Grade &amp; Section</th>
          <th style="text-align:left; padding:10px 16px; font-size:11.5px;">Added</th>
          <th style="text-align:left; padding:10px 16px; font-size:11.5px;"></th>
        </tr>
      </thead>
      <tbody>
        @forelse ($students as $student)
          <tr style="border-bottom:1px solid #F1F2F0;">
            <td style="padding:11px 16px; font-weight:600;">{{ $student->student_no }}</td>
            <td style="padding:11px 16px;">
              <div style="display:flex; align-items:center; gap:10px;">
                @if ($student->profilePictureUrl())
                  <img src="{{ $student->profilePictureUrl() }}" alt="" style="width:30px; height:30px; border-radius:50%; object-fit:cover;">
                @else
                  <div class="user-avatar" style="width:30px; height:30px; font-size:11.5px;">{{ $student->initials() }}</div>
                @endif
                {{ $student->fullName() }}
              </div>
            </td>
            <td style="padding:11px 16px; color:var(--sub);">
              @if ($student->age() !== null)
                {{ $student->age() }} yrs
              @else
                <span style="color:#C2660B;">—</span>
              @endif
            </td>
            <td style="padding:11px 16px; color:var(--sub);">Gr.{{ $student->grade_level }} — {{ $student->section }}</td>
            <td style="padding:11px 16px; color:var(--sub);">{{ $student->created_at->format('M d, Y') }}</td>
            <td style="padding:11px 16px;">
              <button type="button" class="btn" style="padding:6px 12px; font-size:12px;"
                onclick='openEditModal({{ json_encode([
                  "id" => $student->id,
                  "student_no" => $student->student_no,
                  "fingerprint_id" => $student->fingerprint_id,
                  "first_name" => $student->first_name,
                  "middle_name" => $student->middle_name,
                  "last_name" => $student->last_name,
                  "birth_date" => optional($student->birth_date)->format("Y-m-d"),
                  "grade_level" => $student->grade_level,
                  "section" => $student->section,
                  "guardian_name" => $student->guardian_name,
                  "guardian_contact" => $student->guardian_contact,
                  "guardian_email" => $student->guardian_email,
                  "update_url" => route("admin.students.update", $student),
                ], JSON_HEX_APOS | JSON_HEX_QUOT) }})'>
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                Edit
              </button>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" style="padding:26px 16px; text-align:center; color:var(--sub);">No students yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- ===== Edit Student modal ===== --}}
<div
  id="edit-student-overlay"
  class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm opacity-0 transition-opacity duration-300 ease-out"
  onclick="if (event.target === this) closeEditModal()"
>
  <div
    id="edit-student-panel"
    class="w-full max-w-md mx-4 max-h-[88vh] overflow-y-auto rounded-2xl bg-white p-6 shadow-2xl ring-1 ring-black/5 scale-95 opacity-0 transition-all duration-300 ease-out"
  >
    <div class="flex items-center justify-between mb-5">
      <h2 class="text-lg font-bold text-slate-900">Edit Student</h2>
      <button type="button" onclick="closeEditModal()" class="grid h-8 w-8 place-items-center rounded-full text-slate-400 transition hover:bg-slate-100 hover:text-slate-700">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>

    <form id="edit-student-form" method="POST" class="space-y-4">
      @csrf
      @method('PATCH')

      <div>
        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Student ID</label>
        <input name="student_no" id="edit_student_no" required
          class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:ring-4 focus:ring-slate-100">
      </div>

      <div>
        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Fingerprint ID</label>
        <input name="fingerprint_id" id="edit_fingerprint_id" placeholder="Optional"
          class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:ring-4 focus:ring-slate-100">
      </div>

      <div>
        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">First name</label>
        <input name="first_name" id="edit_first_name" required
          class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:ring-4 focus:ring-slate-100">
      </div>

      <div>
        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Middle name</label>
        <input name="middle_name" id="edit_middle_name" placeholder="Optional"
          class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:ring-4 focus:ring-slate-100">
      </div>

      <div>
        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Last name</label>
        <input name="last_name" id="edit_last_name" required
          class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:ring-4 focus:ring-slate-100">
      </div>

      <div>
        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Birthday</label>
        <input type="date" name="birth_date" id="edit_birth_date" max="{{ now()->subDay()->format('Y-m-d') }}" oninput="updateAgePreview(this, 'edit-age-preview')"
          class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:ring-4 focus:ring-slate-100">
        <div id="edit-age-preview" class="mt-1.5 text-xs font-medium text-slate-500"></div>
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Grade level</label>
          <select name="grade_level" id="edit_grade_level" required
            class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:ring-4 focus:ring-slate-100"></select>
        </div>
        <div>
          <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Section</label>
          <select name="section" id="edit_section" required
            class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:ring-4 focus:ring-slate-100"></select>
        </div>
      </div>

      <div>
        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Guardian name</label>
        <input name="guardian_name" id="edit_guardian_name" placeholder="Optional"
          class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:ring-4 focus:ring-slate-100">
      </div>

      <div>
        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Guardian contact</label>
        <input name="guardian_contact" id="edit_guardian_contact" placeholder="Optional"
          class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:ring-4 focus:ring-slate-100">
      </div>

      <div>
        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Guardian email</label>
        <input type="email" name="guardian_email" id="edit_guardian_email" placeholder="For attendance notifications"
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
    (SECTIONS_BY_GRADE["{{ $section->grade_level }}"] ??= []).push({
      name: "{{ $section->name }}",
      full: {{ $section->capacity && $section->isFull() ? 'true' : 'false' }},
    });
  @endforeach

  const gradeSelect = document.getElementById('grade_level');
  const sectionSelect = document.getElementById('section');
  const oldSection = "{{ old('section') }}";

  function populateSections(gradeEl, sectionEl, keepValue) {
    const grade = gradeEl.value;
    sectionEl.innerHTML = '';

    const options = SECTIONS_BY_GRADE[grade] || [];
    if (options.length === 0) {
      sectionEl.innerHTML = '<option value="" disabled selected>No sections for this grade yet</option>';
      return;
    }

    sectionEl.innerHTML = '<option value="" disabled selected>Select section</option>';
    options.forEach(sec => {
      const opt = document.createElement('option');
      opt.value = sec.name;
      opt.textContent = sec.name + (sec.full ? ' (Full)' : '');
      if (sec.full && sec.name !== keepValue) opt.disabled = true;
      if (sec.name === keepValue) opt.selected = true;
      sectionEl.appendChild(opt);
    });
  }

  gradeSelect.addEventListener('change', () => populateSections(gradeSelect, sectionSelect, oldSection));
  if (gradeSelect.value) populateSections(gradeSelect, sectionSelect, oldSection);

  function previewPhoto(input, targetId) {
    const target = document.getElementById(targetId);
    const file = input.files && input.files[0];
    if (!file) { target.innerHTML = 'No photo'; return; }
    const reader = new FileReader();
    reader.onload = e => { target.innerHTML = `<img src="${e.target.result}" style="width:100%; height:100%; object-fit:cover;">`; };
    reader.readAsDataURL(file);
  }

  function updateAgePreview(input, targetId) {
    const target = document.getElementById(targetId);
    if (!input.value) { target.textContent = ''; return; }
    const dob = new Date(input.value);
    const today = new Date();
    let age = today.getFullYear() - dob.getFullYear();
    const m = today.getMonth() - dob.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) age--;
    target.textContent = age >= 0 ? `Age: ${age} years old` : '';
  }

  // ===== Edit modal =====
  const editGradeSelect = document.getElementById('edit_grade_level');
  const editSectionSelect = document.getElementById('edit_section');

  function buildGradeOptions(selectEl, selected) {
    selectEl.innerHTML = '';
    @foreach ($gradeLevels as $level)
      selectEl.insertAdjacentHTML('beforeend', `<option value="{{ $level }}" ${selected === "{{ $level }}" ? 'selected' : ''}>Grade {{ $level }}</option>`);
    @endforeach
  }

  function openEditModal(data) {
    document.getElementById('edit-student-form').action = data.update_url;
    document.getElementById('edit_student_no').value = data.student_no || '';
    document.getElementById('edit_fingerprint_id').value = data.fingerprint_id || '';
    document.getElementById('edit_first_name').value = data.first_name || '';
    document.getElementById('edit_middle_name').value = data.middle_name || '';
    document.getElementById('edit_last_name').value = data.last_name || '';
    document.getElementById('edit_birth_date').value = data.birth_date || '';
    document.getElementById('edit_guardian_name').value = data.guardian_name || '';
    document.getElementById('edit_guardian_contact').value = data.guardian_contact || '';
    document.getElementById('edit_guardian_email').value = data.guardian_email || '';

    buildGradeOptions(editGradeSelect, data.grade_level);
    populateSections(editGradeSelect, editSectionSelect, data.section);
    updateAgePreview(document.getElementById('edit_birth_date'), 'edit-age-preview');

    editGradeSelect.onchange = () => populateSections(editGradeSelect, editSectionSelect, null);

    const overlay = document.getElementById('edit-student-overlay');
    const panel = document.getElementById('edit-student-panel');

    overlay.classList.remove('hidden');
    overlay.classList.add('flex');
    document.body.classList.add('overflow-hidden');

    // Start from the hidden state, then flip to visible on the next frame
    // so the browser actually animates the transition instead of snapping.
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
    const overlay = document.getElementById('edit-student-overlay');
    const panel = document.getElementById('edit-student-panel');

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
