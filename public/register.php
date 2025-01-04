<!-- ユーザー登録フォーム。 -->

<?php
include '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $password);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        header('Location: login.php');
        exit;
    } else {
        echo "登録に失敗しました。";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>登録</title>
</head>
<body>
    <form method="POST">
        <label>名前:</label>
        <input type="text" name="name" required>
        <label>メールアドレス:</label>
        <input type="email" name="email" required>
        <label>パスワード:</label>
        <input type="password" name="password" required>
        <button type="submit">登録</button>
    </form>
</body>
</html>

