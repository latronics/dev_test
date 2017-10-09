<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Captcha_model extends Model 
{
    function Captcha_model()
    {
        parent::Model();
		$this->load->library('grecaptcha');
		
    }

function DoCaptcha()
	{
			$this->mysmarty->assign("captcha", '<div class="g-recaptcha" data-sitekey="6LdpPw8TAAAAABHwEsqLRU8rCdWz-xTIpNffV0UI" data-callback="dogo"></div>');		
	}
	
function CheckCaptcha()
	{
	if (isset($_POST['g-recaptcha-response']) && $_POST['g-recaptcha-response'] != '')
	{		
	
		$url = 'https://www.google.com/recaptcha/api/siteverify';
		$params = array('response' => trim($_POST['g-recaptcha-response']),
						'secret' => '6LdpPw8TAAAAAD1aaU9hoSyCrAD0vBJQ6jGN6HB6',
						'remoteip' => $_SERVER['REMOTE_ADDR']
						);
		 $postData = '';
		 foreach($params as $k => $v) 
		   { 
			  $postData .= $k . '='.$v.'&'; 
		   }
		 rtrim($postData, '&');
		 
		$ch = curl_init();  
		 
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_HEADER, false); 
		curl_setopt($ch, CURLOPT_POST, count($postData));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);    
		 
		$output=curl_exec($ch);
		 
		curl_close($ch);
		$res = json_decode($output);
		if(isset($res->success) && (int)$res->success == 1) return TRUE;
		else return FALSE;	
	}
	else return FALSE;
}	
}
?>