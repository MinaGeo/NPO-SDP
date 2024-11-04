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
    'DB_CART_ITEMS_TABLE' => 'cart_items',
    'DB_EVENTS_TABLE'     => 'event',

    // Routes
    'ROUTES' => [
        ''               => 'TestController@show',
        '/'              => 'TestController@show',
        '/test'          => 'TestController@show',
        '/login'         => 'LoginController@show',
        '/shop' => 'ShopController@show',
        '/deleteShopItem' => 'ShopController@shopDeleteItem',
        '/addShopItemToCart'=>'ShopController@shopAddItemToCart',
        '/showAddItem' => 'ShopController@showAddItem',
        '/addShopItem' => 'ShopController@shopAddItem',
        '/cart' => 'ShopController@showCart', //----------->RAFIK: Don't write the arguments here, JUST SEND THE ARGUMENTS NORMALLY
        '/event' => 'EventController@show',
        '/addEventView' => 'EventController@showAddEvent',
        '/addEvent' => 'EventController@addNewEvent',
        '/deleteEvent' => 'EventController@deleteEvent',

    ],


);
