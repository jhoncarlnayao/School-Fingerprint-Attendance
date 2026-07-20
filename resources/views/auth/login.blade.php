<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BANTAY — Sign in</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{
  --dark:#1A1A1A;
  --dark2:#111111;
  --primary:#FF7A3D;
  --primary2:#E8672A;
  --secondary:#FFB088;
  --bg:#ECECEA;
  --card:#FFFFFF;
  --border:#ECECEC;
  --text:#1A1A1A;
  --sub:#8A8A8E;
  --success:#22C55E;
  --warning:#F5A623;
  --danger:#E5484D;
}
*{box-sizing:border-box;}
body{
  margin:0; min-height:100vh; background:var(--bg); color:var(--text);
  font-family:'Inter',sans-serif; -webkit-font-smoothing:antialiased;
  display:flex; align-items:center; justify-content:center; padding:32px;
}
.font-display{font-family:'Poppins',sans-serif;}

/* ===== Centered auth card ===== */
.auth-shell{
  width:100%; max-width:400px;
}

.brand-mark{
  width:44px; height:44px; border-radius:12px;
  background:linear-gradient(160deg, var(--primary), var(--primary2));
  display:flex; align-items:center; justify-content:center;
  margin-bottom:20px;
  box-shadow:0 8px 20px rgba(255,122,61,.28);
}
.brand-mark svg{width:22px; height:22px;}

.auth-title{font-family:'Poppins',sans-serif; font-weight:700; font-size:26px; color:var(--dark); margin:0 0 8px;}
.auth-sub{font-size:14px; color:var(--sub); margin:0 0 28px; line-height:1.5;}

.form-field{margin-bottom:14px; position:relative;}
.form-field input{
  width:100%; padding:13px 14px; border:1px solid var(--border); border-radius:10px;
  font-size:14px; font-family:'Inter',sans-serif; outline:none; background:var(--bg); color:var(--text);
  transition:border-color .15s ease, box-shadow .15s ease, background .15s ease;
}
.form-field input::placeholder{color:var(--sub);}
.form-field input:focus{border-color:var(--primary); background:#fff; box-shadow:0 0 0 3px rgba(255,122,61,.15);}
.field-error{color:var(--danger); font-size:12px; margin-top:6px;}

.field-toggle{
  position:absolute; right:14px; top:50%; transform:translateY(-50%);
  cursor:pointer; color:var(--sub); background:none; border:none; padding:0;
  display:flex; align-items:center;
}
.field-toggle svg{width:18px; height:18px;}

.btn-signin{
  width:100%; padding:13px; border:none; border-radius:10px; cursor:pointer;
  background:var(--dark); color:#fff; font-size:14.5px; font-weight:600;
  font-family:'Inter',sans-serif; transition:background .15s ease;
  margin-top:8px;
}
.btn-signin:hover{background:var(--dark2);}

.auth-links{margin-top:18px; font-size:13.5px; color:var(--text);}
.auth-links a{color:var(--primary); font-weight:600; text-decoration:none;}
.auth-links a:hover{text-decoration:underline;}
.forgot-row{margin-bottom:6px;}

.role-hint{
  margin-top:24px; padding:12px 14px; border-radius:10px; background:#FFF1E8;
  color:var(--primary2); font-size:12px; line-height:1.5; text-align:center;
}

.session-status{background:#EAF9EE; color:var(--success); font-size:12.5px; padding:10px 14px; border-radius:10px; margin-bottom:18px; text-align:center;}
.session-error{background:#FCE9E9; color:var(--danger); font-size:12.5px; padding:10px 14px; border-radius:10px; margin-bottom:18px; text-align:center;}

.auth-foot{font-size:11.5px; color:var(--sub); text-align:center; margin-top:32px;}
</style>
</head>
<body>

<div class="auth-shell">

  <div class="brand-mark">
    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M12 2L3 6v6c0 5 3.8 8.7 9 10 5.2-1.3 9-5 9-10V6l-9-4z" stroke="#fff" stroke-width="2" stroke-linejoin="round"/>
      <path d="M9 12l2 2 4-4" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
  </div>

  <h1 class="auth-title">Login to BANTAY</h1>
  <p class="auth-sub">Welcome back. Sign in with your admin or teacher account to continue.</p>

  @if (session('status'))
    <div class="session-status">{{ session('status') }}</div>
  @endif

  @if ($errors->any())
    <div class="session-error">{{ $errors->first() }}</div>
  @endif

  <form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="form-field">
      <input id="email" type="email" name="email" value="{{ old('email') }}"
             placeholder="Email or username" required autofocus>
    </div>

    <div class="form-field">
      <input id="password" type="password" name="password"
             placeholder="Password" required>
      <button type="button" class="field-toggle" onclick="togglePassword()" aria-label="Show password">
        <svg id="eyeIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/>
          <circle cx="12" cy="12" r="3"/>
        </svg>
      </button>
    </div>

    <div class="forgot-row">
      <a href="#" class="forgot-link" style="color:var(--primary); font-weight:600; text-decoration:none; font-size:13.5px;">Forgot password?</a>
    </div>

    <button type="submit" class="btn-signin">Log in</button>
  </form>

  <div class="role-hint">Accounts are provisioned by an administrator — Admin &amp; Teacher access only.</div>

  <div class="auth-foot">&copy; BANTAY {{ date('Y') }}</div>
</div>

<script>
function togglePassword(){
  const input = document.getElementById('password');
  const icon = document.getElementById('eyeIcon');
  if(input.type === 'password'){
    input.type = 'text';
    icon.innerHTML = '<path d="M17.94 17.94A10.94 10.94 0 0112 20c-7 0-11-8-11-8a20.3 20.3 0 015.06-6.06M9.9 4.24A10.94 10.94 0 0112 4c7 0 11 8 11 8a20.5 20.5 0 01-3.22 4.44M14.12 14.12a3 3 0 11-4.24-4.24" stroke-linecap="round" stroke-linejoin="round"/><line x1="1" y1="1" x2="23" y2="23"/>';
  } else {
    input.type = 'password';
    icon.innerHTML = '<path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/>';
  }
}
</script>

</body>
</html>