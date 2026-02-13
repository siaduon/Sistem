<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function requireLogin(): void
{
    if (empty($_SESSION['user'])) {
        header('Location: /elearning/auth/login.php');
        exit;
    }
}

function requireRole(string $role): void
{
    requireLogin();
    if (($_SESSION['user']['role'] ?? '') !== $role) {
        if ($_SESSION['user']['role'] === 'admin') {
            header('Location: /elearning/admin/dashboard.php');
        } else {
            header('Location: /elearning/user/dashboard.php');
        }
        exit;
    }
}

function redirectByRole(): void
{
    if (!empty($_SESSION['user'])) {
        if ($_SESSION['user']['role'] === 'admin') {
            header('Location: /elearning/admin/dashboard.php');
        } else {
            header('Location: /elearning/user/dashboard.php');
        }
        exit;
    }
}

function isEnrolled(PDO $pdo, int $userId, int $courseId): bool
{
    $stmt = $pdo->prepare('SELECT id FROM enrollments WHERE user_id = ? AND course_id = ?');
    $stmt->execute([$userId, $courseId]);
    return (bool) $stmt->fetch();
}

function calculateCourseProgress(PDO $pdo, int $userId, int $courseId): int
{
    $stmtTotal = $pdo->prepare('SELECT COUNT(*) FROM lessons WHERE course_id = ?');
    $stmtTotal->execute([$courseId]);
    $total = (int) $stmtTotal->fetchColumn();

    if ($total === 0) {
        return 0;
    }

    $stmtDone = $pdo->prepare(
        'SELECT COUNT(lp.id)
         FROM lesson_progress lp
         JOIN lessons l ON l.id = lp.lesson_id
         WHERE lp.user_id = ? AND l.course_id = ?'
    );
    $stmtDone->execute([$userId, $courseId]);
    $done = (int) $stmtDone->fetchColumn();

    return (int) round(($done / $total) * 100);
}

function updateEnrollmentProgress(PDO $pdo, int $userId, int $courseId): void
{
    $progress = calculateCourseProgress($pdo, $userId, $courseId);
    $status = $progress >= 100 ? 'completed' : 'in_progress';

    $stmt = $pdo->prepare('UPDATE enrollments SET progress = ?, status = ? WHERE user_id = ? AND course_id = ?');
    $stmt->execute([$progress, $status, $userId, $courseId]);
}
