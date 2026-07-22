<?php
session_start();
if (!isset($_SESSION['id'])) {
    header('location: login.php');
    exit();
}
include 'admin/dbcon.php';

$category = isset($_GET['category']) && $_GET['category'] !== '' ? $_GET['category'] : 'PHP';
$user_id = $_SESSION['id'];

// Handle Video Progress Update via AJAX
if (isset($_POST['update_progress'])) {
    $video_id = intval($_POST['video_id']);
    $watched_duration = intval($_POST['watched_duration']);
    $is_completed = intval($_POST['is_completed']);
    
    $check_query = "SELECT * FROM user_video_progress WHERE user_id='$user_id' AND video_id='$video_id'";
    $check_res = mysqli_query($con, $check_query);
    
    if (mysqli_num_rows($check_res) > 0) {
        $update = "UPDATE user_video_progress SET watched_duration='$watched_duration', is_completed='$is_completed' WHERE user_id='$user_id' AND video_id='$video_id'";
        mysqli_query($con, $update);
    } else {
        $insert = "INSERT INTO user_video_progress (user_id, video_id, watched_duration, is_completed) VALUES ('$user_id', '$video_id', '$watched_duration', '$is_completed')";
        mysqli_query($con, $insert);
    }
    echo json_encode(['success' => true]);
    exit();
}

// Handle Mark as Completed
if (isset($_POST['mark_completed'])) {
    // Check if all videos are completed
    $videos_query = "SELECT COUNT(*) as total FROM grooming_videos WHERE category='$category'";
    $videos_res = mysqli_query($con, $videos_query);
    $videos_row = mysqli_fetch_assoc($videos_res);
    $total_videos = $videos_row['total'];
    
    $completed_query = "SELECT COUNT(*) as completed FROM user_video_progress 
                        WHERE user_id='$user_id' AND is_completed=1 
                        AND video_id IN (SELECT id FROM grooming_videos WHERE category='$category')";
    $completed_res = mysqli_query($con, $completed_query);
    $completed_row = mysqli_fetch_assoc($completed_res);
    $completed_videos = $completed_row['completed'];
    
    if ($completed_videos >= $total_videos) {
        $update_query = "UPDATE user_quiz_status SET grooming_completed=1 WHERE user_id='$user_id' AND category='$category' AND status='failed'";
        if (mysqli_query($con, $update_query)) {
            echo "<script>alert('Congratulations! All videos completed. You can now retake the assessment.'); window.location.href='quiz.php?category=$category';</script>";
        }
    } else {
        echo "<script>alert('Please complete all videos before proceeding. ($completed_videos/$total_videos completed)');</script>";
    }
    exit();
}

// Check User Status
$status_query = "SELECT * FROM user_quiz_status WHERE user_id='$user_id' AND category='$category'";
$status_res = mysqli_query($con, $status_query);
$needs_grooming = false;
$can_retake = true;

if (mysqli_num_rows($status_res) > 0) {
    $status_row = mysqli_fetch_assoc($status_res);
    if ($status_row['status'] == 'failed' && $status_row['grooming_completed'] == 0) {
        $needs_grooming = true;
        $can_retake = false;
    }
}

// Fetch Videos for Category
$videos_query = "SELECT * FROM grooming_videos WHERE category='$category' ORDER BY order_index ASC";
$videos_result = mysqli_query($con, $videos_query);
$videos = [];
while ($video = mysqli_fetch_assoc($videos_result)) {
    // Get user progress for this video
    $progress_query = "SELECT * FROM user_video_progress WHERE user_id='$user_id' AND video_id='{$video['id']}'";
    $progress_res = mysqli_query($con, $progress_query);
    $progress = mysqli_fetch_assoc($progress_res);
    
    $video['watched_duration'] = $progress ? $progress['watched_duration'] : 0;
    $video['is_completed'] = $progress ? $progress['is_completed'] : 0;
    $video['progress_percent'] = $video['duration'] > 0 ? min(100, ($video['watched_duration'] / $video['duration']) * 100) : 0;
    $videos[] = $video;
}

// Calculate overall progress
$total_videos = count($videos);
$completed_count = 0;
foreach ($videos as $v) {
    if ($v['is_completed']) $completed_count++;
}
$overall_progress = $total_videos > 0 ? ($completed_count / $total_videos) * 100 : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Skill Learning - <?php echo htmlspecialchars($category); ?></title>
    <?php include 'links.php'; ?>
    <style>
        .grooming-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 0 20px;
        }
        
        .learning-hub-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            border: 1px solid rgba(255,255,255,0.6);
        }

        .hub-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            padding: 50px 40px;
            text-align: center;
            color: white;
            position: relative;
        }

        .hub-body {
            padding: 50px 40px;
        }

        /* Progress Bar */
        .overall-progress {
            background: #f1f5f9;
            border-radius: 50px;
            height: 12px;
            overflow: hidden;
            margin: 30px 0;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
        }

        .overall-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--success-color), #34d399);
            transition: width 0.6s ease;
            box-shadow: 0 0 10px rgba(16, 185, 129, 0.5);
        }

        .progress-text {
            text-align: center;
            font-weight: 700;
            color: var(--primary-color);
            margin-top: 10px;
        }

        /* Video Card */
        .video-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .video-card:hover {
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
            transform: translateY(-3px);
            border-color: var(--primary-color);
        }

        .video-card.completed {
            border-color: var(--success-color);
            background: linear-gradient(145deg, #ffffff, #f0fdf4);
        }

        .video-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .video-title {
            font-size: 1.3rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .video-description {
            color: #64748b;
            font-size: 0.95rem;
            margin-bottom: 15px;
        }

        .video-badge {
            background: var(--success-color);
            color: white;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .video-badge.pending {
            background: #94a3b8;
        }

        .video-container {
            position: relative;
            width: 100%;
            padding-bottom: 56.25%; /* 16:9 aspect ratio */
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
            margin-bottom: 15px;
        }

        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }

        .video-progress-bar {
            background: #e2e8f0;
            border-radius: 8px;
            height: 8px;
            overflow: hidden;
            margin-top: 12px;
        }

        .video-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            transition: width 0.3s ease;
        }

        .video-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 12px;
            font-size: 0.85rem;
            color: #64748b;
        }

        .completion-banner {
            background: linear-gradient(135deg, var(--success-color), #34d399);
            color: white;
            padding: 30px;
            border-radius: 20px;
            text-align: center;
            margin: 30px 0;
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
        }

        .completion-banner h3 {
            color: white;
            margin-bottom: 15px;
        }

        .alert-info-custom {
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            border-left: 5px solid var(--primary-color);
            padding: 20px;
            border-radius: 16px;
            margin-bottom: 30px;
        }

        .lock-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            z-index: 10;
        }

        .lock-overlay i {
            font-size: 4rem;
            color: white;
            opacity: 0.7;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container grooming-container animated fadeIn">
        <div class="learning-hub-card">
            
            <div class="hub-header">
                <i class="fas fa-graduation-cap fa-4x mb-3" style="opacity:0.9"></i>
                <h1 class="font-weight-bold mb-2">Skill Improvement Program</h1>
                <p class="lead mb-0" style="opacity:0.85">Master <?php echo htmlspecialchars($category); ?> through curated video lessons</p>
            </div>
            
            <div class="hub-body">
                <?php if ($needs_grooming): ?>
                    <div class="alert-info-custom">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle fa-2x mr-3" style="color: var(--primary-color);"></i>
                            <div>
                                <h5 class="mb-1" style="color: var(--primary-color); font-weight: 800;">Complete Video Training Required</h5>
                                <p class="mb-0" style="color: #475569;">Watch all video tutorials completely to unlock your retake opportunity. Track your progress below.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Overall Progress -->
                    <div class="mb-4">
                        <h4 style="color: #0f172a; font-weight: 800;">
                            <i class="fas fa-chart-line mr-2" style="color: var(--success-color);"></i>
                            Your Learning Progress
                        </h4>
                        <div class="overall-progress">
                            <div class="overall-progress-bar" style="width: <?php echo $overall_progress; ?>%"></div>
                        </div>
                        <div class="progress-text">
                            <?php echo $completed_count; ?> of <?php echo $total_videos; ?> videos completed (<?php echo round($overall_progress); ?>%)
                        </div>
                    </div>

                    <!-- Video Playlist -->
                    <h4 style="color: #0f172a; font-weight: 800; margin-top: 40px;">
                        <i class="fas fa-play-circle mr-2" style="color: var(--primary-color);"></i>
                        Video Lessons
                    </h4>

                    <?php foreach ($videos as $index => $video): ?>
                        <div class="video-card <?php echo $video['is_completed'] ? 'completed' : ''; ?>" id="video-card-<?php echo $video['id']; ?>">
                            <div class="video-header">
                                <div>
                                    <div class="video-title">
                                        <?php echo ($index + 1); ?>. <?php echo htmlspecialchars($video['title']); ?>
                                    </div>
                                    <div class="video-description">
                                        <?php echo htmlspecialchars($video['description']); ?>
                                    </div>
                                </div>
                                <span class="video-badge <?php echo $video['is_completed'] ? '' : 'pending'; ?>">
                                    <?php if ($video['is_completed']): ?>
                                        <i class="fas fa-check-circle"></i> Completed
                                    <?php else: ?>
                                        <i class="fas fa-clock"></i> Pending
                                    <?php endif; ?>
                                </span>
                            </div>

                            <div class="video-container">
                                <iframe 
                                    id="video-<?php echo $video['id']; ?>"
                                    src="<?php echo $video['video_url']; ?>?enablejsapi=1&rel=0&modestbranding=1" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen
                                    data-video-id="<?php echo $video['id']; ?>"
                                    data-duration="<?php echo $video['duration']; ?>">
                                </iframe>
                            </div>

                            <div class="video-progress-bar">
                                <div class="video-progress-fill" id="progress-<?php echo $video['id']; ?>" style="width: <?php echo $video['progress_percent']; ?>%"></div>
                            </div>

                            <div class="video-meta">
                                <span><i class="fas fa-clock mr-1"></i> <?php echo gmdate("i:s", $video['duration']); ?> minutes</span>
                                <span id="watched-<?php echo $video['id']; ?>">
                                    Watched: <?php echo gmdate("i:s", $video['watched_duration']); ?> / <?php echo gmdate("i:s", $video['duration']); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <!-- Completion Section -->
                    <?php if ($overall_progress >= 100): ?>
                        <div class="completion-banner">
                            <i class="fas fa-trophy fa-3x mb-3"></i>
                            <h3>Congratulations! All Videos Completed! 🎉</h3>
                            <p class="mb-4">You've successfully completed all training videos. You're now ready to retake the assessment.</p>
                            <form method="POST">
                                <button type="submit" name="mark_completed" class="btn btn-light btn-lg rounded-pill px-5 shadow-lg font-weight-bold">
                                    <i class="fas fa-arrow-right mr-2"></i> Proceed to Assessment
                                </button>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="text-center mt-5 pt-4">
                            <div class="alert alert-warning shadow-sm d-inline-block px-4 py-3 rounded-pill">
                                <i class="fas fa-lock mr-2"></i> Complete all videos to unlock the assessment
                            </div>
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-check-circle fa-5x mb-4" style="color: var(--success-color);"></i>
                        <h3 class="font-weight-bold mb-3">Training Completed!</h3>
                        <p class="lead mb-4">You've successfully completed the skill improvement program.</p>
                        <a href="quiz.php?category=<?php echo htmlspecialchars($category); ?>" class="btn btn-primary btn-lg rounded-pill px-5 shadow-lg btn-hover-effect">
                            <i class="fas fa-redo-alt mr-2"></i> Retake Assessment
                        </a>
                    </div>
                <?php endif; ?>
                
                <div class="text-center mt-4">
                    <a href="index.php" class="text-muted"><i class="fas fa-arrow-left mr-2"></i>Back to Job Board</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Video Progress Tracking
        const videoData = <?php echo json_encode($videos); ?>;
        const trackers = {};

        videoData.forEach(video => {
            const iframe = document.getElementById(`video-${video.id}`);
            if (iframe) {
                let watchedTime = parseInt(video.watched_duration) || 0;
                let maxWatchedTime = watchedTime;
                
                // Track video time every 5 seconds
                trackers[video.id] = setInterval(() => {
                    watchedTime += 5;
                    if (watchedTime > maxWatchedTime) {
                        maxWatchedTime = watchedTime;
                        
                        // Update UI
                        const progressPercent = Math.min(100, (maxWatchedTime / video.duration) * 100);
                        document.getElementById(`progress-${video.id}`).style.width = progressPercent + '%';
                        document.getElementById(`watched-${video.id}`).innerHTML = 
                            `Watched: ${formatTime(maxWatchedTime)} / ${formatTime(video.duration)}`;
                        
                        // Check if completed (watched at least 90%)
                        const isCompleted = progressPercent >= 90 ? 1 : 0;
                        
                        // Save progress to database
                        fetch('grooming.php?category=<?php echo $category; ?>', {
                            method: 'POST',
                            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                            body: `update_progress=1&video_id=${video.id}&watched_duration=${maxWatchedTime}&is_completed=${isCompleted}`
                        }).then(response => response.json())
                        .then(data => {
                            if (data.success && isCompleted && !video.is_completed) {
                                // Mark as completed in UI
                                const card = document.getElementById(`video-card-${video.id}`);
                                card.classList.add('completed');
                                const badge = card.querySelector('.video-badge');
                                badge.classList.remove('pending');
                                badge.innerHTML = '<i class="fas fa-check-circle"></i> Completed';
                                
                                // Reload to update overall progress
                                setTimeout(() => location.reload(), 1000);
                            }
                        });
                    }
                }, 5000);
            }
        });

        function formatTime(seconds) {
            const mins = Math.floor(seconds / 60);
            const secs = seconds % 60;
            return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        }

        // Stop trackers when leaving page
        window.addEventListener('beforeunload', () => {
            Object.values(trackers).forEach(interval => clearInterval(interval));
        });
    </script>
</body>
</html>
