<?php
include '../config/config.php';
include '../config/functions.php';
session_start();

redirectIfNotLoggedIn();

$user_id = $_SESSION['user_id'];
$event_id = $_GET['id'] ?? null;

if ($event_id) {
    try {
        // イベント削除クエリ
        $stmt = $conn->prepare("DELETE FROM events WHERE id = ? AND created_by = ?");
        $stmt->bind_param("ii", $event_id, $user_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            header('Location: dashboard.php?message=イベントが削除されました');
            exit;
        } else {
            echo "イベントの削除に失敗しました。";
        }
    } catch (Exception $e) {
        echo "エラーが発生しました: " . $e->getMessage();
    }
} else {
    echo "無効なリクエストです。";
}
?>
