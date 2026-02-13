<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';
requireRole('user');

$userId=(int)$_SESSION['user']['id'];
$lessonId=(int)($_GET['lesson_id']??0);
$courseId=(int)($_GET['course_id']??0);
if (!isEnrolled($pdo, $userId, $courseId)) { die('Anda belum enroll course ini.'); }

$stmt=$pdo->prepare('SELECT * FROM lessons WHERE id=? AND course_id=?');
$stmt->execute([$lessonId,$courseId]);
$lesson=$stmt->fetch();
if(!$lesson){die('Lesson tidak ditemukan.');}

$prev=$pdo->prepare('SELECT id FROM lessons WHERE course_id=? AND order_number < ? ORDER BY order_number DESC LIMIT 1');
$prev->execute([$courseId,$lesson['order_number']]);
$prevLesson=$prev->fetch();
$locked=false;
if($prevLesson){
    $chk=$pdo->prepare('SELECT id FROM lesson_progress WHERE user_id=? AND lesson_id=?');
    $chk->execute([$userId,$prevLesson['id']]);
    if(!$chk->fetch()){ $locked=true; }
}

if(isset($_POST['complete']) && !$locked){
    $check=$pdo->prepare('SELECT id FROM lesson_progress WHERE user_id=? AND lesson_id=?');
    $check->execute([$userId,$lessonId]);
    if(!$check->fetch()){
        $ins=$pdo->prepare('INSERT INTO lesson_progress (user_id, lesson_id) VALUES (?, ?)');
        $ins->execute([$userId,$lessonId]);
        updateEnrollmentProgress($pdo,$userId,$courseId);
    }
    header('Location: lesson.php?lesson_id='.$lessonId.'&course_id='.$courseId); exit;
}

$doneStmt=$pdo->prepare('SELECT id FROM lesson_progress WHERE user_id=? AND lesson_id=?');
$doneStmt->execute([$userId,$lessonId]);
$isDone=(bool)$doneStmt->fetch();
$progress=calculateCourseProgress($pdo,$userId,$courseId);

function youtubeEmbed(string $url): string {
    if (preg_match('/(?:v=|be\/)([a-zA-Z0-9_-]{11})/', $url, $m)) { return 'https://www.youtube.com/embed/' . $m[1]; }
    return $url;
}
?>
<!doctype html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Lesson</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head><body class="bg-light">
<div class="container py-4"><a href="course_detail.php?course_id=<?=$courseId?>" class="btn btn-secondary btn-sm mb-3">Kembali</a><div class="card"><div class="card-body"><h4><?=e($lesson['title'])?></h4>
<div class="progress mb-3"><div class="progress-bar" style="width: <?=$progress?>%"><?=$progress?>%</div></div>
<?php if($locked):?><div class="alert alert-warning">Lesson terkunci. Selesaikan lesson sebelumnya.</div><?php else:?>
<?php if($lesson['video_type']==='youtube'):?><div class="ratio ratio-16x9 mb-3"><iframe src="<?=e(youtubeEmbed($lesson['video_url']))?>" allowfullscreen></iframe></div><?php else:?><video controls class="w-100 mb-3"><source src="/elearning/uploads/<?=e($lesson['video_url'])?>"></video><?php endif;?>
<?php if(!$isDone):?><form method="post"><button name="complete" class="btn btn-success">Mark as Completed</button></form><?php else:?><div class="alert alert-success">Lesson sudah selesai.</div><?php endif;?>
<?php endif;?></div></div></div>
</body></html>
