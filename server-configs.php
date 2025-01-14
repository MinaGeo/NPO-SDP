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
    'DB_VOLUNTEER_EVENTS_TABLE' => 'volunteer_events',
    'DB_DONATIONS_TABLE' => 'donations',
    'DB_LOCATIONS_TABLE' => 'location',
    'DB_LOCATION_HIERARCHY_TABLE' => 'location_hierarchy',

    // Routes
    'ROUTES' => [
        // '/' => 'HomepageController@show',
        '/test'          => 'TestController@show',
        '/home'         => 'HomepageController@show',
        '/logout' => 'HomepageController@logout',
        //----------------------USER------------------------------//
        // login
        '/login'         => 'LoginController@show',
        '/validateLogin' => 'LoginController@validateLogin',
        // Register
        '/register'         => 'RegisterController@show',
        '/validateRegister' => 'RegisterController@validateRegister',
        // Update
        '/updateUser' => 'UserDataEditController@show',
        '/updateUserData'=> 'UserDataEditController@updateUserData',
        //-----------------------SHOP----------------------------//
        '/shop' => 'ShopController@show',
        '/deleteShopItem' => 'ShopController@shopDeleteItem',
        '/addShopItemToCart'=>'ShopController@shopAddItemToCart',
        '/showAddItem' => 'ShopController@showAddItem',
        '/addShopItem' => 'ShopController@shopAddItem',
        '/showCategoryTree' => 'ShopController@showCategoryTree',
        //-----------------------CART----------------------------//
        '/cart' => 'CartController@show',
        '/removeCartItem' => 'CartController@removeCartItem',
        '/checkout'=>'CartController@checkout',
        '/cartHistory' => 'CartController@showCartHistory',
        //-----------------------EVENT----------------------------//
        '/event' => 'EventController@show',
        '/addEventView' => 'EventController@showAddEvent',
        '/addEvent' => 'EventController@addNewEvent',
        '/deleteEvent' => 'EventController@deleteEvent',
        '/registerForEvent'=> 'EventController@registerForEvent',
        '/myEventsView' => 'EventController@showVolunteerEvents',
        '/removeMyEvent' => 'EventController@removeMyEvent',
        //-----------------------DONATION----------------------------//
        '/donation' => 'DonationController@show',
        '/processDonation' =>'DonationController@processDonation'

    ],
);