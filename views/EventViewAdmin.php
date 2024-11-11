<!DOCTYPE html>
<html lang="en">

<head>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@materializecss/materialize@1.0.0/dist/css/materialize.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo "Events" ?> Admin</title>
    <link rel="stylesheet" href="../assets/eventStyle.css">
</head>

<body>
    <script src="https://cdn.jsdelivr.net/npm/@materializecss/materialize@1.0.0/dist/js/materialize.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <div class="container">
        <div class="row">
            <div class="col s12">
                <h5 style="text-align: center;">Welcome to <?php echo "Events" ?> Admin!</h5>
                <h6 style="text-align: center;">What would you like to do?</h6>
            </div>
        </div>

         <!-- Dropdowns in a flex container -->
         <div class="dropdown-container">
            <!-- Sorting Dropdown -->
            <div class="input-field">
                <select id="sortSelect">
                    <option value="">Sorting Option</option>
                    <option value="name_asc">Sort by Name (Asc)</option>
                    <option value="name_desc">Sort by Name (Desc)</option>
                    <option value="date_asc">Sort by Date (Closest to farthest)</option>
                    <option value="date_desc">Sort by Date (Farthest to closest)</option>
                </select>
                <label>
                    <img class="logo" src="../assets/sort.png" alt="Sort Logo" /> Choose Sorting Option
                </label>
            </div>

            <!-- Filtering Dropdown -->
            <div class="input-field">
                <select id="filterSelect">
                    <option value="event_type" <?php echo (isset($_GET['eventFilter']) && $_GET['eventFilter'] == 'event_type') ? 'selected' : ''; ?>>Event Type</option>
                    <option value="location" <?php echo (isset($_GET['eventFilter']) && $_GET['eventFilter'] == 'location') ? 'selected' : ''; ?>>Location</option>
                </select>
                <label>
                    <img class="logo" src="../assets/filter.png" alt="Filter Logo" /> Choose Filtering Option
                </label>
            </div>

            <!-- Event Type Dropdown -->
            <div class="input-field" id="eventTypeSelectContainer">
                <select id="eventTypeSelect">
                    <option value="">All Events</option>
                    <option value="Fundraising" <?php echo (isset($_GET['eventType']) && $_GET['eventType'] == 'Fundraising') ? 'selected' : ''; ?>>Fundraising</option>
                    <option value="Donation" <?php echo (isset($_GET['eventType']) && $_GET['eventType'] == 'Donation') ? 'selected' : ''; ?>>Donation</option>
                    <option value="Awareness" <?php echo (isset($_GET['eventType']) && $_GET['eventType'] == 'Awareness') ? 'selected' : ''; ?>>Awareness</option>
                </select>
                <label>
                    <img class="logo" src="../assets/filter_type.png" alt="Event Type Logo" /> Choose Event Type
                </label>
            </div>

            <!-- Location Dropdown (Hidden by default) -->
            <div class="input-field" id="locationSelectContainer" style="display:none;">
                <select id="locationSelect">
                    <option value="">Choose Location</option>
                    <?php
                        $governorates = ['Cairo', 'Alexandria', 'Giza', 'Port Said', 'Suez', 'Damietta', 'Mansoura', 'Tanta', 'Ismailia', 'Minya', 'Luxor', 'Aswan', 'Asyut', 'Qena', 'Shubra', 'Beni Suef', 'Fayoum', 'Kafr El Sheikh', 'Dakahlia', 'Sharkia', 'Monufia', 'Beheira', 'Matrouh', 'Red Sea', 'North Sinai', 'South Sinai'];
                        foreach ($governorates as $governorate) {
                            $selected = (isset($_GET['location']) && $_GET['location'] == $governorate) ? 'selected' : '';
                            echo "<option value='$governorate' $selected>$governorate</option>";
                        }
                    ?>
                </select>
                <label>Choose Location</label>
            </div>
        </div>

        <!-- Event Cards -->
        <div class="row">
            <?php foreach ($events as $event): ?>
                <div class="col s12 m6 l4">
                    <div class="card">
                        <div class="card-content">
                            <span class="card-title">
                                <h5><?php echo htmlspecialchars($event->name); ?></h5>
                            </span>
                            <p><span class="event-detail-label">Description:</span> <span class="event-detail-value"><?php echo htmlspecialchars($event->description); ?></span></p>
                            <p><span class="event-detail-label">Location:</span> <span class="event-detail-value"><?php echo htmlspecialchars($event->location); ?></span></p>
                            <p><span class="event-detail-label">Event Type:</span> <span class="event-detail-value"><?php echo htmlspecialchars($event->type); ?></span></p>
                            <p><span class="event-detail-label">Date:</span> <span class="event-detail-value"><?php echo htmlspecialchars($event->date); ?></span></p>
                        </div>
                        <div class="card-action">
                            <button
                                onclick="deleteEvent(<?php echo htmlspecialchars($event->id); ?>)"
                                class="deleteBtn btn waves-effect waves-light" type="button"
                                data-event-id="<?php echo $event->id; ?>">
                                <i class="material-icons left">delete</i>Delete
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Add Event Button -->
        <div class="row">
            <div class="col s12">
                <a href="addEventView" class="btn waves-effect waves-light green">
                    <i class="material-icons left">add</i>Add Event
                </a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var elems = document.querySelectorAll('select');
            M.FormSelect.init(elems); // Initialize Materialize dropdown

            // Initial setup based on selected filter
            updateFilterOptions(document.getElementById('filterSelect').value);
        });

        // Handle filtering option change
        document.getElementById('filterSelect').addEventListener('change', function() {
            const selectedFilter = this.value;
            updateFilterOptions(selectedFilter);
            const selectedEventType = document.getElementById('eventTypeSelect').value;
            const selectedLocation = document.getElementById('locationSelect').value;
            window.location.href = `?eventFilter=${selectedFilter}&eventType=${selectedEventType}&location=${selectedLocation}`;
        });

        // Handle event type change
        document.getElementById('eventTypeSelect').addEventListener('change', function() {
            const selectedFilter = document.getElementById('filterSelect').value;
            const selectedEventType = this.value;
            const selectedLocation = document.getElementById('locationSelect').value;
            window.location.href = `?eventFilter=${selectedFilter}&eventType=${selectedEventType}&location=${selectedLocation}`;
        });

        // Handle location change
        document.getElementById('locationSelect').addEventListener('change', function() {
            const selectedFilter = document.getElementById('filterSelect').value;
            const selectedEventType = document.getElementById('eventTypeSelect').value;
            const selectedLocation = this.value;
            window.location.href = `?eventFilter=${selectedFilter}&eventType=${selectedEventType}&location=${selectedLocation}`;
        });

        // Handle sorting option change
        document.getElementById('sortSelect').addEventListener('change', function() {
            const selectedSort = this.value;
            window.location.href = `?eventSort=${selectedSort}`;
        });

        // Update the filter options visibility based on selection
        function updateFilterOptions(selectedFilter) {
            if (selectedFilter === 'event_type') {
                // Show event type dropdown and hide location dropdown
                document.getElementById('locationSelectContainer').style.display = 'none';
                document.getElementById('eventTypeSelectContainer').style.display = 'block';
            } else if (selectedFilter === 'location') {
                // Show location dropdown and hide event type dropdown
                document.getElementById('locationSelectContainer').style.display = 'block';
                document.getElementById('eventTypeSelectContainer').style.display = 'none';
            } else {
                // If no filter is selected, hide both dropdowns
                document.getElementById('locationSelectContainer').style.display = 'none';
                document.getElementById('eventTypeSelectContainer').style.display = 'none';
            }
        }

        // Delete Event function
        function deleteEvent(eventId) {
            if (confirm('Are you sure you want to delete this event?')) {
                $.ajax({
                    url: 'deleteEvent',
                    type: 'POST',
                    data: {
                        deleteEvent: true,
                        id: eventId,
                    },
                    success: function(response) {
                        // Reload the page after successful deletion
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        // Optional: Handle any errors here
                        console.error("An error occurred:", error);
                    }
                });
            }
        }
    </script>

</body>

</html>