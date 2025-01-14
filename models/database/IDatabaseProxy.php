<?php

require_once "./models/database/IDatabase.php";
require_once "./models/userBase.php";
$configs = require "./server-configs.php";
require_once "./db_setup.php";

class DatabaseProxy implements IDatabase
{
    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function validateUser() : bool
    {
        return User::get_by_id($_SESSION['USER_ID']) != null;
    }

    public function run_query($query, $params = [], $echo = false)
    {
        if($this->validateUser()){
            return run_query($query);
        }
        // return $this->database->run_query($query, $params, $echo);
    }

    public function run_select_query($query, $params = [], $echo = false) : mysqli_result|bool
    {
        if($this->validateUser()){
            return run_select_query($query);
        }
    }
}

?>