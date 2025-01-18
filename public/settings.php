<?php
session_start();
include '../config/config.php';
include '../config/functions.php';

// header.php を読み込む
include '../templates/header.php';

redirectIfNotLoggedIn();

$user_id = $_SESSION['user_id'];
$message = "";

try {
    // ユーザー情報を取得
    $stmt = $conn->prepare("SELECT name, email, notify_updates FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $notify_updates = isset($_POST['notify_updates']) ? 1 : 0;

        if (empty($name) || empty($email)) {
            $_SESSION['message'] = "名前とメールアドレスは必須項目です。";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['message'] = "有効なメールアドレスを入力してください。";
        } else {
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
                $_SESSION['message'] = "設定が更新されました。";
            } else {
                $_SESSION['message'] = "変更はありませんでした。";
            }
            header('Location: settings.php');
            exit;
        }
    }
} catch (Exception $e) {
    $_SESSION['message'] = "エラーが発生しました: " . htmlspecialchars($e->getMessage());
    header('Location: settings.php');
    exit;
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
    <main>
        <div class="container">
            <h1>設定</h1>
            <?php if (!empty($_SESSION['message'])): ?>
                <div class="card">
                    <p><?php echo htmlspecialchars($_SESSION['message']); ?></p>
                </div>
                <?php unset($_SESSION['message']); ?>
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

                <button type="submit" class="btn-save">保存</button>
            </form>
        </div>
    </main>
</body>
</html>
