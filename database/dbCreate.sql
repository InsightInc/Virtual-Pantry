CREATE DATABASE IF NOT EXISTS VirtualPantryDB;
USE 'VirtualPantryDB';

DROP TABLE IF EXISTS PantryList;
DROP TABLE IF EXISTS User;
CREATE TABLE IF NOT EXISTS User (uid int NOT NULL AUTO_INCREMENT, fname varchar(30), lname varchar(30), email varchar(45), 
					password varchar(50), PRIMARY KEY(uid));

CREATE TABLE IF NOT EXISTS PantryList (uid int NOT NULL, pname varchar(100), barcode varchar(15), price decimal(5,2),  
						              PRIMARY KEY(uid, pname), FOREIGN KEY(uid) REFERENCES User(uid) ON DELETE CASCADE);
						              
DROP TABLE IF EXISTS ShoppingList;
CREATE TABLE IF NOT EXISTS ShoppingList (uid int, name varchar(100), quantity int, FOREIGN KEY(uid));

DROP TABLE IF EXISTS Ingredient;
CREATE TABLE IF NOT EXISTS Ingredient (barcode varchar(15), fat int, chol int, sodium int, carb int, protien int,
						 			  servsize int, price decimal(5,2), cal int, exp date, pname varchar(100), manuf varchar(75),
						 			  PRIMARY KEY(barcode));

DROP TABLE IF EXISTS DietaryRestrictions;
CREATE TABLE IF NOT EXISTS DietaryRestrictions (uid int, restricts tinyint, PRIMARY KEY(uid, restricts), FOREIGN KEY(uid)
											   REFERENCES User(uid) ON DELETE CASCADE);

DROP TABLE IF EXISTS DietaryKey;
CREATE TABLE IF NOT EXISTS DietaryKey (id tinyint, name varchar(20), apicode varchar(30), PRIMARY KEY(id));

