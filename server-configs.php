<?php

return (object) array(

    // App Data
    'SITE_NAME'     => "Rotarct Cairo North",
    'APP_ROOT'      => dirname(dirname(__FILE__)),
    'URL_ROOT'      => 'NPO',
    'URL_SUBFOLDER' => 'index.php',

    // DB Data
    'DB_HOST' => 'localhost',
    'DB_USER' => 'root',
    'DB_PASS' => '',
    'DB_NAME' => 'npo',

    // DB Tables
    'DB_USERS_TABLE'      => 'user',
    'DB_ITEMS_TABLE'      => 'shirt',
    'DB_CARTS_TABLE'      => 'cart',
    'DB_CART_ITEMS_TABLE' => 'cart_item',
    'DB_EVENTS_TABLE'     => 'event',

    // Routes
    'ROUTES' => [
        // 'login'         => 'LoginController@show',
        // // '/shop/{userId}' => 'ShopController@show',
        // // '/cart/{userId}' => 'ShopController@showCart',
        // '/event' => 'EventController@show',
        ''               => 'TestController@show',
        '/'              => 'TestController@show',
        '/test'          => 'TestController@show',
        '/login'         => 'LoginController@show',
        '/shop/{userId}' => 'ShopController@show',
        '/cart/{userId}' => 'ShopController@showCart',
        '/event' => 'EventController@show'
    ],

);

