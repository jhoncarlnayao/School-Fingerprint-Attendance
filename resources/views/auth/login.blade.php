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
  --dark:#152B0C;
  --dark2:#1B3812;
  --primary:#3F5A2A;
  --secondary:#8BC34A;
  --bg:#F3F6F2;
  --card:#FFFFFF;
  --border:#E5E7EB;
  --text:#1F2937;
  --sub:#6B7280;
  --danger:#EF4444;
}
*{box-sizing:border-box;}
body{
  margin:0; min-height:100vh; background:#D9DED6; color:var(--text);
  font-family:'Inter',sans-serif; -webkit-font-smoothing:antialiased;
  display:flex; align-items:center; justify-content:center; padding:32px;
}
.font-display{font-family:'Poppins',sans-serif;}

.auth-shell{
  width:100%; max-width:1070px; min-height:620px;
  background:var(--bg); border-radius:24px; overflow:hidden;
  display:flex; box-shadow:0 30px 60px rgba(21,43,12,.14);
}

/* ===== Left: form ===== */
.auth-left{
  flex:1; min-width:0; background:var(--card);
  padding:44px 60px; display:flex; flex-direction:column;
}
.brand{display:flex; align-items:center; gap:8px; margin-bottom:64px;}
.brand-dot{width:8px; height:8px; border-radius:50%; background:var(--dark);}
.brand-name{font-family:'Poppins',sans-serif; font-weight:700; font-size:16px; color:var(--dark);}

.auth-form-wrap{max-width:340px; margin:0 auto; width:100%; flex:1; display:flex; flex-direction:column; justify-content:center;}
.auth-title{font-family:'Poppins',sans-serif; font-weight:700; font-size:30px; color:var(--dark); margin:0 0 8px;}
.auth-sub{font-size:13.5px; color:var(--sub); margin:0 0 28px;}

.form-field{margin-bottom:18px;}
.form-field label{display:block; font-size:13px; font-weight:600; color:var(--text); margin-bottom:6px;}
.form-field input{
  width:100%; padding:11px 14px; border:1px solid var(--border); border-radius:10px;
  font-size:13.5px; font-family:'Inter',sans-serif; outline:none; background:#fff; color:var(--text);
}
.form-field input:focus{border-color:var(--secondary); box-shadow:0 0 0 3px rgba(139,195,74,.18);}
.field-error{color:var(--danger); font-size:12px; margin-top:6px;}

.form-row{display:flex; align-items:center; justify-content:space-between; margin-bottom:22px; font-size:13px;}
.remember{display:flex; align-items:center; gap:8px; color:var(--text);}
.remember input{width:15px; height:15px; accent-color:var(--primary);}
.forgot-link{color:var(--primary); font-weight:600; text-decoration:none;}
.forgot-link:hover{text-decoration:underline;}

.btn-signin{
  width:100%; padding:12px; border:none; border-radius:10px; cursor:pointer;
  background:var(--dark); color:#fff; font-size:14px; font-weight:600;
  font-family:'Inter',sans-serif; transition:background .15s ease;
}
.btn-signin:hover{background:var(--dark2);}

.role-hint{
  margin-top:22px; padding:12px 14px; border-radius:10px; background:#E7F3DE;
  color:var(--primary); font-size:12px; line-height:1.5; text-align:center;
}

.session-status{background:#E7F3DE; color:var(--primary); font-size:12.5px; padding:10px 14px; border-radius:10px; margin-bottom:18px; text-align:center;}
.session-error{background:#FBDADA; color:var(--danger); font-size:12.5px; padding:10px 14px; border-radius:10px; margin-bottom:18px; text-align:center;}

.auth-foot{font-size:11.5px; color:var(--sub); text-align:center; margin-top:auto; padding-top:24px;}

/* ===== Right: gradient art panel ===== */
.auth-right{
  flex:1; min-width:0; background:#EEF1EC; position:relative;
  display:flex; align-items:center; justify-content:center; overflow:hidden;
}
.blob-wrap{position:relative; width:280px; height:280px; display:flex; align-items:flex-end; justify-content:center;}
.blob-top{
  width:220px; height:110px; border-radius:110px 110px 0 0;
  background:linear-gradient(160deg, var(--secondary), var(--primary));
}
.blob-glow{
  position:absolute; top:110px; left:50%; transform:translateX(-50%);
  width:340px; height:190px; border-radius:50%;
  background:radial-gradient(closest-side, rgba(63,90,42,.55), rgba(63,90,42,0));
  filter:blur(6px);
}

@media (max-width:860px){
  .auth-right{display:none;}
  .auth-left{padding:36px 28px;}
}
</style>
</head>
<body>

<div class="auth-shell">
  <div class="auth-left">
    <div class="brand">
      <span class="brand-dot"></span>
      <span class="brand-name">BANTAY</span>
    </div>

    <div class="auth-form-wrap">
      <h1 class="auth-title">Welcome back</h1>
      <p class="auth-sub">Sign in with your admin or teacher account to continue.</p>

      @if (session('status'))
        <div class="session-status">{{ session('status') }}</div>
      @endif

      @if ($errors->any())
        <div class="session-error">{{ $errors->first() }}</div>
      @endif

      <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-field">
          <label for="email">Email</label>
          <input id="email" type="email" name="email" value="{{ old('email') }}"
                 placeholder="Enter your email" required autofocus>
        </div>

        <div class="form-field">
          <label for="password">Password</label>
          <input id="password" type="password" name="password"
                 placeholder="Enter your password" required>
        </div>

        <div class="form-row">
          <label class="remember">
            <input type="checkbox" name="remember">
            Remember for 30 days
          </label>
          <a class="forgot-link" href="#">Forgot password</a>
        </div>

        <button type="submit" class="btn-signin">Sign in</button>
      </form>

      <div class="role-hint">Accounts are provisioned by an administrator — Admin &amp; Teacher access only.</div>
    </div>

    <div class="auth-foot">&copy; BANTAY {{ date('Y') }}</div>
  </div>

  <div class="auth-right">
    <div class="blob-wrap">
      <div class="blob-glow"></div>
      <div class="blob-top"></div>
    </div>
  </div>
</div>

</body>
</html>
