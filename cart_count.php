<?php
session_start();
include 'db.php';
$session_id = session_id();
$res = $conn->query("SELECT SUM(quantity) as total FROM cart WHERE session_id = '$session_id'");
$row = $res->fetch_assoc();
echo $row['total'] ?? 0;
?>
<script>
    
document.addEventListener("DOMContentLoaded", function() {
  const cartCount = document.getElementById("cart-count");

  function updateCartCount() {
    fetch('cart_count.php')
      .then(res => res.text())
      .then(count => {
        cartCount.textContent = count;
      });
  }

  document.querySelectorAll(".add-to-cart-btn").forEach(button => {
    button.addEventListener("click", function() {
      const productId = this.dataset.productId;
      fetch('add_to_cart.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: product_id=${productId}
      })
      .then(res => res.text())
      .then(newCount => {
        cartCount.textContent = newCount;
      });
    });
  });

  updateCartCount(); // On page load
});
</script>
