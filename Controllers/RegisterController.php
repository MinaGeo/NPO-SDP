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

        // Rate Limiting (Example - Improve for production)
        $ip = $_SERVER['REMOTE_ADDR'];
        if (isset($_SESSION['registration_attempts'][$ip]) && $_SESSION['registration_attempts'][$ip]['count'] >= 3 && (time() - $_SESSION['registration_attempts'][$ip]['last_attempt']) < 60) { //3 attempts in 60 seconds
            echo json_encode(['success' => false, 'message' => 'Too many registration attempts. Please try again after 60 seconds.']);
            exit;
        }

        // Email Verification (Basic Example - Needs more robust implementation)
        if (isset($_POST['email']) && !$this->isEmailAvailable($_POST['email'])) {
            echo json_encode(['success' => false, 'message' => 'This email is already in use.']);
            exit;
        }

        $result = $this->realService->register($userData);

        if ($result === false) { // Registration failed
            if (!isset($_SESSION['registration_attempts'][$ip])) {
                $_SESSION['registration_attempts'][$ip] = ['count' => 0, 'last_attempt' => time()];
            }
            $_SESSION['registration_attempts'][$ip]['count']++;
            $_SESSION['registration_attempts'][$ip]['last_attempt'] = time();
        } else {
            unset($_SESSION['registration_attempts'][$ip]); // Reset on successful registration
        }
        return $result;
    }

    private function isValidInput(array $userData): bool {
        if (empty($userData['firstName']) || empty($userData['lastName']) || empty($userData['email']) || empty($userData['password'])) {
            return false;
        }
        if (strlen($userData['firstName']) > 255 || strlen($userData['lastName']) > 255 || strlen($userData['email']) > 255 || strlen($userData['password']) < 6 ) {
            return false;
        }

        if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        return true;
    }

    private function isEmailAvailable($email) {
        $passwordHash = md5("someRandomStringThatWontBeUsed"); //just to make the function work
        return !User::get_by_email_and_password_hash($email,$passwordHash);
    }
}

class RealRegistrationService implements RegistrationService {
    public function register(array $user_data){
         if (User::get_by_email_and_password_hash($user_data['email'], $user_data['passwordHash'])) {
            return false; // Indicate failure
        } 
        else {
            User::create_new_user($user_data);
            return true; // Indicate success
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

                $registrationService = new RegistrationProxy();
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