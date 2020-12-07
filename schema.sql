CREATE DATABASE IF NOT EXISTS cctv2 DEFAULT CHARACTER SET utf8mb4 DEFAULT COLLATE utf8mb4_unicode_ci;
USE cctv2;
CREATE TABLE Date
(
   UID INT NOT NULL auto_increment,
   `date` date NOT NULL UNIQUE,
   PRIMARY KEY (UID)
);
CREATE TABLE Temperature
(
   dateID INT NOT NULL,
   time time NOT NULL,
   temp decimal
   (
      10,
      2
   )
   DEFAULT NULL,
   PRIMARY KEY
   (
      dateID,
      time
   ),
   FOREIGN KEY (dateID) REFERENCES Date (UID)
);
CREATE TABLE Camera
(
   UID INT NOT NULL auto_increment,
   name VARCHAR (15) NOT NULL UNIQUE,
   rootdir VARCHAR (255) NOT NULL UNIQUE,
   cachedir VARCHAR (255) NOT NULL UNIQUE,
   PRIMARY KEY (UID)
);
CREATE TABLE CameraDate
(
   UID INT NOT NULL auto_increment,
   cameraID INT NOT NULL,
   dateID INT NOT NULL,
   fetched BOOL NOT NULL DEFAULT FALSE,
   processed BOOL NOT NULL DEFAULT FALSE,
   PRIMARY KEY (UID),
   UNIQUE
   (
      dateID,
      cameraID
   ),
   FOREIGN KEY (dateID) REFERENCES Date (UID),
   FOREIGN KEY (cameraID) REFERENCES Camera (UID)
);
CREATE TABLE Photo
(
   UID INT NOT NULL auto_increment,
   cameraDateID INT NOT NULL,
   filepath VARCHAR (40) NOT NULL,
   time TIME NOT NULL,
   size FLOAT NOT NULL,
   thumbSize FLOAT,
   processed BOOL NOT NULL DEFAULT FALSE,
   PRIMARY KEY (UID),
   UNIQUE
   (
      cameraDateID,
      time
   ),
   FOREIGN KEY (cameraDateID) REFERENCES CameraDate (UID)
);
CREATE TABLE Object
(
   UID SMALLINT NOT NULL auto_increment,
   name VARCHAR (255) NOT NULL UNIQUE,
   PRIMARY KEY (UID)
);
CREATE TABLE Detection
(
   UID INT NOT NULL auto_increment,
   photoID INT NOT NULL,
   objectID SMALLINT NOT NULL,
   x1 SMALLINT NOT NULL,
   y1 SMALLINT NOT NULL,
   x2 SMALLINT NOT NULL,
   y2 SMALLINT NOT NULL,
   probability TINYINT NOT NULL,
   PRIMARY KEY (UID),
   UNIQUE
   (
      photoID,
      x1,
      y1,
      x2,
      y2
   ),
   FOREIGN KEY (photoID) REFERENCES Photo (UID),
   FOREIGN KEY (objectID) REFERENCES Object (UID)
);
CREATE TABLE Video
(
   UID INT NOT NULL auto_increment,
   cameraDateID INT NOT NULL,
   filepath VARCHAR (40) NOT NULL,
   time TIME NOT NULL,
   size FLOAT NOT NULL,
   duration SMALLINT,
   PRIMARY KEY (UID),
   UNIQUE
   (
      cameraDateID,
      time
   ),
   FOREIGN KEY (cameraDateID) REFERENCES CameraDate (UID)
);

INSERT INTO Camera(name,rootdir, cachedir) VALUES("Gate", "/mnt/hdd/tmp/GateCamera", "/mnt/hdd/tmp/cache/GateCamera");
INSERT INTO Camera(name,rootdir, cachedir) VALUES("Stairs", "/mnt/hdd/tmp/StairsCamera", "/mnt/hdd/tmp/cache/StairsCamera");