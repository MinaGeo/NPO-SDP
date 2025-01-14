<?php

interface IDatabase{
    function run_query($query, $params = [], $echo = false);
    function run_select_query($query, $params = [], $echo = false) : mysqli_result|bool;   
}

?>