<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';
requireRole('admin');

$message = '';
$error = '';

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM courses WHERE id = ?');
    $stmt->execute([$id]);
    header('Location: course.php?msg=deleted');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $thumbName = $_POST['existing_thumbnail'] ?? null;

    if (!empty($_FILES['thumbnail']['name'])) {
        $ext = strtolower(pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($ext, $allowed, true)) {
            $error = 'Format thumbnail tidak valid.';
        } else {
            $thumbName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $_FILES['thumbnail']['name']);
            move_uploaded_file($_FILES['thumbnail']['tmp_name'], __DIR__ . '/../uploads/' . $thumbName);
        }
    }

    if ($title === '' || $description === '') {
        $error = 'Title dan description wajib diisi.';
    }

    if ($error === '') {
        if ($id > 0) {
            $stmt = $pdo->prepare('UPDATE courses SET title = ?, description = ?, thumbnail = ? WHERE id = ?');
            $stmt->execute([$title, $description, $thumbName, $id]);
            $message = 'Course berhasil diupdate.';
        } else {
            $stmt = $pdo->prepare('INSERT INTO courses (title, description, thumbnail) VALUES (?, ?, ?)');
            $stmt->execute([$title, $description, $thumbName]);
            $message = 'Course berhasil ditambahkan.';
        }
    }
}

$edit = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM courses WHERE id = ?');
    $stmt->execute([(int) $_GET['edit']]);
    $edit = $stmt->fetch();
}

$courses = $pdo->query('SELECT * FROM courses ORDER BY id DESC')->fetchAll();
$enrolledUsers = $pdo->query('SELECT c.title, u.name, e.progress, e.status FROM enrollments e JOIN users u ON u.id = e.user_id JOIN courses c ON c.id = e.course_id ORDER BY e.id DESC')->fetchAll();
?>
<!doctype html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Admin Course</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head><body>
<nav class="navbar navbar-dark bg-dark fixed-top"><div class="container-fluid"><span class="navbar-brand">Admin Course</span><a class="btn btn-outline-light btn-sm" href="/elearning/auth/logout.php">Logout</a></div></nav>
<div class="container-fluid" style="padding-top:70px;"><div class="row"><div class="col-md-2 bg-light min-vh-100 p-3"><ul class="nav flex-column"><li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li><li class="nav-item"><a class="nav-link active" href="course.php">Course</a></li><li class="nav-item"><a class="nav-link" href="lesson.php">Lesson</a></li><li class="nav-item"><a class="nav-link" href="mission.php">Mission</a></li><li class="nav-item"><a class="nav-link" href="quiz.php">Quiz</a></li></ul></div><div class="col-md-10 p-4">
<?php if ($message): ?><div class="alert alert-success"><?= e($message) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<div class="card mb-4"><div class="card-body"><h5><?= $edit ? 'Edit' : 'Tambah' ?> Course</h5>
<form method="post" enctype="multipart/form-data"><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>"><input type="hidden" name="existing_thumbnail" value="<?= e($edit['thumbnail'] ?? '') ?>">
<div class="mb-2"><label class="form-label">Title</label><input class="form-control" name="title" value="<?= e($edit['title'] ?? '') ?>" required></div>
<div class="mb-2"><label class="form-label">Description</label><textarea class="form-control" name="description" required><?= e($edit['description'] ?? '') ?></textarea></div>
<div class="mb-2"><label class="form-label">Thumbnail</label><input type="file" class="form-control" name="thumbnail"></div>
<button class="btn btn-primary">Simpan</button></form></div></div>

<div class="card mb-4"><div class="card-body"><h5>Daftar Course</h5><table class="table table-bordered"><thead><tr><th>Thumbnail</th><th>Title</th><th>Description</th><th>Aksi</th></tr></thead><tbody>
<?php foreach ($courses as $course): ?><tr>
<td><?php if ($course['thumbnail']): ?><img src="/elearning/uploads/<?= e($course['thumbnail']) ?>" width="80"><?php endif; ?></td>
<td><?= e($course['title']) ?></td><td><?= e($course['description']) ?></td>
<td><a class="btn btn-sm btn-warning" href="?edit=<?= (int)$course['id'] ?>">Edit</a> <a class="btn btn-sm btn-danger" href="?delete=<?= (int)$course['id'] ?>" onclick="return confirm('Hapus course?')">Delete</a></td>
</tr><?php endforeach; ?></tbody></table></div></div>

<div class="card"><div class="card-body"><h5>User Enroll</h5><table class="table"><thead><tr><th>Course</th><th>User</th><th>Progress</th><th>Status</th></tr></thead><tbody>
<?php foreach ($enrolledUsers as $row): ?><tr><td><?= e($row['title']) ?></td><td><?= e($row['name']) ?></td><td><?= (int)$row['progress'] ?>%</td><td><?= e($row['status']) ?></td></tr><?php endforeach; ?>
</tbody></table></div></div>
</div></div></div></body></html>
