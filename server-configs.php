<?php

return (object) array(

    // App Data
    'SITE_NAME'     => "Rotarct Cairo North",
    'APP_ROOT'      => dirname(dirname(__FILE__)),
    'URL_ROOT'      => 'Lab%2003',
    'URL_SUBFOLDER' => '15-index.php',

    // DB Data
    'DB_HOST' => 'localhost',
    'DB_USER' => 'root',
    'DB_PASS' => '',
    'DB_NAME' => 'eshop',

    // DB Tables
    'DB_USERS_TABLE'      => 'user',
    'DB_ITEMS_TABLE'      => 'shirt',
    'DB_CARTS_TABLE'      => 'cart',
    'DB_CART_ITEMS_TABLE' => 'cart_item',
    'DB_EVENTS_TABLE'     => 'event',

    // Routes
    'ROUTES' => [
        ''               => 'TestController@show',
        '/'              => 'TestController@show',
        '/test'          => 'TestController@show',
        '/test/{arg}'    => 'TestController@show',
        '/login'         => 'LoginController@show',
        '/shop/{userId}' => 'ShopController@show',
        '/cart/{userId}' => 'ShopController@showCart',
        // '/user/{userId}' => 'UserController@show',
        // Other routes...
    ],

);
