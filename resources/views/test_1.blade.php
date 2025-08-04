{{-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        #product-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .product-row {
            display: grid;
            gap: 20px;
            grid-auto-flow: column;
            justify-content: start;
        }
        .product-row.right {
            justify-content: end;
        }

        .product-card {
            background-color: #f0f0f0;
            padding: 20px;
            text-align: center;
            min-width: 150px;
            flex-shrink: 0;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 1200px) {
            .product-row {
                grid-auto-flow: initial;
                grid-template-columns: repeat(3, 1fr);
                justify-content: start !important;
            }

            .product-card {
                background-color: #f0f0f0;
                padding: 10px;
                text-align: center;
                min-width: 50px;
                flex-shrink: 0;
                border-radius: 8px;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            }

        }

        @media (max-width: 768px) {
            .product-row {
                grid-template-columns: repeat(2, 1fr);
            }

            .product-card {
                background-color: #f0f0f0;
                padding: 10px;
                text-align: center;
                min-width: 50px;
                flex-shrink: 0;
                border-radius: 8px;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            }
        }
        @media (min-width: 480px) and (max-width: 767px) {
            .product-row {
                grid-template-columns: 1fr;
            }
            .product-card {
                background-color: #f0f0f0;
                padding: 10px;
                text-align: center;
                min-width: 50px;
                flex-shrink: 0;
                border-radius: 8px;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            }
        }
    </style>
</head>
<body>
    <div id="product-container"></div>
    <script>
        async function fetchProducts() {
            const response = await fetch('http://192.168.18.33:8000/Getproducts');
            const products = await response.json();
            const container = document.getElementById('product-container');
            const perRow = 5;
            for (let i = 0; i < products.length; i += perRow) {
                const rowProducts = products.slice(i, i + perRow);
                const rowDiv = document.createElement('div');
                rowDiv.classList.add('product-row');
                if ((i / perRow) % 2 === 1) {
                    rowDiv.classList.add('right');
                }
                rowProducts.forEach(product => {
                    const card = document.createElement('div');
                    card.classList.add('product-card');
                    card.textContent = product.name;
                    rowDiv.appendChild(card);
                });
                container.appendChild(rowDiv);
            }
        }
        fetchProducts();
    </script>
</body>
</html> --}}


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        #product-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .product-row {
            display: grid;
            gap: 20px;
            grid-template-columns: repeat(5, minmax(150px, 1fr));
            justify-content: start;
        }

        .product-row.right {
            justify-content: end;
        }

        .product-row.right::before {
            content: '';
            grid-column: 1;
        }

        .product-row:not(.right)::after {
            content: '';
            grid-column: -1;
        }

        .product-card {
            background-color: #f0f0f0;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 1200px) {
            .product-row {
                grid-template-columns: repeat(4, minmax(120px, 1fr));
                justify-content: start !important;
            }

            .product-row.right::before,
            .product-row:not(.right)::after {
                content: none;
            }
        }

        @media (max-width: 992px) {
            .product-row {
                grid-template-columns: repeat(3, minmax(100px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .product-row {
                grid-template-columns: repeat(2, minmax(100px, 1fr));
            }
        }

        @media (max-width: 480px) {
            .product-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div id="product-container"></div>
    <script>
        async function fetchProducts() {
            const response = await fetch('http://192.168.18.33:8000/Getproducts');
            const products = await response.json();
            const container = document.getElementById('product-container');
            const maxPerRow = 5;
            let currentIndex = 0;

            while (currentIndex < products.length) {
                const rowDiv = document.createElement('div');
                rowDiv.classList.add('product-row');
                if (Math.floor(currentIndex / maxPerRow) % 2 === 1) {
                    rowDiv.classList.add('right');
                }

                const productsInRow = products.slice(currentIndex, currentIndex + maxPerRow);
                productsInRow.forEach(product => {
                    const card = document.createElement('div');
                    card.classList.add('product-card');
                    card.textContent = product.name;
                    rowDiv.appendChild(card);
                });

                container.appendChild(rowDiv);
                currentIndex += maxPerRow;
            }
        }

        fetchProducts();
    </script>
</body>
</html>