<?php
session_start();

if (!isset($_SESSION['account'])) {
    header("Location: login_FGPT.php");
    exit();
}

$account = $_SESSION['account'];
$role = $_SESSION['role'];

function display_admin_interface($account) {
    // 管理員介面，可修改/刪除訂單與新增訂單
    // 連接資料庫並顯示資料
    $link = mysqli_connect("localhost", "root", "", "FrugalGPT");
    mysqli_query($link, "SET NAMES utf8");

    // 判斷日期篩選
    date_default_timezone_set('Asia/Taipei');
    $dateFilter = isset($_GET['datetime']) ? date('Y-m-d', strtotime($_GET['datetime'])) : date('Y-m-d');

    // 擷取蔬果類型和總價
    $sql = "SELECT * FROM orders WHERE Date(time) = '$dateFilter'";

    $result = mysqli_query($link, $sql);

    // query蔬果列表
    $sql1 = "SELECT vegetable FROM vegetablelist";
    $result1 = mysqli_query($link, $sql1);

    // 建立蔬果列表array
    $vegetableOptions = array();
    while ($row = mysqli_fetch_assoc($result1)) {
        $vegetableOptions[] = $row['vegetable'];
    }

    echo "<h2>訂單列表</h2>";
    echo "<form action='' method='GET'>";
    echo "<label for='date'>選擇日期：</label>";
    echo "<input type='date' id='date' name='date' value='<?php echo $dateFilter; ?>'>";
    echo "<input type='submit' value='查詢'></form>";

    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>蔬果</th><th>數量</th><th>價格</th><th>總價</th><th>使用者</th><th>時間</th><th>操作</th></tr>";

    while ($row = mysqli_fetch_assoc($result)) {
        $order_id = $row['id'];
        $vegetable = $row['vegetable'];
        $amount = $row['amount'];
        $price = $row['price'];
        $total = $price * $amount;
        $id = $row['user_id'];
        $time = $row['time'];
        echo "<tr><td>$order_id</td><td>$vegetable</td><td>$amount</td><td>$price</td><td>$total</td><td>$id</td><td>$time</td>";
        echo "<td><a href='edit_order.php?id=$order_id'>修改</a> | <a href='delete_order.php?id=$order_id'>刪除</a></td></tr>";
    }

    echo "</table>";
    echo "<a href='barchart.php'>查看蔬果總價直條圖</a> ｜ <a href='piechart.php'>查看蔬果總價圓餅圖</a>";;
    echo "<h2>新增訂單</h2>";
    echo "<p><a href='manage_vegetable.php'>管理蔬果</a></p>";
    echo "<form method='POST' action='add_order.php'>";
    // 蔬果用下拉式選單
    echo "<label for='vegetable'>蔬果：</label>";
    echo "<select style='width:200px;height:32px;font-size:16px' name='vegetable' id='vegetable' required>";
    foreach ($vegetableOptions as $option) {
        echo "<option style='font-size:16px' value='$option'>$option</option>";}
    echo "</select><br>";
    echo "<label for='amount'>數量：</label>";
    echo "<input type='text' name='amount' id='amount' required><br>";
    echo "<label for='price'>價格：</label>";
    echo "<input type='text' name='price' id='price' required><br>";
    echo "<input type='submit' value='提交'>";
    echo "</form>";
    echo "<p><a href='manage_user.php'>管理使用者</a></p>";
    echo "<p><a href='change_password.php'>變更密碼</a></p>";
    echo "<p><a href='logout_FGPT.php'>登出</a></p>";

    mysqli_close($link);
}

function display_user_interface($account) {
    // 員工介面，只能讀取訂單列表與新增訂單
    // 連接資料庫顯示資料
    $link = mysqli_connect("localhost", "root", "", "FrugalGPT");
    mysqli_query($link, "SET NAMES utf8");

    // 擷取蔬果類型和總價
    $sql = "SELECT * FROM user_test WHERE account = '$account'";
    $sql1 = "SELECT * FROM user_level WHERE account = '$account' ORDER BY ID DESC LIMIT 1";

    $result = mysqli_query($link, $sql);
    $result1 = mysqli_query($link, $sql1);

    while ($row = mysqli_fetch_assoc($result1)) {
        $user_word = $row['word'];
        $user_grammar = $row['grammar'];
        $user_phrase = $row['phrase'];
        echo "<tr><td>【 單字能力:$user_word </td><td>文法能力:$user_grammar </td><td>片語能力:$user_phrase 】</td>";
    }

    echo "<h2>測驗歷史</h2>";
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>測驗時間</th><th>單字能力</th><th>文法能力</th><th>片語能力</th><th>意見回饋</th></tr>";


    while ($row = mysqli_fetch_assoc($result)) {
        $test_ID = $row['ID'];
        $datetime = $row['datetime'];
        $formattedDatetime =  date('Y-m-d H:i:s', strtotime($datetime));
        $word = $row['word'];
        $grammar = $row['grammar'];
        $phrase = $row['phrase'];
        echo "<tr><td>$test_ID</td><td>$formattedDatetime</td><td>$word</td><td>$grammar</td><td>$phrase</td><td><a href='feedback.php?test_id=$test_ID'>意見回饋</a></td></tr>";
    }   

    echo "</table>";
    echo "<p><a href='test_zone.php'>前往考試區</a></p>";
    echo "<p><a href='triangle.php'>查看目前能力三角圖</a></p>";
    echo "<p><a href='change_password.php'>變更密碼</a></p>";
    echo "<p><a href='delete_account.php'>刪除帳號</a></p>";    
    echo "<p><a href='logout_FGPT.php'>登出</a></p>";

    mysqli_close($link);
}

?>

<html>
<head>
    <meta charset="utf-8">
    <title>FrugalGPT</title>
    <link rel="stylesheet" type="text/css" href="style1.css">
</head>
<body>
    <?php
    if ($role === 'admin') {
        echo "<h1>管理員介面</h1>";
        echo "<p>你好，管理員-$account</p>";
        display_admin_interface($account);
    } else {
        echo "<h1>使用者介面</h1>";
        echo "<p>你好，使用者-$account</p>";
        display_user_interface($account);
    }
    ?>
</body>
</html>