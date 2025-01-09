/* カラーテーマ */
:root {
    --color-1: #333;
    /* メインテキストカラー */
    --color-accent: #ffbfb3;
    /* アクセントカラー (柔らかなピンク) */
    --color-accent-dark: #e98e83;
    /* サブアクセントカラー (濃いピンク) */
    --color-background: #fff2e5;
    /* 背景色 (淡いオレンジピンク) */
    --color-card: white;
    /* カード背景色 */
    --color-shadow: rgba(0, 0, 0, 0.1);
    /* カードシャドウ */
}

/* 全体のリセット */
*,
*:before,
*:after {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: 'Nunito', sans-serif;
    font-size: 16px;
    line-height: 1.75;
    color: var(--color-1);
    background-color: var(--color-background);
    margin: 0;
    padding: 0;
    overflow-x: hidden;
}

/* ヘッダー */
header {
    text-align: center;
    background-color: var(--color-accent);
    color: white;
    padding: 15px 0;
    font-size: 1.5rem;
    font-weight: bold;
}

/* コンテナ */
.container {
    max-width: 500px;
    margin: 50px auto;
    background-color: var(--color-card);
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 6px var(--color-shadow);
}

/* フォーム */
form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

form label {
    font-weight: bold;
    margin-bottom: 5px;
    color: var(--color-1);
}

form input {
    padding: 10px;
    border: 1px solid var(--color-accent);
    border-radius: 5px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
}

form input:focus {
    border-color: var(--color-accent-dark);
    outline: none;
}

/* ボタン */
button {
    padding: 10px;
    background-color: var(--color-accent);
    color: white;
    font-size: 1rem;
    font-weight: bold;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

button:hover {
    background-color: var(--color-accent-dark);
    transform: scale(1.05);
}

button:focus {
    outline: none;
    transform: scale(1.05);
}

/* テキストセンター */
.text-center {
    text-align: center;
    margin-top: 20px;
}

.text-center a {
    color: var(--color-accent-dark);
    text-decoration: none;
    font-weight: bold;
    transition: color 0.3s ease;
}

.text-center a:hover {
    color: var(--color-accent);
}
