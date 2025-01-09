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
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新規登録 - Lovendar</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;700&display=swap" rel="stylesheet">
    <!-- カスタムCSS -->
    <link rel="stylesheet" href="../assets/css/regiater.css">
</head>
<body>
    <header>新規登録</header>
    <main class="centered-layout">
        <div class="container">
            <form method="POST">
                <h2>アカウント作成</h2>
                <label for="name">名前:</label>
                <input type="text" id="name" name="name" placeholder="お名前を入力" required>
                
                <label for="email">メールアドレス:</label>
                <input type="email" id="email" name="email" placeholder="example@example.com" required>
                
                <label for="password">パスワード:</label>
                <input type="password" id="password" name="password" placeholder="パスワードを入力" required>
                
                <button type="submit">登録</button>
            </form>
            <p class="text-center">
                既にアカウントをお持ちですか？ <a href="login.php">ログイン</a>
            </p>
        </div>
    </main>
</body>
</html>


