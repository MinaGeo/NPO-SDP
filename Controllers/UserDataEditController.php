<?php
require_once './models/userBase.php';
// session_start();

// Update id=1 with _SESSION in a future commit

class UserDataEditController
{
    public function show()
    {   
        $user = User::get_by_id($_SESSION['USER_ID']);
        if(!$user) {
            echo 'User not found!';
            exit;
        }
        else {
            $userData = [
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'email' => $user->getEmail(),
                'passwordHash' => $user->getPasswordHash()
            ];
            require_once "./views/UserDataEditView.php";
        }
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
