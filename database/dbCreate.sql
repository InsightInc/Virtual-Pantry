CREATE DATABASE IF NOT EXISTS VirtualPantryDB;
USE 'VirtualPantryDB';

DROP TABLE IF EXISTS PantryList;
DROP TABLE IF EXISTS User;
CREATE TABLE IF NOT EXISTS User (uid int NOT NULL AUTO_INCREMENT, fname varchar(30), lname varchar(30), email varchar(45), 
					password varchar(50), did int, srid int, PRIMARY KEY(uid));

CREATE TABLE IF NOT EXISTS PantryList (pantryid int NOT NULL AUTO_INCREMENT, uid int NOT NULL, pid varchar(20), barcode varchar(15), 
						 price decimal(5,2), pname varchar(50), PRIMARY KEY(pantryid), FOREIGN KEY(uid)
						 REFERENCES User(uid) ON DELETE CASCADE);

DROP TABLE IF EXISTS Ingredient;
CREATE TABLE IF NOT EXISTS Ingredient (pid varchar(20), fat int, chol int, sodium int, carb int, protien int,
						 servsize int, price decimal(5,2), barcode varchar(15), cal int, exp date, 
						 pname varchar(50), PRIMARY KEY(barcode));

DROP TABLE IF EXISTS ExpiredList;
CREATE TABLE IF NOT EXISTS ExpiredList (pid varchar(20), barcode varchar(15), price decimal(5,2), PRIMARY KEY(barcode));


DROP TABLE IF EXISTS SavedRecipes;
CREATE TABLE IF NOT EXISTS SavedRecipes (srid int, rid int, rname varchar(50), ptime time, ringid int,
						   rprice decimal(5,2), PRIMARY KEY (srid, rid));

DROP TABLE IF EXISTS RecipeIngredient;
CREATE TABLE IF NOT EXISTS RecipeIngredient (ringid int, rid int, pid int, ingamt int, PRIMARY KEY(ringid));

DROP TABLE IF EXISTS DietaryRestrictions;
CREATE TABLE IF NOT EXISTS DietaryRestrictions(did int, restricts varchar(50));

