@extends('layouts.admin')

@section('title', 'Register Teacher')

@section('head')
<script src="https://cdn.tailwindcss.com"></script>
@endsection

@section('content')
<div class="page-head">
  <div class="page-title">Register Teacher</div>
  <div class="page-sub">Admin only — create a login account for a teacher.</div>
</div>

<div style="display:grid; grid-template-columns:380px 1fr; gap:20px; align-items:start;">
  <div class="card" style="padding:22px;">
    <form method="POST" action="{{ route('admin.teachers.store') }}" enctype="multipart/form-data">
      @csrf

      <div class="form-field" style="text-align:center;">
        <label>Profile picture</label>
        <div id="photo-preview" style="width:84px; height:84px; border-radius:50%; background:#F3F6F2; border:1px solid var(--border); margin:6px auto; display:flex; align-items:center; justify-content:center; overflow:hidden; color:var(--sub); font-size:11px;">No photo</div>
        <input id="profile_picture" type="file" name="profile_picture" accept="image/*" onchange="previewPhoto(this, 'photo-preview')">
        @error('profile_picture') <div class="field-error">{{ $message }}</div> @enderror
      </div>

      <div class="form-field">
        <label for="first_name">First name</label>
        <input id="first_name" name="first_name" value="{{ old('first_name') }}" placeholder="e.g. Maria" required autofocus>
        @error('first_name') <div class="field-error">{{ $message }}</div> @enderror
      </div>

      <div class="form-field">
        <label for="middle_name">Middle name</label>
        <input id="middle_name" name="middle_name" value="{{ old('middle_name') }}" placeholder="Optional">
        @error('middle_name') <div class="field-error">{{ $message }}</div> @enderror
      </div>

      <div class="form-field">
        <label for="last_name">Last name</label>
        <input id="last_name" name="last_name" value="{{ old('last_name') }}" placeholder="e.g. Santos" required>
        @error('last_name') <div class="field-error">{{ $message }}</div> @enderror
      </div>

      <div class="form-field">
        <label for="birth_date">Birthday</label>
        <input id="birth_date" type="date" name="birth_date" value="{{ old('birth_date') }}" max="{{ now()->subDay()->format('Y-m-d') }}" oninput="updateAgePreview(this, 'age-preview')">
        <div id="age-preview" style="font-size:12px; color:var(--sub); margin-top:6px;"></div>
        @error('birth_date') <div class="field-error">{{ $message }}</div> @enderror
      </div>

      <div class="form-field">
        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="teacher@school.edu" required>
        @error('email') <div class="field-error">{{ $message }}</div> @enderror
      </div>

      <div class="form-field">
        <label for="password">Temporary password</label>
        <input id="password" type="password" name="password" placeholder="Min. 8 characters" required>
        @error('password') <div class="field-error">{{ $message }}</div> @enderror
      </div>

      <div class="form-field">
        <label for="password_confirmation">Confirm password</label>
        <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Re-enter password" required>
      </div>

      <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">Create Teacher Account</button>
      <div class="form-hint">The teacher can sign in right away at <strong>/login</strong>. Assign them to a section afterward.</div>
    </form>
  </div>

  <div class="card">
    <div style="padding:16px 18px; border-bottom:1px solid var(--border); font-family:'Poppins',sans-serif; font-weight:700; font-size:15px;">
      All Teachers ({{ $teachers->count() }})
    </div>
    <table style="width:100%; border-collapse:collapse; font-size:13px;">
      <thead>
        <tr style="background:var(--dark); color:#fff;">
          <th style="text-align:left; padding:10px 16px; font-size:11.5px;">Name</th>
          <th style="text-align:left; padding:10px 16px; font-size:11.5px;">Email</th>
          <th style="text-align:left; padding:10px 16px; font-size:11.5px;">Age</th>
          <th style="text-align:left; padding:10px 16px; font-size:11.5px;">Section</th>
          <th style="text-align:left; padding:10px 16px; font-size:11.5px;">Status</th>
          <th style="text-align:left; padding:10px 16px; font-size:11.5px;">Joined</th>
          <th style="text-align:left; padding:10px 16px; font-size:11.5px;"></th>
        </tr>
      </thead>
      <tbody>
        @forelse ($teachers as $teacher)
          <tr style="border-bottom:1px solid #F1F2F0;">
            <td style="padding:11px 16px; font-weight:600;">
              <div style="display:flex; align-items:center; gap:10px;">
                @if ($teacher->profilePictureUrl())
                  <img src="{{ $teacher->profilePictureUrl() }}" alt="" style="width:30px; height:30px; border-radius:50%; object-fit:cover;">
                @else
                  <div class="user-avatar" style="width:30px; height:30px; font-size:11.5px;">{{ $teacher->initials() }}</div>
                @endif
                {{ $teacher->name }}
              </div>
            </td>
            <td style="padding:11px 16px; color:var(--sub);">{{ $teacher->email }}</td>
            <td style="padding:11px 16px; color:var(--sub);">
              @if ($teacher->age() !== null)
                {{ $teacher->age() }} yrs
              @else
                <span style="color:#C2660B;">—</span>
              @endif
            </td>
            <td style="padding:11px 16px;">
              @if($teacher->assigned_section)
                <span class="badge badge-green">Gr.{{ $teacher->assigned_grade_level }} — {{ $teacher->assigned_section }}</span>
              @else
                <span class="badge badge-orange">Unassigned</span>
              @endif
            </td>
            <td style="padding:11px 16px;">
              @if($teacher->is_active)
                <span class="badge badge-green">Active</span>
              @else
                <span class="badge badge-red" title="{{ $teacher->disabled_reason }}">Disabled</span>
              @endif
            </td>
            <td style="padding:11px 16px; color:var(--sub);">{{ $teacher->created_at->format('M d, Y') }}</td>
            <td style="padding:11px 16px; white-space:nowrap;">
              <div style="display:flex; gap:6px; flex-wrap:wrap;">
                <button type="button" class="btn" style="padding:6px 12px; font-size:12px;"
                  onclick='openViewTeacherModal({{ json_encode([
                    "name" => $teacher->name,
                    "email" => $teacher->email,
                    "photo" => $teacher->profilePictureUrl(),
                    "initials" => $teacher->initials(),
                    "birth_date" => optional($teacher->birth_date)->format("M d, Y"),
                    "age" => $teacher->age(),
                    "grade_level" => $teacher->assigned_grade_level,
                    "section" => $teacher->assigned_section,
                    "subject" => $teacher->assigned_subject,
                    "is_active" => $teacher->is_active,
                    "disabled_reason" => $teacher->disabled_reason,
                    "joined" => $teacher->created_at->format("M d, Y"),
                  ], JSON_HEX_APOS | JSON_HEX_QUOT) }})'>
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"/><circle cx="12" cy="12" r="3"/></svg>
                  View
                </button>
                <button type="button" class="btn" style="padding:6px 12px; font-size:12px;"
                  onclick='openEditTeacherModal({{ json_encode([
                    "id" => $teacher->id,
                    "first_name" => $teacher->first_name,
                    "middle_name" => $teacher->middle_name,
                    "last_name" => $teacher->last_name,
                    "birth_date" => optional($teacher->birth_date)->format("Y-m-d"),
                    "email" => $teacher->email,
                    "photo" => $teacher->profilePictureUrl(),
                    "initials" => $teacher->initials(),
                    "update_url" => route("admin.teachers.update", $teacher),
                  ], JSON_HEX_APOS | JSON_HEX_QUOT) }})'>
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                  Edit
                </button>
                @if($teacher->is_active)
                  <button type="button" class="btn" style="padding:6px 12px; font-size:12px; color:var(--danger); border-color:#F3C9C9;"
                    onclick='openDisableModal({{ json_encode([
                      "id" => $teacher->id,
                      "name" => $teacher->name,
                      "update_url" => route("admin.teachers.disable", $teacher),
                    ], JSON_HEX_APOS | JSON_HEX_QUOT) }})'>
                    Disable
                  </button>
                @else
                  <form method="POST" action="{{ route('admin.teachers.enable', $teacher) }}" style="display:inline;" onsubmit="return confirm('Re-enable {{ $teacher->name }}\'s account?');">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn" style="padding:6px 12px; font-size:12px; color:var(--primary); border-color:#CFE3BE;">Enable</button>
                  </form>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="7" style="padding:26px 16px; text-align:center; color:var(--sub);">No teachers yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- ===== View Teacher modal ===== --}}
<div
  id="view-teacher-overlay"
  class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm opacity-0 transition-opacity duration-300 ease-out"
  onclick="if (event.target === this) closeViewTeacherModal()"
>
  <div
    id="view-teacher-panel"
    class="w-full max-w-md mx-4 max-h-[88vh] overflow-y-auto rounded-2xl bg-white p-6 shadow-2xl ring-1 ring-black/5 scale-95 opacity-0 transition-all duration-300 ease-out"
  >
    <div class="flex items-center justify-between mb-5">
      <h2 class="text-lg font-bold text-slate-900">Teacher Information</h2>
      <button type="button" onclick="closeViewTeacherModal()" class="grid h-8 w-8 place-items-center rounded-full text-slate-400 transition hover:bg-slate-100 hover:text-slate-700">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>

    <div class="flex items-center gap-3 mb-5">
      <div id="view-teacher-photo" style="width:56px; height:56px; border-radius:50%; background:#F3F6F2; border:1px solid #E5E7EB; display:flex; align-items:center; justify-content:center; overflow:hidden; color:#6B7280; font-weight:700; font-family:'Poppins',sans-serif; font-size:18px; flex-shrink:0;"></div>
      <div>
        <div id="view-teacher-name" class="text-base font-bold text-slate-900"></div>
        <div id="view-teacher-email" class="text-xs text-slate-500"></div>
      </div>
    </div>

    <dl class="space-y-3 text-sm">
      <div class="flex justify-between gap-3 border-b border-slate-100 pb-2">
        <dt class="font-semibold text-slate-500">Birthday</dt>
        <dd id="view-teacher-birthday" class="text-right text-slate-900"></dd>
      </div>
      <div class="flex justify-between gap-3 border-b border-slate-100 pb-2">
        <dt class="font-semibold text-slate-500">Age</dt>
        <dd id="view-teacher-age" class="text-right text-slate-900"></dd>
      </div>
      <div class="flex justify-between gap-3 border-b border-slate-100 pb-2">
        <dt class="font-semibold text-slate-500">Assigned section</dt>
        <dd id="view-teacher-section" class="text-right text-slate-900"></dd>
      </div>
      <div class="flex justify-between gap-3 border-b border-slate-100 pb-2">
        <dt class="font-semibold text-slate-500">Subject</dt>
        <dd id="view-teacher-subject" class="text-right text-slate-900"></dd>
      </div>
      <div class="flex justify-between gap-3 border-b border-slate-100 pb-2">
        <dt class="font-semibold text-slate-500">Status</dt>
        <dd id="view-teacher-status" class="text-right"></dd>
      </div>
      <div id="view-teacher-disabled-reason-wrap" class="hidden rounded-lg bg-red-50 px-3.5 py-2.5 text-xs text-red-600">
        <span class="font-semibold">Disabled reason:</span> <span id="view-teacher-disabled-reason"></span>
      </div>
      <div class="flex justify-between gap-3">
        <dt class="font-semibold text-slate-500">Joined</dt>
        <dd id="view-teacher-joined" class="text-right text-slate-900"></dd>
      </div>
    </dl>

    <button type="button" onclick="closeViewTeacherModal()"
      class="mt-6 w-full rounded-lg border border-slate-200 bg-white py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
      Close
    </button>
  </div>
</div>

{{-- ===== Edit Teacher modal ===== --}}
<div
  id="edit-teacher-overlay"
  class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm opacity-0 transition-opacity duration-300 ease-out"
  onclick="if (event.target === this) closeEditTeacherModal()"
>
  <div
    id="edit-teacher-panel"
    class="w-full max-w-md mx-4 max-h-[88vh] overflow-y-auto rounded-2xl bg-white p-6 shadow-2xl ring-1 ring-black/5 scale-95 opacity-0 transition-all duration-300 ease-out"
  >
    <div class="flex items-center justify-between mb-5">
      <h2 class="text-lg font-bold text-slate-900">Edit Teacher</h2>
      <button type="button" onclick="closeEditTeacherModal()" class="grid h-8 w-8 place-items-center rounded-full text-slate-400 transition hover:bg-slate-100 hover:text-slate-700">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>

    <form id="edit-teacher-form" method="POST" enctype="multipart/form-data" class="space-y-4">
      @csrf
      @method('PATCH')

      <div class="text-center">
        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Profile picture</label>
        <div id="edit-teacher-photo-preview" style="width:76px; height:76px; border-radius:50%; background:#F3F6F2; border:1px solid #E5E7EB; margin:6px auto; display:flex; align-items:center; justify-content:center; overflow:hidden; color:#6B7280; font-size:11px;">No photo</div>
        <input type="file" name="profile_picture" accept="image/*" onchange="previewPhoto(this, 'edit-teacher-photo-preview')"
          class="mx-auto block text-xs">
      </div>

      <div>
        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">First name</label>
        <input name="first_name" id="edit_teacher_first_name" required
          class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:ring-4 focus:ring-slate-100">
      </div>

      <div>
        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Middle name</label>
        <input name="middle_name" id="edit_teacher_middle_name" placeholder="Optional"
          class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:ring-4 focus:ring-slate-100">
      </div>

      <div>
        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Last name</label>
        <input name="last_name" id="edit_teacher_last_name" required
          class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:ring-4 focus:ring-slate-100">
      </div>

      <div>
        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Birthday</label>
        <input type="date" name="birth_date" id="edit_teacher_birth_date" max="{{ now()->subDay()->format('Y-m-d') }}"
          class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:ring-4 focus:ring-slate-100">
      </div>

      <div>
        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Email</label>
        <input type="email" name="email" id="edit_teacher_email" required
          class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:ring-4 focus:ring-slate-100">
      </div>

      <div class="flex gap-3 pt-2">
        <button type="button" onclick="closeEditTeacherModal()"
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

{{-- ===== Disable Teacher modal ===== --}}
<div
  id="disable-teacher-overlay"
  class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm opacity-0 transition-opacity duration-300 ease-out"
  onclick="if (event.target === this) closeDisableModal()"
>
  <div
    id="disable-teacher-panel"
    class="w-full max-w-md mx-4 max-h-[88vh] overflow-y-auto rounded-2xl bg-white p-6 shadow-2xl ring-1 ring-black/5 scale-95 opacity-0 transition-all duration-300 ease-out"
  >
    <div class="flex items-center justify-between mb-5">
      <h2 class="text-lg font-bold text-slate-900">Disable Teacher Account</h2>
      <button type="button" onclick="closeDisableModal()" class="grid h-8 w-8 place-items-center rounded-full text-slate-400 transition hover:bg-slate-100 hover:text-slate-700">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>

    <div id="disable-teacher-name" class="mb-4 text-sm font-semibold text-slate-500"></div>

    <form id="disable-teacher-form" method="POST" class="space-y-4">
      @csrf
      @method('PATCH')

      <div>
        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Reason for disabling</label>
        <textarea name="disabled_reason" id="disabled_reason" rows="4" required placeholder="e.g. Account under review by admin office."
          class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:ring-4 focus:ring-slate-100"></textarea>
      </div>

      <div class="rounded-lg bg-red-50 px-3.5 py-2.5 text-xs text-red-600">
        This teacher will not be able to sign in while disabled. This reason will be shown to them on the login page.
      </div>

      <div class="flex gap-3 pt-2">
        <button type="button" onclick="closeDisableModal()"
          class="flex-1 rounded-lg border border-slate-200 bg-white py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
          Cancel
        </button>
        <button type="submit"
          class="flex-1 rounded-lg bg-red-600 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700 active:scale-[0.98]">
          Disable Account
        </button>
      </div>
    </form>
  </div>
</div>

<script>
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

  // ===== View modal =====
  function openViewTeacherModal(data) {
    const photo = document.getElementById('view-teacher-photo');
    if (data.photo) {
      photo.innerHTML = `<img src="${data.photo}" style="width:100%; height:100%; object-fit:cover;">`;
    } else {
      photo.innerHTML = data.initials || '?';
    }

    document.getElementById('view-teacher-name').textContent = data.name || '';
    document.getElementById('view-teacher-email').textContent = data.email || '';
    document.getElementById('view-teacher-birthday').textContent = data.birth_date || '—';
    document.getElementById('view-teacher-age').textContent = data.age !== null && data.age !== undefined ? `${data.age} yrs` : '—';
    document.getElementById('view-teacher-section').textContent = (data.grade_level && data.section) ? `Grade ${data.grade_level} — ${data.section}` : 'Unassigned';
    document.getElementById('view-teacher-subject').textContent = data.subject || '—';
    document.getElementById('view-teacher-joined').textContent = data.joined || '';

    const statusEl = document.getElementById('view-teacher-status');
    const reasonWrap = document.getElementById('view-teacher-disabled-reason-wrap');
    if (data.is_active) {
      statusEl.innerHTML = '<span class="badge badge-green">Active</span>';
      reasonWrap.classList.add('hidden');
    } else {
      statusEl.innerHTML = '<span class="badge badge-red">Disabled</span>';
      document.getElementById('view-teacher-disabled-reason').textContent = data.disabled_reason || '—';
      reasonWrap.classList.remove('hidden');
    }

    const overlay = document.getElementById('view-teacher-overlay');
    const panel = document.getElementById('view-teacher-panel');

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

    document.addEventListener('keydown', closeViewTeacherOnEscape);
  }

  function closeViewTeacherModal() {
    const overlay = document.getElementById('view-teacher-overlay');
    const panel = document.getElementById('view-teacher-panel');

    overlay.classList.remove('opacity-100');
    overlay.classList.add('opacity-0');
    panel.classList.remove('opacity-100', 'scale-100');
    panel.classList.add('opacity-0', 'scale-95');

    document.removeEventListener('keydown', closeViewTeacherOnEscape);

    setTimeout(() => {
      overlay.classList.add('hidden');
      overlay.classList.remove('flex');
      document.body.classList.remove('overflow-hidden');
    }, 300);
  }

  function closeViewTeacherOnEscape(e) {
    if (e.key === 'Escape') closeViewTeacherModal();
  }

  // ===== Edit modal =====
  function openEditTeacherModal(data) {
    document.getElementById('edit-teacher-form').action = data.update_url;
    document.getElementById('edit_teacher_first_name').value = data.first_name || '';
    document.getElementById('edit_teacher_middle_name').value = data.middle_name || '';
    document.getElementById('edit_teacher_last_name').value = data.last_name || '';
    document.getElementById('edit_teacher_birth_date').value = data.birth_date || '';
    document.getElementById('edit_teacher_email').value = data.email || '';

    const preview = document.getElementById('edit-teacher-photo-preview');
    if (data.photo) {
      preview.innerHTML = `<img src="${data.photo}" style="width:100%; height:100%; object-fit:cover;">`;
    } else {
      preview.innerHTML = data.initials || 'No photo';
    }
    document.getElementById('edit-teacher-form').querySelector('input[type=file]').value = '';

    const overlay = document.getElementById('edit-teacher-overlay');
    const panel = document.getElementById('edit-teacher-panel');

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

    document.addEventListener('keydown', closeEditTeacherOnEscape);
  }

  function closeEditTeacherModal() {
    const overlay = document.getElementById('edit-teacher-overlay');
    const panel = document.getElementById('edit-teacher-panel');

    overlay.classList.remove('opacity-100');
    overlay.classList.add('opacity-0');
    panel.classList.remove('opacity-100', 'scale-100');
    panel.classList.add('opacity-0', 'scale-95');

    document.removeEventListener('keydown', closeEditTeacherOnEscape);

    setTimeout(() => {
      overlay.classList.add('hidden');
      overlay.classList.remove('flex');
      document.body.classList.remove('overflow-hidden');
    }, 300);
  }

  function closeEditTeacherOnEscape(e) {
    if (e.key === 'Escape') closeEditTeacherModal();
  }

  // ===== Disable modal =====
  function openDisableModal(data) {
    document.getElementById('disable-teacher-form').action = data.update_url;
    document.getElementById('disable-teacher-name').textContent = data.name || '';
    document.getElementById('disabled_reason').value = '';

    const overlay = document.getElementById('disable-teacher-overlay');
    const panel = document.getElementById('disable-teacher-panel');

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

    document.addEventListener('keydown', closeDisableOnEscape);
  }

  function closeDisableModal() {
    const overlay = document.getElementById('disable-teacher-overlay');
    const panel = document.getElementById('disable-teacher-panel');

    overlay.classList.remove('opacity-100');
    overlay.classList.add('opacity-0');
    panel.classList.remove('opacity-100', 'scale-100');
    panel.classList.add('opacity-0', 'scale-95');

    document.removeEventListener('keydown', closeDisableOnEscape);

    setTimeout(() => {
      overlay.classList.add('hidden');
      overlay.classList.remove('flex');
      document.body.classList.remove('overflow-hidden');
    }, 300);
  }

  function closeDisableOnEscape(e) {
    if (e.key === 'Escape') closeDisableModal();
  }
</script>
@endsection
