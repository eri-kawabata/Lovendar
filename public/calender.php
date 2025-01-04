<?php
include '../config/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT title, date_time AS start FROM events WHERE created_by = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$events = [];
while ($event = $result->fetch_assoc()) {
    $events[] = $event;
}

header('Content-Type: application/json');
echo json_encode($events);
?>

