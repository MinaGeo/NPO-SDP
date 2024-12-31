<?php

require_once "./models/DonationModel.php";

// Require all state classes to implement the same function for executing the state logic
interface IDonationState{
    public function execute(Donation $donation);
    public function next(Donation $donation);
    public function previous(Donation $donation);
}

?>