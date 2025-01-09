<?php
include '../config/config.php';
include '../config/functions.php';
session_start();

redirectIfNotLoggedIn();

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $partner_email = trim($_POST['partner_email']);
    $anniversary_date = $_POST['anniversary_date'];

    // 入力チェック
    if (filter_var($partner_email, FILTER_VALIDATE_EMAIL) && !empty($anniversary_date)) {
        try {
            // パートナーのユーザーIDを取得
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $partner_email);
            $stmt->execute();
            $result = $stmt->get_result();
            $partner = $result->fetch_assoc();

            if ($partner) {
                $partner_id = $partner['id'];
                $user_id = $_SESSION['user_id'];

                // 既にペアリングされているか確認
                $stmt = $conn->prepare("SELECT * FROM couples WHERE (user1_id = ? AND user2_id = ?) OR (user1_id = ? AND user2_id = ?)");
                $stmt->bind_param("iiii", $user_id, $partner_id, $partner_id, $user_id);
                $stmt->execute();
                $existing_pairing = $stmt->get_result()->fetch_assoc();

                if ($existing_pairing) {
                    $message = "既にこのユーザーとペアリングされています。";
                } else {
                    // カップルデータを挿入
                    $stmt = $conn->prepare("INSERT INTO couples (user1_id, user2_id, anniversary_date) VALUES (?, ?, ?)");
                    $stmt->bind_param("iis", $user_id, $partner_id, $anniversary_date);
                    if ($stmt->execute()) {
                        // ユーザーの partner_id を更新
                        $stmt = $conn->prepare("UPDATE users SET partner_id = ? WHERE id = ?");
                        $stmt->bind_param("ii", $partner_id, $user_id);
                        $stmt->execute();

                        $message = "ペアリングが完了しました！";
                    } else {
                        $message = "ペアリングの登録中にエラーが発生しました。";
                    }
                }
            } else {
                $message = "指定したメールアドレスのユーザーが見つかりません。";
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            $message = "システムエラーが発生しました。管理者にお問い合わせください。";
        }
    } else {
        $message = "入力データが正しくありません。";
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ペアリング - Lovendar</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;700&display=swap" rel="stylesheet">
    <!-- カスタムCSS -->
    <link rel="stylesheet" href="../assets/css/pairing.css">
</head>
<body>
    <header>
        <input type="checkbox" id="menu" />
        <label for="menu" class="menu">
            <span></span>
            <span></span>
            <span></span>
        </label>
        <nav class="nav">
            <ul>
                <li><a href="dashboard.php">ホーム</a></li>
                <li><a href="event_form.php">イベントの作成</a></li>
                <li><a href="calendar.php">カレンダー</a></li>
                <li><a href="settings.php">設定</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <div class="container pairing-container">
    <h2>パートナーとペアリング</h2>
    <?php if ($message): ?>
        <div class="alert">
            <p><?php echo htmlspecialchars($message); ?></p>
        </div>
    <?php endif; ?>

    <form method="POST" class="pairing-form">
        <div class="form-group">
            <label for="partner_email">パートナーのメールアドレス:</label>
            <input type="email" id="partner_email" name="partner_email" placeholder="例: partner@example.com" required>
        </div>

        <div class="form-group">
            <label for="anniversary_date">記念日:</label>
            <input type="date" id="anniversary_date" name="anniversary_date" required>
        </div>

        <button type="submit" class="btn">ペアリング</button>
    </form>
</div>

    </main>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const menuToggle = document.getElementById('menu');
            menuToggle.addEventListener('change', function () {
                document.querySelector('.nav').classList.toggle('active');
            });
        });
    </script>
</body>
</html>
