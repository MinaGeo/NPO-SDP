<?php
require_once './models/userBase.php';
require_once './views/RegisterView.php';

class RegisterController implements IControl
{
    public function show()
    {
        $registerView = new RegisterView();
    }

    public function validateRegister()
    {
        if (isset($_POST['registerFlag'])) {
            if (!empty($_POST['firstName']) && !empty($_POST['lastName']) && !empty($_POST['email']) && !empty($_POST['password'])) {
                $user_data = [
                    'firstName' => $_POST['firstName'],
                    'lastName' => $_POST['lastName'],
                    'email' => $_POST['email'],
                    'passwordHash' => md5($_POST['password'])
                ];

                if (User::get_by_email_and_password_hash($user_data['email'], $user_data['passwordHash'])) {
                    echo json_encode([
                        'success' => false,
                        'message' => $_POST['email'] . ' already exists!'
                    ]);
                } 
                else {
                    User::create_new_user($user_data);
                    echo json_encode([
                        'success' => true,
                        'message' => $_POST['email'] . ' got created successfully!'
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Missing data!'
                ]);
            }
            exit; // Stop further script execution
        }
    }
}
