<?php require 'db.php'; require_login();
$id = $_SESSION['user']['id']; $msg = $err = '';
$st = $pdo->prepare('SELECT * FROM users WHERE id=?'); $st->execute([$id]); $me = $st->fetch();
if ($_SERVER['REQUEST_METHOD'] === 'POST') { // UPDATE own profile
  check_csrf();
  $name = trim($_POST['full_name'] ?? ''); $new = $_POST['new_password'] ?? '';
  if ($name === '') $err = 'Name is required.';
  elseif ($new !== '' && strlen($new) < 8) $err = 'New password must be at least 8 characters.';
  elseif ($new !== '' && !password_verify($_POST['current_password'] ?? '', $me['password'])) $err = 'Current password is wrong.';
  else {
    $pdo->prepare('UPDATE users SET full_name=? WHERE id=?')->execute([$name, $id]);
    if ($new !== '') $pdo->prepare('UPDATE users SET password=? WHERE id=?')->execute([password_hash($new, PASSWORD_DEFAULT), $id]);
    $_SESSION['user']['full_name'] = $name; $me['full_name'] = $name; $msg = 'Profile saved.';
  }
}
head('Profile'); ?>
<div class="row justify-content-center"><div class="col-md-6"><div class="card shadow-sm"><div class="card-body p-4">
<h1 class="h4 mb-3">My profile</h1>
<?php if ($err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endif; ?>
<?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
<form method="post" class="needs-validation" novalidate><input type="hidden" name="csrf" value="<?= csrf() ?>">
<div class="mb-3"><label class="form-label">Full name</label><input name="full_name" class="form-control" maxlength="100" required value="<?= e($me['full_name']) ?>"></div>
<div class="mb-3"><label class="form-label">Email</label><input class="form-control" value="<?= e($me['email']) ?>" disabled></div>
<hr><p class="small text-muted">Leave blank to keep your current password.</p>
<div class="mb-3"><label class="form-label">Current password</label><input type="password" name="current_password" class="form-control"></div>
<div class="mb-3"><label class="form-label">New password</label><input type="password" name="new_password" class="form-control" minlength="8"></div>
<button class="btn btn-main">Save changes</button> <a class="btn btn-link" href="dashboard.php">Back</a></form>
</div></div></div></div>
<?php foot();
