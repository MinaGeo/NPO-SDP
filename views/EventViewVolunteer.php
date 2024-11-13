<!DOCTYPE html>
<html lang="en">

<head>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@materializecss/materialize@1.0.0/dist/css/materialize.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo "Events" ?></title>
    <link rel="stylesheet" href="../assets/eventStyle.css">

</head>

<body>
    <script src="https://cdn.jsdelivr.net/npm/@materializecss/materialize@1.0.0/dist/js/materialize.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <div class="container">
        <div class="row">
            <div class="col s12">
                <h5 style="text-align: center;">Welcome to the <?php echo "Events" ?>!</h5>
                <h6 style="text-align: center;">What would you like to attend?</h6>
            </div>
        </div>

        <div class="dropdown-container">
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

            <div class="input-field">
                <select id="filterSelect">
                    <option value="event_type" <?php echo (isset($_GET['eventFilter']) && $_GET['eventFilter'] == 'event_type') ? 'selected' : ''; ?>>Event Type</option>
                    <option value="location" <?php echo (isset($_GET['eventFilter']) && $_GET['eventFilter'] == 'location') ? 'selected' : ''; ?>>Location</option>
                </select>
                <label>
                    <img class="logo" src="../assets/filter.png" alt="Filter Logo" /> Choose Filtering Option
                </label>
            </div>

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

        <div class="row">
            <?php foreach ($events as $event): ?>
                <div class="col s12 m6 l4">
                    <div class="card">
                        <div class="card-content">
                            <span class="card-title">
                                <h5><?php echo htmlspecialchars($event->get_name()); ?></h5>
                            </span>
                            <p><span class="event-detail-label">Description:</span> <span class="event-detail-value"><?php echo htmlspecialchars($event->get_description()); ?></span></p>
                            <p><span class="event-detail-label">Location:</span> <span class="event-detail-value"><?php echo htmlspecialchars($event->get_location()); ?></span></p>
                            <p><span class="event-detail-label">Event Type:</span> <span class="event-detail-value"><?php echo htmlspecialchars($event->get_type()); ?></span></p>
                            <p><span class="event-detail-label">Date:</span> <span class="event-detail-value"><?php echo htmlspecialchars($event->get_date()); ?></span></p>
                        </div>
                        <div class="card-action">
                            <button class="attendBtn btn waves-effect waves-light" type="button" onclick="registerEvent(<?php echo htmlspecialchars($event->get_id()); ?>)">
                                Attend
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="row">
            <div class="col s12">
                <a href="myEventsView" class="btn waves-effect waves-light green">My Events</a>
            </div>
        </div>
    </div>
    <div style="margin-top: 20px; padding: 10px; background-color: #f9f9f9; border-radius: 8px;">
        <h5>Notifications</h5>
        <div id="notificationArea" style="margin-top: 10px;"></div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var elems = document.querySelectorAll('select');
            M.FormSelect.init(elems);

            // Initialize filter options based on selected filter
            updateFilterOptions(document.getElementById('filterSelect').value);

            // Event listener for filter selection
            document.getElementById('filterSelect').addEventListener('change', function() {
                const selectedFilter = this.value;
                updateFilterOptions(selectedFilter);
                const selectedEventType = document.getElementById('eventTypeSelect').value;
                const selectedLocation = document.getElementById('locationSelect').value;
                window.location.href = `?eventFilter=${selectedFilter}&eventType=${selectedEventType}&location=${selectedLocation}`;
            });

            // Event listener for event type selection
            document.getElementById('eventTypeSelect').addEventListener('change', function() {
                const selectedFilter = document.getElementById('filterSelect').value;
                const selectedEventType = this.value;
                const selectedLocation = document.getElementById('locationSelect').value;
                window.location.href = `?eventFilter=${selectedFilter}&eventType=${selectedEventType}&location=${selectedLocation}`;
            });

            // Event listener for location selection
            document.getElementById('locationSelect').addEventListener('change', function() {
                const selectedFilter = document.getElementById('filterSelect').value;
                const selectedEventType = document.getElementById('eventTypeSelect').value;
                const selectedLocation = this.value;
                window.location.href = `?eventFilter=${selectedFilter}&eventType=${selectedEventType}&location=${selectedLocation}`;
            });

            // Event listener for sort selection
            document.getElementById('sortSelect').addEventListener('change', function() {
                const selectedSort = this.value;
                window.location.href = `?eventSort=${selectedSort}`;
            });
        });

        // Function to update the visibility of the dropdowns
        function updateFilterOptions(selectedFilter) {
            if (selectedFilter === 'event_type') {
                document.getElementById('locationSelectContainer').style.display = 'none';
                document.getElementById('eventTypeSelectContainer').style.display = 'block';
            } else if (selectedFilter === 'location') {
                document.getElementById('locationSelectContainer').style.display = 'block';
                document.getElementById('eventTypeSelectContainer').style.display = 'none';
            }
        }

        function registerEvent(eventId) {
            $.ajax({
                url: 'registerForEvent',
                type: 'POST',
                data: {
                    registerEvent: true,
                    event_id: eventId,
                },
                success: function(response) {
                    const notificationArea = document.getElementById('notificationArea');
                    notificationArea.innerHTML = ''; // Clear existing notifications

                    // Parse response and wrap each notification with inline styles
                    const notifications = response.split('<pre>').map(item => item.replace('</pre>', '')).filter(Boolean);
                    notifications.pop();
                    notifications.forEach(notification => {
                        notificationArea.innerHTML += `
                        <div class="notification-card">
                            <i class="material-icons notification-icon">notifications</i>
                            <span class="notification-text">
                                <strong>Notifying:</strong><br>${notification}
                            </span>
                        </div>

                `;
                    });

                    // Apply fade-in effect
                    setTimeout(() => {
                        document.querySelectorAll('#notificationArea > div').forEach(card => {
                            card.style.opacity = '1';
                            card.style.transform = 'translateY(0)';
                        });
                    }, 100);
                    M.toast({
                        html: 'Successfully registered for the event!',
                        displayLength: 1000,
                        classes: 'rounded blue'
                    });
                },
                error: function(xhr, status, error) {
                    console.error("An error occurred:", error);
                    M.toast({
                        html: 'Failed to register for the event.',
                        displayLength: 1000,
                        classes: 'rounded red'
                    });
                }
            });
        }
    </script>

</body>

</html>