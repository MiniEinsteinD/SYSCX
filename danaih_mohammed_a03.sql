--DROP DATABASE IF EXISTS daniah_mohammed_syscx;
-- Create the database
CREATE DATABASE IF NOT EXISTS daniah_mohammed_syscx;

-- Use the database
USE daniah_mohammed_syscx;

-- Create table users_info
CREATE TABLE IF NOT EXISTS users_info (
    student_id INT(10) AUTO_INCREMENT PRIMARY KEY,
    student_email VARCHAR(150),
    first_name VARCHAR(150),
    last_name VARCHAR(150),
    dob DATE
);

-- Create table users_program
CREATE TABLE IF NOT EXISTS users_program (
    student_id INT(10),
    Program VARCHAR(50),
    FOREIGN KEY (student_id) REFERENCES users_info(student_id)
);

-- Create table users_avatar
CREATE TABLE IF NOT EXISTS users_avatar (
    student_id INT(10),
    avatar VARCHAR(1),
    FOREIGN KEY (student_id) REFERENCES users_info(student_id)
);

-- Create table users_address
CREATE TABLE IF NOT EXISTS users_address (
    student_id INT(10),
    street_number INT(5),
    street_name VARCHAR(150),
    city VARCHAR(30),
    province VARCHAR(2),
    postal_code VARCHAR(7),
    FOREIGN KEY (student_id) REFERENCES users_info(student_id)
);

-- Create table users_posts
CREATE TABLE IF NOT EXISTS users_posts (
    post_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT(10),
    new_post TEXT,
    post_date TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users_info(student_id)
);
CREATE TABLE IF NOT EXISTS users_passwords (
    student_id INT(10),
    FOREIGN KEY (student_id) REFERENCES users_info(student_id),
    password VARCHAR(255)
);
CREATE TABLE IF NOT EXISTS users_permissions (
    student_id INT(10),
    FOREIGN KEY (student_id) REFERENCES users_info(student_id),
    account_type INT(1) DEFAULT 1
);

-- Set auto-increment for student_id starting at 100100
ALTER TABLE users_info AUTO_INCREMENT = 100100;