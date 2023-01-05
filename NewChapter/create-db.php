<?php
// connect to server 
$handler = mysqli_connect("localhost", "root", "");

// check connection to server
if (!$handler) {
    die("Error connecting to server: ". mysqli_connect_error());
} else {
    echo "Successfully connected to server!";
}
echo "<br>";

// create database for website
// check if database is successfully created
if (mysqli_query($handler, "CREATE DATABASE newchapterdb")) {
    echo "Successfully created database!";
} else {
    echo "Error creating database: " . mysqli_error($handler);
}
echo "<br>";

// connect to newly created database 
$handler1 = mysqli_connect("localhost", "root", "", "newchapterdb");

// check connection to database
if (!$handler1) {
    die("Error connecting to database: ". mysqli_connect_error());
} else {
    echo "Successfully connected to database!";
}
echo "<br>";

// create table to store customer details 
$customer_query = "CREATE TABLE users (
    userID INT(3) AUTO_INCREMENT, 
    fname VARCHAR(50),
    lname VARCHAR(50),
    contactNum VARCHAR(14),
    username VARCHAR(50),
    email VARCHAR(50),
    password VARCHAR(255),
    role INT(11),
    PRIMARY KEY (userID)
    )";

// execute query to create customer table 
// check if customer table is successfully created
if (mysqli_query($handler1, $customer_query)) {
    echo "Successfully created customers table!";
} else {
    echo "Error creating customers table: " . mysqli_error($handler1);
}
echo "<br>";

// create table to store book details 
$book_query = "CREATE TABLE books (
    bookID INT(3) AUTO_INCREMENT, 
    title VARCHAR(50),
    description VARCHAR(5000), 
    author VARCHAR(50),
    publisher VARCHAR(50),
    genre VARCHAR(200),
    year INT(4),
    language VARCHAR(15),
    price INT(2),
    isbn VARCHAR(17),
    inStock INT(3),
    newArrival BOOLEAN, 
    clearanceBook BOOLEAN, 
    premiumPick BOOLEAN,
    imageFile VARCHAR(50),
    imageDirectory VARCHAR(100),
    PRIMARY KEY (bookID)
    )";

// execute query to create book table
// check if book table is successfully created
if (mysqli_query($handler1, $book_query)) {
    echo "Successfully created books table!";
} else {
    echo "Error creating books table: " . mysqli_error($handler1);
}
echo "<br>";

// create table to store order details 
$order_query = "CREATE TABLE orders (
    orderID INT(3) AUTO_INCREMENT,
    status VARCHAR(10), 
    bookIDs VARCHAR(100),
    userID VARCHAR(4),
    orderTotal DECIMAL(7,2),
    paymentMethod VARCHAR(20),
    orderDate DATE,
    deliveryOption VARCHAR(20),
    deliveryStreet VARCHAR(100),
    deliveryCity VARCHAR(20),
    deliveryState VARCHAR(20),
    deliveryPostcode INT(5),
    deliveryDate DATE,
    deliveryTime TIME,
    PRIMARY KEY (orderID)
    )";

// execute query to create order table
// check if order table is successfully created
if (mysqli_query($handler1, $order_query)) {
    echo "Successfully created orders table!";
} else {
    echo "Error creating orders table: " . mysqli_error($handler1);
}
echo "<br>";

// create table to store donation details 
$donation_query = "CREATE TABLE donations (
    donationID INT(3) AUTO_INCREMENT,
    status VARCHAR(10), 
    userID VARCHAR(4),
    bookTitles VARCHAR(500),
    donationMethod VARCHAR(7),
    donationStreet VARCHAR(100),
    donationCity VARCHAR(20),
    donationState VARCHAR(20),
    donationPostcode INT(5),
    donationDate DATE,
    donationTime TIME,
    PRIMARY KEY (donationID)
    )";

// execute query to create donation table
// check if book table is successfully created
if (mysqli_query($handler1, $donation_query)) {
    echo "Successfully created donations table!";
} else {
    echo "Error creating donations table: " . mysqli_error($handler1);
}
echo "<br>";
?>