-- SQL Script for Student Portal (Mini Project 2)
-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS student_portal;
USE student_portal;

-- 1. Create the 'users' table for Admin authentication
CREATE TABLE IF NOT EXISTS users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Create the 'students' table for student management
CREATE TABLE IF NOT EXISTS students (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    matrix_no VARCHAR(20) NOT NULL UNIQUE,
    course VARCHAR(100) NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Insert sample admin account
-- Default Username: admin
-- Default Password: password123 (hashed)
INSERT INTO users (username, password) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- 4. Insert sample student data
INSERT INTO students (name, matrix_no, course, image_path) VALUES 
('Alex Johnson', '20DDT23F1001', 'Information Technology (Digital Technology)', 'uploads/sample1.jpg'),
('Siti Aminah', '20DIT23F2005', 'Information Security', 'uploads/sample2.jpg');
