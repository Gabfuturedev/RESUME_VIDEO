<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['user_id']) && isset($_POST['video_id']) && isset($_POST['progress_time'])) {
    $user_id = $_SESSION['user_id'];
    $video_id = $_POST['video_id'];
    $progress_time = $_POST['progress_time'];

    // Debug lines
    error_log("User ID: $user_id");
    error_log("Video ID: $video_id");
    error_log("Progress Time: $progress_time");

    // Database connection
    $conn = new mysqli("localhost", "root", "", "vidtestdb");

    if ($conn->connect_error) {
        die(json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]));
    }

    // Check if entry already exists
    $sql = "SELECT id FROM user_video_progress WHERE user_id=? AND video_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $video_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Update existing entry
        $stmt->close();
        $sql = "UPDATE user_video_progress SET progress_time=?, last_updated=NOW() WHERE user_id=? AND video_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("dii", $progress_time, $user_id, $video_id);
    } else {
        // Insert new entry
        $stmt->close();
        $sql = "INSERT INTO user_video_progress (user_id, video_id, progress_time) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iid", $user_id, $video_id, $progress_time);
    }

    if ($stmt->execute()) {
        echo json_encode(['message' => 'Progress saved successfully']);
        error_log("Progress saved successfully");
    } else {
        echo json_encode(['error' => 'Error: ' . $stmt->error]);
        error_log("Error: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['error' => 'Invalid request']);
    error_log("Invalid request");
}
?>
