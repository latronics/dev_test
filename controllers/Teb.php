<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Teb extends Controller {
	
	function Teb()
	{
		parent::Controller();
		
		

	}
	
	function index()
	{
		set_time_limit(60);
		ini_set('mysql.connect_timeout', 60);
		ini_set('max_execution_time', 60);  
		ini_set('default_socket_timeout', 60); 
		require_once($this->config->config['ebaypath'].'get-common/keysnew.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
		$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
		$requestXmlBody .= '<GetOrdersRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
		$requestXmlBody .= '<DetailLevel>ItemReturnAttributes</DetailLevel>';
		$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
		$requestXmlBody .= "<Version>$compatabilityLevel</Version><NumberOfDays>1</NumberOfDays>";
		$requestXmlBody .= '
		<IncludeFinalValueFee>'.TRUE.'</IncludeFinalValueFee>
		<NumberOfDays>1</NumberOfDays>	
		<Pagination><EntriesPerPage>100</EntriesPerPage></Pagination>
		</GetOrdersRequest>';	
		$verb = 'GetOrdersRequest';

		//Create a new eBay session with all details pulled in from included keys.php
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
				
		//send the request and get response
		$responseXml = $session->sendHttpRequest($requestXmlBody);
		if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
			die('<P>Error sending request');
		
		printcool ($responseXml);
	}
	

}