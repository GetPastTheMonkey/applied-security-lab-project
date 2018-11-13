CREATE DATABASE asl_certdata;
USE asl_certdata;

CREATE TABLE certificates (
	serial_nr BIGINT(20) UNSIGNED PRIMARY KEY,
	user VARCHAR(64) NOT NULL,
	pkcs12 LONGBLOB NOT NULL,
	purpose TEXT NOT NULL,
	created DATETIME NOT NULL,
	revoked DATETIME DEFAULT NULL
);

CREATE USER 'asl_certdata_user'@'localhost' IDENTIFIED BY 'my_super_duper_password';

GRANT SELECT, INSERT, UPDATE (revoked) ON certificates TO 'asl_certdata_user'@'localhost';

SELECT 'Database setup of asl_certdata finished' AS ' ';
