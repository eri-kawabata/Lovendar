<?php
session_start();
include '../config/config.php';
include '../config/functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$message = "";

try {
    // ユーザー情報を取得
    $stmt = $conn->prepare("SELECT name, email, notify_updates FROM users WHERE id = ?");
    if (!$stmt) {
        throw new Exception('クエリ準備に失敗しました: ' . $conn->error);
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if (!$user) {
        throw new Exception('ユーザー情報が見つかりません。');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // フォームデータを取得
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $notify_updates = isset($_POST['notify_updates']) ? 1 : 0;

        // 入力バリデーション
        if (empty($name) || empty($email)) {
            $message = "名前とメールアドレスは必須項目です。";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "有効なメールアドレスを入力してください。";
        } else {
            // ユーザー情報を更新
            if (!empty($password)) {
                $password_hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, password = ?, notify_updates = ? WHERE id = ?");
                $stmt->bind_param("sssii", $name, $email, $password_hashed, $notify_updates, $user_id);
            } else {
                $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, notify_updates = ? WHERE id = ?");
                $stmt->bind_param("ssii", $name, $email, $notify_updates, $user_id);
            }
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $message = "設定が更新されました。";
            } else {
                $message = "変更はありませんでした。";
            }
        }
    }
} catch (Exception $e) {
    $message = "エラーが発生しました: " . htmlspecialchars($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>設定</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/settings.css">
</head>
<body>
    <header>
        <div class="menu" id="menu">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <nav class="nav" id="nav">
            <ul>
                <li><a href="dashboard.php">ホーム</a></li>
                <li><a href="event_form.php">イベントの作成</a></li>
                <li><a href="calendar.php">カレンダー</a></li>
                <li><a href="settings.php">設定</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <div class="container">
            <h1>設定</h1>
            <?php if ($message): ?>
                <div class="card">
                    <p><?php echo htmlspecialchars($message); ?></p>
                </div>
            <?php endif; ?>

            <form method="POST" class="card">
                <label for="name">名前:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>

                <label for="email">メールアドレス:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>

                <label for="password">新しいパスワード (変更する場合):</label>
                <input type="password" id="password" name="password">

                <label>
                    <input type="checkbox" name="notify_updates" <?php echo !empty($user['notify_updates']) ? 'checked' : ''; ?>>
                    アップデートに関する通知を受け取る
                </label>

                <button type="submit">保存</button>
            </form>
        </div>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const menu = document.getElementById('menu');
            const nav = document.getElementById('nav');

            menu.addEventListener('click', () => {
                menu.classList.toggle('open');
                nav.classList.toggle('open');
            });

            nav.addEventListener('click', () => {
                menu.classList.remove('open');
                nav.classList.remove('open');
            });
        });
    </script>
</body>
</html>
