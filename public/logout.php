<!-- セッションを破棄してログアウトする処理。 -->
<?php
session_start();

// セッションを破棄
$_SESSION = []; // セッション変数を空にする
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    ); // セッションのクッキーを削除
}
session_destroy(); // セッションを破壊

// ログインページまたはホームページにリダイレクト
header("Location: login.php");
exit;
