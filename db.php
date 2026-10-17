<?php
// Shared: DB connection (PDO), session, helpers, page layout
require 'config.php';
session_start();
try {
  $pdo = new PDO("mysql:host=$DB_HOST;port=$DB_PORT;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
} catch (PDOException $e) { die('Database connection failed: ' . $e->getMessage()); }

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); } // XSS prevention
function require_login($role = null) {
  if (empty($_SESSION['user'])) { header('Location: index.php'); exit; }
  if ($role && $_SESSION['user']['role'] !== $role) { http_response_code(403); die('Access denied.'); }
}
function csrf() { return $_SESSION['csrf'] ??= bin2hex(random_bytes(16)); }
function check_csrf() { if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) die('Invalid request.'); }
function head($title) { $u = $_SESSION['user'] ?? null; ?>
<!DOCTYPE html><html lang="en" data-bs-theme="dark"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?> | Student Portal</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
:root{--cy:#00f5ff;--bg:#050a14;--line:rgba(0,245,255,.22);--mut:#8fb3c9}
body{font-family:'Inter',system-ui,sans-serif;background:radial-gradient(circle at 50% 0%,#0b1424 0%,#03060c 65%) fixed;color:#e8f4ff;min-height:100vh;display:flex;flex-direction:column}
h1,h2,.serif{font-family:'Inter',sans-serif;font-weight:600;color:#e9fdff;letter-spacing:.5px;text-shadow:0 0 14px rgba(0,245,255,.45)}
.topbar{background:rgba(5,10,20,.92);border-bottom:1px solid var(--cy);box-shadow:0 2px 20px rgba(0,245,255,.25)}
.topbar .navbar-brand{color:#fff;font-weight:600;letter-spacing:1px;display:flex;align-items:center;gap:10px}
.logo{width:36px;height:36px;border-radius:50%;background:rgba(0,245,255,.1);border:2px solid var(--cy);color:var(--cy);display:grid;place-items:center;box-shadow:0 0 12px rgba(0,245,255,.5)}
.topbar .who{color:var(--mut);font-size:13px}
.btn-nav{color:#fff;border:1px solid rgba(0,245,255,.45);background:transparent;font-size:13px;border-radius:20px}
.btn-nav:hover{background:rgba(0,245,255,.15);color:#fff;box-shadow:0 0 12px rgba(0,245,255,.4)}
main{flex:1}
.card{background:var(--bg);border:1px solid var(--line);border-radius:18px;box-shadow:0 0 26px rgba(0,245,255,.12)}
.btn-main{background:rgba(0,245,255,.08);color:#fff;border:2px solid var(--cy);font-weight:600;letter-spacing:1px;padding:.5rem 1.2rem;border-radius:30px;text-shadow:0 0 8px var(--cy);box-shadow:0 0 14px rgba(0,245,255,.35),inset 0 0 12px rgba(0,245,255,.12)}
.btn-main:hover{background:rgba(0,245,255,.22);color:#fff;box-shadow:0 0 24px rgba(0,245,255,.6)}
.btn-outline-secondary{color:var(--cy);border-color:rgba(0,245,255,.5);border-radius:20px}
.btn-outline-secondary:hover{background:rgba(0,245,255,.15);color:#fff;border-color:var(--cy)}
.btn-outline-danger{border-radius:20px}
.btn-link{color:var(--cy)}
.form-control,.form-select{background-color:rgba(255,255,255,.05);color:#fff;border:1px solid rgba(0,245,255,.28);border-radius:30px;padding:.6rem 1rem}
.form-control:focus,.form-select:focus{background-color:rgba(255,255,255,.07);color:#fff;border-color:var(--cy);box-shadow:0 0 14px rgba(0,245,255,.45)}
.form-control::placeholder{color:#8fa6b8}
.form-control:disabled{background-color:rgba(255,255,255,.03);color:var(--mut)}
.form-select option{background:#050a14;color:#fff}
.form-label{font-size:13px;color:var(--mut)}
.table{--bs-table-bg:transparent;--bs-table-color:#e8f4ff;--bs-table-hover-bg:rgba(0,245,255,.06);--bs-table-hover-color:#fff;--bs-table-border-color:rgba(0,245,255,.12)}
.table thead th{background:rgba(0,245,255,.1);color:var(--cy);font-weight:500;font-size:13px;letter-spacing:.5px;border-bottom:1px solid var(--cy);white-space:nowrap}
.stat{background:var(--bg);border:1px solid var(--line);border-left:4px solid var(--cy);border-radius:12px;padding:14px 16px;box-shadow:0 0 16px rgba(0,245,255,.15)}
.stat .lbl{font-size:12px;text-transform:uppercase;letter-spacing:.6px;color:var(--mut)}
.stat .num{font-size:28px;font-weight:600;color:#fff;text-shadow:0 0 12px rgba(0,245,255,.6);line-height:1.2}
.badge-role{background:rgba(0,245,255,.15);color:var(--cy);font-size:11px;padding:3px 9px;border-radius:20px;text-transform:uppercase;letter-spacing:.5px;border:1px solid rgba(0,245,255,.4)}
.alert-danger{background:rgba(255,92,122,.1);border:1px solid rgba(255,92,122,.4);color:#ff9db0}
.alert-success{background:rgba(0,255,150,.08);border:1px solid rgba(0,255,150,.4);color:#7dffb2}
hr{border-color:var(--line);opacity:1}
footer.foot{text-align:center;font-size:12px;color:var(--mut);padding:18px}
</style>
</head><body class="<?= $u ? '' : 'auth' ?>">
<header class="topbar"><nav class="navbar"><div class="container">
<a class="navbar-brand" href="dashboard.php"><span class="logo"><svg viewBox="0 0 24 24" width="62%" height="62%" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4 1.5 9.5 12 15l10.5-5.5L12 4Z"/><path d="M5.5 12v4.5c0 1.4 2.9 2.8 6.5 2.8s6.5-1.4 6.5-2.8V12"/><path d="M22.5 9.5v6"/></svg></span>Student Portal</a>
<?php if ($u): ?><div class="d-flex align-items-center gap-2">
<span class="who d-none d-sm-inline"><?= e($u['full_name']) ?> <span class="badge-role"><?= e($u['role']) ?></span></span>
<a class="btn btn-sm btn-nav" href="profile.php">Profile</a><a class="btn btn-sm btn-nav" href="logout.php">Log out</a></div><?php endif; ?>
</div></nav></header><main class="container py-4">
<?php if (!$u): ?><div class="row justify-content-center"><div class="col-md-6 col-lg-5"><div class="brand-top"><div class="logo mx-auto" style="width:60px;height:60px;border-radius:50%"><svg viewBox="0 0 24 24" width="62%" height="62%" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4 1.5 9.5 12 15l10.5-5.5L12 4Z"/><path d="M5.5 12v4.5c0 1.4 2.9 2.8 6.5 2.8s6.5-1.4 6.5-2.8V12"/><path d="M22.5 9.5v6"/></svg></div><h2>Student Portal</h2><p>Academic records, simplified.</p></div></div></div><?php endif; ?>
<?php }
function foot() { echo '</main><footer class="foot">&copy; ' . date('Y') . ' Student Portal. All rights reserved.</footer><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script><script src="app.js"></script></body></html>'; }
