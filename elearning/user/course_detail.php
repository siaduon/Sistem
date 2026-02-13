<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';
requireRole('user');

$userId = (int)$_SESSION['user']['id'];
$courseId = (int)($_GET['course_id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM courses WHERE id = ?');
$stmt->execute([$courseId]);
$course = $stmt->fetch();
if (!$course) { die('Course tidak ditemukan'); }

if (isset($_POST['enroll'])) {
    if (!isEnrolled($pdo, $userId, $courseId)) {
        $enroll = $pdo->prepare('INSERT INTO enrollments (user_id, course_id, progress, status) VALUES (?, ?, 0, ?)');
        $enroll->execute([$userId, $courseId, 'in_progress']);
    }
    header('Location: course_detail.php?course_id=' . $courseId);
    exit;
}

$enrolled = isEnrolled($pdo, $userId, $courseId);
$progress = $enrolled ? calculateCourseProgress($pdo, $userId, $courseId) : 0;
if ($enrolled) { updateEnrollmentProgress($pdo, $userId, $courseId); }

$lessonsStmt = $pdo->prepare('SELECT * FROM lessons WHERE course_id = ? ORDER BY order_number');
$lessonsStmt->execute([$courseId]);
$lessons = $lessonsStmt->fetchAll();

$missionsStmt = $pdo->prepare('SELECT * FROM missions WHERE course_id = ?');
$missionsStmt->execute([$courseId]);
$missions = $missionsStmt->fetchAll();

$quizzesStmt = $pdo->prepare('SELECT * FROM quizzes WHERE course_id = ?');
$quizzesStmt->execute([$courseId]);
$quizzes = $quizzesStmt->fetchAll();
?>
<!doctype html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Course Detail</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head><body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top"><div class="container"><a class="navbar-brand" href="dashboard.php">Dashboard</a><a class="btn btn-outline-light btn-sm" href="/elearning/auth/logout.php">Logout</a></div></nav>
<div class="container" style="padding-top:90px;"><div class="card mb-3"><div class="card-body"><h3><?=e($course['title'])?></h3><p><?=e($course['description'])?></p><?php if(!$enrolled):?><form method="post"><button name="enroll" class="btn btn-success">Enroll Course</button></form><?php else:?><div class="progress"><div class="progress-bar" style="width: <?=$progress?>%"><?=$progress?>%</div></div><?php endif;?></div></div>
<?php if($enrolled):?><div class="row g-3"><div class="col-md-4"><div class="card"><div class="card-body"><h5>Lessons</h5><?php foreach($lessons as $l):?><a class="d-block mb-2" href="lesson.php?lesson_id=<?=$l['id']?>&course_id=<?=$courseId?>"><?=e($l['title'])?></a><?php endforeach;?></div></div></div><div class="col-md-4"><div class="card"><div class="card-body"><h5>Missions</h5><?php foreach($missions as $m):?><a class="d-block mb-2" href="mission.php?course_id=<?=$courseId?>"><?=e($m['title'])?></a><?php endforeach;?></div></div></div><div class="col-md-4"><div class="card"><div class="card-body"><h5>Quiz</h5><?php foreach($quizzes as $q):?><a class="d-block mb-2" href="quiz.php?quiz_id=<?=$q['id']?>"><?=e($q['title'])?></a><?php endforeach;?></div></div></div></div><?php else:?><div class="alert alert-warning">Anda harus enroll untuk mengakses materi.</div><?php endif;?>
</div></body></html>
