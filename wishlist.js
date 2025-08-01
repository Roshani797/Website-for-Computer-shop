function updateWishlistCount() {
    fetch('wishlist.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=count'
    })
    .then(res => res.text())
    .then(count => {
        document.querySelector('#wishlist-count').textContent = count;
    });
}

document.addEventListener('DOMContentLoaded', () => {
    updateWishlistCount();

    document.querySelectorAll('.add-to-wishlist-btn').forEach(button => {
        button.addEventListener('click', () => {
            const productId = button.dataset.productId;
            const name = button.dataset.productName;
            const price = button.dataset.productPrice;
            const image = button.dataset.productImage;

            const data = new URLSearchParams();
            data.append('action', 'add');
            data.append('product_id', productId);
            data.append('product_name', name);
            data.append('product_price', price);
            data.append('product_image', image);

            fetch('wishlist.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: data.toString()
            })
            .then(res => res.text())
            .then(count => {
                document.querySelector('#wishlist-count').textContent = count;
            });
        });
    });
});
