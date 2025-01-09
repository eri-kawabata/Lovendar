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
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;700&display=swap" rel="stylesheet">
    <!-- カスタムCSS -->
    <link rel="stylesheet" href="../assets/css/calendar.css">
</head>
<body>
    <header>
        <input type="checkbox" id="menu" />
        <label for="menu" class="menu">
            <span></span>
            <span></span>
            <span></span>
        </label>
        <nav class="nav">
            <ul>
                <li><a href="dashboard.php">ホーム</a></li>
                <li><a href="event_form.php">イベントの作成</a></li>
                <li><a href="calendar.php">カレンダー</a></li>
                <li><a href="settings.php">設定</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <div class="container">
            <h1>イベントカレンダー</h1>
            <div id="calendar"></div>
        </div>
    </main>

    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'ja',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: '../api/calendar_events.php', // APIからイベントデータを取得
                eventClick: function (info) {
                    // イベントクリック時の詳細表示
                    alert('イベント: ' + info.event.title + '\n場所: ' + info.event.extendedProps.location + '\n詳細: ' + info.event.extendedProps.description);
                }
            });
            calendar.render();
        });
    </script>
</body>
</html>





