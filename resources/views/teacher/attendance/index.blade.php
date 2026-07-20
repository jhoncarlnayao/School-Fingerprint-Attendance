@extends('layouts.teacher')

@section('title', 'Attendance')

@section('content')
@if (!empty($unassigned))
  <div class="welcome-row">
    <div>
      <div class="welcome">Attendance</div>
      <div class="sub">You haven't been assigned to a section yet.</div>
    </div>
  </div>
  <div class="card">
    <div class="empty" style="padding:32px 15px;">
      Ask your admin to assign you a grade level and section before you can view attendance.
    </div>
  </div>
@else
  <div class="welcome-row">
    <div>
      <div class="welcome">Attendance</div>
      <div class="sub">Grade {{ $teacher->assigned_grade_level }} - {{ $teacher->assigned_section }} · {{ $date->format('F j, Y') }}</div>
    </div>
    <form method="GET" style="display:flex; gap:8px; align-items:center;">
      <input type="date" name="date" value="{{ $date->format('Y-m-d') }}" max="{{ now()->format('Y-m-d') }}"
        style="padding:7px 10px; border:1px solid var(--border); border-radius:8px; font-size:12px;"
        onchange="this.form.submit()">
    </form>
  </div>

  <div class="grid">
    <div class="stat">
      <div class="stat-label">Present</div>
      <div class="stat-value" style="color:var(--primary);">{{ $present }}</div>
    </div>
    <div class="stat">
      <div class="stat-label">Late</div>
      <div class="stat-value" style="color:#C2660B;">{{ $late }}</div>
    </div>
    <div class="stat">
      <div class="stat-label">Absent</div>
      <div class="stat-value" style="color:var(--danger);">{{ $absent }}</div>
    </div>
    <div class="stat">
      <div class="stat-label">Not yet scanned</div>
      <div class="stat-value">{{ $notYetScanned }}</div>
    </div>
  </div>

  <div class="panels" style="grid-template-columns:1.4fr 1fr;">
    <div class="card">
      <div class="card-head">My Section Roster ({{ $roster->count() }})</div>
      <table class="tbl">
        <thead>
          <tr>
            <th>Student</th>
            <th>Time In</th>
            <th>Time Out</th>
            <th>Status</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @forelse ($roster as $row)
            <tr>
              <td style="font-weight:600;">{{ $row['student']->fullName() }}</td>
              <td style="color:var(--sub);">{{ $row['log']?->timeInFormatted() ?? '—' }}</td>
              <td style="color:var(--sub);">{{ $row['log']?->timeOutFormatted() ?? '—' }}</td>
              <td>
                @if ($row['log'])
                  <span class="badge {{ $row['log']->badgeClass() }}">{{ $row['log']->statusLabel() }}</span>
                @else
                  <span class="badge" style="background:#F1F2F0; color:var(--sub);">No scan yet</span>
                @endif
              </td>
              <td style="text-align:right;">
                <a href="{{ route('teacher.attendance.show', $row['student']) }}" class="btn" style="padding:5px 10px; font-size:11px;">History</a>
              </td>
            </tr>
          @empty
            <tr><td colspan="5" style="padding:24px 15px; text-align:center; color:var(--sub);">No students in your section yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="card" style="padding:15px 16px;">
      <div class="font-display" style="font-weight:700; font-size:13.5px; margin-bottom:11px;">
        Quick Manual Override
      </div>
      @if($unscannedStudents->isEmpty())
        <div style="font-size:12px; color:var(--sub);">Everyone in your section has a scan recorded for this date.</div>
      @else
        <form method="POST" action="" id="manual-override-form">
          @csrf
          <div class="form-field">
            <label>Student (no scan yet)</label>
            <select id="manual-student" required onchange="document.getElementById('manual-override-form').action = this.options[this.selectedIndex].dataset.url">
              <option value="" disabled selected>Select student</option>
              @foreach ($unscannedStudents as $s)
                <option value="{{ $s->id }}" data-url="{{ route('teacher.attendance.mark', $s) }}">{{ $s->fullName() }}</option>
              @endforeach
            </select>
          </div>
          <input type="hidden" name="date" value="{{ $date->format('Y-m-d') }}">
          <div class="form-field">
            <label>Status</label>
            <select name="status" required>
              <option value="present">Present</option>
              <option value="late">Late</option>
              <option value="absent">Absent</option>
              <option value="excused">Excused</option>
            </select>
          </div>
          <div class="form-field">
            <label>Note</label>
            <input name="note" placeholder="e.g. scanner offline">
          </div>
          <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">Save Override</button>
        </form>
      @endif
    </div>
  </div>
@endif
@endsection