<?php
include '../config/config.php';
include '../config/functions.php';
session_start();

redirectIfNotLoggedIn();

$message = "";
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $date_time = $_POST['date_time'];
    $location = trim($_POST['location']);
    $created_by = $_SESSION['user_id'];

    if (empty($title)) {
        $errors['title'] = "タイトルは必須項目です。";
    } elseif (strlen($title) > 255) {
        $errors['title'] = "タイトルは255文字以内で入力してください。";
    }

    if (empty($date_time)) {
        $errors['date_time'] = "日時は必須項目です。";
    }

    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("INSERT INTO events (title, description, date_time, location, created_by) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssi", $title, $description, $date_time, $location, $created_by);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $_SESSION['success_message'] = "イベントが作成されました";
                header('Location: dashboard.php');
                exit;
            } else {
                $message = "イベントの作成に失敗しました。";
            }
        } catch (Exception $e) {
            $message = "エラーが発生しました。管理者にお問い合わせください。";
        }
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
    <link rel="stylesheet" href="../assets/css/event_form.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>
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
    <main>
        <div class="container">
            <?php if ($message): ?>
                <div class="card">
                    <p><?php echo htmlspecialchars($message); ?></p>
                </div>
            <?php endif; ?>

            <form method="POST" class="card">
                <label for="title">タイトル:</label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    placeholder="イベントのタイトル" 
                    value="<?php echo htmlspecialchars($title ?? ''); ?>" 
                    required>
                <?php if (!empty($errors['title'])): ?>
                    <p class="error"><?php echo htmlspecialchars($errors['title']); ?></p>
                <?php endif; ?>

                <label for="description">説明:</label>
                <textarea 
                    id="description" 
                    name="description" 
                    placeholder="イベントの詳細を記載"><?php echo htmlspecialchars($description ?? ''); ?></textarea>

                <label for="date_time">日時:</label>
                <input 
                    type="datetime-local" 
                    id="date_time" 
                    name="date_time" 
                    value="<?php echo htmlspecialchars($date_time ?? ''); ?>" 
                    required>
                <?php if (!empty($errors['date_time'])): ?>
                    <p class="error"><?php echo htmlspecialchars($errors['date_time']); ?></p>
                <?php endif; ?>

                <label for="location">場所:</label>
                <input 
                    type="text" 
                    id="location" 
                    name="location" 
                    placeholder="イベントの場所" 
                    value="<?php echo htmlspecialchars($location ?? ''); ?>">

                <button type="submit">イベントを作成</button>
            </form>
        </div>
    </main>
</body>
</html>

