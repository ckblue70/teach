<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>考試區</title>
<link rel="stylesheet" type="text/css" href="style1.css">
<script>
var countdownTime = 3600; // 60 minutes in seconds

function updateTimerDisplay() {
    var minutes = Math.floor(countdownTime / 60);
    var seconds = countdownTime % 60;
    document.getElementById("timer").innerHTML = "剩餘時間：" + minutes + " 分 " + seconds + " 秒";
}

var timer = setInterval(function() {
    countdownTime--;
    updateTimerDisplay();
    
    if (countdownTime <= 0) {
        clearInterval(timer);
        alert("考試時間已到，您將被退回到首頁。");
        window.location.href = "index.php"; // Replace with the actual URL of your index page
    }
}, 1000); // Update every 1 second
</script>
</head>
<body>
<h1>歡迎來到考試區</h1>
<h3>考試為三十題隨機抽選之單字、文法、片語題</h3>
<h3 style="color:red;">限時60分鐘完成</h3>

<!-- 计时器显示区域 -->
<div id="timer"></div>

<?php
session_start();

if (!isset($_SESSION['account'])) {
    header("Location: login_FGPT.php");
    exit();
}

$account = $_SESSION['account'];

$link = mysqli_connect("localhost", "root", "", "FrugalGPT");
mysqli_query($link, "SET NAMES utf8");

// 执行 SQL 查询以获取随机题目
$query = "(SELECT * FROM test_word ORDER BY RAND() LIMIT 10)
          UNION
          (SELECT * FROM test_grammar ORDER BY RAND() LIMIT 10)
          UNION
          (SELECT * FROM test_phrase ORDER BY RAND() LIMIT 10)";

$result = mysqli_query($link, $query);

$queationID = [];
$questionsAndAnswers = [];
$questionNumber = 1;

// 定義取得表名的函數
function getTableName($questionNumber) {
    if ($questionNumber <= 10) {
        return "test_word";
    } elseif ($questionNumber <= 20) {
        return "test_grammar";
    } else {
        return "test_phrase";
    }
}

// 显示题目
echo "<form method='post' action='process_answer.php'>";
while ($row = mysqli_fetch_assoc($result)) {
    echo "<p>" . $questionNumber . "." . $row['question'] . "</p>";
    echo "<input type='radio' name='q" . $questionNumber . "' value='A'>" . $row['A'] . "<br>";
    echo "<input type='radio' name='q" . $questionNumber . "' value='B'>" . $row['B'] . "<br>";
    echo "<input type='radio' name='q" . $questionNumber . "' value='C'>" . $row['C'] . "<br>";
    echo "<input type='radio' name='q" . $questionNumber . "' value='D'>" . $row['D'] . "<br>";

    // 查询答案
    $questionsAndAnswers[$questionNumber] = $row['answer'];
    $queationID[$questionNumber] = $row['ID'];

    $questionNumber++;  
}
// 序列化关联数组并作为隐藏字段传递给 process_answer.php
echo "<input type='hidden' name='questionsAndAnswers' value='" . htmlspecialchars(serialize($questionsAndAnswers)) . "'>";
echo "<input type='hidden' name='queationID' value='" . htmlspecialchars(serialize($queationID)) . "'>";

// 关闭数据库连接
mysqli_close($link);
?>

<form method='post' action='process_answer.php'>
<!-- 表单内容 -->
<input type='submit' value='Submit'>
</form>

<!-- 退回首页链接 -->
<p><a href='index_FGPT.php'>返回</a></p>
</body>
</html>
