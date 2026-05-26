CREATE DATABASE IF NOT EXISTS library_management_simple;
USE library_management_simple;

CREATE TABLE IF NOT EXISTS users (
    email VARCHAR(120) PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    pass VARCHAR(120) NOT NULL
);

CREATE TABLE IF NOT EXISTS students (
    email VARCHAR(120) PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    phone VARCHAR(40) NOT NULL,
    registration_date DATE NOT NULL
);

CREATE TABLE IF NOT EXISTS vendors (
    name VARCHAR(120) PRIMARY KEY,
    email VARCHAR(120) NOT NULL,
    phone VARCHAR(40) NOT NULL
);

CREATE TABLE IF NOT EXISTS publications (
    name VARCHAR(120) PRIMARY KEY,
    address VARCHAR(200) NOT NULL,
    description VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS subscriptions (
    title VARCHAR(120) PRIMARY KEY,
    amount DECIMAL(10,2) NOT NULL,
    number_of_days INT NOT NULL
);

CREATE TABLE IF NOT EXISTS books (
    book_name VARCHAR(150) PRIMARY KEY,
    book_image VARCHAR(255) DEFAULT '',
    author_name VARCHAR(120) NOT NULL,
    available_quantity INT NOT NULL DEFAULT 0,
    publication_name VARCHAR(120) NOT NULL,
    CONSTRAINT fk_books_publication
        FOREIGN KEY (publication_name) REFERENCES publications(name)
);

CREATE TABLE IF NOT EXISTS purchases (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_name VARCHAR(150) NOT NULL,
    vendor_name VARCHAR(120) NOT NULL,
    quantity INT NOT NULL,
    per_book_price DECIMAL(10,2) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    purchase_date DATE NOT NULL,
    CONSTRAINT fk_purchases_book
        FOREIGN KEY (book_name) REFERENCES books(book_name),
    CONSTRAINT fk_purchases_vendor
        FOREIGN KEY (vendor_name) REFERENCES vendors(name)
);

CREATE TABLE IF NOT EXISTS allotments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_name VARCHAR(150) NOT NULL,
    student_email VARCHAR(120) NOT NULL,
    subscription_title VARCHAR(120) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    allotment_date DATE NOT NULL,
    CONSTRAINT fk_allotments_book
        FOREIGN KEY (book_name) REFERENCES books(book_name),
    CONSTRAINT fk_allotments_student
        FOREIGN KEY (student_email) REFERENCES students(email),
    CONSTRAINT fk_allotments_subscription
        FOREIGN KEY (subscription_title) REFERENCES subscriptions(title)
);

INSERT INTO users (email, name, pass)
VALUES ('admin@example.com', 'Admin User', '$2y$10$wN/aX1vB6mYn8JdLoUxFvOu5L9yW9/hO8o1q.T1X8TzBf3gN5aJyG')
ON DUPLICATE KEY UPDATE name = VALUES(name), pass = VALUES(pass);

INSERT INTO subscriptions (title, amount, number_of_days)
VALUES
    ('Monthly', 100.00, 30),
    ('Quarterly', 250.00, 90),
    ('Yearly', 900.00, 365)
ON DUPLICATE KEY UPDATE
    amount = VALUES(amount),
    number_of_days = VALUES(number_of_days);
