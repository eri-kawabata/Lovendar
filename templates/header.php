<?php
// header.php: 共通ヘッダー
?>
<header>
    <div class="container">
        <!-- ロゴ -->
        <a href="#" class="logo">イベント管理システム</a>

        <!-- ハンバーガーメニュー -->
        <button class="hamburger" id="hamburger">☰</button>

        <!-- ナビゲーションメニュー -->
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

<!-- ヘッダー用スタイル -->
<style>
    :root {
        --color-header-bg: #ffbfb3; /* ヘッダー背景色 */
        --color-header-text: white; /* ヘッダー文字色 */
        --color-nav-bg: #e98e83; /* ナビゲーション背景色 */
        --color-nav-hover: rgba(255, 255, 255, 0.2); /* ホバー時の背景色 */
        --color-shadow: rgba(0, 0, 0, 0.1); /* ボックスシャドウ */
    }

    /* ヘッダー全体 */
    header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: var(--color-header-bg);
        padding: 10px 20px;
        position: fixed;
        width: 100%;
        top: 0;
        z-index: 100;
        box-shadow: 0 2px 4px var(--color-shadow);
    }

    /* ロゴ */
    .logo {
        font-size: 1.5rem;
        font-weight: bold;
        color: var(--color-header-text);
        text-decoration: none;
    }

    /* ハンバーガーメニュー */
    .hamburger {
        font-size: 1.5rem;
        color: var(--color-header-text);
        background: none;
        border: none;
        cursor: pointer;
        transition: transform 0.3s ease;
    }

    .hamburger:hover {
        transform: scale(1.1); /* ホバー時に拡大 */
    }

    /* ナビゲーションメニュー */
    .nav-menu {
        display: none; /* デフォルトは非表示 */
        position: fixed;
        top: 0;
        left: 0;
        width: 250px;
        height: 100%;
        background-color: var(--color-nav-bg);
        box-shadow: 2px 0 4px var(--color-shadow);
        padding-top: 60px;
        z-index: 99;
        transition: transform 0.3s ease-in-out;
        transform: translateX(-100%); /* 初期は左外に隠す */
    }

    .nav-menu.active {
        display: block; /* 表示時 */
        transform: translateX(0); /* 左から展開 */
    }

    .nav-menu ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .nav-menu ul li {
        margin: 15px 0;
    }

    .nav-menu ul li a {
        display: block;
        color: white;
        text-decoration: none;
        font-size: 1.2rem;
        padding: 10px 20px;
        transition: background-color 0.3s ease;
    }

    .nav-menu ul li a:hover {
        background-color: var(--color-nav-hover); /* ホバー背景色 */
        border-radius: 5px; /* 丸みを追加 */
    }

    /* レスポンシブ対応 */
    @media (min-width: 768px) {
        .hamburger {
            display: none; /* デスクトップでは非表示 */
        }

        .nav-menu {
            display: flex; /* メニューを常時表示 */
            position: static;
            transform: none;
            background: none;
            box-shadow: none;
            width: auto;
            height: auto;
            padding: 0;
        }

        .nav-menu ul {
            flex-direction: row;
        }

        .nav-menu ul li {
            margin: 0 10px;
        }

        .nav-menu ul li a {
            padding: 5px 10px;
            font-size: 1rem;
            color: var(--color-header-text);
        }

        .nav-menu ul li a:hover {
            background: none; /* 背景ホバー効果を無効 */
            color: #333; /* ホバー時に文字色を変更 */
        }
    }
</style>

<!-- ヘッダー用スクリプト -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const hamburger = document.getElementById('hamburger');
        const navMenu = document.getElementById('nav-menu');

        // ハンバーガーメニューの表示/非表示を切り替え
        hamburger.addEventListener('click', function () {
            navMenu.classList.toggle('active');
        });
    });
</script>






