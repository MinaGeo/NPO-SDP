<?php
include_once './config.php';
include_once './controllers/DonationController.php';

$controller = new DonationController($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->processDonation($_POST);
} else {
    include './views/donationForm.php';
}

