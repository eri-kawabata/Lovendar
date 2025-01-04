<!-- 共通で利用するPHP関数を定義（例: 入力バリデーション、リダイレクト処理など）。 -->

<?php
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function redirectIfNotLoggedIn() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}
?>
