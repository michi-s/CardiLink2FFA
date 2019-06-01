This software converts allerts from an [CardiLink](https://cardi-link.com/") AED to the [FF-Agent](http://www.ff-agent.com/).


#Installation
This PHP scripts need a webserver (e.g. Apache) with PHP and the php_curl module needs to be installed.

1. Change the credentials in <b>credentials.php</b> to yours. If your webserver is setup right, the php code (so are your cedentials)  is not showen to any user of your webserver. 
 To protect your cedentials the file is also forbidden in .htaccess
 
 2. FF-Agent needs client certificate authentication. You get a cerfificate named ffagent_crt.p12 from FF-Agent support. Place this file in the same folder as index.php. Again to protect this file .htaccess is used. If your rename this file, make sure you change the name in the .htaccess file accordingly.
 
 3. In your CardiLink user profile (top right), go to **Notifications** and add a **New Notification** choose **HTTPS Notification** as Notification Type and use the credentials that you have setup in your credentials.php. 
   Activate the following Message Types: **AED in Motion**, **New AED geo position** and **Defibrillator cover opened**. All other messages will be ignored.
   
 #Testing
 To check the connection to FF-Agent call the test.php and performe a test. After the test is successfull, remove the test.php or disable it in the .htaccess file.
 
 All connections to index.php are logged in the call.log file. Use this file for troubleshooting.
 
 #Final
 Open your browser and try to download your ffagent_crt.p12, if you can, then there is something wrong. You should get a forbidden message.
