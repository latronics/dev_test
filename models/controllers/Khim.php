<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Khim extends Controller {
	
	function Khim()
	{
		parent::Controller();

	}
	
	function index()
	{
	
	}
	
	function UpdateTransactionShippingCost(){
		set_time_limit(60);
		ini_set('mysql.connect_timeout', 60);
		ini_set('max_execution_time', 60);  
		ini_set('default_socket_timeout', 60); 
		
		require_once($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
		
		$verb = 'GetSellingManagerSaleRecord';
		
		//Create a new eBay session with all details pulled in from included keys.php
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
		
		$this->db->select('rec, et_id, itemid, transid, asc');	
		$this->db->order_by("rec", "DESC");
		$this->db->limit(10);
		$q = $this->db->get('ebay_transactions');
		
		if ($q->num_rows() > 0) 
		{
			$echo = '<html><head></head><body> DB:'.count($q->result_array());

			$updatedIds = '';
			
			$this->load->helper('directory');
			$this->load->helper('file');
			
			$current_date = gmDate("Y-m-d\TH:i:s\Z"); 
			//$file = $this->config->config['ebaypath'].'items-' . $current_date . ".html";
			//$echo .= "<br/> File Name:" . $file . " <br/> ";
			$echo .= "Updating   ITEM ID   ET_ID   ";
			
			//$updatedIds .= "<br/> File Name:" . $file . " <br/> ";
					
			 foreach($q->result_array() as $t)
			 {
			 	
			 	$updatedIds .=  "$t[itemid] ";

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
				
				
				 
				if ($item && ((string)$item->ActualShippingCost != $t['asc']))
				{
					$echo .= "Updating   $t[itemid]   $t[et_id]   ";
					$echo .= (string)$item->ActualShippingCost . ' - '.$t['asc'].' <br/>';
					//$this->db->update('ebay_transactions', array('asc' => (string)$item->ActualShippingCost), array('et_id' => $t['et_id']));						
				}	
				unset($item);
			 }

			/*$this->db->insert('admin_history', array('msg_type' => 1, 'msg_title' => 'Actual Shipping Cost Updated', 
											'msg_body' => $updatedIds, 
											'msg_date' => CurrentTime(),
										  	'admin' => 'Auto',
										  	'sev' => '')); 
*/
			$echo .= "</body></html>";
			// write_file($file, $echo);					  			 
			 $this->mysmarty->assign("info", $echo);
			 $this->mysmarty->assign("ids", $updatedIds);
		}

		$this->mysmarty->view('myebay/khim.html');	
}
	
	

function Transactions()
	{
		set_time_limit(60);
		ini_set('mysql.connect_timeout', 60);
		ini_set('max_execution_time', 60);  
		ini_set('default_socket_timeout', 60); 
		require_once($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');

		$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
		$requestXmlBody .= '<GetSellerTransactionsRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
		$requestXmlBody .= '<DetailLevel>ItemReturnAttributes</DetailLevel>';
		$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
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
		<EntriesPerPage>100</EntriesPerPage>
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
				if (!write_file($this->config->config['ebaypath'].'/trans.txt', $responseXml)) GoMail(array ('msg_title' => 'Unable to write Trans.txt @ '.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime()), '365@1websolutions.net', 'norelpy@la-tronics.com');
				else {}//GoMail(array ('msg_title' => 'Transactions written @ '.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime()), '365@1websolutions.net', 'norelpy@la-tronics.com');
			}
}


function TestGetOrders()
	{	
	
		set_time_limit(60);
		ini_set('mysql.connect_timeout', 60);
		ini_set('max_execution_time', 60);  
		ini_set('default_socket_timeout', 60); 
		require_once($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');

		$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
		$requestXmlBody .= '<GetOrderTransactionsRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
		$requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';//ItemReturnAttributes
		$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
		//$requestXmlBody .= "<Version>$compatabilityLevel</Version>";		
		
		$requestXmlBody .= '
		<ItemTransactionIDArray>
		<ItemTransactionID><ItemID>171321150117</ItemID><TransactionID>1204232939007</TransactionID></ItemTransactionID>

  </ItemTransactionIDArray>
		</GetOrderTransactionsRequest>';	
		$verb = 'GetOrderTransactionsRequest';

		//Create a new eBay session with all details pulled in from included keys.php
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, 811, $this->config->config['ebaysiteid'], $verb);
				
		//send the request and get response
		$responseXml = $session->sendHttpRequest($requestXmlBody);
		if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
			die('<P>Error sending request');
		
		$this->load->helper('directory');
		$this->load->helper('file');
		if ($responseXml) 
		{
			$xml = simplexml_load_string($responseXml);
			printcool ($xml);
		}

	}

function TestProcessTransactions()
	{	
	
		set_time_limit(60);
		ini_set('mysql.connect_timeout', 60);
		ini_set('max_execution_time', 60);  
		ini_set('default_socket_timeout', 60); 
		require_once($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');

		$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
		$requestXmlBody .= '<GetSellerTransactionsRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
		$requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';//ItemReturnAttributes
		$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
		$requestXmlBody .= "<Version>$compatabilityLevel</Version><NumberOfDays>1</NumberOfDays>	";
		//http://developer.ebay.com/DevZone/XML/docs/Reference/eBay/types/GranularityLevelCodeType.html
		//$dates = array('from' => date('Y-m-d H:i:s', strtotime("-2 Hours")), 'to' => date("Y-m-d H:i:s"));
		//<ModTimeFrom>'.$dates['from'].'</ModTimeFrom>
 		//<ModTimeTo>'.$dates['to'].'</ModTimeTo>  
		
			
		//<IncludeCodiceFiscale>'.TRUE.'</IncludeCodiceFiscale>		
		//<IncludeContainingOrder>'.TRUE.'</IncludeContainingOrder> 
		
		$requestXmlBody .= '
	<OutputSelector>TransactionArray.Transaction.CreatedDate,TransactionArray.Transaction.SellingManagerSalesRecordNumber,TransactionArray.Transaction.AmountPaid,TransactionArray.Transaction.FinalValueFee,TransactionArray.Transaction.ShippingDetails,TransactionArray.Transaction.PaidTime,TransactionArray.Transaction.ItemID,TransactionArray.Transaction.Buyer.UserID,TransactionArray.Transaction.Buyer.Email,TransactionArray.Transaction.Item.Quantity,TransactionArray.Transaction.QuantityPurchased,TransactionArray.Transaction.ActualShippingCost,TransactionArray.Transaction.ShippingServiceSelected,TransactionArray.Transaction.SellingStatus.QuantitySold</OutputSelector> 
		 <IncludeFinalValueFee>'.TRUE.'</IncludeFinalValueFee>
			<NumberOfDays>1</NumberOfDays>	
		<Pagination>
		<EntriesPerPage>120</EntriesPerPage>
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
			$xml = simplexml_load_string($responseXml);
			printcool ($xml);
		}

	}
	


function ProcessTransactions()
	{
		$this->printstr = '';
		$this->load->helper('directory');
		$this->load->helper('file');
		$list = read_file($this->config->config['ebaypath'].'/trans.txt');
		$xml = simplexml_load_string($list);
		$list = $xml->TransactionArray->Transaction;
		$insert = false;
		if ($list) foreach ($list as $l)
		{
			$tmpdate = explode ('|', CleanBadDate($l->CreatedDate));
			$date = explode('-', trim($tmpdate[0]));
			$time = explode(':', trim($tmpdate[1]));
			$mkdt = mktime((int)$time[0], (int)$time[1], (int)$time[2], (int)$date[1], (int)$date[2], (int)$date[0]);
			if ($mkdt >= (mktime()-86401))
			{
			$insert[(int)$l->ShippingDetails->SellingManagerSalesRecordNumber]['datetime'] = CleanBadDate($l->CreatedDate);
			$insert[(int)$l->ShippingDetails->SellingManagerSalesRecordNumber]['mkdt'] = $mkdt;
			$insert[(int)$l->ShippingDetails->SellingManagerSalesRecordNumber]['rec'] = (int)$l->ShippingDetails->SellingManagerSalesRecordNumber;
			$insert[(int)$l->ShippingDetails->SellingManagerSalesRecordNumber]['paid'] = (string)$l->AmountPaid;
			$insert[(int)$l->ShippingDetails->SellingManagerSalesRecordNumber]['fee'] = (string)$l->FinalValueFee;
			$insert[(int)$l->ShippingDetails->SellingManagerSalesRecordNumber]['shipping'] = (string)$l->ShippingDetails->ShippingServiceUsed;
			$insert[(int)$l->ShippingDetails->SellingManagerSalesRecordNumber]['tracking'] = (string)$l->ShippingDetails->ShipmentTrackingNumber;
			$insert[(int)$l->ShippingDetails->SellingManagerSalesRecordNumber]['paidtime'] = CleanBadDate((string)$l->PaidTime);
			$insert[(int)$l->ShippingDetails->SellingManagerSalesRecordNumber]['itemid'] = (string)$l->Item->ItemID;
			$insert[(int)$l->ShippingDetails->SellingManagerSalesRecordNumber]['buyerid'] = (string)$l->Buyer->UserID;
			$insert[(int)$l->ShippingDetails->SellingManagerSalesRecordNumber]['buyeremail'] = (string)$l->Buyer->Email;
			$insert[(int)$l->ShippingDetails->SellingManagerSalesRecordNumber]['qtyof'] = (int)$l->Item->Quantity;
			$insert[(int)$l->ShippingDetails->SellingManagerSalesRecordNumber]['qty'] = (int)$l->QuantityPurchased;	
			$insert[(int)$l->ShippingDetails->SellingManagerSalesRecordNumber]['asc'] = (string)$l->ActualShippingCost;	
			$insert[(int)$l->ShippingDetails->SellingManagerSalesRecordNumber]['ssc'] = (string)$l->ShippingServiceSelected->ShippingServiceCost;
			$insert[(int)$l->ShippingDetails->SellingManagerSalesRecordNumber]['ebsold'] = (string)$l->Item->SellingStatus->QuantitySold;	
			$insert[(int)$l->ShippingDetails->SellingManagerSalesRecordNumber]['transid'] = (string)$l->TransactionID;	
			}
				
		}
		
		if (is_array($insert)) ksort($insert);
		$echo = 'EB:'.count($insert);
		$unsetted = '<br>Unset: ';
		$unsetcount = 0;
		$this->db->select('rec, paid, fee, shipping, tracking, paidtime, qtyof, asc, ssc, updated, ebsold');	
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
					$this->printstr .= printcool ('<table>				
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
				</table><br>', true);
					
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
							
					if ($insert[$t['rec']]['paid'] != $t['paid'])
					{
						$updstr .= ' Paid: '.IfFillEmpty($insert[$t['rec']]['paid'],'b').' / '.IfFillEmpty($t['paid'],'r').' |';
						$updatedata['paid'] = $insert[$t['rec']]['paid'];
						$paychange = TRUE;
					}
										
					if ($insert[$t['rec']]['fee'] != $t['fee'])
					{
						$updstr .= ' Fee: '.IfFillEmpty($insert[$t['rec']]['fee'],'b').' / '.IfFillEmpty($t['fee'],'r').' |';
						$updatedata['fee'] = $insert[$t['rec']]['fee'];
					}
										
					if ($insert[$t['rec']]['shipping'] != $t['shipping']) 
					{
						$updstr .= ' Shipping: '.IfFillEmpty($insert[$t['rec']]['shipping'],'b').' / '.IfFillEmpty($t['shipping'],'r').' |';
						$updatedata['shipping'] = $insert[$t['rec']]['shipping'];
					}
										
					if ($insert[$t['rec']]['tracking'] != $t['tracking'])
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
					
					if ($insert[$t['rec']]['asc'] != $t['asc']) 
					{
						$updstr .= ' ActShipCost: '.IfFillEmpty($insert[$t['rec']]['asc'],'b').' / '.IfFillEmpty($t['asc'],'r').' |'; 
						$updatedata['asc'] = $insert[$t['rec']]['asc'];
					}
					
					if ($insert[$t['rec']]['ssc'] != $t['ssc']) 
					{
						$updstr .= ' ShipCost: '.IfFillEmpty($insert[$t['rec']]['ssc'],'b').' / '.IfFillEmpty($t['ssc'],'r').' |'; 
						$updatedata['ssc'] = $insert[$t['rec']]['ssc'];
					}
					if ($insert[$t['rec']]['sold'] != $t['sold']) 
					{
						$updstr .= ' Sold: '.IfFillEmpty($insert[$t['rec']]['ebsold'],'b').' / '.IfFillEmpty($t['ebsold'],'r').' |'; 
						$updatedata['ebsold'] = $insert[$t['rec']]['ebsold'];
					}
					
					$updstr .= '<br>';
					
					$updatedata['updated'] = $t['updated'].$updstr;
					$this->printstr .= printcool ($updstr, true);
					$this->printstr .= printcool ($updatedata,true);
					$this->db->update('ebay_transactions', $updatedata, array('rec' => (int)$t['rec']));
					$this->db->insert('admin_history', array('msg_type' => 1, 'msg_title' => 'Transaction Updated: '.$updstr, 'msg_body' => '', 'msg_date' => CurrentTime(),
												  'e_id' => $this->_GetEbayFromItemID($insert[$t['rec']]['itemid']),
												  'itemid' => $insert[$t['rec']]['itemid'],
												  'trec' => $t['rec'],
												  'admin' => 'Auto',
												  'sev' => '')); 
					if ($paychange)
					{						
												
						$bcnarray = $this->_DoBCNS($insert[$t['rec']]);
/*						A - BCN

						G Ebay Title
						J Date Sold
						K Price Sold
						L Shipping
						M Where (ebay)
						U ItemID link to ebay listing*/
						//echo 'UPDATE';
						$e = $this->_GetEbayTitleFromItemID($insert[$t['rec']]['itemid']);
						$this->_InsertGS($bcnarray, $e['e_id'], $insert[$t['rec']]['itemid'], $t['rec'], $e['e_title'], $insert[$t['rec']]['paid'], $insert[$t['rec']]['paidtime'], $e['gsid1'], $e['gsid2'], $e['gsid3'], $e['gsid4'], $e['gsid5']);
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
				$this->db->insert('ebay_transactions', $i); 
				
				if ($i['paid'] > 0 && $i['paidtime'] != '') $pay = ' <span style="color:#FF9900;">(Paid)</span>';
				else $pay = ' <span style="color:red;">(Unpaid)</span>';
				
					$this->db->insert('admin_history', array('msg_type' => 1, 'msg_title' => '<span style="color:blue;">New eBay Transaction</span>'.$pay, 'msg_body' => '', 'msg_date' => CurrentTime(),
												  'e_id' => $this->_GetEbayFromItemID($i['itemid']),
												  'itemid' => $i['itemid'],
												  'trec' => $i['rec'],
												  'admin' => 'Auto',
												  'sev' => '')); 
					
					if ($i['paid'] > 0 && $i['paidtime'] != '')
					{
						$bcnarray = $this->_DoBCNS($i);
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
						$this->_InsertGS($bcnarray, $e['e_id'], $i['itemid'], $i['rec'], $e['e_title'], $i['paid'], $i['paidtime'], $e['gsid1'], $e['gsid2'], $e['gsid3'], $e['gsid4'], $e['gsid5']);
					}			
			}
		
		//GoMail(array ('msg_title' => $echo.' UN:'.$unsetcount.' @'.CurrentTime(), 'msg_body' => $this->printstr.$unsetted.' ('.$unsetcount.')', 'msg_date' => CurrentTime()), '365@1websolutions.net', 'norelpy@la-tronics.com');
}
function DummyInsertGS()
{
	$this->_InsertGS(array('DEL1214'), '111', '1111', '11111', '111111', '1111111', '11111111');
}
function _InsertGS($bcnarray = '', $eid = '', $itemid = '', $trans = '', $title = '', $paid = '', $paidtime = '', $gsid1 = '', $gsid2 = '', $gsid3 = '' , $gsid4 = '', $gsid5 = '')
{	
	
	if (is_array($bcnarray) && count($bcnarray) > 0)
	{
		foreach ($bcnarray as $b)
		{
			/*$e['e_title'], $i['paid'], $i['paidtime']*/
			
			$demobcns = array('AP1201','AP1202','AP1203','AP1204','AP1205','AP1206','AS1207','AS1208','AS1209','AS1210','AS1211','AS1212','AS1213','DEL1214','DEL1215','DEL1216','DEL1217','GTW1218','SAM1219','SAM1220','SAM1221','SAM1222','SAM1223','SAM1224','SAM1225','SAM1226','SAM1227','SAM1228','SAM1229','SAM1230','SAM1231','SAM1232','SAM1233','SAM1234','SAM1235','SAM1236','SAM1237','SAM1238','SAM1239','SAM1240','SAM1241','SAM1242','SAM1243','SAM1244','SAM1245','SAM1246','SAM1247','SAM1248','SAM1249','SAM1250','SAM1251','SAM1252','SAM1253','SAM1254','SAM1255','SAM1256','SAM1257','SAM1258','SAM1259','SAM1260','SAM1261','SAM1262','SAM1263','SAM1264','SAM1265','SAM1266','SAM1267','SAM1268','SAM1269','SAM1270','SAM1271','SAM1272','SAM1273','SAM1274','SAM1275','SAM1276','SAM1277','SAM1278','SAM1279','SAM1280','SAM1281','SAM1282','SAM1283','SAM1284','SAM1285','SAM1286');		
		
			$data = serialize(array('7' => $title, '10' => $paidtime, '11' => $paid, '13' => 'Ebay', '21' => 'http://www.ebay.com/itm/'.$itemid));
			
			//$this->db->insert('gsdata', array('bcn' => $b, 'trans' => $trans, 'eid' => $eid, 'itemid' => $itemid, 'tvalue' => $data));
			
			$this->db->insert('gsdata', array('bcn' => $demobcns[array_rand($demobcns, 1)], 'trans' => $trans, 'eid' => $eid, 'itemid' => $itemid, 'tvalue' => $data, 'gsid1' => $gsid1, 'gsid2' => $gsid2, 'gsid3' => $gsid3, 'gsid4' => $gsid4, 'gsid5' => $gsid5));
			$iid = $this->db->insert_id();
			//TURNED OFF FOR NOW
			//GoMail(array ('msg_title' => 'Google Spreadsheet Que Insert '.$iid, 'msg_body' => $data, 'msg_date' => CurrentTime()), '365@1websolutions.net', 'norelpy@la-tronics.com');
			//
			//$this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' =>'Google Spreadsheet Que Insert '.$iid, 'msg_body' => $data, 'msg_date' => CurrentTime(), 'e_id' => $eid, 'itemid' => $itemid, 'trec' => $trans, 'admin' => 'Auto', 'sev' => 0));
			
		}
	
	}
	else
	{
		//TURNED OFF FOR NOW
		//$dt = array ('msg_title' => 'Insert GS Empty BCN Array @ '.CurrentTime(), 'msg_body' => 'EID: '.$eid.'<br>ITEMID: '.$itemid.'<br>Transaction: '.$trans.'<br><br>'.serialize($bcnarray), 'msg_date' => CurrentTime());
		
		//GoMail($dt, '365@1websolutions.net', 'norelpy@la-tronics.com');
		
		//$this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => $dt['msg_title'], 'msg_body' => $dt['msg_body'], 'msg_date' => CurrentTime(), 'e_id' => $eid, 'itemid' => $itemid, 'trec' => $trans, 'admin' => 'Auto', 'sev' => 1));
	}
}
function ProcessGS()
{
	$this->db->where('proc', 0);
	$eb = $this->db->get('gsdata');
	if ($eb->num_rows() > 0) 
	{
		$res = $eb->result_array();
	}
	else exit();

	set_time_limit(60);
	ini_set('mysql.connect_timeout', 60);
	ini_set('max_execution_time', 60);  
	ini_set('default_socket_timeout', 60); 
		
	require_once($this->config->config['pathtopublic'].'/gsssettings.php');
	$spreadsheet_key = '0ApHMD7nkSM4YdGtMU3NwUk9vZ1hyM3VxUG1BdHRteXc';
	// Spreadsheet key
	
	/*$bcn = 'STG1TB-1107';
	$eid = '1005';
	$itemid = '926033';
	$trans = '123456';*/
	
	// Set your own column name & value to be searched. Below one is an example
	// Column name is 'A', value to be searched is '100001466750079'
	
	// Set your own new values for specific columns. Below one is an example
	// The below example means that we have to change the values of 2 columns in the row that gets found
	// Column 1 : name of the column is 'D', new value of the column must be 'abhas1abhas@hotmail.com'	
	// Column 2 : name of the column is 'F', new value of the column must be '999999'	
	
	$msg = '';
	try {
		// Create an object
		$this->load->library('Googlesheets');
		//$this->googlesheets->testme();
		// Create an access token using the Refresh Token saved in settings.php
		$access_token = $this->googlesheets->GetRefreshedAccessToken(GOOGLE_CLIENT_ID, GOOGLE_REFRESH_TOKEN, GOOGLE_CLIENT_SECRET);
		// Get Worksheet Information 
		$spreadsheet_info = $this->googlesheets->GetWorksheetInformation($spreadsheet_key, $access_token);
		// Get the column cells
		$cells = $this->googlesheets->GetCells($spreadsheet_info['worksheet_cells_feed_url'], 1, $spreadsheet_info['row_count'], ord(strtolower('A'))-96, ord(strtolower('A'))-96, $access_token);	
		
		foreach ($res as $r)
		{
		
			
			$this->db->update('gsdata', array ('time' => CurrentTimer(), 'proc' => 1), array('gsid' => $r['gsid']));
			
			$col_search = array('name' => 'A', 'value' => $r['bcn']);			
			
			$newvals = unserialize($r['tvalue']);			
			$cols_new = array(array('name' => 'G','value' => $newvals[7]), array('name' => 'J','value' => $newvals[10]), array('name' => 'K','value' => $newvals[11]), array('name' => 'M','value' => $newvals[13]), array('name' => 'U','value' => $newvals[21]));
	
		$row_no = 0;
		for($i=0;$i<sizeof($cells);$i++) 
			{
				if($cells[$i]['value'] == $col_search['value']) 
				{
					$row_no = ($i+1);
					break;
				}
			}
		if($row_no == 0)
		{
			//TURNED OFF
			//$dt1 = array ('msg_title' => 'Cannot match BCN ('.$col_search['value'].') in Google Spreadsheet @ '.CurrentTime(), 'msg_body' => 'GS Record: '.$r['gsid'], 'msg_date' => CurrentTime());
		
			//GoMail($dt1, '365@1websolutions.net', 'norelpy@la-tronics.com');
			
			//$this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => $dt1['msg_title'], 'msg_body' => $dt1['msg_body'], 'msg_date' => CurrentTime(), 'e_id' => $r['eid'], 'itemid' => $r['itemid'], 'trec' => $r['trans'], 'admin' => 'Auto', 'sev' => 1));
		}
		else
		{
		// Get the cells of the row that was found
		$cellsin = $this->googlesheets->GetCells($spreadsheet_info['worksheet_cells_feed_url'], $row_no, $row_no, 1, $spreadsheet_info['col_count'], $access_token);
		$cols_old = array();
		$to_be_updated = array();
		for($i=0;$i<sizeof($cols_new);$i++) {
			$cols_old[] = $cellsin[ord(strtolower($cols_new[$i]['name']))-97];
			$cellsin[ord(strtolower($cols_new[$i]['name']))-97]['value'] = $cols_new[$i]['value'];
			$to_be_updated[] = $cellsin[ord(strtolower($cols_new[$i]['name']))-97];
		}
		// Update Cells
		$this->googlesheets->UpdateCells($spreadsheet_info['worksheet_cells_feed_url'], $to_be_updated, $access_token);
		// 'value' key of each element of $cols_old stores the old values of the cells that were updated
					
		$colmap = array(1 => 'A',2 => 'B',3 => 'C',4 => 'D',5 => 'E',6 => 'F',7 => 'G',8 => 'H',9 => 'I',10 => 'J',11 => 'K',12 => 'L',13 => 'M',14 => 'N',15 => 'O',16 => 'P',17 => 'Q',18 => 'R',19 => 'S',	20 => 'T',21 => 'U',22 => 'V',23 => 'W',24 => 'X',25 => 'Y',26 => 'Z');		
		
		foreach ($cols_old as $c => $cv) { unset($cols_old[$c]['id']); unset($cols_old[$c]['row']); unset($cols_old[$c]['edit_url']); } 
		
		foreach ($to_be_updated as $c => $cv) { unset($to_be_updated[$c]['id']); unset($to_be_updated[$c]['row']); unset($to_be_updated[$c]['edit_url']); }  
	
		$this->db->update('gsdata', array('sheet' => $spreadsheet_key, 'row' => $row_no, 'fvalue' => serialize($cols_old)), array('gsid' => $r['gsid']));		
				
		
		/// CONVERT TO ACTIONLOG
		//$this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => 'Google Spreadsheet Update Row '.$row_no, 'msg_body' => '<strong>From:</strong> '.serialize($cols_old).' | <strong>To:</strong>'. serialize($to_be_updated), 'msg_date' => CurrentTime(), 'e_id' => $r['eid'], 'itemid' => $r['itemid'], 'trec' => $r['trans'], 'admin' => 'Auto', 'sev' => 0));
		//
		//GoMail(array ('msg_title' => 'Google Spreadsheet Update Row '.$row_no, 'msg_body' => 'From: '.serialize($cols_old).' | To:'. serialize($to_be_updated), 'msg_date' => CurrentTime()), '365@1websolutions.net', 'norelpy@la-tronics.com');
		
		}
		
	}
	}
	catch(Exception $e) {
		$msg .= $e->getMessage();
	}
	
echo $msg;
	
	
}
function _DoBCNS($i)
{
					$this->db->select('e_id, e_title, ebay_id, quantity, ebayquantity, e_part, e_qpart, ebsold');
					$this->db->where('ebay_id', $i['itemid']);
					$eb = $this->db->get('ebay');
					if ($eb->num_rows() > 0) 
						{
							$res = $eb->row_array();
							$qty = $res['quantity'];
							$resoldquantity = $qty;
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
								$moved = implode(',', $moved);
								if ((int)$unavailble > 0) $this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => '<span style="color:blue;">BCN Auto Update - </span><span style="color:red;">LISTING DOES NOT HAVE ENOUGH BCN ITEMS - "'.$unavailble.'" Unavailable for total required '.$i['qty'].'</span>', 'msg_body' => '', 'msg_date' => CurrentTime(),
												  'e_id' => $res['e_id'],
												  'itemid' => $res['ebay_id'],
												  'trec' => $i['rec'],
												  'admin' => 'Auto',
												  'sev' => 1));
								
								$this->db->update('ebay_transactions', array('sn' => $moved), array('rec' => (int)$i['rec']));
								
								/*$this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => '<span style="color:blue;">Transaction BCN Auto Updated</span> to <span style="color:#FF9900;">"'.$moved.'"</span>', 'msg_body' => '', 'msg_date' => CurrentTime(),
												  'e_id' => $res['e_id'],
												  'itemid' => $res['ebay_id'],
												  'trec' => $i['rec'],
												  'admin' => 'Auto',
												  'sev' => ''));*/
												  
								$this->printstr .= printcool ($moved, true);
								$this->printstr .= printcool ($bcns,true);
								$bcns = implode(',', $bcns);
								$this->printstr .= printcool ($bcns,true);
								
								$this->printstr .= printcool ('BCN MOVE END',true);
								$this->db->update('ebay', array('e_part' => $bcns, 'e_qpart' => $this->_RealCount($bcns)), array('e_id' => (int)$res['e_id']));
								
								$this->_logaction('Transactions', 'B', array('BCN' => $bcnsold), array('BCN' => $bcns), (int)$res['e_id'], $res['ebay_id'], $i['rec']);
								$this->_logaction('Transactions', 'B', array('BCN Count' => $this->_RealCount($bcnsold)), array('BCN Count' => $this->_RealCount($bcns)), (int)$res['e_id'], $res['ebay_id'], $i['rec']);
								$this->_logaction('Transactions', 'B', array('Transaction BCN' => ''), array('Transaction BCN' => $moved), (int)$res['e_id'], $res['ebay_id'], $i['rec']);
								
								
								
								/*$this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => '<span style="color:blue;">Listing '.$res['e_id'].' BCN Updated</span> to value <span style="color:#FF9900;">"'.$bcns.'" ('.$this->_RealCount($bcns).') pcs</span> after piece transfer to Transaction.', 'msg_body' => '', 'msg_date' => CurrentTime(),
												  'e_id' => $res['e_id'],
												  'itemid' => $res['ebay_id'],
												  'trec' => $i['rec'],
												  'admin' => 'Auto',
												  'sev' => ''));*/
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
							/*$this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => 'eBay Listing Quantity changed from <span style="color:#FF9900;">'.$qty.'</span> to <span style="color:'.$colorstep.';">'.$res['quantity'].'</span> for transaction.</span>', 'msg_body' => '', 'msg_date' => CurrentTime(),
												  'e_id' => $res['e_id'],
												  'itemid' => $res['ebay_id'],
												  'trec' => $i['rec'],
												  'admin' => 'Auto',
												  'sev' => '')); */

							$this->db->update('ebay', array('quantity' => $res['quantity'], 'ebayquantity' => $i['qtyof'], 'ebsold' => $i['ebsold']), array('e_id' => (int)$res['e_id']));
							$this->_logaction('Transactions', 'Q', array('Quantity' => $resoldquantity), array('Quantity' => $res['quantity']), (int)$res['e_id'], $res['ebay_id'], $i['rec']);
							$this->_logaction('Transactions', 'Q', array('Local eBay Quantity' => $res['ebayquantity']), array('Local eBay Quantity' => $i['qtyof']), (int)$res['e_id'], $res['ebay_id'], $i['rec']);
							
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
						
						if (isset($returnmove)) return $returnmove;
}
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
		<UserID>la.tronics</UserID></GetStoreRequest>';

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
					GoMail(array ('msg_title' => 'Unable to write Cats.txt @ '.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime()), '365@1websolutions.net', 'norelpy@la-tronics.com');
					echo 'Unable to update Cats.';
				}
				else
				{
					GoMail(array('msg_title' => 'Cats written @ '.CurrentTime(), 'msg_body' => $responseXml, 'msg_date' => CurrentTime()), '365@1websolutions.net', 'norelpy@la-tronics.com'); 
					echo 'Cats updated. Refresh the admin view for the product now and close this window.';
				}
			}
		
}

function GetSellerEvents()
{

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
  <ModTimeTo>".date('Y-m-d H:i:s')."</ModTimeTo><IncludeWatchCount>FALSE</IncludeWatchCount><OutputSelector>ItemArray.Item.ItemID,ItemArray.Item.SellingStatus.CurrentPrice,ItemArray.Item.Title,ItemArray.Item.Quantity</OutputSelector></GetSellerEventsRequest>";
						$verb = 'GetSellerEvents';
						$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
						$responseXml = $session->sendHttpRequest($requestXmlBody);
						if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
							die('<P>Error sending request');
						
						$xml = simplexml_load_string($responseXml);

						//printcool ($xml);
						$stats['xml'] = count($xml->ItemArray->Item);
						$start = 1;
						$this->db->select('e_id, buyItNowPrice, e_title, ebayquantity, ebay_id');
						foreach ($xml->ItemArray->Item as $i)
						{
							if ($start == 1) $this->db->where('ebay_id', (int)$i->ItemID);
							else $this->db->or_where('ebay_id', (int)$i->ItemID);
							
							$data[(int)$i->ItemID] = array('ebay_id' => (int)$i->ItemID, 'buyItNowPrice' => (string)trim($i->SellingStatus->CurrentPrice), 'e_title' => (string)trim($i->Title), 'ebayquantity' => (string)trim($i->Quantity));
							$start++;
						}
						
						$mod = array();
						$dup = array();
						$query = $this->db->get('ebay');
						if ($query->num_rows() > 0) 
									{
																//printcool ($query->result_array());
									foreach ($query->result_array() as $q)
										{
											
											if ((string)trim($q['buyItNowPrice']) != $data[$q['ebay_id']]['buyItNowPrice']) $mod[$q['e_id']]['buyItNowPrice'] = $data[$q['ebay_id']]['buyItNowPrice'];
											if ((string)trim($q['e_title']) != $data[$q['ebay_id']]['e_title']) $mod[$q['e_id']]['e_title'] = $data[$q['ebay_id']]['e_title'];
											if ((string)trim($q['ebayquantity']) != $data[$q['ebay_id']]['ebayquantity']) $mod[$q['e_id']]['ebayquantity'] = $data[$q['ebay_id']]['ebayquantity'];
											if (isset($mod[$q['e_id']])) { $mod[$q['e_id']]['ebay_id'] = $data[$q['ebay_id']]['ebay_id']; $local[$q['e_id']] = $q; }
											//unset($data[$q['ebay_id']]);
											/*if (isset($mod[$q['e_id']]))
											{
											printcool($q);
											printcool($data[$q['ebay_id']]);
											printcool ($mod[$q['e_id']]);
											printcool ('-----<br><Br><Br><br><br>');
											}*/
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
									
									$stats['run'] = CurrentTimeR();
									
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
function GetSellerEventsTEMP()
{

echo 'SellerEventsCron';

exit();

require_once($this->config->config['ebaypath'].'get-common/keys.php');
						require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');


						$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
						$requestXmlBody .= '<GetSellerEventsRequest xmlns="urn:ebay:apis:eBLBaseComponents">
';
						$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
						$requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';
						$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
						$requestXmlBody .= "<Version>$compatabilityLevel</Version>";						
						$requestXmlBody .= "<ModTimeFrom>".date('Y-m-d H:i:s', strtotime("-1 days"))."</ModTimeFrom>
  <ModTimeTo>".date('Y-m-d H:i:s')."</ModTimeTo></GetSellerEventsRequest>";
						$verb = 'GetSellerEvents';
						$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
						$responseXml = $session->sendHttpRequest($requestXmlBody);
						if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
							die('<P>Error sending request');
						
						$xml = simplexml_load_string($responseXml);

//printcool ($xml);
$count = 1;
foreach ($xml->ItemArray->Item as $i)
			{
			//$this->db->insert('ebay_cron', array('e_id' => 0, 'data' => '', 'time' => CurrentTime()));
			//printcool ($i);
				if (((string)$i->SellingStatus->ListingStatus == 'Active') && ($count < 3))
				{ 
					$priceval = preg_replace('/[^0-9\.]/', '', (float)$i->SellingStatus->CurrentPrice);
					$updt = array('from' => '', 'to' => $priceval, 'date' => CurrentTime());
					/*if (strlen($items[(int)$i->ItemID]['mods']) > 15) $items[(int)$i->ItemID]['mods'] = unserialize($items[(int)$i->ItemID]['mods']);
					else $items[(int)$i->ItemID]['mods'] = array();
					 
					$items[(int)$i->ItemID]['mods'][] = $updt;
					
					$this->db->update('ebay', array('buyItNowPrice' => $priceval, 'startPrice' => $priceval, 'mods' => serialize($items[(int)$i->ItemID]['mods'])), array('e_id' => $items[(int)$i->ItemID]['e_id']));
					*/
					//$this->db->insert('ebay_cron', array('e_id' => (int)$i->ItemID, 'data' => serialize($updt), 'time' => CurrentTime()));
					
				$count++;		
				}
			//$this->db->insert('ebay_cron', array('e_id' => 0, 'data' => '', 'time' => CurrentTime()));	
				
			}

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
		$this->db->select('e_id, e_title, gsid1, gsid2, gsid3, gsid4, gsid5');
		$this->db->where('ebay_id', $itemid);
		$this->query = $this->db->get('ebay');
		if ($this->query->num_rows() > 0) 
			{
			$r = $this->query->row_array();	
			return ($r);
			}
		else return array('e_id' => 0, 'e_title' => 'Not Found', 'gsid1' => 0, 'gsid2' => 0, 'gsid3' => 0, 'gsid4' => 0, 'gsid5' => 0);
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
								if (!write_file($this->config->config['ebaypath'].'/shipping.txt', $responseXml)) GoMail(array ('msg_title' => 'Unable to write Shippinh.txt @ '.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime()), '365@1websolutions.net', 'norelpy@la-tronics.com');
								else {}//GoMail(array ('msg_title' => 'Shipping written @ '.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime()), '365@1websolutions.net', 'norelpy@la-tronics.com');
							}
	
						
	
	}

	
	function CleanHistory()
	{
		/*$this->load->model('Mystart_model'); 
		$this->Mystart_model->DeleteOlderHistory(60);
		$m = array ('msg_title' => 'Admin history older than 60 days has been purged @ '.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime());					
		$this->db->insert('admin_history', $m); 
		*/
		//GoMail($m, '', 'norelpy@la-tronics.com');
	}
	
	
	
	
	
	
	/*function TransactionsOld()
	{
		set_time_limit(60);
		ini_set('mysql.connect_timeout', 300);
		ini_set('default_socket_timeout', 300); 
		require_once($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');

		$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
		$requestXmlBody .= '<GetSellerTransactionsRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
		$requestXmlBody .= '<DetailLevel>ItemReturnAttributes</DetailLevel>';
		$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
		$requestXmlBody .= "<Version>$compatabilityLevel</Version>";
		//http://developer.ebay.com/DevZone/XML/docs/Reference/eBay/types/GranularityLevelCodeType.html
		$dates = array('from' => date('Y-m-d H:i:s', strtotime("-2 Hours")), 'to' => date("Y-m-d H:i:s"));
		//<ModTimeFrom>'.$dates['from'].'</ModTimeFrom>
 		//<ModTimeTo>'.$dates['to'].'</ModTimeTo>  
		
		//<NumberOfDays>1</NumberOfDays>		
		//<IncludeCodiceFiscale>'.TRUE.'</IncludeCodiceFiscale>		
		//<IncludeContainingOrder>'.TRUE.'</IncludeContainingOrder> 
		
		$requestXmlBody .= '
	
		 <IncludeFinalValueFee>'.TRUE.'</IncludeFinalValueFee>
			<NumberOfDays>1</NumberOfDays>	
		<Pagination>
		<EntriesPerPage>40</EntriesPerPage>
		</Pagination>
		</GetSellerTransactionsRequest>';	
		$verb = 'GetSellerTransactions';

		//Create a new eBay session with all details pulled in from included keys.php
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
				
		//send the request and get response
		$responseXml = $session->sendHttpRequest($requestXmlBody);
		if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
			die('<P>Error sending request');
		
		$xml = simplexml_load_string($responseXml);
		$list = $xml->TransactionArray->Transaction;
	
		$insert = false;
		if ($list) foreach ($list as $l)
		{
			$tmpdate = explode ('|', CleanBadDate($l->CreatedDate));
			$date = explode('-', trim($tmpdate[0]));
			$time = explode(':', trim($tmpdate[1]));
			$mkdt = mktime((int)$time[0], (int)$time[1], (int)$time[2], (int)$date[1], (int)$date[2], (int)$date[0]);
			if ($mkdt >= (mktime()-86401))
			{
			$insert[(int)$l->ShippingDetails->SellingManagerSalesRecordNumber]['datetime'] = CleanBadDate($l->CreatedDate);
			$insert[(int)$l->ShippingDetails->SellingManagerSalesRecordNumber]['mkdt'] = $mkdt;
			$insert[(int)$l->ShippingDetails->SellingManagerSalesRecordNumber]['rec'] = (int)$l->ShippingDetails->SellingManagerSalesRecordNumber;
			$insert[(int)$l->ShippingDetails->SellingManagerSalesRecordNumber]['paid'] = (string)$l->AmountPaid;
			$insert[(int)$l->ShippingDetails->SellingManagerSalesRecordNumber]['fee'] = (string)$l->FinalValueFee;
			$insert[(int)$l->ShippingDetails->SellingManagerSalesRecordNumber]['shipping'] = (string)$l->ShippingDetails->ShippingServiceUsed;
			$insert[(int)$l->ShippingDetails->SellingManagerSalesRecordNumber]['tracking'] = (string)$l->ShippingDetails->ShipmentTrackingNumber;
			$insert[(int)$l->ShippingDetails->SellingManagerSalesRecordNumber]['paidtime'] = CleanBadDate((string)$l->PaidTime);
			$insert[(int)$l->ShippingDetails->SellingManagerSalesRecordNumber]['itemid'] = (string)$l->Item->ItemID;
			$insert[(int)$l->ShippingDetails->SellingManagerSalesRecordNumber]['buyerid'] = (string)$l->Buyer->UserID;
			$insert[(int)$l->ShippingDetails->SellingManagerSalesRecordNumber]['buyeremail'] = (string)$l->Buyer->Email;
			$insert[(int)$l->ShippingDetails->SellingManagerSalesRecordNumber]['qtyof'] = (int)$l->Item->Quantity;
			$insert[(int)$l->ShippingDetails->SellingManagerSalesRecordNumber]['qty'] = (int)$l->QuantityPurchased;	
			}
					
		}
		//printcool ($insert);
		//break;
		ksort($insert);
		$echo = 'EB:'.count($insert);
		//printcool ($insert);
		$this->db->select('rec');	
		$this->db->where('mkdt >= ', mktime()-86401);
		$this->db->order_by("rec", "DESC");
		$q = $this->db->get('ebay_transactions');
		if ($q->num_rows() > 0) 
		{
		$echo .= ' DB:'.count($q->result_array());
			 foreach($q->result_array() as $t)
			 {
			 	if (isset($insert[$t['rec']])) unset($insert[$t['rec']]);
			 }
			 
		}
		$echo .= ' FIN:'.count($insert);
		//printcool ($insert);
		if ($insert) foreach($insert as $i) 
			{
				$this->db->insert('ebay_transactions', $i); 
				$m = array ('msg_title' => 'New eBay Transaction (Record '.$i['rec'].') for Item ID '.$i['itemid'].' @ '.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime());					
				$this->db->insert('admin_history', $m); 
				//GoMail($m, '', 'norelpy@la-tronics.com');
			}
			
		GoMail(array ('msg_title' => $echo.' @'.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime()), '365@1websolutions.net', 'norelpy@la-tronics.com');
	}
	
	
	*/
	
	
	
	
	
	
	
	
	
	/*
	function fix()
	{
					$this->db->select('e_id, e_sef');
					$this->db->limit(100, 6300);
					$eb = $this->db->get('ebay');
					if ($eb->num_rows() > 0) 
						{
							$res = $eb->result_array();
							foreach ($res as $r)
							{ 
								//printcool ($r);
								$this->db->update('ebay', array('e_sef' => $this->_CleanSef($r['e_sef'])), array('e_id' => (int)$r['e_id']));
							}
						}	
	
	}
	
	function _CleanSef ($string) {

	$string = str_replace(" ", "-", $string);
	$string = str_replace("_", "-", $string);
	$cyrchars = array('А','Б','В','Г','Д','Е','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ч','Ц','Ш','Щ','Ъ','Ь','Ю','Я','а','б','в','г','д','е','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ч','ц','ш','щ','ъ','ь','ю','я');							 
	$latinchars = array('A','B','V','G','D','E','J','Z','I','I','K','L','M','N','O','P','R','S','T','U','F','H','CH','TS','SH','SHT','U','U','JU','YA','a','b','v','g','d','e','j','z','i','i','k','l','m','n','o','p','r','s','t','u','f','h','ch','ts','sh','sht','u','u','ju','ya');							 
	$string = str_replace($cyrchars, $latinchars, $string);	
	$string = str_replace('---', '-', $string);	
	$string = str_replace('--', '-', $string);
	$string =  preg_replace('/[^A-Za-z0-9\-]/', '',$string);	

	return $string;
	}
	*/
	
function _RealCount($array)
{

	if ($array != '') return count(explode(',', $array));
	else return 0;
}


function _logaction($location = '', $type = 'M', $datafrom = '', $datato = '', $eid = '', $itemid = '', $transid = '')
{

		foreach ($datato as $k => $v)
		{
			if ($v != $datafrom[$k])
			{
				if (isset($this->session->userdata['ownnames'])) $admin = $this->session->userdata['ownnames'];
				else $admin = 'Cron';
				
					
					$hmsg = array ('msg_title' => 'Action Log for '.(int)$eid.' - Field: '.$k.' ('.$datafrom[$k].'/'.$datafrom[$k].') by '.$admin, 'msg_body' => 'Action Log for '.(int)$eid.' - Field: '.$k.' ('.$datafrom[$k].'/'.$datafrom[$k].') by '.$admin, 'msg_date' => CurrentTime());
					
					//GoMail($hmsg, '365@1websolutions.net', 'norelpy@la-tronics.com');
				
				if ($k == 'Sold') $type = 'Q';
				$this->db->insert('ebay_actionlog', array('atype' => $type, 'e_id' => (int)$eid, 'ebay_id' => (int)$itemid, 'time' => CurrentTimeR(), 'datafrom' => $datafrom[$k], 'datato' => $v, 'field' => $k, 'admin' => $admin, 'trans_id' => (int)$transid, 'ctrl' => $location)); 			
			}
		}
}


function _logactionTEST($location = '', $type = 'M', $datafrom = '', $datato = '', $eid = '', $itemid = '', $transid = '')
{

		foreach ($datato as $k => $v)
		{
			if ($v != $datafrom[$k])
			{
				if (isset($this->session->userdata['ownnames'])) $admin = $this->session->userdata['ownnames'];
				else $admin = 'Cron';
				
					
					$hmsg = array ('msg_title' => 'Action Log for '.(int)$eid.' - Field: '.$k.' ('.$datafrom[$k].'/'.$datafrom[$k].') by '.$admin, 'msg_body' => 'Action Log for '.(int)$eid.' - Field: '.$k.' ('.$datafrom[$k].'/'.$datafrom[$k].') by '.$admin, 'msg_date' => CurrentTime());
					
					//GoMail($hmsg, '365@1websolutions.net', 'norelpy@la-tronics.com');
				
				if ($k == 'Sold') $type = 'Q';
				$this->db->insert('ebay_actionlogtest', array('atype' => $type, 'e_id' => (int)$eid, 'ebay_id' => (int)$itemid, 'time' => CurrentTimeR(), 'datafrom' => $datafrom[$k], 'datato' => $v, 'field' => $k, 'admin' => $admin, 'trans_id' => (int)$transid, 'ctrl' => $location)); 			
			}
		}
}


}