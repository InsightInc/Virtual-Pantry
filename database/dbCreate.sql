CREATE DATABASE IF NOT EXISTS VirtualPantryDB;
USE 'VirtualPantryDB';

CREATE TABLE PantryList (pantryid int NOT NULL AUTO_INCREMENT, u_id int NOT NULL, pid int, barcode int, 
						 price decimal(5,2), pname varchar(50), PRIMARY KEY(pantryid), FOREIGN KEY(u_id)
						 REFERENCES User(uid) ON DELETE CASCADE);

CREATE TABLE Ingredient (pid int, fat int, chol int, sodium int, carb int, protien int,
						 servsize int, price decimal(5,2), barcode int, cal int, exp date, 
						 pname varchar(50), PRIMARY KEY(pid));

CREATE TABLE ExpiredList (pid int, barcode int, price decimal(5,2), PRIMARY KEY(pid));

CREATE TABLE User (uid int NOT NULL AUTO_INCREMENT, fname varchar(30), lname varchar(30), email varchar(45), 
					password varchar(50), did int, srid int, PRIMARY KEY(uid));

CREATE TABLE SavedRecipes (srid int, rid int, rname varchar(50), ptime time, ringid int,
						   rprice decimal(5,2), PRIMARY KEY (srid, rid));

CREATE TABLE RecipeIngredient (ringid int, rid int, pid int, ingamt int, PRIMARY KEY(ringid));

CREATE TABLE DietaryRestrictions(did int, restrict varchar(50));

