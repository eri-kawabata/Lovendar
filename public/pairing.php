<?php
include '../config/config.php';
include '../config/functions.php';
session_start();

redirectIfNotLoggedIn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $partner_email = trim($_POST['partner_email']);
    $anniversary_date = $_POST['anniversary_date'];

    // パートナーのユーザーIDを取得
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $partner_email);
    $stmt->execute();
    $result = $stmt->get_result();
    $partner = $result->fetch_assoc();

    if ($partner) {
        $partner_id = $partner['id'];
        $user_id = $_SESSION['user_id'];

        // カップルデータを挿入
        $stmt = $conn->prepare("INSERT INTO couples (user1_id, user2_id, anniversary_date) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $user_id, $partner_id, $anniversary_date);
        $stmt->execute();

        // ユーザーの partner_id を更新
        $stmt = $conn->prepare("UPDATE users SET partner_id = ? WHERE id = ?");
        $stmt->bind_param("ii", $partner_id, $user_id);
        $stmt->execute();

        echo "ペアリングが完了しました！";
    } else {
        echo "指定したメールアドレスのユーザーが見つかりません。";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>ペアリング</title>
</head>
<body>
    <form method="POST">
        <label>パートナーのメールアドレス:</label>
        <input type="email" name="partner_email" required>
        <label>記念日:</label>
        <input type="date" name="anniversary_date" required>
        <button type="submit">ペアリング</button>
    </form>
</body>
</html>
