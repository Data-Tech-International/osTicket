	<?php

	#
	# Configuration: Enter the url and key. That is it.
	#  url => URL to api/task/cron e.g #  http://yourdomain.com/support/api/tickets.json
	#  key => API's Key (see admin panel on how to generate a key)
	#  $data add custom required fields to the array.	
	#  Created by Ghanshyam Sharma [https://github.com/sharmaghanshyam]
	#  Create Multiple tickets at one glance
    
    require 'api.inc.php';
	$jsonReqUrl  = "php://input";
	$reqjson = file_get_contents($jsonReqUrl);
	$reqjsonDecode = json_decode($reqjson,true);

	// You must configure the url and key in the array below.

	$config = array(
			'url'=> HELPDESKURL.'/api/http.php/tickets.json',  // URL to site.tld/api/tickets.json
			'key'=> APIKEYVALUE  // API Key goes here
	);


	/*
	# Add in attachments here if necessary
	# Note: there is something with this wrong with the file attachment here it does not work.
	/* $data['attachments'][] =
	array('file.txt' =>
			'data:text/plain;base64;'
				.base64_encode(file_get_contents('/file.txt')));  // replace ./file.txt with /path/to/your/test/filename.txt

	*/
	#pre-checks
	function_exists('curl_version') or die('CURL support required');
	function_exists('json_encode') or die('JSON support required');

	#set timeout
	set_time_limit(300);
	$response = array();
	$i= 0;
	// Apply array loop below for each request  if  token is invalid it will respond error else it will provide you response with success
	foreach($reqjsonDecode as  $value)
	{
		#curl post
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $config['url']);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($value));
		curl_setopt($ch, CURLOPT_USERAGENT, 'osTicket API Client');
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Expect:', 'X-API-Key: '.$config['key']));
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$result=curl_exec($ch);
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$response[$i] = json_decode($result, true);
		curl_close($ch);
	 $i++;
	}

	echo json_encode($response);
	# Continue onward here if necessary. $ticket_id has the ID number of the
	# newly-created ticket

	function IsNullOrEmptyString($question){
		return (!isset($question) || trim($question)==='');
	}

	?>
