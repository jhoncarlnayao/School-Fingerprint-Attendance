@extends('layouts.admin')

@section('title', 'Announcements')

@section('head')
<script src="https://cdn.tailwindcss.com"></script>
@endsection

@section('content')
@if (session('mail_error'))
  <div class="alert alert-error">Email delivery failed: {{ session('mail_error') }} — check <code>MAIL_*</code> in your <code>.env</code>.</div>
@endif
<div class="page-head" style="display:flex; align-items:flex-end; justify-content:space-between;">
  <div>
    <div class="page-title">Announcements</div>
    <div class="page-sub">Post updates for staff and students.</div>
  </div>
  <div style="display:flex; gap:10px;">
    <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary">+ New Announcement</a>
  </div>
</div>

<div class="card">
  <table style="width:100%; border-collapse:collapse; font-size:13px;">
    <thead>
      <tr style="background:var(--dark); color:#fff;">
        <th style="text-align:left; padding:12px 18px; font-size:11.5px;">Title</th>
        <th style="text-align:left; padding:12px 18px; font-size:11.5px;">Audience</th>
        <th style="text-align:left; padding:12px 18px; font-size:11.5px;">Posted by</th>
        <th style="text-align:left; padding:12px 18px; font-size:11.5px;">Status</th>
        <th style="text-align:left; padding:12px 18px; font-size:11.5px;">Date</th>
        <th style="text-align:left; padding:12px 18px; font-size:11.5px;"></th>
      </tr>
    </thead>
    <tbody>
      @forelse ($announcements as $a)
        <tr style="border-bottom:1px solid #F1F2F0;">
          <td style="padding:12px 18px;">
            <div style="font-weight:600;">{{ $a->title }}</div>
            <div style="font-size:12px; color:var(--sub); max-width:420px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $a->body }}</div>
          </td>
          <td style="padding:12px 18px;"><span class="badge badge-green">{{ ucfirst($a->audience) }}</span></td>
          <td style="padding:12px 18px; color:var(--sub);">{{ $a->author->name ?? '—' }}</td>
          <td style="padding:12px 18px;">
            @if($a->emailed)
              <span class="badge badge-green">Emailed</span>
            @else
              <span class="badge badge-orange">Posted only</span>
            @endif
          </td>
          <td style="padding:12px 18px; color:var(--sub);">{{ $a->created_at->format('M d, Y') }}</td>
          <td style="padding:12px 18px;">
            <button type="button" class="btn" style="padding:6px 12px; font-size:12px;"
              onclick='openEditModal({{ json_encode([
                "id" => $a->id,
                "title" => $a->title,
                "body" => $a->body,
                "audience" => $a->audience,
                "update_url" => route("admin.announcements.update", $a),
              ], JSON_HEX_APOS | JSON_HEX_QUOT) }})'>
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
              Edit
            </button>
          </td>
        </tr>
      @empty
        <tr><td colspan="6" style="padding:30px 18px; text-align:center; color:var(--sub);">No announcements yet.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

{{-- ===== Edit Announcement modal ===== --}}
<div
  id="edit-announcement-overlay"
  class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm opacity-0 transition-opacity duration-300 ease-out"
  onclick="if (event.target === this) closeEditModal()"
>
  <div
    id="edit-announcement-panel"
    class="w-full max-w-md mx-4 max-h-[88vh] overflow-y-auto rounded-2xl bg-white p-6 shadow-2xl ring-1 ring-black/5 scale-95 opacity-0 transition-all duration-300 ease-out"
  >
    <div class="flex items-center justify-between mb-5">
      <h2 class="text-lg font-bold text-slate-900">Edit Announcement</h2>
      <button type="button" onclick="closeEditModal()" class="grid h-8 w-8 place-items-center rounded-full text-slate-400 transition hover:bg-slate-100 hover:text-slate-700">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>

    <form id="edit-announcement-form" method="POST" class="space-y-4">
      @csrf
      @method('PATCH')

      <div>
        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Title</label>
        <input name="title" id="edit_title" required
          class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:ring-4 focus:ring-slate-100">
      </div>

      <div>
        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Send to</label>
        <select name="audience" id="edit_audience" required
          class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:ring-4 focus:ring-slate-100">
          <option value="all">Everyone (Admins &amp; Teachers)</option>
          <option value="teachers">Teachers only</option>
          <option value="students">Students / Guardians (posted only)</option>
        </select>
      </div>

      <div>
        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Message</label>
        <textarea name="body" id="edit_body" rows="6" required
          class="w-full rounded-lg border border-slate-200 px-3.5 py-2.5 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:ring-4 focus:ring-slate-100"></textarea>
      </div>

      <div class="rounded-lg bg-slate-50 px-3.5 py-2.5 text-xs text-slate-500">
        Saving changes here updates the posted announcement only — it will not re-send the email.
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
  function openEditModal(data) {
    document.getElementById('edit-announcement-form').action = data.update_url;
    document.getElementById('edit_title').value = data.title || '';
    document.getElementById('edit_audience').value = data.audience || 'all';
    document.getElementById('edit_body').value = data.body || '';

    const overlay = document.getElementById('edit-announcement-overlay');
    const panel = document.getElementById('edit-announcement-panel');

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
    const overlay = document.getElementById('edit-announcement-overlay');
    const panel = document.getElementById('edit-announcement-panel');

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
