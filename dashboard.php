<?php require 'db.php'; require_login();
$u = $_SESSION['user']; $admin = $u['role'] === 'admin';
if ($admin && $_SERVER['REQUEST_METHOD'] === 'POST') { // DELETE
  check_csrf();
  $pdo->prepare('DELETE FROM grades WHERE id = ?')->execute([(int)$_POST['delete_id']]);
  header('Location: dashboard.php'); exit;
}
if ($admin) {
  $rows = $pdo->query('SELECT g.*, u.full_name FROM grades g JOIN users u ON u.id=g.user_id ORDER BY u.full_name, g.term')->fetchAll();
} else {
  $st = $pdo->prepare('SELECT g.*, u.full_name FROM grades g JOIN users u ON u.id=g.user_id WHERE g.user_id=? ORDER BY g.term');
  $st->execute([$u['id']]); $rows = $st->fetchAll();
}
$avg = [];
foreach ($rows as $r) { $avg[$r['full_name']][] = (float)$r['grade']; }
head('Dashboard'); ?>
<?php if ($avg): ?><div class="row g-2 mb-3">
<?php foreach ($avg as $n => $gs): ?><div class="col-6 col-md-3"><div class="stat">
<div class="lbl text-truncate"><?= $admin ? e($n) : 'My average' ?></div>
<div class="num"><?= number_format(array_sum($gs)/count($gs), 2) ?></div></div></div><?php endforeach; ?>
</div><?php endif; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
<h1 class="h3 mb-0"><?= $admin ? 'All student grades' : 'My grades' ?></h1>
<?php if ($admin): ?><a class="btn btn-main" href="grade_form.php">Add grade</a><?php endif; ?></div>
<?php if ($admin): ?><input id="search" class="form-control mb-3" placeholder="Search student or subject" oninput="filterRows(this.value)"><?php endif; ?>
<div class="card shadow-sm"><div class="table-responsive"><table class="table table-hover mb-0 align-middle">
<thead><tr><?php if ($admin): ?><th>Student</th><?php endif; ?><th>Subject</th><th>Term</th><th>Grade</th><?php if ($admin): ?><th></th><?php endif; ?></tr></thead>
<tbody>
<?php foreach ($rows as $r): ?>
<tr><?php if ($admin): ?><td><?= e($r['full_name']) ?></td><?php endif; ?>
<td><?= e($r['subject']) ?></td><td><?= e($r['term']) ?></td><td><?= e($r['grade']) ?></td>
<?php if ($admin): ?><td class="text-end text-nowrap"><a class="btn btn-sm btn-outline-secondary" href="grade_form.php?id=<?= (int)$r['id'] ?>">Edit</a>
<form method="post" class="d-inline" data-confirm="Delete this grade?"><input type="hidden" name="csrf" value="<?= csrf() ?>"><input type="hidden" name="delete_id" value="<?= (int)$r['id'] ?>"><button class="btn btn-sm btn-outline-danger">Delete</button></form></td><?php endif; ?></tr>
<?php endforeach; if (!$rows): ?><tr><td colspan="5" class="text-center text-muted py-4">No grades yet.</td></tr><?php endif; ?>
</tbody></table></div></div>
<script>function filterRows(q){q=q.toLowerCase();document.querySelectorAll('tbody tr').forEach(t=>t.style.display=t.textContent.toLowerCase().includes(q)?'':'none')}</script>
<?php foot();
