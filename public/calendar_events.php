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
    $stmt = $conn->prepare("
        SELECT title, date_time AS start, description, location 
        FROM events 
        WHERE created_by = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $events = [];
    while ($event = $result->fetch_assoc()) {
        $events[] = [
            'title' => htmlspecialchars($event['title']),
            'start' => $event['start'], // ISO 8601形式 (例: "2025-05-29T18:00:00")
            'description' => htmlspecialchars($event['description']),
            'location' => htmlspecialchars($event['location']),
        ];
    }

    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'events' => $events]);

} catch (Exception $e) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
