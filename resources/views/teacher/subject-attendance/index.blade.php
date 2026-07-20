@extends('layouts.teacher')

@section('title', 'Subject Attendance')

@section('head')
<style>
.sa-table{width:100%; border-collapse:collapse; font-size:12.5px;}
.sa-table th{text-align:left; padding:9px 14px; font-size:10.5px; text-transform:uppercase; letter-spacing:.03em; background:var(--dark); color:#fff;}
.sa-table td{padding:8px 14px; border-bottom:1px solid #F1F2F0; vertical-align:middle;}
.sa-table tr:last-child td{border-bottom:none;}
.sa-name{font-weight:600;}
.sa-sub{color:var(--sub); font-size:11px;}

.seg{display:inline-flex; border:1px solid var(--border); border-radius:8px; overflow:hidden;}
.seg label{position:relative; cursor:pointer;}
.seg input{position:absolute; opacity:0; width:0; height:0;}
.seg span{display:inline-block; padding:6px 10px; font-size:11px; font-weight:700; color:var(--sub); border-right:1px solid var(--border); white-space:nowrap;}
.seg label:last-child span{border-right:none;}
.seg input:checked + span{color:#fff;}
.seg .st-present input:checked + span{background:#3F5A2A;}
.seg .st-late input:checked + span{background:#C2660B;}
.seg .st-absent input:checked + span{background:#B42323;}
.seg .st-excused input:checked + span{background:#6B7280;}
.seg span:hover{background:#F6F8F7;}

.notified-tag{font-size:10px; font-weight:700; color:#3F5A2A; display:inline-flex; align-items:center; gap:3px;}
.mark-all{display:flex; gap:6px; align-items:center; margin-bottom:10px;}
.mark-all button{font-size:11px; padding:5px 10px; border-radius:7px; border:1px solid var(--border); background:#fff; cursor:pointer; font-weight:600; color:var(--sub);}
.mark-all button:hover{background:#F6F8F7; color:var(--text);}
</style>
@endsection

@section('content')
@if (!empty($unassigned))
  <div class="welcome-row">
    <div>
      <div class="welcome">Subject Attendance</div>
      <div class="sub">You haven't been assigned to a section and subject yet.</div>
    </div>
  </div>
  <div class="card">
    <div class="empty" style="padding:36px 16px;">
      Ask your admin to assign you a grade level, section, and subject before you can take attendance.
    </div>
  </div>
@else
  <div class="welcome-row">
    <div>
      <div class="welcome">Subject Attendance</div>
      <div class="sub">{{ $teacher->assigned_subject }} · Grade {{ $teacher->assigned_grade_level }} - {{ $teacher->assigned_section }} · {{ $date->format('F j, Y') }}</div>
    </div>
    <form method="GET" style="display:flex; gap:8px; align-items:center;">
      <input type="date" name="date" value="{{ $date->format('Y-m-d') }}" max="{{ now()->format('Y-m-d') }}"
        style="padding:8px 11px; border:1px solid var(--border); border-radius:9px; font-size:12.5px;"
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
      <div class="stat-label">Not marked</div>
      <div class="stat-value">{{ $notMarked }}</div>
    </div>
  </div>

  <div class="card">
    <div class="card-head">
      <span>{{ $teacher->assigned_subject }} Roster ({{ $roster->count() }})</span>
      <span style="font-size:11px; color:var(--sub); font-weight:600;">Marking present/late emails the guardian automatically</span>
    </div>
    <div style="padding:12px 14px 0;">
      <div class="mark-all">
        <span style="font-size:11px; color:var(--sub); font-weight:600;">Mark all:</span>
        <button type="button" onclick="setAll('present')">Present</button>
        <button type="button" onclick="setAll('late')">Late</button>
        <button type="button" onclick="setAll('absent')">Absent</button>
      </div>
    </div>
    <form method="POST" action="{{ route('teacher.subject-attendance.store') }}">
      @csrf
      <input type="hidden" name="date" value="{{ $date->format('Y-m-d') }}">
      <table class="sa-table">
        <thead>
          <tr>
            <th>Student</th>
            <th>Status</th>
            <th>Guardian</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @forelse ($roster as $row)
            @php $current = $row['log']->status ?? 'present'; @endphp
            <tr>
              <td>
                <div class="sa-name">{{ $row['student']->fullName() }}</div>
                <div class="sa-sub">{{ $row['student']->student_no }}</div>
              </td>
              <td>
                <div class="seg" data-student="{{ $row['student']->id }}">
                  <label class="st-present"><input type="radio" name="statuses[{{ $row['student']->id }}]" value="present" {{ $current === 'present' ? 'checked' : '' }}><span>Present</span></label>
                  <label class="st-late"><input type="radio" name="statuses[{{ $row['student']->id }}]" value="late" {{ $current === 'late' ? 'checked' : '' }}><span>Late</span></label>
                  <label class="st-absent"><input type="radio" name="statuses[{{ $row['student']->id }}]" value="absent" {{ $current === 'absent' ? 'checked' : '' }}><span>Absent</span></label>
                  <label class="st-excused"><input type="radio" name="statuses[{{ $row['student']->id }}]" value="excused" {{ $current === 'excused' ? 'checked' : '' }}><span>Excused</span></label>
                </div>
              </td>
              <td>
                @if ($row['log']?->guardian_notified_at)
                  <span class="notified-tag">✓ Emailed {{ $row['log']->guardian_notified_at->format('g:i A') }}</span>
                @elseif (!$row['student']->guardian_email)
                  <span style="font-size:10.5px; color:var(--sub);">No guardian email on file</span>
                @else
                  <span style="font-size:10.5px; color:var(--sub);">Not yet</span>
                @endif
              </td>
              <td style="text-align:right;">
                <a href="{{ route('teacher.subject-attendance.show', $row['student']) }}" class="btn" style="padding:5px 10px; font-size:11px;">History</a>
              </td>
            </tr>
          @empty
            <tr><td colspan="4" style="padding:26px 16px; text-align:center; color:var(--sub);">No students in your section yet.</td></tr>
          @endforelse
        </tbody>
      </table>
      @if ($roster->isNotEmpty())
        <div style="padding:14px; display:flex; justify-content:flex-end;">
          <button type="submit" class="btn btn-primary">Save &amp; Notify Guardians</button>
        </div>
      @endif
    </form>
  </div>
@endif

<script>
function setAll(status) {
  document.querySelectorAll('.seg').forEach(function (seg) {
    var input = seg.querySelector('input[value="' + status + '"]');
    if (input) input.checked = true;
  });
}
</script>
@endsection
