<?php
require_once '../config/Database.php';
require_once '../models/SalesTransaction.php';

$db = new Database();
$salesTransaction = new SalesTransaction($db);

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'getDetails':
        try {
            $transactionId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            
            if (!$transactionId) {
                throw new Exception("Transaction ID is required");
            }

            $details = $salesTransaction->getTransactionDetails($transactionId);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => array_merge($details['transaction'], ['items' => $details['items']])
            ]);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;

    default:
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid action'
        ]);
        break;
}
?>
