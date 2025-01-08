<?php
// header.php: 共通ヘッダー
?>
<header>
    <div class="container">
        <a href="#" class="logo">イベント管理システム</a>
        <button class="hamburger" id="hamburger">☰</button>
        <nav class="nav-menu" id="nav-menu">
            <ul>
                <li><a href="dashboard.php">ホーム</a></li>
                <li><a href="event_form.php">イベントの作成</a></li>
                <li><a href="calendar.php">カレンダー</a></li>
                <li><a href="settings.php">設定</a></li>
            </ul>
        </nav>
    </div>
</header>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const hamburger = document.getElementById('hamburger');
        const navMenu = document.getElementById('nav-menu');

        hamburger.addEventListener('click', function () {
            navMenu.classList.toggle('active');
        });
    });
</script>




