<!-- データベース接続設定を記述。 -->

<?php
$host = 'mysql3104.db.sakura.ne.jp';
$db = 'gs-erik_lovendar';
$user = 'gs-erik_lovendar';
$pass = 'gintoki555';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("データベース接続失敗: " . $conn->connect_error);
}
?>