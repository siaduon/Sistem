<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';
requireRole('admin');

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['form_type'] ?? '') === 'quiz') {
        $stmt = $pdo->prepare('INSERT INTO quizzes (course_id, title, passing_score) VALUES (?, ?, ?)');
        $stmt->execute([(int)$_POST['course_id'], trim($_POST['title']), (int)$_POST['passing_score']]);
        $msg = 'Quiz berhasil ditambah.';
    }

    if (($_POST['form_type'] ?? '') === 'question') {
        $pdo->beginTransaction();
        try {
            $stmtQ = $pdo->prepare('INSERT INTO questions (quiz_id, question_text) VALUES (?, ?)');
            $stmtQ->execute([(int)$_POST['quiz_id'], trim($_POST['question_text'])]);
            $questionId = (int)$pdo->lastInsertId();

            $stmtA = $pdo->prepare('INSERT INTO answers (question_id, answer_text, is_correct) VALUES (?, ?, ?)');
            for ($i = 1; $i <= 4; $i++) {
                $answer = trim($_POST['answer_' . $i] ?? '');
                $isCorrect = ((int)($_POST['correct_answer'] ?? 1) === $i) ? 1 : 0;
                $stmtA->execute([$questionId, $answer, $isCorrect]);
            }
            $pdo->commit();
            $msg = 'Soal quiz berhasil ditambah.';
        } catch (Throwable $e) {
            $pdo->rollBack();
            $msg = 'Gagal tambah soal: ' . $e->getMessage();
        }
    }
}

$courses = $pdo->query('SELECT id,title FROM courses')->fetchAll();
$quizzes = $pdo->query('SELECT q.*, c.title course_title FROM quizzes q JOIN courses c ON c.id=q.course_id ORDER BY q.id DESC')->fetchAll();
$results = $pdo->query('SELECT u.name, q.title, r.score, r.status, r.created_at FROM user_quiz_results r JOIN users u ON u.id=r.user_id JOIN quizzes q ON q.id=r.quiz_id ORDER BY r.id DESC')->fetchAll();
?>
<!doctype html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Admin Quiz</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head><body>
<nav class="navbar navbar-dark bg-dark fixed-top"><div class="container-fluid"><span class="navbar-brand">Admin Quiz</span><a class="btn btn-outline-light btn-sm" href="/elearning/auth/logout.php">Logout</a></div></nav>
<div class="container-fluid" style="padding-top:70px"><div class="row"><div class="col-md-2 bg-light min-vh-100 p-3"><ul class="nav flex-column"><li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li><li class="nav-item"><a class="nav-link" href="course.php">Course</a></li><li class="nav-item"><a class="nav-link" href="lesson.php">Lesson</a></li><li class="nav-item"><a class="nav-link" href="mission.php">Mission</a></li><li class="nav-item"><a class="nav-link active" href="quiz.php">Quiz</a></li></ul></div>
<div class="col-md-10 p-4"><?php if($msg):?><div class="alert alert-info"><?=e($msg)?></div><?php endif;?>
<div class="card mb-3"><div class="card-body"><h5>Buat Quiz</h5><form method="post"><input type="hidden" name="form_type" value="quiz"><div class="mb-2"><select name="course_id" class="form-select" required><option value="">Pilih Course</option><?php foreach($courses as $c):?><option value="<?=$c['id']?>"><?=e($c['title'])?></option><?php endforeach;?></select></div><div class="mb-2"><input name="title" class="form-control" placeholder="Judul quiz" required></div><div class="mb-2"><input type="number" name="passing_score" class="form-control" value="70" min="0" max="100" required></div><button class="btn btn-primary">Tambah Quiz</button></form></div></div>
<div class="card mb-3"><div class="card-body"><h5>Tambah Soal Pilihan Ganda</h5><form method="post"><input type="hidden" name="form_type" value="question"><div class="mb-2"><select name="quiz_id" class="form-select" required><option value="">Pilih Quiz</option><?php foreach($quizzes as $q):?><option value="<?=$q['id']?>"><?=e($q['course_title'].' - '.$q['title'])?></option><?php endforeach;?></select></div><div class="mb-2"><textarea name="question_text" class="form-control" placeholder="Pertanyaan" required></textarea></div>
<?php for($i=1;$i<=4;$i++):?><div class="mb-2"><input name="answer_<?=$i?>" class="form-control" placeholder="Jawaban <?=$i?>" required></div><?php endfor;?><div class="mb-2"><label class="form-label">Jawaban Benar</label><select name="correct_answer" class="form-select"><?php for($i=1;$i<=4;$i++):?><option value="<?=$i?>">Jawaban <?=$i?></option><?php endfor;?></select></div><button class="btn btn-success">Tambah Soal</button></form></div></div>
<div class="card"><div class="card-body"><h5>Hasil Quiz User</h5><table class="table"><thead><tr><th>User</th><th>Quiz</th><th>Skor</th><th>Status</th><th>Tanggal</th></tr></thead><tbody><?php foreach($results as $r):?><tr><td><?=e($r['name'])?></td><td><?=e($r['title'])?></td><td><?=$r['score']?></td><td><?=e($r['status'])?></td><td><?=e($r['created_at'])?></td></tr><?php endforeach;?></tbody></table></div></div>
</div></div></div>
</body></html>
