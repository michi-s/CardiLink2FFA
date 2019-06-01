
<!DOCTYPE html>
<html lang="de">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" href="data:image/x-icon;," type="image/x-icon"> 
    <title>Testsite CardiLLink to FFA Relay</title>
  </head>
  <body>
    <!-- Sichtbarer Dokumentinhalt im body -->

    <form method="GET">
    	Bitte wählen Sie die gewünschte Option die getestet werden soll: 
    	<select name="action" >
    		<option>motion</option>
    		<option>gps</option>
    		<option>open</option>
    		<option>other</option>
    	</select>
    	<button type="submit">testen</button>
    </form>
    <br>

<?php


////////////////////////////////////////////////////////////////
//
//		Simulate Cardilink allert
//
//
////////////////////////////////////////////////////////////////

include "credentials.php";

$localPath = explode('/', $_SERVER['SCRIPT_NAME']);
array_pop($localPath);
$localPath = implode('/', $localPath);

$url = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['SERVER_NAME']."".$localPath ."/index.php" ;

//create a new cURL resource
$simCardiCH = curl_init($url);



if(isset($_GET['action'])){
	switch ($_GET['action']) {
		case 'motion':
			$simCardiJSON = '[
				  {
				    "AEDINMOTION": {
				      "@id": "aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee@runner0001",
				      "@context": "https://cardi-link.cloud/contexts/cardidb+json/communicator/messages/AEDINMOTION",
				      "timestamp": "2019-05-29T18:46:43.996Z",
				      "eventTrackingId": "11111111-2222-3333-4444-555555555555",
				      "sender": "10.0.0.0:54579",
				      "defibrillatorId": "000000001",
				      "communicatorId": "000000002",
				      "batteryLevel": "96%",
				      "motionDetected": true,
				      "cryptoHash": ""
				    },
				    "timestamp": "2019-05-29T18:46:44.918Z"
				  }
				]';
			break;
		
		case 'gps':
			$simCardiJSON = '[
				  {
				    "GPS": {
				      "@id": "aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee@runner0001",
				      "@context": "https://cardi-link.cloud/contexts/cardidb+json/communicator/messages/GPS",
				      "timestamp": "2019-05-29T18:50:20.792Z",
				      "eventTrackingId": "11111111-2222-3333-4444-555555555555",
				      "sender": "10.0.0.0:59488",
				      "defibrillatorId": "000000001",
				      "communicatorId": "000000002",
				      "batteryLevel": "96%",
				      "lat": "48.179014",
				      "lng": "'.sprintf('11.%03d', time() % 100).'",  //set different position every time you call
				      "cryptoHash": ""
				    },
				    "timestamp": "2019-05-29T18:50:22.101Z"
				  }
				]';
			break;

		case 'open':
			$simCardiJSON = '[
				  {
				    "AEDCoverOpen": {
				      "@id": "aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee@runner0001",
				      "@context": "https://cardi-link.cloud/contexts/cardidb+json/communicator/messages/AEDCoverOpen",
				      "timestamp": "2019-05-29T18:50:30.336Z",
				      "eventTrackingId": "11111111-2222-3333-4444-555555555555",
				      "sender": "10.0.0.0:53040",
				      "defibrillatorId": "000000001",
				      "communicatorId": "000000002",
				      "batteryLevel": "96%",
				      "cryptoHash": ""
				    },
				    "timestamp": "2019-05-29T18:50:31.261Z"
				  }
				]';
			break;

		case 'other':
			$simCardiJSON = '[
				  {
				    "DAILY_MessageReceived": {
				      "@id": "aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee@runner0001",
				      "@context": "https://cardi-link.cloud/contexts/cardidb+json/communicator/messages/DAILY",
				      "timestamp": "2019-05-29T01:58:24.770Z",
				      "eventTrackingId": "11111111-2222-3333-4444-555555555555",
				      "sender": "10.0.0.0:53327",
				      "defibrillatorId": "000000001",
				      "communicatorId": "000000002",
				      "batteryLevel": "96%",
				      "selfTest": "EFA88083FF80FF808080808080FF8080",
				      "retries": 1,
				      "success": true,
				      "AEDVisibileOnBluetooth": true,
				      "cryptoHash": ""
				    },
				    "timestamp": "2019-05-29T01:58:25.202Z"
				  }
				]';
			break;		
	}
}

//echo "<br>", $simCardiJSON, "<br>\n<br>\n";
//unset($simCardiJSON);

//send test message
if(isset($simCardiJSON)){

	$header = array(
	    "Content-Type:application/json",
	    "Accept:application/json",
	    "User-Agent: Michi Simulation",
	    "API-Key: $cardiAPIKey"
	);

	curl_setopt($simCardiCH, CURLOPT_HTTPHEADER, $header);

	curl_setopt($simCardiCH, CURLOPT_USERPWD, $cardiUser . ":" . $cardiPass);  


	//return response instead of outputting
	curl_setopt($simCardiCH, CURLOPT_RETURNTRANSFER, true);

	//attach encoded JSON string to the POST fields
	curl_setopt($simCardiCH, CURLOPT_POSTFIELDS, $simCardiJSON);

	//execute the POST request
	$result = curl_exec($simCardiCH);



	if (!$result) {
	    die('Error: "' . curl_error($simCardiCH) . '" - Code: ' . curl_errno($simCardiCH));
	}


	echo "Result:<br>", $result;


	//close cURL resource
	curl_close($simCardiCH);

}

?>



  </body>
</html>