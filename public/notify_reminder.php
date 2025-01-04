<?php
include '../config/config.php';

$stmt = $conn->prepare("
    SELECT u.email, e.title, e.date_time 
    FROM events e
    JOIN users u ON e.created_by = u.id
    WHERE DATE(e.date_time) = DATE_ADD(CURDATE(), INTERVAL 1 DAY)
");
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $to = $row['email'];
    $subject = "リマインダー: " . $row['title'];
    $message = "明日はイベント '" . $row['title'] . "' が予定されています。";
    mail($to, $subject, $message);
}
?>
