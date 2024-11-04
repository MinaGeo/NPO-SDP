<?php
require_once __DIR__ . '/../models/authentication-strategies.php';


class LoginController
{
    public function show()
    {
        require_once "../views/LoginView.php";
    }
}

if (isset($_POST['loginFlag'])) {
    if (!empty($_POST['email']) && !empty($_POST['password'])) {
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
                echo json_encode([
                    'sucess' => true,
                    'message' => $_POST['email'] . ' logged in successful'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => $_POST['email'] . ' was not found'
                ]);
            }
        } 
        else {
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
else {
    $controller = new LoginController();
    $controller->show();
}

?>
