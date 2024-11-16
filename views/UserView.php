<?php
class UserView{
    // Home Page
    function showHome(){
        require_once "./views/Navbar.php";
        require_once './views/pages/HomePage.php';
    }

    // Login Page 
    function showLogin(){
        require_once './views/pages/LoginPage.php';
    }

    // Register Page 
    function showRegister(){
        require_once './views/pages/RegisterPage.php';
    }

    // Profile Page 
    function showProfile($userData){
        require_once './views/pages/UserDataEditPage.php';
    }
} 
?>