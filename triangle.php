<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>能力三角圖</title>
    <link rel="stylesheet" type="text/css" href="style1.css">
    <style>
        /* Style for the container of the chart */
        .chart-container {
            width: 40%; /* Adjust the width as needed */
            margin: 0 auto; /* Center the chart */
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.5.1/chart.min.js"></script>
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

    $sql = "SELECT * FROM user_level WHERE account = '$account'";
    $result = mysqli_query($link, $sql);

    if ($row = mysqli_fetch_assoc($result)) {
        $word = intval($row['word']); // Convert to integer
        $grammar = intval($row['grammar']); // Convert to integer
        $phrase = intval($row['phrase']); // Convert to integer
    }

    mysqli_close($link);
    ?>

    <h1>能力三角圖</h1>
    <div class="chart-container">
        <canvas id="triangleChart" width="160" height="160"></canvas>
    </div>

    <script>
        var ctx = document.getElementById('triangleChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'radar',
            data: {
                labels: ['Word', 'Grammar', 'Phrase'],
                datasets: [{
                    label: 'Your Abilities',
                    data: [<?php echo $word; ?>, <?php echo $grammar; ?>, <?php echo $phrase; ?>],
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    pointRadius: 5,
                    pointBackgroundColor: 'rgba(75, 192, 192, 1)'
                }]
            },
            options: {
    scales: {
        r: {
            beginAtZero: true,
            max: 5,
            stepSize: 1,
            ticks: {
                callback: function(value, index, values) {
                    // Only show integer values, hide decimal values
                    if (Number.isInteger(value)) {
                        return value;
                    }
                    return ''; // Hide decimal values
                },
                color: 'rgba(0, 0, 0, 1)'
            },
            grid: {
                color: 'rgba(0, 0, 0, 0.1)'
            },
            pointLabels: {
                color: 'rgba(0, 0, 0, 0.8)'
            }
        }
    }
}
        });
    </script>
    <p><a href='index_FGPT.php'>返回</a></p>
</body>
</html>
