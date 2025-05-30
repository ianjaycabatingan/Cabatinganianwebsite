<?php
require_once '../config/Database.php';
require_once '../models/Product.php';

$db = new Database();
$product = new Product($db);

$productId = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
$productDetails = $product->getProductById($productId);

if (!$productDetails) {
    header("Location: MainDashboard.php");
    exit;
}
?>

<?php include_once '../assets/header.html'; ?>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-body">
                        <h2 class="card-title text-center mb-4">Buy Product</h2>
                        <div class="row">
                            <div class="col-md-6">
                                <h4><?php echo htmlspecialchars($productDetails['product_name']); ?></h4>
                                <p class="text-muted"><?php echo htmlspecialchars($productDetails['product_description']); ?></p>
                                <p><strong>Category:</strong> <?php echo htmlspecialchars($productDetails['category']); ?></p>
                                <p><strong>Price:</strong> ₱<?php echo number_format($productDetails['price'], 2); ?></p>
                                <p>
                                    <strong>Stock:</strong> 
                                    <span class="badge <?php echo $productDetails['stock_quantity'] < 10 ? 'bg-danger' : 'bg-success'; ?>">
                                        <?php echo $productDetails['stock_quantity']; ?>
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <form action="../controllers/ProductController.php" method="GET">
                                    <input type="hidden" name="action" value="buy">
                                    <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                                    
                                    <div class="mb-3">
                                        <label for="quantity" class="form-label">Quantity</label>
                                        <input type="number" class="form-control" id="quantity" name="quantity" 
                                               value="1" min="1" max="<?php echo $productDetails['stock_quantity']; ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label for="payment_method" class="form-label">Payment Method</label>
                                        <select class="form-select" id="payment_method" name="payment_method">
                                            <option value="cash">Cash</option>
                                            <option value="gcash">GCash</option>
                                            <option value="paymaya">PayMaya</option>
                                        </select>
                                    </div>

                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary" 
                                                <?php echo $productDetails['stock_quantity'] < 1 ? 'disabled' : ''; ?>>
                                            Confirm Purchase
                                        </button>
                                        <a href="MainDashboard.php" class="btn btn-secondary ms-2">Cancel</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><?php include_once '../assets/footer.html'; ?>
