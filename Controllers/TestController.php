<?php
// src/controllers/TestController.php

class TestController
{
    public function show($arg = null)
    {
        if ($arg) {
            echo "TestController@show with arg: " . htmlspecialchars($arg);
        } else {
            echo "TestController@show without arguments.";
        }
    }
}
