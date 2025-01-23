<?php
include '../config/config.php';
include '../config/functions.php';
session_start();

// ユーザーがログインしていない場合はリダイレクト
redirectIfNotLoggedIn();

$user_id = $_SESSION['user_id'];

// 初期化
$pairing_info = null;
$events = null;
$notifications = null;

try {
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
        SELECT e.id, e.title, e.description, e.date_time, e.location 
        FROM events e 
        WHERE e.created_by = ? 
        ORDER BY e.date_time
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $events = $stmt->get_result();

    // 通知一覧を取得
    $stmt = $conn->prepare("SELECT id, message, is_read FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $notifications = $stmt->get_result();

} catch (Exception $e) {
    error_log($e->getMessage());
    die("データベースエラーが発生しました。管理者にお問い合わせください。");
}
?>


<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ダッシュボード</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>
    <!-- ヘッダーをインクルード -->
    <?php include '../templates/header.php'; ?>

    <main>
        <div class="container">
            <!-- ペアリング情報 -->
            <?php if ($pairing_info): ?>
                <div class="card">
                    <h2>パートナー情報</h2>
                    <p>パートナー: <?= htmlspecialchars($pairing_info['partner_name']) ?></p>
                    <p>記念日: <?= htmlspecialchars($pairing_info['anniversary_date']) ?></p>
                </div>
            <?php else: ?>
                <div class="card">
                    <p>パートナーとペアリングしてください。</p>
                    <a href="pairing.php" class="btn">ペアリングする</a>
                </div>
            <?php endif; ?>

            <!-- イベント一覧 -->
            <div class="card">
                <h2>イベント一覧</h2>
                <?php if ($events && $events->num_rows > 0): ?>
                    <ul>
                        <?php while ($event = $events->fetch_assoc()): ?>
                            <li>
                                <strong><?= htmlspecialchars($event['title']) ?></strong><br>
                                説明: <?= htmlspecialchars($event['description']) ?><br>
                                日時: <?= htmlspecialchars($event['date_time']) ?><br>
                                場所: <?= htmlspecialchars($event['location']) ?><br>
                                <a href="event_edit.php?id=<?= $event['id'] ?>" class="btn btn-sm">編集</a>
                                <a href="event_delete.php?id=<?= $event['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('本当にこのイベントを削除しますか？');">削除</a>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p>イベントが登録されていません。</p>
                <?php endif; ?>
            </div>

            <!-- 通知一覧 -->
            <div class="card">
                <h2>通知</h2>
                <?php if ($notifications && $notifications->num_rows > 0): ?>
                    <ul>
                        <?php while ($notification = $notifications->fetch_assoc()): ?>
                            <li>
                                <?= htmlspecialchars($notification['message']) ?>
                                <?php if (!$notification['is_read']): ?>
                                    <span class="badge bg-warning text-dark">新規</span>
                                <?php endif; ?>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p>通知はありません。</p>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>
