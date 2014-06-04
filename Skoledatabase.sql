DROP TABLE Has;

DROP TABLE Children;

DROP TABLE Users;

CREATE TABLE Users(
    email varchar(60) NOT NULL,
    password varchar(60) NOT NULL,
    firstName varchar(50),
    lastName varchar(50),
    tel INTEGER, 
    isAdmin char(1),
    salt CHAR(128) NOT NULL,
    PRIMARY KEY (email));
    
INSERT INTO Users VALUES('123@hotmail.com','c275a088774aa9684896e4a9be0ea5e3','Anders','Balling',13371337,'n', 123123132);
INSERT INTO Users VALUES('Eriklarsen@lel.dk','e10adc3949ba59abbe56e057f20f883e','Erik','Larsen',31453415,'n', 313123213);
INSERT INTO Users VALUES('lelele@wup.dk','7b217fc6f2690762d0ed3c4f7324a495','Hundrede P','Bror',12376512,'n', 131232321323);
INSERT INTO Users VALUES('nummerto@wup.dk','d38a132a07fe5dfe0e83272a2250f2cb','Jeg er','toeren',22222222,'n', 321321321321); 
INSERT INTO Users VALUES('Admin@lel.dk','89e38e6cb0a24671ff72148c28a7a175','Admin', NULL, NULL, 'y', 321321321321);
INSERT INTO Users VALUES('test@123.dk', 'e10adc3949ba59abbe56e057f20f883e', 'B�rge', 'B�versen', 12312345, 'n', 12312321321333); 
    
CREATE TABLE Children(
    cpr INTEGER,
    firstName varchar(50),
    lastName varchar(50),
    grade varchar(10),
    queueNumber INTEGER,
    PRIMARY KEY (cpr));
    
  
    
INSERT INTO Children VALUES(1234567890,'Andersbarn','Ballingbarn', 'bhkl', 1);
INSERT INTO Children VALUES(1812941738,'Nicolas','barn', 'bhkl', 2);
INSERT INTO Children VALUES(4141311233,'Helt hundrede','bror','2.kl',1);
INSERT INTO Children VALUES(8077380773,'To forældre','alligevel','2.kl',2);
    
CREATE TABLE Has(
    email varchar(60),
    cpr INTEGER,
    PRIMARY KEY(email, cpr),
    FOREIGN KEY (email) REFERENCES Users,
    FOREIGN KEY (cpr) REFERENCES Children);
 
INSERT INTO Has VALUES('123@hotmail.com',1234567890);
INSERT INTO Has VALUES('Eriklarsen@lel.dk',1812941738);
INSERT INTO Has Values('lelele@wup.dk',8077380773);
INSERT INTO Has Values('nummerto@wup.dk',8077380773);
INSERT INTO Has Values('123@hotmail.com',1812941738);
commit;

SELECT DISTINCT Children.lastName FROM Users, Children, Has
WHERE Children.cpr = 1234567890;

SELECT * FROM USERS;

SELECT DISTINCT Users.firstName, Users.lastName FROM USERS, HAS, CHILDREN
WHERE Children.cpr = 8077380773;
SELECT DISTINCT children.cpr, firstname, lastname 
FROM has, children WHERE email='123@hotmail.com' 
AND has.cpr = children.cpr