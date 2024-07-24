<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <a href="logout.php">Logout</a>
    <video id="videoPlayer" controls>
        <source src="test.mp4" type="video/mp4">
    </video>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            var video = document.getElementById('videoPlayer');
            var video_id = 1; // Replace with dynamic video ID

            // Get progress when video loads
            $.get('get_progress.php', {video_id: video_id}, function (data) {
                console.log('Progress data fetched:', data); // Debug line
                if (data.progress_time) {
                    video.currentTime = data.progress_time;
                    console.log('Set video time to:', data.progress_time); // Debug line
                } else {
                    console.log('No progress time found, starting from the beginning');
                }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.error('Failed to fetch progress:', textStatus, errorThrown); // Debug line
            });

            // Save progress periodically
            video.addEventListener('timeupdate', function () {
                var progress_time = video.currentTime;
                console.log('Saving progress:', progress_time); // Debug line
                $.post('save_progress.php', {video_id: video_id, progress_time: progress_time}, function (response) {
                    console.log('Save response:', response); // Debug line
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    console.error('Failed to save progress:', textStatus, errorThrown); // Debug line
                });
            });
        });
    </script>
</body>
</html>
