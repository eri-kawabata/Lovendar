<?php
include '../config/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // イベントを取得
    $stmt = $conn->prepare("SELECT title, description, location, date_time AS start FROM events WHERE created_by = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $events = [];
    while ($event = $result->fetch_assoc()) {
        $events[] = [
            'title' => htmlspecialchars($event['title']),
            'start' => htmlspecialchars($event['start']),
            'description' => htmlspecialchars($event['description']),
            'location' => htmlspecialchars($event['location']),
        ];
    }

    // JSONレスポンスを返す
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'events' => $events]);

} catch (Exception $e) {
    // エラーの場合
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['error' => 'An error occurred while fetching events.']);
}
?>

