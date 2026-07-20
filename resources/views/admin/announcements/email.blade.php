@extends('layouts.admin')

@section('title', 'Email Announcement')

@section('content')
<div class="page-head">
  <div class="page-title">Email an Announcement</div>
  <div class="page-sub">Send an existing announcement, or write a new one, straight to inboxes.</div>
</div>

<div class="card" style="padding:22px; max-width:560px;">
  <form method="POST" action="{{ route('admin.announcements.email.send') }}">
    @csrf

    <div class="form-field">
      <label for="announcement_id">Use an existing announcement (optional)</label>
      <select id="announcement_id" name="announcement_id" onchange="fillFromExisting(this)">
        <option value="">— Write a new one below —</option>
        @foreach ($announcements as $a)
          <option value="{{ $a->id }}" data-title="{{ $a->title }}" data-body="{{ $a->body }}" data-audience="{{ $a->audience }}">{{ $a->title }}</option>
        @endforeach
      </select>
    </div>

    <div class="form-field">
      <label for="title">Title</label>
      <input id="title" name="title" value="{{ old('title') }}" placeholder="e.g. Parent-Teacher Conference">
      @error('title') <div class="field-error">{{ $message }}</div> @enderror
    </div>

    <div class="form-field">
      <label for="audience">Send to</label>
      <select id="audience" name="audience" required>
        <option value="all">Everyone (Admins &amp; Teachers)</option>
        <option value="teachers">Teachers only</option>
        <option value="students">Students / Guardians</option>
      </select>
    </div>

    <div class="form-field">
      <label for="body">Message</label>
      <textarea id="body" name="body" rows="6" placeholder="Write the email body...">{{ old('body') }}</textarea>
      @error('body') <div class="field-error">{{ $message }}</div> @enderror
    </div>

    <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">Send Email</button>
    <div class="form-hint">Make sure your <code>MAIL_*</code> settings in <code>.env</code> are configured before sending.</div>
  </form>
</div>

<script>
function fillFromExisting(select) {
  const opt = select.options[select.selectedIndex];
  if (!opt.value) return;
  document.getElementById('title').value = opt.dataset.title || '';
  document.getElementById('body').value = opt.dataset.body || '';
  document.getElementById('audience').value = opt.dataset.audience || 'all';
}
</script>
@endsection
