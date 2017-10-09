<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Cronn extends Controller {
	
	function Cronn()
	{
		parent::Controller();
		
		/*
		REFERENCE
		
		Minute	Hour	Day	Month	Weekday	Command	Actions
*\/3	*	*	*	*	lynx -source http://www.la-tronics.com/Cronn/DoRevise	Edit   Delete
7	*	*	*	*	lynx -source http://www.la-tronics.com/Cronn/RevTransSelling	Edit   Delete
0	12,18	*	*	*	lynx -source http://www.la-tronics.com/Cronn/CallCheck	Edit   Delete
14	1,7,12,15,19	*	*	*	lynx -source http://www.la-tronics.com/Cronn/CronnGetMyeBaySelling	Edit   Delete
25	12,19	*	*	*	lynx -source http://www.la-tronics.com/Cronn/GetSellerEvents	Edit   Delete
34	2,8,13,16,20	*	*	*	lynx -source http://www.la-tronics.com/Cronn/EndedListings	Edit   Delete
40	6	*	*	*	lynx -source http://www.la-tronics.com/Cronn/UpdateSales	Edit   Delete
44	19	*	*	*	lynx -source http://www.la-tronics.com/Cronn/UpdateTransactionShippingCost	Edit   Delete
48	15,21	*	*	*	lynx -source http://www.la-tronics.com/Cronn/cleanupqns	Edit   Delete
0	5	*	*	*	lynx -source http://www.la-tronics.com/Backup/cleantrash	Edit   Delete
15	5	*	*	*	lynx -source http://www.la-tronics.com/Backup/ebay	Edit   Delete
30	5	*	*	*	lynx -source http://www.la-tronics.com/Backup/warehouse	Edit   Delete
45	5	*	*	*	lynx -source http://www.la-tronics.com/Backup/ebaytransactions	Edit   Delete
55	5	*	*	*	lynx -source http://www.la-tronics.com/Backup/orders	Edit   Delete
15	6	*	*	*	lynx -source http://www.la-tronics.com/Backup/logs	Edit   Delete
0	*	*	*	*	lynx -source http://www.la-tronics.com/Cronn/AutoPilot	Edit   Delete
0	8	*	*	*	lynx -source http://www.la-tronics.com/Cronn/WorkCompetitorQue	Edit   Delete
20	3	*	*	*	lynx -source http://www.la-tronics.com/Cronn/DoComparePrices	Edit   Delete

*/
		
		//$time = mktime();
		//GoMail(array ('msg_title' => 'Running: '.gmdate("H:i:s", $time-(1411760686)).' '.$this->router->fetch_method(), 'msg_body' => '', 'msg_date' => CurrentTime()), 'mitko@rusev.me', $this->config->config['no_reply_email']);	
		
		$this->load->helper('explore');
		
		
	}
	
	function index()
	{

	}	
function testtime()
{
	set_time_limit(600);
		ini_set('mysql.connect_timeout', 600);
		ini_set('max_execution_time', 600);  
		ini_set('default_socket_timeout', 600);
	//printcool (phpinfo());
	echo CurrentTime();	
}
function ManualTrans()
{
	$this->RevTransSelling();
	Redirect('Myebay/GetOrders');
}
function _savedatatofile($row = '')
{
	$this->load->helper('file');
	$data = read_file($this->config->config['pathtosystem'].'/'.$this->config->config['pathtoapplication'].'/sess/0000.txt');
	$data .= $row;
	file_put_contents($this->config->config['pathtosystem'].'/'.$this->config->config['pathtoapplication'].'/sess/0000.txt', $data);
}
function RevTransSelling()
{
	ini_set('memory_limit','2048M');
	set_time_limit(900);
		ini_set('mysql.connect_timeout', 1200);
		ini_set('max_execution_time', 1200);  
		ini_set('default_socket_timeout', 1200);
	$starttime = mktime();	
	$time = mktime()-$starttime;
	/*echo ($time.' DoRevise BEGIN
');*/
	$this->DoRevise();
	$time = mktime()-$starttime;
	/*echo ($time.' DoRevise END
');*/
	if ($time > 250) GoMail(array ('msg_title' => 'RunTime 1 - '.$time.' @ '.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
	$time = mktime()-$starttime;
	/*echo ($time.' Transactions BEGIN
');*/
	$this->Transactions();
	$time = mktime()-$starttime;
	/*echo ($time.' Transactions END
');*/
	if ($time > 250) GoMail(array ('msg_title' => 'RunTime 2 - '.$time.' @ '.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
	$time = mktime()-$starttime;
	/*echo ($time.' Process Transactions BEGIN
');*/
	$this->ProcessTransactions();
	$time = mktime()-$starttime;
	/*echo ($time.' Process Transactions END
');*/
	if ($time > 250) GoMail(array ('msg_title' => 'RunTime 3 - '.$time.' @ '.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);

	/*echo ($time.' GetMyeBaySelling BEGIN
');*/
	$this->DoRevise();
	//sleep(15);
	//$time = mktime()-$starttime;
	//$this->CronnGetMyeBaySelling();
	//$time = mktime()-$starttime;
	/*echo ($time.' GetMyeBaySelling END
');*/
	//if ($time > 250) GoMail(array ('msg_title' => 'RunTime 4 - '.$time.' @ '.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
//echo CurrentTime();	
	
	//GoMail(array ('msg_title' => 'Train Ran - '.$time.' @ '.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
}
function CallCheck()
{
		set_time_limit(120);
		ini_set('mysql.connect_timeout', 180);
		ini_set('max_execution_time', 180);  
		ini_set('default_socket_timeout', 180); 
		
		require_once($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
		
		$verb = 'GetApiAccessRules';
		
		//Create a new eBay session with all details pulled in from included keys.php
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);




				$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
				$requestXmlBody .= '<GetApiAccessRulesRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
				$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
				
				$requestXmlBody .= "<Version>$compatabilityLevel</Version>";
				$requestXmlBody .=  '</GetApiAccessRulesRequest>';			

				//send the request and get response
				$responseXml = $session->sendHttpRequest($requestXmlBody);
				if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
					die('<P>Error sending request');
				
				$xml = simplexml_load_string($responseXml);
				$data = $this->_XML2Array($xml);
//printcool ($data);
$txt = CleanBadDate($data['Timestamp'], 90).' - '.$data['ApiAccessRule']['DailyUsage'].' - '.$data['ApiAccessRule']['RuleCurrentStatus'].' @ '.CleanBadDate($data['ApiAccessRule']['ModTime'],'90-1');
echo (int)$data['ApiAccessRule']['DailyUsage'];
if ((int)$data['ApiAccessRule']['DailyUsage'] > 2500) GoMail(array ('msg_title' => $txt, 'msg_body' => '', 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);

}
function Transactions($process = false)
	{
		//$this->DoRevise();
		//sleep(5);
	
		set_time_limit(600);
		ini_set('mysql.connect_timeout', 1200);
		ini_set('max_execution_time', 1200);  
		ini_set('default_socket_timeout', 1200); 
		require($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');

		$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
		$requestXmlBody .= '<GetSellerTransactionsRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
		$requestXmlBody .= '<DetailLevel>ItemReturnAttributes</DetailLevel>';
		$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
		$requestXmlBody .= '<IncludeContainingOrder>true</IncludeContainingOrder>';
		$requestXmlBody .= "<Version>$compatabilityLevel</Version><NumberOfDays>5</NumberOfDays>";
		//http://developer.ebay.com/DevZone/XML/docs/Reference/eBay/types/GranularityLevelCodeType.html
		//$dates = array('from' => date('Y-m-d H:i:s', strtotime("-2 Hours")), 'to' => date("Y-m-d H:i:s"));
		//<ModTimeFrom>'.$dates['from'].'</ModTimeFrom>
 		//<ModTimeTo>'.$dates['to'].'</ModTimeTo>  
		
			
		//<IncludeCodiceFiscale>'.TRUE.'</IncludeCodiceFiscale>		
		//<IncludeContainingOrder>'.TRUE.'</IncludeContainingOrder> 
		
		$requestXmlBody .= '
	
		 <IncludeFinalValueFee>'.TRUE.'</IncludeFinalValueFee>
			<NumberOfDays>1</NumberOfDays>	
		<Pagination>
		<EntriesPerPage>200</EntriesPerPage>
		</Pagination>
		</GetSellerTransactionsRequest>';	
		$verb = 'GetSellerTransactions';

		//Create a new eBay session with all details pulled in from included keys.php
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
				
		//send the request and get response
		$responseXml = $session->sendHttpRequest($requestXmlBody);
		if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
			die('<P>Error sending request');
		
		$this->load->helper('directory');
		$this->load->helper('file');
		if ($responseXml)
			{
				if (!write_file($this->config->config['ebaypath'].'/trans.txt', $responseXml)) GoMail(array ('msg_title' => 'Unable to write Trans.txt @ '.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
				else {}//GoMail(array ('msg_title' => 'Transactions written @ '.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);				
			}
		if ($process) Redirect('TransactionsComplete');
}

function testasc()
{
	$q = $this->db->query('SELECT `rec`, `et_id`, `e_id`, `itemid`, `transid`, `asc`, `paydata`, `paidtime`, `sn` FROM ebay_transactions WHERE `mkdt` >= '.(mktime()-604800));
		
		$log = '';
		
		printcool($q->num_rows());
		
}

function UpdateTransactionShippingCost()
{
		$this->load->model('Myseller_model');
		$this->load->model('Auth_model');
		
		ini_set('memory_limit','2048M');
		set_time_limit(1200);
		ini_set('mysql.connect_timeout', 1200);
		ini_set('max_execution_time', 1200);  
		ini_set('default_socket_timeout', 1200); 
		
		require_once($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
		
		$verb = 'GetSellingManagerSaleRecord';
		
		//Create a new eBay session with all details pulled in from included keys.php
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
		
		
		$q = $this->db->query('SELECT `rec`, `et_id`, `itemid`, `transid`, `fee`, `asc`, `ssc`, `qty`, `paydata`, `paid`, `paidtime`,`notpaid`,`refunded`,`pendingpay`,`customcode` FROM ebay_transactions WHERE (`asc` IS NULL OR `asc` = "0.00" OR `asc` = "0.0") AND `mkdt` >= '.(mktime()-1209600));
		if ($q->num_rows() > 0) 
		{		
		foreach($q->result_array() as $t)
			 {
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
			//printcool ($item);
				if ($item)
				{
				$asc = floater((string)$item->ActualShippingCost);
				
				
				if ((float)$asc != (float)$t['asc'] && $asc > 0)

				{
					
					$this->db->update('ebay_transactions', array('asc' => (float)$asc, 'cascupd' => 2), array('et_id' => $t['et_id']));
					$this->_logaction('CronUpdateTransactionShippingCost', 'B', array('ActShipCost' => $t['asc']), array('ActShipCost' =>(float)$asc), 0, $t['itemid'], $t['rec']);	
					$update['shipped_actual']= (float)$asc;		
				}	
					 
				$paid = floater(((int)$item->SellingManagerSoldTransaction->QuantitySold*(float)$item->SellingManagerSoldTransaction->ItemPrice));
			
				if ((float)$paid != (float)$t['paid'])
				{
					
					$this->db->update('ebay_transactions', array('paid' => (float)$paid, 'cascupd' => 2), array('et_id' => $t['et_id']));
				}
				$update['paid']= floater((float)$item->SellingManagerSoldTransaction->ItemPrice);	
				if (isset($item->ShippingDetails->ShippingServiceOptions->ShippingServiceCost)) $update['shipped'] =  	floater($item->ShippingDetails->ShippingServiceOptions->ShippingServiceCost/(int)$item->SellingManagerSoldTransaction->QuantitySold);
				else $update['shipped'] = floater((float)$t['ssc']/(int)$item->SellingManagerSoldTransaction->QuantitySold);
				$update['sellingfee'] = floater($t['fee']/(int)$item->SellingManagerSoldTransaction->QuantitySold);
				
			 $ar = $this->_XML2Array($item->OrderStatus);
			 $ar = $ar['OrderStatus'];
			
			 if (isset($ar['PaidTime']) && (CleanBadDate((string)$ar['PaidTime']) != $t['paidtime']) && (CleanBadDate((string)$ar['PaidTime']) != ''))
				{
					
					$this->db->update('ebay_transactions', array('paidtime' => CleanBadDate((string)$ar['PaidTime'])), array('et_id' => $t['et_id']));
					$this->_logaction('CronUpdateTransactionShippingCost', 'B', array('PaidTime' => $t['paidtime']), array('PaidTime' => CleanBadDate((string)$ar['PaidTime'])), 0, $t['itemid'], $t['rec']);		
					$update['paid_date']= (float)$t['paidtime'];
				}	
				
			
			 if (isset($ar['CheckoutStatus']))
				{
					if ($ar['CheckoutStatus'] == 'CustomCode')
					{
						if ($t['customcode'] == 0) 
						{						
							$this->db->update('ebay_transactions', array('customcode' => 1), array('et_id' => $t['et_id']));
							$this->_logaction('CronUpdateTransactionShippingCost', 'B', array('customcode' => 0), array('customcode' => 1), 0, $t['itemid'], $t['rec']);
						}
					}
					elseif ($ar['CheckoutStatus'] == 'Incomplete')
					{
						if ($t['notpaid'] == 0) 
						{ 
							$this->db->update('ebay_transactions', array('notpaid' => 1), array('et_id' => $t['et_id']));
							$this->_logaction('CronUpdateTransactionShippingCost', 'B', array('notpaid' => 0), array('notpaid' => 1), 0, $t['itemid'], $t['rec']);
						}																				
					}
					elseif ($ar['CheckoutStatus'] == 'Pending')
					{
						if ($t['pendingpay'] == 0) 
						{
						 
							$this->db->update('ebay_transactions', array('pendingpay' => 1), array('et_id' => $t['et_id']));
							$this->_logaction('CronUpdateTransactionShippingCost', 'B', array('pendingpay' => 0), array('pendingpay' => 1), 0, $t['itemid'], $t['rec']);
						}										
					}	
					elseif ($ar['CheckoutStatus'] == 'CheckoutComplete')
					{
						if ($t['pendingpay'] == 1) 
						{
							 
							$this->db->update('ebay_transactions', array('pendingpay' => 0), array('et_id' => $t['et_id']));
							$this->_logaction('CronUpdateTransactionShippingCost', 'B', array('pendingpay' => $t['pendingpay']), array('pendingpay' => 0), 0, $t['itemid'], $t['rec']);
						}	
						if ($t['notpaid'] == 1) 
						{
							 
							$this->db->update('ebay_transactions', array('notpaid' => 0), array('et_id' => $t['et_id']));
							$this->_logaction('CronUpdateTransactionShippingCost', 'B', array('notpaid' => $t['notpaid']), array('notpaid' => 0), 0, $t['itemid'], $t['rec']);
						}	
						if ($t['customcode'] ==1) 
						{
							 
							$this->db->update('ebay_transactions', array('customcode' => 0), array('et_id' => $t['et_id']));
							$this->_logaction('CronUpdateTransactionShippingCost', 'B', array('customcode' => $t['customcode']), array('customcode' => 0), 0, $t['itemid'], $t['rec']);
						}										
					}					
					
				}
			 unset($ar['paidtime']);
			 $pd = serialize($ar);
			 
			  if ($item && ($pd != $t['paydata']))
				{					
					$this->db->update('ebay_transactions', array('paydata' => $pd), array('et_id' => $t['et_id']));
											
				}
				
				if (isset($update))
				{			
							$this->load->model('Myseller_model');
							$this->db->select('wid, bcn, '.$this->Myseller_model->sellingfields());
							$this->db->where('channel', 1);
							$this->db->where('sold_id', $t['et_id']);
							$this->db->where('vended', 1);
							
							$f = $this->db->get('warehouse');
							if ($f->num_rows() > 0)
							{
								$fr = $f->result_array();
								foreach ($fr as $fl)
								{	
									if ($fl['vended'] == 1) $this->Myseller_model->HandleBCN($update, $fl);									
								}
							}	
				}
				}
        
			 }
		}
		
		//1 Month
		//$q = $this->db->query('SELECT `rec`, `et_id`, `e_id`, `itemid`, `transid`, `asc`, `paydata`, `paidtime`, `sn` FROM ebay_transactions WHERE (`asc` IS NULL OR `asc` = "0.00" OR `asc` = "0.0") AND `mkdt` >= '.(mktime()-2592000));
		
		//2 month
		//$q = $this->db->query('SELECT `rec`, `et_id`, `e_id`, `itemid`, `transid`, `asc`, `paydata`, `paidtime`, `sn` FROM ebay_transactions WHERE (`asc` IS NULL OR `asc` = "0.00" OR `asc` = "0.0") AND `mkdt` >= '.(mktime()-5184000).' AND `mkdt` <= '.(mktime()-2592000) );
		
		//3month
		//$q = $this->db->query('SELECT `rec`, `et_id`, `e_id`, `itemid`, `transid`, `asc`, `paydata`, `paidtime`, `sn` FROM ebay_transactions WHERE (`asc` IS NULL OR `asc` = "0.00" OR `asc` = "0.0") AND `mkdt` >= '.(mktime()-7776000).' AND `mkdt` <= '.(mktime()-5184000) );
			
			
		//echo $q->num_rows();
		
		//2592000 HOUSEKEEPING
		
		$log = '';
		
		
			
		//$q2 = $this->db->query('SELECT distinct `e`.`et_id`, `e`.`rec`, `e`.`e_id`, `e`.`itemid`, `e`.`transid`, `e`.`asc`, `e`.`paydata`, `e`.`paidtime`, `e`.`sn`, `w`.`shipped_actual` FROM (ebay_transactions e) LEFT JOIN warehouse w ON e.et_id = w.sold_id WHERE `e`.`mkdt` >= '.(mktime()-1209600).' AND `w`.`deleted` = 0  AND `w`.`nr` = 0 AND `w`.`vended` = 1 AND (`w`.`shipped_actual` IS NULL OR `w`.`shipped_actual` = "0.00" OR `w`.`shipped_actual` = "0.0")');
		
		
		
		//1 Month
		//$q2 = $this->db->query('SELECT distinct `e`.`et_id`, `e`.`rec`, `e`.`e_id`, `e`.`itemid`, `e`.`transid`, `e`.`asc`, `e`.`paydata`, `e`.`paidtime`, `e`.`sn`, `w`.`shipped_actual` FROM (ebay_transactions e) LEFT JOIN warehouse w ON e.et_id = w.sold_id WHERE `e`.`mkdt` >= '.(mktime()-2592000).' AND `w`.`deleted` = 0  AND `w`.`nr` = 0 AND `w`.`vended` = 1 AND (`w`.`shipped_actual` IS NULL OR `w`.`shipped_actual` = "0.00" OR `w`.`shipped_actual` = "0.0")');
		
		
		//2 month
		//$q2 = $this->db->query('SELECT distinct `e`.`et_id`, `e`.`rec`, `e`.`e_id`, `e`.`itemid`, `e`.`transid`, `e`.`asc`, `e`.`paydata`, `e`.`paidtime`, `e`.`sn`, `w`.`shipped_actual` FROM (ebay_transactions e) LEFT JOIN warehouse w ON e.et_id = w.sold_id WHERE (`e`.`mkdt` >= '.(mktime()-5184000).' AND `e`.`mkdt` <= '.(mktime()-2592000).') AND `w`.`deleted` = 0  AND `w`.`nr` = 0 AND `w`.`vended` = 1 AND (`w`.`shipped_actual` IS NULL OR `w`.`shipped_actual` = "0.00" OR `w`.`shipped_actual` = "0.0")');
		
		
		//3month
		//$q2 = $this->db->query('SELECT distinct `e`.`et_id`, `e`.`rec`, `e`.`e_id`, `e`.`itemid`, `e`.`transid`, `e`.`asc`, `e`.`paydata`, `e`.`paidtime`, `e`.`sn`, `w`.`shipped_actual` FROM (ebay_transactions e) LEFT JOIN warehouse w ON e.et_id = w.sold_id WHERE (`e`.`mkdt` >= '.(mktime()-7776000).' AND `e`.`mkdt` <= '.(mktime()-5184000).') AND `w`.`deleted` = 0  AND `w`.`nr` = 0 AND `w`.`vended` = 1 AND (`w`.`shipped_actual` IS NULL OR `w`.`shipped_actual` = "0.00" OR `w`.`shipped_actual` = "0.0")');
		
		
		
		//
		//if ($q2->num_rows() > 0) 
		//{		
		//foreach($q2->result_array() as $t)
		//	 {
		//		 $list[] = $t;
		//	 }
		//}
		/*
		
		if (isset($list))	
		{
		$this->load->model('Myseller_model');
		foreach($list as $t)
			 {

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
				
				
				$xml = simplexml_load_string($responseXml);
				$item = $xml->SellingManagerSoldOrder;	
				
				if ($item)
				{
				if ((string)$item->ActualShippingCost != $t['asc'])
				{
					$log .= explore((string)$item->ActualShippingCost, FALSE, 'ASC');
					$this->db->update('ebay_transactions', array('asc' => floater((string)$item->ActualShippingCost), 'cascupd' => 1), array('et_id' => $t['et_id']));
					$this->_logaction('Transactions', 'B', array('ActShipCost' => $t['asc']), array('ActShipCost' => floater((string)$item->ActualShippingCost)), 0, $t['itemid'], $t['rec'],'Cron Transactions');		
					$this->load->model('Mywarehouse_model');
					$data = $this->Mywarehouse_model->getsaleattachdata(1, $t['et_id'], $t['e_id'],1);
					
					$ashc = floater((string)$item->ActualShippingCost);

					$warehouse = array();	
					
					if(isset($data['qty']) && $data['qty'] > 1) $ashc = floater((float)$ashc/$data['qty']);
					
					$warehouse['shipped_actual'] = floater($ashc);
					
						$bcns = $this->Myseller_model->getSales(array((int)$t['et_id']),1, TRUE, TRUE);
						if ($bcns) foreach($bcns as $wid)
						{
							$warehouse['netprofit'] = floater(((float)$wid['paid']+(float)$wid['shipped'])-((float)$wid['cost']+(float)$wid['sellingfee']+(float)$ashc));
							$log .= explore((float)$ashc, FALSE, 'wASC '.$wid['wid']);
							foreach($warehouse as $k => $v)
							{								
							 	if ($v != $wid[$k]) $this->wlog($wid['bcn'], $wid['wid'], $k, $wid[$k], $v);	
								else unset($warehouse[$k]);
							}
							if (count($warehouse) > 0) $this->db->update('warehouse', $warehouse, array('wid' => (int)$wid['wid']));	
						}
					
				}				 

			 $ar = $this->_XML2Array($item->OrderStatus);
			 $ar = $ar['OrderStatus'];

			 if (isset($ar['PaidTime']) && (CleanBadDate((string)$ar['PaidTime'], '287-1') != $t['paidtime']) && (CleanBadDate((string)$ar['PaidTime'], '287-2') != ''))
				{
					//$log .= printcool ($item, TRUE, 'PAIDTIME');
					$updateebt = array('paidtime' => CleanBadDate((string)$ar['PaidTime']));
					//$this->db->update('ebay_transactions', array('paidtime' => CleanBadDate((string)$ar['PaidTime'], 290)), array('et_id' => $t['et_id']));
					$this->_logaction('Transactions', 'B', array('PaidTime' => $t['paidtime']), array('PaidTime' => CleanBadDate((string)$ar['PaidTime'],291)), 0, $t['itemid'], $t['rec'], 'Cron Transactions');	
					 unset($ar['paidtime']);
					 $pd = serialize($ar);
					  if ($item && ($pd != $t['paydata']))
						{	
							//$log .= printcool ($item, TRUE, 'PAYDATA');
							$updateebt['paydata'] = $pd;
							//$this->db->update('ebay_transactions', array('paydata' => $pd), array('et_id' => $t['et_id']));					
						}
					if (isset($updateebt))	
					{
						$this->db->update('ebay_transactions',$updateebt, array('et_id' => $t['et_id']));		
						unset($updateebt);
					}
				}	
			
				}
        
			
		}	
		
		//if ($log != '') GoMail(array ('msg_title' => 'UpdateTransactionShippingCost Log @'.CurrentTime(), 'msg_body' => $log, 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
		
	}	
	*/
	
}
function UpdateSales()
{
	ini_set('memory_limit','1024M');
	$starttime = mktime();
	set_time_limit(600);
		ini_set('mysql.connect_timeout', 600);
		ini_set('max_execution_time', 600);  
		ini_set('default_socket_timeout', 600); 
		
	require_once($this->config->config['ebaypath'].'get-common/keys.php');
						require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');

	$compatabilityLevel = 959;
	
						$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
						$requestXmlBody .= '<GetSellingManagerSoldListingsRequest xmlns="urn:ebay:apis:eBLBaseComponents">
';
						$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
						$requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';
						//$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
						$requestXmlBody .= "<Version>$compatabilityLevel</Version>";
						$days = 60; //maxtime						
						$requestXmlBody .= "<Pagination>
    <EntriesPerPage>200</EntriesPerPage>
    <PageNumber>1</PageNumber>
  </Pagination>
  </GetSellingManagerSoldListingsRequest>";
  
  //http://developer.ebay.com/devzone/xml/docs/Reference/eBay/GetMyeBaySelling.html
  
						$verb = 'GetSellingManagerSoldListings';
						$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
						$responseXml = $session->sendHttpRequest($requestXmlBody);
						if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
							die('<P>Error sending request');
						
						$xml = simplexml_load_string($responseXml);
						$pages = (int)$xml->PaginationResult->TotalNumberOfPages;
						
						$this->db->select('et_id, rec, paid, paidtime,  notpaid,refunded,pendingpay,customcode');
						$c = 1;	
						$selling = array();
						$et_id = false;
						foreach ($xml->SaleRecord as $x)
						{	
							/*echo (mktime()-$starttime. 'Loop  '.$x->SellingManagerSoldTransaction->SaleRecordID.'
');*/
							if (count($x->SellingManagerSoldTransaction) > 1) $x->multiple = count($x->SellingManagerSoldTransaction);
							else $x->multiple = 1;
							if (!$et_id) $et_id = $x->SellingManagerSoldTransaction->SaleRecordID;
							elseif ((int)$et_id > (int)$x->SellingManagerSoldTransaction->SaleRecordID)  $et_id = $x->SellingManagerSoldTransaction->SaleRecordID;
							//if ($c == 1) $this->db->where('rec', $x->SellingManagerSoldTransaction->SaleRecordID);
							//else $this->db->or_where('rec', $x->SellingManagerSoldTransaction->SaleRecordID);
							$selling[] = $x;
							$c++;
						}
						
						if ($pages > 1)
						{
							$page = 1;
							while ($page <= $pages) 
							{
								$page++;
								$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
											$requestXmlBody .= '<GetSellingManagerSoldListingsRequest xmlns="urn:ebay:apis:eBLBaseComponents">
					';
											$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
											$requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';
											//$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
											$requestXmlBody .= "<Version>$compatabilityLevel</Version>";
											$dates = array('from' => date('Y-m-d H:i:s', strtotime("-90 Days")), 'to' => date("Y-m-d H:i:s"));
											$requestXmlBody .= "<SaleDateRange>
							<TimeFrom>".$dates['from']."</TimeFrom>
							<TimeTo>".$dates['to']."</TimeTo>
						  </SaleDateRange>
						  <Pagination>
						<EntriesPerPage>200</EntriesPerPage>
						<PageNumber>".$page."</PageNumber>
					  </Pagination>
					  </GetSellingManagerSoldListingsRequest>";
										  $verb = 'GetSellingManagerSoldListings';
											$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
											$responseXml = $session->sendHttpRequest($requestXmlBody);
											if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
												die('<P>Error sending request');
											
											$xml = simplexml_load_string($responseXml);
											$c = 1;	
											foreach ($xml->SaleRecord as $x)
											{
												/*echo (mktime()-$starttime.' Loop Page '.$page.' - '.$x->SellingManagerSoldTransaction->SaleRecordID.'
');*/
												if (count($x->SellingManagerSoldTransaction) > 1) $x->multiple = count($x->SellingManagerSoldTransaction);
												else $x->multiple = 1;
												if (!$et_id) $et_id = $x->SellingManagerSoldTransaction->SaleRecordID;
												elseif ((int)$et_id > (int)$x->SellingManagerSoldTransaction->SaleRecordID)  $et_id = $x->SellingManagerSoldTransaction->SaleRecordID;
												//if ($c == 1) $this->db->or_where('rec', $x->SellingManagerSoldTransaction->SaleRecordID);
												//else $this->db->or_where('rec', $x->SellingManagerSoldTransaction->SaleRecordID);
												$selling[] = $x;
												$c++;
											}
							}								
						}
						if (count($selling) > 0)
						{	$this->db->or_where('rec >=', $et_id);
							$e = $this->db->get('ebay_transactions');
							if($e->num_rows() > 0)
							{
								
								foreach ($e->result_array() as $ee)
								$eb[$ee['rec']] = $ee;								
							}
							GoMail(array ('msg_title' => 'Update sales run with Min Rec '.$et_id.') @ '.CurrentTime(), 'msg_body' => printcool($selling,true,'Selling').printcool($eb,true,'DB Recs'), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
							
							//echo (mktime()-$starttime.'Selling');
							foreach ($selling as $s)
							{
								
								$key = (int)$s->SellingManagerSoldTransaction->SaleRecordID;
								//$stats[(string)$s->OrderStatus->PaidStatus] = TRUE;
								
								if (isset($eb[$key]))
								{
									if (isset($s->OrderStatus->PaidTime) && $eb[$key]['paidtime'] != CleanBadDate((string)$s->OrderStatus->PaidTime) && CleanBadDate((string)$s->OrderStatus->PaidTime) != '')
									{
										$tdata['paidtime'] = CleanBadDate((string)$s->OrderStatus->PaidTime);															
									}
									if ((string)$s->OrderStatus->PaidStatus == 'Unpaid' && $eb[$key]['notpaid'] == 0)
									{
										$tdata['sellingstatus'] = (string)$s->OrderStatus->PaidStatus;
										$tdata['notpaid'] = 1;
										$tdata['refunded'] = 0;
									}
									if (((string)$s->OrderStatus->PaidStatus == 'Refunded' || (string)$s->OrderStatus->PaidStatus == 'PartiallyPaid') && $eb[$key]['refunded'] == 0)
									{
										$tdata['sellingstatus'] = (string)$s->OrderStatus->PaidStatus;
										$tdata['notpaid'] = 0;
										$tdata['refunded'] = 1;
										if ($s->multiple == 1)$tdata['paid'] = (string)$s->TotalAmount;
									}
									if (((string)$s->OrderStatus->PaidStatus == 'Refunded' || (string)$s->OrderStatus->PaidStatus == 'PartiallyPaid') && (isset($tdata['paid']) && $tdata['paid'] != (string)$s->TotalAmount)) {
										GoMail(array ('msg_title' => 'Refunded item new price (Rec: '.$key.') - from: '.$tdata['paid'].' to: '.(string)$s->TotalAmount.' @ '.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
										
										if ($s->multiple == 1) $tdata['paid'] = (string)$s->TotalAmount;
									}
									
									if ((string)$s->OrderStatus->PaidStatus == 'Paid' && ($eb[$key]['notpaid'] == 1 || $eb[$key]['refunded'] == 1))
									{
										$tdata['sellingstatus'] = (string)$s->OrderStatus->PaidStatus;
										$tdata['notpaid'] = 0;
										$tdata['refunded'] = 0;
									}									
									
									if (isset($tdata))
									{
										$tdata['paydata'] = serialize($this->_XML2Array($s->OrderStatus));
										//printcool ($tdata, false, $key);
										$this->db->update('ebay_transactions', $tdata, array('rec' => $key));
										unset($tdata);
									}
									
								}
								
							}							
						}
}
function hackap()
{
	$this->db->update('autopilot_rules', array('runnextmk' => mktime()+120), array('rid' => 517));
	/*$this->db->update('autopilot_rules', array('runnextmk' => mktime()+120), array('rid' => 169));
	$this->db->update('autopilot_rules', array('runnextmk' => mktime()+120), array('rid' => 157));
	$this->db->update('autopilot_rules', array('runnextmk' => mktime()+120), array('rid' => 522));
	$this->db->update('autopilot_rules', array('runnextmk' => mktime()+120), array('rid' => 526));
	$this->db->update('autopilot_rules', array('runnextmk' => mktime()+120), array('rid' => 650));
	$this->db->update('autopilot_rules', array('runnextmk' => mktime()+120), array('rid' => 60));
	$this->db->update('autopilot_rules', array('runnextmk' => mktime()+120), array('rid' => 170));
	$this->db->update('autopilot_rules', array('runnextmk' => mktime()+120), array('rid' => 455));
	$this->db->update('autopilot_rules', array('runnextmk' => mktime()+120), array('rid' => 533));
	$this->db->update('autopilot_rules', array('runnextmk' => mktime()+120), array('rid' => 537));
	$this->db->update('autopilot_rules', array('runnextmk' => mktime()+120), array('rid' => 625));
	$this->db->update('autopilot_rules', array('runnextmk' => mktime()+120), array('rid' => 530));*/
}
function AutoPilot()
	{
		$log = 'Autopilot Running  @ '.CurrentTime();
		
		$this->load->model('Myseller_model');
		$this->db->select("admin_id, email, ownnames");
		$query = $this->db->get('administrators');
		
		if ($query->num_rows() > 0) 
		{
			foreach ($query->result_array() as $a) $adm[$a['admin_id']] = $a;	
		}
		
		
		$crules = $this->db->query("SELECT cid, e_id FROM competitor_rules");	
		if ($crules->num_rows() > 0)
		{
			foreach ($crules->result_array() as $cr)
			{
				$existing_competitor[$cr['e_id']][] = $cr['cid'];	
			}
			
			$log .= printcool ($existing_competitor, true, 'existing_ALL_competitor'); 
		}
//echo CurrentTime();
		//$nowmk = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		$nowmk = (mktime()-60);
		//
		//$log .= printcool (mktime(), true, 'mktime');
		//$log .= printcool ($nowmk, true, '$nowml');
		$listings = $this->db->query("SELECT distinct e.e_id,e.price_ch1, e.quantity, a.* FROM (ebay e) LEFT JOIN autopilot_rules a ON e.e_id = a.e_id WHERE e.e_id = a.e_id AND a.runnextmk >= '".$nowmk."' AND a.runnextmk <= '".($nowmk+3600)."' AND e.ebended IS NULL"); // AND a.daystocheck = $days
		//$log .= printcool ("AND a.runnextmk >= '".$nowmk."' AND a.runnextmk <= '".($nowmk+3600)."'");
		
		//
		
		//$listings = $this->db->query("SELECT * FROM autopilot_rules WHERE runnextmk >= '".$nowmk."' AND runnextmk <= '".($nowmk+3600)."'"); 
		
		
		//$log .= printcool ($listings);
		
		 $log .= printcool ($listings, true, '$Listings');
		 //$log .= printcool ('Check 1');
		if ($listings->num_rows() > 0)
        {
			$log .= printcool ($listings, true, '$Listings');
			
        $activerules = array();
			foreach ($listings->result_array() as $l)
			{
				$activerules[$l['e_id']][] = $l;	
			}
		$log .= printcool ($activerules, true, 'ActiveRules');
			
			if (count($activerules > 0))
			{
				$sql = "select distinct e_id from ebay_transactions where mkdt between ".($nowmk-3600)." AND ".($nowmk+120)." AND (";
				$c = 1;
				foreach ($activerules as $k => $v)
				{
					if ($c == 1) $sql .= 'e_id = '.(int)$k;
					else $sql .= ' OR e_id = '.(int)$k;
					$c++;					
				}
					
				$sql .= ')';
				$log .= printcool ($sql, true, 'EIDs');
				$sales = $this->db->query($sql);	
					
				if ($sales->num_rows() > 0)
				{
					foreach ($sales->result_array() as $s)
					{
							if (isset($activerules[$s['e_id']])) 
							{
								foreach($activerules[$s['e_id']] as $uark => $uarv)
								{
									if ((int)$uarv['quantity'] > 0) 
									{
										if ((int)$uarv['hours'] == 1)	
										{
											$array = array(
															'runnext' => date("Y-m-d H:i:s", time()+((int)$uarv['daystocheck']*3600)),
															'runnextmk' => mktime()+((int)$uarv['daystocheck']*3600)
															);
											$this->db->update('autopilot_rules', $array , array('rid' => $uarv['rid']));		
										}
										else
										{
											
											$array = array(
															'runnext' => date("Y-m-d H:i:s", time()+((int)$uarv['daystocheck']*3600*24)),
															'runnextmk' => mktime()+((int)$uarv['daystocheck']*3600*24)
															);
															
											$this->db->update('autopilot_rules', $array , array('rid' => $uarv['rid']));											
										}	
										$log .= printcool ($array, true, 'Postponed Rule Due To Exisitng Transaction - RID '.$uarv['rid']);
										if ($this->config->config['sendemails1']) GoMail(array ('msg_title' => 'Autopilot Rule '.$uarv['rid'].' Extended 1 @ '.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);				
									}
									else if ($this->config->config['sendemails1']) GoMail(array ('msg_title' => 'Autopilot Rule '.$uarv['rid'].' NOT EXTENDED 1 @ '.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
								
								}
								unset($activerules[$s['e_id']]);
							}							
					}						
				}
				
					foreach ($activerules as $k => $v)
					{
						if (isset($existing_competitor[$k]))
						{$log .= printcool ($existing_competitor[$k], true, 'existing_competitor'); 
							foreach($v as $uark => $uarv)
									{
										if ((int)$uarv['quantity'] > 0) 
										{
											if ((int)$uarv['hours'] == 1)	
											{
												$array = array(
																'runnext' => date("Y-m-d H:i:s", time()+((int)$uarv['daystocheck']*3600)),
																'runnextmk' => mktime()+((int)$uarv['daystocheck']*3600)
																);
												$this->db->update('autopilot_rules', $array , array('rid' => $uarv['rid']));		
											}
											else
											{
												
												$array = array(
																'runnext' => date("Y-m-d H:i:s", time()+((int)$uarv['daystocheck']*3600*24)),
																'runnextmk' => mktime()+((int)$uarv['daystocheck']*3600*24)
																);
																
												$this->db->update('autopilot_rules', $array , array('rid' => $uarv['rid']));											
											}	$log .= printcool ($array, true, 'Postponed - Competitor Rule Exists - RID '.$uarv['rid']);
											if ($this->config->config['sendemails1']) GoMail(array ('msg_title' => 'Autopilot Rule '.$uarv['rid'].' Extended 2 @ '.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);				
										}
										else if ($this->config->config['sendemails1']) GoMail(array ('msg_title' => 'Autopilot Rule '.$uarv['rid'].' NOT EXTENDED 2 @ '.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
									
									}
									
									unset($activerules[$k]);
						}
					}
				
				}
					
					
					if (count($activerules) > 0)
					{
						foreach ($activerules as $k => $a)
						{
							foreach ($a as $ka => $aa)
							{
							$eid = $aa['e_id'];
							$current = $aa['price_ch1'];
							$min = $aa['rununtil'];
							$asqlround = $min;
							$dispose = $aa['dispose'];
												
							switch ($aa['add'])
							{
								case 0:
								{
									switch ($aa['isamount'])
									{
										case 0:
										{
											$log = 'Subtract '.$aa['changevalue'].' Percent';
											$aa['changevalue'] = ($current/100)*$aa['changevalue'];
											$sign = '-';
											//$a['changevalue'] = 1+($a['changevalue']/100);
											$asql = $current-$aa['changevalue'];
											$displ = $current-$aa['changevalue'];
											$minround = $min-$aa['changevalue'];
										break;	
										}
										case 1:
										{
											$log = 'Subtract '.$aa['changevalue'].' Fixed';
											$sign = '-';
											$asql = $current-$aa['changevalue'];
											$displ = $current-$aa['changevalue'];	
											$minround = $min-$aa['changevalue'];									
										break;
										}							

									}
								break;	
								}
								case 1:
								{
									switch ($aa['isamount'])
									{
										case 0:
										{
											$log = 'Add '.$aa['changevalue'].' Percent';
											$aa['changevalue'] = ($current/100)*$aa['changevalue'];
											$sign = '+';
											//$a['changevalue'] = 1+($a['changevalue']/100);
											$asql = $current+$aa['changevalue'];
											$displ = $current+$aa['changevalue'];
											$minround = $min+$aa['changevalue'];
										break;	
										}
										case 1:
										{
											$log = 'Add '.$aa['changevalue'].' Fixed';
											$sign = '+';
											$asql = $current+$aa['changevalue'];											
											$displ = $current+$aa['changevalue'];
											$minround = $min+$aa['changevalue'];										
										break;
										}							
									}								
								break;
								}							
						}
						
						
						$notice = 'Listing '.$eid.' - from: '.$current.' - to: '.$current.$sign.$aa['changevalue'].' or '.$displ.' ('.$log.') by Rule '.$aa['rid'];
						$log .= printcool ($notice, true, 'notice'); 
						
						
						if (($aa['add'] == 0 && ($displ < $min && $displ < $minround)) || ($aa['add'] == 1 && ($displ > $min && $displ > $minround)))
						{
							if ((int)$aa['runtimes'] == 0 || ((int)$aa['runtimes'] > 0 && ((int)$aa['hasrun'] <= (int)$aa['runtimes']))) 
							{
								 $log .= printcool ('Proceeding with RUN', true, 'RUN');
							}
							else
							{
								 $log .= printcool ('<strong>WILL NOT RUN, RESULT EXEEDS LIMIT</strong>', true, 'No RUN');
								 if ($this->config->config['sendemails1']) GoMail(array ('msg_title' => 'Autopilot Rule '.$uarv['rid'].' NOT EXTENDED 3 @ '.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
							}
						}
						$log .= printcool ('Check 2',true);
						 if (($aa['add'] == 0 && ($displ >= $min || $displ >= $minround)) || ($aa['add'] == 1 && ($displ <= $min || $displ <= $minround)))

						{
							if ((int)$aa['runtimes'] == 0 || ((int)$aa['runtimes'] > 0 && (int)$aa['hasrun'] <= (int)$aa['runtimes'])) 
							{	//DO NOT RESET IF MINIMUM IS REACHED		
								if ((int)$aa['hasrun'] < (int)$aa['runtimes'] || (int)$aa['runtimes'] == 0)
								{
									if ((int)$aa['hours'] == 1)	
									{
										$array = array(
														'runnext' => date("Y-m-d H:i:s", time()+((int)$aa['daystocheck']*3600)),
														'runnextmk' => mktime()+((int)$aa['daystocheck']*3600),
														'hasrun' => (int)$aa['hasrun']+1,
														);
										$this->db->update('autopilot_rules', $array , array('rid' => $aa['rid']));		
									}
									else
									{
										
										$array = array(
														'runnext' => date("Y-m-d H:i:s", time()+((int)$aa['daystocheck']*3600*24)),
														'runnextmk' => mktime()+((int)$aa['daystocheck']*3600*24),
														'hasrun' => (int)$aa['hasrun']+1,
														);
														
										$this->db->update('autopilot_rules', $array , array('rid' => $aa['rid']));											
									}					
									
									$log .= printcool ($array, true, 'Update RID '.$aa['rid']);
													
									if ($aa['inform'] == 0)
									{
										if (($aa['add'] == 0 && ($displ < $min && $displ >= $minround)) || ($aa['add'] == 1 && ($displ > $min && $displ <= $minround))) { $asql = $asqlround; $displ = $asqlround; }
									$this->db->query('UPDATE ebay SET `price_ch1` = '.floater($asql).' WHERE `e_id` = '.(int)$eid);
									$this->Myseller_model->que_rev((int)$eid, 'p', floater($asql), 'AutoPilot '.$adm[$aa['adminassigned']]);	
									$this->db->insert('autopilot_log', array('apl_listingid' =>(int)$eid, 'apl_from' =>$current, 'apl_to' =>floater($displ), 'apl_rid' =>(int)$aa['rid'], 'apl_adminid' => $aa['adminassigned'], 'apl_time' =>CurrentTime(), 'apl_tstime' =>mktime()));
									
										$ra['admin'] = 'AutoPilot '.$adm[$aa['adminassigned']]['ownnames'];
										$ra['time'] = CurrentTime();
										$ra['ctrl'] = 'Autopilot';
										$ra['field'] = 'price_ch1';
										$ra['atype'] = 'M';
										$ra['e_id'] = (int)$eid;
										$ra['ebay_id'] = 0;
										$ra['datafrom'] = $current;
										$ra['datato'] = floater($displ);
													
										$this->db->insert('ebay_actionlog', $ra);
										
										$log .= printcool ($ra, true, 'ActionLog');
									}
									else
									{
										GoMail(array ('msg_title' => 'Informative Autopilot Rule @ '.CurrentTime(), 'msg_body' => $notice, 'msg_date' => CurrentTime()), array($adms[$aa['adminassigned']]['email'],$this->config->config['support_email']), $this->config->config['no_reply_email']);
									}
									
									
									
								}
								elseif ($dispose == 1) 
									{
										$this->db->update('ebay', array('dispose' => 1), array('e_id' => (int)$eid));
										if ($this->config->config['sendemails1']) GoMail(array ('msg_title' => 'Autopilot Rule '.$uarv['rid'].' NOT EXTENDED 4 @ '.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
									}
								else if ($this->config->config['sendemails1']) GoMail(array ('msg_title' => 'Autopilot Rule '.$uarv['rid'].' NOT EXTENDED 5 @ '.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
							}
						}
					}
					}
						//printcool ($activerules[$k], '', $k);
				}
			}
	//if ($this->config->config['sendemails1']) GoMail(array ('msg_title' => 'Autopilot Run Log 5 @ '.CurrentTime(), 'msg_body' => $log, 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
    //GoMail(array ('msg_title' => 'Autopilot Run Log 5 @ '.CurrentTime(), 'msg_body' => $log, 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);

	}
	
function DoComparePrices()
    {
        //   
        // This CRON job updates (if its lower ) competitor's price for an item based on given ebay id (entered manualy by the staff)!
        //
        require($this->config->config['ebaypath'] . 'get-common/keys.php');
        require_once($this->config->config['ebaypath'] . 'get-common/eBaySession.php');
        $this->load->model('Myebay_model');
		
		$this->db->select("admin_id, email, ownnames");
			$query = $this->db->get('administrators');
			
			if ($query->num_rows() > 0) 
			{
				foreach ($query->result_array() as $a) $adm[$a['admin_id']] = $a;	
			}
			
       /* $this->db->select('ebay.e_id, buyItNowPrice, ebay_id, competitor_rules.competitor_item_id, ebended, price_ch1, 	price_change_value, competitor_price, changetype');
      //  $this->db->from('ebay');
        $this->db->join('competitor_rules', 'ebay.e_id = competitor_rules.e_id');
        $this->db->where("ebended = ''");
        $this->db->where("competitor_rules.competitor_item_id > 0");		
        $res = $this->db->get('ebay');
		*/
		$res = $this->db->query('SELECT distinct c.*, e.e_id, e.buyItNowPrice, e.ebay_id, e.ebended, e.price_ch1 FROM (ebay e) LEFT JOIN competitor_rules c ON e.e_id = c.e_id WHERE e.e_id = c.e_id AND e.ebended IS NULL AND e.ebay_id > 0'); 
		//printcool ($res->result_array());exit();
        //echo $this->db->last_query();
        if ($res->num_rows() > 0) {
			
			
			
			//if ($this->config->config['sendemails1']) GoMail(array ('msg_title' => 'Competitor DoComparePrices Run ('.$res->num_rows().') @ '.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
           // GoMail(array ('msg_title' => 'Competitor DoComparePrices Run ('.$res->num_rows().') @ '.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);

            // echo "<p>works";
           // $rev = $res->result_array();
       // } else {
        //    echo "<p>exit";
       //     $rev = false;
       //     log_message('error', 'Cron job DoComparePrices() - Did not find any listings to check for lower competitors prices!' . CurrentTime());
       //     exit;
        //}
        // First Test
        //$id = '172381819562';
        //$this->load->model('Myebay_model');
        //$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?\>';
        //$requestXmlBody .= '<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        //$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
        //$requestXmlBody .= '<DetailLevel>ItemReturnAttributes</DetailLevel>';
        //$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
        //$requestXmlBody .= "<Version>$compatabilityLevel</Version>";						
        //$requestXmlBody .= '<ItemID>'.(int)$id.'</ItemID></GetItemRequest>';
        //$verb = 'GetItem';
        //$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
        //$responseXml = $session->sendHttpRequest($requestXmlBody);
        //if(stristr($responseXml, 'HTTP 404') || $responseXml == '') die('<P>Error sending request');
        //$xml = simplexml_load_string($responseXml);	
        //echo 'Test->'.(float)$xml->Item->StartPrice;
        //if ($r->num_rows() > 0)
        //{
        //    set_time_limit(600);
        //    ini_set('mysql.connect_timeout', 600);
        //    ini_set('max_execution_time', 600);  
        //    ini_set('default_socket_timeout', 600); 
        //    $revs = $r->result_array();
        //    require($this->config->config['ebaypath'].'get-common/keys.php');
        //    require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
        //    $this->load->model('Myebay_model');
        //    $id = '201696022610';				
        //    $item = $this->Myebay_model->GetItem((int)$id);	
        //    log_message('error', 'REVISE START '.(int)$id.' @ '.CurrentTime());
        //    if (!$item) 
        //    { 
        //        echo 'Item not found!';  
        //    }
        //    elseif ((int)$item['ebay_id'] == 0) $this->db->insert('ebay_revise_log', array('eid'=>$id,'type'=>$rev['e_type'],'value'=>'X','oldvalue'=>'X','attime'=>CurrentTimeR(), 'atmk'=>mktime(),'response' => 'Not Commited, Listing Not Submited To eBay', 'place'=> $rev['place'], 'admin' => $rev['admin']));
        //    elseif ($item['ebended'] != '') $this->db->insert('ebay_revise_log', array('eid'=>$id,'type'=>$rev['e_type'],'value'=>'X','oldvalue'=>'X','attime'=>CurrentTimeR(), 'atmk'=>mktime(),'response' => 'Not Commited, Listing Is Ended', 'place'=> $rev['place'], 'admin' => $rev['admin']));
        //    else
        //    {			
        //            $requestXmlBody .= '<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        //            $requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
        //            $requestXmlBody .= '<DetailLevel>ItemReturnAttributes</DetailLevel>';
        //            $requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
        //            $requestXmlBody .= "<Version>$compatabilityLevel</Version>";						
        //            $requestXmlBody .= '<ItemID>'.(int)$item['ebay_id'].'</ItemID></GetItemRequest>';
        //            $verb = 'GetItem';
        //            $session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
        //            $responseXml = $session->sendHttpRequest($requestXmlBody);
        //            if(stristr($responseXml, 'HTTP 404') || $responseXml == '') die('<P>Error sending request');
        //            $xml = simplexml_load_string($responseXml);
        //            log_message('error', 'REVISE 1 '.(int)$id.' @ '.CurrentTime());
        //            if ((string)$xml->Item->ItemID == '') 
        //            { 
        //                log_message('error', 'ERROR: Invalid Item ID... '.(int)$id.' @ '.CurrentTime()); 
        //                echo 'ERROR: Invalid Item ID...'; 
        //                if ($rev['e_type'] == 'p') $newebayvalue = $item['price_ch1']; 
        //                else  $newebayvalue = (int)$item['qn_ch1'];
        //                //$this->db->insert('ebay_revise_log', array('eid'=>$id,'type'=>$rev['e_type'],'value'=>$newebayvalue,'oldvalue'=>"?",'attime'=>CurrentTimeR(), 'atmk'=>mktime(),'response' => 'ERROR: Invalid Item ID...', 'sev' => 1, 'place'=> $rev['place'], 'admin' => $rev['admin']));
        //                //GoMail(array ('msg_title' => 'ERROR: Invalid Item ID... '.(int)$id.' / '.$item['ebay_id'].' @'.CurrentTime(), 'msg_body' => explore($xml,false).explore($item, false).explore($requestXmlBody, false), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
        //            }
        //            else 
        //            {
        //                if ($rev['e_type'] == 'p') 
        //                {
        //log_message('error', 'REVISE 2p '.(int)$id.' @ '.CurrentTime());
        //                    $oldebayvalue = (string)$xml->Item->StartPrice;
        //                    $newebayvalue = $item['price_ch1'];
        //                }
        //                else
        //                {
        //                    //log_message('error', 'REVISE 2q '.(int)$id.' @ '.CurrentTime());
        //                     $oldebayvalue = (int)$xml->Item->Quantity-(int)$xml->Item->SellingStatus->QuantitySold;
        //                     $newebayvalue = (int)$item['qn_ch1'];	
        //                     if((int)$item['qn_ch1'] == 0 && $item['ebended'] == '') $this->db->update('ebay', array('ostock' => CurrentTime()), array('e_id' => (int)$id));				 
        //                }
        //            }
        //        }	
        //    }
       // if ($rev) {
            //echo "<p>Enter if";
			
            set_time_limit(600);
            ini_set('mysql.connect_timeout', 600);
            ini_set('max_execution_time', 600);
            ini_set('default_socket_timeout', 600);
            //UPDATE `ebay` SET `competitor_item_id` =172381812796 where ebay_id>0 and buyItNowPrice>0 LIMIT 100 
            //SELECT count( * ) FROM `ebay` WHERE `competitorLowerPrice` != ''
            //SELECT `buyItNowPrice` , `competitorLowerPrice` FROM `ebay` WHERE `competitorLowerPrice` != ''
            
            foreach ($res->result_array() as $k => $revs)
			{
				$count_lowest_prices = 0;
	            $count_higher_prices = 0;
				$echo = '';
                $id                 = $revs['e_id'];
                $ebay_id            = $revs['ebay_id'];
                $competitor_item_id = $revs['competitor_item_id'];
                $changetype         = $revs['changetype'];
                $price_ch1          = $revs['price_ch1'];
                $price_change_value = $revs['price_change_value'];
                $competitor_price   = $revs['competitor_price'];
				$cid = $revs['cid'];
				$admin = $revs['adminassigned'];
				$inform = $revs['inform'];
				$hours = $revs['time_delay'];
				$lastcompprice = $revs['competitor_price'];
				$from = 0;
				$to = 0;
				$echo .= '<h4><strong>'.($k+1).'.</strong> Starting Check From Listing <strong>'.$id.'</strong> to Competitor ItemID <strong>'.$competitor_item_id.'</strong></h4>';
                if ($ebay_id == '' || $competitor_item_id == '')
                    continue;
                $item           = $this->Myebay_model->GetItem((int) $id);
                //$item = $this->Myebay_model->GetItem((int)172186226202);	
                $requestXmlBody = '<?xml version="1.0" encoding="utf-8" ?>
';
                $requestXmlBody .= '
<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
                $requestXmlBody .= "
  <RequesterCredentials>
    <eBayAuthToken>$userToken</eBayAuthToken>
  </RequesterCredentials>
  ";
                $requestXmlBody .= '
  <DetailLevel>ItemReturnAttributes</DetailLevel>
  ';
                $requestXmlBody .= '
  <ErrorLanguage>en_US</ErrorLanguage>
  ';
                $requestXmlBody .= "
  <Version>$compatabilityLevel</Version>
  ";
                //TODO: Change $ebay_id with $competitor_item_id when the project is in production
                $requestXmlBody .= '
  <ItemID>' . (int) $competitor_item_id . '</ItemID>
</GetItemRequest>
';
                $verb        = 'GetItem';
                $session     = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
                $responseXml = $session->sendHttpRequest($requestXmlBody);
                if (stristr($responseXml, 'HTTP 404') || $responseXml == '') {
                    log_message('error', 'DoComparePrices() - GetItem doesn\'t return eBay competitor\'s id of the listing ' . (int) $id . ' @ ' . CurrentTime());
                    continue;
					$echo .= '<Br>No competitor ID returned by Ebay for listing '.(int)$id;
                }
                $xml                = simplexml_load_string($responseXml);
                $competitorNewPrice = floater((float) $xml->Item->StartPrice);
                //if((float)$xml->Item->StartPrice < (float)$item['buyItNowPrice'])
                //echo "<br>eBay Price comp () - ".(float)$xml->Item->StartPrice.". Our price is ".(float)$price_ch1;
                //Our price will be reduced
				if((float) $xml->Item->StartPrice != (float)$lastcompprice)
				{
				$echo .= "<br><br><span style='color:orange'>Saving Competitor Price Now!</span>";
                    $this->db->query('UPDATE competitor_rules SET competitor_price = '.floater($competitorNewPrice).'
                                        ,last_applied_lower_price=\''.CurrentTime().'\''.
                                        ',last_applied_lower_price_mk='.mktime().
                                        ' WHERE e_id = '.(int)$id);    
				}
                if ((float) $xml->Item->StartPrice < (float) $price_ch1 && (float) $price_ch1 > 0 && (float) $xml->Item->StartPrice != (float)$lastcompprice) {
                    $count_lowest_prices++;
                    //amount 
					$echo .= '<strong>&darr; CASE IS LOWER PRICE &darr;</strong><br><br>';
					
                    if ($changetype == 0) {
                        $echo .= "<strong>Rule is Amount.</strong> <br><span style='color:red;'>Competitor price is $" . $competitorNewPrice . ".</span><br><span style='color:blue;'>Our price is $" . (float) $price_ch1 . ".</span><br><span style='color:green;'>Our new price will be $" . ((float)$competitorNewPrice - (float) $price_change_value) . "." . "</span><br>Action amount is $" . (float) $price_change_value;
						$from = $price_ch1;
						$to = ((float)$competitorNewPrice - (float) $price_change_value); 
                    }
                    //margin 
                    elseif ($changetype == 1) {
                        // echo "<br>Margin.Competitor price is ".(float)$competitorNewPrice;
                        if ((float) $competitorNewPrice < (float) $competitor_price AND isset($competitor_price) AND (float) $competitor_price > 0) {
                            $echo .= "<strong>Rules is Margin. </strong><br><span style='color:red;'>Competitor price is $" . (float) $competitorNewPrice . "</span><br><span style='color:blue;'>Our price is $" . (float) $price_ch1 . ".</span><br><span style='color:green;'>Our new price will be $" . ((float) $price_ch1 - ((float) $competitor_price - (float) $competitorNewPrice)) . ".</span> Competitor Margin Is ($" . (float) $competitor_price . " - $".(float) $competitorNewPrice." = $".((float) $competitor_price - (float) $competitorNewPrice);
							
							$from = $price_ch1;
							$to = ((float) $price_ch1 - ((float) $competitor_price - (float) $competitorNewPrice)); 

                        }
						elseif ((float) $competitor_price = 0)
						{
							echo "<br><span style='color:red;'>We do not have old competitor price value in order to calculate margin</span>";								
						}
                    }
                    //fixed 
                    else {
                        $echo .= "<br>
                            <strong>Rule is Fixed Value</strong><br><span style='color:red;'>Competitor price is  $" . (float) $competitorNewPrice . "</span><br>
							 <span style='color:blue;'>Our price is $" . (float) $price_ch1 . ".</span><br>
							 <span style='color:green;'>Our new price will be fixed to $" . (float) $price_change_value."</span>";
							 
							 $from = $price_ch1;
							$to = (float) $price_change_value; 
                    }
					
                    
                }
                //Our price will be lifted
                if ((float) $xml->Item->StartPrice > (float) $item['price_ch1'] && (float) $xml->Item->StartPrice != (float)$lastcompprice) {
                    $count_higher_prices++;
                    //echo '<p>Price is higher or equal, not implemented functionality yet!';
                    //amount
					$echo .= '<strong>&uarr; CASE IS HIGHER PRICE &uarr;</strong><br><br>';
                    if ($changetype == 0) {
                        
						/*$echo .= "<br>
  Amount. Competitor price is " . $competitorNewPrice . ", our price is " . (float) $price_ch1 . ". Our new price is " . (float) $price_change_value . "." . " Amount, amount = " . (float) $price_change_value;*/
  						
						$echo .= "<strong>Rule is Amount.</strong> <br><span style='color:red;'>Competitor price is $" . $competitorNewPrice . ".</span><br><span style='color:blue;'>Our price is $" . (float) $price_ch1 . ".</span><br><span style='color:green;'>Our new price will be $" . ((float)$competitorNewPrice + (float) $price_change_value) . "." . "</span><br>Action amount is $" . (float) $price_change_value;
						
						$from = $price_ch1;
						$to = ((float)$competitorNewPrice + (float) $price_change_value); 
                    }
                    //margin
                    elseif ($changetype == 1) {
                        // echo "<br>Margin.Competitor price is ".(float)$competitorNewPrice;
                        if ((float) $competitorNewPrice > (float) $competitor_price AND isset($competitor_price) AND (float) $competitor_price > 0) {
                        
						   /* $echo .= "<br>
  Margin. Competitor price is " . (float) $competitorNewPrice . ", our price is " . (float) $price_ch1 . ". Our new price is " . ((float) $price_ch1 + ((float) $competitorNewPrice - (float) $competitor_price)) . ". Margin. Old competitor price is " . (float) $competitor_price . ". New competitor price is " . (float) $competitorNewPrice;*/
  
   							 $echo .= "<strong>Rules is Margin. </strong><br><span style='color:red;'>Competitor price is $" . (float) $competitorNewPrice . "</span><br><span style='color:blue;'>Our price is $" . (float) $price_ch1 . ".</span><br><span style='color:green;'>Our new price will be $" . ((float) $price_ch1 + ((float) $competitorNewPrice-(float) $competitor_price)) . ".</span> Competitor Margin Is ($" . (float) $competitorNewPrice . " - $".(float) $competitor_price." = $".((float) $competitorNewPrice - (float) $competitor_price);
							 
							 $from = $price_ch1;
								$to = ((float) $price_ch1 + ((float) $competitorNewPrice-(float) $competitor_price)); 
						
                        }
						elseif ((float) $competitor_price = 0)
						{
							echo "<br><span style='color:red;'>We do not have old competitor price value in order to calculate margin</span>";								
						}
                    }
                    //fixed
                    else { $echo .= "<br><strong>Rule is Fixed Value</strong><br><span style='color:red;'>Scenario is unavailable for price rising</span>";
                        //$echo .= "<p>Fixed. Competitor price is  " . (float) $competitorNewPrice . ", our price is " . (float) $price_ch1 . ". Our new price is " . ((float) $price_ch1 + (float) $price_change_value) . ". Fixed, amount to subtract = " . (float) $price_change_value.'</p>';
                    }
                }
				
				$echo .= "<h3 style='color:purple;'>Competitor's have <span style='font-size:25px;'>$count_lowest_prices</span> <strong>LOWER</strong> prices.</h3>";
				$echo .= "<h3 style='color:purple;'>Competitor's have <span style='font-size:25px;'>$count_higher_prices</span> <strong>HIGHER</strong> prices.</h3>";
				$echo .= "<h2>Have a nice day!  &#9786;</h2>";
				if((float) $xml->Item->StartPrice != (float)$lastcompprice)
				{
				if ($this->config->config['sendemails1']) GoMail(array ('msg_title' => 'CompetitorPilot Rule in Que for + '.$hours.' Hours @ '.CurrentTime(), 'msg_body' => $echo , 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
				
				//echo $echo;
				
				//SEE IF DOESNT EXIST FIRST BY EID,CID,ITEMID
				$this->db->insert('competitor_que', array(			
				'cq_eid' => (int)$id,
				'cq_itemid' =>  $ebay_id,
				'cq_cid' => $cid,
				'cq_from' => $from ,
				'cq_to' => floater($to),
				'cq_created' => CurrentTime(),
				'cq_createdmk' => mktime(),
				'cq_admin' => $admin,
				'cq_runat' => mktime()+(3600*$hours)			
				));
                    GoMail(array ('msg_title' => 'Competitor DoComparePrices Run INSERT ('.$id.') @ '.CurrentTime(), 'msg_body' => printcool(array(
                        'cq_eid' => (int)$id,
                        'cq_itemid' =>  $ebay_id,
                        'cq_cid' => $cid,
                        'cq_from' => $from ,
                        'cq_to' => floater($to),
                        'cq_created' => CurrentTime(),
                        'cq_createdmk' => mktime(),
                        'cq_admin' => $admin,
                        'cq_runat' => mktime()+(3600*$hours)
                    ),TRUE,'DUMP'), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);

                    if ($inform == 1)
				{
					GoMail(array ('msg_title' => 'Inform CompetitorPilot Rule in Que for + '.$hours.' Hours @ '.CurrentTime(), 'msg_body' => $echo, 'msg_date' => CurrentTime()), array($adm[$admin]['email'],$this->config->config['support_email']), $this->config->config['no_reply_email']);		
				}
				}
            }
            
        }
    }
	
function WorkCompetitorQue()
	{
		$nowmk = (mktime()-300);		
		$que = $this->db->query("SELECT * FROM competitor_que WHERE cq_runat >= '".$nowmk."' AND cq_runat <= '".($nowmk+3000)."'");	
		if ($que->num_rows() > 0)
		{
			/*
			'cq_eid' => (int)$id,
			'cq_itemid' =>  $ebay_id,
			'cq_cid' => $cid,
			'cq_from' => $from ,
			'cq_to' => $to,
			'cq_created' => CurrentTime(),
			'cq_createdmk' => mktime(),
			'cq_admin' => $admin,
			'cq_runat' => mktime()+(3600*$hours)			
			*/
			
			$this->db->select("admin_id, email, ownnames");
			$query = $this->db->get('administrators');
			
			if ($query->num_rows() > 0) 
			{
				foreach ($query->result_array() as $a) $adm[$a['admin_id']] = $a;	
			}
		
			foreach ($que->result_array() as $q)
			{
				$this->db->query('UPDATE ebay SET `price_ch1` = '.$q['cq_to'].' WHERE `e_id` = '.(int)$q['cq_eid']);
				
				$this->db->insert('competitor_rules_log', array('cl_listingid' =>(int)$q['cq_eid'], 'cl_from' =>$q['cq_from'], 'cl_to' =>$q['cq_to'], 'cl_rid' =>$q['cq_cid'], 'cl_adminid' => $q['cq_admin'], 'cl_time' =>CurrentTime(), 'cl_tstime' =>mktime()));
				
					$this->load->model('Myseller_model');   
                    $this->Myseller_model->que_rev((int)$q['cq_eid'], 'p', $q['cq_to'], 'CompetitorPilot '.$adm[$q['cq_admin']]['ownnames']); 
                    $ra['admin'] = 'CompetitorPilot '.$adm[$q['cq_admin']]['ownnames'];  
                    $ra['time'] = CurrentTime();  
                    $ra['ctrl'] = 'CompetitorRule';  
                    $ra['field'] = 'price_ch1';  
                    $ra['atype'] = 'M';  
                    $ra['e_id'] = (int)$q['cq_eid'];  
                    $ra['ebay_id'] = $q['cq_itemid'];  
                    $ra['datafrom'] = $q['cq_from'];  
                    $ra['datato'] = $q['cq_to'];
					$this->db->insert('ebay_actionlog', $ra);
				
				$this->db->where('cq_id', $q['cq_id']);
				$this->db->delete('competitor_que');
				
				if ($this->config->config['sendemails1']) GoMail(array ('msg_title' => 'WorkCompetitorQue Run @ '.CurrentTime(), 'msg_body' => printcool ($q, TRUE, 'CQ').printcool ($ra, TRUE, 'RA') , 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']); 
				
			}			
		}		
		$que = $this->db->query("SELECT * FROM competitor_que");
		if ($que->num_rows() == 0)
		{
			$this->db->truncate('competitor_que');
			
			//GoMail(array ('msg_title' => ' Truncate CompetitorQue @ '.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']); 
			
		}
	}
	
	
	
	
	
function cleanupqns()
{
	$q = $this->db->query("SELECT e_id FROM ebay WHERE `ebay_id` != 0  AND `ebended` IS NULL AND `quantity` != `ebayquantity`");		
	if ($q->num_rows() > 0)
		{
			$this->load->model('Myseller_model'); 	
			foreach ($q->result_array() as $rr)
			{
					$this->Myseller_model->ProcessFinalCounts($rr['e_id']);	
			}
		}	
}
	
	
	
	
	
	
	
	
		
function CronnGetMyeBaySelling()
{
	$this->DoRevise();
	
	sleep(30);
	
	require($this->config->config['ebaypath'].'get-common/keys.php');
	require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');

	set_time_limit(1800);
				ini_set('mysql.connect_timeout', 1800);
				ini_set('max_execution_time', 1800);  
				ini_set('default_socket_timeout', 1800);
	//$this->DoRevise();
	//sleep(20);
	//$this->db->query("DELETE FROM ebay_cron WHERE ts < ".(mktime()-(60*60*24*60))."");
	$compatabilityLevel = 959;
	
	

						$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
						$requestXmlBody .= '<GetMyeBaySellingRequest xmlns="urn:ebay:apis:eBLBaseComponents">
';
						$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
						//$requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';
						$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
						$requestXmlBody .= "<Version>$compatabilityLevel</Version>";
						$days = 60; //maxtime	

									
						$requestXmlBody .= " <ActiveList>
    <Include>TRUE</Include>
	<Pagination>
<EntriesPerPage>200</EntriesPerPage>
<PageNumber>1</PageNumber>
</Pagination>
  </ActiveList> 
  <HideVariations>FALSE</HideVariations> 
  <SellingSummary>
    <Include>TRUE</Include>
  </SellingSummary> 
  </GetMyeBaySellingRequest>";
						$verb = 'GetMyeBaySelling';
						$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
						$responseXml = $session->sendHttpRequest($requestXmlBody);
						if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
							die('<P>Error sending request');
						
						$xml = simplexml_load_string($responseXml);
						if (!isset($xml->ActiveList->PaginationResult->TotalNumberOfPages))
                                                {
                                                    GoMail(array ('msg_title' => 'GetMyeBaySellingRequest NO PAGES @ '.CurrentTime(), 'msg_body' => printcool($xml,TRUE), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
                                                    exit();
                                                }
						$pages = (int)$xml->ActiveList->PaginationResult->TotalNumberOfPages;
						$entries = (int)$xml->ActiveList->PaginationResult->TotalNumberOfEntries;
						
						$list['active'][1] = $xml->ActiveList->ItemArray;		
						
						
						$used = array();
						
						if (count($list['active'][1]) > 0)
						{	
						$ebay_id = '';
						foreach ($list['active'][1] as $vv)
						{
							foreach ($vv->Item as $i)
							{
								if (!isset($used[(int)$i->ItemID])) 
								{	
									$used[(int)$i->ItemID] = true;
									$ebay_id .= (int)$i->ItemID.',';
								}
							}
						}
						$ebay_id= rtrim($ebay_id,",");
						}				
					
						$sql = "SELECT e_id, ebay_id, e_title, quantity, qn_ch1, qn_ch2, price_ch1, price_ch2, ebayquantity, ebended, sitesell FROM ebay WHERE ebay_id IN (".$ebay_id.")";	
						
						$r = $this->db->query($sql);		
						//printcool($r->num_rows(), false, '$r->num_rows()');
						if ($r->num_rows() > 0)
						{ 
							foreach ($r->result_array() as $rk => $rv)
							{
								$mitemids[(int)$rv['ebay_id']] = $rv;				
							} 	   	
						}		
				//	printcool(count($used));
					
	if ($pages > 1)
	{
		$page = 2;
		while ($page <= $pages) 
		{
			
			$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
						$requestXmlBody .= '<GetMyeBaySellingRequest xmlns="urn:ebay:apis:eBLBaseComponents">
';
						$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
						//$requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';
						$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
						$requestXmlBody .= "<Version>$compatabilityLevel</Version>";						
						$requestXmlBody .= "<ActiveList><Include>TRUE</Include><Pagination><EntriesPerPage>200</EntriesPerPage><PageNumber>".$page."</PageNumber></Pagination></ActiveList></GetMyeBaySellingRequest>";
						$verb = 'GetMyeBaySelling';
						$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
						$responseXml = $session->sendHttpRequest($requestXmlBody);
						if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
							die('<P>Error sending request');						
						$xml = simplexml_load_string($responseXml);
						$list['active'][$page] = $xml->ActiveList->ItemArray;
						if (count($list['active'][$page]) > 0)
						{	
						$ebay_id = '';
						foreach ($list['active'][$page] as $vv)
						{
							foreach ($vv->Item as $i)
							{
								if (!isset($used[(int)$i->ItemID])) 
								{	
									$ebay_id .= (int)$i->ItemID.',';
									$used[(int)$i->ItemID] = true;
								}
							}
						}
						$ebay_id= rtrim($ebay_id,",");
						}				
					
						$sql = "SELECT e_id, ebay_id, e_title, quantity, qn_ch1, qn_ch2, price_ch1, price_ch2, ebayquantity, ebended, sitesell FROM ebay WHERE ebay_id IN (".$ebay_id.")";	
						
						$r = $this->db->query($sql);		
						//printcool($r->num_rows(), false, '$r->num_rows()');
						if ($r->num_rows() > 0)
						{ 
							foreach ($r->result_array() as $rk => $rv)
							{
								$mitemids[(int)$rv['ebay_id']] = $rv;				
							} 	   	
						}
						$page++;
						//printcool(count($used));
		}								
	}
	
		
	if (count($mitemids) == 0) exit('No Active Listings');
	
	$this->db->select("e_id, sitesell");
	$this->db->where('sitesell', 1);
	$ss = $this->db->get('ebay');
	if ($ss->num_rows() > 0)
	{ 	
		foreach ($ss->result_array() as $s)
		{
			$sellingonsite[$s['e_id']] = TRUE;	
		}
	}
	$ebon = $this->db->query('SELECT e_id, ebay_id FROM ebay WHERE `ebay_id` != 0 AND `ebended` IS NULL');
	if ($ebon->num_rows() > 0)
	{
		foreach ($ebon->result_array() as $ee)
		{
			$localactive[$ee['ebay_id']] = $ee['e_id']; 	
		}
	}

	$this->db->truncate('ebay_live'); 
	
	$reactived = array();
	$deactivated = array();
	foreach($list['active'] as $k=>$v)
	{
		if (!isset($v) || (isset($v) && count($v) == 0)) 
		{
			GoMail(array ('msg_title' => '$v issue @ '.CurrentTime(), 'msg_body' => explore($v, FALSE, '$v').printcool($k, true, '$k'), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
		
			GoMail(array ('msg_title' => '$v issue @ '.CurrentTime(), 'msg_body' => explore($list['active'][$k], FALSE, '$list[active][$k]'), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
		
		    echo ('$v is bugged');
		}
		if (count($v) > 0) foreach ($v as $vv)
		{ 
			foreach ($vv->Item as $i)
			{	
				
                            $itemid = (int)$i->ItemID;
				
				if (isset($mitemids[$itemid]) && $mitemids[$itemid]['sitesell'] == 0)
				{
					$mitemids[$itemid]['sitesell'] = 1;
					$this->db->update('ebay', array('sitesell' => 1), array('e_id' => (int)$mitemids[$itemid]['e_id']));
				}
				//if (!isset($mitemids[$itemid])) printcool ($itemid);
				if (isset($localactive[$itemid])) unset($localactive[$itemid]);
				
				$data = array(
				'etype' => 'a',
				'itemid' => (int)$i->ItemID,
				'ebavq' => (int)$i->QuantityAvailable,
				'ebtq' => (int)$i->Quantity,
				'etitle' => trim(addslashes($i->Title))
				);
				$data['eid'] = 0;
				if (isset($mitemids[$itemid])) $data['lq'] = $mitemids[$itemid]['qn_ch1'];				
				if (isset($mitemids[$itemid])) $data['lebq'] = $mitemids[$itemid]['ebayquantity'];
				if (isset($mitemids[$itemid])) $data['eid'] = $mitemids[$itemid]['e_id'];
				if (isset($mitemids[$itemid]) && $mitemids[$itemid]['ebended'] !='') $data['locended'] = 1;
				
				$this->db->insert('ebay_live', $data);
				
				if ((int)$data['eid'] > 0 && isset($sellingonsite[$data['eid']]))  unset($sellingonsite[$data['eid']]);
				
				//if ((int)$data['eid'] == 14516) GoMail(array ('msg_title' => '1. 14516 FOUND @'.CurrentTime(), 'msg_body' => printcool($data,true), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
				if (isset($mitemids[$itemid]))
				{
					if (trim($i->Title) != trim(stripslashes($mitemids[$itemid]['e_title'])))
						{
							if ((int)$data['eid'] > 0) 
							{
							$ra['admin'] = 'Cron eBaySelling';
							$ra['time'] = CurrentTime();
							$ra['ctrl'] = 'CronnGetMyeBaySelling';
							$ra['field'] = 'e_title';
							$ra['atype'] = 'M';
							$ra['e_id'] = (int)$data['eid'];
							$ra['ebay_id'] = $itemid;
							$ra['datafrom'] = addslashes($mitemids[$itemid]['e_title']);
							$ra['datato'] = addslashes($i->Title);
										
							$this->db->insert('ebay_actionlog', $ra); 
							unset($ra);
							$this->db->update('ebay', array('e_title' => trim(addslashes($i->Title))), array('e_id' => (int)$data['eid']));
							}
						}		
						
						//if ((int)$data['eid'] == 14516) GoMail(array ('msg_title' => '2. 14516 $i->QuantityAvailable/$mitemids[$itemid][\'ebayquantity\'] @'.CurrentTime(), 'msg_body' => printcool((int)$i->QuantityAvailable,true, '(int)$i->QuantityAvailable').printcool((int)$mitemids[$itemid]['ebayquantity'],true, '$mitemids[$itemid][\'ebayquantity\']'), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
						
						if ($mitemids[$itemid]['ebended'] != '')
						{
							$this->db->update('ebay', array('ebended' => NULL), array('e_id' => $mitemids[$itemid]['e_id']));
							$ra['admin'] = 'Cron eBaySelling';
							$ra['time'] = CurrentTime();
							$ra['ctrl'] = 'CronnGetMyeBaySelling';
							$ra['field'] = 'ebended';
							$ra['atype'] = 'M';
							$ra['e_id'] = $mitemids[$itemid]['e_id'];
							$ra['ebay_id'] = $itemid;
							$ra['datafrom'] = $mitemids[$itemid]['ebended'];
							$ra['datato'] = '';										
							$this->db->insert('ebay_actionlog', $ra); 
							unset($ra);
							$reactived[] = $mitemids[$itemid];
							//GoMail(array ('msg_title' => 'Active listing - removed local ended value @ '.CurrentTime(), 'msg_body' => explore($mitemids[$itemid], FALSE, '$mitemids[$itemid]'), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
							$mitemids[$itemid]['ebended'] = '';	
						}
						
						
						if ((int)$i->QuantityAvailable != (int)$mitemids[$itemid]['ebayquantity'] && $mitemids[$itemid]['ebended'] == '')
						{
							if ((int)$data['eid'] > 0) 
							{
								$this->db->insert('ebay_cron', array('e_id' => $data['eid'], 'data' => 'eBayQN: '.$mitemids[$itemid]['ebayquantity'].' - To: '.(int)$i->QuantityAvailable.' - (ref data: QuantityAvailable(eBay):'.$i->QuantityAvailable.', @ GetMyEbaySelling)', 'time' => CurrentTime(), 'ts' => mktime()));
							
							$ra['admin'] = 'Cron eBaySelling';
							$ra['time'] = CurrentTime();
							$ra['ctrl'] = 'CronnGetMyeBaySelling';
							$ra['field'] = 'ebayquantity';
							$ra['atype'] = 'Q';
							$ra['e_id'] = (int)$data['eid'];
							$ra['ebay_id'] = $itemid;
							$ra['datafrom'] = $mitemids[$itemid]['ebayquantity'];
							$ra['datato'] = (int)$i->QuantityAvailable;
										
							$this->db->insert('ebay_actionlog', $ra); 
							unset($ra);
							$this->db->update('ebay', array('ebayquantity' => (int)$i->QuantityAvailable, 'e_qpart' => (int)$i->QuantityAvailable), array('e_id' => (int)$data['eid']));
							}
						}
						//if ((int)$data['eid'] == 14516) GoMail(array ('msg_title' => '3. 14516 $i->QuantityAvailable/3x @'.CurrentTime(), 'msg_body' => printcool((int)$i->QuantityAvailable,true, '(int)$i->QuantityAvailable').printcool($mitemids[$itemid],true, '$mitemids[$itemid]'), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
						
						if ((((int)$i->QuantityAvailable != $mitemids[$itemid]['qn_ch1']) || ((int)$i->QuantityAvailable != $mitemids[$itemid]['qn_ch2']) || ((int)$i->QuantityAvailable != $mitemids[$itemid]['quantity'])) && $mitemids[$itemid]['ebended'] == '')
						{
							
							//if ((int)$data['eid'] == 14516) GoMail(array ('msg_title' => '4. 14516  3x ENTERED @'.CurrentTime(), 'msg_body' => printcool((int)$i->QuantityAvailable,true, '(int)$i->QuantityAvailable').printcool($mitemids[$itemid],true, '$mitemids[$itemid]'), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
							
							if ((int)$i->QuantityAvailable != $mitemids[$itemid]['qn_ch1'])
							{
							if ((int)$data['eid'] > 0) 
							{	$this->db->insert('ebay_cron', array('e_id' => $data['eid'], 'data' => 'QnCh1: '.$mitemids[$itemid]['qn_ch1'].' - To: '.(int)$i->QuantityAvailable.' - (ref data: QuantityAvailable(eBay):'.$i->QuantityAvailable.', @ GetMyEbaySelling)', 'time' => CurrentTime(), 'ts' => mktime()));
							
							$ra['admin'] = 'Cron eBaySelling';
							$ra['time'] = CurrentTime();
							$ra['ctrl'] = 'CronnGetMyeBaySelling';
							$ra['field'] = 'qn_ch1';
							$ra['atype'] = 'Q';
							$ra['e_id'] = (int)$data['eid'];
							$ra['ebay_id'] = $itemid;
							$ra['datafrom'] = $mitemids[$itemid]['qn_ch1'];
							$ra['datato'] = (int)$i->QuantityAvailable;
										
							$this->db->insert('ebay_actionlog', $ra); 
							unset($ra);
							$this->db->update('ebay', array('qn_ch1' => (int)$i->QuantityAvailable), array('e_id' => (int)$data['eid']));
							}
							}
							
							if ((int)$i->QuantityAvailable != $mitemids[$itemid]['qn_ch2'])
							{
							if ((int)$data['eid'] > 0) 
							{
								$this->db->insert('ebay_cron', array('e_id' => $data['eid'], 'data' => 'QnCh2: '.$mitemids[$itemid]['qn_ch2'].' - To: '.(int)$i->QuantityAvailable.' - (ref data: QuantityAvailable(eBay):'.$i->QuantityAvailable.', @ GetMyEbaySelling)', 'time' => CurrentTime(), 'ts' => mktime()));
						
							$ra['admin'] = 'Cron eBaySelling';
							$ra['time'] = CurrentTime();
							$ra['ctrl'] = 'CronnGetMyeBaySelling';
							$ra['field'] = 'qn_ch2';
							$ra['atype'] = 'Q';
							$ra['e_id'] = (int)$data['eid'];
							$ra['ebay_id'] = $itemid;
							$ra['datafrom'] = $mitemids[$itemid]['qn_ch2'];
							$ra['datato'] = (int)$i->QuantityAvailable;
										
							if ((int)$ra['e_id'] != 0) $this->db->insert('ebay_actionlog', $ra); 
							unset($ra);
							$this->db->update('ebay', array('qn_ch2' => (int)$i->QuantityAvailable), array('e_id' => (int)$data['eid']));
							}
							}
							
							
							//if ((int)$data['eid'] == 14516) GoMail(array ('msg_title' => '5. 14516 PRE BCN REGEN @'.CurrentTime(), 'msg_body' => printcool((int)$i->QuantityAvailable,true, '(int)$i->QuantityAvailable').printcool($mitemids[$itemid],true, '$mitemids[$itemid]'), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
							if ((int)$data['eid'] > 0)
							{
								//GoMail(array ('msg_title' => 'BCNREGEN DUMP $i: '.(int)$data['eid'].' @ '.CurrentTime(), 'msg_body' =>	printcool ($i, true, 'BCNREGEN DUMP $i'), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
								
								//GoMail(array ('msg_title' => 'BCNREGEN DUMP $mitemids[$itemid]: '.(int)$data['eid'].' @ '.CurrentTime(), 'msg_body' =>	printcool ($mitemids[$itemid], true, 'BCNREGEN DUMP $mitemids[$itemid]'), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
							
								$this->_BCNRegen((int)$data['eid']);
							}
						}	
						
						
						if (trim((string)$i->BuyItNowPrice) != trim($mitemids[$itemid]['price_ch1']))
						{
							if ((int)$data['eid'] > 0) 
							{$this->db->insert('ebay_cron', array('e_id' => $data['eid'], 'data' => 'Price: '.trim($mitemids[$itemid]['price_ch1']).' - To: '.trim((string)$i->BuyItNowPrice), 'time' => CurrentTime(), 'ts' => mktime()));
							
							$ra['admin'] = 'Cron eBaySelling';
							$ra['time'] = CurrentTime();
							$ra['ctrl'] = 'CronnGetMyeBaySelling';
							$ra['field'] = 'price_ch1';
							$ra['atype'] = 'M';
							$ra['e_id'] = (int)$data['eid'];
							$ra['ebay_id'] = $itemid;
							$ra['datafrom'] = $mitemids[$itemid]['price_ch1'];
							$ra['datato'] = floater((string)$i->BuyItNowPrice);										
							$this->db->insert('ebay_actionlog', $ra); 
							$ra['field'] = 'price_ch1';
							$ra['datafrom'] = $mitemids[$itemid]['price_ch1'];
							$this->db->insert('ebay_actionlog', $ra); 
							unset($ra);
							$this->db->update('ebay', array('price_ch1' => floater((string)$i->BuyItNowPrice), 'price_ch2' => floater((string)$i->BuyItNowPrice)), array('e_id' => (int)$data['eid']));
							
							$this->load->model('Myautopilot_model');	
							if($mitemids[$itemid]['qn_ch1'] >0)$this->Myautopilot_model->ResetRules((int)$data['eid'], 'GetMyeBaySelling');
							$this->Myautopilot_model->LogPriceChange((int)$data['eid'], $mitemids[$itemid]['price_ch1'], floater((string)$i->BuyItNowPrice), 0);
							
							}
						}						
						unset($itemid);
					}
				}
			}
		}
	if (isset($sellingonsite) && count($sellingonsite > 0))
	{
		foreach ($sellingonsite as $k => $s)
		{
			$this->db->update('ebay', array('sitesell' => 0), array('e_id' => (int)$k));
		}
	}
	if (isset($localactive) && count($localactive > 0))
	{
		$this->load->model('Myseller_model');																
		foreach ($localactive as $k => $q)
		{
			$this->db->update('ebay', array('ebended' => CurrentTime(), 'endedreason' => 'Not present in eBay Selling Cron', 'sitesell' => 0), array('e_id' => (int)$q));								
			//GoMail(array ('msg_title' => 'Listing Ended ('.$q.') - Not present in eBay Selling Cron @'.CurrentTime(), 'msg_body' => explore($list['active'], FALSE, 'ListActive').explore($localactive, FALSE, 'LocalActive'), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);	
			
			$deactivated[] = $q;
			//GoMail(array ('msg_title' => 'Listing Ended ('.$q.') - Not present in eBay Selling Cron @'.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);	
			
			$bcns = $this->Myseller_model->getBase(array((int)$q),TRUE);
			/*if ($bcns) foreach($bcns as $wid)
			{
				$wdata['status_notes'] = 'Changed from "'.$wid['status'].'" - Cron Ended Selling';
				if (trim($wid['status_notes']) != '') $wdata['status_notes'] .= ' | '.$wid['status_notes'];								
				$wdata['status'] = 'Not Listed';				
				$this->wlog($wid['bcn'], $wid['wid'], 'status', $wid['status'], $wdata['status']);	
				$this->db->update('warehouse', $wdata, array('wid' => (int)$wid['wid']));	
				unset($wdata);
			}
			$this->Myseller_model->ProcessFinalCounts((int)$q);*/
		}
	}
	
	if (count($deactivated) > 0) GoMail(array ('msg_title' => 'Listings Ended ('.count($deactivated).') - Not present in eBay Selling Cron @'.CurrentTime(), 'msg_body' => printcool ($deactivated,true), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
	
	if (count($reactived ) > 0) GoMail(array ('msg_title' => 'Active listings ('.count($reactived ).') - Removed local ended value @'.CurrentTime(), 'msg_body' => printcool ($reactived,true), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
}
/*
function CronnGetOLDMyeBaySelling()
{
	set_time_limit(180);
				ini_set('mysql.connect_timeout', 180);
				ini_set('max_execution_time', 180);  
				ini_set('default_socket_timeout', 180);
	//$this->DoRevise();
	//sleep(10);
	$this->db->query("DELETE FROM ebay_cron WHERE ts < ".(mktime()-(60*60*24*30))."");
	//$this->db->insert('ebay_cron', array('e_id' => 123, 'data' => 'lalala', 'time' => CurrentTime(), 'ts' => mktime()));
	//exit();
	require_once($this->config->config['ebaypath'].'get-common/keys.php');
						require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');


						$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
						$requestXmlBody .= '<GetMyeBaySellingRequest xmlns="urn:ebay:apis:eBLBaseComponents">
';
						$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
						//$requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';
						$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
						$requestXmlBody .= "<Version>$compatabilityLevel</Version>";
						$days = 60; //maxtime	
									
						$requestXmlBody .= " <ActiveList>
    <Include>TRUE</Include>
	<Pagination>
<EntriesPerPage>200</EntriesPerPage>
<PageNumber>1</PageNumber>
</Pagination>
  </ActiveList>
  <BidList> ItemListCustomizationType
    <Include>TRUE</Include>
  </BidList>
  <DeletedFromSoldList>
    <Include>TRUE</Include>
  </DeletedFromSoldList>
  <DeletedFromUnsoldList>
    <Include>TRUE</Include>
  </DeletedFromUnsoldList>
  <HideVariations>FALSE</HideVariations>
  <ScheduledList>
    <Include>TRUE</Include>
  </ScheduledList>
  <SellingSummary>
    <Include>TRUE</Include>
  </SellingSummary>
  <SoldList>
    <Include>TRUE</Include>
	<DurationInDays>".$days."</DurationInDays>
  </SoldList>
  <UnsoldList>
    <Include>TRUE</Include>
	<DurationInDays>".$days."</DurationInDays>
  </UnsoldList>
  </GetMyeBaySellingRequest>";
						$verb = 'GetMyeBaySelling';
						$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
						$responseXml = $session->sendHttpRequest($requestXmlBody);
						if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
							die('<P>Error sending request');
						
						$xml = simplexml_load_string($responseXml);
						
						$pages = (int)$xml->ActiveList->PaginationResult->TotalNumberOfPages;
						$entries = (int)$xml->ActiveList->PaginationResult->TotalNumberOfEntries;
				//printcool ($xml);
						$list['active'][1] = $xml->ActiveList->ItemArray;
						$list['sold'] = $xml->SoldList->OrderTransactionArray;
						$list['unsold'] = $xml->UnsoldList->ItemArray;
	if ($pages > 1)
	{
		$page = 1;
		while ($page <= $pages) 
		{
			$page++;
			$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
						$requestXmlBody .= '<GetMyeBaySellingRequest xmlns="urn:ebay:apis:eBLBaseComponents">
';
						$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
						//$requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';
						$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
						$requestXmlBody .= "<Version>$compatabilityLevel</Version>";						
						$requestXmlBody .= "<ActiveList><Include>TRUE</Include><Pagination><EntriesPerPage>200</EntriesPerPage><PageNumber>".$page."</PageNumber></Pagination></ActiveList></GetMyeBaySellingRequest>";
						$verb = 'GetMyeBaySelling';
						$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
						$responseXml = $session->sendHttpRequest($requestXmlBody);
						if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
							die('<P>Error sending request');						
						$xml = simplexml_load_string($responseXml);
						$list['active'][$page] = $xml->ActiveList->ItemArray;
		}								
	}
	
	$itemids = array();
	$activecount = 0;	
	$done = array();
	foreach($list['active'] as $v)
	{		
		foreach ($v as $vv)
		{
			foreach ($vv->Item as $i)
			{
				if (!isset($done[(int)$i->ItemID])) 
				{
					$itemids[(int)$i->ItemID] = false;
					$activecount++;
					$done[(int)$i->ItemID] = true;
				}
			}
		}
	}
	foreach($list['sold'] as $k=>$v)
	{
		foreach ($v->OrderTransaction as $i)
			{//printcool ($i);
				if (!isset($i->Transaction->Item->ItemID))  GoMail(array ('msg_title' => '$i->Transaction->Item->ItemID @ 302', 'msg_body' => printcool($i, true), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
				$itemids[(int)$i->Transaction->Item->ItemID] = false;
			}		
	}
	foreach($list['unsold'] as $k=>$v)
	{
		foreach ($v->Item as $i)
			{		
				$itemids[(int)$i->ItemID] = false;
			}		
	}
	
	
	$this->db->select("e_id, ebay_id, e_title, qn_ch1, qn_ch2, price_ch1, price_ch2, ebayquantity, ebended");		
	$cnt = 1;
	foreach ($itemids as $k => $v)
	{
		if ($cnt == 1) $this->db->where("ebay_id", $k);	
		else $this->db->or_where("ebay_id", $k);	
		$cnt++;	
	}
	$r = $this->db->get('ebay');		
	if ($r->num_rows() > 0)
	{ 	
		foreach ($r->result_array() as $rk => $rv)
		{
			$mitemids[(int)$rv['ebay_id']] = $rv;	
			unset($itemids[(int)$rv['ebay_id']]);		
		} 
	   	
	}		
	
	$this->db->where('etype', 'a');
	$this->db->delete('ebay_live'); 

	$insactive = 0;
	$inserted = array();
	foreach($list['active'] as $k=>$v)
	{
		foreach ($v as $vv)
		{
			foreach ($vv->Item as $i)
			{
				if (!isset($inserted[(int)$i->ItemID]))
				{
				$itemid = (int)$i->ItemID;
				$data = array(
				'etype' => 'a',
				'itemid' => (int)$i->ItemID,
				'ebavq' => (int)$i->QuantityAvailable,
				'ebtq' => (int)$i->Quantity,
				'etitle' => trim(addslashes($i->Title))
				);
				$data['eid'] = 0;
				if (isset($mitemids[$itemid])) $data['lq'] = $mitemids[$itemid]['qn_ch1'];
				if (isset($mitemids[$itemid])) $data['lebq'] = $mitemids[$itemid]['ebayquantity'];
				if (isset($mitemids[$itemid])) $data['eid'] = $mitemids[$itemid]['e_id'];
				if (isset($mitemids[$itemid]) && $mitemids[$itemid]['ebended'] !='') $data['locended'] = 1;
				
				$this->db->insert('ebay_live', $data);
				$insactive++;
				
					if (isset($mitemids[$itemid])  && (trim($i->Title) != trim(stripslashes($mitemids[$itemid]['e_title']))))
						{
							if ((int)$data['eid'] > 0) 
							{
							$ra['admin'] = 'Cron';
							$ra['time'] = CurrentTime();
							$ra['ctrl'] = 'CronnGetMyeBaySelling';
							$ra['field'] = 'e_title';
							$ra['atype'] = 'M';
							$ra['e_id'] = (int)$data['eid'];
							$ra['ebay_id'] = $itemid;
							$ra['datafrom'] = addslashes($mitemids[$itemid]['e_title']);
							$ra['datato'] = addslashes($i->Title);
										
							$this->db->insert('ebay_actionlog', $ra); 
							unset($ra);
							$this->db->update('ebay', array('e_title' => trim(addslashes($i->Title))), array('e_id' => (int)$data['eid']));
							}
						}		
						
				
						
						if ((int)$i->QuantityAvailable != (int)$mitemids[$itemid]['ebayquantity'])
						{
							if ((int)$data['eid'] > 0) 
							{
								$this->db->insert('ebay_cron', array('e_id' => $data['eid'], 'data' => 'eBayQN: '.$mitemids[$itemid]['ebayquantity'].' - To: '.(int)$i->QuantityAvailable.' - (ref data: QNAll:'.$i->Quantity.', @ GetMyEbaySelling', 'time' => CurrentTime(), 'ts' => mktime()));
							
							$ra['admin'] = 'Cron';
							$ra['time'] = CurrentTime();
							$ra['ctrl'] = 'CronnGetMyeBaySelling';
							$ra['field'] = 'ebayquantity';
							$ra['atype'] = 'Q';
							$ra['e_id'] = (int)$data['eid'];
							$ra['ebay_id'] = $itemid;
							$ra['datafrom'] = $mitemids[$itemid]['ebayquantity'];
							$ra['datato'] = (int)$i->QuantityAvailable;
										
							$this->db->insert('ebay_actionlog', $ra); 
							unset($ra);
							$this->db->update('ebay', array('ebayquantity' => (int)$i->QuantityAvailable), array('e_id' => (int)$data['eid']));
							}
						}
						
						if (((int)$i->QuantityAvailable != $mitemids[$itemid]['qn_ch1']) || ((int)$i->QuantityAvailable != $mitemids[$itemid]['qn_ch2']) )
						{
							if ((int)$i->QuantityAvailable != $mitemids[$itemid]['qn_ch1'])
							{
							if ((int)$data['eid'] > 0) 
							{	$this->db->insert('ebay_cron', array('e_id' => $data['eid'], 'data' => 'QnCh1: '.$mitemids[$itemid]['qn_ch1'].' - To: '.(int)$i->QuantityAvailable.' - (ref data: QN:'.$i->Quantity.', @ GetMyEbaySelling', 'time' => CurrentTime(), 'ts' => mktime()));
							
							$ra['admin'] = 'Cron';
							$ra['time'] = CurrentTime();
							$ra['ctrl'] = 'CronnGetMyeBaySelling';

							$ra['field'] = 'qn_ch1';
							$ra['atype'] = 'Q';
							$ra['e_id'] = (int)$data['eid'];
							$ra['ebay_id'] = $itemid;
							$ra['datafrom'] = $mitemids[$itemid]['qn_ch1'];
							$ra['datato'] = (int)$i->QuantityAvailable;
										
							$this->db->insert('ebay_actionlog', $ra); 
							unset($ra);
							$this->db->update('ebay', array('qn_ch1' => (int)$i->QuantityAvailable), array('e_id' => (int)$data['eid']));
							}
							}
							
							if ((int)$i->QuantityAvailable != $mitemids[$itemid]['qn_ch2'])
							{
							if ((int)$data['eid'] > 0) 
							{
								$this->db->insert('ebay_cron', array('e_id' => $data['eid'], 'data' => 'QnCh2: '.$mitemids[$itemid]['qn_ch2'].' - To: '.(int)$i->QuantityAvailable.' - (ref data: QN:'.$i->Quantity.', @ GetMyEbaySelling', 'time' => CurrentTime(), 'ts' => mktime()));
						
							$ra['admin'] = 'Cron';
							$ra['time'] = CurrentTime();
							$ra['ctrl'] = 'CronnGetMyeBaySelling';
							$ra['field'] = 'qn_ch2';
							$ra['atype'] = 'Q';
							$ra['e_id'] = (int)$data['eid'];
							$ra['ebay_id'] = $itemid;
							$ra['datafrom'] = $mitemids[$itemid]['qn_ch2'];
							$ra['datato'] = (int)$i->QuantityAvailable;
										
							if ((int)$ra['e_id'] != 0) $this->db->insert('ebay_actionlog', $ra); 
							unset($ra);
							$this->db->update('ebay', array('qn_ch2' => (int)$i->QuantityAvailable), array('e_id' => (int)$data['eid']));
							}
							}
							
							if ((int)$data['eid'] > 0) $this->_BCNRegen((int)$data['eid']);
						}	
						
						
						if (trim((string)$i->BuyItNowPrice) != trim($mitemids[$itemid]['price_ch1']))
						{
							if ((int)$data['eid'] > 0) 
							{$this->db->insert('ebay_cron', array('e_id' => $data['eid'], 'data' => 'Price: '.trim($mitemids[$itemid]['price_ch1']).' - To: '.trim((string)$i->BuyItNowPrice), 'time' => CurrentTime(), 'ts' => mktime()));
							
							$ra['admin'] = 'Cron';
							$ra['time'] = CurrentTime();
							$ra['ctrl'] = 'CronnGetMyeBaySelling';
							$ra['field'] = 'price_ch1';
							$ra['atype'] = 'M';
							$ra['e_id'] = (int)$data['eid'];
							$ra['ebay_id'] = $itemid;
							$ra['datafrom'] = $mitemids[$itemid]['price_ch1'];
							$ra['datato'] = trim((string)$i->BuyItNowPrice);										
							$this->db->insert('ebay_actionlog', $ra); 
							$ra['field'] = 'price_ch2';
							$ra['datafrom'] = $mitemids[$itemid]['price_ch2'];
							$this->db->insert('ebay_actionlog', $ra); 
							unset($ra);
							$this->db->update('ebay', array('price_ch1' => trim((string)$i->BuyItNowPrice), 'price_ch2' => trim((string)$i->BuyItNowPrice)), array('e_id' => (int)$data['eid']));
							}
						}
						$inserted[$itemid] = true;	
						unset($itemid);
				}
				
				
				}
				
			}
		}
	echo '<h1>'.$insactive.'</h1>';	
	////////////////
	$sold = array();
	$unsold = array();
	$this->db->where('etype !=', 'a');
	$query = $this->db->get('ebay_live');

		if ($query->num_rows() > 0) 
			{
				foreach ($query->result_array() as $row)
				{
					if ($row['etype'] == 's') $sold[$row['ebtrid']] = $row;
					else $unsold[$row['itemid']] = $row;
				}
			}			
	////	
	foreach($list['sold'] as $k=>$v)
	{
		foreach ($v->OrderTransaction as $i)
			{
				if (isset($sold[(int)$i->Transaction->TransactionID]))
				{					
					if ($sold[(int)$i->Transaction->TransactionID]['eid'] == 0 && isset($mitemids[(int)$i->Transaction->Item->ItemID]))
					{
						$itemid = (int)$i->Transaction->Item->ItemID;
						if (isset($mitemids[$itemid]))
						{
						$data = array();
							 
						if ($sold[(int)$i->Transaction->TransactionID]['lq'] != $mitemids[$itemid]['qn_ch1']) $data['lq'] = $mitemids[$itemid]['qn_ch1'];
						if ($sold[(int)$i->Transaction->TransactionID]['lebq'] != $mitemids[$itemid]['ebayquantity']) $data['lebq'] = $mitemids[$itemid]['ebayquantity'];
						$data['eid'] = $mitemids[$itemid]['e_id'];
						if (isset($mitemids[$itemid]) && $mitemids[$itemid]['ebended'] != NULL) $data['locended'] = 1;
						else $data['locended'] = 0;
						if ($data['locended'] == $sold[(int)$i->Transaction->TransactionID]['locended']) unset($data['locended']);
					
						//printcool ($data);
						$this->db->update('ebay_live', $data, array('el_id' => $sold[(int)$i->Transaction->TransactionID]['el_id']));
						unset($data);
						}
						
					}
				}
				elseif (!isset($inserted[(int)$i->Transaction->TransactionID]))
				{
					$date = explode('T', $i->Transaction->CreatedDate);
					if (!isset($date[1]))  GoMail(array ('msg_title' => '$date[1] @ 429', 'msg_body' => printcool($i, true), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
					$date[0] = explode('-', $date[0]);
					$date[1] = explode(':', $date[1]);
					$date[1][2] = (int)$date[1][2];					
					$itemid = (int)$i->Transaction->Item->ItemID;
					$data = array(
						'etype' => 's',
						'itemid' => $itemid,
						'ebqs' => (int)$i->Transaction->QuantityPurchased,
						'ebavq' => (int)$i->Transaction->Item->SellingStatus->QuantitySold,
						'ebtq' => (int)$i->Transaction->Item->Quantity,
						'etitle' => trim(addslashes($i->Transaction->Item->Title)),
						'ebmkdate' => mktime ($date[1][0], $date[1][1], $date[1][2], $date[0][1], $date[0][2], $date[0][0]),
						'ebdate' => str_replace('T', ' ' , str_replace('Z', '' , $i->Transaction->CreatedDate)),
						'ebtrid' => $i->Transaction->TransactionID
						);
						$data['eid'] = 0;
						if (isset($mitemids[$itemid])) $data['lq'] = $mitemids[$itemid]['qn_ch1'];
						if (isset($mitemids[$itemid])) $data['lebq'] = $mitemids[$itemid]['ebayquantity'];
						if (isset($mitemids[$itemid])) $data['eid'] = $mitemids[$itemid]['e_id'];
						if (isset($mitemids[$itemid]) && $mitemids[$itemid]['ebended'] !='') $data['locended'] = 1;
						
						if (isset($mitemids[$itemid])  && (trim($i->Transaction->Item->Title) != trim(stripslashes($mitemids[$itemid]['e_title']))))
						{
							if ((int)$data['eid'] > 0) 
							{
							$ra['admin'] = 'Cron';
							$ra['time'] = CurrentTime();
							$ra['ctrl'] = 'CronnGetMyeBaySelling';
							$ra['field'] = 'e_title';
							$ra['atype'] = 'M';
							$ra['e_id'] = (int)$data['eid'];
							$ra['ebay_id'] = $itemid;
							$ra['datafrom'] = addslashes($mitemids[$itemid]['e_title']);
							$ra['datato'] = addslashes($i->Transaction->Item->Title);
										
							$this->db->insert('ebay_actionlog', $ra); 
							unset($ra);
							$this->db->update('ebay', array('e_title' => trim(addslashes($i->Transaction->Item->Title))), array('e_id' => (int)$data['eid']));
							}
						}			
						$this->db->insert('ebay_live', $data);
						$inserted[(int)$i->Transaction->TransactionID] = true;	
						unset($itemid);			
				}				
			}	
	}	
	foreach($list['unsold'] as $k=>$v)
	{		
		foreach ($v->Item as $i)
			{	
			if (isset($unsold[(int)$i->ItemID]))
				{
					if ($unsold[(int)$i->ItemID]['eid'] == 0 && isset($mitemids[(int)$i->ItemID]))
					{
						$itemid = (int)$i->ItemID;
						if (isset($mitemids[$itemid]))
						{
						$data = array();
							 
						if ($unsold[(int)$i->ItemID]['lq'] != $mitemids[$itemid]['qn_ch1']) $data['lq'] = $mitemids[$itemid]['qn_ch1'];
						if ($unsold[(int)$i->ItemID]['lebq'] != $mitemids[$itemid]['ebayquantity']) $data['lebq'] = $mitemids[$itemid]['ebayquantity'];
						$data['eid'] = $mitemids[$itemid]['e_id'];
						if (isset($mitemids[$itemid]) && $mitemids[$itemid]['ebended'] != NULL) $data['locended'] = 1;
						else $data['locended'] = 0;
						if ($data['locended'] == $unsold[(int)$i->ItemID]['locended']) unset($data['locended']);
					
						//printcool ($data);
						$this->db->update('ebay_live', $data, array('el_id' => $unsold[(int)$i->ItemID]['el_id']));
						unset($data);
						}
						
					}
				
				}
				elseif (!isset($inserted[(int)$i->ItemID]))
				{					
					$itemid = (int)$i->ItemID;
					$data = array(
						'etype' => 'u',
						'itemid' => $itemid,

						'ebavq' => (int)$i->QuantityAvailable,
						'ebtq' => (int)$i->Quantity,
						'etitle' => trim($i->Title),
						'ebmkdate' => mktime (),
						'ebdate' => str_replace('T', ' ' , str_replace('Z', '' , $i->ListingDetails->EndTime))
						);
						$data['eid'] = 0;
						if (isset($mitemids[$itemid])) $data['lq'] = $mitemids[$itemid]['qn_ch1'];
						if (isset($mitemids[$itemid])) $data['lebq'] = $mitemids[$itemid]['ebayquantity'];
						if (isset($mitemids[$itemid])) $data['eid'] = $mitemids[$itemid]['e_id'];
						if (isset($mitemids[$itemid]) && $mitemids[$itemid]['ebended'] != '') $data['locended'] = 1;
						
						if (isset($mitemids[$itemid])  && (trim($i->Title) != trim(stripslashes($mitemids[$itemid]['e_title']))))
						{
							if ((int)$data['eid'] > 0) 
							{
							$ra['admin'] = 'Cron';
							$ra['time'] = CurrentTime();
							$ra['ctrl'] = 'CronnGetMyeBaySelling';
							$ra['field'] = 'e_title';
							$ra['atype'] = 'M';
							$ra['e_id'] = (int)$data['eid'];
							$ra['ebay_id'] = $itemid;
							$ra['datafrom'] = addslashes($mitemids[$itemid]['e_title']);
							$ra['datato'] = addslashes($i->Title);
										
							$this->db->insert('ebay_actionlog', $ra); 
							unset($ra);
							 $this->db->update('ebay', array('e_title' => trim(addslashes($i->Title))), array('e_id' => (int)$data['eid']));
							}
						}			
						$this->db->insert('ebay_live', $data);
						$inserted[$itemid] = true;
						unset($itemid);
				}				
			}		
	}	
}*/
function _XML2Array($parent)
{
    $array = array();

    foreach ($parent as $name => $element) {
        ($node = & $array[$name])
            && (1 === count($node) ? $node = array($node) : 1)
            && $node = & $node[];

        $node = $element->count() ? $this->_XML2Array($element) : trim($element);
    }

    return $array;
}
function _ParseResponse($response = '')
{
	//$response = "<P><B>eBay returned the following error(s):</B><P>21919189 : You'll be able to list 657 more items and $5,132.02 more this month. <BR>You'll be able to list 657 more items and $5,132.02 more this month. Request to list more: https://scgi.ebay.com/ws/eBayISAPI.dll?UpgradeLimits&appId=0&refId=19 <P><B>eBay returned the following error(s):</B><P>21919189 : You'll be able to list 651 more items and $3,818.02 more this month. <BR>You'll be able to list 651 more items and $3,818.02 more this month. Request to list more: https://scgi.ebay.com/ws/eBayISAPI.dll?UpgradeLimits&appId=0&refId=19 <P><B>eBay returned the following error(s):</B><P>21919189 : You'll be able to list 649 more items and $3,582.02 more this month. <BR>You'll be able to list 649 more items and $3,582.02 more this month. Request to list more: https://scgi.ebay.com/ws/eBayISAPI.dll?UpgradeLimits&appId=0&refId=19 <P><B>eBay returned the following error(s):</B><P>21919188 : This listing would cause you to exceed the amount ($525,000.00) you can list. <BR>This listing would cause you to exceed the amount you can list. You can list up to $3,582.02 more in total sales this month. Please consider reducing the starting price or request to list more: https://scgi.ebay.com/ws/eBayISAPI.dll?UpgradeLimits&appId=0&refId=19 .<P><B>eBay returned the following error(s):</B><P>21919189 : You'll be able to list 645 more items and $3,518.22 more this month. <BR>You'll be able to list 645 more items and $3,518.22 more this month. Request to list more: https://scgi.ebay.com/ws/eBayISAPI.dll?UpgradeLimits&appId=0&refId=19";
	if (trim($response) == 'Success |')
	{
		$items[] = 'No Limits MSG at Last Revision<br>'.CurrentTime(); 	
	}
	else
	{
	$response = str_replace('<BR>', '<P>', $response);
	$response = explode("<P>", $response);	
	foreach ($response as $k => $v)
	{
		$response[$k] = str_replace(' more items and $', '|', $v);		
		$response[$k] = trim(str_replace("eBay returned the following error(s):", '', $response[$k]));
		$response[$k] = trim(str_replace(" more this month.", '', $response[$k]));
		$response[$k] = trim(str_replace("Request to list more: https://scgi.ebay.com/ws/eBayISAPI.dll?UpgradeLimits&appId=0&refId=19", '', $response[$k]));
		$response[$k] = trim(str_replace("21919189 : ", '', $response[$k]));
		$response[$k] = trim(str_replace("You'll be able to list", '', $response[$k]));
		$response[$k] = trim(str_replace("<BR>", '', $response[$k]));			
		$response[$k] = trim(str_replace("<P>", '', $response[$k]));			
		$response[$k] = trim(str_replace("</P>", '', $response[$k]));			
		$response[$k] = trim(str_replace("<B>", '', $response[$k]));			
		$response[$k] = trim(str_replace("</B>", '', $response[$k]));	
		
		$check = explode('|', $response[$k]);
		if (isset($check[1]))
		{
			$check[1] = explode('.', $check[1]);
			if (isset($check[1][1]) && is_numeric(trim($check[1][1])))
			{				
				$color = '<span style="color:white;">';
				$dollar = str_replace(',','',$check[1][0]);
				if ((int)$check[0] < 100 || (int)$do < 1000) $color = '<span style="color:red;">';
				$string = $color.(int)$check[0].' / $'. $check[1][0].'.'.$check[1][1].'<br>'.CurrentTime().'</span>';
				if (strlen($string) < 100) $items[] = $string; 	
			}
			
		}
	}
	}
	if (isset($items))
	{
		$last = end(array_keys($items));
		$this->db->update('settings', array('svalue'=>$items[$last]), array('skey' => 'ebayresponse'));
		//GoMail(array ('msg_title' => 'ebayresponse @'.CurrentTime(), 'msg_body' => $items[$last], 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
	}
}

function DoRevise($id = 0)
{
    
	$this->db->select("admin_id, email, ownnames");
		$query = $this->db->get('administrators');
		
		if ($query->num_rows() > 0) 
		{
			foreach ($query->result_array() as $a) $adm[$a['admin_id']] = $a;	
		}
	if ((int)$id > 0) $this->db->where('er_id',(int)$id);
	$r = $this->db->get('ebay_revise');
	if ($r->num_rows() > 0)
	{
		set_time_limit(900);
		ini_set('mysql.connect_timeout', 1200);
		ini_set('max_execution_time', 1200);  
		ini_set('default_socket_timeout', 1200); 
		
		$revs = $r->result_array();
		require($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
		$this->load->model('Myebay_model');
               
		foreach ($revs as $rev)
		{		

			$revid = $rev['e_id'];		
                        
			$item = $this->Myebay_model->GetItem((int)$revid);	
				
				log_message('error', 'REVISE START '.(int)$revid.' @ '.CurrentTime());
				if (!$item) { echo 'Item not found!';  }
				elseif ((int)$item['ebay_id'] == 0) $this->db->insert('ebay_revise_log', array('eid'=>$revid,'type'=>$rev['e_type'],'value'=>'X','oldvalue'=>'X','attime'=>CurrentTimeR(), 'atmk'=>mktime(),'response' => 'Not Commited, Listing Not Submited To eBay', 'place'=> $rev['place'], 'admin' => $rev['admin']));
				elseif ($item['ebended'] != '') $this->db->insert('ebay_revise_log', array('eid'=>$revid,'type'=>$rev['e_type'],'value'=>'X','oldvalue'=>'X','attime'=>CurrentTimeR(), 'atmk'=>mktime(),'response' => 'Not Commited, Listing Is Ended', 'place'=> $rev['place'], 'admin' => $rev['admin']));
				else
				{			
				
				$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
				$requestXmlBody .= '<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
				$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
				$requestXmlBody .= '<DetailLevel>ItemReturnAttributes</DetailLevel>';



				$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
				$requestXmlBody .= "<Version>$compatabilityLevel</Version>";						
				$requestXmlBody .= '<ItemID>'.(int)$item['ebay_id'].'</ItemID></GetItemRequest>';
				$verb = 'GetItem';
				$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
				$responseXml = $session->sendHttpRequest($requestXmlBody);
				if(stristr($responseXml, 'HTTP 404') || $responseXml == '') die('<P>Error sending request');
				$xml = simplexml_load_string($responseXml);
				log_message('error', 'REVISE 1 '.(int)$revid.' @ '.CurrentTime());
                                                                                
                                if ((string)$xml->Item->ItemID == '') 
				{ 
					log_message('error', 'ERROR: Invalid Item ID... '.(int)$revid.' @ '.CurrentTime()); 
					echo 'ERROR: Invalid Item ID...'; 
					
					//if ($rev['e_type'] == 'p') $newebayvalue = $item['price_ch1']; 
					//else  $newebayvalue = (int)$item['qn_ch1'];
					
					if ($rev['e_type'] == 'p') $newebayvalue = $rev['e_val']; 
					else  $newebayvalue = (int)$rev['e_val'];
					
					
					$this->db->insert('ebay_revise_log', array('eid'=>$revid,'type'=>$rev['e_type'],'value'=>$newebayvalue,'oldvalue'=>"?",'attime'=>CurrentTimeR(), 'atmk'=>mktime(),'response' => 'ERROR: Invalid Item ID...', 'sev' => 1, 'place'=> $rev['place'], 'admin' => $rev['admin']));
					GoMail(array ('msg_title' => 'ERROR: Invalid Item ID... '.(int)$revid.' / '.$item['ebay_id'].' @'.CurrentTime(), 'msg_body' => explore($xml,false).explore($item, false).explore($requestXmlBody, false), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
				}
				else 
				{
                                                                                
					if ($rev['e_type'] == 'p') 
					{log_message('error', 'REVISE 2p '.(int)$revid.' @ '.CurrentTime());
						$oldebayvalue = (string)$xml->Item->StartPrice;
						//$newebayvalue = $item['price_ch1'];
						$newebayvalue = floatval($rev['e_val']);
					}
					else
					{log_message('error', 'REVISE 2q '.(int)$revid.' @ '.CurrentTime());
						 $oldebayvalue = (int)$xml->Item->Quantity-(int)$xml->Item->SellingStatus->QuantitySold;
						 //$newebayvalue = (int)$item['qn_ch1'];	
						 $newebayvalue = (int)$rev['e_val'];
						 
						 //if((int)$item['qn_ch1'] == 0
						 if((int)$newebayvalue == 0 && $item['ebended'] == '') $this->db->update('ebay', array('ostock' => CurrentTime()), array('e_id' => (int)$id));				 
					}
				
				/*if ($rev['e_type'] == 'q' && $item['qn_ch1'] == 0)
				{
					
					
						$requestXmlBody  = '<?xml version="1.0" encoding="utf-8"?>
		<EndItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';				
						$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
						$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
						$requestXmlBody .= "<Version>$compatabilityLevel</Version>";	
						$requestXmlBody .= "<EndingReason>NotAvailable</EndingReason>
						<ItemID>".$item['ebay_id']."</ItemID>";												
						$requestXmlBody .= "</EndItemRequest>";											
						$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], 'EndItem');
										
						$responseXml = $session->sendHttpRequest($requestXmlBody);
						if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
							die('<P>Error sending request');
						$responseDoc = new DomDocument();
						$responseDoc->loadXML($responseXml);						
						$errors = $responseDoc->getElementsByTagName('Errors');
						$response = NULL;				
						if($errors->length > 0)
						{
							$code     = $errors->item(0)->getElementsByTagName('ErrorCode');
							$shortMsg = $errors->item(0)->getElementsByTagName('ShortMessage');			
							$response = str_replace(">", "&gt;", str_replace("<", "&lt;", $shortMsg->item(0)->nodeValue));
							$longMsg  = $errors->item(0)->getElementsByTagName('LongMessage');
							echo '<P>', $code->item(0)->nodeValue, ' : ', str_replace(">", "&gt;", str_replace("<", "&lt;", $shortMsg->item(0)->nodeValue));
							if(count($longMsg) > 0) echo '<BR>', str_replace(">", "&gt;", str_replace("<", "&lt;", $longMsg->item(0)->nodeValue));			
						} 
						else
						{ 
							$responses = $responseDoc->getElementsByTagName("EndItemResponse");
							$eachack = '';
							foreach ($responses as $response) 
							{
							  $acks = $response->getElementsByTagName("Ack");
								$ack   = $acks->item(0)->nodeValue;		
								$eachack .= $ack.' | ';  
							}							
							$response = $eachack;
						}
						$this->db->update('ebay', array('ebayquantity' => (int)$item['qn_ch1'], 'ebended' => CurrentTime(), 'endedreason' => 'Revised to zero by AutoReviser', 'sitesell' => 0), array('e_id' => (int)$id));	
						
						$this->_logaction('EbayInventoryUpdate', 'Q',array('Quantity @ eBay' => $oldebayvalue), array('Quantity @ eBay' => $item['qn_ch1']), $id, $item['ebay_id'], 0);
						$this->_logaction('EbayInventoryUpdate', 'Q',array('Local eBay Quantity' => $item['ebayquantity']), array('Local eBay Quantity' => $item['qn_ch1']), $id, $item['ebay_id'], 0);
						$this->db->insert('ebay_revise_log', array('eid'=>$revid,'type'=>$rev['e_type'],'value'=>$newebayvalue, 'oldvalue'=>$oldebayvalue, 'attime'=>CurrentTimeR(), 'atmk'=>mktime(),'response' => $response));

					
					
				}
				else
				{*/
						log_message('error', 'REVISE IFNOT EQUAL LAUNCHE ('.trim($newebayvalue).' / '.trim($oldebayvalue).')'.(int)$revid.' @ '.CurrentTime());	
                                                                                
				if (trim($newebayvalue) == trim($oldebayvalue))
				{
					$this->db->insert('ebay_revise_log', array('eid'=>$revid,'type'=>$rev['e_type'],'value'=>$newebayvalue,'oldvalue'=>$oldebayvalue,'attime'=>CurrentTimeR(), 'atmk'=>mktime(),'response' => 'Not Commited, eBay Value matches Local (System flow working properly)', 'place'=> $rev['place'], 'admin' => $rev['admin']));
                                        if ($rev['e_type'] == 'q' && $item['ebayquantity'] != $newebayvalue) $this->db->update('ebay', array('ebayquantity' => $newebayvalue), array('e_id' => (int)$revid));
                                        
                                }
				else
				{			
						log_message('error', 'REVISE IFNOT EQUAL GO ('.$newebayvalue.' / '.$oldebayvalue.')'.(int)$revid.' @ '.CurrentTime());
						$requestXmlBody  = '<?xml version="1.0" encoding="utf-8"?>
		<ReviseInventoryStatusRequest xmlns="urn:ebay:apis:eBLBaseComponents">';				
						$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
						$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
						$requestXmlBody .= "<Version>$compatabilityLevel</Version>
								";	
								
								//<Quantity>".$item['quantity']."</Quantity>			
						$requestXmlBody .= "<InventoryStatus>
						<ItemID>".$item['ebay_id']."</ItemID>";						
						if ($rev['e_type'] == 'p')
						{
							//$requestXmlBody .= "<StartPrice>".$item['price_ch1']."</StartPrice>";
							$requestXmlBody .= "<StartPrice>".floatval($newebayvalue)."</StartPrice>";
						}
						elseif ($rev['e_type'] == 'q')
						{
							//$requestXmlBody .= "<Quantity>".$item['qn_ch1']."</Quantity>";
							$requestXmlBody .= "<Quantity>".(int)$newebayvalue."</Quantity>";
						}
						$requestXmlBody .= "
						</InventoryStatus>
						</ReviseInventoryStatusRequest>";				
						
						//GoMail(array ('msg_title' => 'INVENTORY UPDATED '.(int)$revid.' @'.CurrentTime(), 'msg_body' => $requestXmlBody, 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
										
						$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], 'ReviseInventoryStatus');
										
						$responseXml = $session->sendHttpRequest($requestXmlBody);
						if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
							die('<P>Error sending request');
						
						$responseDoc = new DomDocument();
						$responseDoc->loadXML($responseXml);
                                                
										log_message('error', 'REVISE RESPONSE '.$rev['e_type'].' - '.(int)$revid.' @ '.CurrentTime());
						$errors = $responseDoc->getElementsByTagName('Errors');
						$response = NULL;				
						if($errors->length > 0)
						{
							
							echo '<P><B>eBay returned the following error(s):</B>';
							$code     = $errors->item(0)->getElementsByTagName('ErrorCode');
							$shortMsg = $errors->item(0)->getElementsByTagName('ShortMessage');
							log_message('error', 'REVISE RESPONSE ERRORS'.(int)$revid.' '.str_replace(">", "&gt;", str_replace("<", "&lt;", $shortMsg->item(0)->nodeValue)).' @ '.CurrentTime());
							$response = str_replace(">", "&gt;", str_replace("<", "&lt;", $shortMsg->item(0)->nodeValue));
							$longMsg  = $errors->item(0)->getElementsByTagName('LongMessage');
							echo '<P>', $code->item(0)->nodeValue, ' : ', str_replace(">", "&gt;", str_replace("<", "&lt;", $shortMsg->item(0)->nodeValue));
							if(count($longMsg) > 0) echo '<BR>', str_replace(">", "&gt;", str_replace("<", "&lt;", $longMsg->item(0)->nodeValue));			
						} 
						else
						{ //no errors
							log_message('error', 'REVISE RESPONSE NO ERRORS'.(int)$revid.' @ '.CurrentTime());

							//get results nodes
							$responses = $responseDoc->getElementsByTagName("ReviseInventoryStatusResponse");
							$eachack = '';
							foreach ($responses as $oresponse) 
							{
							  $acks = $oresponse->getElementsByTagName("Ack");
		/*				*/ 	  $ack   = $acks->item(0)->nodeValue;		
								$eachack .= $ack.' | ';  
							   $this->session->set_flashdata('success_msg', 'Result: '.$ack);
							} // foreach response
							
							$response = $eachack;
						}
						if ($rev['e_type'] == 'q')
						{
						
						log_message('error', 'REVISE UPDATE EBAY Q'.(int)$revid.' @ '.CurrentTime());
						//$this->db->update('ebay', array('ebayquantity' => (int)$item['qn_ch1']), array('e_id' => (int)$revid));	
						$this->db->update('ebay', array('ebayquantity' => (int)$newebayvalue, 'quantity' => (int)$newebayvalue), array('e_id' => (int)$revid));
                                                                                
						$linkBase = "http://cgi.ebay.com/ws/eBayISAPI.dll?ViewItem&item=";						 
						
						//$this->_logaction('DoAutoRevise', 'Q',array('Quantity @ eBay' => $oldebayvalue), array('Quantity @ eBay' => $item['qn_ch1']), $revid, $item['ebay_id'], 0);
						//$this->_logaction('DoAutoRevise', 'Q',array('Local eBay Quantity' => $item['ebayquantity']), array('Local eBay Quantity' => $item['qn_ch1']), $revid, $item['ebay_id'], 0);
						
						$this->_logaction('DoAutoRevise', 'Q',array('Quantity @ eBay' => $oldebayvalue), array('Quantity @ eBay' => $newebayvalue), $revid, $item['ebay_id'],0,$rev['admin']);
						$this->_logaction('DoAutoRevise', 'Q',array('Local eBay Quantity' => $item['ebayquantity']), array('Local eBay Quantity' => $newebayvalue), $revid, $item['ebay_id'], 0,$rev['admin']);
						}						
						else
						{
							log_message('error', 'REVISE UPDATE EBAY P'.(int)$revid.' @ '.CurrentTime());
							//$this->_logaction('DoAutoRevise', 'M',array('eBay Channel Price' => $oldebayvalue), array('eBay Channel Price' => $item['price_ch1']), $revid, $item['ebay_id'], 0);
							//$this->load->model('Myautopilot_model');	
							//if($item['qn_ch1'] >0) $this->Myautopilot_model->ResetRules((int)$revid, 'DoRevise');
							//$this->Myautopilot_model->LogPriceChange((int)$revid, $oldebayvalue, $item['price_ch1'], 0);
							
							$this->_logaction('DoAutoRevise', 'M',array('eBay Channel Price' => $oldebayvalue), array('eBay Channel Price' => $newebayvalue), $revid, $item['ebay_id'], 0, $rev['admin']);
							$this->load->model('Myautopilot_model');	
							if($newebayvalue >0) $this->Myautopilot_model->ResetRules((int)$revid, 'DoRevise');
							$this->Myautopilot_model->LogPriceChange((int)$revid, $oldebayvalue, $newebayvalue, $rev['admin']);
						}
						log_message('error', 'REVISE LOG'.(int)$revid.' @ '.CurrentTime());
						$sev = 0;
						if (trim($response) == 'FixedPrice item ended.' || trim($response) == 'ERROR: Invalid Item ID...' || trim($response) == 'Quantity is not valid.') {
						$sev = 1;
						GoMail(array ('msg_title' => 'Severe Error... '.$response.' - '.(int)$revid.' / '.$item['ebay_id'].' @'.CurrentTime(), 'msg_body' => explore($xml,false, 'xml').explore($item, false, 'Item').explore($requestXmlBody, false,'requestXmlBody'),explore ($responseXml, false,'responseXml'), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
						}
						$this->db->insert('ebay_revise_log', array('eid'=>$revid,'type'=>$rev['e_type'],'value'=>$newebayvalue,'oldvalue'=>$oldebayvalue,'attime'=>CurrentTimeR(), 'atmk'=>mktime(),'response' => $response, 'sev' => $sev, 'place'=> $rev['place'], 'admin' => $rev['admin']));
						$this->_ParseResponse($response);
						log_message('error', 'REVISE LOG END '.(int)$revid.' @ '.CurrentTime());
						
					}
				//}
				}
			
			}
                        $this->db->where('er_id',(int)$rev['er_id']);
                        $this->db->delete('ebay_revise');
                        
		}
	
	if ((int)$id == 0)
	{
		$this->db->truncate('ebay_revise'); 
                log_message('error', 'TRUNCATE END '.(int)$id.' @ '.CurrentTime());
	}
	
		
	}
	$this->RunItemSpecQue();
}
function RunItemSpecQue()
{
	$this->db->where('ts <=', mktime()-120);
	$ebis = $this->db->get('ebay_itemspec_que');
	if ($ebis->num_rows() >0)
	{
		require($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
		$this->load->model('Myebay_model');

		foreach ($ebis->result_array() as $res)
		{
			$item = $this->Myebay_model->GetItem((int)$res['e_id']);

			$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
			$requestXmlBody .= '<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
			$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
			$requestXmlBody .= '<DetailLevel>ItemReturnAttributes</DetailLevel>';
			$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
			$requestXmlBody .= "<Version>$compatabilityLevel</Version>";
			$requestXmlBody .= "<IncludeItemSpecifics>true</IncludeItemSpecifics>";
			$requestXmlBody .= '<ItemID>'.(int)$item['ebay_id'].'</ItemID></GetItemRequest>';
			//$requestXmlBody .= '<ItemID>'.'172186226202'.'</ItemID></GetItemRequest>';
			$verb = 'GetItem';
			$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
			$responseXml = $session->sendHttpRequest($requestXmlBody);
			if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
			{
				log_message('error', 'function PopulateItemSpecifics - GetItem doesn\'t return eBay item to update upc e_id=  '.(int)$id.' @ '.CurrentTime());
				GoMail(array ('msg_title' => 'function PopulateItemSpecifics - GetItem doesnt return eBay item to update upc e_id=  '.(int)$id.' @ '.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
			}
			$xml = simplexml_load_string($responseXml);
			//$xml->Item->ItemSpecifics->NameValueList;
			// printcool($xml->Item->ItemSpecifics);
			$itemspec = array();
			if (isset($xml->Item->ItemSpecifics->NameValueList)) foreach ($xml->Item->ItemSpecifics->NameValueList as $v)
			{
				$itemspec[(string)$v->Name] = (string)$v->Value;
			}
			if (count($itemspec) > 0 && strlen(serialize($itemspec)) >10 )
			{
				GoMail(array ('msg_title' => 'YES Item Specifics for Listing '.(int)(int)$res['e_id'], 'msg_body' => printcool($itemspec,true), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
				printcool ($itemspec);
				$this->db->update('ebay', array('eBay_specs' => serialize($itemspec)), array('e_id' => (int)(int)$res['e_id']));
			}
			else
			{
				GoMail(array ('msg_title' => 'No Item Specifics for Listing '.(int)(int)$res['e_id'], 'msg_body' => printcool($itemspec,true), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
			}
			$this->db->where('eisq_id', (int)$res['eisq_id']);
			$this->db->delete('ebay_itemspec_que');
		}
	}
}
function CheckServices()
	{
		$now = mktime(0,0,0,date("n"),date("j"),date("Y"));
		//printcool ($now);
		$this->db->where('dateendmk <=', (int)$now+2592000);//30 days
		$this->db->where('dateendmk >=', (int)$now-86400);
		$this->db->where('status !=', 1);
		$q = $this->db->get('services');
		if ($q->num_rows() > 0)
		{
			foreach ($q->result_array() as $k => $v)
			{
				if ($v['datebeginmk'] == $now && $v['status'] == 2)
				{
					$this->db->update('services', array('status' => 0), array('sid' => (int)$v['sid']));
					$msg_data['msg_title'] = 'Service now active';
					$msg_data['msg_body'] = $v['title'].'<br>
							Provider '.$v['provider'].'<br>
							From '.$v['datebegin'].'<br>
							To '.$v['dateend'].'<br>
							Status Set to Active on '.str_replace('/', '-', CurrentTimeR());
					$msg_data['msg_date'] = CurrentTime();

					GoMail($msg_data, $this->config->config['support_email'], $this->config->config['no_reply_email']);
				}
				$key = (((($v['dateendmk']-$now)/60)/60)/24);
				//printcool ($key);

				if ($key == 30 || $key == 5 || $key == 1 || $key == 0)
				{
					if ($key == 1) $msg_data['msg_title'] = '1 say until service expiry';
					elseif ($key == 0) $msg_data['msg_title'] = 'Service expiring today';
					else  $msg_data['msg_title'] = $key.' days until service expiry';
					$msg_data['msg_body'] = $v['title'].'<br>
							Provider '.$v['provider'].'<br>
							From '.$v['datebegin'].'<br>
							To '.$v['dateend'].'<br>
							Remaining '.$key.'  days.';
					$msg_data['msg_date'] = CurrentTime();

					GoMail($msg_data, $this->config->config['support_email'], $this->config->config['no_reply_email']);
				}

				if ($key < 0 && $v['status'] == 0)
				{
					$this->db->update('dogovori', array('status' => 1), array('sid' => (int)$v['sid']));
					$msg_data['msg_title'] = 'Service now expired';
					$msg_data['msg_body'] = $v['title'].'<br>
							Provider '.$v['provider'].'<br>
							From '.$v['datebegin'].'<br>
							To '.$v['dateend'].'<br>
							Status Set to Expired '.str_replace('/', '-', CurrentTimeR());
					$msg_data['msg_date'] = CurrentTime();

					GoMail($msg_data, $this->config->config['support_email'], $this->config->config['no_reply_email']);
				}
			}
		}
	}
function ServiceProblematic()
{
	set_time_limit(120);
		ini_set('mysql.connect_timeout', 120);
		ini_set('max_execution_time', 120);  
		ini_set('default_socket_timeout', 120); 
		
		require_once($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
		
		$verb = 'GetSellingManagerSaleRecord';
		
		//Create a new eBay session with all details pulled in from included keys.php
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
		
		$this->db->select('rec, et_id, itemid, transid, asc, paydata, paidtime');	
		$this->db->where('mkdt >= ', mktime()-864010);
		$this->db->where('paidtime', '');
		$this->db->where('paydata', NULL);
		$this->db->order_by("rec", "DESC");
		//$this->db->limit(10);
		$q = $this->db->get('ebay_transactions');
		
		if ($q->num_rows() > 0) 
		{
			foreach($q->result_array() as $t)
			 {
			 	
			 	//$updatedIds .=  "$t[itemid] ";

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
				
				$xml = simplexml_load_string($responseXml);
				$item = $xml->SellingManagerSoldOrder;					
			
				
				$ar = $this->_XML2Array($item->OrderStatus);
				 $ar = $ar['OrderStatus'];
	
				 if ($item && isset($ar['PaidTime']) && (CleanBadDate((string)$ar['PaidTime']) != $t['paidtime']) && (CleanBadDate((string)$ar['PaidTime']) != ''))
					{
						
						$this->db->update('ebay_transactions', array('paidtime' => CleanBadDate((string)$ar['PaidTime'])), array('et_id' => $t['et_id']));
						$this->_logaction('Transactions', 'B', array('PaidTime' => $t['paidtime']), array('PaidTime' => CleanBadDate((string)$ar['PaidTime'])), 0, $t['itemid'], $t['rec']);		
						$this->db->insert('admin_history', array('msg_type' => 1, 'msg_title' => 'Updated: PaidTime', 
											'msg_body' => '', 
											'msg_date' => CurrentTime(),
											'e_id' => $this->_GetEbayFromItemID($t['itemid']),
										  	'itemid' => $t['itemid'],
											'trec' => $t['rec'],
										  	'admin' => 'Auto',
										  	'sev' => '')); 
					}	
				 unset($ar['paidtime']);
				 $pd = serialize($ar);
				  if ($item && ($pd != $t['paydata']))
					{					
						$this->db->update('ebay_transactions', array('paydata' => $pd), array('et_id' => $t['et_id']));	
						$this->db->insert('admin_history', array('msg_type' => 1, 'msg_title' => 'Updated: Payment Data', 
											'msg_body' => '', 
											'msg_date' => CurrentTime(),
											'e_id' => $this->_GetEbayFromItemID($t['itemid']),
										  	'itemid' => $t['itemid'],
											'trec' => $t['rec'],
										  	'admin' => 'Auto',
										  	'sev' => '')); 
				
					}
				
				unset($item);
			 }
		}

}
function wlog($bcn, $id, $field, $from, $to, $place = false, $url = false)
{

	if (!$place) $place = $this->router->method;	
	if (!$url) $url = $place;
	$this->db->insert('warehouse_log', array('bcn' => $bcn, 'wid'=> $id, 'time' => CurrentTime(), 'ts' => mktime(), 'datafrom' => $from, 'datato' => $to, 'field' => $field, 'admin' => 'Cron', 'ctrl' => $place, 'url' => $url, 'year' => date('Y'), 'month' => date('m'), 'day' => date('d')));	
}
function ProcessTransactions($process = false)
	{
		set_time_limit(900);
		ini_set('mysql.connect_timeout', 1200);
		ini_set('max_execution_time', 1200);  
		ini_set('default_socket_timeout', 1200); 
		
		$this->printstr = '';

		$this->load->helper('directory');
		$this->load->helper('file');
		$list = read_file($this->config->config['ebaypath'].'/trans.txt');
		$xml = simplexml_load_string($list);
		$list = $xml->TransactionArray->Transaction;
		$insert = false;
		if ($list) foreach ($list as $l)
		{
			$tmpdate =  explode (' ', CleanBadDate($l->CreatedDate, '2475'));
			$date = explode('-', trim($tmpdate[0]));
			$time = explode(':', trim($tmpdate[1]));
			$mkdt = mktime((int)$time[0], (int)$time[1], (int)$time[2], (int)$date[1], (int)$date[2], (int)$date[0]);		
		
			if ($mkdt >= (mktime()-86401))
			{
				$inskey = (int)$l->ShippingDetails->SellingManagerSalesRecordNumber;
				$eid = $this->_ListingIdFromItemID((string)$l->Item->ItemID);
				
				if ((int)$eid > 0) $insert[$inskey]['e_id'] = $eid;
				else { unset($eid); $insert[$inskey]['e_id'] = 0; }
				
				$insert[$inskey]['datetime'] = CleanBadDate($l->CreatedDate, 2488);
				$insert[$inskey]['mkdt'] = $mkdt;
				$insert[$inskey]['rec'] = $inskey;
				//$insert[$inskey]['paid'] = floater((string)$l->AmountPaid);
				//if (isset($l->ShippingDetails->ShippingServiceUsed)) $insert[$inskey]['paid'] = ((float)$l->TransactionPrice*(int)$l->QuantityPurchased)+(float)$l->ShippingDetails->ShippingServiceUsed;
				//else $insert[$inskey]['paid'] = floater((float)$l->TransactionPrice*(int)$l->QuantityPurchased);
				//$insert[$inskey]['paid'] = floater((float)$l->TransactionPrice*(int)$l->QuantityPurchased);
				$insert[$inskey]['paid'] = floater((float)$l->TransactionPrice*(int)$l->QuantityPurchased);
				$insert[$inskey]['eachpaid'] = floater((float)$l->TransactionPrice);
				$insert[$inskey]['fee'] = floater((string)$l->FinalValueFee);
				$insert[$inskey]['shipping'] = (string)$l->ShippingDetails->ShippingServiceUsed;
				$insert[$inskey]['tracking'] = (string)$l->ShippingDetails->ShipmentTrackingNumber;
				if (isset($l->PaidTime)) $insert[$inskey]['paidtime'] = CleanBadDate((string)$l->PaidTime, 2495);
				else $insert[$inskey]['paidtime'] = ''; 
				$insert[$inskey]['itemid'] = (string)$l->Item->ItemID;
				$insert[$inskey]['buyerid'] = (string)$l->Buyer->UserID;
				$insert[$inskey]['buyeremail'] = (string)$l->Buyer->Email;
				$insert[$inskey]['qtyof'] = (int)$l->Item->Quantity;
				$insert[$inskey]['qty'] = (int)$l->QuantityPurchased;	
				$insert[$inskey]['asc'] = floater((string)$l->ActualShippingCost);	
				$insert[$inskey]['ssc'] = floater((string)$l->ShippingServiceSelected->ShippingServiceCost);
				$insert[$inskey]['ebsold'] = (string)$l->Item->SellingStatus->QuantitySold;	
				$insert[$inskey]['transid'] = (string)$l->TransactionID;
				$insert[$inskey]['notpaid'] = 0;
				$insert[$inskey]['refunded'] = 0;
				$insert[$inskey]['pendingpay'] = 0;
				$insert[$inskey]['customcode'] = 0;
				
				if (isset($l->SellerPaidStatus))
				{
				if ((string)$l->SellerPaidStatus == 'NotPaid')
				{
					$insert[$inskey]['notpaid'] = 1;
				}
				if ((string)$l->SellerPaidStatus == 'Refunded')
				{
					$insert[$inskey]['refunded'] = 1;
				}
				if ((string)$l->SellerPaidStatus == 'PaymentPendingWithPayPal' || (string)$l->Transaction->SellerPaidStatus == 'PaymentPending')
				{
					$insert[$inskey]['pendingpay'] = 1;
				}
				if ((string)$l->SellerPaidStatus == 'CustomCode')
				{
					$insert[$inskey]['customcode'] = 1;
				}
				
				}
				$insert[$inskey]['hk_amp'] = serialize(array('AmountPaid' => (string)$l->AmountPaid, 'AdjustmentAmount' => (string)$l->AdjustmentAmount, 'ConvertedAdjustmentAmount' => (string)$l->ConvertedAdjustmentAmount, 'ConvertedAmountPaid' => (string)$l->ConvertedAmountPaid,'ConvertedTransactionPrice' => (string)$l->ConvertedTransactionPrice));
			if (isset($l->ContainingOrder->ShippingDetails->SellingManagerSalesRecordNumber)) $insert[$inskey]['contorderid'] =(int)$l->ContainingOrder->ShippingDetails->SellingManagerSalesRecordNumber;
			}
				
		}
		
		if (is_array($insert)) ksort($insert);
		$echo = 'EB:'.count($insert);
		$unsetted = '<br>Unset: ';
		$unsetcount = 0;
		$this->db->select('et_id, e_id, rec, paid, eachpaid, fee, shipping, tracking, paidtime, qty, qtyof, asc, ssc, updated, ebsold,notpaid,refunded,pendingpay,customcode');	
		$this->db->where('mkdt >= ', mktime()-86401);
		$this->db->order_by("rec", "DESC");
		$q = $this->db->get('ebay_transactions');
		if ($q->num_rows() > 0) 
		{
		$echo .= ' DB:'.count($q->result_array());
			 foreach($q->result_array() as $t)
			 {					 
			 	if (isset($insert[$t['rec']]) && ($insert[$t['rec']]['paid'] == $t['paid'] && $insert[$t['rec']]['fee'] == $t['fee'] && $insert[$t['rec']]['shipping'] == $t['shipping'] && $insert[$t['rec']]['tracking'] == $t['tracking']  && $insert[$t['rec']]['paidtime'] == $t['paidtime'] && $insert[$t['rec']]['qtyof'] == $t['qtyof'] && $insert[$t['rec']]['asc'] == $t['asc'] && $insert[$t['rec']]['ssc'] == $t['ssc'])) { $unsetted .= $t['rec'].' '; $unsetcount++; unset($insert[$t['rec']]);  }
				elseif (isset($insert[$t['rec']]))
				{
					//$updatedata = $insert[$t['rec']];					
					$this->printstr .= explore ('<table>				
				<tr><td>Rec</td><td>'.$t['rec'].'</td><td></td></tr>
				<tr><td>Paid</td><td>'.$insert[$t['rec']]['paid'].'</td><td>'.$t['paid'].'</td></tr>
				<tr><td>Fee</td><td>'.$insert[$t['rec']]['fee'].'</td><td>'.$t['fee'].'</td></tr>
				<tr><td>Shipping</td><td>'.$insert[$t['rec']]['shipping'].'</td><td>'.$t['shipping'].'</td></tr>
				<tr><td>Tracking</td><td>'.$insert[$t['rec']]['tracking'].'</td><td>'.$t['tracking'].'</td></tr>
				<tr><td>PaidTime</td><td>'.$insert[$t['rec']]['paidtime'].'</td><td>'.$t['paidtime'].'</td></tr>
				<tr><td>QtyOf</td><td>'.$insert[$t['rec']]['qtyof'].'</td><td>'.$t['qtyof'].'</td></tr>
				<tr><td>Asc</td><td>'.$insert[$t['rec']]['asc'].'</td><td>'.$t['asc'].'</td></tr>
				<tr><td>Ssc</td><td>'.$insert[$t['rec']]['ssc'].'</td><td>'.$t['ssc'].'</td></tr>
				<tr><td>Sold</td><td>'.$insert[$t['rec']]['ebsold'].'</td><td>'.$t['ebsold'].'</td></tr>
				</table><br>', false);
					
					//unset($insert[$t['rec']]);
					/*
					$updstr = '';
					
					if ($updatedata['paid'] == $t['paid']) unset($updatedata['paid']);
					else $updstr .= ' Paid: '.IfFillEmpty($updatedata['paid'],'b').' / '.IfFillEmpty($t['paid'],'r').' |';
										
					if ($updatedata['fee'] == $t['fee']) unset($updatedata['fee']);
					else $updstr .= ' Fee: '.IfFillEmpty($updatedata['fee'],'b').' / '.IfFillEmpty($t['fee'],'r').' |';
										
					if ($updatedata['shipping'] == $t['shipping']) unset($updatedata['shipping']);
					else $updstr .= ' Shipping: '.IfFillEmpty($updatedata['shipping'],'b').' / '.IfFillEmpty($t['shipping'],'r').' |';
										
					if ($updatedata['tracking'] == $t['tracking']) unset($updatedata['tracking']);
					else $updstr .= ' Tracking: '.IfFillEmpty($updatedata['tracking'],'b').' / '.IfFillEmpty($t['tracking'],'r').' |';
										
					if ($updatedata['paidtime'] == $t['paidtime']) unset($updatedata['paidtime']);
					else $updstr .= ' PaidTime: '.IfFillEmpty($updatedata['paidtime'],'b').' / '.IfFillEmpty($t['paidtime'],'r').' |';
										
					if ($updatedata['qtyof'] == $t['qtyof']) unset($updatedata['qtyof']);
					else $updstr .= ' QtyOf: '.IfFillEmpty($updatedata['qtyof'],'b').' / '.IfFillEmpty($t['qtyof'],'r').' |'; 
										
					if ($updatedata['asc'] == $t['asc']) unset($updatedata['asc']);
					else $updstr .= ' ActShipCost: '.IfFillEmpty($updatedata['asc'],'b').' / '.IfFillEmpty($t['asc'],'r').' |'; 
										
					if ($updatedata['ssc'] == $t['ssc']) unset($updatedata['ssc']);
					else $updstr .= ' ShipCost: '.IfFillEmpty($updatedata['ssc'],'b').' / '.IfFillEmpty($t['ssc'],'r').' |'; 
					
					if ($insertdata['ssc'] != $t['ssc']) unset($updatedata['ssc']);
					else $updstr .= ' ShipCost: '.IfFillEmpty($insertdata['ssc'],'b').' / '.IfFillEmpty($t['ssc'],'r').' |'; 
					
					$updstr .= ' @ '.CurrentTime().'<br>';
					
					$updatedata['updated'] = $t['updated'].$updstr;
					printcool ($updstr);
					printcool ($updatedata);
					//$this->db->update('ebay_transactions', $updatedata, array('rec' => (int)$t['rec']));
					*/
					
					$updstr = '';
					$paychange = FALSE;
					$warehouse = array();		
					if ($insert[$t['rec']]['paid'] != $t['paid'])
					{
						$updstr .= ' Paid: '.IfFillEmpty($insert[$t['rec']]['paid'],'b').' / '.IfFillEmpty($t['paid'],'r').' |';
						$updatedata['paid'] = $insert[$t['rec']]['paid'];
						$paychange = TRUE;
						$warehouse['paid'] = $insert[$t['rec']]['eachpaid'];
					}
										
					if ($insert[$t['rec']]['fee'] != $t['fee'])
					{
						$updstr .= ' Fee: '.IfFillEmpty($insert[$t['rec']]['fee'],'b').' / '.IfFillEmpty($t['fee'],'r').' |';
						$updatedata['fee'] = $insert[$t['rec']]['fee'];
						$warehouse['sellingfee'] = (float)$insert[$t['rec']]['fee']/$t['qty'];
					}
										
					if ($insert[$t['rec']]['shipping'] != $t['shipping'] && $insert[$t['rec']]['shipping'] != '') 
					{
						$updstr .= ' Shipping: '.IfFillEmpty($insert[$t['rec']]['shipping'],'b').' / '.IfFillEmpty($t['shipping'],'r').' |';
						$updatedata['shipping'] = $insert[$t['rec']]['shipping'];
					}
										
					if ($insert[$t['rec']]['tracking'] != $t['tracking'] && $insert[$t['rec']]['tracking'] != '')
					{
						$updstr .= ' Tracking: '.IfFillEmpty($insert[$t['rec']]['tracking'],'b').' / '.IfFillEmpty($t['tracking'],'r').' |';
						$updatedata['tracking'] = $insert[$t['rec']]['tracking'];
					}
										
					if ($insert[$t['rec']]['paidtime'] != $t['paidtime'])
					{
						$updstr .= ' PaidTime: '.IfFillEmpty($insert[$t['rec']]['paidtime'],'b').' / '.IfFillEmpty($t['paidtime'],'r').' |';
						$updatedata['paidtime'] = $insert[$t['rec']]['paidtime'];
					}										
										
					if ($insert[$t['rec']]['qtyof'] != $t['qtyof'])
					{
						$updstr .= ' QtyOf: '.IfFillEmpty($insert[$t['rec']]['qtyof'],'b').' / '.IfFillEmpty($t['qtyof'],'r').' |'; 
						$updatedata['qtyof'] = $insert[$t['rec']]['qtyof'];
					}
					
					/*if ($insert[$t['rec']]['asc'] != $t['asc'] && $insert[$t['rec']]['asc'] > $t['asc']) 
					{
						$updstr .= ' ActShipCost: '.IfFillEmpty($insert[$t['rec']]['asc'],'b').' / '.IfFillEmpty($t['asc'],'r').' |'; 
						$updatedata['asc'] = $insert[$t['rec']]['asc'];
						$warehouse['shipped_actual'] = (float)$insert[$t['rec']]['asc']/$t['qty'];
					}*/
					
					if ($insert[$t['rec']]['ssc'] != $t['ssc'])// && floater($insert[$t['rec']]['ssc']) > 0) 
					{
						$updstr .= ' ShipCost: '.IfFillEmpty($insert[$t['rec']]['ssc'],'b').' / '.IfFillEmpty($t['ssc'],'r').' |'; 
						$updatedata['ssc_old'] =  floater($t['ssc']);
                                                $updatedata['ssc'] = $insert[$t['rec']]['ssc'];
						$warehouse['shipped'] = (float)$insert[$t['rec']]['ssc']/$t['qty'];
					}
					if ($insert[$t['rec']]['ebsold'] != $t['ebsold']) 
					{
						$updstr .= ' Sold: '.IfFillEmpty($insert[$t['rec']]['ebsold'],'b').' / '.IfFillEmpty($t['ebsold'],'r').' |'; 
						$updatedata['ebsold'] = $insert[$t['rec']]['ebsold'];
					}
					
						if ($insert[$t['rec']]['notpaid'] != $t['notpaid'])
						{
							$updatedata['notpaid'] = 1;
							$updstr .= ' Set To Not Paid |'; 
						}
						if ($insert[$t['rec']]['refunded'] != $t['refunded'])
						{
							$updatedata['refunded'] = 1;
							$updstr .= ' Set To Refunded |'; 
						}
						if ($insert[$t['rec']]['pendingpay'] != $t['pendingpay'])
						{
							$updatedata['pendingpay'] = 1;
							$updstr .= ' Set To Pending Pay |'; 
						}
						if ($insert[$t['rec']]['customcode'] != $t['customcode'])
						{
							$updatedata['customcode'] = 1;
							$updstr .= ' Set To CustomCode |'; 
						}
						
					$updstr .= '<br>';
					
					$updatedata['updated'] = $t['updated'].$updstr;
					$this->printstr .= explore($updstr, false);
					$this->printstr .= explore($updatedata,false);
					
					$this->db->update('ebay_transactions', $updatedata, array('rec' => (int)$t['rec']));
					if (strlen($updstr) > 7) $this->db->insert('admin_history', array('msg_type' => 1, 'msg_title' => 'Transaction Updated: '.$updstr, 'msg_body' => '', 'msg_date' => CurrentTime(),
												  'e_id' => $this->_GetEbayFromItemID($insert[$t['rec']]['itemid']),
												  'itemid' => $insert[$t['rec']]['itemid'],

												  'trec' => $t['rec'],
												  'admin' => 'Auto',
												  'sev' => '')); 
					if ($paychange)
					{						
												
						//$bcnarray = $this->_DoBCNS($insert[$t['rec']]);
/*						A - BCN

						G Ebay Title
						J Date Sold
						K Price Sold
						L Shipping
						M Where (ebay)
						U ItemID link to ebay listing*/
						//echo 'UPDATE';
						$e = $this->_GetEbayTitleFromItemID($insert[$t['rec']]['itemid']);
						
					}				  
					if (count($warehouse) > 0)
					{
						$this->load->model('Myseller_model');
						
						$bcns = $this->Myseller_model->getSales(array((int)$t['et_id']),1, TRUE, TRUE);
						if ($bcns) foreach($bcns as $wid)
						{
							foreach($warehouse as $k => $v)
							{								
							 	if ($v != $wid[$k] && $wid['vended'] == 1) $this->wlog($wid['bcn'], $wid['wid'], $k, $wid[$k], $v);	
								else unset($warehouse[$k]);
							}
							if (count($warehouse) > 0) $this->db->update('warehouse', $warehouse, array('wid' => (int)$wid['wid']));	
						}
					}
					unset($updatedata);
					unset($updstr);
					unset($insert[$t['rec']]);
					
				}
			 }			 
		}
		$echo .= ' FIN:'.count($insert);
		$this->printstr .= printcool ($insert,true);
	
		$this->printstr .= printcool ('------ INSERT LIST START -----',true);
		if ($insert) foreach($insert as $i) 
			{
				$i['et_id'] = $i['rec'];
				$this->db->insert('ebay_transactions', $i); 
				$i['et_id'] = $this->db->insert_id();
				
				$this->load->model('Myautopilot_model');	
				$this->Myautopilot_model->ResetRules((int)$i['e_id'], 'ProcessTransactions');
												
				$this->db->insert('ebay_cron', array('e_id' => $i['e_id'], 'data' => 'QuantityPurchased: '.(int)$i['qty'].' - of Quantity: '.(int)$i['qtyof'].' - QuantitySold: '.(string)$i['ebsold'].' - @ ProcessTransactions for '.$i['et_id'], 'time' => CurrentTime(), 'ts' => mktime()));
								
				if ($i['paid'] > 0 && $i['paidtime'] != '') $pay = ' <span style="color:#FF9900;">(Paid)</span>';
				else $pay = ' <span style="color:red;">(Unpaid)</span>';
				
					/*$this->db->insert('admin_history', array('msg_type' => 1, 'msg_title' => '<span style="color:blue;">New eBay Transaction</span>'.$pay, 'msg_body' => '', 'msg_date' => CurrentTime(),
												  'e_id' => $this->_GetEbayFromItemID($i['itemid']),
												  'itemid' => $i['itemid'],
												  'trec' => $i['rec'],
												  'admin' => 'Auto',
												  'sev' => '')); 
					*/
					
					$this->_DoBCNS($i);
					if ($i['paid'] > 0 && $i['paidtime'] != '')
					{
						//$bcnarray = $this->_DoBCNS($i);
/*						A - BCN

						G Ebay Title
						J Date Sold
						K Price Sold
						L Shipping
						M Where (ebay)
						U ItemID link to ebay listing*/
						$e = $this->_GetEbayTitleFromItemID($i['itemid']);
						/*echo 'INSERT';
						printcool ($e);
						printcool ($i);*/						
					}			
			}
		
		//GoMail(array ('msg_title' => $echo.' UN:'.$unsetcount.' @'.CurrentTime(), 'msg_body' => $this->printstr.$unsetted.' ('.$unsetcount.')', 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
		
		if ($process) Redirect('Myebay/GetEbayTransactions/Complete');
}
function _ListingIdFromItemID($itemid = '')
{
	$this->db->select('e_id');
	$this->db->where('ebay_id', (int)$itemid);
	$e = $this->db->get('ebay');
	if ($e->num_rows() > 0)
	{
		$er = $e->row_array();
		return $er['e_id'];	
	}
	else return 0;	
}


function _DoBCNS($i)
{
	
	/*$inskey = (int)$l->ShippingDetails->SellingManagerSalesRecordNumber;
				$insert[$inskey]['e_id'] = $this->_ListingIdFromItemID((string)$l->Item->ItemID);
				$insert[$inskey]['datetime'] = CleanBadDate($l->CreatedDate);
				$insert[$inskey]['mkdt'] = $mkdt;
				$insert[$inskey]['rec'] = $inskey;
				$insert[$inskey]['paid'] = (string)$l->AmountPaid;
				$insert[$inskey]['fee'] = (string)$l->FinalValueFee;
				$insert[$inskey]['shipping'] = (string)$l->ShippingDetails->ShippingServiceUsed;
				$insert[$inskey]['tracking'] = (string)$l->ShippingDetails->ShipmentTrackingNumber;
				$insert[$inskey]['paidtime'] = CleanBadDate((string)$l->PaidTime);
				$insert[$inskey]['itemid'] = (string)$l->Item->ItemID;
				$insert[$inskey]['buyerid'] = (string)$l->Buyer->UserID;
				$insert[$inskey]['buyeremail'] = (string)$l->Buyer->Email;
				$insert[$inskey]['qtyof'] = (int)$l->Item->Quantity;
				$insert[$inskey]['qty'] = (int)$l->QuantityPurchased;	
				$insert[$inskey]['asc'] = (string)$l->ActualShippingCost;	
				$insert[$inskey]['ssc'] = (string)$l->ShippingServiceSelected->ShippingServiceCost;
				$insert[$inskey]['ebsold'] = (string)$l->Item->SellingStatus->QuantitySold;	
				$insert[$inskey]['transid'] = (string)$l->TransactionID;*/
				
				
				$this->load->model('Myseller_model');
				
				$this->Myseller_model->AssignBCN($i, 1);
				/*
					$this->db->select('e_id, e_title, ebay_id, quantity, ebayquantity, e_part, e_qpart, ebsold');
					$this->db->where('ebay_id', $i['itemid']);
					$eb = $this->db->get('ebay');
					if ($eb->num_rows() > 0) 
						{
							$res = $eb->row_array();
							$qty = $res['quantity'];
							$resoldquantity = $qty;
							$res['e_part'] = commasep(commadesep($res['e_part']));
							$bcnsold = $res['e_part'];							
							$res['quantity'] = $res['quantity'] - $i['qty'];
							$bcncount = $this->_RealCount($res['e_part']);					
												
							$this->printstr .= printcool ($bcncount, true);
							if ($bcncount > 0) 
							{
								$bcns = explode(',', $res['e_part']);
								
								$this->printstr .= printcool ($bcns,true);		
								$this->printstr .= printcool ('BCN MOVE BEGIN', true);
								
								$start = 1;
								$moved = array();
								$unavailble = 0;
								while ($start <= $i['qty'])
								{
									if (isset($bcns[$bcncount-1]))
									{
										$moved[] = trim($bcns[$bcncount-1]);
										unset($bcns[$bcncount-1]);
										$bcncount = count($bcns);
									}
									else $unavailble++;																		
									$start++;
								}
								$returnmove = $moved;
								$moved = commasep(commadesep(implode(',', $moved)));
								if ((int)$unavailble > 0) $this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => '<span style="color:blue;">BCN Auto Update - </span><span style="color:red;">LISTING DOES NOT HAVE ENOUGH BCN ITEMS - "'.$unavailble.'" Unavailable for total required '.$i['qty'].'</span>', 'msg_body' => '', 'msg_date' => CurrentTime(),
												  'e_id' => $res['e_id'],
												  'itemid' => $res['ebay_id'],
												  'trec' => $i['rec'],
												  'admin' => 'Auto',
												  'sev' => 1));
								
								$this->db->update('ebay_transactions', array('sn' => $moved), array('rec' => (int)$i['rec']));
								
								
												  
								$this->printstr .= printcool ($moved, true);
								$this->printstr .= printcool ($bcns,true);
								$bcns = commasep(commadesep(implode(',', $bcns)));
								$this->printstr .= printcool ($bcns,true);
								
								$this->printstr .= printcool ('BCN MOVE END',true);
								$this->db->update('ebay', array('e_part' => $bcns, 'e_qpart' => $this->_RealCount($bcns), 'ngen' =>  $this->_CountGhosts($bcns)), array('e_id' => (int)$res['e_id']));
								
								$this->_logaction('Transactions', 'B', array('BCN' => $bcnsold), array('BCN' => $bcns), (int)$res['e_id'], $res['ebay_id'], $i['rec']);
								$this->_logaction('Transactions', 'B', array('BCN Count' => $this->_RealCount($bcnsold)), array('BCN Count' => $this->_RealCount($bcns)), (int)$res['e_id'], $res['ebay_id'], $i['rec']);
								$this->_logaction('Transactions', 'B', array('Transaction BCN' => ''), array('Transaction BCN' => $moved), (int)$res['e_id'], $res['ebay_id'], $i['rec']);
								
															
							
							}
							else
							{
							$this->printstr .= printcool ('<span class="red">Cannot allocate BCN pcs.</span> from Listing <a href="'.Site_url().'Myebay/Edit/'.$res['e_id'].'" target="_blank">'.$res['e_id'].'</a> | ItemID: <a href="http://cgi.ebay.com/ws/eBayISAPI.dll?ViewItem&item='.$res['ebay_id'].'" target="_blank">'.$res['ebay_id'].'</a> for Transaction record '.$i['rec'], true);
							$this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => '<span style="color:red;">Cannot auto allocate BCN piece</span> from Listing to Transaction', 'msg_body' => '', 'msg_date' => CurrentTime(),
												  'e_id' => $res['e_id'],
												  'itemid' => $res['ebay_id'],
												  'trec' => $i['rec'],
												  'admin' => 'Auto',
												  'sev' => 1));
							}
							$this->printstr .= printcool ('----', true);
							$colorstep = '#FF9900';
							if ($res['quantity'] < 1) $colorstep = 'red';
							

							$this->db->update('ebay', array('quantity' => $res['quantity'], 'ebayquantity' => $i['qtyof'], 'ebsold' => $i['ebsold']), array('e_id' => (int)$res['e_id']));
							$this->_logaction('Transactions', 'Q', array('Quantity' => $resoldquantity), array('Quantity' => $res['quantity']), (int)$res['e_id'], $res['ebay_id'], $i['rec']);
							$this->_logaction('Transactions', 'Q', array('Local eBay Quantity' => $res['ebayquantity']), array('Local eBay Quantity' => $i['qtyof']-$i['ebsold']), (int)$res['e_id'], $res['ebay_id'], $i['rec']);
							
							$this->_logaction('Transactions', 'Q', array('Sold' => $res['ebsold']), array('Sold' => $i['ebsold']), (int)$res['e_id'], $res['ebay_id'], $i['rec']);
							
							
								
						}
						else
						{
							$this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => 'Listing with eBay ItemID <span style="color:red">NOT FOUND</span> in database. Listing quantity to be manually changed, deduction by <span style="color:#FF9900;">'.$i['qty'].'</span>', 'msg_body' => '', 'msg_date' => CurrentTime(),
												  'e_id' => 0,
												  'itemid' => $i['itemid'],
												  'trec' => $i['rec'],
												  'admin' => 'Auto',
												  'sev' => 1)); 							
						}
						
						if (isset($returnmove)) return $returnmove;*/
}
/*
function _CountGhosts($bcnstr = '')
{
	$ghosts = 0;
	$bcnstr = explode(',', $bcnstr);
	foreach ($bcnstr as $b)
	{
		if (substr(trim($b), 0, 1) == 'G') $ghosts++;
	}
	return (int)$ghosts;
}
*/
	function GetCats()
	{

		set_time_limit(120);
		ini_set('mysql.connect_timeout', 120);
		ini_set('max_execution_time', 120);
		ini_set('default_socket_timeout', 120);
		require_once($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');

		$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
		$requestXmlBody .= '<GetStoreRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
		$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
		$requestXmlBody .= "<Version>$compatabilityLevel</Version>";
		$requestXmlBody .= '<CategoryStructureOnly>TRUE</CategoryStructureOnly>
		<UserID>'.$ebayuserid.'</UserID></GetStoreRequest>';

		$verb = 'GetStore';
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
		$responseXml = $session->sendHttpRequest($requestXmlBody);
		if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
			die('<P>Error sending request');
		$this->load->helper('directory');
		$this->load->helper('file');
		if ($responseXml)
		{
			$store = simplexml_load_string($responseXml);

			$this->cs = array();
			$this->dbs = array();
			$this->listorder = 0;
			//printcool($store);
			if (isset($store->Store->CustomCategories->CustomCategory)) $this->_storecatting($store->Store->CustomCategories->CustomCategory);
			//printcool ($this->cs);
			//printcool ($this->dbs);

			$this->db->where("wsc_title <>", "ACTIONS");
			$this->db->orderby('listorder', 'ASC');
			$categories = $this->db->get("warehouse_sku_categories")->result_array();
			if (count($categories) > 0)
			{
				foreach ($categories as $c)
				{
					if (isset($this->dbs[$c['wsc_id']]))
					{
						if ($c['wsc_title'] != $this->dbs[$c['wsc_id']]['name'] || (int)$c['wsc_parent'] != (int)$this->dbs[$c['wsc_id']]['parent'] || (int)$c['leaf'] != (int)$this->dbs[$c['wsc_id']]['leaf'] || (int)$c['listorder'] != (int)$this->dbs[$c['wsc_id']]['listorder'])
						{
							$this->db->update('warehouse_sku_categories', array('wsc_title' => $this->dbs[$c['wsc_id']]['name'], 'wsc_parent' => (int)$this->dbs[$c['wsc_id']]['parent'], 'leaf' => (int)$this->dbs[$c['wsc_id']]['leaf'], 'path' => $this->dbs[$c['wsc_id']]['path'],'listorder' => $this->dbs[$c['wsc_id']]['listorder'],'notebay'=> 0),array('wsc_id' => $c['wsc_id']));

							GoMail(array ('msg_title' => 'Store Category Updated @ '.CurrentTime(), 'msg_body' => printcool($this->dbs[$c['wsc_id']],'UPDATE', TRUE).printcool($c,'WAS IN DB', TRUE), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
						}
					}
					else
					{
						//$this->db->where('wsc_id', (int)$c['wsc_id']);
						//$this->db->delete('warehouse_sku_categories');
						$this->db->update('warehouse_sku_categories', array('notebay' => 1, 'path' => $c['wsc_title']),array('wsc_id' => $c['wsc_id']));
						GoMail(array ('msg_title' => 'Store Category Deleted @ '.CurrentTime(), 'msg_body' => printcool($this->dbs,'DBS', TRUE).printcool($c,'WAS IN DB', TRUE), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
					}
					unset($this->dbs[$c['wsc_id']]);
				}
			}
			if (count($this->dbs) > 0)
			{
				foreach($this->dbs as $k => $v)
				{
					$this->db->insert('warehouse_sku_categories', array('wsc_id' => (int)$k, 'wsc_title' => $v['name'], 'wsc_parent' => (int)$v['parent'], 'leaf' => (int)$v['leaf'], 'path' => $v['path'],'listorder' => $this->dbs[$c['wsc_id']]['listorder']));
					GoMail(array ('msg_title' => 'Store Category Inserted @ '.CurrentTime(), 'msg_body' => printcool($this->dbs[$c['wsc_id']],'INSERT', TRUE), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
				}
			}
		}
		exit();



		set_time_limit(120);
		ini_set('mysql.connect_timeout', 120);
		ini_set('max_execution_time', 120);
		ini_set('default_socket_timeout', 120);
		require_once($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');

		$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
		$requestXmlBody .= '<GetStoreRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
		$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
		$requestXmlBody .= "<Version>$compatabilityLevel</Version>";
		$requestXmlBody .= '<CategoryStructureOnly>TRUE</CategoryStructureOnly>
		<UserID>'.$ebayuserid.'</UserID></GetStoreRequest>';

		$verb = 'GetStore';

		//Create a new eBay session with all details pulled in from included keys.php
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);

		//send the request and get response
		$responseXml = $session->sendHttpRequest($requestXmlBody);
		if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
			die('<P>Error sending request');

		$this->load->helper('directory');
		$this->load->helper('file');
		if ($responseXml)
		{
			if (!write_file($this->config->config['ebaypath'].'/cats.txt', $responseXml))
			{
				GoMail(array ('msg_title' => 'Unable to write Cats.txt @ '.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
				echo 'Unable to update Cats.';
			}
			else
			{
				//GoMail(array('msg_title' => 'Cats written @ '.CurrentTime(), 'msg_body' => $responseXml, 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
				//echo 'Cats updated. Refresh the admin view for the product now and close this window.';
			}
		}
		else
		{

			GoMail(array ('msg_title' => 'No Data for Cats.txt @ '.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);

		}

	}
	function _catstruct($obj, $lvl = 1,$parent = '', $parentid = 0)
	{//echo 123;
		$this->listorder++;
		$a = array();
		//printcool($this->cs);
		$a = (array)$obj;
		$cnt = 1;
		$indent = '';
		//$txtindent = '';
		//if ($lvl > 2)  printcool($a,false,'lvl-'.$lvl);

		//while ($cnt < $lvl)
		//{
		//	$txtindent .= $indent;
		//	$cnt++;
		//}
		if (isset($a['Name']) && $a['Name'] !='')
		{
			if(!isset($this->cs[$a['CategoryID']]))
			{	 if ($parent != '') $parent = $parent.' > ';
				$parent = $this->cs[$a['CategoryID']] = $parent.$a['Name'];
				$a['parent'] = $parent;
				if (isset($a['ChildCategory']) && count($a['ChildCategory']) > 0) $leaf = 1;
				else $leaf  =0;
				$this->dbs[$a['CategoryID']] = array('name' => $a['Name'], 'parent' => (int)$parentid,'leaf' => $leaf, 'path' => $parent,'listorder'=> $this->listorder);

			}
			//$parent = $this->cs[$a['CategoryID']] = $parent.$a['Name'];

		}
		if (isset($a['ChildCategory']))
		{
			//printcool ( $parent,false,'0 '.$a['CategoryID'].' '.$a['Name']);
			//printcool($lvl);
			$this->_catstruct($a['ChildCategory'],($lvl+1), $parent, $a['CategoryID']);
			//printcool ( $parent,false,'ppp '.$a['CategoryID']).' '.$a['Name'];

			if(count($a['ChildCategory']) > 0)
			{
				//printcool($parent,false,'parent1 '.$a['CategoryID'].' '.$a['Name']);
				//$parent = $parent.$a['Name'];
				foreach($a['ChildCategory'] as $s1)
				{
					$s1 = (array)$s1;
					//printcool ($s1,false,'s1');
					if (isset($s1['Name']) && $s1['Name'] !='')
					{
						$s1['parent'] = $parent.' > ';
						$s1['parentid'] = $a['ChildCategory'];
					}
					//printcool ($s1['parent'],false,'S1 2');

					$this->_catstruct($s1,($lvl+2),$parent, $a['CategoryID']);
				}
			}

		}
	}
	function _storecatting($cc)
	{
		$lvl = 1;
		foreach ($cc as $s)
		{
			$this->_catstruct($s,$lvl);
		}
		//printcool($this->cs);
	}
function _BCNRegen($eid = 0)
{
	$this->db->select('ebayquantity');
	$this->db->where('e_id', (int)$eid);
	$l = $this->db->get('ebay');
	if ($l->num_rows() > 0)
	{
		$ls = $l->row_array();
		$ebq = $ls['ebayquantity'];	
		//printcool ($ebq);
		$this->db->insert('ebay_cron', array('e_id' => (int)$eid, 'data' => 'BCNRegen from '.$eid.' - eBayQuantity:'.$ebq, 'time' => CurrentTime(), 'ts' => mktime()));

		$this->load->model('Myseller_model');
		$bcns = $this->Myseller_model->getBase(array((int)$eid),TRUE);
		$lq = 0;
		$lqg = array();
		
		//if ((int)$eid == 14516) GoMail(array ('msg_title' => '14516 BCNS @ '.CurrentTime(), 'msg_body' => printcool ($bcns, true, 'bcns'), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
		
		if ($bcns) foreach($bcns as $k => $wid)
		{
			if ($wid['status'] == 'Listed' && ($wid['channel'] == 0 || $wid['channel'] == 1)) 
			{
				 $lq++;				
				 if ($wid['generic'] != 0) {  $lqg[] = $wid['wid']; unset($bcns[$k]); }
			}
		}	
		$this->db->insert('ebay_cron', array('e_id' => (int)$eid, 'data' => 'BCNRegen from '.$eid.' - Counted Local Ghosts:'.count($lqg), 'time' => CurrentTime(), 'ts' => mktime()));

		//printcool ($lq);
		//$lq = $lq-5;
		if ($ebq > $lq) 
		{
			$this->db->insert('ebay_cron', array('e_id' => (int)$eid, 'data' => 'BCNRegen from '.$eid.' - GoGenGhost:'.($ebq-$lq), 'time' => CurrentTime(), 'ts' => mktime()));		
			$this->Myseller_model->AutoGhoster((int)$eid, ($ebq-$lq), $bcns);
		}		
		elseif ($ebq < $lq) 
		{
			$this->db->insert('ebay_cron', array('e_id' => (int)$eid, 'data' => 'BCNRegen from '.$eid.' - ToRem:'.($lq-$ebq), 'time' => CurrentTime(), 'ts' => mktime()));
			
			GoMail(array ('msg_title' => 'AutoUnGhoster RUN: '.(int)$eid.' @ '.CurrentTime(), 'msg_body' => printcool($lq,true,'$lq').printcool($ebq,true,'$ebq').printcool($lqg,true,'$lqg').printcool($bcns,true,'$bcns'), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
			
			$this->Myseller_model->AutoUnGhoster((int)$eid, ($lq-$ebq), $lqg, $bcns,$lq);
		}
		else
		{
			$this->db->insert('ebay_cron', array('e_id' => (int)$eid, 'data' => 'BCNRegen from '.$eid.' - Nothing to change, all is good', 'time' => CurrentTime(), 'ts' => mktime()));
		}
	}
	
	
	
}
function GetSellerEvents()
{
	//$this->DoRevise();
	//sleep(10);
	
require_once($this->config->config['ebaypath'].'get-common/keys.php');
						require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');


						$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
						$requestXmlBody .= '<GetSellerEventsRequest xmlns="urn:ebay:apis:eBLBaseComponents">
';
						$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
						//$requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';
						$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
						$requestXmlBody .= "<Version>$compatabilityLevel</Version>";						
						$requestXmlBody .= "<ModTimeFrom>".date('Y-m-d H:i:s', strtotime("-1 days"))."</ModTimeFrom>
  <ModTimeTo>".date('Y-m-d H:i:s')."</ModTimeTo><IncludeWatchCount>FALSE</IncludeWatchCount><OutputSelector>ItemArray.Item.ItemID,ItemArray.Item.SellingStatus.CurrentPrice,ItemArray.Item.SellingStatus.QuantitySold,ItemArray.Item.Title,ItemArray.Item.Quantity</OutputSelector></GetSellerEventsRequest>";
						$verb = 'GetSellerEvents';
						$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
						$responseXml = $session->sendHttpRequest($requestXmlBody);
						if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
							die('<P>Error sending request');
						
						$xml = simplexml_load_string($responseXml);

						//printcool ($xml);
						$stats['xml'] = count($xml->ItemArray->Item);
						$start = 1;
						$this->db->select('e_id, price_ch1, price_ch2, e_title, ebayquantity, qn_ch1, qn_ch2, ebay_id, ebsold, e_part');
						$this->db->order_by("e_id", "DESC");
						//e//
						if ($xml->ItemArray->Item) foreach ($xml->ItemArray->Item as $i)
						{
							if ($start == 1) $this->db->where('ebay_id', (int)$i->ItemID);
							else $this->db->or_where('ebay_id', (int)$i->ItemID);
							
							$data[(int)$i->ItemID] = array('ebay_id' => (int)$i->ItemID, 'price_ch1' => (string)trim($i->SellingStatus->CurrentPrice), 'price_ch2' => (string)trim($i->SellingStatus->CurrentPrice), 'e_title' => (string)trim($i->Title), 'ebsold' => (string)$i->SellingStatus->QuantitySold);
							$start++;

						}
						//e//
						else exit();
						
						$mod = array();
						$dup = array();
						//PUT HERE
						$query = $this->db->get('ebay');
						
						if ($query->num_rows() > 0) 
									{
																//printcool ($query->result_array());
									foreach ($query->result_array() as $q)
										{
											if (isset($ebdb[$q['ebay_id']])) $dup[$q['ebay_id']] = $q;
											$ebdb[$q['ebay_id']] = $q;											
										}
										
									foreach ($ebdb as $q)
										{
										//e//
										
										///ItemArray.Item.SellingStatus.QuantitySoldQuantitySold
											if (isset($data[$q['ebay_id']]))
											{
											if ((string)trim($q['price_ch1']) != $data[$q['ebay_id']]['price_ch1']) 
											{
												 $mod[$q['e_id']]['price_ch1'] = floater($data[$q['ebay_id']]['price_ch1']);
												 $this->load->model('Myautopilot_model');	
												$this->Myautopilot_model->ResetRules((int)$q['e_id'], 'GetSellerEvents');
												$this->Myautopilot_model->LogPriceChange((int)$q['e_id'], $q['price_ch1'], $mod[$q['e_id']]['price_ch1'], 0);
											}
											if ((string)trim($q['price_ch2']) != $data[$q['ebay_id']]['price_ch2']) $mod[$q['e_id']]['price_ch2'] = floater($data[$q['ebay_id']]['price_ch2']);
											if ((int)$q['ebsold'] != (int)$data[$q['ebay_id']]['ebsold']) $mod[$q['e_id']]['ebsold'] = $data[$q['ebay_id']]['ebsold'];
											if ((string)trim($q['e_title']) != $data[$q['ebay_id']]['e_title']) 
											{ 
												$mod[$q['e_id']]['e_title'] = $data[$q['ebay_id']]['e_title']; 
												//$gdrvmod[$q['e_id']]['e_title'] = $data[$q['ebay_id']]['e_title'];
												//$gdrvmod[$q['e_id']]['e_part'] = $ebdb[$q['ebay_id']]['e_part'];
											
											}
											/*if (((int)trim($q['ebayquantity']) != (int)$data[$q['ebay_id']]['ebayquantity']) || ((int)trim($q['ebayquantity']) != (int)$data[$q['ebay_id']]['qn_ch1']) || ((int)trim($q['ebayquantity']) != (int)$data[$q['ebay_id']]['qn_ch2']))
											{
												$mod[$q['e_id']]['qn_ch1'] = $mod[$q['e_id']]['qn_ch2'] = $mod[$q['e_id']]['ebayquantity'] = $data[$q['ebay_id']]['ebayquantity'];
											}*/
											if (isset($mod[$q['e_id']])) { $mod[$q['e_id']]['ebay_id'] = $data[$q['ebay_id']]['ebay_id']; $local[$q['e_id']] = $q; }
											unset($data[$q['ebay_id']]);
											
											}
										}
									}
								

									
									foreach ($mod as $k => $v)
									{
										$bcnregen = false;
										if (isset($data[$v['ebay_id']])) unset($data[$v['ebay_id']]);										
										$ebid = $v['ebay_id'];
										unset($v['ebay_id']);
										
										foreach ($v as $kk => $vv)
										{
											$field = array('ebsold' => 'eBay Sold', 'price_ch1' => 'Price eBay', 'price_ch2' => 'WebSite', 'e_title' => 'Title', 'ebayquantity' => 'Local eBay Quantity');
											$action = array('ebsold' => 'M', 'price_ch1' => 'M','price_ch2' => 'M', 'e_title' => 'M', 'ebayquantity' => 'Q');
											//if ($kk == 'ebayquantity') $bcnregen = TRUE;
											$this->_logaction('eBayEvents', $action[$kk], array($field[$kk] => $local[$k][$kk]), array($field[$kk] => $vv), (int)$k, $ebid, 0);
											
											if ($action[$kk] == 'price_ch1') $this->db->insert('autopilot_log', array('apl_listingid' => (int)$k, 'apl_from' => $local[$k][$kk], 'apl_to' => $vv, 'apl_adminid' => 0,'apl_nonuser'=>1, 'apl_time' => CurrentTime(), 'apl_tstime' => mktime()));
										}
										

									$this->db->update('ebay' ,$v, array('e_id' => (int)$k));
									if ($bcnregen) $this->_BCNRegen((int)$k);
										
									
									}
									$stats['mod'] = count($mod);
									$stats['notfnd'] = count($data);
									$stats['dup'] = count($dup);
									
									$stats['run'] = CurrentTimeR();
									
									
						/*if ($query->num_rows() > 0) 
									{
																//printcool ($query->result_array());
									foreach ($query->result_array() as $q)
										{
										//e//
											if (isset($data[$q['ebay_id']]))
											{
											if ((string)trim($q['buyItNowPrice']) != $data[$q['ebay_id']]['buyItNowPrice']) $mod[$q['e_id']]['buyItNowPrice'] = $data[$q['ebay_id']]['buyItNowPrice'];
											if ((string)trim($q['e_title']) != $data[$q['ebay_id']]['e_title']) $mod[$q['e_id']]['e_title'] = $data[$q['ebay_id']]['e_title'];
											if ((string)trim($q['ebayquantity']) != $data[$q['ebay_id']]['ebayquantity']) $mod[$q['e_id']]['ebayquantity'] = $data[$q['ebay_id']]['ebayquantity'];
											if (isset($mod[$q['e_id']])) { $mod[$q['e_id']]['ebay_id'] = $data[$q['ebay_id']]['ebay_id']; $local[$q['e_id']] = $q; }
											unset($data[$q['ebay_id']]);
											
											}
										}
									}
								

									
									foreach ($mod as $k => $v)
									{
										if (isset($data[$v['ebay_id']])) unset($data[$v['ebay_id']]);
										else $dup[$v['ebay_id']] = $k;
										$ebid = $v['ebay_id'];
										unset($v['ebay_id']);
										
										foreach ($v as $kk => $vv)
										{
											$field = array('buyItNowPrice' => 'Price', 'e_title' => 'Title', 'ebayquantity' => 'Local eBay Quantity');
											$action = array('buyItNowPrice' => 'M', 'e_title' => 'M', 'ebayquantity' => 'Q');
											//if ($kk == 'buyItNowPrice') { $field = 'Price'; $action = 'M'; } 
											//elseif ($kk == 'e_title') { $field = 'Title';  $action = 'M'; }
											//elseif ($kk == 'ebayquantity') { $field = 'Local eBay Quantity';  $action = 'Q'; }
											//printcool (array($field[$kk] => $local[$k][$kk]));
											//printcool (array($field[$kk] => $vv));
											
											$this->_logaction('eBayEvents', $action[$kk], array($field[$kk] => $local[$k][$kk]), array($field[$kk] => $vv), (int)$k, $ebid, 0);
										}
									
									//printcool ('update(\'ebay\' ,'.$v.', array(\'e_id\' => '.(int)$k.')');

									$this->db->update('ebay' ,$v, array('e_id' => (int)$k));
										
										
									
									}
									$stats['mod'] = count($mod);
									$stats['notfnd'] = count($data);
									$stats['dup'] = count($dup);
									
									$stats['run'] = CurrentTimeR();*/
									
									$stats['notfndlist'] = array();
									
									$stats['duplist'] = '';
									if ($stats['dup'] > 0) $stats['duplist'] = serialize($dup);

									foreach ($data as $k => $v) $stats['notfndlist'][$k] = $v['e_title'];									
									
									$stats['notfndlist'] = serialize($stats['notfndlist']);
									
									$this->db->insert('ebay_sellerevents', $stats); 
									$id = $this->db->insert_id();
									if ($stats['notfnd'] > 0) $this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => 'eBayEvents cound not find <span style="color:red;">'.$stats['notfnd'].'</span> local listings returned in change check. <a href="'.Site_url().'Myebay/CronEventsLog#'.$id.'" style="color: #419aff;" target="_blank">OPEN</a>', 'msg_body' => '', 'msg_date' => CurrentTime(),
												  'e_id' => 0,
												  'itemid' => 0,
												  'trec' => 0,
												  'admin' => 'Auto',
												  'sev' => 1)); 
											
									if ($stats['dup'] > 0) $this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => 'eBayEvents found <span style="color:red;">'.$stats['dup'].'</span> duplicate local listings for item ids. <a href="'.Site_url().'Myebay/CronEventsLog#'.$id.'" style="color: #419aff;" target="_blank">OPEN</a>', 'msg_body' => '', 'msg_date' => CurrentTime(),
												  'e_id' => 0,
												  'itemid' => 0,
												  'trec' => 0,
												  'admin' => 'Auto',
												  'sev' => 1)); 
									
									
}

function EndedListings()
{
	//exit('Testing');
	
	require_once($this->config->config['ebaypath'].'get-common/keys.php');
	require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
	$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
	$requestXmlBody .= '<GetSellerEventsRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
	$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
	//$requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';
	$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
	$requestXmlBody .= "<Version>$compatabilityLevel</Version>";						
	$requestXmlBody .= "<ModTimeFrom>".date('Y-m-d H:i:s', strtotime("-1 days"))."</ModTimeFrom><ModTimeTo>".date('Y-m-d H:i:s')."</ModTimeTo><IncludeWatchCount>FALSE</IncludeWatchCount><OutputSelector>ItemArray.Item.ItemID,ItemArray.Item.ListingDetails,ItemArray.Item.SellingStatus,ItemArray.Item.Quantity</OutputSelector>  </GetSellerEventsRequest>";

	$verb = 'GetSellerEvents';
	$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
	$responseXml = $session->sendHttpRequest($requestXmlBody);
	if(stristr($responseXml, 'HTTP 404') || $responseXml == '') die('<P>Error sending request');
						
	$xml = simplexml_load_string($responseXml);

	//foreach ($xml->ItemArray->Item as $i) printcool ($i);
	//exit();
	//GoMail(array ('msg_title' => 'Cron Ended Listings @ '. CurrentTime(), 'msg_body' => printcool($xml, TRUE), 'msg_date' => CurrentTime()), 'mitko@rusev.me', $this->config->config['no_reply_email']);
	$start = 1;
	$this->db->select('e_id, ebay_id, ebended');
	$this->db->order_by("e_id", "DESC");
	///
	
	if ($xml->ItemArray->Item) 
	foreach ($xml->ItemArray->Item as $i)
	{
		

		if ($i->SellingStatus->ListingStatus == 'Completed' || $i->SellingStatus->ListingStatus == 'Ended')
		{
			if ($start == 1) $this->db->where('ebay_id', (int)$i->ItemID);
			else $this->db->or_where('ebay_id', (int)$i->ItemID);
			
				$begin = CleanBadDate((string)trim($i->ListingDetails->StartTime), 3268);
				//2015-01-20 | 19:49:20
				$begin = explode(" ", $begin);
				$begin[0] = explode("-", trim($begin[0]));				
				$begin[1] = explode(":", trim($begin[1]));
				$begin = mktime ((int)$begin[1][0], (int)$begin[1][1], (int)$begin[1][2], (int)$begin[0][1], (int)$begin[0][2], (int)$begin[0][0]);
				$finish = CleanBadDate((string)trim($i->ListingDetails->EndTime), 3274);	
				$finish = explode(" ", $finish);
				$finish[0] = explode("-", trim($finish[0]));				
				$finish[1] = explode(":", trim($finish[1]));
				$finish = mktime ((int)$finish[1][0], (int)$finish[1][1], (int)$finish[1][2], (int)$finish[0][1], (int)$finish[0][2], (int)$finish[0][0]);
				$diff = $finish - $begin;				
				$diff = gmdate("d / H:i", $diff);				
				//if ($diff < 2592000) $reason = 'Premature ending ('.$diff.')';
				//else $reason = 'Listing ended ('.$diff.')';
			
			if ($i->SellingStatus->QuantitySold == $i->Quantity) $reason = 'All quantity sold ['.$diff.']';
			else $reason = 'Quantity not sold ('.$i->SellingStatus->QuantitySold.' of '.$i->Quantity.') ['.$diff.']';
			
			$data[(int)$i->ItemID] = array('ebended' => CleanBadDate((string)trim($i->ListingDetails->EndTime),3287), 'endedreason' => $reason, 'sitesell' => 0);
			
			$start++;
		}
	
		
	}
	///
	else exit();
	//exit();
	$updatestring = '';
	$endedupdate = 0;
	$query = $this->db->get('ebay');
						
	if ($query->num_rows() > 0) 
	{	
		$this->load->model('Myseller_model');	
																	
		foreach ($query->result_array() as $q)
		{

			if (isset($data[$q['ebay_id']]) && $q['ebended'] == '')
			{
			$updatestring .= 'Ebay Listing <a href="'.Site_url().'Myebay/Search/'.$q['e_id'].'" target="_blank" style="color: #419aff; font-size:10px;"><img src="'.Site_url().'images/admin/b_search.png" class="linkicon" />'.$q['e_id'].'</a> - ItemID: <a href="http://www.ebay.com/itm/'.$q['ebay_id'].'" target="_blank" style="color: #419aff; font-size:10px;"><img src="'.Site_url().'images/admin/b_search.png" class="linkicon"/>'.$q['ebay_id'].'</a> ended at '.$data[$q['ebay_id']]['ebended'].' - '.$data[$q['ebay_id']]['endedreason'].'<br>';
			
			
			
			$this->db->update('ebay' ,array('ebended' => $data[$q['ebay_id']]['ebended'], 'endedreason' => $data[$q['ebay_id']]['endedreason'], 'sitesell' => 0), array('e_id' => (int)$q['e_id']));									
			$endedupdate++;								
						
			
						$bcns = $this->Myseller_model->getBase(array((int)$q['e_id']),TRUE);
						if ($bcns) foreach($bcns as $wid)
						{
							if ($wid['status'] != 'Not Listed')
							{
							$wdata['status_notes'] = 'Changed from "'.$wid['status'].'" - Cron Ended';							
							if (trim($wid['status_notes']) != '') $wdata['status_notes'] .= ' | '.$wid['status_notes'];	
							$wdata['status'] = 'Not Listed';							
							$this->wlog($wid['bcn'], $wid['wid'], 'status', $wid['status'], $wdata['status']);	
							$this->db->update('warehouse', $wdata, array('wid' => (int)$wid['wid']));
							unset($wdata);	
							}

						}
						$this->Myseller_model->ProcessFinalCounts((int)$q['e_id']);
			}
			
		}
	}	
	
	
	
	if ($updatestring != '')
	{
		//echo $updatestring;
		$this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => 'Cron Ended Listings found <span style="color:red;">'.$endedupdate.'</span> ended listings for last 24 hours', 'msg_body' => $updatestring, 'msg_date' => CurrentTime(),
						  'e_id' => 0,
						  'itemid' => 0,
						  'trec' => 0,
						  'admin' => 'Auto',
						  'sev' => 0));
	}
	
	//printcool ($updatestring);
}
function _GetEbayFromItemID($itemid = '')
	{
		$this->db->select('e_id');
		$this->db->where('ebay_id', $itemid);
		$this->query = $this->db->get('ebay');
		if ($this->query->num_rows() > 0) 
			{
			$r = $this->query->row_array();	
			return ($r['e_id']);
			}
		else return 0;
	}
function _GetEbayTitleFromItemID($itemid = '')
	{
		$this->db->select('e_id, e_title');
		$this->db->where('ebay_id', $itemid);
		$this->query = $this->db->get('ebay');
		if ($this->query->num_rows() > 0) 
			{
			$r = $this->query->row_array();	
			return ($r);
			}
		else return array('e_id' => 0, 'e_title' => 'Not Found', 'gsid1' => 0);
	}


function GetShipping()
	{
	
						set_time_limit(60);
						ini_set('mysql.connect_timeout', 60);
						ini_set('max_execution_time', 60);  
						ini_set('default_socket_timeout', 60); 
						require_once($this->config->config['ebaypath'].'get-common/keys.php');
						require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
						
						
						$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
						$requestXmlBody .= '<GeteBayDetailsRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
						$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
						$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
						$requestXmlBody .= "<Version>$compatabilityLevel</Version>";
						$requestXmlBody .= '<DetailName>ShippingServiceDetails</DetailName>';
						$requestXmlBody .= '</GeteBayDetailsRequest>';
						$verb = 'GeteBayDetails';
						$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
						$responseXml = $session->sendHttpRequest($requestXmlBody);
						if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
							die('<P>Error sending request');						
						
						$this->load->helper('directory');
						$this->load->helper('file');
						if ($responseXml)
							{
								if (!write_file($this->config->config['ebaypath'].'/shipping.txt', $responseXml)) GoMail(array ('msg_title' => 'Unable to write Shippinh.txt @ '.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
								else {}//GoMail(array ('msg_title' => 'Shipping written @ '.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
							}
	
						
	
	}

	
	function CleanHistory()
	{
		$this->load->model('Mystart_model'); 
		$this->Mystart_model->DeleteOlderHistory(60);
		//$m = array ('msg_title' => 'Admin history older than 60 days has been purged @ '.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime());					
		//$this->db->insert('admin_history', $m); 
		
		//GoMail($m, '', $this->config->config['no_reply_email']);
	}
	
	
	
	/*
function _RealCount($array)
{

	if ($array != '') return count(explode(',', $array));
	else return 0;
}*/


function _logaction($location = '', $type = 'M', $datafrom = '', $datato = '', $eid = '', $itemid = '', $transid = '', $admin = '')
{

		foreach ($datato as $k => $v)
		{
			if ($v != $datafrom[$k])
			{
				if (isset($this->session->userdata['ownnames'])) $admin = $this->session->userdata['ownnames'];
				elseif ($admin == '') $admin = 'Cron';
				
					
					$hmsg = array ('msg_title' => 'Action Log for '.(int)$eid.' - Field: '.$k.' ('.$datafrom[$k].'/'.$datafrom[$k].') by '.$admin, 'msg_body' => 'Action Log for '.(int)$eid.' - Field: '.$k.' ('.$datafrom[$k].'/'.$datafrom[$k].') by '.$admin, 'msg_date' => CurrentTime());
					
					//GoMail($hmsg, $this->config->config['support_email'], $this->config->config['no_reply_email']);
				
				if ($k == 'Sold') $type = 'Q';
				$this->db->insert('ebay_actionlog', array('atype' => $type, 'e_id' => (int)$eid, 'ebay_id' => (int)$itemid, 'time' => CurrentTimeR(), 'datafrom' => $datafrom[$k], 'datato' => $v, 'field' => $k, 'admin' => $admin, 'trans_id' => (int)$transid, 'ctrl' => $location)); 			
			}
		}
}

function manualque()
{
    $this->load->model('Myseller_model');
    $this->Myseller_model->que_rev(12116, 'q', 0, 'manualque');
    
}



	function TransactionsFixBacklog($process = false)
	{
		//$this->DoRevise();
		//sleep(5);

		set_time_limit(600);
		ini_set('mysql.connect_timeout', 1200);
		ini_set('max_execution_time', 1200);
		ini_set('default_socket_timeout', 1200);
		require($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');

		$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
		$requestXmlBody .= '<GetSellerTransactionsRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
		$requestXmlBody .= '<DetailLevel>ItemReturnAttributes</DetailLevel>';
		$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
		$requestXmlBody .= '<IncludeContainingOrder>true</IncludeContainingOrder>';
		$requestXmlBody .= "<Version>$compatabilityLevel</Version><NumberOfDays>1</NumberOfDays>";
		//http://developer.ebay.com/DevZone/XML/docs/Reference/eBay/types/GranularityLevelCodeType.html
		//$dates = array('from' => date('Y-m-d H:i:s', strtotime("-2 Hours")), 'to' => date("Y-m-d H:i:s"));
		//<ModTimeFrom>'.$dates['from'].'</ModTimeFrom>
		//<ModTimeTo>'.$dates['to'].'</ModTimeTo>


		//<IncludeCodiceFiscale>'.TRUE.'</IncludeCodiceFiscale>
		//<IncludeContainingOrder>'.TRUE.'</IncludeContainingOrder>

		$requestXmlBody .= '
	
		 <IncludeFinalValueFee>'.TRUE.'</IncludeFinalValueFee>
			<NumberOfDays>1</NumberOfDays>	
		<Pagination>
		<EntriesPerPage>200</EntriesPerPage>
		<PageNumber>1</PageNumber>
		</Pagination>
		</GetSellerTransactionsRequest>';
		$verb = 'GetSellerTransactions';

		//Create a new eBay session with all details pulled in from included keys.php
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);

		//send the request and get response
		$responseXml = $session->sendHttpRequest($requestXmlBody);
		if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
			die('<P>Error sending request');

		printcool(simplexml_load_string($responseXml));
		//exit();
		$this->load->helper('directory');
		$this->load->helper('file');
		if ($responseXml)
		{
			if (!write_file($this->config->config['ebaypath'].'/trans.txt', $responseXml)) GoMail(array ('msg_title' => 'Unable to write Trans.txt @ '.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
			else {}//GoMail(array ('msg_title' => 'Transactions written @ '.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
		}

		//must change to days - if ($mkdt >= (mktime()-86401)) in processtrans.
		//second lpace where get local $this->db->where('mkdt >= ', mktime()-86401);
		$this->ProcessTransactions();
		//if ($process) Redirect('TransactionsComplete');
	}
}
