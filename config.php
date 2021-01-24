<?php
	define("DB_HOST", "localhost");
//	define("USEMYSQL", TRUE); // deprecated, can be found in old commits
		//not need for sqlite
		/*
			CREATE USER 'addressbook'@'localhost';
			GRANT ALL PRIVILEGES ON addressbook.* To 'addressbook'@'localhost' IDENTIFIED BY 'YOURSTRONGPASSWORD';
			create database addressbook;
			FLUSH PRIVILEGES;
		*/
		define("DB_PASS", "realPassword39405%%");
		define("DB_USER", "addressbook");
		define("DB_DB","addressbook");
	//
	define("I2PHTTPPROXY",'tcp://127.0.0.1:4444');
?>
