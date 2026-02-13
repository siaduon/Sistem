<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';
requireRole('user');

$userId=(int)$_SESSION['user']['id'];
$courseId=(int)($_GET['course_id']??0);
if (!isEnrolled($pdo, $userId, $courseId)) { die('Enroll course dulu.'); }

if(isset($_GET['complete'])){
    $missionId=(int)$_GET['complete'];
    $check=$pdo->prepare('SELECT id FROM user_missions WHERE user_id=? AND mission_id=?');
    $check->execute([$userId,$missionId]);
    if(!$check->fetch()){
        $stmt=$pdo->prepare('INSERT INTO user_missions (user_id, mission_id, status) VALUES (?, ?, ?)');
        $stmt->execute([$userId,$missionId,'completed']);
    }
    header('Location: mission.php?course_id='.$courseId); exit;
}

$missions=$pdo->prepare('SELECT * FROM missions WHERE course_id=?');
$missions->execute([$courseId]);
$missionRows=$missions->fetchAll();
?>
<!doctype html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Mission</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head><body class="bg-light"><div class="container py-4"><a href="course_detail.php?course_id=<?=$courseId?>" class="btn btn-secondary btn-sm mb-3">Kembali</a><h3>Mission Course</h3>
<?php foreach($missionRows as $m):
$chk=$pdo->prepare('SELECT status FROM user_missions WHERE user_id=? AND mission_id=?');$chk->execute([$userId,$m['id']]);$st=$chk->fetchColumn();
?><div class="card mb-2"><div class="card-body"><h5><?=e($m['title'])?></h5><p><?=e($m['description'])?></p><?php if($st==='completed'):?><span class="badge bg-success">Completed</span><?php else:?><a class="btn btn-primary btn-sm" href="?course_id=<?=$courseId?>&complete=<?=$m['id']?>">Selesaikan Mission</a><?php endif;?></div></div><?php endforeach;?>
</div></body></html>
