<?php
session_start();

if (isset($_SESSION['account'])) {
    header("Location: index_FGPT.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $account = $_POST['account'];
    $password = $_POST['password'];
    $password0 = $_POST['password0'];
    $role = 'user'; // 預設為員工

    // 連接資料庫
    $link = mysqli_connect("localhost", "root", "", "FrugalGPT");
    mysqli_query($link, "SET NAMES utf8");

    // 檢查帳號是否重複
    $sql = "SELECT * FROM user_account WHERE account = '$account'";
    $result = mysqli_query($link, $sql);

    if (mysqli_num_rows($result) > 0) {
        $error_message = "帳號已存在";
    } elseif ($password != $password0){
        $error_message = "請重新確認密碼";
    } else {
        // 新增帳號資訊
        $sql = "INSERT INTO user_account (account, password, role) VALUES ('$account', '$password', '$role')";
        $result = mysqli_query($link, $sql);

        if ($result) {
            $_SESSION['account'] = $account;
            $_SESSION['role'] = $role;
            header("Location: logout_FGPT.php");
            exit();
        } else {
            $error_message = "註冊失敗";
        }
    }

    mysqli_close($link);
}
?>

<html>
<head>
    <meta charset="utf-8">
    <title>註冊</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            padding: 20px;
            color: #333;
        }

        h1 {
            color: #333;
        }

        h2 {
            color: #007bff;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        form {
            margin-bottom: 20px;
        }

        input[type="text"],
        input[type="password"] {
            width: 200px;
            padding: 5px;
        }

        input[type="submit"] {
            padding: 5px 10px;
            background-color: #007bff;
            border: none;
            color: white;
            cursor: pointer;
        }

        a {
            text-decoration: none;
            color: #007bff;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>註冊</h1>
    <?php if (isset($error_message)) { echo "<p>$error_message</p>"; } ?>
    <form method="POST" action="register_FGPT.php">
        <label for="account">帳號：</label>
        <input type="text" name="account" id="account" required><br>
        <label for="password">密碼：</label>
        <input type="password" name="password" id="password" required><br>
        <label for="password0">確認密碼：</label>
        <input type="password" name="password0" id="password0" required><br>
        <input type="submit" value="註冊">
    </form>
    <p>已經有帳號了？<a href="login_FGPT.php">登錄</a></p>
</body>
</html>