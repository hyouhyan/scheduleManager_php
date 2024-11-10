<?php
session_start();
require '/config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /auth/login.php');
    exit;
}

$stmt = $pdo->query("SELECT * FROM schedules ORDER BY begin ASC");
$schedules = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Schedules</title>
</head>
<body>
<div class="container mt-5">
    <div class="d-flex justify-content-end">
        <a href="/auth/logout.php" class="btn btn-danger">Logout</a>
    </div>

    <h2>Your Schedules</h2>

    <?php if (isset($_GET['message']) && $_GET['message'] === 'deleted'): ?>
    <div class="alert alert-success">Schedule deleted successfully.</div>
    <?php endif; ?>

    <?php if (isset($_GET['message']) && $_GET['message'] === 'updated'): ?>
    <div class="alert alert-success">Schedule updated successfully.</div>
    <?php endif; ?>

    <?php if (isset($_GET['message']) && $_GET['message'] === 'created'): ?>
    <div class="alert alert-success">Schedule created successfully.</div>
    <?php endif; ?>

    <a href="/schedule/manage/create.php" class="btn btn-success mb-3">Add New Schedule</a>

    <div class="mb-3">
        <a href="/schedule/schedule_month.php" class="btn btn-info mr-1">View Monthly Schedule</a>
        <a href="/schedule/schedule_week.php" class="btn btn-info mr-1">View Weekly Schedule</a>
    </div>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Begin</th>
            <th>End</th>
            <th>Place</th>
            <th>Content</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($schedules as $schedule): ?>
            <tr>
                <td><?= htmlspecialchars($schedule['begin']) ?></td>
                <td><?= htmlspecialchars($schedule['end']) ?></td>
                <td><?= htmlspecialchars($schedule['place']) ?></td>
                <td><?= htmlspecialchars($schedule['content']) ?></td>
                <td>
                    <a href="/schedule/manage/edit.php?id=<?= $schedule['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="/schedule/manage/delete.php?id=<?= $schedule['id'] ?>" class="btn btn-danger btn-sm">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
