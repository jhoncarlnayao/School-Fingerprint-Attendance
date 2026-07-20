@extends('layouts.admin')

@section('title', 'New Announcement')

@section('content')
<div class="page-head">
  <div class="page-title">Post Announcement</div>
  <div class="page-sub">This posts to the announcement board <strong>and</strong> emails it (via SMTP) straight to the registered inboxes of the audience you pick below — one step, nothing else to configure.</div>
</div>

<div class="card" style="padding:22px; max-width:560px;">
  <form method="POST" action="{{ route('admin.announcements.store') }}">
    @csrf
    <div class="form-field">
      <label for="title">Title</label>
      <input id="title" name="title" value="{{ old('title') }}" placeholder="e.g. No classes on Friday" required autofocus>
      @error('title') <div class="field-error">{{ $message }}</div> @enderror
    </div>

    <div class="form-field">
      <label for="audience">Send to</label>
      <select id="audience" name="audience" required>
        <option value="all" {{ old('audience') === 'all' ? 'selected' : '' }}>Everyone (Admins &amp; Teachers)</option>
        <option value="teachers" {{ old('audience', 'teachers') === 'teachers' ? 'selected' : '' }}>Teachers only</option>
        <option value="students" {{ old('audience') === 'students' ? 'selected' : '' }}>Students / Guardians (posted only — no email on file yet)</option>
      </select>
      <div class="form-hint">Emails go out to whatever address each teacher registered with — no extra setup needed.</div>
    </div>

    <div class="form-field">
      <label for="body">Message</label>
      <textarea id="body" name="body" rows="6" placeholder="Write the announcement..." required>{{ old('body') }}</textarea>
      @error('body') <div class="field-error">{{ $message }}</div> @enderror
    </div>

    <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">Post &amp; Email Announcement</button>
    <div class="form-hint">Make sure your <code>MAIL_*</code> SMTP settings in <code>.env</code> are configured — see the README.</div>
  </form>
</div>
@endsection
