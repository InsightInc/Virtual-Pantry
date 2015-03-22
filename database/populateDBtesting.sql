USE 'VirtualPantryDB';

-- CREATE TABLE User (uid int NOT NULL AUTO_INCREMENT, fname varchar(30), lname varchar(30), email varchar(45), 
-- 					password varchar(50), did int, srid int, PRIMARY KEY(uid));

INSERT into User values(1, "Ashley", "Isles", "ashley@smu.edu", "test1", 1, 1),
(2, "Clay", "Lewis", "clay@smu.edu", "test1", 2, 2),
(3, "Nick", "Antonelli", "nick@smu.edu", "test1", 3, 3),
(4, "Alex", "Russell", "alex@smu.edu", "test1", 4, 4),
(5, "Conner", "Knuston", "conner@smu.edu", "test1", 5, 5);

-- CREATE TABLE PantryList (pantryid int NOT NULL AUTO_INCREMENT, u_id int NOT NULL, pid varchar(20), barcode int, 
-- 						 price decimal(5,2), pname varchar(50), PRIMARY KEY(pantryid), FOREIGN KEY(u_id)
-- 						 REFERENCES User(uid) ON DELETE CASCADE);

INSERT into Pantrylist(uid, pid, barcode, pname) values(1, "Bacon", 035826034724, "Bacon"),
(1, "Oreo", 044000007492, "Oreo"),
(1,"Apple", 033383027814,"Apple"),
(1, "Pomegranate", 073296153637, "Pomegranate");




