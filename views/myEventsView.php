<!DOCTYPE html>
<html lang="en">

<head>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@materializecss/materialize@1.0.0/dist/css/materialize.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo "Events" ?></title>
    <style>
        .event-detail-label {
            font-weight: bold;
            margin-right: 5px;
        }

        .event-detail-value {
            color: red;
            display: inline;
        }

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
                <h5>Welcome to your <?php echo "Events" ?>!</h5>
                <h6>Are you ready to attend?</h6>
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
                </select>
                <label>
                    <img class="logo" src="../assets/filter.png" alt="Filter Logo" /> Choose Filtering Option
                </label>
            </div>

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
            <?php foreach ($volunteerEvents as $event): ?>
                <div class="col s12 m6 l4">
                    <div class="card">
                        <div class="card-content">
                            <span class="card-title">
                                <h5><?php echo htmlspecialchars($event->name); ?></h5>
                            </span>
                            <p><span class="event-detail-label">Description:</span>
                                <span class="event-detail-value"><?php echo htmlspecialchars($event->description); ?></span>
                            </p>
                            <p><span class="event-detail-label">Location:</span>
                                <span class="event-detail-value"><?php echo htmlspecialchars($event->location); ?></span>
                            </p>
                            <p><span class="event-detail-label">Event Type:</span>
                                <span class="event-detail-value"><?php echo htmlspecialchars($event->type); ?></span>
                            </p>
                            <p><span class="event-detail-label">Date:</span>
                                <span class="event-detail-value"><?php echo htmlspecialchars($event->date); ?></span>
                            </p>
                        </div>
                        <div class="card-action">
                            <button class="removeBtn btn red waves-effect waves-light" type="button" onclick="removeEvent(<?php echo htmlspecialchars($event->id); ?>)">
                                Remove
                            </button>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        </div>


    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var elems = document.querySelectorAll('select');
            M.FormSelect.init(elems);
        });

        document.querySelectorAll('.attendBtn').forEach(button => {
            button.addEventListener('click', () => M.toast({
                html: 'Event reserved.',
                displayLength: 1000,
                classes: 'rounded blue'
            }));
        });

        // Event handler for the filter select dropdown
        document.getElementById('filterSelect').addEventListener('change', function() {
            const selectedFilter = this.value;
            const selectedEventType = document.getElementById('eventTypeSelect').value;
            const volunteerId = "<?php echo 1; ?>"; //  $volunteerId Include the volunteer ID ----------------->
            window.location.href = `?eventFilter=${selectedFilter}&eventType=${selectedEventType}&volunteerId=${volunteerId}`;
        });

        // Event handler for the event type select dropdown
        document.getElementById('eventTypeSelect').addEventListener('change', function() {
            const selectedFilter = document.getElementById('filterSelect').value;
            const selectedEventType = this.value;
            const volunteerId = "<?php echo 1; ?>"; // Include the volunteer ID ------------------->
            window.location.href = `?eventFilter=${selectedFilter}&eventType=${selectedEventType}&volunteerId=${volunteerId}`;
        });

        // Event handler for the sort select dropdown
        document.getElementById('sortSelect').addEventListener('change', function() {
            const selectedSort = this.value;
            const volunteerId = "<?php echo 1; ?>"; // Include the volunteer ID ------------>
            window.location.href = `?eventSort=${selectedSort}&volunteerId=${volunteerId}`;
        });

        function removeVolunteerFromEvent(eventId) {
        if (confirm('Are you sure you want to remove yourself from this event?')) {
            $.ajax({
                url: 'removeMyEvent',
                type: 'POST',
                data: {
                    removeMyEvent: true,
                    eventId: eventId, 
                    volunteerId: <?php echo 1 ; ?>, //volunteer's ID $volunteerId --------->
                },
                success: function(response) {
                    var result = JSON.parse(response);
                    if (result.success) {
                        alert('You have been removed from the event.');
                        location.reload();
                    } else {
                        alert('Failed to remove from event: ' + result.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("An error occurred:", error);
                    alert('There was an error removing you from the event. Please try again.');
                }
            });
        }
    }
    </script>


</body>

</html>