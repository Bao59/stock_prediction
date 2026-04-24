<?php
// 資料庫設定
$db_host = 'localhost';
$db_user = 'stock_user';
$db_pass = 'StrongPassword123!';
$db_name = 'stock_prediction_db';

$chartData = null;
$errorMsg = '';
$ticker = '2330';
$period = '6mo';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticker = trim($_POST['ticker'] ?? '2330');
    $period = $_POST['period'] ?? '6mo';

    // 呼叫背景 Python API
    $ch = curl_init('http://127.0.0.1:5000/api/predict');
    $postData = json_encode(['ticker' => $ticker, 'period' => $period]);
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
    $response = curl_exec($ch);
    
    if(curl_errno($ch)){
        $errorMsg = 'API 連線失敗，請確認 app.py 正在運行。';
    } else {
        $result = json_decode($response, true);
        if (isset($result['error'])) {
            $errorMsg = $result['error'];
        } else {
            $chartData = $result;
            
            // 寫入 MySQL 紀錄
            try {
                $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
                if (!$conn->connect_error) {
                    $stmt = $conn->prepare("INSERT INTO predictions (ticker, predicted_price, query_period) VALUES (?, ?, ?)");
                    $stmt->bind_param("sds", $result['ticker'], $result['predicted_price'], $result['query_period']);
                    $stmt->execute();
                    $stmt->close();
                }
                $conn->close();
            } catch (Exception $e) {
                error_log("DB Error: " . $e->getMessage());
            }
        }
    }
    curl_close($ch);
}

// 載入純 HTML 畫面
require_once 'view.html';