<?php
/*********************************************************************
    api.inc.php

    File included on every API page...handles common includes.

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
file_exists('../main.inc.php') or die('System Error');

/*
 * Why API_SESSION ??
 * It indicates that the session is API - which session handler should handle as
 * stateless for new sessions.
 * Existing session continue to work as expected - this it's important for
 * SSO authentication, which uses /api/auth/* endpoints. Such calls are not
 * stateless.
 *
 */
define('API_SESSION', true);

// APICALL const.
define('APICALL', true);
define('HELPDESKURL', 'http://localhost:8012/migration');
define('APIKEYVALUE', '6DB0650B63D2A7C61817B42DB439A2F0'); 
require_once('../main.inc.php');
require_once(INCLUDE_DIR.'class.http.php');
require_once(INCLUDE_DIR.'class.api.php');

function get_status_code_message($code){
		
		if($code == 200){			
			$status_message =  "Reply posted successfully";
		}		
		if($code == 203){			
			$status_message =  "Non-Authoritative Information";
		}
        if($code == 400){			
			$status_message =  "Bad Request";
		}		
		if($code == 401){			
			$status_message =  "Unauthorized Access";
		}		
		if($code == 403){			
			$status_message =  "Forbidden";
		}
        if($code == 404){			
			$status_message =  "Ticket Id Not Found";
		}		
		if($code == 500){			
			$status_message =  "Try again later or contact your system administrator";
		}
		 
		return $status_message;
	}

?>
