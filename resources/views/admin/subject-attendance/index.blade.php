@extends('layouts.admin')

@section('title', 'Subject Attendance')

@section('content')
<div class="page-head" style="display:flex; align-items:flex-end; justify-content:space-between; gap:16px; flex-wrap:wrap;">
  <div>
    <div class="page-title">Subject Attendance</div>
    <div class="page-sub">Per-subject attendance taken by teachers, {{ $date->format('F j, Y') }}. Read-only.</div>
  </div>
  <form method="GET" style="display:flex; gap:8px; align-items:center;">
    <select name="teacher_id" onchange="this.form.submit()"
      style="padding:9px 12px; border:1px solid var(--border); border-radius:10px; font-size:12.5px;">
      <option value="">All teachers</option>
      @foreach ($teachers as $t)
        <option value="{{ $t->id }}" {{ request('teacher_id') == $t->id ? 'selected' : '' }}>{{ $t->name }} — {{ $t->assigned_subject }}</option>
      @endforeach
    </select>
    <input type="date" name="date" value="{{ $date->format('Y-m-d') }}" max="{{ now()->format('Y-m-d') }}"
      style="padding:9px 12px; border:1px solid var(--border); border-radius:10px; font-size:12.5px;"
      onchange="this.form.submit()">
  </form>
</div>

<div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:14px; margin-bottom:18px;">
  <div class="card" style="padding:15px 16px;">
    <div style="font-size:11.5px; color:var(--sub); font-weight:600;">Present</div>
    <div class="font-display" style="font-size:22px; font-weight:700; color:var(--primary); margin-top:6px;">{{ $present }}</div>
  </div>
  <div class="card" style="padding:15px 16px;">
    <div style="font-size:11.5px; color:var(--sub); font-weight:600;">Late</div>
    <div class="font-display" style="font-size:22px; font-weight:700; color:#C2660B; margin-top:6px;">{{ $late }}</div>
  </div>
  <div class="card" style="padding:15px 16px;">
    <div style="font-size:11.5px; color:var(--sub); font-weight:600;">Absent / Excused</div>
    <div class="font-display" style="font-size:22px; font-weight:700; color:var(--danger); margin-top:6px;">{{ $absent }}</div>
  </div>
  <div class="card" style="padding:15px 16px;">
    <div style="font-size:11.5px; color:var(--sub); font-weight:600;">Guardians emailed</div>
    <div class="font-display" style="font-size:22px; font-weight:700; margin-top:6px;">{{ $emailed }}</div>
  </div>
</div>

<div class="card">
  <div style="padding:14px 16px; border-bottom:1px solid var(--border); font-family:'Poppins',sans-serif; font-weight:700; font-size:14px;">
    Records ({{ $logs->count() }})
  </div>
  <table style="width:100%; border-collapse:collapse; font-size:12.5px;">
    <thead>
      <tr style="background:var(--dark); color:#fff;">
        <th style="text-align:left; padding:9px 14px; font-size:11px;">Student</th>
        <th style="text-align:left; padding:9px 14px; font-size:11px;">Section</th>
        <th style="text-align:left; padding:9px 14px; font-size:11px;">Subject</th>
        <th style="text-align:left; padding:9px 14px; font-size:11px;">Teacher</th>
        <th style="text-align:left; padding:9px 14px; font-size:11px;">Status</th>
        <th style="text-align:left; padding:9px 14px; font-size:11px;">Guardian Email</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($logs as $log)
        <tr style="border-bottom:1px solid #F1F2F0;">
          <td style="padding:9px 14px; font-weight:600;">{{ $log->student->fullName() }}</td>
          <td style="padding:9px 14px; color:var(--sub);">Gr.{{ $log->grade_level }} {{ $log->section }}</td>
          <td style="padding:9px 14px; color:var(--sub);">{{ $log->subject }}</td>
          <td style="padding:9px 14px; color:var(--sub);">{{ $log->teacher->name }}</td>
          <td style="padding:9px 14px;"><span class="badge {{ $log->badgeClass() }}">{{ $log->statusLabel() }}</span></td>
          <td style="padding:9px 14px; color:var(--sub);">
            @if ($log->guardian_notified_at)
              Sent {{ $log->guardian_notified_at->format('g:i A') }}
            @elseif ($log->guardian_notify_error)
              <span style="color:var(--danger);">Failed</span>
            @else
              —
            @endif
          </td>
        </tr>
      @empty
        <tr><td colspan="6" style="padding:24px 14px; text-align:center; color:var(--sub);">No subject attendance recorded for this date.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection
