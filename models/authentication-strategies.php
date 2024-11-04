<?php

require_once __DIR__."/UserBase.php";
// Slightly modified behavior to make use of user model... hmm... model?

interface AuthenticationProvider
{
    public function login(String $email, String $password): User|null;
}

class FacebookAuthenticator implements AuthenticationProvider
{
    public function login(String $email, String $password): User|null
    {
        return User::get_by_email_and_password_hash($email, md5($password));
    }
}

class GoogleAuthenticator implements AuthenticationProvider
{
    public function login(String $email, String $password): User|null
    {
        return User::get_by_email_and_password_hash($email, md5($password));
    }
}

class GitHubAuthenticator implements AuthenticationProvider
{
    public function login(String $email, String $password): User|null
    {
        return User::get_by_email_and_password_hash($email, md5($password));
    }
}

class DBAuthenticator implements AuthenticationProvider
{
    public function login(String $email, String $password): User|null
    {
        $md5Hash = md5($password);
        // $rows = run_select_query("SELECT * FROM ESHOP.user WHERE email = '$email' AND passwordHash = '$md5Hash'");
        return User::get_by_email_and_password_hash($email, $md5Hash);
    }
}

class ContextAuthenticator
{
    private AuthenticationProvider $strategy;
    public function __construct(AuthenticationProvider $strategy = new DBAuthenticator())
    {
        $this->strategy = $strategy;
    }
    public function setProvider(AuthenticationProvider $strategy)
    {
        $this->strategy = $strategy;
    }
    public function login(String $email, String $password): User|null
    {
        return $this->strategy->login($email, $password);
    }
}
