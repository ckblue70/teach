<?php
session_start();

if (isset($_SESSION['account'])) {
    header("Location: index_FGPT.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $account = $_POST['account'];
    $password = $_POST['password'];

    // 連接資料庫
    $link = mysqli_connect("localhost", "root", "", "FrugalGPT");
    mysqli_query($link, "SET NAMES utf8");

    // 查詢帳號資訊
    $sql = "SELECT * FROM user_account WHERE account = '$account' AND password = '$password'";
    $result = mysqli_query($link, $sql);
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        // 登入成功，設定使用者狀態
        $_SESSION['ID'] = $row['ID'];
        $_SESSION['account'] = $row['account'];
        $_SESSION['password'] = $row['password'];
        $_SESSION['role'] = $row['role'];
        header("Location: index_FGPT.php");
        exit();
    } else {
        $error_message = "帳號或密碼錯誤";
    }

    mysqli_close($link);
}
?>

<html>
<head>
    <meta charset="utf-8">
    <title>登入</title>
    <link rel="stylesheet" type="text/css" href="style1.css">
</head>
<body>
    <h1 style='font-size:48px'>歡迎光臨FrugalGPT</h1>
    <h1>登入</h1>
    <?php if (isset($error_message)) { echo "<p>$error_message</p>"; } ?>
    <form method="POST" action="login_FGPT.php">
        <label for="account">帳號：</label>
        <input type="text" name="account" id="account" required><br>
        <label for="password">密碼：</label>
        <input type="password" name="password" id="password" required><br>
        <input type="submit" value="登入">
    </form>
    <p>還沒有帳號嗎？<a href="register_FGPT.php">註冊</a></p>
</body>
</html>