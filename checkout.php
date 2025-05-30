<?php
session_start();

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

$cart = $_SESSION['cart'];
$total = 0;
foreach ($cart as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<?php include_once '../assets/header.html'; ?>
<link rel="stylesheet" href="../assets/cart_checkout.css">

<div class="checkout-container">
    <div class="cart-breadcrumb">
        <span>Shopping Cart</span>
        <span class="separator">/</span>
        <span class="active">Checkout</span>
    </div>
    <h2>Checkout</h2>
                  <form action="../controllers/ProductController.php?action=processCheckout" method="POST" class="needs-validation" novalidate>
                    
                    <div class="form-section">
                        <h5>Order Summary</h5>
                        <div class="table-responsive">
                            <table class="checkout-summary">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cart as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td>₱<?php echo number_format($item['price'], 2); ?></td>
                                        <td>₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                    </tr>
                                    <?php endforeach; ?>                                    <tr class="total-row">
                                        <td colspan="3" style="text-align: right"><strong>Total Amount:</strong></td>
                                        <td><strong class="cart-item-price">₱<?php echo number_format($total, 2); ?></strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="form-section">
                        <h5>Payment Method</h5>
                        <div class="payment-methods">
                            <div class="payment-method-option" onclick="selectPayment('cash')">
                                <i class="fas fa-money-bill"></i>
                                <div>Cash</div>
                                <input type="radio" name="payment_method" value="cash" required hidden>
                            </div>
                            <div class="payment-method-option" onclick="selectPayment('gcash')">
                                <i class="fas fa-mobile-alt"></i>
                                <div>GCash</div>
                                <input type="radio" name="payment_method" value="gcash" required hidden>
                            </div>
                            <div class="payment-method-option" onclick="selectPayment('paymaya')">
                                <i class="fas fa-credit-card"></i>
                                <div>PayMaya</div>
                                <input type="radio" name="payment_method" value="paymaya" required hidden>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h5>Customer Information</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-section">
                                    <label for="customer_name">Name</label>
                                    <input type="text" id="customer_name" name="customer_name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-section">
                                    <label for="contact_number">Contact Number</label>
                                    <input type="tel" id="contact_number" name="contact_number" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="cart.php" class="continue-shopping">Back to Cart</a>
                        <button type="submit" class="checkout-btn">Place Order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Payment method selection
    function selectPayment(method) {
        const options = document.querySelectorAll('.payment-method-option');
        options.forEach(option => {
            option.classList.remove('selected');
            option.querySelector('input').checked = false;
        });

        const selectedOption = document.querySelector(`.payment-method-option input[value="${method}"]`).parentElement;
        selectedOption.classList.add('selected');
        selectedOption.querySelector('input').checked = true;
    }

    // Form validation
    (function () {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()
</script>

<?php include_once '../assets/footer.html'; ?>
