<?php
include '../config/config.php';
include '../config/functions.php';
session_start();

redirectIfNotLoggedIn();

$message = "";

if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit;
}

$event_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// イベントデータを取得
$stmt = $conn->prepare("SELECT * FROM events WHERE id = ? AND created_by = ?");
$stmt->bind_param("ii", $event_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

if (!$event) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $date_time = $_POST['date_time'];
    $location = trim($_POST['location']);

    try {
        // イベントを更新
        $stmt = $conn->prepare("UPDATE events SET title = ?, description = ?, date_time = ?, location = ? WHERE id = ? AND created_by = ?");
        $stmt->bind_param("ssssii", $title, $description, $date_time, $location, $event_id, $user_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            header('Location: dashboard.php?message=イベントが更新されました');
            exit;
        } else {
            $message = "イベントの更新に失敗しました。";
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
    <title>イベント編集</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;700&display=swap" rel="stylesheet">
    <!-- カスタムCSS -->
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <header>イベント編集</header>
    <div class="container">
        <?php if ($message): ?>
            <div class="card">
                <p><?php echo htmlspecialchars($message); ?></p>
            </div>
        <?php endif; ?>

        <form method="POST" class="card">
            <label for="title">タイトル:</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($event['title']); ?>" required>

            <label for="description">説明:</label>
            <textarea id="description" name="description"><?php echo htmlspecialchars($event['description']); ?></textarea>

            <label for="date_time">日時:</label>
            <input type="datetime-local" id="date_time" name="date_time" value="<?php echo date('Y-m-d\TH:i', strtotime($event['date_time'])); ?>" required>

            <label for="location">場所:</label>
            <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($event['location']); ?>">

            <button type="submit">イベントを更新</button>
        </form>
    </div>
</body>
</html>
