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
    <link rel="stylesheet" href="../assets/css/register.css">
</head>
<body>
    <main class="centered-layout">
        <header>新規登録</header>
        <div class="container">
            <form method="POST" id="registerForm">
                <h2>アカウント作成</h2>
                <label for="name">名前:</label>
                <input type="text" id="name" name="name" placeholder="お名前を入力" required>
                
                <label for="email">メールアドレス:</label>
                <input type="email" id="email" name="email" placeholder="example@example.com" required>
                
                <label for="password">パスワード:</label>
                <input type="password" id="password" name="password" placeholder="パスワードを入力" required>
                
                <button type="submit" id="registerButton">登録</button>
            </form>
            <p class="text-center">
                既にアカウントをお持ちですか？ <a href="login.php">ログイン</a>
            </p>
        </div>
    </main>

    <!-- ハートアニメーション用コンテナ -->
    <div id="heartsContainer"></div>

    <script>
        document.getElementById('registerForm').addEventListener('submit', function (event) {
            event.preventDefault(); // 通常の送信を防ぐ
            triggerHearts(); // ハートをトリガー
            setTimeout(() => this.submit(), 2000); // 2秒後にフォーム送信
        });

        function triggerHearts() {
    const container = document.getElementById('heartsContainer');
    container.innerHTML = ''; // 古いハートを削除
    for (let i = 0; i < 50; i++) { // ハートの数を増やす
        const heart = document.createElement('div');
        heart.className = 'heart';
        heart.style.left = Math.random() * 100 + 'vw';
        heart.style.animationDelay = Math.random() * 1 + 's';
        heart.style.animationDuration = 3 + Math.random() * 4 + 's';

        // ランダムサイズのハートを作る
        const size = Math.random() * 30 + 50; // サイズを50px〜80pxにランダム化
        heart.style.width = size + 'px';
        heart.style.height = size + 'px';

        container.appendChild(heart);
    }
}

    </script>
</body>
</html>



