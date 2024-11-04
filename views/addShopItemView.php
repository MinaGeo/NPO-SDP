<!DOCTYPE html>
<html lang="en">

<head>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@materializecss/materialize@1.0.0/dist/css/materialize.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add New Item</title>
    <style>
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
                <h5>Create a New Item</h5>
            </div>
        </div>

        <form id="eventForm">
            <div class="row">
                <!-- Item Name -->
                <div class="input-field col s12">
                    <input id="itemName" name="name" type="text" required>
                    <label for="itemName">Item Name</label>
                </div>

                <!-- Item Description -->
                <div class="input-field col s12">
                    <textarea id="itemDescription" name="description" class="materialize-textarea" required></textarea>
                    <label for="itemDescription">Description</label>
                </div>

                <!-- Item Price -->
                <div class="input-field col s12">
                    <input id="itemPrice" name="price" type="number" required min="0" step="0.01">
                    <label for="itemPrice">Price</label>
                </div>
            </div>

            <div class="row">
                <div class="col s12">
                    <button class="addBtn btn waves-effect waves-light green" type="button" onclick="submitItemForm()">Add Item
                        <i class="material-icons right">send</i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize Materialize components
            M.AutoInit();
        });

        function validateInputs() {
            const name = document.getElementById('itemName').value;
            const description = document.getElementById('itemDescription').value;
            const price = document.getElementById('itemPrice').value;

            if (!name || !description || !price) {
                M.toast({ html: 'Please fill in all fields.', classes: 'rounded red' });
                return false; // Return false if any field is empty
            }
            if (isNaN(price) || price < 0) {
                M.toast({ html: 'Please enter a valid price.', classes: 'rounded red' });
                return false; // Return false if price is not a valid number
            }
            return true; // All fields are filled
        }

        function submitItemForm() {
            // Validate inputs
            if (!validateInputs()) return;

            // Gather form data
            const name = document.getElementById('itemName').value;
            const description = document.getElementById('itemDescription').value;
            const price = document.getElementById('itemPrice').value;

            // Call addItem function with form data
            addItem(name, description, price);
        }

        function addItem(name, description, price) {
            $.ajax({
                url: '../Controllers/ShopController.php',
                type: 'POST',
                data: {
                    addItem: true,
                    name: name,
                    description: description,
                    price: price,
                },
                success: function (response) {
                    window.location.href = "../Controllers/ShopController.php";
                },
                error: function (xhr, status, error) {
                    M.toast({ html: 'Error adding item.', classes: 'rounded red' });
                    console.error("An error occurred:", error);
                }
            });
        }
    </script>
</body>

</html>
