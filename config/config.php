<!-- データベース接続設定を記述。 -->

<?php
$host = 'localhost';
$db = 'event_manager';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("データベース接続失敗: " . $conn->connect_error);
}
?>