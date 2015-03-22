USE 'VirtualPantryDB';

-- CREATE TABLE User (uid int NOT NULL AUTO_INCREMENT, fname varchar(30), lname varchar(30), email varchar(45), 
-- 					password varchar(50), did int, srid int, PRIMARY KEY(uid));

INSERT into User values(1, "Ashley", "Isles", "ashley@smu.edu", "test1", 1, 1),
(2, "Clay", "Lewis", "clay@smu.edu", "test1", 2, 2),
(3, "Nick", "Antonelli", "nick@smu.edu", "test1", 3, 3),
(4, "Alex", "Russell", "alex@smu.edu", "test1", 4, 4),
(5, "Conner", "Knuston", "conner@smu.edu", "test1", 5, 5);

-- CREATE TABLE PantryList (pantryid int NOT NULL AUTO_INCREMENT, u_id int NOT NULL, pid int, barcode int, 
-- 						 price decimal(5,2), pname varchar(50), PRIMARY KEY(pantryid), FOREIGN KEY(u_id)
-- 						 REFERENCES User(uid) ON DELETE CASCADE);

INSERT into Pantrylist values(1, 1, 40000000002, 1234321, 5.99, "Bacon Strips"),
(2, 2, 40000000002, 12343210, 5.99, "Bacon Strips"),
(3, 3, 40000000007, 24680864, 25.99, "1 lb Angus Steak"),
(4, 4, 40000000005, 28163834, 2.79, "Red Apple"),
(5, 5, 40000000003, 66996699, 3.27, "Pomegranate");

-- CREATE TABLE Ingredient (pid int, fat int, chol int, sodium int, carb int, protien int,
-- 						 servsize int, price decimal(5,2), barcode int, cal int, exp date, 
-- 						 pname varchar(50), PRIMARY KEY(pid));

-- CREATE TABLE ExpiredList (pid int, barcode int, price decimal(5,2), PRIMARY KEY(pid));

-- CREATE TABLE SavedRecipes (srid int, rid int, rname varchar(50), ptime time, ringid int,
-- 						   rprice decimal(5,2), PRIMARY KEY (srid, rid));

-- CREATE TABLE RecipeIngredient (ringid int, rid int, pid int, ingamt int, PRIMARY KEY(ringid));

-- CREATE TABLE DietaryRestrictions(did int, restrict varchar(50));



