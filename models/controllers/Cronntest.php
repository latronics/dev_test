<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Cronntest extends Controller {
	
	function Cronntest()
	{
		parent::Controller();

	}
	
	function index()
	{	




	ini_set('max_execution_time', 120);  
	echo 'staring';
	sleep(110);
	echo 'slept';
	/*		ini_set('mysql.connect_timeout', 300);
		ini_set('max_execution_time', 300);  
		ini_set('default_socket_timeout', 300); 
	phpinfo();*/
	}
function _object2array($object) { return @json_decode(@json_encode($object),1); } 
function _GetSpreadSheets($key = '')
{
	if ($key == '') exit('NO KEY');
	printcool ($key);
	if (!isset($this->spreadsheet_info[$key]))
			 {
				$this->spreadsheet_info[$key] = $this->googlesheets->GetWorksheetsInformation($key, $this->access_token);
				
				foreach ($this->spreadsheet_info[$key] as $skk => $svv)
				{
			 	$this->cells[$key][$skk] = $this->googlesheets->GetCells($this->spreadsheet_info[$key][$skk]['worksheet_cells_feed_url'], 1, $this->spreadsheet_info[$key][$skk]['row_count'], ord(strtolower('A'))-96, ord(strtolower('A'))-96, $this->access_token);			
				}
			 }

}
function ProcessGS()
{
	$this->db->where('proc', 0);
	$eb = $this->db->get('gsdata_test');
	if ($eb->num_rows() > 0) 
	{
		$res = $eb->result_array();
	}
	else exit();
	$sheet = '0ApHMD7nkSM4YdDRveElySzNrNGExQjVkWXVLdnJYc2c';
/*
set_time_limit(150);
		ini_set('mysql.connect_timeout', 150);
		ini_set('max_execution_time', 150);  
		ini_set('default_socket_timeout', 150); 

		sleep (45);		phpinfo();*/
	//$this->db->select("gs_id, gs_title");

	
//printcool ($this->sheets);
//printcool ($res);
/*foreach ($res as $r)
{

printcool (unserialize($r['tvalue']));

} */

 
//break;
	//set_time_limit(60);
	//ini_set('mysql.connect_timeout', 60);
	//ini_set('max_execution_time', 60);  
	//ini_set('default_socket_timeout', 60); 
		
	require_once($this->config->config['pathtopublic'].'/gsssettings.php');
	//$spreadsheet_key = '0ApHMD7nkSM4YdGtMU3NwUk9vZ1hyM3VxUG1BdHRteXc';
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
		$this->access_token = $this->googlesheets->GetRefreshedAccessToken(GOOGLE_CLIENT_ID, GOOGLE_REFRESH_TOKEN, GOOGLE_CLIENT_SECRET);

		// Get Worksheet Information 
		//$this->spreadsheet_info = $this->googlesheets->GetWorksheetInformation($spreadsheet_key, $this->access_token);
		// Get the column cells
		//$this->cells = $this->googlesheets->GetCells($this->spreadsheet_info['worksheet_cells_feed_url'], 1, $this->spreadsheet_info['row_count'], ord(strtolower('A'))-96, ord(strtolower('A'))-96, $this->access_token);	
		
		
		
		//////
		
		/// GET ALL SPREADSHEETS , ARRAY KEY = ID
		
		/////
		
		$col_search = array('name' => 'A', 'value' => $r['bcn']);

		
		
		$res[$k]['row_no'] = 0;
		$res[$k]['sheet_no'] = false;		
	
				$this->_GetSpreadSheets($sheet);
				printcool ($this->spreadsheet_info);
							exit();
				
				//// here
				for($i=0;$i<sizeof($this->cells[$this->sheets[$r['gsid'.$s]]['gs_key']]);$i++) 
				{					
					if($this->cells[$i]['value'] == $col_search['value']) 
					{
						$res[$k]['row_no'] = ($i+1);
						$res[$k]['sheet_no'] = $s;
						$res[$k]['sheet'] = $this->sheets[$r['gsid'.$s]]['gs_key'];
						$res[$k]['sheetid'] = $this->sheets[$r['gsid'.$s]];
						
						break;
					}
				}
				
		

			if($r['row_no'] == 0)
			{
				$dt1 = array ('msg_title' => 'Cannot match BCN ('.$col_search['value'].') in Google Spreadsheet @ '.CurrentTime(), 'msg_body' => 'GS Record: '.$r['gsid'], 'msg_date' => CurrentTime());
			
				//GoMail($dt1, '365@1websolutions.net', 'norelpy@la-tronics.com');
				
				//$this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => $dt1['msg_title'], 'msg_body' => $dt1['msg_body'], 'msg_date' => CurrentTime(), 'e_id' => $r['eid'], 'itemid' => $r['itemid'], 'trec' => $r['trans'], 'admin' => 'Auto', 'sev' => 1));
			}
			
			//$this->db->update('gsdata', array('time' => CurrentTimer(), 'proc' => 1), array('gsid' => $r['gsid']));
		
		
		
		exit();
		//////////////////////////////
		
		foreach ($res as $r)		
		{
		$newvals = unserialize($r['tvalue']);
			
		/// COLS BASED ON SPREADSHEET
		$gsidnum = $r['sheet_no'];				
		$cols_new[$r['gsid']] = array(
							array('name' => $this->cells[$this->sheets[$r['gsid'.$gsidnum]]['field_title']], 'value' => $newvals[7]), 
							array('name' => $this->cells[$this->sheets[$r['gsid'.$gsidnum]]['field_paidtime']], 'value' => $newvals[10]), 
							array('name' => $this->cells[$this->sheets[$r['gsid'.$gsidnum]]['field_paid']], 'value' => $newvals[11]), 
							array('name' => $this->cells[$this->sheets[$r['gsid'.$gsidnum]]['field_type']], 'value' => $newvals[13]), 
							array('name' => $this->cells[$this->sheets[$r['gsid'.$gsidnum]]['field_auction']], 'value' => $newvals[21])
						);
		///
		// Get the cells of the row that was found
		
		$cellsin[$r['gsid']] = $this->googlesheets->GetCells($this->spreadsheet_info[$this->sheets[$r['gsid'.$gsidnum]]['gs_key']]['worksheet_cells_feed_url'], $r['row_no'], $r['row_no'], 1, $this->spreadsheet_info[$this->sheets[$r['gsid'.$gsidnum]]['gs_key']]['col_count'], $this->access_token);
		
		$cols_old[$r['gsid']] = array();
		
		$to_be_updated[$r['gsid']] = array();
		
		for($i=0;$i<sizeof($cols_new[$r['gsid']]);$i++) 
		{
			$cols_old[$r['gsid']][] = $cellsin[$r['gsid']][ord(strtolower($cols_new[$i]['name']))-97];
			$cellsin[$r['gsid']][ord(strtolower($cols_new[$i]['name']))-97]['value'] = $cols_new[$r['gsid']][$i]['value'];
			/////////////////
			$to_be_updated[$r['gsid']][] = $this->cellsin[$r['gsid']][ord(strtolower($cols_new[$i]['name']))-97];
			/////////////////
		}
		// Update Cells
		
///////////////////////////LOOP  LAST		//UPDATE
		
		
		exit();
		
		//$this->googlesheets->UpdateCells($this->spreadsheet_info[$this->sheets[$r['gsid1']]['gs_key']]['worksheet_cells_feed_url'], $to_be_updated[$r['gsid']], $this->access_token);
		// 'value' key of each element of $cols_old stores the old values of the cells that were updated
					
		$colmap = array(1 => 'A',2 => 'B',3 => 'C',4 => 'D',5 => 'E',6 => 'F',7 => 'G',8 => 'H',9 => 'I',10 => 'J',11 => 'K',12 => 'L',13 => 'M',14 => 'N',15 => 'O',16 => 'P',17 => 'Q',18 => 'R',19 => 'S',	20 => 'T',21 => 'U',22 => 'V',23 => 'W',24 => 'X',25 => 'Y',26 => 'Z');		
		
		foreach ($cols_old as $clsoldk => $clsoldv) foreach ($$clsold as $c => $cv) { unset($cols_old[$clsoldk][$c]['id']); unset($cols_old[$clsoldk][$c]['row']); unset($cols_old[$clsoldk][$c]['edit_url']); } 
		
		foreach ($to_be_updated as $tbupk => $tbupv) foreach ($tbup as $c => $cv) { unset($to_be_updated[$tbupk][$c]['id']); unset($to_be_updated[$tbupk][$c]['row']); unset($to_be_updated[$tbupk][$c]['edit_url']); }  
	

		//$this->db->update('gsdata_test', array('sheet' => $r['sheet'], 'sheetno' => $r['sheet_no'], 'row' => $r['row_no'], 'fvalue' => serialize($cols_old[$r['gsid']])), array('gsid' => $r['gsid']));		
				
		
		//$this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => 'Google Spreadsheet Update Row '.$r['row_no'].' - SheetID '.$r['sheetid'].' - GSheet No:'.$r['sheet_no'], 'msg_body' => '<strong>From:</strong> '.serialize($cols_old[$r['gsid']]).' | <strong>To:</strong>'. serialize($to_be_updated[$r['gsid']]), 'msg_date' => CurrentTime(), 'e_id' => $r['eid'], 'itemid' => $r['itemid'], 'trec' => $r['trans'], 'admin' => 'Auto', 'sev' => 0));
		//
		//GoMail(array ('msg_title' => 'Google Spreadsheet Update Row '.$r['row_no'].' - SheetID '.$r['sheetid'].' - GSheet No:'.$r['sheet_no'], 'msg_body' => 'From: '.serialize($cols_old[$r['gsid']]).' | To:'. serialize($to_be_updated[$r['gsid']]), 'msg_date' => CurrentTime()), '365@1websolutions.net', 'norelpy@la-tronics.com');
		
		}
		
	}

	catch(Exception $e) {
		$msg .= $e->getMessage();
	}
	
echo $msg;
	
	
}



function Transactions($process = false)
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
				printcool (simplexml_load_string($responseXml));
			}
}




}