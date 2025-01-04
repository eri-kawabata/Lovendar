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
<html>
<head>
    <title>ログイン</title>
</head>
<body>
    <form method="POST">
        <label>メールアドレス:</label>
        <input type="email" name="email" required>
        <label>パスワード:</label>
        <input type="password" name="password" required>
        <button type="submit">ログイン</button>
    </form>
</body>
</html>
