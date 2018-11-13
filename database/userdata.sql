CREATE DATABASE asl_userdata;
USE asl_userdata;

CREATE TABLE users (
	uid VARCHAR(64) PRIMARY KEY,
	lastname VARCHAR(64) NOT NULL,
	firstname VARCHAR(64) NOT NULL,
	email VARCHAR(64) NOT NULL,
	pwd VARCHAR(64) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


INSERT INTO users VALUES ('fu','Fuerst','Andreas','fu@imovies.ch','6e58f76f5be5ef06a56d4eeb2c4dc58be3dbe8c7'),('db','Basin','David','db@imovies.ch','8d0547d4b27b689c3a3299635d859f7d50a2b805'),('ms','Schlaepfer','Michael','ms@imovies.ch','4d7de8512bd584c3137bb80f453e61306b148875'),('a3','Anderson','Andres Alan','and@imovies.ch','6b97f534c330b5cc78d4cc23e01e48be3377105b');

CREATE TABLE admins (
	admin_id VARCHAR(64) PRIMARY KEY,
	lastname VARCHAR(64) NOT NULL,
	firstname VARCHAR(64) NOT NULL,
	email VARCHAR(64) NOT NULL,
	pwd CHAR(128) NOT NULL,
	salt CHAR(128) NOT NULL
);

CREATE TABLE logins (
	uid VARCHAR(64) NOT NULL,
	session_id VARCHAR(128) NOT NULL,
	token CHAR(128) NOT NULL,
	ip_address VARCHAR(45) NOT NULL,
	timestamp DATETIME NOT NULL,
	expired DATETIME DEFAULT NULL,
	PRIMARY KEY(session_id, token)
);

CREATE USER 'asl_userdata_user'@'localhost' IDENTIFIED BY 'my_secure_password_that_i_have_to_change';

GRANT SELECT, UPDATE (lastname, firstname, email, pwd) ON users TO 'asl_userdata_user'@'localhost';
GRANT SELECT, UPDATE (lastname, firstname, email, pwd, salt) ON admins TO 'asl_userdata_user'@'localhost';
GRANT SELECT (uid, session_id, token, expired), INSERT ON logins TO 'asl_userdata_user'@'localhost';

SELECT 'Database setup of asl_userdata finished' AS ' ';
