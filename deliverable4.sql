-- Deliverable 4 – Data Modeling and Starting Design
-- Group: H
-- Project: Web-Based Online Ordering System for Small Restaurant
-- Database: PostgreSQL
-- File: deliverable4.sql

-- ============================================================================

-- 1. USERS table
CREATE TABLE USERS (
    user_id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL
);

-- 2. MENU_ITEMS table
CREATE TABLE MENU_ITEMS (
    item_id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL
);

-- 3. CART table
CREATE TABLE CART (
    cart_id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES USERS(user_id) ON DELETE CASCADE
);

-- 4. CART_ITEMS table
CREATE TABLE CART_ITEMS (
    cart_item_id SERIAL PRIMARY KEY,
    cart_id INTEGER NOT NULL REFERENCES CART(cart_id) ON DELETE CASCADE,
    item_id INTEGER NOT NULL REFERENCES MENU_ITEMS(item_id),
    quantity INTEGER NOT NULL,
    saved_for_later BOOLEAN DEFAULT FALSE
);

-- 5. ORDERS table
CREATE TABLE ORDERS (
    order_id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES USERS(user_id),
    order_type VARCHAR(20) NOT NULL CHECK (order_type IN ('delivery', 'pickup')),
    total_amount DECIMAL(10,2) NOT NULL,
    status VARCHAR(20) DEFAULT 'pending' CHECK (status IN ('pending', 'confirmed', 'preparing', 'ready', 'completed', 'cancelled')),
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 6. ORDER_ITEMS table
CREATE TABLE ORDER_ITEMS (
    order_item_id SERIAL PRIMARY KEY,
    order_id INTEGER NOT NULL REFERENCES ORDERS(order_id) ON DELETE CASCADE,
    item_id INTEGER NOT NULL REFERENCES MENU_ITEMS(item_id),
    quantity INTEGER NOT NULL,
    price_at_time DECIMAL(10,2) NOT NULL
);

-- 7. DELIVERY table
CREATE TABLE DELIVERY (
    delivery_id SERIAL PRIMARY KEY,
    order_id INTEGER NOT NULL UNIQUE REFERENCES ORDERS(order_id) ON DELETE CASCADE,
    address VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    state VARCHAR(50) NOT NULL,
    zip_code VARCHAR(20) NOT NULL,
    estimated_time VARCHAR(100)
);

-- =======================================================================================

-- Insert users (5 customers)
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
(1, 1, 2, FALSE),
(1, 6, 1, FALSE),
(2, 2, 1, FALSE),
(2, 8, 2, TRUE),
(3, 4, 1, FALSE),
(4, 3, 1, FALSE),
(5, 5, 1, FALSE),
(5, 7, 2, FALSE);

-- Insert orders (5 orders across different users)
INSERT INTO ORDERS (user_id, order_type, total_amount, status) VALUES
(1, 'delivery', 23.97, 'completed'),
(2, 'pickup', 10.99, 'confirmed'),
(3, 'delivery', 8.99, 'preparing'),
(4, 'pickup', 14.99, 'ready'),
(1, 'delivery', 5.98, 'pending');

-- Insert order items (items within each order)
INSERT INTO ORDER_ITEMS (order_id, item_id, quantity, price_at_time) VALUES
(1, 1, 2, 9.99),
(1, 6, 1, 3.99),
(2, 2, 1, 10.99),
(3, 4, 1, 8.99),
(4, 3, 1, 14.99),
(5, 6, 1, 3.99),
(5, 5, 1, 1.99);

-- Insert delivery records (only for delivery-type orders: order_id 1, 3, 5)
INSERT INTO DELIVERY (order_id, address, city, state, zip_code, estimated_time) VALUES
(1, '742 Evergreen Terrace', 'Springfield', 'MD', '21201', '25-35 minutes'),
(3, '221B Baker Street', 'Baltimore', 'MD', '21218', '15-20 minutes'),
(5, '742 Evergreen Terrace', 'Springfield', 'MD', '21201', '30-40 minutes');

-- =====================================================================================================

-- VERIFICATION QUERIES (run after insert to confirm)
-- SELECT * FROM USERS;
-- SELECT * FROM MENU_ITEMS;
-- SELECT * FROM CART;
-- SELECT * FROM CART_ITEMS;
-- SELECT * FROM ORDERS;
-- SELECT * FROM ORDER_ITEMS;
-- SELECT * FROM DELIVERY;
