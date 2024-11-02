<?php
require_once "db_setup.php";

// Bunch of CRUDing without the U to populate our lovely database
run_queries(
    queries: [

        // Drop the database if it already exists, do nothing otherwise
        "DROP DATABASE IF EXISTS $configs->DB_NAME",

        // Create the database from scratch
        "CREATE DATABASE $configs->DB_NAME",

        // Create the table for users
        "CREATE TABLE $configs->DB_NAME.$configs->DB_USERS_TABLE (
            `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `firstName` VARCHAR(50) NULL DEFAULT NULL,
            `lastName` VARCHAR(50) NULL DEFAULT NULL,
            `email` VARCHAR(50) NULL,
            `passwordHash` VARCHAR(32) NOT NULL,
            UNIQUE INDEX `uq_email` (`email` ASC)
        );",

        // And the table for items
        "CREATE TABLE $configs->DB_NAME.$configs->DB_ITEMS_TABLE (
            `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(100) NOT NULL,
            `description` TEXT,
            `price` DECIMAL(10, 2) NOT NULL,
            `discount_percentage` DECIMAL(5, 2) DEFAULT 0.00,
            `stock_quantity` INT DEFAULT 0,
            `size` VARCHAR(5) NOT NULL,
            `color` VARCHAR(50) NOT NULL,
            `material` VARCHAR(50)
        );",

        // And the table for carts
        "CREATE TABLE $configs->DB_NAME.$configs->DB_CARTS_TABLE (
            `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT NOT NULL,
            `status` ENUM('active', 'completed', 'abandoned') DEFAULT 'active',
            FOREIGN KEY (user_id) REFERENCES $configs->DB_NAME.$configs->DB_USERS_TABLE(id)
        );",

        // And the table for cart items
        "CREATE TABLE $configs->DB_NAME.$configs->DB_CART_ITEMS_TABLE (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `cart_id` INT NOT NULL,
            `item_id` INT NOT NULL,
            `quantity` INT NOT NULL DEFAULT 1,
            FOREIGN KEY (cart_id) REFERENCES $configs->DB_NAME.$configs->DB_CARTS_TABLE(id),
            FOREIGN KEY (item_id) REFERENCES $configs->DB_NAME.$configs->DB_ITEMS_TABLE(id)
        );",

        // Create table for events
        "CREATE TABLE IF NOT EXISTS $configs->DB_NAME.$configs->DB_EVENTS_TABLE (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(100) NOT NULL,
            `description` TEXT,
            `location` VARCHAR(50),
            `type` VARCHAR(50),
            `date` DATETIME
        );",

        // Populating event data
        "INSERT INTO $configs->DB_NAME.$configs->DB_EVENTS_TABLE (name, description, location, type, date) VALUES
        ('7ayah kareema', 'An 7ayah kareema event', 'Cairo', 'Fundraising', '2024-12-25 10:00:00'),
        ('57357', 'Cancer awareness event', 'Cairo', 'Awareness', '2024-12-26 11:00:00'),
        ('Blood Donation', 'A blood donation event', 'Cairo', 'Donation', '2024-12-27 12:00:00'
        );",


        // Then insert some random (not so random) users
        "INSERT INTO $configs->DB_NAME.$configs->DB_USERS_TABLE VALUES
        (1,'GitHub','User','github.user@github.com','25d55ad283aa400af464c76d713c07ad'),
        (2,'Google','User','google.user@google.com','25d55ad283aa400af464c76d713c07ad'),
        (3,'Facebook','User','facebook.user@meta.com','25d55ad283aa400af464c76d713c07ad'),
        (4,'7amada','Belganzabeel','7amada@belganzabeel.com','25d55ad283aa400af464c76d713c07ad'),
        (5,'7amada','Tany','5ales@depression.inc','a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3');",

        // And some random (these are random fe3lan this time) items
        "INSERT INTO $configs->DB_NAME.$configs->DB_ITEMS_TABLE VALUES
        (1,'Classic Cotton Tee', 'A timeless cotton t-shirt for everyday wear', 19.99, 10.00, 50, 'M', 'White', 'Cotton'),
        (2,'Vintage Graphic Tee', 'Retro graphic print, soft touch', 25.99, 5.00, 30, 'L', 'Black', 'Polyester'),
        (3,'Sporty Performance Tee', 'Moisture-wicking for active use', 29.99, 15.00, 20, 'S', 'Blue', 'Nylon'),
        (4,'Casual Striped Tee', 'Comfortable striped t-shirt, casual fit', 22.50, 0.00, 60, 'XL', 'Green', 'Cotton'),
        (5,'Premium Silk Tee', 'Luxurious silk t-shirt with a smooth feel', 45.00, 20.00, 10, 'M', 'Red', 'Silk'),
        (6,'Eco-Friendly Bamboo Tee', 'Sustainable and breathable bamboo fabric', 30.00, 0.00, 40, 'L', 'Grey', 'Bamboo'),
        (7,'Heavyweight Pocket Tee', 'Durable t-shirt with a functional pocket', 18.50, 0.00, 70, 'M', 'Navy', 'Cotton'),
        (8,'Long-Sleeve Tee', 'Lightweight long-sleeve t-shirt', 27.00, 12.00, 25, 'XL', 'White', 'Cotton'),
        (9,'Tie-Dye Tee', 'Unique tie-dye patterns for each piece', 24.99, 8.50, 35, 'S', 'Multi-color', 'Cotton'),
        (10,'V-Neck Tee', 'Simple V-neck design for a stylish look', 21.99, 0.00, 45, 'M', 'Black', 'Polyester');",

        // And some dummy cart associations to each user
        "INSERT INTO $configs->DB_NAME.$configs->DB_CARTS_TABLE VALUES
        (1,1,'active'),
        (2,2,'active'),
        (3,3,'active'),
        (4,4,'active'),
        (5,5,'active');",

    ],
    echo: true
);
