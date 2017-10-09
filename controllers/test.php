<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

////MITKO
/// TEST CONTROLLER FOR CREATION OF PDF...

class test extends Controller {

	function test()
	{		
		parent::Controller();	
					
	}
	function trans($rec = 0)
	{
 
	if ((int)$rec > 0)
		{
		set_time_limit(120);
		ini_set('mysql.connect_timeout', 120);
		ini_set('max_execution_time', 120);  
		ini_set('default_socket_timeout', 120); 
		
		require_once($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
		
		$verb = 'GetSellingManagerSaleRecord';
		$compatabilityLevel = 959;
		//Create a new eBay session with all details pulled in from included keys.php
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
		
		$this->db->select('  itemid, transid, shipping, tracking, asc, ssc');	
		$this->db->where('rec', (int)$rec);
		$q = $this->db->get('ebay_transactions');
		
		if ($q->num_rows() > 0) 
		{
			$t = $q->row_array();

				$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
				$requestXmlBody .= '<GetSellingManagerSaleRecordRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
				$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
				$requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';
				$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
				$requestXmlBody .= '<ErrorHandling>BestEffort</ErrorHandling>';
				$requestXmlBody .= "<ItemID>$t[itemid]</ItemID>";
				$requestXmlBody .= "<TransactionID>$t[transid]</TransactionID>";
				$requestXmlBody .= "<Version>$compatabilityLevel</Version>";
				$requestXmlBody .=  '</GetSellingManagerSaleRecordRequest>';			

				//send the request and get response
				$responseXml = $session->sendHttpRequest($requestXmlBody);
				if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
					die('<P>Error sending request');
				
				$xml = simplexml_load_string($responseXml);//printcool($xml);
				$item = $xml->SellingManagerSoldOrder;
			
				if ($item)
				{
					printcool ($t, '', 'LOCAL VALUES');
					printcool ($item,'', 'EBAY Values');
				}
				
		}
		
		
		
		
		}
	}
	function index() 
	{
	
	  $this->load->helper('explore');
			$data['var1']= '11112222';
			$data['var2'] = 'lalala';
			$d[0] = $data;
			$d[1] = $data;
			explore($data);
	}
	function tpl($id = 16754)
	{
		$this->load->model('Myebay_model'); 
		$item = $this->Myebay_model->GetItem($id);	
		
		$this->mysmarty->assign('displays', $item);
		echo $this->mysmarty->fetch('myebay/myebay_tpl.html');
		
	}
	function dbtest()
	{
		$query = $this->db->query('select bcn, datato from warehouse_log where field = "status" and year=2016 and month between 1 and 12 and day between 1 and 31 order by bcn ASC, year ASC, month ASC, day ASC');
		printcool ($query->num_rows());	
	}
	function convert()
	{
		
		/*
		
		$returndata[] = array("<a target=\"_blank\" href=\"/Mywarehouse/gotobcn/".cstr($l['bcn'])."\"><img src=\"/images/admin/table.png\" border=\"0\"></a><a target=\"_blank\" href=\"/Myebay/Search/".cstr($l['listingid'])."\"><img src=\"/images/admin/b_search.png\" border=\"0\"></a><a target=\"_blank\" href=\"/Myebay/ShowOrder/".cstr($l['sold_id'])."/".cstr($l['channel'])."\"><img src=\"/images/admin/shoppingbag.png\" border=\"0\"></a>", cstr($l['bcn']), cstr($l['oldbcn']), cstr($l['title']), cstr($l['location']),cstr($audit),cstr($l['status']),cstr($l['listed']),cstr($l['listed_date']),cstr($l['sold_date']),cstr($l['sold']), cstr($l['sold_id']), cstr($l['soldqn']),cstr($l['paid']),cstr($l['shipped']), cstr($l['shipped_actual']),cstr($l['shipped_inbound']),cstr($l['ordernotes']),cstr($l['sellingfee']), cstr($l['netprofit']),cstr($l['cost']),cstr($l['aupdt']));
				}
				else
				{									
					$returndata[] = array("<a target=\"_blank\" href=\"/Mywarehouse/gotobcn/".cstr($l['bcn'])."\"><img src=\"/images/admin/table.png\" border=\"0\"></a><a target=\"_blank\" href=\"/Myebay/Search/".cstr($l['listingid'])."\"><img src=\"/images/admin/b_search.png\" border=\"0\"></a><a target=\"_blank\" href=\"/Myebay/ShowOrder/".cstr($l['sold_id'])."/".cstr($l['channel'])."\"><img src=\"/images/admin/shoppingbag.png\" border=\"0\"></a>", cstr($l['bcn']), cstr($l['oldbcn']), cstr($l['title']), cstr($l['location']), cstr($audit),cstr($l['status']),cstr($l['listed']), cstr($l['listed_date']), cstr($l['sold_date']), cstr($l['sold']),  cstr($l['sold_id']),  cstr($l['soldqn']), cstr($l['paid']),  cstr($l['shipped']), cstr($l['shipped_actual']),  cstr($l['shipped_inbound']), cstr($l['ordernotes']), cstr($l['aupdt']));						
				}				
			}	
			if (count($list['data']) > 0)
			{
			foreach ($returndata as $r)
				{
					$loaddata .= "["; 
					foreach ($r as $rr)
					{
						$loaddata .= "'".$rr."',"; 
					}
					$loaddata .= "],"; 
					
				}	
			}
			if ($return)
			{
				//echo '['.rtrim($loaddata, ',').']';
				echo json_encode($returndata);
				exit();
		
		*/
		
	$loaddata .= "['<a target=\"_blank\" href=\"/Mywarehouse/gotobcn/".cstr($l['bcn'])."\"><img src=\"/images/admin/table.png\" border=\"0\"></a><a target=\"_blank\" href=\"/Myebay/Search/".cstr($l['listingid'])."\"><img src=\"/images/admin/b_search.png\" border=\"0\"></a><a target=\"_blank\" href=\"/Myebay/ShowOrder/".cstr($l['sold_id'])."/".cstr($l['channel'])."\"><img src=\"/images/admin/shoppingbag.png\" border=\"0\"></a>', '".cstr($l['bcn'])."', '".cstr($l['oldbcn'])."', '".cstr($l['title'])."', '".cstr($l['location'])."', '".cstr($audit)."', '".cstr($l['status'])."','".cstr($l['listed'])."', '".cstr($l['listed_date'])."', '".cstr($l['sold_date'])."', '".cstr($l['sold'])."', '".cstr($l['sold_id'])."','".cstr($l['soldqn'])."', '".cstr($l['paid'])."',  '".cstr($l['shipped'])."', '".cstr($l['shipped_actual'])."', '".cstr($l['shipped_inbound'])."', '".cstr($l['ordernotes'])."','".cstr($l['aupdt'])."'],
				";						
				
				$formateddata = str_replace('[\'','', rtrim($loaddata, ','));
				$formateddata = str_replace(']','', $formateddata);
				$formateddata = str_replace('\'"' ,'', $formateddata);
				$formateddata = str_replace('"\'','', $formateddata);
			//	$formateddata = explode(',', $formateddata);
				var_dump ($formateddata);
				
		
	}
	
	
			
}
	