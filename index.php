
<?php

include "credentials.php";

//check if user and password are submitted
if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="Please enter username and password!"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'You need to authenticate, to use this service!';
    exit;
} 


//validate user and password
if($_SERVER['PHP_AUTH_USER'] != $cardiUser || $_SERVER['PHP_AUTH_PW'] != $cardiPass) {
    header('HTTP/1.0 401 Unauthorized');
    echo 'You need to authenticate, to use this service!';
    exit;
}


if($_SERVER['REQUEST_METHOD'] == 'GET'){

	echo "Hier könnte man von Hand auslösen, hab ich aber nicht implementiert.";

}else{
	if( strpos($_SERVER['CONTENT_TYPE'], "Content-Type: application/json" === false)){

		echo "Ich hätte gerne JSON";
	}else{
		processJSON();
	}
}

function processJSON(){
	$file = fopen("call.log","a");

	fwrite($file, "------------------------------------------\n");

	fwrite($file, date('Y-m-d H:i:s')."\n");

	fwrite($file, "\n\$_SERVER[]:\n");
	while (list($var,$value) = each ($_SERVER)) {
	  fwrite($file,  "\t$var => $value\n");
	}

	fwrite($file, "\ngetallheaders():\n");
	foreach (getallheaders() as $name => $value) {
	    #echo "$name: $value <br>\n";

	    fwrite($file, "\t$name: $value\n");



	}

	$json = file_get_contents('php://input');

	fwrite($file, "\n".$json."\n");




	$jarray = json_decode($json,true)[0];


	if(array_key_exists("GPS", $jarray)){

		$ret = ffaAllertGPS($jarray['GPS']['lat'], $jarray['GPS']['lng']);
		fwrite($file, "--REPLY FROM FFA SERVER:--\n");
		fwrite($file, $ret."\n");
		echo $ret;
	}

	if(array_key_exists("AEDCoverOpen", $jarray)){

		$ret = ffaAllertCoverOpen();
		fwrite($file, "--REPLY FROM FFA SERVER:--\n");
		fwrite($file, $ret."\n");
		echo $ret;
	}

	if(array_key_exists("AEDINMOTION", $jarray)){
		if($jarray['AEDINMOTION']['motionDetected'] == 1){

			$ret = ffaAllertMotion();
			fwrite($file, "--REPLY FROM FFA SERVER:--\n");
			fwrite($file, $ret."\n");
			echo $ret;
		}
	}

	fwrite($file, "------------------------------------------\n\n");
	fclose($file);
}




////////////////////////////////////////////////////////////////
//
//		FFAgent alarm   
//
//
////////////////////////////////////////////////////////////////


//Alarmed 1 of 1! CardiLink@Feuerwehr Aich (FFB) : registered alarm - created new mission because no mission was active. 
//Alarmed 1 of 1! CardiLink@Feuerwehr Aich (FFB) : registered alarm - updated mission. 
//Error while processing Alarm! Unknown selective call code CardiLink123 for Soft Gateway - Feuerwehr Aich (FFB)


function ffaAllert($payload){

	include "credentials.php";

	$localPath = explode('/', $_SERVER['SCRIPT_FILENAME']);
	array_pop($localPath);
	$localPath = implode('/', $localPath);

	$certPath = $localPath ."/" . $certName;


	//API URL
	$url = 'https://api.service.ff-agent.com/v1/WebService/triggerAlarm';

	//create a new cURL resource
	$callFFACH = curl_init($url);



	$hmac = hash_hmac("sha256",$webApiToken.$selectiveCallCode.$accessToken.$payload,$webAPIKey);

	$header = array(
	    "Content-Type:application/json",
	    "Accept:application/json",
	    "webApiToken:$webApiToken",
	    "accessToken:$accessToken",
	    "selectiveCallCode:$selectiveCallCode",
	    "hmac:$hmac"
	);

	curl_setopt($callFFACH, CURLOPT_HTTPHEADER, $header);//array('Content-Type:application/json'));


	curl_setopt($callFFACH, CURLOPT_SSLCERTTYPE, "P12");
	curl_setopt($callFFACH, CURLOPT_SSLCERT, $certPath);
	curl_setopt($callFFACH, CURLOPT_SSLCERTPASSWD, $certPassword);

	//return response instead of outputting
	curl_setopt($callFFACH, CURLOPT_RETURNTRANSFER, true);

	//attach encoded JSON string to the POST fields
	curl_setopt($callFFACH, CURLOPT_POSTFIELDS, $payload);

	//execute the POST request
	$result = curl_exec($callFFACH);



	if (!$result) {
	    die('Error: "' . curl_error($callFFACH) . '" - Code: ' . curl_errno($callFFACH));
	}


	curl_close($callFFACH);		//close cURL resource

	return $result;
}

function ffaAllertMotion(){
	return ffaAllert($payload = '{"keyword":"RD 2","message":"AED in Bewegung","note":"Gruppe AED Ersthelfer","location":"Gemeindehaus","district":"Aich","lat":"48.179014","lng":"11.1880683"}');
}

function ffaAllertGPS($lat, $lng){
	return ffaAllert($payload = '{"keyword":"RD 2","note":"Gruppe AED Ersthelfer","location":"Siehe Karte","lat":"'.$lat.'","lng":"'.$lng.'"}');
}

function ffaAllertCoverOpen(){
	return ffaAllert($payload = '{"keyword":"RD 2","message":"AED wurde geöffnet"}');
}


?>