<?php require 'db.php'; require_login('admin');
$id = (int)($_GET['id'] ?? 0); $g = ['user_id'=>'','subject'=>'','term'=>'','grade'=>''];
if ($id) { $s = $pdo->prepare('SELECT * FROM grades WHERE id=?'); $s->execute([$id]); $g = $s->fetch() ?: die('Not found.'); }
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  check_csrf();
  $g = ['user_id'=>(int)$_POST['user_id'],'subject'=>trim($_POST['subject']),'term'=>trim($_POST['term']),'grade'=>$_POST['grade']];
  if (!$g['user_id'] || $g['subject']==='' || $g['term']==='' || !is_numeric($g['grade']) || $g['grade']<0 || $g['grade']>100) $err = 'Fill all fields. Grade must be 0 to 100.';
  else {
    if ($id) $pdo->prepare('UPDATE grades SET user_id=?,subject=?,term=?,grade=? WHERE id=?')->execute([...array_values($g), $id]);
    else $pdo->prepare('INSERT INTO grades (user_id,subject,term,grade) VALUES (?,?,?,?)')->execute(array_values($g));
    header('Location: dashboard.php'); exit;
  }
}
$students = $pdo->query("SELECT id, full_name FROM users WHERE role='student' ORDER BY full_name")->fetchAll();
head($id ? 'Edit grade' : 'Add grade'); ?>
<div class="row justify-content-center"><div class="col-md-6"><div class="card shadow-sm"><div class="card-body p-4">
<h1 class="h4 mb-3"><?= $id ? 'Edit grade' : 'Add grade' ?></h1>
<?php if ($err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endif; ?>
<form method="post" class="needs-validation" novalidate><input type="hidden" name="csrf" value="<?= csrf() ?>">
<div class="mb-3"><label class="form-label">Student</label><select name="user_id" class="form-select" required><option value="">Choose a student</option>
<?php foreach ($students as $s): ?><option value="<?= (int)$s['id'] ?>" <?= $s['id']==$g['user_id']?'selected':'' ?>><?= e($s['full_name']) ?></option><?php endforeach; ?></select></div>
<div class="mb-3"><label class="form-label">Subject</label><input name="subject" class="form-control" maxlength="100" required value="<?= e($g['subject']) ?>"></div>
<div class="mb-3"><label class="form-label">Term</label><input name="term" class="form-control" maxlength="30" placeholder="e.g. 1st Sem 2026" required value="<?= e($g['term']) ?>"></div>
<div class="mb-3"><label class="form-label">Grade (0 to 100)</label><input type="number" step="0.01" min="0" max="100" name="grade" class="form-control" required value="<?= e($g['grade']) ?>"></div>
<button class="btn btn-main">Save grade</button> <a class="btn btn-link" href="dashboard.php">Cancel</a></form>
</div></div></div></div>
<?php foot();
