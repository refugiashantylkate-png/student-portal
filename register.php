<?php require 'db.php';
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  check_csrf();
  $name = trim($_POST['full_name'] ?? ''); $email = trim($_POST['email'] ?? '');
  $pw = $_POST['password'] ?? '';
  if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $err = 'Enter your name and a valid email.';
  elseif (strlen($pw) < 8) $err = 'Password must be at least 8 characters.';
  elseif ($pw !== ($_POST['confirm'] ?? '')) $err = 'Passwords do not match.';
  else {
    $c = $pdo->prepare('SELECT id FROM users WHERE email = ?'); $c->execute([$email]);
    if ($c->fetch()) $err = 'That email is already registered.';
    else {
      $pdo->prepare('INSERT INTO users (full_name,email,password,role) VALUES (?,?,?,"student")')
          ->execute([$name, $email, password_hash($pw, PASSWORD_DEFAULT)]); // hashed
      header('Location: index.php'); exit;
    }
  }
}
head('Register'); ?>
<div class="row justify-content-center"><div class="col-md-5"><div class="card shadow-sm"><div class="card-body p-4">
<h1 class="h4 mb-3">Create student account</h1>
<?php if ($err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endif; ?>
<form method="post" class="needs-validation" novalidate>
<input type="hidden" name="csrf" value="<?= csrf() ?>">
<div class="mb-3"><label class="form-label">Full name</label><input name="full_name" class="form-control" maxlength="100" required value="<?= e($_POST['full_name'] ?? '') ?>"><div class="invalid-feedback">Name is required.</div></div>
<div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required value="<?= e($_POST['email'] ?? '') ?>"><div class="invalid-feedback">Enter a valid email.</div></div>
<div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" minlength="8" required><div class="invalid-feedback">At least 8 characters.</div></div>
<div class="mb-3"><label class="form-label">Confirm password</label><input type="password" name="confirm" class="form-control" required><div class="invalid-feedback">Passwords must match.</div></div>
<button class="btn btn-main w-100">Register</button></form>
<p class="mt-3 mb-0 small">Have an account? <a href="index.php">Log in</a></p>
</div></div></div></div>
<?php foot();
