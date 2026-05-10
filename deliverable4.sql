DROP TABLE IF EXISTS DELIVERY;
DROP TABLE IF EXISTS ORDER_ITEMS;
DROP TABLE IF EXISTS ORDERS;
DROP TABLE IF EXISTS CART_ITEMS;
DROP TABLE IF EXISTS CART;
DROP TABLE IF EXISTS MENU_ITEMS;
DROP TABLE IF EXISTS USERS;

CREATE TABLE USERS (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL
);

CREATE TABLE MENU_ITEMS (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description VARCHAR(255),
    price DECIMAL(10,2) NOT NULL,
    category_id INT DEFAULT 1,
    category_name VARCHAR(100) DEFAULT 'Main Dishes'
);

CREATE TABLE CART (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES USERS(user_id) ON DELETE CASCADE
);

CREATE TABLE CART_ITEMS (
    cart_item_id INT AUTO_INCREMENT PRIMARY KEY,
    cart_id INT NOT NULL,
    item_id INT NOT NULL,
    quantity INT NOT NULL,
    saved_for_later TINYINT(1) DEFAULT 0,
    FOREIGN KEY (cart_id) REFERENCES CART(cart_id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES MENU_ITEMS(item_id)
);

CREATE TABLE ORDERS (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_type VARCHAR(20) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status VARCHAR(20) DEFAULT 'pending',
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES USERS(user_id)
);

CREATE TABLE ORDER_ITEMS (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    item_id INT NOT NULL,
    quantity INT NOT NULL,
    price_at_time DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES ORDERS(order_id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES MENU_ITEMS(item_id)
);

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

INSERT INTO USERS (name, email, phone) VALUES
('James Wilson', 'james.wilson@example.com', '301-555-2341'),
('Maria Garcia', 'maria.garcia@example.com', '443-555-7892');

INSERT INTO MENU_ITEMS (name, description, price, category_id, category_name) VALUES
('BBQ Chicken Pizza', 'Smoky BBQ chicken pizza.', 14.99, 1, 'Pizza'),
('Caesar Salad', 'Fresh Caesar salad.', 9.99, 2, 'Salads'),
('French Fries', 'Golden crispy fries.', 3.99, 3, 'Sides'),
('Garlic Bread', 'Toasted garlic bread.', 4.99, 3, 'Sides'),
('Greek Salad', 'Greek salad with feta.', 8.99, 2, 'Salads'),
('Margherita Pizza', 'Classic margherita pizza.', 12.99, 1, 'Pizza'),
('Spaghetti Bolognese', 'Pasta with meat sauce.', 13.99, 4, 'Pasta'),
('Tiramisu', 'Italian dessert.', 6.99, 5, 'Dessert');

