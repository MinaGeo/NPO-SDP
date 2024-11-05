<!DOCTYPE html>
<html lang="en">

<head>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@materializecss/materialize@1.0.0/dist/css/materialize.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo "Shop" ?></title>
    <style>
        /* Style adjustments for shop items */
        .item-detail-label {
            font-weight: bold;
            margin-right: 5px;
        }

        .item-detail-value {
            color: red;
            display: inline;
        }

        /* Styles for dropdown container and dropdowns */
        .dropdown-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .input-field {
            flex: 1;
            margin-right: 10px;
            min-width: 150px;
        }

        /* Make logos smaller */
        .logo {
            width: 20px;
            margin-right: 5px;
            vertical-align: middle;
        }
    </style>
</head>

<body>
    <script src="https://cdn.jsdelivr.net/npm/@materializecss/materialize@1.0.0/dist/js/materialize.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>


    <div class="container">
        <div class="row">
            <div class="col s12">
                <h5>Welcome to <?php echo "Shop" ?>!</h5>
                <h6>Browse our items below:</h6>
            </div>
        </div>

        <!-- Sorting Dropdown -->
        <div class="input-field">
            <select id="itemSort">
                <option value="">Sorting Option</option>
                <option value="name_asc">Sort by Name (Asc)</option>
                <option value="name_desc">Sort by Name (Desc)</option>
                <option value="price_asc">Sort by Price (Low to High)</option>
                <option value="price_desc">Sort by Price (High to Low)</option>
            </select>
            <label>
                <img class="logo" src="../assets/sort.png" alt="Sort Logo" /> Choose Sorting Option
            </label>
        </div>


        <!-- Display Shop Items -->
        <div class="row">
            <?php foreach ($shop_items as $item): ?>
                <div class="col s12 m6 l4">
                    <div class="card">
                        <div class="card-content">
                            <span class="card-title">
                                <h5><?php echo htmlspecialchars($item->name); ?></h5>
                            </span>
                            <p><span class="item-detail-label">Description:</span> <span class="item-detail-value"><?php echo htmlspecialchars($item->description); ?></span></p>
                            <p><span class="item-detail-label">Price:</span> <span class="item-detail-value">$<?php echo htmlspecialchars($item->price); ?></span></p>
                        </div>
                        <div class="card-action">
                            <button 
                            onclick="addToCart(<?php echo htmlspecialchars($item->id); ?>)"
                            class="addToCartBtn btn waves-effect waves-light" type="button">
                                Add to Cart
                            </button>
                            <button
                                onclick="deleteItem(<?php echo htmlspecialchars($item->id); ?>)"
                                class="deleteBtn btn red waves-effect waves-light" type="button"
                                data-item-id="<?php echo $item->id; ?>">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="row">
            <div class="col s12">
                <a href="showAddItem" class="btn waves-effect waves-light green">Add Item</a>
            </div>
        </div>
        <div class="fixed-action-btn">
                <a href="cart"
                class="btn-floating btn-large blue">
                <i class="large material-icons">shopping_cart</i>
                </a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var elems = document.querySelectorAll('select');
            M.FormSelect.init(elems); // Initialize Materialize dropdown
        });

        document.querySelectorAll('.addToCartBtn').forEach(button => {
            button.addEventListener(
                'click',
                () => M.toast({
                    html: 'Item added to cart.',
                    displayLength: 1000,
                    classes: 'rounded blue'
                })
            );
        });

        document.querySelectorAll('.deleteBtn').forEach(button => {
            button.addEventListener(
                'click',
                () => M.toast({
                    html: 'Item Deleted!',
                    displayLength: 1000,
                    classes: 'rounded red'
                })
            );
        });

        function addToCart(itemId){
            $.ajax({
                url: 'addShopItemToCart',
                type: 'POST',   
                data: {
                    addToCart: true,
                    itemId: itemId,
                },
            });
        };

        function deleteItem(itemId) {
            if (confirm('Are you sure you want to delete this item?')) {
                $.ajax({
                    url: 'deleteShopItem',
                    type: 'POST',
                    data: {
                        deleteItem: true,
                        id: itemId,
                    },
                    success: function(response) {
                        // Reload the page after successful deletion
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.error("An error occurred:", error);
                    }
                });
            }
        }
        

        // Handle sorting option change
        document.getElementById('itemSort').addEventListener('change', function() {
            const selectedSort = this.value;
            window.location.href = `?itemSort=${selectedSort}`; // Change itemSort to eventSort
        });
    </script>
</body>

</html>