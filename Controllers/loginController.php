<?php
require_once './models/authentication-strategies.php';
require_once './views/UserView.php';
require_once 'IControl.php';
$configs = require "server-configs.php";
// session_start(); // Start the session at the beginning

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
                        $_SESSION['USER_ID'] = $user->get_id();
                        $_SESSION['USER_EMAIL'] = $user->getEmail();
                        (int)$_SESSION['USER_TYPE'] = $user->getType();

                        $_SESSION['USERNAME'] = $user->getFirstName();
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
