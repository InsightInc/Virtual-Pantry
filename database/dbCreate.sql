CREATE DATABASE IF NOT EXISTS VirtualPantryDB;
USE 'VirtualPantryDB';

CREATE TABLE IF NOT EXISTS User (uid int NOT NULL AUTO_INCREMENT, fname varchar(30), lname varchar(30), email varchar(45), 
					password varchar(50), did int, srid int, PRIMARY KEY(uid));

CREATE TABLE IF NOT EXISTS PantryList (pantryid int NOT NULL AUTO_INCREMENT, u_id int NOT NULL, pid varchar(20), barcode int, 
						 price decimal(5,2), pname varchar(50), PRIMARY KEY(pantryid), FOREIGN KEY(u_id)
						 REFERENCES User(uid) ON DELETE CASCADE);

CREATE TABLE IF NOT EXISTS Ingredient (pid varchar(20), fat int, chol int, sodium int, carb int, protien int,
						 servsize int, price decimal(5,2), barcode int, cal int, exp date, 
						 pname varchar(50), PRIMARY KEY(barcode));

CREATE TABLE IF NOT EXISTS ExpiredList (pid varchar(20), barcode int, price decimal(5,2), PRIMARY KEY(barcode));



CREATE TABLE IF NOT EXISTS SavedRecipes (srid int, rid int, rname varchar(50), ptime time, ringid int,
						   rprice decimal(5,2), PRIMARY KEY (srid, rid));

CREATE TABLE IF NOT EXISTS RecipeIngredient (ringid int, rid int, pid int, ingamt int, PRIMARY KEY(ringid));

CREATE TABLE IF NOT EXISTS DietaryRestrictions(did int, restricts varchar(50));

