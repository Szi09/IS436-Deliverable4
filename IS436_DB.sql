-- RESTAURANT ORDERING SYSTEM DATABASE

CREATE DATABASE RestaurantSystem;
USE RestaurantSystem;

-- STEP 1: USERS TABLE
CREATE TABLE Users (
    UserId INT NOT NULL PRIMARY KEY,
    Name VARCHAR(100),
    Email VARCHAR(100),
    Phone VARCHAR(20)
);

-- STEP 2: MENU ITEMS TABLE
CREATE TABLE MenuItems (
    ItemId INT NOT NULL PRIMARY KEY,
    ItemName VARCHAR(100),
    Price DECIMAL(10,2)
);

-- STEP 3: CART TABLE
CREATE TABLE Cart (
    CartId INT NOT NULL PRIMARY KEY,
    UserId INT,
    FOREIGN KEY (UserId) REFERENCES Users(UserId)
);

-- STEP 4: CART ITEMS TABLE
CREATE TABLE CartItems (
    CartItemId INT NOT NULL PRIMARY KEY,
    CartId INT,
    ItemId INT,
    Quantity INT,
    SavedForLater BOOLEAN,
    FOREIGN KEY (CartId) REFERENCES Cart(CartId),
    FOREIGN KEY (ItemId) REFERENCES MenuItems(ItemId)
);

-- STEP 5: ORDERS TABLE
CREATE TABLE Orders (
    OrderId INT NOT NULL PRIMARY KEY,
    UserId INT,
    OrderType VARCHAR(20),
    TotalAmount DECIMAL(10,2),
    Status VARCHAR(20),
    FOREIGN KEY (UserId) REFERENCES Users(UserId)
);

-- STEP 6: ORDER ITEMS TABLE
CREATE TABLE OrderItems (
    OrderItemId INT NOT NULL PRIMARY KEY,
    OrderId INT,
    ItemId INT,
    Quantity INT,
    PriceAtTime DECIMAL(10,2),
    FOREIGN KEY (OrderId) REFERENCES Orders(OrderId),
    FOREIGN KEY (ItemId) REFERENCES MenuItems(ItemId)
);

-- STEP 7: DELIVERY TABLE
CREATE TABLE Delivery (
    DeliveryId INT NOT NULL PRIMARY KEY,
    OrderId INT,
    Address VARCHAR(255),
    City VARCHAR(50),
    State VARCHAR(50),
    ZipCode VARCHAR(10),
    EstimatedTime INT,
    FOREIGN KEY (OrderId) REFERENCES Orders(OrderId)
);

-- INSERT DATA

-- USERS
INSERT INTO Users (UserId, Name, Email, Phone)
VALUES
(1, 'Ahmed Khan', 'ahmed.khan@email.com', '443-555-0199'),
(2, 'Sarah Johnson', 'sarah.johnson@email.com', '410-555-1122'),
(3, 'Michael Lee', 'michael.lee@email.com', '301-555-3344'),
(4, 'Jessica Brown', 'jessica.brown@email.com', '240-555-7788');

-- MENU ITEMS
INSERT INTO MenuItems (ItemId, ItemName, Price)
VALUES
(1, 'Chicken Shawarma Wrap', 9.99),
(2, 'Beef Shawarma Wrap', 10.99),
(3, 'Mixed Grill Plate', 14.99),
(4, 'Falafel Wrap', 8.49),
(5, 'Fries', 3.99),
(6, 'Hummus & Pita', 5.49),
(7, 'Baklava', 4.99);

-- CARTS
INSERT INTO Cart (CartId, UserId)
VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4);

-- CART ITEMS
INSERT INTO CartItems (CartItemId, CartId, ItemId, Quantity, SavedForLater)
VALUES
(1, 1, 1, 2, FALSE),
(2, 1, 5, 1, FALSE),
(3, 2, 2, 1, FALSE),
(4, 2, 7, 2, TRUE),
(5, 3, 3, 1, FALSE),
(6, 3, 6, 1, FALSE),
(7, 4, 4, 1, FALSE),
(8, 4, 7, 1, FALSE);

-- ORDERS
INSERT INTO Orders (OrderId, UserId, OrderType, TotalAmount, Status)
VALUES
(1, 1, 'delivery', 23.97, 'received'),
(2, 2, 'pickup', 10.99, 'preparing'),
(3, 3, 'pickup', 14.99, 'received'),
(4, 4, 'delivery', 18.47, 'received');

-- ORDER ITEMS
INSERT INTO OrderItems (OrderItemId, OrderId, ItemId, Quantity, PriceAtTime)
VALUES
(1, 1, 1, 3, 9.99),
(2, 1, 5, 1, 3.99),
(3, 2, 2, 1, 10.99),
(4, 3, 3, 1, 14.99),
(5, 4, 4, 1, 8.49),
(6, 4, 7, 1, 4.99);

-- DELIVERY
INSERT INTO Delivery (DeliveryId, OrderId, Address, City, State, ZipCode, EstimatedTime)
VALUES
(1, 1, '123 Maple Street', 'Baltimore', 'MD', '21201', 30),
(2, 4, '88 University Blvd', 'Catonsville', 'MD', '21228', 25);