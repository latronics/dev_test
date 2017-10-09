<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class wtf extends Controller {

	function wtf()
	{		
		parent::Controller();	
						
	}
		
	function index() 
	{
		$to      = 'errors@1websolutions.net';
		$headers = 'From: ERROR_REPORTER@'.$_SERVER['HTTP_HOST']. "\r\n" .
			'Reply-To: noreply@'.$_SERVER['HTTP_HOST']. "\r\n" .
			'X-Mailer: PHP/' . phpversion();
		if(isset($_SERVER['HTTP_REFERER'])) $referer = $_SERVER['HTTP_REFERER'];
		else $referer = 'Manual Input';
		$subject = 'WTF PERSON - '.$_SERVER['HTTP_HOST'].' - '.date("H:i:s d/m/Y");
		$msg = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'
		***WTF PERSON***

		Referer: 
		'.$referer.'
		User Agent: 
		'.$_SERVER['HTTP_USER_AGENT'].'
		IP: 
		'.$_SERVER['REMOTE_ADDR'].'
		Query String: 
		'.$_SERVER['QUERY_STRING'].'
		Date: 
		'.date("H:i:s d/m/Y").'
		';
		//mail($to, $subject, $msg, $headers);

		echo ' 
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>WHAT THE F$#% ARE YOU DOING HERE ?!</title>
		</head>
		
		<body style="background:#FFF; text-align:center; font-size:16px; font-weight:bolder; color:#F00; font-family:Verdana, Geneva, sans-serif">
		<br /><br />
		<img src="'.Site_url().'images/wtf.jpg" border="0" />
		<br /><br>
		WHAT THE F$#% ARE YOU DOING HERE ?!<br><br><br><br>
		'.$_SERVER['REMOTE_ADDR'].' LOGGED.
		
		</body>
		</html>
				
		
		
		';
	}

function joomla()
	{
		$to      = 'errors@1websolutions.net';
		$headers = 'From: ERROR_REPORTER@'.$_SERVER['HTTP_HOST']. "\r\n" .
			'Reply-To: noreply@'.$_SERVER['HTTP_HOST']. "\r\n" .
			'X-Mailer: PHP/' . phpversion();
		if(isset($_SERVER['HTTP_REFERER'])) $referer = $_SERVER['HTTP_REFERER'];
		else $referer = 'Manual Input';
		$subject = 'JOOMLA WTF PERSON - '.$_SERVER['HTTP_HOST'].' - '.date("H:i:s d/m/Y");
		$msg = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'
		***JOOMLA WTF PERSON***

		Referer: 
		'.$referer.'
		User Agent: 
		'.$_SERVER['HTTP_USER_AGENT'].'
		IP: 
		'.$_SERVER['REMOTE_ADDR'].'
		Query String: 
		'.$_SERVER['QUERY_STRING'].'
		Date: 
		'.date("H:i:s d/m/Y").'
		';
		//mail($to, $subject, $msg, $headers);
		
		echo '
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>THIS AIN\'T JOOMLA ! AND WHY THE F$#% ARE YOU TRYING TO FIND OUT ?!</title>
		</head>
		
		<body style="background:#FFF; text-align:center; font-size:16px; font-weight:bolder; color:#F00; font-family:Verdana, Geneva, sans-serif">
		<br /><br />
		<img src="'.Site_url().'images/wtf.jpg" border="0" />
		<br /><br>
		THIS AIN\'T JOOMLA !<br><br>
		AND WHY THE F$#% ARE YOU TRYING TO FIND OUT ?!<br><br><br><br>
		'.$_SERVER['REMOTE_ADDR'].' LOGGED.
		</body>
		</html>
		';
	}
}
