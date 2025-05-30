<?php
require_once '../config/Database.php';
require_once '../models/Product.php';
require_once '../models/SalesTransaction.php';

$db = new Database();
$product = new Product($db);
$salesTransaction = new SalesTransaction($db);

$action = $_GET['action'] ?? '';

switch ($action) {    
    
    case 'buy':
        if (!isset($_GET['quantity'])) {
            // If quantity not set, redirect to buy view
            header("Location: ../views/buy.php?product_id=" . $_GET['product_id']);
            exit;
        }
          $productId = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
        $quantity = isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1;
        $paymentMethod = isset($_GET['payment_method']) ? $_GET['payment_method'] : 'cash';
        
        try {
            // Get product details
            $productDetails = $product->getProductById($productId);
            
            if (!$productDetails) {
                throw new Exception("Product not found");
            }

            if ($productDetails['stock_quantity'] <= 0) {
                throw new Exception("Failed to buy product: Product out of stock");
            }
            
            if ($productDetails['stock_quantity'] < $quantity) {
                throw new Exception("Not enough stock available");
            }

            // Process the sale
            $items = [[
                'product_id' => $productId,
                'quantity' => 1,
                'unit_price' => $productDetails['price']
            ]];            // Add to cart instead of direct purchase
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            $_SESSION['cart'][] = [
                'product_id' => $productId,
                'product_name' => $productDetails['product_name'],
                'price' => $productDetails['price'],
                'quantity' => $quantity
            ];

            // Redirect to checkout
            header("Location: ../views/checkout.php");
            exit;
        } catch (Exception $e) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;    case 'viewCart':
        header("Location: ../views/cart.php");
        exit;
        break;

    case 'addToCart':
        $productId = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
        
        try {
            // Start session if not already started
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Initialize cart if doesn't exist
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            // Get product details
            $productDetails = $product->getProductById($productId);
            
            if (!$productDetails) {
                throw new Exception("Product not found");
            }

            // Check if product exists in cart
            $found = false;
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['product_id'] === $productId) {
                    if ($item['quantity'] + 1 > $productDetails['stock_quantity']) {
                        throw new Exception("Not enough stock available");
                    }
                    $item['quantity']++;
                    $found = true;
                    break;
                }
            }

            // If product not in cart, add it
            if (!$found) {
                if ($productDetails['stock_quantity'] < 1) {
                    throw new Exception("Product out of stock");
                }
                $_SESSION['cart'][] = [
                    'product_id' => $productId,
                    'product_name' => $productDetails['product_name'],
                    'price' => $productDetails['price'],
                    'quantity' => 1
                ];
            }

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Product added to cart successfully',
                'cart_count' => count($_SESSION['cart'])
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
        
    case 'updateCart':
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $index = isset($_GET['index']) ? (int)$_GET['index'] : -1;
        $change = isset($_GET['change']) ? (int)$_GET['change'] : 0;
        
        try {
            if (!isset($_SESSION['cart'][$index])) {
                throw new Exception("Item not found in cart");
            }

            $newQuantity = $_SESSION['cart'][$index]['quantity'] + $change;
            
            if ($newQuantity < 1) {
                throw new Exception("Quantity cannot be less than 1");
            }

            // Check stock availability
            $productDetails = $product->getProductById($_SESSION['cart'][$index]['product_id']);
            if ($newQuantity > $productDetails['stock_quantity']) {
                throw new Exception("Not enough stock available");
            }

            $_SESSION['cart'][$index]['quantity'] = $newQuantity;

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Cart updated successfully'
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

    case 'removeFromCart':
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $index = isset($_GET['index']) ? (int)$_GET['index'] : -1;
        
        try {
            if (!isset($_SESSION['cart'][$index])) {
                throw new Exception("Item not found in cart");
            }

            array_splice($_SESSION['cart'], $index, 1);

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Item removed from cart'
            ]);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;    case 'clearCart':
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        try {
            $_SESSION['cart'] = [];
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Cart cleared successfully'
            ]);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;    case 'processCheckout':
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: ../views/checkout.php");
            exit;
        }

        try {            require_once '../models/Customer.php';
            $customerModel = new Customer($db);

            // Create customer record
            $customerId = $customerModel->createCustomer($_POST['customer_name']);
            
            // Calculate total and prepare items
            $total = 0;
            $items = [];
            foreach ($_SESSION['cart'] as $item) {
                $total += $item['price'] * $item['quantity'];
                $items[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price']
                ];
            }

            // Process the sale transaction
            $result = $salesTransaction->processSaleTransaction(
                $customerId,
                $total,
                $_POST['payment_method'],
                0, // no discount
                0, // no tax
                $_POST['customer_name'],
                $items
            );            // Clear the cart after successful purchase
            $_SESSION['cart'] = [];
            
            // Set success message
            $_SESSION['success'] = 'Order placed successfully!';

            // Redirect to dashboard
            header("Location: ../views/MainDashboard.php");
            exit;

        } catch (Exception $e) {
            $_SESSION['checkout_error'] = $e->getMessage();
            header("Location: ../views/checkout.php");
            exit;
        }
        break;

    case 'checkout':
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['cart'])) {
            header("Location: ../views/cart.php");
            exit;
        }

        header("Location: ../views/checkout.php");
        exit;
        break;

    case 'addProduct':
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $name = $_POST['product_name'] ?? '';
            $description = $_POST['description'] ?? '';
            $category = $_POST['category'] ?? '';
            $price = (float)($_POST['price'] ?? 0);
            $stock = (int)($_POST['stock'] ?? 0);
            $cost_price = (float)$_POST['cost_price'] ?? 0;
            $expiry_date = !empty($_POST['expiry_date']) ? $_POST['expiry_date'] : null;

            // Validate input
            if (empty($name) || empty($category) || $price <= 0 || $stock < 0 || $cost_price <= 0) {
                throw new Exception('Please fill in all required fields with valid values');
            }

            $result = $product->addProduct($name, $description, $category, $price, $stock, $cost_price, $expiry_date);
            
            $_SESSION['success'] = 'Product added successfully';
            header("Location: ../views/admin/products.php");
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: ../views/admin/add_product.php");
            exit;
        }
        break;
        
    case 'updateProduct':
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $id = (int)($_POST['product_id'] ?? 0);
            $name = $_POST['product_name'] ?? '';
            $description = $_POST['description'] ?? '';
            $category = $_POST['category'] ?? '';
            $price = (float)($_POST['price'] ?? 0);
            $stock = (int)($_POST['stock'] ?? 0);
            $cost_price = (float)($_POST['cost_price'] ?? 0);
            $expiry_date = !empty($_POST['expiry_date']) ? $_POST['expiry_date'] : null;

            // Validate input
            if ($id <= 0 || empty($name) || empty($category) || $price <= 0 || $stock < 0 || $cost_price <= 0) {
                throw new Exception('Please fill in all required fields with valid values');
            }

            $result = $product->updateProduct($id, $name, $description, $category, $price, $stock, $cost_price, $expiry_date);
            
            $_SESSION['success'] = 'Product updated successfully';
            header("Location: ../views/admin/products.php");
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: ../views/admin/edit_product.php?id=" . $id);
            exit;
        }
        break;

    case 'deleteProduct':
        try {
            $id = (int)($_GET['id'] ?? 0);
            if ($id <= 0) {
                throw new Exception('Invalid product ID');
            }

            $result = $product->deleteProduct($id);
            
            $_SESSION['success'] = 'Product deleted successfully';
            header("Location: ../views/admin/products.php");
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: ../views/admin/products.php");
            exit;
        }
        break;

    case 'getProduct':
        try {
            header('Content-Type: application/json');
            
            $id = (int)($_GET['id'] ?? 0);
            if ($id <= 0) {
                throw new Exception('Invalid product ID');
            }

            $result = $product->getProductById($id);
            if (!$result) {
                throw new Exception('Product not found');
            }

            echo json_encode([
                'success' => true,
                'data' => $result
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;
}