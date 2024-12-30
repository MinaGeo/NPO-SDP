<?php
require_once './models/userBase.php';
require_once './views/UserView.php';
require_once 'IControl.php';

interface RegistrationService {
    public function register(array $userData);
}

class RegistrationProxy implements RegistrationService {
    private $realService;

    public function __construct() {
        $this->realService = new RealRegistrationService();
    }

    public function register(array $userData) {
        if (!$this->isValidInput($userData)) {
            echo json_encode(['success' => false, 'message' => 'Invalid input parameters']);
            exit;
        }
       
        return $this->realService->register($userData);
    }

    private function isValidInput(array $userData): bool {
        // More robust input validation 
        if (empty($userData['firstName']) || empty($userData['lastName']) || empty($userData['email']) || empty($userData['password'])) {
            return false;
        }
        if (strlen($userData['firstName']) > 255 || strlen($userData['lastName']) > 255 || strlen($userData['email']) > 255 || strlen($userData['password']) < 6 ) { // Example length checks and password min length
            return false;
        }

       if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        return true;
    }
}

class RealRegistrationService implements RegistrationService {
    public function register(array $user_data){
         if (User::get_by_email_and_password_hash($user_data['email'], $user_data['passwordHash'])) {
            echo json_encode([
                'success' => false,
                'message' => $user_data['email'] . ' already exists!'
            ]);
            exit;
        } 
        else {
            User::create_new_user($user_data);
            echo json_encode([
                'success' => true,
                'message' => $user_data['email'] . ' got created successfully!'
            ]);
            exit;
        }
    }
}
class RegisterController implements IControl
{
    public function show()
    {
        $userView = new UserView();
        $userView->showRegister();
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

                $registrationService = new RegistrationProxy(); // Use the Proxy
                $registrationService->register($user_data);

            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Missing data!'
                ]);
                exit;
            }
        }
    }
}
?>