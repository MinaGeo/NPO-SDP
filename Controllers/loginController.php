<?php
require_once "./models/authentication-strategies.php";

class LoginController
{
    public function show()
    {
        $msg = '';
        if (isset($_POST['login'])) {
            if (!empty($_POST['email']) && !empty($_POST['password'])) {
                $context = new ContextAuthenticator();
                $user = $context->login($_POST['email'], $_POST['password']);
                if ($user) {
                    // $msg .= "<strong>User found:</strong><br/><pre>$user</pre>";
                    // Redirect to the shop upon successful login then terminate current script
                    header("Location: shop/$user->id");
                    exit();
                } else {
                    $msg .= "<strong>User not found.</strong><br/><br/><!--deng-->";
                }
            } else {
                $msg .= '<strong>Error: Please enter email and password.</strong>';
            }
        }
        // XXX: Absolutely disgusting, but it works... should use classes for cleaner code
        require_once "08-login-view.php";
    }
}
