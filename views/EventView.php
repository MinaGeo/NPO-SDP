<!DOCTYPE html>
<html lang="en">

<head>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@materializecss/materialize@1.0.0/dist/css/materialize.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo "Events" ?></title>
    <style>
        /* Style the description in cyan */
        .event-detail-label {
            font-weight: bold;
            margin-right: 5px;
        }

        .event-detail-value {
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
            /* Make dropdowns take equal space */
            margin-right: 10px;
            /* Spacing between dropdowns */
            min-width: 150px;
            /* Minimum width for smaller dropdowns */
        }

        /* Make logos smaller */
        .logo {
            width: 20px;
            /* Adjust size as needed */
            margin-right: 5px;
            /* Space between logo and label */
            vertical-align: middle;
            /* Align logo with text */
        }
    </style>
</head>

<body>
    <script src="https://cdn.jsdelivr.net/npm/@materializecss/materialize@1.0.0/dist/js/materialize.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <div class="container">
        <div class="row">
            <div class="col s12">
                <h5>Welcome to <?php echo "Events" ?>!</h5>
                <h6>What would you like to attend?</h6>
            </div>
        </div>

        <!-- Dropdowns in a flex container -->
        <div class="dropdown-container">
            <!-- Sorting Dropdown -->
            <div class="input-field">
                <select id="sortSelect">
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
                    <!-- Add more filter options here -->
                </select>
                <label>
                    <img class="logo" src="../assets/filter.png" alt="Filter Logo" /> Choose Filtering Option
                </label>
            </div>

            <!-- Event Type Dropdown -->
            <div class="input-field">
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
        </div>

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
                            <button class="attendBtn btn waves-effect waves-light" type="button">
                                Attend
                            </button>
                            <button
                                onclick="deleteEvent(<?php echo htmlspecialchars($event->id); ?>)"
                                class="deleteBtn btn red waves-effect waves-light" type="button"
                                data-event-id="<?php echo $event->id; ?>">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="row">
            <div class="col s12">
                <a href="addEventView" class="btn waves-effect waves-light green">Add Event</a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var elems = document.querySelectorAll('select');
            M.FormSelect.init(elems); // Initialize Materialize dropdown
        });

        document.querySelectorAll('.attendBtn').forEach(button => {
            button.addEventListener(
                'click',
                () => M.toast({
                    html: 'Event reserved.',
                    displayLength: 1000,
                    classes: 'rounded blue'
                })
            );
        });

        document.querySelectorAll('.deleteBtn').forEach(button => {
            button.addEventListener(
                'click',
                () => M.toast({
                    html: 'Event Deleted!',
                    displayLength: 1000,
                    classes: 'rounded red'
                })
            );
        });

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
    document.addEventListener('DOMContentLoaded', function() {
        const elems = document.querySelectorAll('select');
        M.FormSelect.init(elems); // Initialize Materialize dropdown
    });

    // Handle filtering option change
    document.getElementById('filterSelect').addEventListener('change', function() {
        const selectedFilter = this.value;
        const selectedEventType = document.getElementById('eventTypeSelect').value;
        window.location.href = `?eventFilter=${selectedFilter}&eventType=${selectedEventType}`;
    });

    document.getElementById('eventTypeSelect').addEventListener('change', function() {
        const selectedFilter = document.getElementById('filterSelect').value;
        const selectedEventType = this.value;
        window.location.href = `?eventFilter=${selectedFilter}&eventType=${selectedEventType}`;
    });

    // Handle sorting option change
    document.getElementById('sortSelect').addEventListener('change', function() {
        const selectedSort = this.value;
        window.location.href = `?eventSort=${selectedSort}`;
    });
</script>

</body>

</html>