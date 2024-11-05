<?php

class HomepageController
{
    public function show()
    {
        require_once "./views/Navbar.php";
        require_once "./views/HomepageView.php";
    }

    public function logout()
    {
        $_SESSION['USER_ID'] = -1;
        $this -> show();
    }
}

?>