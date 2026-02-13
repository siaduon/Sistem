<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';
requireRole('admin');

$totalUsers = (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role='user'")->fetchColumn();
$totalCourses = (int) $pdo->query('SELECT COUNT(*) FROM courses')->fetchColumn();
$totalEnrollments = (int) $pdo->query('SELECT COUNT(*) FROM enrollments')->fetchColumn();
$totalQuizAttempts = (int) $pdo->query('SELECT COUNT(*) FROM user_quiz_results')->fetchColumn();
?>
<!doctype html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Admin Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><script src="https://cdn.jsdelivr.net/npm/chart.js"></script></head>
<body>
<nav class="navbar navbar-dark bg-dark fixed-top"><div class="container-fluid"><span class="navbar-brand">Admin Panel</span><a class="btn btn-outline-light btn-sm" href="/elearning/auth/logout.php">Logout</a></div></nav>
<div class="container-fluid" style="padding-top:70px;"><div class="row">
<div class="col-md-2 bg-light min-vh-100 p-3"><ul class="nav flex-column"><li class="nav-item"><a class="nav-link active" href="dashboard.php">Dashboard</a></li><li class="nav-item"><a class="nav-link" href="course.php">Course</a></li><li class="nav-item"><a class="nav-link" href="lesson.php">Lesson</a></li><li class="nav-item"><a class="nav-link" href="mission.php">Mission</a></li><li class="nav-item"><a class="nav-link" href="quiz.php">Quiz</a></li></ul></div>
<div class="col-md-10 p-4">
<div class="row g-3 mb-4">
<div class="col-md-3"><div class="card text-bg-primary"><div class="card-body"><h6>Total User</h6><h3><?= $totalUsers ?></h3></div></div></div>
<div class="col-md-3"><div class="card text-bg-success"><div class="card-body"><h6>Total Course</h6><h3><?= $totalCourses ?></h3></div></div></div>
<div class="col-md-3"><div class="card text-bg-warning"><div class="card-body"><h6>Total Enrollment</h6><h3><?= $totalEnrollments ?></h3></div></div></div>
<div class="col-md-3"><div class="card text-bg-info"><div class="card-body"><h6>Total Quiz Attempt</h6><h3><?= $totalQuizAttempts ?></h3></div></div></div>
</div>
<div class="card"><div class="card-body"><h5>Ringkasan Statistik</h5><canvas id="statsChart" height="90"></canvas></div></div>
</div></div></div>
<script>
new Chart(document.getElementById('statsChart'),{type:'bar',data:{labels:['Users','Courses','Enrollments','Quiz Attempts'],datasets:[{label:'Total',data:[<?= $totalUsers ?>,<?= $totalCourses ?>,<?= $totalEnrollments ?>,<?= $totalQuizAttempts ?>],backgroundColor:['#0d6efd','#198754','#ffc107','#0dcaf0']}]},options:{responsive:true}});
</script>
</body></html>
