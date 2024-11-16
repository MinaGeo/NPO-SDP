<?php
class EventView{
    
    public function showGuestPage($events){
        require_once './views/Navbar.php';
        require_once './views/pages/EventGuestPage.php';
    }

    public function showAdminPage($events){
        require_once './views/Navbar.php';
        require_once './views/pages/EventAdminPage.php';
    }

    public function showVolunteerPage($events){
        require_once './views/Navbar.php';
        require_once './views/pages/EventVolunteerPage.php';
    }

    public function showAddEvent()
    {
        require_once "./views/Navbar.php";
        require_once "./views/addEventView.php";
    }

    public function showVolunteerEvents($volunteerEvents,$volunteerId){
        require_once './views/Navbar.php';
        require_once './views/myEventsView.php';
    }
}
?>