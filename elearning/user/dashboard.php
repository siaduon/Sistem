<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';
requireRole('user');

$userId = (int)$_SESSION['user']['id'];

$enrollments = $pdo->prepare('SELECT e.*, c.title, c.thumbnail FROM enrollments e JOIN courses c ON c.id=e.course_id WHERE e.user_id=? ORDER BY e.id DESC');
$enrollments->execute([$userId]);
$enrollmentRows = $enrollments->fetchAll();

$lastQuiz = $pdo->prepare('SELECT q.title, uqr.score, uqr.status FROM user_quiz_results uqr JOIN quizzes q ON q.id=uqr.quiz_id WHERE uqr.user_id=? ORDER BY uqr.id DESC LIMIT 1');
$lastQuiz->execute([$userId]);
$lastQuizRow = $lastQuiz->fetch();

$totalMission = $pdo->prepare("SELECT COUNT(*) FROM user_missions WHERE user_id=? AND status='completed'");
$totalMission->execute([$userId]);
$totalMissionDone = (int)$totalMission->fetchColumn();
?>
<!doctype html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>User Dashboard</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head><body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top"><div class="container"><a class="navbar-brand" href="dashboard.php">E-Learning</a><div><a class="btn btn-outline-light btn-sm" href="/elearning/auth/logout.php">Logout</a></div></div></nav>
<div class="container" style="padding-top:90px;">
<div class="row g-3 mb-4"><div class="col-md-4"><div class="card"><div class="card-body"><h6>Last Quiz</h6><p class="mb-0"><?= e($lastQuizRow['title'] ?? '-') ?> | <?= e((string)($lastQuizRow['score'] ?? '-')) ?> (<?= e($lastQuizRow['status'] ?? '-') ?>)</p></div></div></div><div class="col-md-4"><div class="card"><div class="card-body"><h6>Total Mission Selesai</h6><h3><?= $totalMissionDone ?></h3></div></div></div><div class="col-md-4"><div class="card"><div class="card-body"><h6>Total Course Diikuti</h6><h3><?= count($enrollmentRows) ?></h3></div></div></div></div>
<h4>Course Saya</h4><div class="row g-3">
<?php foreach($enrollmentRows as $row):?><div class="col-md-4"><div class="card h-100"><?php if($row['thumbnail']):?><img class="card-img-top" src="/elearning/uploads/<?=e($row['thumbnail'])?>" style="height:180px;object-fit:cover"><?php endif;?><div class="card-body"><h5><?=e($row['title'])?></h5><div class="progress mb-2"><div class="progress-bar" style="width: <?=$row['progress']?>%;"><?=$row['progress']?>%</div></div><p>Status: <strong><?=e($row['status'])?></strong></p><a href="course_detail.php?course_id=<?=$row['course_id']?>" class="btn btn-primary btn-sm">Lihat Detail</a></div></div></div><?php endforeach;?>
</div>
<hr><h4>Jelajahi Course</h4>
<div class="row g-3">
<?php $allCourses=$pdo->query('SELECT * FROM courses ORDER BY id DESC')->fetchAll(); foreach($allCourses as $c):?><div class="col-md-4"><div class="card"><div class="card-body"><h5><?=e($c['title'])?></h5><p><?=e($c['description'])?></p><a href="course_detail.php?course_id=<?=$c['id']?>" class="btn btn-outline-primary btn-sm">Buka</a></div></div></div><?php endforeach;?>
</div>
</div></body></html>
