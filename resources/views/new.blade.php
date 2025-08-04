<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product List</title>
    <style>
        .product-card {
            padding: 10px;
            margin: 5px;
            background-color: lightgray;
            border: 1px solid #ccc;
            display: inline-block;
        }
    </style>
    <script>
        async function fetchProducts() {
                const response = await fetch('http://192.168.18.33:8000/Getproducts');
                const products = await response.json();
                const container = document.getElementById('product-container');
                const rowDiv = document.createElement('div');
                products.forEach(product => {
                    const card = document.createElement('div');
                    card.classList.add('product-card');
                    card.textContent = product.name;
                    rowDiv.appendChild(card);
                });
                container.appendChild(rowDiv);
        }
        window.onload = fetchProducts;
    </script>
</head>
<body>
    <div id="product-container"></div>
</body>
</html>
