<?php require 'db.php';
if (!empty($_SESSION['user'])) { header('Location: dashboard.php'); exit; }
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  check_csrf();
  $st = $pdo->prepare('SELECT * FROM users WHERE email = ?'); // prepared statement
  $st->execute([trim($_POST['email'] ?? '')]);
  $u = $st->fetch();
  if ($u && password_verify($_POST['password'] ?? '', $u['password'])) {
    session_regenerate_id(true);
    $_SESSION['user'] = ['id'=>$u['id'],'full_name'=>$u['full_name'],'role'=>$u['role']];
    header('Location: dashboard.php'); exit;
  }
  $err = 'Wrong email or password.';
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Log in | Student Portal</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box}
html,body{margin:0;min-height:100%}
body{min-height:100vh;display:grid;place-items:center;padding:24px 12px;background:radial-gradient(circle at 50% 40%,#0b1424 0%,#03060c 70%);font-family:'Inter',system-ui,sans-serif;color:#e8f4ff}
.shell{position:relative;width:min(92vw,440px);aspect-ratio:1;border-radius:50%;isolation:isolate}
.shell.round{aspect-ratio:auto;border-radius:36px}
.shell::before,.shell::after{content:"";position:absolute;border-radius:inherit;background:conic-gradient(from 0deg,#00f5ff,#0a5cff,#8a2be2,#00f5ff);animation:spin 6s linear infinite;z-index:-2}
.shell::before{inset:-6px;filter:blur(14px);opacity:.85}
.shell::after{inset:-2px}
.panel{position:relative;z-index:1;width:100%;height:100%;border-radius:inherit;background:#050a14;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:0 12%;text-align:center}
.round .panel{padding:34px 9%}
h1{font-size:30px;letter-spacing:2px;margin:0;color:#e9fdff;text-shadow:0 0 14px rgba(0,245,255,.6)}
.sub{font-size:12px;color:#8fb3c9;margin:4px 0 16px}
.f{position:relative;width:100%;margin-bottom:12px}
.f input{width:100%;padding:12px 18px;border-radius:30px;border:1px solid rgba(0,245,255,.25);background:rgba(255,255,255,.05);color:#fff;font-size:14px;outline:0;transition:.2s}
.f input::placeholder{color:#8fa6b8}
.f input:focus{border-color:#00f5ff;box-shadow:0 0 14px rgba(0,245,255,.45)}
.f .eye{position:absolute;right:14px;top:50%;transform:translateY(-50%);cursor:pointer;opacity:.7;font-size:13px;user-select:none}
.f input:user-invalid{border-color:#ff5c7a}
.btn{width:100%;padding:12px;border-radius:30px;border:2px solid #00f5ff;background:rgba(0,245,255,.08);color:#fff;font-weight:600;letter-spacing:2px;cursor:pointer;margin-top:4px;text-shadow:0 0 8px #00f5ff;box-shadow:0 0 16px rgba(0,245,255,.35),inset 0 0 14px rgba(0,245,255,.15);transition:.2s}
.btn:hover{background:rgba(0,245,255,.22);box-shadow:0 0 26px rgba(0,245,255,.65),inset 0 0 18px rgba(0,245,255,.25)}
.alt{font-size:12px;color:#8fb3c9;margin-top:16px}
.alt a{color:#00f5ff;font-weight:600;text-decoration:none}
.err{width:100%;font-size:12px;color:#ff8da1;background:rgba(255,92,122,.1);border:1px solid rgba(255,92,122,.4);border-radius:12px;padding:6px 10px;margin-bottom:10px}
.ok{color:#7dffb2}
@keyframes spin{to{transform:rotate(360deg)}}
@media (max-width:420px){h1{font-size:25px}.panel{padding:0 11%}}
</style></head><body>
<div class="shell"><div class="panel">
<h1>WELCOME</h1><div class="sub">Login to your account</div>
<?php if ($err): ?><div class="err"><?= e($err) ?></div><?php endif; ?>
<form method="post" style="width:100%">
<input type="hidden" name="csrf" value="<?= csrf() ?>">
<div class="f"><input type="email" name="email" placeholder="Email Address" required autocomplete="username"></div>
<div class="f"><input type="password" name="password" placeholder="Password" required autocomplete="current-password"><span class="eye">Show</span></div>
<button class="btn">LOGIN</button></form>
<div class="alt">Don't have an account? <a href="register.php">Sign Up</a></div>
</div></div>
<script>
document.querySelectorAll('.eye').forEach(function(b){b.addEventListener('click',function(){var i=b.parentNode.querySelector('input');i.type=i.type==='password'?'text':'password';});});
</script></body></html>
