<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';
requireRole('admin');

$msg = ''; $err = '';
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare('DELETE FROM lessons WHERE id = ?');
    $stmt->execute([(int)$_GET['delete']]);
    header('Location: lesson.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courseId = (int)($_POST['course_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $videoType = $_POST['video_type'] ?? 'youtube';
    $orderNumber = (int)($_POST['order_number'] ?? 1);
    $videoUrl = trim($_POST['video_url'] ?? '');

    if ($videoType === 'file' && !empty($_FILES['video_file']['name'])) {
        $ext = strtolower(pathinfo($_FILES['video_file']['name'], PATHINFO_EXTENSION));
        $allowed = ['mp4', 'webm', 'ogg'];
        if (!in_array($ext, $allowed, true)) {
            $err = 'Ekstensi video tidak diizinkan.';
        } else {
            $videoUrl = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $_FILES['video_file']['name']);
            move_uploaded_file($_FILES['video_file']['tmp_name'], __DIR__ . '/../uploads/' . $videoUrl);
        }
    }

    if ($courseId <= 0 || $title === '' || $videoUrl === '') {
        $err = 'Data lesson belum lengkap.';
    }

    if ($err === '') {
        $stmt = $pdo->prepare('INSERT INTO lessons (course_id, title, video_type, video_url, order_number) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$courseId, $title, $videoType, $videoUrl, $orderNumber]);
        $msg = 'Lesson berhasil ditambahkan.';
    }
}

$courses = $pdo->query('SELECT id, title FROM courses ORDER BY title')->fetchAll();
$lessons = $pdo->query('SELECT l.*, c.title as course_title FROM lessons l JOIN courses c ON c.id = l.course_id ORDER BY c.id, l.order_number')->fetchAll();
?>
<!doctype html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Admin Lesson</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head><body>
<nav class="navbar navbar-dark bg-dark fixed-top"><div class="container-fluid"><span class="navbar-brand">Admin Lesson</span><a class="btn btn-outline-light btn-sm" href="/elearning/auth/logout.php">Logout</a></div></nav>
<div class="container-fluid" style="padding-top:70px"><div class="row"><div class="col-md-2 bg-light min-vh-100 p-3"><ul class="nav flex-column"><li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li><li class="nav-item"><a class="nav-link" href="course.php">Course</a></li><li class="nav-item"><a class="nav-link active" href="lesson.php">Lesson</a></li><li class="nav-item"><a class="nav-link" href="mission.php">Mission</a></li><li class="nav-item"><a class="nav-link" href="quiz.php">Quiz</a></li></ul></div>
<div class="col-md-10 p-4"><?php if($msg):?><div class="alert alert-success"><?=e($msg)?></div><?php endif;?><?php if($err):?><div class="alert alert-danger"><?=e($err)?></div><?php endif;?>
<div class="card mb-4"><div class="card-body"><h5>Tambah Lesson</h5><form method="post" enctype="multipart/form-data">
<div class="mb-2"><label class="form-label">Course</label><select name="course_id" class="form-select" required><option value="">Pilih</option><?php foreach($courses as $c):?><option value="<?=$c['id']?>"><?=e($c['title'])?></option><?php endforeach;?></select></div>
<div class="mb-2"><label class="form-label">Title</label><input class="form-control" name="title" required></div>
<div class="mb-2"><label class="form-label">Order</label><input type="number" class="form-control" name="order_number" min="1" value="1" required></div>
<div class="mb-2"><label class="form-label">Video Type</label><select class="form-select" name="video_type"><option value="youtube">YouTube</option><option value="file">Upload File</option></select></div>
<div class="mb-2"><label class="form-label">YouTube URL</label><input class="form-control" name="video_url"></div>
<div class="mb-3"><label class="form-label">Upload Video File</label><input type="file" name="video_file" class="form-control"></div>
<button class="btn btn-primary">Tambah Lesson</button></form></div></div>
<div class="card"><div class="card-body"><h5>Daftar Lesson</h5><table class="table"><thead><tr><th>Course</th><th>Title</th><th>Type</th><th>Order</th><th>Aksi</th></tr></thead><tbody><?php foreach($lessons as $l):?><tr><td><?=e($l['course_title'])?></td><td><?=e($l['title'])?></td><td><?=e($l['video_type'])?></td><td><?=$l['order_number']?></td><td><a onclick="return confirm('Hapus lesson?')" class="btn btn-sm btn-danger" href="?delete=<?=$l['id']?>">Delete</a></td></tr><?php endforeach;?></tbody></table></div></div>
</div></div></div>
</body></html>
