<!DOCTYPE html>
<html lang="en">

<head>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@materializecss/materialize@1.0.0/dist/css/materialize.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add New Event</title>
    <link rel="stylesheet" href="../assets/eventStyle.css">

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

                <?php
                $governorates = ['Cairo', 'Alexandria', 'Giza', 'Port Said', 'Suez', 'Damietta', 'Mansoura', 'Gharbia', 'Ismailia', 'Minya', 'Luxor', 'Aswan', 'Asyut', 'Beni Suef', 'Fayoum', 'Kafr El Sheikh', 'Sharkia', 'Monufia', 'Beheira', 'Matrouh'];
                $subLocations = [
                    'Cairo' => ['Nasr City', 'Maadi', 'Heliopolis', 'Dokki', 'Mohandessin', 'Zamalek', 'Ain Shams', 'Shubra', 'Helwan', 'Abbasiya', 'Sayeda Zeinab', 'Garden City', 'Manial', 'Bulaq', 'Imbaba', 'Manshiyat Naser', 'Dar El Salam', 'El Marg', 'El Matareya', 'El Salam City', 'El Sayeda Aisha', 'El Shorouk', 'El Tagamu El Khames', 'El Waily', 'El Zaytoun', 'El Zawya El Hamra', 'El-Nozha', 'Hadayek El Kobba', 'Helmeyet El-Zaitoun', 'Kasr El Nile', 'Korba', 'Madinet Nasr', 'Masr El Qadima', 'New Cairo', 'Old Cairo', 'Rod El-Farag'],
                    'Alexandria' => ['Bacos', 'Bolkly', 'Camp Caesar', 'Camp Shezar', 'Cleopatra', 'Dekhela', 'Downtown', 'El Agami', 'El Amreya', 'El Asafra'],
                    'Giza' => ['6th of October City', 'Agouza', 'Daher', 'Dokki', 'Faisal', 'Giza Square', 'Haram', 'Imbaba', 'Mohandessin', 'Nahia', 'Nasr City', 'Oseem', 'Pyramids', 'Zamalek'],
                    'Port Said' => ['Port Fouad', 'Port Said'],
                    'Suez' => ['Ataqah', 'Faisal', 'Ganayen', 'Ganoub', 'Kuwait', 'Suez'],
                    'Damietta' => ['New Damietta', 'Ras El Bar', 'Zarqa'],
                    'Mansoura' => ['Talkha', 'Dekernes', 'Aga', 'Sherbin'],
                    'Gharbia' => ['Tanta', 'Mahalla', 'Zefta', 'Kafr El Zayat'],
                    'Ismailia' => ['Fayed', 'Qantara'],
                    'Minya' => ['Maghagha', 'Bani Mazar', 'Samalut'],
                    'Luxor' => ['Karnak', 'West Bank'],
                    'Aswan' => ['Kom Ombo', 'Kalabsha'],
                    'Asyut' => ['Abnub', 'Dairut'],
                    'Beni Suef' => ['Nasser'],
                    'Fayoum' => ['Tamiya', 'Yusuf El Sediaq'],
                    'Kafr El Sheikh' => ['Desouk', 'Fuwwah'],
                    'Sharkia' => ['Sharkia'],
                    'Monufia' => ['Monufia'],
                    'Beheira' => ['Beheira'],
                    'Matrouh' => ['Matrouh'],
                ];
                ?>
                <!-- Governorates Dropdown -->
                <div class="input-field col s12"> <select id="governorateSelect" name="governorate_id" required>
                        <option value="" disabled selected>Choose Governorate</option> <?php foreach ($governorates as $governorate): ?> <option value="<?= htmlspecialchars($governorate) ?>"><?= htmlspecialchars($governorate) ?></option> <?php endforeach; ?>
                    </select> <label for="governorateSelect">Governorate</label> </div> 
                    
                    <!-- Sub-locations Dropdown -->
                <div class="input-field col s12"> <select id="subLocationSelect" name="location_id" required>
                        <option value="" disabled selected>Choose Sub-location</option>
                    </select> <label for="subLocationSelect">Sub-location</label> </div>

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
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Materialize components
            M.AutoInit();
            // Handle governorate selection change
            document.getElementById('governorateSelect').addEventListener('change', function() {
                const selectedGovernorate = this.value;
                const subLocationSelect = document.getElementById('subLocationSelect');

                // Clear current options
                subLocationSelect.innerHTML = '<option value="" disabled selected>Choose Sub-location</option>';

                // Populate sub-location dropdown based on selected governorate
                const subLocations = <?php echo json_encode($subLocations); ?>;
                if (subLocations[selectedGovernorate]) {
                    subLocations[selectedGovernorate].forEach(function(subLocation) {
                        const option = document.createElement('option');
                        option.value = subLocation;
                        option.text = subLocation;
                        subLocationSelect.add(option);
                    });
                }

                // Re-initialize the sub-location select with Materialize
                M.FormSelect.init(subLocationSelect);
            });
        });

        function validateInputs() {
            const name = document.getElementById('eventName').value;
            const description = document.getElementById('eventDescription').value;
            const governorate = document.getElementById('governorateSelect').value;
            const location = document.getElementById('subLocationSelect').value;
            const date = document.getElementById('eventDate').value;
            const type = document.getElementById('eventType').value;

            if (!name || !description ||!governorate ||!location || !date || !type) {
                M.toast({
                    html: 'Please fill in all fields.',
                    classes: 'rounded red'
                });
                return false; // Return false if any field is empty
            }
            return true; // All fields are filled
        }

        function validateDateFormat() {
            const dateInput = document.getElementById('eventDate').value;
            const dateFormat = /^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/;

            if (!dateFormat.test(dateInput)) {
                M.toast({
                    html: 'Please enter the date in the format YYYY-MM-DD HH:MM:SS.',
                    classes: 'rounded red'
                });
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
            const governorate_id = document.getElementById('governorateSelect').value;
            const location_id = document.getElementById('subLocationSelect').value;
            const date = document.getElementById('eventDate').value;
            const type = document.getElementById('eventType').value;

            // Call addEvent function with form data
            addEvent(name, description,governorate_id, location_id, type, date);
        }

        function addEvent(name, description,governorate_id, location_id, type, date) {
            $.ajax({
                url: 'addEvent',
                type: 'POST',
                data: {
                    addEvent: true,
                    name: name,
                    description: description,
                    governorate_id: governorate_id,
                    location_id: location_id,
                    type: type,
                    date: date,
                },
                success: function(response) {
                    alert("Event Added!");
                    window.location.href = "event?usertype=admin";
                },
                error: function(xhr, status, error) {
                    M.toast({
                        html: 'Error adding event.',
                        classes: 'rounded red'
                    });
                    console.error("An error occurred:", error);
                }
            });
        }
    </script>
</body>

</html>