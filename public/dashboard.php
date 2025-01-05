<!-- ログイン後のメインページ（イベント一覧など）。 -->

<?php
include '../config/config.php';
include '../config/functions.php';
session_start();

redirectIfNotLoggedIn();

$user_id = $_SESSION['user_id'];

// ペアリング情報
$stmt = $conn->prepare("
    SELECT u.name AS partner_name, c.anniversary_date 
    FROM couples c 
    JOIN users u ON u.id = (CASE WHEN c.user1_id = ? THEN c.user2_id ELSE c.user1_id END)
    WHERE c.user1_id = ? OR c.user2_id = ?
");
$stmt->bind_param("iii", $user_id, $user_id, $user_id);
$stmt->execute();
$pairing_info = $stmt->get_result()->fetch_assoc();

// イベント一覧
$stmt = $conn->prepare("
    SELECT e.id, e.title, e.description, e.date_time, e.location 
    FROM events e 
    WHERE e.created_by = ? 
    ORDER BY e.date_time
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$events = $stmt->get_result();

// 通知一覧
$stmt = $conn->prepare("SELECT id, message, is_read FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$notifications = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ダッシュボード</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;700&display=swap" rel="stylesheet">
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
    <!-- カスタムCSS -->
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <header>ダッシュボード</header>
    <div class="container">
        <!-- ペアリング情報 -->
        <?php if ($pairing_info): ?>
            <div class="card">
                <h2>パートナー情報</h2>
                <p>パートナー: <?php echo htmlspecialchars($pairing_info['partner_name']); ?></p>
                <p>記念日: <?php echo htmlspecialchars($pairing_info['anniversary_date']); ?></p>
            </div>
        <?php else: ?>
            <div class="card">
                <p>パートナーとペアリングしてください。</p>
                <a href="pairing.php" class="btn">ペアリングする</a>
            </div>
        <?php endif; ?>

        <!-- カレンダー -->
        <div id="calendar" class="card"></div>
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var calendarEl = document.getElementById('calendar');
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    events: 'calendar.php',
                    eventClick: function(info) {
                        alert('イベント: ' + info.event.title);
                    }
                });
                calendar.render();
            });
        </script>

        <!-- イベント一覧 -->
        <div class="card">
            <h2>イベント一覧</h2>
            <ul>
                <?php while ($event = $events->fetch_assoc()): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($event['title']); ?></strong><br>
                        説明: <?php echo htmlspecialchars($event['description']); ?><br>
                        日時: <?php echo htmlspecialchars($event['date_time']); ?><br>
                        場所: <?php echo htmlspecialchars($event['location']); ?><br>
                        <a href="event_edit.php?id=<?php echo $event['id']; ?>" class="btn btn-sm">編集</a>
                        <a href="event_delete.php?id=<?php echo $event['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('本当にこのイベントを削除しますか？');">削除</a>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>

        <!-- 通知一覧 -->
        <div class="card">
            <h2>通知</h2>
            <ul>
                <?php while ($notification = $notifications->fetch_assoc()): ?>
                    <li>
                        <?php echo htmlspecialchars($notification['message']); ?>
                        <?php if (!$notification['is_read']): ?>
                            <span class="badge bg-warning text-dark">新規</span>
                        <?php endif; ?>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>
    </div>
</body>
</html>



