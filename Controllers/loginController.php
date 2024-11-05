<?php
require_once './models/authentication-strategies.php';
$configs = require "server-configs.php";
// session_start(); // Start the session at the beginning

class LoginController
{
    public function show()
    {
        require_once "./views/LoginView.php";
    }

    public function validateLogin()
    {
        if (isset($_POST['loginFlag'])) {
            if (!empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['loginMethod'])) {
                if (!empty($_POST['loginMethod'])) {
                    $authenticators = [
                        'facebook' => new FacebookAuthenticator(),
                        'google' => new GoogleAuthenticator(),
                        'github' => new GitHubAuthenticator(),
                        'database' => new DBAuthenticator(),
                    ];
                    $context = new ContextAuthenticator($authenticators[$_POST['loginMethod']]);
                    $user = $context->login($_POST['email'], $_POST['password']);
                    if ($user) {
                        $userId = $user->get_id();
                        $_SESSION['USER_ID'] = $userId;
<<<<<<< HEAD
                        if($user->getType()==1){
                            (int)$_SESSION['USER_TYPE'] = 1;
                        } else {
                            (int)$_SESSION['USER_TYPE'] = 0;
                        }

=======
                        $_SESSION['USERNAME'] = $user->getFirstName();
>>>>>>> homepage
                        echo json_encode([
                            'success' => true,
                            'message' => $_POST['email'] . ' logged in successful'
                        ]);
                    } else {
                        echo json_encode([
                            'success' => false,
                            'message' => $_POST['email'] . ' was not found'
                        ]);
                    }
                } else {
                    echo json_encode(
                        [
                            'success' => false,
                            'message' => 'Missing parameters'
                        ]
                    );
                }
            }
            exit;
        }
    }
}
?>
