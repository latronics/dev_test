<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Cronn extends Controller {
	
	function Cronn()
	{
		parent::Controller();

	}
	
	function index()
	{	
	}
	
	function ProcessEbayCategories()
	{
	exit();
	$data = array ('msg_title' =>  'Cronn',
												'msg_body' => CurrentTime(),
												'msg_date' => CurrentTime()
												);	
				GoMail($data, $this->config->config['support_email']);

	set_time_limit(1500);
	//echo ini_get('mysql.connect_timeout'); // OUTPUT 60
	ini_set('mysql.connect_timeout',90);	
	//	echo ini_get('mysql.connect_timeout');

	require_once($this->config->config['ebaypath'].'get-common/keys.php');
	require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');

	$catTreeDoc = $this->_getEntireCategoryTree($devID, $appID, $certID, $compatabilityLevel,$this->config->config['ebaysiteid'], $userToken, $serverUrl);
	$categories = $catTreeDoc->getElementsByTagName('Category');

	foreach($categories as $cat)
	{	
		//get the ID and ParentID
		$catIDNode = $cat->getElementsByTagName('CategoryID');
		$catNameNode = $cat->getElementsByTagName('CategoryName');	
		$CategoryLevel = $cat->getElementsByTagName('CategoryLevel');
		$parentIDNode = $cat->getElementsByTagName('CategoryParentID');
		$leafNode = $cat->getElementsByTagName('LeafCategory');
		//if ID equals ParentID then it is a Top-Level category
		if ($leafNode->length > 0) $leafNode = 1;		
		else $leafNode = 0;
	
		$arr[] = array('catID'=>$catIDNode->item(0)->nodeValue,
														'catName'=>$catNameNode->item(0)->nodeValue,
														'CategoryLevel'=>$CategoryLevel->item(0)->nodeValue,
														'parentID'=>$parentIDNode->item(0)->nodeValue,
														'LeafCategory'=>$leafNode);
		
		/*$this->db->trans_start();
		$this->db->insert('ebaydata_categories', array('catID'=>$catIDNode->item(0)->nodeValue,
														'catName'=>$catNameNode->item(0)->nodeValue,
														'CategoryLevel'=>$CategoryLevel->item(0)->nodeValue,
														'parentID'=>$parentIDNode->item(0)->nodeValue,
														'LeafCategory'=>$leafNode)
													);	
		$this->db->trans_complete();*/
			
	}
	
		$doc = new DOMDocument('1.0');
		$doc->formatOutput = true;
		$root = $doc->createElement('root');
		$root = $doc->appendChild($root);
		foreach($arr as $key=>$value)
		{
		   $em = $doc->createElement($key);       
		   $text = $doc->createTextNode($value);
		   $em->appendChild($text);
		   $root->appendChild($em);		
		}			
		
		$doc->save($this->config->config['ebaypath'].'/ebayxmls/SiteCates.xml');
	
	echo 'COMPLETE';
	
	}

function _getEntireCategoryTree($devID, $appID, $certID, $compatabilityLevel, $siteID, $userToken, $serverUrl)
{
	//Build the request Xml string
	$requestXmlBody = '<?xml version="1.0" encoding="utf-8" ?>';
	$requestXmlBody .= '<GetCategoriesRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
	$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
	$requestXmlBody .= "<DetailLevel>ReturnAll</DetailLevel>"; //get the entire tree
	$requestXmlBody .= "<Item><Site>$siteID</Site></Item>";
	$requestXmlBody .= "<ViewAllNodes>1</ViewAllNodes>"; //Gets all nodes not just leaf nodes
	$requestXmlBody .= '</GetCategoriesRequest>';
	
	//Create a new eBay session with all details pulled in from included keys.php
	$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $siteID, 'GetCategories');
	$responseXml = $session->sendHttpRequest($requestXmlBody);
	if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
		die('<P>Error sending request');
	
	//Xml string is parsed and creates a DOM Document object
	$responseDoc = new DomDocument();
	$responseDoc->loadXML($responseXml);
	
	//save the tree to local file
	//$responseDoc->save('CatTree.xml');
	
	//return the DOM Document
	return $responseDoc;
}


function _TrueString($str = '')
{
	if ($str =='true') return 1;
	else return 0;
}
function DoEbayCategories($step = 1)
	{
	set_time_limit(1500);
	ini_set('mysql.connect_timeout',90);

	$xml = simplexml_load_file($this->config->config['ebaypath'].'/ebayxmls/CatTree.xml');
	foreach ($xml->CategoryArray->Category as $cat)
	{
		$this->db->insert('ebaydata_categories', 
		array('catID'=>(int)$cat->CategoryID,
		  	  'catName'=>(string)$cat->CategoryName,
			  'CategoryLevel'=>(int)$cat->CategoryLevel,
			  'parentID'=>(int)$cat->CategoryParentID,
		 	  'LeafCategory'=> $this->_TrueString($cat->LeafCategory))
			 );			
	}	
	}
}