<?php
// Include necessary files and start session
include '../config/config.php';
include '../config/functions.php';
session_start();

// Redirect user if not logged in
redirectIfNotLoggedIn();

$user_id = $_SESSION['user_id'];
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Fetch and sanitize form data
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $notify_updates = isset($_POST['notify_updates']) ? 1 : 0;

    // Validate inputs
    if (empty($name) || empty($email)) {
        $message = "名前とメールアドレスは必須項目です。";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "有効なメールアドレスを入力してください。";
    } else {
        try {
            // Prepare SQL for updating user information
            if (!empty($password)) {
                $password_hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare(
                    "UPDATE users SET name = ?, email = ?, password = ?, notify_updates = ? WHERE id = ?"
                );
                $stmt->bind_param("sssii", $name, $email, $password_hashed, $notify_updates, $user_id);
            } else {
                $stmt = $conn->prepare(
                    "UPDATE users SET name = ?, email = ?, notify_updates = ? WHERE id = ?"
                );
                $stmt->bind_param("ssii", $name, $email, $notify_updates, $user_id);
            }
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $message = "設定が更新されました。";
            } else {
                $message = "変更はありませんでした。";
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            $message = "エラーが発生しました。管理者にお問い合わせください。";
        }
    }
}

// Fetch user information
$stmt = $conn->prepare("SELECT name, email, notify_updates FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
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
    <!-- Header with hamburger menu -->
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

    <!-- Main content -->
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
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

                <label for="email">メールアドレス:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

                <label for="password">新しいパスワード (変更する場合):</label>
                <input type="password" id="password" name="password">

                <label>
                    <input type="checkbox" name="notify_updates" <?php echo $user['notify_updates'] ? 'checked' : ''; ?>>
                    アップデートに関する通知を受け取る
                </label>

                <button type="submit">保存</button>
            </form>
        </div>
    </main>
</body>
</html>


