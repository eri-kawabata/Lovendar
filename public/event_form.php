<!-- イベントの作成・編集を行うページ。 -->

<?php
include '../config/config.php';
include '../config/functions.php';
session_start();

redirectIfNotLoggedIn();

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $date_time = $_POST['date_time'];
    $location = trim($_POST['location']);
    $created_by = $_SESSION['user_id'];

    try {
        // イベントを挿入
        $stmt = $conn->prepare("INSERT INTO events (title, description, date_time, location, created_by) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $title, $description, $date_time, $location, $created_by);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            header('Location: dashboard.php?message=イベントが作成されました');
            exit;
        } else {
            $message = "イベントの作成に失敗しました。";
        }
    } catch (Exception $e) {
        $message = "エラーが発生しました: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>イベント作成</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;700&display=swap" rel="stylesheet">
    <!-- カスタムCSS -->
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <header>イベント作成</header>
    <div class="container">
        <?php if ($message): ?>
            <div class="card">
                <p><?php echo htmlspecialchars($message); ?></p>
            </div>
        <?php endif; ?>

        <form method="POST" class="card">
            <label for="title">タイトル:</label>
            <input type="text" id="title" name="title" placeholder="イベントのタイトル" required>

            <label for="description">説明:</label>
            <textarea id="description" name="description" placeholder="イベントの詳細を記載"></textarea>

            <label for="date_time">日時:</label>
            <input type="datetime-local" id="date_time" name="date_time" required>

            <label for="location">場所:</label>
            <input type="text" id="location" name="location" placeholder="イベントの場所">

            <button type="submit">イベントを作成</button>
        </form>
    </div>
</body>
</html>

