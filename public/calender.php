<?php
include '../config/config.php';
session_start();

// ログインチェック
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// イベントデータを取得
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("
    SELECT title, description, date_time 
    FROM events 
    WHERE created_by = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$events = [];
while ($event = $result->fetch_assoc()) {
    $events[] = [
        'title' => $event['title'],
        'start' => $event['date_time'],
        'description' => $event['description']
    ];
}

header('Content-Type: application/json');
echo json_encode($events);
?>
