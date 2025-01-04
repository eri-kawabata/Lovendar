<!-- イベントの作成・編集を行うページ。 -->

<?php
include '../config/config.php';
include '../config/functions.php';
session_start();

redirectIfNotLoggedIn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $date_time = $_POST['date_time'];
    $location = trim($_POST['location']);
    $created_by = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO events (title, description, date_time, location, created_by) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $title, $description, $date_time, $location, $created_by);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        header('Location: dashboard.php');
        exit;
    } else {
        echo "イベントの作成に失敗しました。";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>イベント作成</title>
</head>
<body>
    <form method="POST">
        <label>タイトル:</label>
        <input type="text" name="title" required>
        <label>説明:</label>
        <textarea name="description"></textarea>
        <label>日時:</label>
        <input type="datetime-local" name="date_time" required>
        <label>場所:</label>
        <input type="text" name="location">
        <button type="submit">イベントを作成</button>
    </form>
</body>
</html>
