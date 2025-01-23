<?php
session_start();
include '../config/config.php';
include '../config/functions.php';

redirectIfNotLoggedIn();

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>イベントカレンダー</title>
    <!-- FullCalendar CSSの読み込み -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
    <!-- Googleフォントの読み込み -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;700&display=swap" rel="stylesheet">
    <!-- カスタムCSSファイルの読み込み -->
    <link rel="stylesheet" href="../assets/css/calendar.css">
</head>
<body>
    <header>
        <!-- ヘッダー部分（必要に応じてカスタマイズ） -->
    </header>
    <main>
        <div class="container">
            <h1>イベントカレンダー</h1>
            <!-- FullCalendarを表示するためのコンテナ -->
            <div id="calendar"></div>
        </div>
    </main>

    <!-- FullCalendar JSライブラリの読み込み -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js"></script>
    <!-- FullCalendarの日本語ロケール対応 -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales/ja.js"></script>
    <!-- SweetAlert2（モーダル表示ライブラリ）の読み込み -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // カレンダーを表示する要素を取得
            const calendarEl = document.getElementById('calendar');

            // FullCalendarのインスタンスを作成
            const calendar = new FullCalendar.Calendar(calendarEl, {
                // カレンダーの初期表示ビュー（ここでは月間ビュー）
                initialView: 'dayGridMonth',
                // 日本語ロケールを適用
                locale: 'ja',
                // ヘッダーツールバーの設定
                headerToolbar: {
                    left: 'prev,next today', // 左側に表示するボタン（前月、次月、今日）
                    center: 'title',         // 中央に表示するカレンダーのタイトル
                    right: 'dayGridMonth,timeGridWeek,timeGridDay' // 右側に表示するビュー切り替え
                },
                // イベントのドラッグ＆ドロップやリサイズを可能にする
                editable: true,
                // 外部ドラッグアイテムの受け入れを可能にする
                droppable: true,
                // イベントデータをAPIから取得
                events: '../api/calendar_events.php',
                // イベントがクリックされたときの処理
                eventClick: function (info) {
                    // SweetAlert2を使ってモーダルを表示
                    Swal.fire({
                        title: info.event.title, // イベントタイトルを表示
                        html: `
                            <p><strong>場所:</strong> ${info.event.extendedProps.location || '未設定'}</p>
                            <p><strong>詳細:</strong> ${info.event.extendedProps.description || '未設定'}</p>
                            <p><strong>開始:</strong> ${info.event.start.toLocaleString()}</p>
                            <p><strong>終了:</strong> ${info.event.end ? info.event.end.toLocaleString() : '未設定'}</p>
                        `,
                        icon: 'info',
                        confirmButtonText: '閉じる'
                    });
                },
                // カレンダーの日付がクリックされたときの処理（新しいイベントの追加）
                dateClick: function (info) {
                    // SweetAlert2を使ったモーダルでイベントの入力フォームを表示
                    Swal.fire({
                        title: '新しいイベント',
                        html: `
                            <input type="text" id="eventTitle" class="swal2-input" placeholder="イベント名">
                            <textarea id="eventDesc" class="swal2-textarea" placeholder="詳細"></textarea>
                        `,
                        showCancelButton: true, // キャンセルボタンを表示
                        confirmButtonText: '保存', // 保存ボタンのテキスト
                        cancelButtonText: 'キャンセル', // キャンセルボタンのテキスト
                        // 保存前のバリデーション処理
                        preConfirm: () => {
                            const title = document.getElementById('eventTitle').value;
                            const description = document.getElementById('eventDesc').value;
                            // タイトルが入力されていない場合のエラーメッセージ
                            if (!title) {
                                Swal.showValidationMessage('タイトルを入力してください');
                            }
                            return { title, description }; // 入力内容を返す
                        }
                    }).then((result) => {
                        // ユーザーが保存ボタンを押した場合
                        if (result.isConfirmed) {
                            // 新しいイベントオブジェクトを作成
                            const newEvent = {
                                title: result.value.title,
                                start: info.dateStr, // 選択された日付
                                description: result.value.description,
                                allDay: true, // 終日イベントとして設定
                            };
                            // カレンダーにイベントを追加
                            calendar.addEvent(newEvent);
                            // 新しいイベントをバックエンドに保存
                            fetch('../api/add_event.php', {
                                method: 'POST', // HTTP POSTリクエストを使用
                                headers: {
                                    'Content-Type': 'application/json', // JSON形式で送信
                                },
                                body: JSON.stringify(newEvent), // イベントデータを送信
                            }).then(response => {
                                // 保存が成功した場合
                                if (response.ok) {
                                    Swal.fire('保存しました', '', 'success');
                                } else {
                                    // 保存が失敗した場合
                                    Swal.fire('保存に失敗しました', '', 'error');
                                }
                            });
                        }
                    });
                },
                // イベントをドラッグ＆ドロップしたときの処理
                eventDrop: function (info) {
                    // 更新されたイベントデータを作成
                    const event = {
                        id: info.event.id, // イベントID
                        start: info.event.start.toISOString(), // 更新後の開始日時
                        end: info.event.end ? info.event.end.toISOString() : null, // 更新後の終了日時
                    };
                    // バックエンドにイベントデータを送信して更新
                    fetch('../api/update_event.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(event),
                    }).catch(() => {
                        // エラーが発生した場合、変更を元に戻す
                        Swal.fire('イベントの更新に失敗しました', '', 'error');
                        info.revert();
                    });
                },
                // イベントをリサイズ（期間変更）したときの処理
                eventResize: function (info) {
                    // 更新されたイベントデータを作成
                    const event = {
                        id: info.event.id,
                        start: info.event.start.toISOString(),
                        end: info.event.end.toISOString(),
                    };
                    // バックエンドにイベントデータを送信して更新
                    fetch('../api/update_event.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(event),
                    }).catch(() => {
                        // エラーが発生した場合、変更を元に戻す
                        Swal.fire('イベントの更新に失敗しました', '', 'error');
                        info.revert();
                    });
                },
            });

            // カレンダーをレンダリング
            calendar.render();
        });
    </script>
</body>
</html>





