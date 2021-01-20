<?php
	define("DB_HOST", "localhost");
	define("USEMYSQL", TRUE);
		//not need for sqlite
		/*
			CREATE USER 'addressbook'@'localhost';
			GRANT ALL PRIVILEGES ON addressbook.* To 'addressbook'@'localhost' IDENTIFIED BY 'YOURSTRONGPASSWORD';
			create database addressbook;
			FLUSH PRIVILEGES;
		*/
		define("DB_PASS", "addressbook");
		define("DB_USER", "addressbook");
		define("DB_DB","addressbook");
	//
	define("I2PHTTPPROXY",'tcp://127.0.0.1:4444');
?>
