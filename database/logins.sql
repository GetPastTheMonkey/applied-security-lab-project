CREATE TABLE logins (
	uid VARCHAR(64) NOT NULL,
	session_id VARCHAR(128) NOT NULL,
	token VARCHAR(128) NOT NULL,
	ip_address VARCHAR(45) NOT NULL,
	timestamp DATETIME NOT NULL,
	expired DATETIME DEFAULT NULL,
	PRIMARY KEY(session_id, token)
);