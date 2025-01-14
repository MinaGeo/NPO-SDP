<?php
class DonationView{
    public function showDonation()
    {
        require_once "./views/Navbar.php";
        require_once './views/pages/donationForm.php';;
        // require_once './views/pages/DonationUserDataForm.php';
    }

    public function showProcessing()
    {
        // require_once "./views/Navbar.php";
        require_once './views/pages/ProcessingPage.php';
    }

    public function showSuccess()
    {
        require_once './views/pages/SuccessProcess.php';
    }

    public function showAdmin($donationsList)
    {
        require_once './views/pages/DonationAdmin.php';
    }
}
?>