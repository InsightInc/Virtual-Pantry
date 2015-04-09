CREATE DATABASE IF NOT EXISTS VirtualPantryDB;
USE 'VirtualPantryDB';

DROP TABLE IF EXISTS PantryList;
DROP TABLE IF EXISTS User;
CREATE TABLE IF NOT EXISTS User (uid int NOT NULL AUTO_INCREMENT, fname varchar(30), lname varchar(30), email varchar(45), 
					password varchar(50), PRIMARY KEY(uid));

CREATE TABLE IF NOT EXISTS PantryList (uid int NOT NULL, pid varchar(20), barcode varchar(15), 
						 price decimal(5,2), pname varchar(100), PRIMARY KEY(uid, pname), FOREIGN KEY(uid)
						 REFERENCES User(uid) ON DELETE CASCADE);

DROP TABLE IF EXISTS Ingredient;
CREATE TABLE IF NOT EXISTS Ingredient (pid varchar(20), fat int, chol int, sodium int, carb int, protien int,
						 servsize int, price decimal(5,2), barcode varchar(15), cal int, exp date, 
						 pname varchar(100), PRIMARY KEY(barcode));

DROP TABLE IF EXISTS ExpiredList;
CREATE TABLE IF NOT EXISTS ExpiredList (pid varchar(20), barcode varchar(15), price decimal(5,2), PRIMARY KEY(barcode));


DROP TABLE IF EXISTS SavedRecipes;
CREATE TABLE IF NOT EXISTS SavedRecipes (uid int, rname varchar(100), rlink varchar(200), PRIMARY KEY (uid), FOREIGN KEY(uid)
										REFERENCES User(uid) ON DELETE CASCADE);

DROP TABLE IF EXISTS DietaryRestrictions;
CREATE TABLE IF NOT EXISTS DietaryRestrictions(uid int, restricts tinyint, PRIMARY KEY(uid), FOREIGN KEY(uid)
						 REFERENCES User(uid) ON DELETE CASCADE);

