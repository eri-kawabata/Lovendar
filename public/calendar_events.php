<?php
// 必要なファイルをインクルード
include '../config/config.php';
session_start();

// セッションチェック
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// ログイン中のユーザーIDを取得
$user_id = $_SESSION['user_id'];

try {
    // データベース接続確認（デバッグ用）
    if (!$conn) {
        throw new Exception('データベース接続エラー: ' . mysqli_connect_error());
    }

    // イベントを取得するSQLクエリ
    $stmt = $conn->prepare("
        SELECT title, date_time AS start, description, location 
        FROM events 
        WHERE created_by = ?
    ");
    $stmt->bind_param("i", $user_id); // パラメータをバインド
    $stmt->execute(); // クエリ実行
    $result = $stmt->get_result();

    $events = [];
    while ($event = $result->fetch_assoc()) {
        // イベントデータを配列に追加
        $events[] = [
            'title' => htmlspecialchars($event['title']), // サニタイズされたタイトル
            'start' => date('c', strtotime($event['start'])), // ISO 8601形式に変換
            'description' => htmlspecialchars($event['description']), // サニタイズされた説明
            'location' => htmlspecialchars($event['location']), // サニタイズされた場所
        ];
    }

    // JSON形式で成功レスポンスを返す
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'events' => $events]);

} catch (Exception $e) {
    // エラーハンドリング
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
    // エラーログの記録
    error_log($e->getMessage(), 3, '/path/to/error.log');
}
?>
