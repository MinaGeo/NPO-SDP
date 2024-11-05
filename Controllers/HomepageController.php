<?php
session_start(); // Start the session at the beginning

class HomepageController
{
    public function show()
    {
        require_once "./views/Navbar.php";
        require_once "./views/HomepageView.php";
    }
}

?>