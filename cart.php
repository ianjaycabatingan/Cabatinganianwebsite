<?php
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cart = $_SESSION['cart'];
$total = 0;
foreach ($cart as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<?php include_once '../assets/header.html'; ?>
<link rel="stylesheet" href="../assets/cart_checkout.css">
        <div class="cart-container">
            <div class="cart-breadcrumb">
                <span class="active">Shopping Cart</span>
                <span class="separator">/</span>
                <span>Checkout</span>
            </div>
            <h2>Shopping Cart</h2>
                        
                        <?php if (empty($cart)): ?>
                            <div class="empty-cart">
                                <i class="fas fa-shopping-basket fa-3x"></i>
                                <h4>Your Shopping Cart is Empty</h4>
                                <p>Looks like you haven't added any items to your cart yet.</p>
                                <a href="MainDashboard.php" class="continue-shopping">
                                    <i class="fas fa-arrow-left"></i> Continue Shopping
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="cart-table">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th>Total</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($cart as $index => $item): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                                <td>₱<?php echo number_format($item['price'], 2); ?></td>
                                                <td>
                                                    <div class="quantity-control">
                                                        <button class="quantity-btn" type="button" 
                                                                onclick="updateQuantity(<?php echo $index; ?>, -1)">-</button>
                                                        <input type="text" class="quantity-input" 
                                                               value="<?php echo $item['quantity']; ?>" readonly>
                                                        <button class="quantity-btn" type="button" 
                                                                onclick="updateQuantity(<?php echo $index; ?>, 1)">+</button>
                                                    </div>
                                                </td>
                                                <td>₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                                <td>
                                                    <button class="remove-btn" 
                                                            onclick="removeItem(<?php echo $index; ?>)">Remove</button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>                                    <tfoot>
                                        <tr class="total-row">
                                            <td colspan="3" style="text-align: right"><strong>Total Amount:</strong></td>
                                            <td><strong class="cart-item-price">₱<?php echo number_format($total, 2); ?></strong></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>                            <div class="cart-actions">
                                <div>
                                    <a href="MainDashboard.php" class="continue-shopping">Continue Shopping</a>
                                    <button onclick="clearCart()" class="remove-btn">Clear Cart</button>
                                </div>
                                <form action="../controllers/ProductController.php" method="GET">
                                    <input type="hidden" name="action" value="checkout">
                                    <button type="submit" class="checkout-btn">Proceed to Checkout</button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>    <script>
    function updateQuantity(index, change) {
        fetch(`../controllers/ProductController.php?action=updateCart&index=${index}&change=${change}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the cart');
            });
    }

    function removeItem(index) {
        if (confirm('Are you sure you want to remove this item?')) {
            fetch(`../controllers/ProductController.php?action=removeFromCart&index=${index}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while removing the item');
                });
        }
    }

    function clearCart() {
        if (confirm('Are you sure you want to clear your cart?')) {
            fetch('../controllers/ProductController.php?action=clearCart')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while clearing the cart');
                });
        }
    }
    </script>
<?php include_once '../assets/footer.html'; ?>
