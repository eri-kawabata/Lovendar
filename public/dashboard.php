<!-- ログイン後のメインページ（イベント一覧など）。 -->

<?php
include '../config/config.php';
include '../config/functions.php';
session_start();

redirectIfNotLoggedIn();

$user_id = $_SESSION['user_id'];

// ペアリング情報を取得
$stmt = $conn->prepare("
    SELECT u.name AS partner_name, c.anniversary_date 
    FROM couples c 
    JOIN users u ON u.id = (CASE WHEN c.user1_id = ? THEN c.user2_id ELSE c.user1_id END)
    WHERE c.user1_id = ? OR c.user2_id = ?
");
$stmt->bind_param("iii", $user_id, $user_id, $user_id);
$stmt->execute();
$pairing_info = $stmt->get_result()->fetch_assoc();

// イベント一覧を取得
$stmt = $conn->prepare("
    SELECT e.title, e.description, e.date_time, e.location 
    FROM events e 
    WHERE e.created_by = ? 
    ORDER BY e.date_time
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$events = $stmt->get_result();

// 通知一覧を取得
$stmt = $conn->prepare("SELECT message, is_read FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$notifications = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>ダッシュボード</title>
</head>
<body>
    <h1>ダッシュボード</h1>

    <!-- ペアリング情報 -->
    <?php if ($pairing_info): ?>
        <h2>パートナー情報</h2>
        <p>パートナー: <?php echo htmlspecialchars($pairing_info['partner_name']); ?></p>
        <p>記念日: <?php echo htmlspecialchars($pairing_info['anniversary_date']); ?></p>
    <?php else: ?>
        <p>パートナーとペアリングしてください。</p>
        <a href="pairing.php">ペアリングする</a>
    <?php endif; ?>

    <!-- イベント一覧 -->
    <h2>イベント一覧</h2>
    <a href="event_form.php">新しいイベントを作成</a>
    <ul>
        <?php while ($event = $events->fetch_assoc()): ?>
            <li>
                <strong><?php echo htmlspecialchars($event['title']); ?></strong><br>
                説明: <?php echo htmlspecialchars($event['description']); ?><br>
                日時: <?php echo htmlspecialchars($event['date_time']); ?><br>
                場所: <?php echo htmlspecialchars($event['location']); ?><br>
            </li>
        <?php endwhile; ?>
    </ul>

    <!-- 通知一覧 -->
    <h2>通知</h2>
    <ul>
        <?php while ($notification = $notifications->fetch_assoc()): ?>
            <li>
                <?php echo htmlspecialchars($notification['message']); ?>
                <?php if (!$notification['is_read']): ?>
                    <strong>(新規)</strong>
                <?php endif; ?>
            </li>
        <?php endwhile; ?>
    </ul>
</body>
</html>
