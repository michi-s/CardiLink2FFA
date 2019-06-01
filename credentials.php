<?php
	//FF-Agent credentials
	$webAPIKey   		= "11111111-2222-3333-4444-555555555555";
	$webApiToken 		= "11111111-2222-3333-4444-555555555555";
	$accessToken 		= "11111111-2222-3333-4444-555555555555";
	$selectiveCallCode 	= "Name der Schleife";
	$certName			= "ffagent_crt.p12";  //Must be .p12 (PKCS12) file. If you change this file name, change it also in .htaccess!
	$certPassword		= "MySecretPassword"; //default from FF-Agent



	//Credentials for CardiLink. You can set them as you like, but you need to set them in the CardiLink HTTP notification settings.
	$cardiUser = "username";
	$cardiPass = "password";
	$cardiAPIKey = "1234"; // currently not evaluated

?>