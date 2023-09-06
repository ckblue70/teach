<?php
session_start();
$test_ID = $_GET['test_id'];    

if (!isset($_SESSION['account'])) {
    echo "Session account is not set.";
    exit();
}

$account = $_SESSION['account'];

$dbConnection = mysqli_connect("localhost", "root", "", "FrugalGPT");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 從 POST 請求中獲取星星數和意見
    $score = $_POST["score"];
    $feedback = $_POST["feedback"];

    // 檢查該學生是否已經回饋過該考試的意見
    $query = "SELECT * FROM user_feedback WHERE account = ? AND test_ID = ?";
    $stmt = $dbConnection->prepare($query);
    $stmt->bind_param("is", $account, $test_ID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $feedbackError = "您已經回饋過該考試的意見。";
    } else {
        // 插入回饋數據到數據庫
        $insertQuery = "INSERT INTO user_feedback (account, test_ID, score, feedback, datetime) VALUES (?, ?, ?, ?, NOW())";
        $insertStmt = $dbConnection->prepare($insertQuery);
        $insertStmt->bind_param("isis", $account, $test_ID, $score, $feedback);
        if (!$insertStmt->execute()) {
            echo "Error: " . $insertStmt->error;
        } else {
            $feedbackSuccess = "感謝您的意見回饋！";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>意見回饋</title>
    <link rel="stylesheet" type="text/css" href="style1.css">
</head>
<body>
    <h2>意見回饋</h2>
    
    <?php if (isset($feedbackError)) : ?>
        <p style="color: red;"><?php echo $feedbackError; ?></p>
    <?php endif; ?>

    <?php if (isset($feedbackSuccess)) : ?>
        <p style="color: green;"><?php echo $feedbackSuccess; ?></p>
        <p><a href='index_FGPT.php'>返回主頁</a></p>
    <?php else : ?>
        <form method="post" action="feedback.php?test_id=<?php echo $test_ID; ?>">
            <label>星星數：</label>
            <select name="score">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
            </select>
            <br>
            <label>意見：</label>
            <textarea name="feedback" rows="4" cols="50" maxlength="100" placeholder="最多100字"></textarea>
            <br>
            <input type="submit" value="提交回饋" >
            <p><a href='index_FGPT.php'>回上頁</a></p>
        </form>
    <?php endif; ?>
</body>
</html>
