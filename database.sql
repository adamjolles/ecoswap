CREATE TABLE User (
    UserID INT PRIMARY KEY AUTO_INCREMENT,
    Name VARCHAR(255),
    Email VARCHAR(255) UNIQUE,
    Password VARCHAR(255),
    Rating DECIMAL(3,2)
);

CREATE TABLE Item (
    ItemID INT PRIMARY KEY AUTO_INCREMENT,
    Title VARCHAR(255),
    Description TEXT,
    Category VARCHAR(255),
    `Condition` VARCHAR(255),
    UserID INT,
    FOREIGN KEY (UserID) REFERENCES User(UserID)
);

CREATE TABLE Exchange (
    ExchangeID INT PRIMARY KEY AUTO_INCREMENT,
    Item1ID INT,
    Item2ID INT,
    Status VARCHAR(255),
    FOREIGN KEY (Item1ID) REFERENCES Item(ItemID),
    FOREIGN KEY (Item2ID) REFERENCES Item(ItemID)
);

CREATE TABLE Review (
    ReviewID INT PRIMARY KEY AUTO_INCREMENT,
    ExchangeID INT,
    Rating DECIMAL(3,2),
    Comment TEXT,
    FOREIGN KEY (ExchangeID) REFERENCES Exchange(ExchangeID)
);
