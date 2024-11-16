<?php
require_once './models/userBase.php';
require_once './views/UserView.php';

class UserDataEditController implements IControl
{
    public function show()
    {   
        $user = User::get_by_id($_SESSION['USER_ID']);
        if(!$user) {
            $userData = null;
        }
        else {
            $userData = [
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'email' => $user->getEmail(),
                'passwordHash' => $user->getPasswordHash()
            ];
        }
        $userView = new UserView();
        $userView->showProfile($userData);
    }

    public function updateUserData()
    {
        if (isset($_POST['registerFlag'])) {
            if (!empty($_POST['firstName']) && !empty($_POST['lastName']) && !empty($_POST['email']) && !empty($_POST['password'])) {
                $user_data = [
                    'firstName' => $_POST['firstName'],
                    'lastName' => $_POST['lastName'],
                    'email' => $_POST['email'],
                    'passwordHash' => md5($_POST['password'])
                ];

                if(User::update_by_id($_SESSION['USER_ID'], $user_data)){
                    echo json_encode([
                        'success' => true,
                        'message' => $_POST['email'] . ' got updated successfully!'
                    ]);
                }
                else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to update ' . $_POST['email']
                    ]);
                }
            } 
            exit;
        }
    }
}
