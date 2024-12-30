<?php
require_once './models/userBase.php';
require_once './views/UserView.php';
require_once 'IControl.php';

interface RegistrationService {
    public function register(array $userData): bool;
}

class RegistrationProxy implements RegistrationService {
    private RegistrationService $realService;

    public function __construct() {
        $this->realService = new RealRegistrationService();
    }

    public function register(array $userData): bool {
        if (!$this->isValidInput($userData)) {
            $this->respondWithError('Invalid input parameters', 400);
            return false; // Important: Return false on error
        }

        $ip = $_SERVER['REMOTE_ADDR'];
        $this->handleRateLimiting($ip);

        if (!$this->isEmailAvailable($userData['email'])) {
            $this->respondWithError('This email is already in use.', 400);
            return false;
        }

        $result = $this->realService->register($userData);

        if (!$result) {
            $this->incrementRegistrationAttempts($ip);
        } else {
            $this->resetRegistrationAttempts($ip);
        }

        return $result;
    }

    private function isValidInput(array $userData): bool {
        return !empty($userData['firstName']) && !empty($userData['lastName']) && !empty($userData['email']) && !empty($userData['password']) &&
               strlen($userData['firstName']) <= 255 && strlen($userData['lastName']) <= 255 && strlen($userData['email']) <= 255 && strlen($userData['password']) >= 6 &&
               filter_var($userData['email'], FILTER_VALIDATE_EMAIL);
    }

    private function handleRateLimiting(string $ip): void {
        if (isset($_SESSION['registration_attempts'][$ip]) && $_SESSION['registration_attempts'][$ip]['count'] >= 3 && (time() - $_SESSION['registration_attempts'][$ip]['last_attempt']) < 60) {
            $this->respondWithError('Too many registration attempts. Please try again after 60 seconds.', 429);
        }
    }

    private function incrementRegistrationAttempts(string $ip): void {
        if (!isset($_SESSION['registration_attempts'][$ip])) {
            $_SESSION['registration_attempts'][$ip] = ['count' => 0, 'last_attempt' => time()];
        }
        $_SESSION['registration_attempts'][$ip]['count']++;
        $_SESSION['registration_attempts'][$ip]['last_attempt'] = time();
    }

    private function resetRegistrationAttempts(string $ip): void {
        unset($_SESSION['registration_attempts'][$ip]);
    }

    private function isEmailAvailable(string $email): bool {
        $passwordHash = password_hash("someRandomStringThatWontBeUsed",PASSWORD_DEFAULT); //This will not be used in query
        return !User::get_by_email_and_password_hash($email, $passwordHash);
    }

    private function respondWithError(string $message, int $statusCode): void {
        http_response_code($statusCode);
        echo json_encode(['success' => false, 'message' => $message]);
        exit;
    }
}

class RealRegistrationService implements RegistrationService {
    public function register(array $user_data): bool {
        if (User::get_by_email_and_password_hash($user_data['email'], $user_data['passwordHash'])) {
            return false;
        } else {
            User::create_new_user($user_data);
            return true;
        }
    }
}

class RegisterController implements IControl {
    public function show() {
        $userView = new UserView();
        $userView->showRegister();
    }

    public function validateRegister() {
        if (isset($_POST['registerFlag'])) {
            if (!empty($_POST['firstName']) && !empty($_POST['lastName']) && !empty($_POST['email']) && !empty($_POST['password'])) {
                $user_data = [
                    'firstName' => $_POST['firstName'],
                    'lastName' => $_POST['lastName'],
                    'email' => $_POST['email'],
                    'passwordHash' => password_hash($_POST['password'], PASSWORD_DEFAULT) // Use password_hash
                ];

                $registrationService = new RegistrationProxy();
                $result = $registrationService->register($user_data);

                if ($result)
                {
                    echo json_encode(['success' => true, 'message' => 'Registration successful!']);
                }
                else
                {
                    echo json_encode(['success' => false, 'message' => 'Registration failed!']);
                }

            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Missing data!']);
            }
            exit;
        }
    }
}
?>