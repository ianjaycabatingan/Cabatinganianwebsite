<?php
require_once '../config/Database.php';

$db = new Database();
$connection = $db->getConnection();

try {
    $stmt = $db->query("CALL GetAllProducts()");
    $products = $stmt->fetchAll();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>

<?php include_once '../assets/header.html'; ?>
<link rel="stylesheet" href="../assets/store_front.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<div class="store-header">
    <h1>Welcome to Sari-Sari Store</h1>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php 
        echo $_SESSION['success'];
        unset($_SESSION['success']);
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="container">
    <div class="filters-section">
        <div class="search-box">
            <input type="text" id="searchProducts" placeholder="Search products..." onkeyup="filterProducts()">
        </div>
        <div class="category-filters" id="categoryFilters">
            <span class="category-filter active" data-category="all">All</span>
            <!-- Categories will be populated dynamically -->
        </div>
    </div>    <div class="product-grid">
                            <?php foreach ($products as $product): ?>
                            <div class="product-card" data-category="<?php echo htmlspecialchars($product['category']); ?>">
                                <div class="product-image">
                                    <i class="fas fa-box"></i>
                                </div>
                                <div class="product-info">
                                    <div class="product-category"><?php echo htmlspecialchars($product['category']); ?></div>
                                    <div class="product-name"><?php echo htmlspecialchars($product['product_name']); ?></div>
                                    <div class="product-description"><?php echo htmlspecialchars($product['product_description']); ?></div>
                                    <div class="product-price">₱<?php echo number_format($product['price'], 2); ?></div>
                                    <div class="product-stock">
                                        <?php if ($product['stock_quantity'] <= 0): ?>
                                            <span class="stock-badge out-stock">Out of Stock</span>
                                        <?php elseif ($product['stock_quantity'] < 10): ?>
                                            <span class="stock-badge low-stock">Low Stock: <?php echo $product['stock_quantity']; ?></span>
                                        <?php else: ?>
                                            <span class="stock-badge in-stock">In Stock: <?php echo $product['stock_quantity']; ?></span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <?php if ($product['stock_quantity'] > 0): ?>
                                        <div class="product-actions">
                                            <button class="btn-buy" onclick="buyProduct(<?php echo $product['product_id']; ?>)">
                                                <i class="fas fa-shopping-bag"></i> Buy Now
                                            </button>
                                            <button class="btn-cart" onclick="addToCart(<?php echo $product['product_id']; ?>)">
                                                <i class="fas fa-cart-plus"></i> Add to Cart
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>                            <?php endforeach; ?>
    </div>
</div>

<!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>    <script>
        // Initialize category filters
        document.addEventListener('DOMContentLoaded', function() {
            const categories = new Set();
            document.querySelectorAll('.product-card').forEach(card => {
                categories.add(card.dataset.category);
            });
            
            const categoryFilters = document.getElementById('categoryFilters');
            categories.forEach(category => {
                const filter = document.createElement('span');
                filter.className = 'category-filter';
                filter.textContent = category;
                filter.dataset.category = category;
                filter.onclick = () => filterByCategory(category);
                categoryFilters.appendChild(filter);
            });
        });

        function filterByCategory(category) {
            document.querySelectorAll('.category-filter').forEach(filter => {
                filter.classList.remove('active');
                if (filter.dataset.category === category) {
                    filter.classList.add('active');
                }
            });

            document.querySelectorAll('.product-card').forEach(card => {
                if (category === 'all' || card.dataset.category === category) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function filterProducts() {
            const searchText = document.getElementById('searchProducts').value.toLowerCase();
            document.querySelectorAll('.product-card').forEach(card => {
                const productName = card.querySelector('.product-name').textContent.toLowerCase();
                const productDescription = card.querySelector('.product-description').textContent.toLowerCase();
                if (productName.includes(searchText) || productDescription.includes(searchText)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function buyProduct(productId) {
            window.location.href = `buy.php?product_id=${productId}`;
        }function addToCart(productId) {
            fetch(`../controllers/ProductController.php?action=addToCart&product_id=${productId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Just show a brief notification, stay on same page
                        alert('Product added to cart successfully!');
                        // Refresh the page to update the cart count in header
                        location.reload();
                    } else {
                        alert('Failed to add product to cart: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while adding to cart');
                });
        }</script>
<?php include_once '../assets/footer.html'; ?>
