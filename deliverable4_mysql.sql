-- MySQL Database Schema for Restaurant Ordering System

-- 1. USERS table
CREATE TABLE USERS (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL
);

-- 2. MENU_ITEMS table
CREATE TABLE MENU_ITEMS (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL
);

-- 3. CART table
CREATE TABLE CART (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES USERS(user_id) ON DELETE CASCADE
);

-- 4. CART_ITEMS table
CREATE TABLE CART_ITEMS (
    cart_item_id INT AUTO_INCREMENT PRIMARY KEY,
    cart_id INT NOT NULL,
    item_id INT NOT NULL,
    quantity INT NOT NULL,
    saved_for_later TINYINT(1) DEFAULT 0,
    FOREIGN KEY (cart_id) REFERENCES CART(cart_id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES MENU_ITEMS(item_id)
);

-- 5. ORDERS table
CREATE TABLE ORDERS (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_type VARCHAR(20) NOT NULL CHECK (order_type IN ('delivery', 'pickup')),
    total_amount DECIMAL(10,2) NOT NULL,
    status VARCHAR(20) DEFAULT 'pending' CHECK (status IN ('pending', 'confirmed', 'preparing', 'ready', 'completed', 'cancelled')),
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES USERS(user_id)
);

-- 6. ORDER_ITEMS table
CREATE TABLE ORDER_ITEMS (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    item_id INT NOT NULL,
    quantity INT NOT NULL,
    price_at_time DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES ORDERS(order_id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES MENU_ITEMS(item_id)
);

-- 7. DELIVERY table
CREATE TABLE DELIVERY (
    delivery_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL UNIQUE,
    address VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    state VARCHAR(50) NOT NULL,
    zip_code VARCHAR(20) NOT NULL,
    estimated_time VARCHAR(100),
    FOREIGN KEY (order_id) REFERENCES ORDERS(order_id) ON DELETE CASCADE
);

-- Insert sample data
INSERT INTO USERS (name, email, phone) VALUES
('James Wilson', 'james.wilson@example.com', '301-555-2341'),
('Maria Garcia', 'maria.garcia@example.com', '443-555-7892'),
('David Chen', 'david.chen@example.com', '410-555-4563'),
('Lisa Rodriguez', 'lisa.rodriguez@example.com', '240-555-8904'),
('Michael Thompson', 'michael.thompson@example.com', '667-555-1235');

-- Insert menu items (8 shawarma restaurant items)
INSERT INTO MENU_ITEMS (name, price) VALUES
('Chicken Shawarma Wrap', 9.99),
('Beef Shawarma Wrap', 10.99),
('Lamb Shawarma Plate', 14.99),
('Falafel Wrap', 8.99),
('Hummus with Pita', 4.99),
('French Fries', 3.99),
('Baklava', 3.49),
('Mint Lemonade', 2.99);

-- Insert carts (one per user)
INSERT INTO CART (user_id) VALUES (1), (2), (3), (4), (5);

-- Insert cart items (sample shopping carts)
INSERT INTO CART_ITEMS (cart_id, item_id, quantity, saved_for_later) VALUES
(1, 1, 2, 0),
(1, 6, 1, 0),
(2, 2, 1, 0),
(2, 8, 2, 1),
(3, 4, 1, 0),
(4, 3, 1, 0),
(5, 5, 1, 0),
(5, 7, 2, 0);