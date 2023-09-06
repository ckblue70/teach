<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>結果發表</title>
<link rel="stylesheet" type="text/css" href="style1.css">
</head>
<body>
    
<?php
session_start();

if (!isset($_SESSION['account'])) {
    header("Location: login_FGPT.php");
    exit();
}

$account = $_SESSION['account'];

$link = mysqli_connect("localhost", "root", "", "FrugalGPT");
mysqli_query($link, "SET NAMES utf8");

$wordScore = 0;
$grammarScore = 0;
$phraseScore = 0;

// 获取序列化的数据并反序列化
$serializedData = $_POST['questionsAndAnswers'];
$questionsAndAnswers = unserialize(htmlspecialchars_decode($serializedData));

$serializedQueationID = $_POST['queationID'];
$queationID = unserialize(htmlspecialchars_decode($serializedQueationID));

// 获取用户提交的答案并计算得分
for ($i = 1; $i <= 30; $i++) {
    if (isset($_POST['q' . $i])) {
        $userAnswer = $_POST['q' . $i]; // 获取用户提交的答案
        $correctAnswerIndex = $questionsAndAnswers[$i]; // 获取问题对应的答案索引（0、1、2、3）
        $questionIDIndex = $queationID[$i];

        // 轉換索引為對應的選項 'A'、'B'、'C'、'D'
        $correctAnswer = ['A', 'B', 'C', 'D'][$correctAnswerIndex];

        echo "<tr>";
        echo "<td>$i.</td>";
        echo "<td> 選擇答案:$userAnswer</td>";
        echo "<td> 正確答案:$correctAnswer</td><br/>";
        echo "</tr>";

        if ($userAnswer === $correctAnswer) {
            if ($i <= 10) {
                $wordScore++;
                $insertQuery = "INSERT INTO test_word_fb (account, queation_ID, correct, choice) VALUES ('$account', '$questionIDIndex', '1', '$userAnswer')";
                mysqli_query($link, $insertQuery);
            } elseif ($i <= 20) {
                $grammarScore++;
                $insertQuery = "INSERT INTO test_grammar_fb (account, queation_ID, correct, choice) VALUES ('$account', '$questionIDIndex', '1', '$userAnswer')";
                mysqli_query($link, $insertQuery);
            } else {
                $phraseScore++;
                $insertQuery = "INSERT INTO test_phrase_fb (account, queation_ID, correct, choice) VALUES ('$account', '$questionIDIndex', '1', '$userAnswer')";
                mysqli_query($link, $insertQuery);
            }
        }
        else {
            if ($i <= 10) {
                $insertQuery = "INSERT INTO test_word_fb (account, queation_ID, correct, choice) VALUES ('$account', '$questionIDIndex', '0', '$userAnswer')";
                mysqli_query($link, $insertQuery);
            } elseif ($i <= 20) {
                $insertQuery = "INSERT INTO test_grammar_fb (account, queation_ID, correct, choice) VALUES ('$account', '$questionIDIndex', '0', '$userAnswer')";
                mysqli_query($link, $insertQuery);
            } else {
                $insertQuery = "INSERT INTO test_phrase_fb (account, queation_ID, correct, choice) VALUES ('$account', '$questionIDIndex', '0', '$userAnswer')";
                mysqli_query($link, $insertQuery);
            }
        }
    }
}

$wordScore = intval($wordScore / 2);
$grammarScore = intval($grammarScore / 2);
$phraseScore = intval($phraseScore / 2);

// 获取当前时间
$dateTime = date('Y-m-d H:i:s');

// 将考试结果存储到 user_test 表中
$insertQuery = "INSERT INTO user_test (account, datetime, word, grammar, phrase) VALUES ('$account', '$dateTime', '$wordScore', '$grammarScore', '$phraseScore')";
mysqli_query($link, $insertQuery);

// 显示考试结果
echo "<h2>考試結果</h2>";
echo "<p>Word 得分：" . $wordScore . "</p>";
echo "<p>Grammar 得分：" . $grammarScore . "</p>";
echo "<p>Phrase 得分：" . $phraseScore . "</p>";
echo "<p><a href='index_FGPT.php'>返回主頁</a></p>";
// 关闭数据库连接
mysqli_close($link);
?>
