	<?php
	#
	#!/usr/bin/php -q
	# Configuration: Enter the url and key. That is it.
	#  url => URL to api/task/cron e.g #  http://yourdomain.com/support/api/tickets.json
	#  key => API's Key (see admin panel on how to generate a key)
	#  $data add custom required fields to the array.
	#  Created by Ghanshyam Sharma [https://github.com/sharmaghanshyam]
	#  Create Multiple tickets at one glance

 	require 'api.inc.php';

	$jsonReqUrl  = "php://input";
	$reqjson = file_get_contents($jsonReqUrl);
	$reqjsonDecode = json_decode($reqjson, true);

	// You must configure the url and key in the array below.

	$config = array(
			'url'=> HELPDESKURL.'/api/http.php/tickets/reply.json',  // URL to site.tld/api/tickets.json
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
	$i=0;
	// Apply array loop below for each request  if  token is invalid it will respond error else it will provide you response with success

	$ticket = [
    "ticketNumber" => "",
    "msgId" => "",
    "a" => "reply",
    "emailreply" => "1",
    "emailcollab" => "1",
    "cannedResp" => "0",
    "draft_id" => "",
    "response" => "",
    "signature" => "none",
    "reply_status_id" => "1",
    "staffUserName" => "",
    "ip_address" => "::1",
    "attachments" => [],
    ];
	$staffUserName = "<username>"; # username for closing tickets - must be a valid osTicket user
    // $staffUserName = "test.user";
	$html     =  $reqjsonDecode['html'];
	$ticketsIdList =  $reqjsonDecode['ticketIds'];

	foreach($ticketsIdList as $ticket_id)
	{

		    # Load ticket and send response
            $ticket = new Ticket(0);
            $ticketd=Ticket::lookup($ticket_id);
			$ticketNumber = $ticketd->ht['number'];
			$status_id = $ticketd->ht['status_id'];
			if( $status_id != 3){
			$ticket_data['ticketNumber'] = $ticketNumber;
			$ticket_data['response'] = $html; // update response
			$ticket_data['reply_status_id'] = 3; // update status
		    $ticket_data['staffUserName'] = $staffUserName;

			#curl post
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $config['url']);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($ticket_data));
			curl_setopt($ch, CURLOPT_USERAGENT, 'osTicket API Client');
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Expect:', 'X-API-Key: '.$config['key']));
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			$result=curl_exec($ch);
			$scode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$status_message  = get_status_code_message($scode);

			$data = json_decode($result, TRUE);
			//$data['ticketNumber'] = $ticketNumber;
            $data["ticketId"] = $ticket_id;
			$data["status_code"] = $scode;
			$data["status_msg"] = $status_message;

			$response[$i] = $data;

			curl_close($ch);

			}else{
			$scode = 401;
			$status_message  = get_status_code_message($scode);
			$data["ticketId"] = $ticket_id;
			$data["status_code"] = $scode;
			$data["status_msg"] = $status_message;
			$response[$i] = $data;
			}
            $i++;

	}
	 echo json_encode($response);
	# Continue onward here if necessary. $ticket_id has the ID number of the
	# newly-created ticket

	function IsNullOrEmptyString($question){
		return (!isset($question) || trim($question)==='');
	}





	?>
