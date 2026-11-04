DROP DATABASE IF EXISTS eventplanner;
CREATE DATABASE eventplanner;
USE eventplanner;
CREATE TABLE users(id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(60),email VARCHAR(80) UNIQUE,password VARCHAR(255),role ENUM('user','admin') DEFAULT 'user');
CREATE TABLE options(id INT AUTO_INCREMENT PRIMARY KEY,event_type VARCHAR(20),category VARCHAR(20),name VARCHAR(80),details VARCHAR(255),price INT,image VARCHAR(60));
CREATE TABLE plans(user_id INT PRIMARY KEY,event_type VARCHAR(20),option_ids VARCHAR(255));
CREATE TABLE bookings(id INT AUTO_INCREMENT PRIMARY KEY,user_id INT,event_type VARCHAR(20),event_date DATE,venue VARCHAR(100),phone VARCHAR(15),guests INT,option_ids VARCHAR(255),total INT,status ENUM('Pending','Approved','Rejected','Cancelled') DEFAULT 'Pending',created TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
INSERT INTO options(event_type,category,name,details,price,image) VALUES
('Birthday','Decoration','Balloon Arch','Colourful balloon arch with photo backdrop',8000,'balloons'),
('Birthday','Decoration','Neon Party Theme','Neon lights, banners and table setup',12000,'neon'),
('Birthday','Cake','Chocolate Truffle Cake','2 kg chocolate truffle cake',1800,'chocolatecake'),
('Birthday','Cake','Fondant Theme Cake','3 kg custom fondant cake',4500,'fondant'),
('Birthday','Host','Kids Party Host','Games, music and emcee for 3 hours',6000,'host'),
('Birthday','Photography','Candid Photographer','3 hours, 150 edited photos',7000,'camera'),
('Birthday','Catering','Snacks Buffet','Veg snacks and drinks, per event',15000,'snacks'),
('Wedding','Decoration','Floral Mandap','Fresh flower mandap and stage',60000,'flowers'),
('Wedding','Decoration','Fairy Light Hall','Full hall fairy light canopy',35000,'lights'),
('Wedding','Cake','Three-tier Cake','5 kg three-tier wedding cake',9000,'weddingcake'),
('Wedding','Host','Wedding Anchor','Bilingual anchor for ceremony and reception',15000,'anchor'),
('Wedding','Photography','Photo + Video Team','Full day photos, video and drone',45000,'wedding'),
('Wedding','Catering','Banquet Catering','Veg banquet for 300 guests',150000,'banquet'),
('Wedding','Catering','Live Counters','Chaat and dessert live counters',40000,'dessert');
INSERT INTO options(event_type,category,name,details,price,image) VALUES
('Anniversary','Decoration','Candlelight Setup','Rose petals, candles and fairy lights',9000,'candles'),
('Anniversary','Cake','Heart Red Velvet Cake','1.5 kg heart-shaped red velvet cake',2200,'redvelvet'),
('Anniversary','Host','Live Singer','1 hour live acoustic set',10000,'singer'),
('Anniversary','Photography','Couple Photoshoot','2 hours, 80 edited photos',8000,'couple'),
('Anniversary','Catering','Candlelight Dinner','3-course dinner for 2 to 20 guests',12000,'dinner'),
('Baby Shower','Decoration','Pastel Balloon Theme','Pastel balloons, banners and photo corner',9000,'pastel'),
('Baby Shower','Cake','Cupcake Tower','24 themed cupcakes',3000,'cupcakes'),
('Baby Shower','Host','Games Host','Baby shower games and prizes, 2 hours',5000,'games'),
('Baby Shower','Catering','Tea and Snacks','Finger snacks and drinks',14000,'tea');
