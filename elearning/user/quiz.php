<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';
requireRole('user');

$userId=(int)$_SESSION['user']['id'];
$quizId=(int)($_GET['quiz_id']??0);

$quizStmt=$pdo->prepare('SELECT q.*, c.id as course_id FROM quizzes q JOIN courses c ON c.id=q.course_id WHERE q.id=?');
$quizStmt->execute([$quizId]);
$quiz=$quizStmt->fetch();
if(!$quiz){die('Quiz tidak ditemukan');}
if(!isEnrolled($pdo,$userId,(int)$quiz['course_id'])){die('Anda harus enroll dulu.');}

$questionsStmt=$pdo->prepare('SELECT * FROM questions WHERE quiz_id=?');
$questionsStmt->execute([$quizId]);
$questions=$questionsStmt->fetchAll();

$resultMsg='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $correct=0;
    foreach($questions as $q){
        $chosen=(int)($_POST['question_'.$q['id']]??0);
        $chk=$pdo->prepare('SELECT is_correct FROM answers WHERE id=? AND question_id=?');
        $chk->execute([$chosen,$q['id']]);
        if((int)$chk->fetchColumn()===1){$correct++;}
    }
    $total=count($questions);
    $score=$total>0 ? (int)round(($correct/$total)*100) : 0;
    $status=$score>=(int)$quiz['passing_score'] ? 'passed' : 'failed';

    $save=$pdo->prepare('INSERT INTO user_quiz_results (user_id, quiz_id, score, status) VALUES (?, ?, ?, ?)');
    $save->execute([$userId,$quizId,$score,$status]);
    $resultMsg="Skor Anda: {$score}. Status: {$status}";
}
?>
<!doctype html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Quiz</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head><body class="bg-light"><div class="container py-4"><a class="btn btn-secondary btn-sm mb-3" href="course_detail.php?course_id=<?=$quiz['course_id']?>">Kembali</a><h3><?=e($quiz['title'])?></h3><p>Passing score: <?=$quiz['passing_score']?></p>
<?php if($resultMsg):?><div class="alert alert-info"><?=e($resultMsg)?></div><?php endif;?>
<form method="post">
<?php foreach($questions as $q):?><div class="card mb-3"><div class="card-body"><p><strong><?=e($q['question_text'])?></strong></p>
<?php $answersStmt=$pdo->prepare('SELECT * FROM answers WHERE question_id=?');$answersStmt->execute([$q['id']]);$answers=$answersStmt->fetchAll();foreach($answers as $a):?><div class="form-check"><input class="form-check-input" type="radio" name="question_<?=$q['id']?>" value="<?=$a['id']?>" required><label class="form-check-label"><?=e($a['answer_text'])?></label></div><?php endforeach;?>
</div></div><?php endforeach;?>
<button class="btn btn-primary">Submit Quiz</button>
</form></div></body></html>
