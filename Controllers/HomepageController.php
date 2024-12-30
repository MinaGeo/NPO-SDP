<?php

require_once 'IControl.php';
require_once './views/UserView.php';

class HomepageController implements IControl
{
    public function show()
    {
        $userView = new UserView();
        $userView->showHome();
    }

    public function logout()
    {
        $_SESSION['USER_ID'] = -1;
        $_SESSION['USER_EMAIL'] = null;
        $this -> show();
    }
}

?>