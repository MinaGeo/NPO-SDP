<?php
require_once './models/authentication-strategies.php';
require_once './views/UserView.php';
require_once 'IControl.php';

interface AuthenticationService {
    public function login(string $email, string $password);
}

class AuthenticationProxy implements AuthenticationService {
    private $realService;

    public function __construct(array $authenticators) {
        if (!isset($_POST['loginMethod']) || !array_key_exists($_POST['loginMethod'], $authenticators)) {
            echo json_encode(['success' => false, 'message' => 'Invalid login method']);
            exit;
        }
        $this->realService = new ContextAuthenticator($authenticators[$_POST['loginMethod']]);
    }

    public function login(string $email, string $password) {
        if (!$this->isValidInput($email, $password)) {
            echo json_encode(['success' => false, 'message' => 'Invalid input parameters']);
            exit;
        }

        // Example Rate Limiting 
        $ip = $_SERVER['REMOTE_ADDR'];
        if (isset($_SESSION['login_attempts'][$ip]) && $_SESSION['login_attempts'][$ip]['count'] >= 5 && (time() - $_SESSION['login_attempts'][$ip]['last_attempt']) < 60) {
            echo json_encode(['success' => false, 'message' => 'Too many login attempts. Please try again after 60 seconds.']);
            exit;
        }

        $user = $this->realService->login($email, $password);

        if (!$user) {
            if (!isset($_SESSION['login_attempts'][$ip])) {
                $_SESSION['login_attempts'][$ip] = ['count' => 0, 'last_attempt' => time()];
            }
            $_SESSION['login_attempts'][$ip]['count']++;
            $_SESSION['login_attempts'][$ip]['last_attempt'] = time();
        } else {
            unset($_SESSION['login_attempts'][$ip]); // Reset on successful login
        }
        return $user;
    }

    private function isValidInput(string $email, string $password): bool {
        return !empty($email) && !empty($password) && strlen($email) <= 255 && strlen($password) <= 255;
    }
}

class LoginController implements IControl
{
    public function show()
    {
        $userView = new UserView();
        $userView->showLogin();
    }

    public function validateLogin()
    {
        if (isset($_POST['loginFlag'])) {
            if (!empty($_POST['email']) && !empty($_POST['password']) && isset($_POST['loginMethod'])) {
                $authenticators = [
                    'facebook' => new FacebookAuthenticator(),
                    'google' => new GoogleAuthenticator(),
                    'github' => new GitHubAuthenticator(),
                    'database' => new DBAuthenticator(),
                ];

                $authService = new AuthenticationProxy($authenticators);
                $user = $authService->login($_POST['email'], $_POST['password']);

                if ($user) {
                    $_SESSION['USER_ID'] = $user->get_id();
                    $_SESSION['USER_EMAIL'] = $user->getEmail();
                    (int)$_SESSION['USER_TYPE'] = $user->getType();
                    $_SESSION['USERNAME'] = $user->getFirstName();
                    echo json_encode(['success' => true, 'message' => $_POST['email'] . ' logged in successful']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Invalid credentials']); // More generic message
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Missing parameters']);
            }
            exit;
        }
    }
}
?>
