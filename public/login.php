<!-- ログインフォーム。 -->

<?php
include '../config/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: dashboard.php');
        exit;
    } else {
        echo "メールアドレスまたはパスワードが間違っています。";
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン - Lovendar</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>
    <main class="centered-layout">
        <header class="login-header">Lovendar ログイン</header>
        <div class="container">
            <form method="POST">
                <label for="email">メールアドレス:</label>
                <input type="email" id="email" name="email" placeholder="example@example.com" required>
                <label for="password">パスワード:</label>
                <input type="password" id="password" name="password" placeholder="パスワードを入力" required>
                <button type="submit">ログイン</button>
            </form>
            <p class="text-center">
                アカウントをお持ちでない場合は <a href="register.php">新規登録</a>
            </p>
        </div>
    </main>
</body>
</html>

