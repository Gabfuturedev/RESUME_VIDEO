<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['user_id']) && isset($_GET['video_id'])) {
    $user_id = $_SESSION['user_id'];
    $video_id = $_GET['video_id'];

    // Database connection
    $conn = new mysqli("localhost", "root", "", "vidtestdb");

    if ($conn->connect_error) {
        echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
        exit();
    }

    $sql = "SELECT progress_time FROM user_video_progress WHERE user_id=? AND video_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $video_id);
    $stmt->execute();
    $stmt->bind_result($progress_time);
    $stmt->fetch();

    if ($progress_time !== null) {
        echo json_encode(['progress_time' => $progress_time]);
    } else {
        echo json_encode(['progress_time' => 0]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>
