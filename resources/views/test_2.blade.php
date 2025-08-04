<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Responsive Product Grid</title>
  <style>
    body {
      margin: 0;
      padding: 40px;
      font-family: Arial, sans-serif;
    }

    #product-container {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .product-row {
      display: flex;
      gap: 20px;
      flex-wrap: nowrap;
      align-items: stretch;
    }

    .product-row.left {
      justify-content: flex-start;
    }

    .product-row.right {
      justify-content: flex-end;
    }

    .product-card {
      background-color: #f0f0f0;
      padding: 20px;
      text-align: center;
      min-width: 150px;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      flex: 1;
    }
    @media (max-width: 480px) {
      .product-row {
        justify-content: center !important;
        flex-wrap: wrap;
      }

      .product-card {
        min-width: 100%;
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
      renderGrid(products);
    }

    function getItemsPerRow() {
      const width = window.innerWidth;
      if (width < 480) return 1;
      if (width < 768) return 2;
      if (width < 1200) return 3;
      return 5;
    }

    function renderGrid(products) {
      const container = document.getElementById('product-container');
      container.innerHTML = ''; 
      const perRow = getItemsPerRow();

      for (let i = 0; i < products.length; i += perRow) {
        const rowProducts = products.slice(i, i + perRow);
        const rowDiv = document.createElement('div');
        rowDiv.classList.add('product-row');

        if (perRow === 1) {
          rowDiv.style.justifyContent = "center";
        } else {
          rowDiv.classList.add((Math.floor(i / perRow) % 2 === 0) ? 'left' : 'right');
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

    window.addEventListener('resize', () => {
      fetchProducts();
    });

    fetchProducts();
  </script>

</body>
</html>
