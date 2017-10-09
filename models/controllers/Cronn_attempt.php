<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Cronn extends Controller {
	
	function Cronn()
	{
		parent::Controller();

	}
	
	function index()
	{	
	}
	function ppi() 
	{ 
		ini_set('mysql.connect_timeout', 45);
		ini_set('default_socket_timeout', 45);
		phpinfo(); 
	}
	function TransactionsFinish()
	{
		$tdata = $this->session->userdata['tdata'];
		printcool ($tdata);
	}
	function Transactions()
	{

	
		//printcool ($insert);		
		//$this->db->limit(500);
		/*$this->db->select('rec');	
		$this->db->where("mkdt >", mktime()-86401); 
		$this->db->order_by("rec", "DESC");
		$q = $this->db->get('ebay_transactions');
		if ($q->num_rows() > 0) 
		{
			$dbres = $q->result_array();
			$echo = ' DB:'.count($dbres);			 
		}*/
		set_time_limit(60); 
		ini_set('mysql.connect_timeout', 45);
		ini_set('default_socket_timeout', 45);
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
		
		$xml = simplexml_load_string($responseXml);
		$list = $xml->TransactionArray->Transaction;
	
		$insert = false;
		if ($list) foreach ($list as $l)
		{
			$insert[(int)$l->ShippingDetails->SellingManagerSalesRecordNumber]['datetime'] = CleanBadDate($l->CreatedDate);
				$tmpdate = explode ('|', CleanBadDate($l->CreatedDate));
				$date = explode('-', trim($tmpdate[0]));
				$time = explode(':', trim($tmpdate[1]));
			$insert[(int)$l->ShippingDetails->SellingManagerSalesRecordNumber]['mkdt'] = mktime((int)$time[0], (int)$time[1], (int)$time[2], (int)$date[1], (int)$date[2], (int)$date[0]);
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

		ksort($insert);
		$echo .= ' EB:'.count($insert);
		//printcool ($insert);
		$this->session->set_userdata('tdata', $insert);
		
		Redirect('TransactionsFinish');
		
		foreach($dbres as $t)
			 {
			 	if (isset($insert[$t['rec']])) unset($insert[$t['rec']]);
			 }
		$echo .= ' FIN:'.count($insert);
		//printcool ($insert);
		if ($insert) foreach($insert as $i) 
			{
				$this->db->insert('ebay_transactions', $i); 
				$m = array ('msg_title' => 'New eBay Transaction (Record '.$i['rec'].') for Item ID '.$i['itemid'].' @ '.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime());					
				if ($i['mkdt'] >= (mktime()-86401)) $this->db->insert('admin_history', $m); 
				//GoMail($m, '', 'norelpy@la-tronics.com');
			}
			
		GoMail(array ('msg_title' => $echo.' @'.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime()), 'mitko@rusev.me', 'norelpy@la-tronics.com');
	}
	function CleanHistory()
	{
		$this->load->model('Mystart_model'); 
		$this->Mystart_model->DeleteOlderHistory(60);
		$m = array ('msg_title' => 'Admin history older than 60 days has been purged @ '.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime());					
		$this->db->insert('admin_history', $m); 
		GoMail($m, '', 'norelpy@la-tronics.com');
	}
	
	
function _PD()
	{
		//printcool ($insert);
		/*$this->db->select('et_id, datetime');	
		$q = $this->db->get('ebay_transactions');
		if ($q->num_rows() > 0) 
		{
			foreach($q->result_array() as $p)
			{
				$tmpdate = explode ('|', $p['datetime']);
				$date = explode('-', trim($tmpdate[0]));
				$time = explode(':', trim($tmpdate[1]));
				$p['mkdt'] = mktime((int)$time[0], (int)$time[1], (int)$time[2], (int)$date[1], (int)$date[2], (int)$date[0]);			
				$this->db->update('ebay_transactions', array('mkdt' => $p['mkdt']), array('et_id' => $p['et_id'])); 
			}
		}*/
	
	
	}
	

}