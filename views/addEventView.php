<!DOCTYPE html>
<html lang="en">

<head>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@materializecss/materialize@1.0.0/dist/css/materialize.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add New Event</title>
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
                <h5>Create a New Event</h5>
            </div>
        </div>

        <form id="eventForm">
            <div class="row">
                <!-- Event Name -->
                <div class="input-field col s12">
                    <input id="eventName" name="name" type="text" required>
                    <label for="eventName">Event Name</label>
                </div>

                <!-- Event Description -->
                <div class="input-field col s12">
                    <textarea id="eventDescription" name="description" class="materialize-textarea" required></textarea>
                    <label for="eventDescription">Description</label>
                </div>

                <!-- Event Location -->
                <div class="input-field col s12">
                    <input id="eventLocation" name="location" type="text" required>
                    <label for="eventLocation">Location</label>
                </div>

                <!-- Event Date -->
                <div class="input-field col s12">
                    <input id="eventDate" name="date" type="text" placeholder="YYYY-MM-DD HH:MM:SS" required>
                    <label for="eventDate">Date</label>
                </div>

                <!-- Event Type -->
                <div class="input-field col s12">
                    <select id="eventType" name="type" required>
                        <option value="" disabled selected>Choose Event Type</option>
                        <option value="Fundraising">Fundraising</option>
                        <option value="Donation">Donation</option>
                        <option value="Awareness">Awareness</option>
                    </select>
                    <label for="eventType">Event Type</label>
                </div>
            </div>

            <div class="row">
                <div class="col s12">
                    <button class="addBtn btn waves-effect waves-light green" type="button" onclick="submitEventForm()">Add Event
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
            const name = document.getElementById('eventName').value;
            const description = document.getElementById('eventDescription').value;
            const location = document.getElementById('eventLocation').value;
            const date = document.getElementById('eventDate').value;
            const type = document.getElementById('eventType').value;

            if (!name || !description || !location || !date || !type) {
                M.toast({ html: 'Please fill in all fields.', classes: 'rounded red' });
                return false; // Return false if any field is empty
            }
            return true; // All fields are filled
        }

        function validateDateFormat() {
            const dateInput = document.getElementById('eventDate').value;
            const dateFormat = /^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/;

            if (!dateFormat.test(dateInput)) {
                M.toast({ html: 'Please enter the date in the format YYYY-MM-DD HH:MM:SS.', classes: 'rounded red' });
                return false;
            }
            return true;
        }

        function submitEventForm() {
            // Validate inputs and date format
            if (!validateInputs() || !validateDateFormat()) return;

            // Gather form data
            const name = document.getElementById('eventName').value;
            const description = document.getElementById('eventDescription').value;
            const location = document.getElementById('eventLocation').value;
            const date = document.getElementById('eventDate').value;
            const type = document.getElementById('eventType').value;

            // Call addEvent function with form data
            addEvent(name, description, location, type, date);
        }

        function addEvent(name, description, location, type, date) {
            $.ajax({
                url: '../Controllers/EventController.php',
                type: 'POST',
                data: {
                    addEvent: true,
                    name: name,
                    description: description,
                    location: location,
                    type: type,
                    date: date,
                },
                success: function (response) {
                    window.location.href = "../Controllers/EventController.php";
                },
                error: function (xhr, status, error) {
                    M.toast({ html: 'Error adding event.', classes: 'rounded red' });
                    console.error("An error occurred:", error);
                }
            });
        }
    </script>
</body>

</html>
