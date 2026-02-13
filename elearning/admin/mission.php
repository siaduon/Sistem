<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';
requireRole('admin');

if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare('DELETE FROM missions WHERE id = ?');
    $stmt->execute([(int)$_GET['delete']]);
    header('Location: mission.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('INSERT INTO missions (course_id, title, description) VALUES (?, ?, ?)');
    $stmt->execute([(int)$_POST['course_id'], trim($_POST['title']), trim($_POST['description'])]);
}

$courses = $pdo->query('SELECT id,title FROM courses')->fetchAll();
$missions = $pdo->query('SELECT m.*, c.title course_title FROM missions m JOIN courses c ON c.id=m.course_id ORDER BY m.id DESC')->fetchAll();
$userProgress = $pdo->query('SELECT u.name, c.title as course_title, m.title as mission_title, um.status, um.submitted_at FROM user_missions um JOIN users u ON u.id=um.user_id JOIN missions m ON m.id=um.mission_id JOIN courses c ON c.id=m.course_id ORDER BY um.id DESC')->fetchAll();
?>
<!doctype html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Admin Mission</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head><body>
<nav class="navbar navbar-dark bg-dark fixed-top"><div class="container-fluid"><span class="navbar-brand">Admin Mission</span><a class="btn btn-outline-light btn-sm" href="/elearning/auth/logout.php">Logout</a></div></nav>
<div class="container-fluid" style="padding-top:70px"><div class="row"><div class="col-md-2 bg-light min-vh-100 p-3"><ul class="nav flex-column"><li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li><li class="nav-item"><a class="nav-link" href="course.php">Course</a></li><li class="nav-item"><a class="nav-link" href="lesson.php">Lesson</a></li><li class="nav-item"><a class="nav-link active" href="mission.php">Mission</a></li><li class="nav-item"><a class="nav-link" href="quiz.php">Quiz</a></li></ul></div>
<div class="col-md-10 p-4"><div class="card mb-4"><div class="card-body"><h5>Tambah Mission</h5><form method="post"><div class="mb-2"><select name="course_id" class="form-select" required><option value="">Pilih Course</option><?php foreach($courses as $c):?><option value="<?=$c['id']?>"><?=e($c['title'])?></option><?php endforeach;?></select></div><div class="mb-2"><input name="title" class="form-control" placeholder="Judul mission" required></div><div class="mb-2"><textarea name="description" class="form-control" placeholder="Deskripsi" required></textarea></div><button class="btn btn-primary">Simpan</button></form></div></div>
<div class="card mb-4"><div class="card-body"><h5>Daftar Mission</h5><table class="table"><thead><tr><th>Course</th><th>Title</th><th>Description</th><th>Aksi</th></tr></thead><tbody><?php foreach($missions as $m):?><tr><td><?=e($m['course_title'])?></td><td><?=e($m['title'])?></td><td><?=e($m['description'])?></td><td><a class="btn btn-danger btn-sm" href="?delete=<?=$m['id']?>">Delete</a></td></tr><?php endforeach;?></tbody></table></div></div>
<div class="card"><div class="card-body"><h5>Progress Mission User</h5><table class="table"><thead><tr><th>User</th><th>Course</th><th>Mission</th><th>Status</th><th>Tanggal</th></tr></thead><tbody><?php foreach($userProgress as $p):?><tr><td><?=e($p['name'])?></td><td><?=e($p['course_title'])?></td><td><?=e($p['mission_title'])?></td><td><?=e($p['status'])?></td><td><?=e((string)$p['submitted_at'])?></td></tr><?php endforeach;?></tbody></table></div></div>
</div></div></div>
</body></html>
