<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Cronn extends Controller {
	
	function Cronn()
	{
		parent::Controller();
		
		//$time = mktime();
		//GoMail(array ('msg_title' => 'Running: '.gmdate("H:i:s", $time-(1411760686)).' '.$this->router->fetch_method(), 'msg_body' => '', 'msg_date' => CurrentTime()), 'mitko@rusev.me', $this->config->config['no_reply_email']);	
		
	}
	
	function index()
	{

	}
	function SheetsBackup()
	{
		exit();
		set_time_limit(300);
				ini_set('mysql.connect_timeout', 300);
				ini_set('max_execution_time', 300);  
				ini_set('default_socket_timeout', 300); 
				
			$days = 4;
			$dir = $this->config->config['pathtosystem'].'/backup/';
			 
			$nofiles = 0;
			 
				if ($handle = opendir($dir)) {
				while (( $file = readdir($handle)) !== false ) {
					if ( $file == '.' || $file == '..' || $file == 'index.html' || is_dir($dir.'/'.$file) ) {
						continue;
					}
					if ((time() - filemtime($dir.'/'.$file)) > ($days *86400)) {
						$nofiles++;						
						unlink($dir.'/'.$file);
					}
				}
				closedir($handle);
				//echo "Total files deleted: $nofiles \n";
			}

		$this->db->select('parent_key, sheet_key, parent_name, sheet_name');
		$this->db->where('no_use', 0);
		$this->db->where('mem_error', 1);
		$this->query = $this->db->get('google_sheets');
					if ($this->query->num_rows() > 0) 
					{	
						$str = '';
						foreach ( $this->query->result_array() as $r)
						{
							$str .= $r['parent_name'].' - '.$r['sheet_name'].'<br><br>';	
						}
						
						$this->db->insert('google_sheets_logs', array('log_value' => 'Google Sheet Errored Backup List - '.$str, 'log_date' => CurrentTime(), 'log_type' => 1));
						
						
					}
							
					$avoidsheets = array();
					$this->db->select('no_use, parent_key, sheet_key, parent_name, sheet_name');
					$this->db->where('no_use', 1);
					$this->query = $this->db->get('google_sheets');
					if ($this->query->num_rows() > 0) 
					{
							foreach ($this->query->result_array() as $r)
							{	
								$avoidsheets[$r['parent_key']][$r['sheet_key']] = $r['parent_name'].' / '.$r['sheet_name'];							
							}
					}
				

					$this->db->select('parent_key, sheet_key, parent_name, sheet_name');
					$this->db->where('no_use', 0);
					$this->db->where('mem_error', 0);
					$this->db->where('backup <', (mktime()-86300));					
					$this->db->order_by('backup', 'ASC');
					$this->query = $this->db->get('google_sheets', 1);
					if ($this->query->num_rows() > 0) 
					{		
							$this->load->library('Googlesheets');
							$this->load->library('zip');
							$access_token = $this->googlesheets->GetRefreshedAccessToken(GOOGLE_CLIENT_ID, GOOGLESS_REFRESH_TOKEN, GOOGLE_CLIENT_SECRET);
							$dbdata = $this->query->row_array();

							try {		
								$spreadsheet_info = $this->googlesheets->GetWorksheetsInformation($dbdata['parent_key'], $access_token);								
								
								foreach ($spreadsheet_info as $k => $s)
									{
										
										$worksheetkey = GetWorksheetSheetKey($s['worksheet_id']);
										
										if (!isset($avoidsheets[$dbdata['parent_key']][$worksheetkey]))
										{											
											$maxprocess = 1000;
											$rowbatch = ceil($s['row_count']/$maxprocess);
											$start = 1;
											while ($start <= $rowbatch)
											{
												$rowmax = $maxprocess*$start;
												if ($start == 1) $rowmin = 1;	
												else $rowmin = ($rowmax-$maxprocess)+1;
												if ($start == $rowbatch) $rowmax = $s['row_count'];
												
												$url = $s['worksheet_cells_feed_url'] . '?min-row='.$rowmin.'&min-col=1&max-col='.$s['col_count']. '&max-row='.$rowmax.'&return-empty=true&access_token=' . $access_token;							
											$ch = curl_init($url);
											curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
											curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
											$savename = CurrentTime().' - '. preg_replace('/[^A-Za-z0-9\. -_]/', '',  $dbdata['parent_name']).' : '. preg_replace('/[^A-Za-z0-9\. -_]/', '',  $s['title']).' - '.$rowmin.' to '.$rowmax.'.xml';
											//file_put_contents($this->config->config['pathtosystem'].'/backup/'.$savename, curl_exec($ch)); 
											$this->zip->add_data($savename, curl_exec($ch));
											$this->zip->archive($this->config->config['pathtosystem'].'/backup/'.$savename.'.zip');
										//	$dirsystem = $this->config->config['pathtosystem'].'/backup/'.$savename;
											//$systemfilename = $savename.'.tar';
											//system("tar cf $savename $dirsystem");	

										
												if ($start == $rowbatch)
												{
													 $this->db->update('google_sheets', array('backup' => mktime()), array('parent_key' => $dbdata['parent_key'], 'sheet_key' => $worksheetkey));
													 $this->db->insert('google_sheets_logs', array('log_value' => 'Google Sheet Backup - '.preg_replace('/[^A-Za-z0-9\. -_]/', '',  $dbdata['parent_name']).' : '. preg_replace('/[^A-Za-z0-9\. -_]/', '',  $s['title']), 'log_date' => CurrentTime(), 'log_type' => 1));
												
												}
												
												$start++;											
											}
											
											/*
											$this->db->update('google_sheets', array('mem_error' => 1), array('parent_key' => $dbdata['parent_key'], 'sheet_key' => $worksheetkey));
																					
											*/
										}
									}
									
								}
								catch(Exception $e) {
									echo ($e->getMessage());
									exit();
									}
					}
	}
function RefreshGoogleSheets()
	{
		exit();
		$this->db->select('sheet_key, sheet_name, parent_key, parent_name');
		$this->query = $this->db->get('google_sheets');
		if ($this->query->num_rows() > 0) 
		{
			foreach ($this->query->result_array() as $r)
			{
				$spreadsheet_keys[$r['parent_key']] = $r['parent_name'];
			}
				try {
					// Create an object
					$this->load->library('Googlesheets');
			
					//$this->googlesheets->testme();
					
					$access_token = $this->googlesheets->GetRefreshedAccessToken(GOOGLE_CLIENT_ID, GOOGLESS_REFRESH_TOKEN, GOOGLE_CLIENT_SECRET);
					
					//printcool ($access_token);		
					
					foreach ($spreadsheet_keys as $skey => $sname)
						{
						//printcool ('***** '. $skey);
						// Get Worksheet Information 
						$spreadsheet_info = $this->googlesheets->GetWorksheetsInformation($skey, $access_token);					
						//printcool ($spreadsheet_info);
						foreach ($spreadsheet_info as $k => $s)
						{
							// Get the column cells
								$worksheetkey = GetWorksheetSheetKey($s['worksheet_id']);
								
								$cells = $this->googlesheets->GetCells($s['worksheet_cells_feed_url'], 1, 1, 1, $s['col_count'], $access_token);								
								$cnt = 0;
								$tomatch = array_flip(array('title', 'ebay title', 'where listed', 'date listed', 'price sold', 'shipping cost', 'where sold', 'date sold'));
								$existing = false;
								//printcool ($cells);
								foreach ($cells as $k => $c)
								{										
									if ((int)$c['col'] == 1) 
									{ 
										$this->sheetlayout[$skey][$worksheetkey]['bcn'] = 1; 
										$cnt++; 
									}
									else
									{		
										$c['value'] = strtolower(trim($c['value']));							
									
										$existing[$c['value']] = $c['value'];	
																
										switch ($c['value'])
										{
											//case 'bcn':
											case 'title' : 
											case 'ebay title' :
											case 'where listed' :
											case 'date listed' :
											case 'price sold' :
											case 'shipping cost' :
											case 'where sold' :
											case 'date sold' :
											{ 
												$this->sheetlayout[$skey][$worksheetkey][str_replace(" ", "", $c['value'])] = (int)$c['col']; 
												$cnt++; 
												
												unset($tomatch[$c['value']]);
												unset($existing[$c['value']]);
												//printcool ($tomatch);
												//printcool ($c['value']);
												//printcool ('Unset: '.$c['value']);	
												//printcool ($existing, FALSE, 'EXISTING');
												//printcool ('--END--');									
											}
												
										}	
									}
								}												
								if ($cnt != 9)
								{
									if (isset($existing) && is_array($existing)) $existing = implode(', ', $existing);
									$displaycount = count($tomatch);
									$tomatch = implode(', ', array_flip($tomatch));
									//printcool ($spreadsheet_keys);
									//printcool ($skey);
									//printcool ($worksheetkey);
									GoMail(array('msg_title' => 'CRONN: Cannot match ('.$displaycount.') columns for '.$sname.' / '.$s['title'], 'msg_body' => '<strong>Unmatched:</strong> '.$tomatch.'<br><br><strong>In Sheet after match:</strong> '.$existing.'<br><br>Key: '.$skey.' / '.$worksheetkey, 'msg_date' => CurrentTime()), '365@1websolutions.net', $this->config->config['no_reply_email']);
									
									 exit('Cannot match all columns for sheet "'.$sname.' / '.$s['title'].'". Operations cannot continue!');
								}
								
								
								$this->sheetlayout[$skey][$worksheetkey]['uni_key'] = $skey.'|'.$worksheetkey;
								$this->sheetlayout[$skey][$worksheetkey]['sheet_key'] = $worksheetkey;
								$this->sheetlayout[$skey][$worksheetkey]['sheet_name'] = $s['title'];
								$this->sheetlayout[$skey][$worksheetkey]['parent_key'] = $skey;
								$this->sheetlayout[$skey][$worksheetkey]['parent_name'] = $sname;
								$this->sheetlayout[$skey][$worksheetkey]['sheetMod'] = CurrentTimeR();
								$this->sheetlayout[$skey][$worksheetkey]['worksheet_id'] = $s['worksheet_id'];
								$this->sheetlayout[$skey][$worksheetkey]['worksheet_list_feed_url'] = $s['worksheet_list_feed_url'];
								$this->sheetlayout[$skey][$worksheetkey]['worksheet_cells_feed_url'] = $s['worksheet_cells_feed_url'];
								$this->sheetlayout[$skey][$worksheetkey]['worksheet_edit_url'] = $s['worksheet_edit_url'];
								$this->sheetlayout[$skey][$worksheetkey]['row_count'] = $s['row_count'];
								$this->sheetlayout[$skey][$worksheetkey]['col_count'] = $s['col_count'];
								//printcool ($this->sheetlayout);
								$this->db->update('google_sheets', $this->sheetlayout[$skey][$worksheetkey], array('sheet_key' => $worksheetkey, 'parent_key' => $skey));
						}
						}						
					}
					catch(Exception $e) {
					echo $e->getMessage();
					//$msg .= $e->getMessage();
				}	
	}
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
$txt = CleanBadDate($data['Timestamp']).' - '.$data['ApiAccessRule']['DailyUsage'].' - '.$data['ApiAccessRule']['RuleCurrentStatus'].' @ '.CleanBadDate($data['ApiAccessRule']['ModTime']);

if ((int)$data['ApiAccessRule']['DailyUsage'] > 200) GoMail(array ('msg_title' => $txt, 'msg_body' => '', 'msg_date' => CurrentTime()), '365@1websolutions.net', $this->config->config['no_reply_email']);

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
				if (!write_file($this->config->config['ebaypath'].'/trans.txt', $responseXml)) GoMail(array ('msg_title' => 'Unable to write Trans.txt @ '.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime()), '365@1websolutions.net', $this->config->config['no_reply_email']);
				else {}//GoMail(array ('msg_title' => 'Transactions written @ '.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime()), '365@1websolutions.net', $this->config->config['no_reply_email']);				
			}
		if ($process) Redirect('TransactionsComplete');
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
	
function UpdateTransactionShippingCost()
{
		set_time_limit(300);
				ini_set('mysql.connect_timeout', 300);
				ini_set('max_execution_time', 300);  
				ini_set('default_socket_timeout', 300); 
		
		require_once($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
		
		$verb = 'GetSellingManagerSaleRecord';
		
		//Create a new eBay session with all details pulled in from included keys.php
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
		
		$this->db->select('rec, et_id, itemid, transid, asc, paydata, paidtime, sn');	
		$this->db->where('mkdt >= ', mktime()-518401);
		$this->db->where('cascupd', 0);
		$this->db->order_by("rec", "DESC");
		//$this->db->limit(10);
		$q = $this->db->get('ebay_transactions');

		$log = '';
		
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
				
				$end = printcool ($responseXml, TRUE, 'XML');
				$xml = simplexml_load_string($responseXml);
				$item = $xml->SellingManagerSoldOrder;	
				
				if ($item)
				{
				if ((string)$item->ActualShippingCost != $t['asc'])
				{
					$log .= printcool ($item, TRUE, 'ASC');
					$this->db->update('ebay_transactions', array('asc' => (string)$item->ActualShippingCost, 'cascupd' => 1), array('et_id' => $t['et_id']));
					$this->_logaction('Transactions', 'B', array('ActShipCost' => $t['asc']), array('ActShipCost' => (string)$item->ActualShippingCost), 0, $t['itemid'], $t['rec']);		
					
					$gdrv[$t['rec']] = array('sn' => $t['sn'], 'asc' => (string)$item->ActualShippingCost);
				}				 

			 $ar = $this->_XML2Array($item->OrderStatus);
			 $ar = $ar['OrderStatus'];

			 if (isset($ar['PaidTime']) && (CleanBadDate((string)$ar['PaidTime']) != $t['paidtime']) && (CleanBadDate((string)$ar['PaidTime']) != ''))
				{
					$log .= printcool ($item, TRUE, 'PAIDTIME');
					$this->db->update('ebay_transactions', array('paidtime' => CleanBadDate((string)$ar['PaidTime'])), array('et_id' => $t['et_id']));
					$this->_logaction('Transactions', 'B', array('PaidTime' => $t['paidtime']), array('PaidTime' => CleanBadDate((string)$ar['PaidTime'])), 0, $t['itemid'], $t['rec']);		
					$gdrv[$t['rec']] = array('sn' => $t['sn'], 'paid' => CleanBadDate((string)$ar['PaidTime']));
				}	
			 unset($ar['paidtime']);
			 $pd = serialize($ar);
			  if ($item && ($pd != $t['paydata']))
				{	
					$log .= printcool ($item, TRUE, 'PAYDATA');
					$this->db->update('ebay_transactions', array('paydata' => $pd), array('et_id' => $t['et_id']));					
				}
				}
        
			
		}	
		
		if (isset($gdrv))
		{			
			$this->db->select("svalue");
			$this->db->where('skey', 'googledriveuse');
			$q = $this->db->get('settings');
			$gdrval = 0;
			if ($q->num_rows() > 0) 
				{
					$gdrval = $q->row_array();
					$gdrval = (int)$gdrval['svalue'];
					if ($gdrval > 2) $gdrval = 0;
				}
				
			foreach ($gdrv as $k => $v)
			{
				
					$search_term = commasep(commadesep($v['sn']));		
					$workdata = array( 
									  'origin' => (int)$t['rec'], 
									  'origin_type' => 'TransactionBCNUpdate', 
									  'admin' => 'Cron',
									  'gdrv' => $gdrval
									  );
					
					if (isset($v['paid'])) $workdata['newvals'][] = array('name' => 'pricesold', 'value' =>  $v['paid']);
					if (isset($v['asc'])) $workdata['newvals'][] = array('name' => 'shippingcost', 'value' =>  $v['asc']);
					
					$this->load->library('Googledrive');
					$this->load->library('Googlesheets');
					if (trim($search_term) != '') $this->googlesheets->ProcessUpdate($this->googlesheets->PopulateWorksheetInfo($this->googledrive->FindInDrive(explode(',', $search_term))), $workdata);
				
				
			}		
		}
		//GoMail(array ('msg_title' => 'UpdateTransactionShippingCost Log @'.CurrentTime(), 'msg_body' => $log.$end, 'msg_date' => CurrentTime()), 'info@1websolutions.net', $this->config->config['no_reply_email']);
	}	
	
	
}
	
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
function ProcessTransactions($process = false)
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
				$inskey = (int)$l->ShippingDetails->SellingManagerSalesRecordNumber;
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
				$insert[$inskey]['transid'] = (string)$l->TransactionID;
			
			if (isset($l->ContainingOrder->ShippingDetails->SellingManagerSalesRecordNumber)) $insert[$inskey]['contorderid'] = (string)$l->ContainingOrder->ShippingDetails->SellingManagerSalesRecordNumber;
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
					
					//if ($insert[$t['rec']]['asc'] != $t['asc']) 
					//{
					//	$updstr .= ' ActShipCost: '.IfFillEmpty($insert[$t['rec']]['asc'],'b').' / '.IfFillEmpty($t['asc'],'r').' |'; 
					//	$updatedata['asc'] = $insert[$t['rec']]['asc'];
					//}
					
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
					if (strlen($updstr) > 7) $this->db->insert('admin_history', array('msg_type' => 1, 'msg_title' => 'Transaction Updated: '.$updstr, 'msg_body' => '', 'msg_date' => CurrentTime(),
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
		
		//GoMail(array ('msg_title' => $echo.' UN:'.$unsetcount.' @'.CurrentTime(), 'msg_body' => $this->printstr.$unsetted.' ('.$unsetcount.')', 'msg_date' => CurrentTime()), '365@1websolutions.net', $this->config->config['no_reply_email']);
		
		if ($process) Redirect('Myebay/GetEbayTransactions/Complete');
}
function DummyInsertGS()
{
	exit();
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
			//GoMail(array ('msg_title' => 'Google Spreadsheet Que Insert '.$iid, 'msg_body' => $data, 'msg_date' => CurrentTime()), '365@1websolutions.net', $this->config->config['no_reply_email']);
			//
			//$this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' =>'Google Spreadsheet Que Insert '.$iid, 'msg_body' => $data, 'msg_date' => CurrentTime(), 'e_id' => $eid, 'itemid' => $itemid, 'trec' => $trans, 'admin' => 'Auto', 'sev' => 0));
			
		}
	
	}
	else
	{
		//TURNED OFF FOR NOW
		//$dt = array ('msg_title' => 'Insert GS Empty BCN Array @ '.CurrentTime(), 'msg_body' => 'EID: '.$eid.'<br>ITEMID: '.$itemid.'<br>Transaction: '.$trans.'<br><br>'.serialize($bcnarray), 'msg_date' => CurrentTime());
		
		//GoMail($dt, '365@1websolutions.net', $this->config->config['no_reply_email']);
		
		//$this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => $dt['msg_title'], 'msg_body' => $dt['msg_body'], 'msg_date' => CurrentTime(), 'e_id' => $eid, 'itemid' => $itemid, 'trec' => $trans, 'admin' => 'Auto', 'sev' => 1));
	}
}
function ProcessGS()
{
	exit();
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
		
			//GoMail($dt1, '365@1websolutions.net', $this->config->config['no_reply_email']);
			
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
		//GoMail(array ('msg_title' => 'Google Spreadsheet Update Row '.$row_no, 'msg_body' => 'From: '.serialize($cols_old).' | To:'. serialize($to_be_updated), 'msg_date' => CurrentTime()), '365@1websolutions.net', $this->config->config['no_reply_email']);
		
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
								
								/*$this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => '<span style="color:blue;">Transaction BCN Auto Updated</span> to <span style="color:#FF9900;">"'.$moved.'"</span>', 'msg_body' => '', 'msg_date' => CurrentTime(),
												  'e_id' => $res['e_id'],
												  'itemid' => $res['ebay_id'],
												  'trec' => $i['rec'],
												  'admin' => 'Auto',
												  'sev' => ''));*/
												  
								$this->printstr .= printcool ($moved, true);
								$this->printstr .= printcool ($bcns,true);
								$bcns = commasep(commadesep(implode(',', $bcns)));
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
					GoMail(array ('msg_title' => 'Unable to write Cats.txt @ '.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime()), '365@1websolutions.net', $this->config->config['no_reply_email']);
					echo 'Unable to update Cats.';
				}
				else
				{
					//GoMail(array('msg_title' => 'Cats written @ '.CurrentTime(), 'msg_body' => $responseXml, 'msg_date' => CurrentTime()), '365@1websolutions.net', $this->config->config['no_reply_email']); 
					//echo 'Cats updated. Refresh the admin view for the product now and close this window.';
				}
			}
			else
			{
				
				GoMail(array ('msg_title' => 'No Data for Cats.txt @ '.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime()), '365@1websolutions.net', $this->config->config['no_reply_email']);
				
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
						$this->db->select('e_id, buyItNowPrice, e_title, ebayquantity, ebay_id, ebsold, e_part');
						$this->db->order_by("e_id", "DESC");
						//e//
						if ($xml->ItemArray->Item) foreach ($xml->ItemArray->Item as $i)
						{
							if ($start == 1) $this->db->where('ebay_id', (int)$i->ItemID);
							else $this->db->or_where('ebay_id', (int)$i->ItemID);
							
							$data[(int)$i->ItemID] = array('ebay_id' => (int)$i->ItemID, 'buyItNowPrice' => (string)trim($i->SellingStatus->CurrentPrice), 'e_title' => (string)trim($i->Title), 'ebayquantity' => (string)trim($i->Quantity));
							$start++;
						}
						//e//
						else exit();
						
						$mod = array();
						$dup = array();
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
											if ((string)trim($q['buyItNowPrice']) != $data[$q['ebay_id']]['buyItNowPrice']) $mod[$q['e_id']]['buyItNowPrice'] = $data[$q['ebay_id']]['buyItNowPrice'];
											if ((string)trim($q['e_title']) != $data[$q['ebay_id']]['e_title']) 
											{ 
												$mod[$q['e_id']]['e_title'] = $data[$q['ebay_id']]['e_title']; 
												$gdrvmod[$q['e_id']]['e_title'] = $data[$q['ebay_id']]['e_title'];
												$gdrvmod[$q['e_id']]['e_part'] = $ebdb[$q['ebay_id']]['e_part'];
											
											}
											if ((string)trim($q['ebayquantity']) != $data[$q['ebay_id']]['ebayquantity']) $mod[$q['e_id']]['ebayquantity'] = $data[$q['ebay_id']]['ebayquantity'];
											if (isset($mod[$q['e_id']])) { $mod[$q['e_id']]['ebay_id'] = $data[$q['ebay_id']]['ebay_id']; $local[$q['e_id']] = $q; }
											unset($data[$q['ebay_id']]);
											
											}
										}
									}
								

									
									foreach ($mod as $k => $v)
									{
										if (isset($data[$v['ebay_id']])) unset($data[$v['ebay_id']]);										
										$ebid = $v['ebay_id'];
										unset($v['ebay_id']);
										
										foreach ($v as $kk => $vv)
										{
											$field = array('buyItNowPrice' => 'Price', 'e_title' => 'Title', 'ebayquantity' => 'Local eBay Quantity');
											$action = array('buyItNowPrice' => 'M', 'e_title' => 'M', 'ebayquantity' => 'Q');
											
											$this->_logaction('eBayEvents', $action[$kk], array($field[$kk] => $local[$k][$kk]), array($field[$kk] => $vv), (int)$k, $ebid, 0);
										}

									$this->db->update('ebay' ,$v, array('e_id' => (int)$k));
										
										
									
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
												  
									if (isset($gdrvmod))
									{
										$this->db->select("svalue");
										$this->db->where('skey', 'googledriveuse');
										$q = $this->db->get('settings');
										$gdrval = 0;
										if ($q->num_rows() > 0) 
											{
												$gdrval = $q->row_array();
												$gdrval = (int)$gdrval['svalue'];
												if ($gdrval > 2) $gdrval = 0;
											}
										foreach ($gdrvmod as $k => $v)
										{										
												$search_term = commasep(commadesep($v['e_part']));		
												
												$workdata = array('newvals' => array(
																					 array('name' => 'ebaytitle',
																						   'value' =>  $v['e_title']
																						   )
																					 ), 
																  'origin' => (int)$q['e_id'], 
																  'origin_type' => 'GetSellerEvents', 
																  'admin' => 'Cron',
																  'gdrv' => $gdrval
																  );
												
												$this->load->library('Googledrive');
												$this->load->library('Googlesheets');
												if (trim($search_term) != '') $this->googlesheets->ProcessUpdate($this->googlesheets->PopulateWorksheetInfo($this->googledrive->FindInDrive(explode(',', $search_term))), $workdata);
											
										}
											
									}
									
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
		if ($start == 1) $this->db->where('ebay_id', (int)$i->ItemID);
		else $this->db->or_where('ebay_id', (int)$i->ItemID);

		if ($i->SellingStatus->ListingStatus == 'Completed')
		{
				$begin = CleanBadDate((string)trim($i->ListingDetails->StartTime));
				//2015-01-20 | 19:49:20
				$begin = explode("|", $begin);
				$begin[0] = explode("-", trim($begin[0]));				
				$begin[1] = explode(":", trim($begin[1]));
				$begin = mktime ((int)$begin[1][0], (int)$begin[1][1], (int)$begin[1][2], (int)$begin[0][1], (int)$begin[0][2], (int)$begin[0][0]);
				$finish = CleanBadDate((string)trim($i->ListingDetails->EndTime));	
				$finish = explode("|", $finish);
				$finish[0] = explode("-", trim($finish[0]));				
				$finish[1] = explode(":", trim($finish[1]));
				$finish = mktime ((int)$finish[1][0], (int)$finish[1][1], (int)$finish[1][2], (int)$finish[0][1], (int)$finish[0][2], (int)$finish[0][0]);
				$diff = $finish - $begin;				
				$diff = gmdate("d / H:i", $diff);				
				//if ($diff < 2592000) $reason = 'Premature ending ('.$diff.')';
				//else $reason = 'Listing ended ('.$diff.')';
			
			if ($i->SellingStatus->QuantitySold == $i->Quantity) $reason = 'All quantity sold ['.$diff.']';
			else $reason = 'Quantity not sold ('.$i->SellingStatus->QuantitySold.' of '.$i->Quantity.') ['.$diff.']';
			
			$data[(int)$i->ItemID] = array('ebended' => CleanBadDate((string)trim($i->ListingDetails->EndTime)), 'endedreason' => $reason);
		}
	
		$start++;
	}
	///
	else exit();
	//exit();
	$updatestring = '';
	$endedupdate = 0;
	$query = $this->db->get('ebay');						
	if ($query->num_rows() > 0) 
	{																
		foreach ($query->result_array() as $q)
		{
			if (isset($data[$q['ebay_id']]) && $q['ebended'] == '')
			{
			$updatestring .= 'Ebay Listing <a href="'.Site_url().'Myebay/Search/'.$q['e_id'].'" target="_blank" style="color: #419aff; font-size:10px;"><img src="'.Site_url().'images/admin/b_search.png" class="linkicon" />'.$q['e_id'].'</a> - ItemID: <a href="http://www.ebay.com/itm/'.$q['ebay_id'].'" target="_blank" style="color: #419aff; font-size:10px;"><img src="'.Site_url().'images/admin/b_search.png" class="linkicon"/>'.$q['ebay_id'].'</a> ended at '.$data[$q['ebay_id']]['ebended'].' - '.$data[$q['ebay_id']]['endedreason'].'<br>';
			
			$this->db->update('ebay' ,array('ebended' => $data[$q['ebay_id']]['ebended'], 'endedreason' => $data[$q['ebay_id']]['endedreason']), array('e_id' => (int)$q['e_id']));									
			$endedupdate++;								
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

function TestEndedListings()
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
	$requestXmlBody .= "<ModTimeFrom>".date('Y-m-d H:i:s', strtotime("-10 days"))."</ModTimeFrom><ModTimeTo>".date('Y-m-d H:i:s')."</ModTimeTo><IncludeWatchCount>FALSE</IncludeWatchCount>  </GetSellerEventsRequest>";

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
	if ($xml->ItemArray->Item) foreach ($xml->ItemArray->Item as $i)
	{
		if ($start == 1) $this->db->where('ebay_id', (int)$i->ItemID);
		else $this->db->or_where('ebay_id', (int)$i->ItemID);
							
		if ($i->SellingStatus->ListingStatus == 'Completed')
		{
			printcool ($i);
			if ((int)$i->SellingStatus->QuantitySold == (int)$i->Quantity) $reason = 'Quantity Sold';
			else 			
			{
				$begin = CleanBadDate((string)trim($i->ListingDetails->StartTime));
				printcool ($begin);
				//2015-01-20 | 19:49:20
				$begin = explode("|", $begin);
				$begin[0] = explode("-", trim($begin[0]));				
				$begin[1] = explode(":", trim($begin[1]));
				$begin = mktime ((int)$begin[1][0], (int)$begin[1][1], (int)$begin[1][2], (int)$begin[0][1], (int)$begin[0][2], (int)$begin[0][0]);
				$finish = CleanBadDate((string)trim($i->ListingDetails->EndTime));
				printcool ($finish);
				$finish = explode("|", $finish);
				$finish[0] = explode("-", trim($finish[0]));				
				$finish[1] = explode(":", trim($finish[1]));
				$finish = mktime ((int)$finish[1][0], (int)$finish[1][1], (int)$finish[1][2], (int)$finish[0][1], (int)$finish[0][2], (int)$finish[0][0]);
				$diff = $finish - $begin;
				printcool ($diff);
				$diff = gmdate("d / H:i", $diff);				
				if ($diff < 2592000) $reason = 'Premature ending ('.$diff.')';
				else $reason = 'Listing ended ('.$diff.')';
			}
			$data[(int)$i->ItemID] = array('ebended' => CleanBadDate((string)trim($i->ListingDetails->EndTime)), 'endedreason' => $reason);
			printcool ($data[(int)$i->ItemID]);
		}
	
		$start++;
	}
	///
	else exit();

	exit();
	$updatestring = '';
	$endedupdate = 0;
	$query = $this->db->get('ebay');						
	if ($query->num_rows() > 0) 
	{																
		foreach ($query->result_array() as $q)
		{
			if (isset($data[$q['ebay_id']]) && $q['ebended'] == '')
			{
			$updatestring .= 'Ebay Listing <a href="'.Site_url().'Myebay/Search/'.$q['e_id'].'" target="_blank" style="color: #419aff; font-size:10px;"><img src="'.Site_url().'images/admin/b_search.png" class="linkicon" />'.$q['e_id'].'</a> - ItemID: <a href="http://www.ebay.com/itm/'.$q['ebay_id'].'" target="_blank" style="color: #419aff; font-size:10px;"><img src="'.Site_url().'images/admin/b_search.png" class="linkicon"/>'.$q['ebay_id'].'</a> ended at '.$data[$q['ebay_id']]['ebended'].'<br>';
			
			$this->db->update('ebay' ,array('ebended' => $data[$q['ebay_id']]['ebended']), array('e_id' => (int)$q['e_id']));									
			$endedupdate++;								
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

function TestGetSellerEvents()
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
						$this->db->select('e_id, buyItNowPrice, e_title, ebayquantity, ebay_id, e_part');
						$this->db->order_by("e_id", "DESC");
						//e//
						if ($xml->ItemArray->Item) foreach ($xml->ItemArray->Item as $i)
						{
							if ($start == 1) $this->db->where('ebay_id', (int)$i->ItemID);
							else $this->db->or_where('ebay_id', (int)$i->ItemID);
							
							$data[(int)$i->ItemID] = array('ebay_id' => (int)$i->ItemID, 'buyItNowPrice' => (string)trim($i->SellingStatus->CurrentPrice), 'e_title' => (string)trim($i->Title), 'ebayquantity' => (string)trim($i->Quantity));
							$start++;
						}
						//e//
						else exit();
						
						$mod = array();
						$dup = array();
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
											if (isset($data[$q['ebay_id']]))
											{
											if ((string)trim($q['buyItNowPrice']) != $data[$q['ebay_id']]['buyItNowPrice']) $mod[$q['e_id']]['buyItNowPrice'] = $data[$q['ebay_id']]['buyItNowPrice'];
											if ((string)trim($q['e_title']) != $data[$q['ebay_id']]['e_title']) $mod[$q['e_id']]['e_title'] = $data[$q['ebay_id']]['e_title'];
											if ((string)trim($q['ebayquantity']) != $data[$q['ebay_id']]['ebayquantity']) $mod[$q['e_id']]['ebayquantity'] = $data[$q['ebay_id']]['ebayquantity'];
											if (isset($mod[$q['e_id']])) { $mod[$q['e_id']]['ebay_id'] = $data[$q['ebay_id']]['ebay_id']; $local[$q['e_id']] = $q; }
											unset($data[$q['ebay_id']]);
											/*if (isset($mod[$q['e_id']]))
											{
											printcool($q);
											printcool($data[$q['ebay_id']]);
											printcool ($mod[$q['e_id']]);
											printcool ('-----<br><Br><Br><br><br>');
											}*/
											}
										}
									}
								

									
									foreach ($mod as $k => $v)
									{
										if (isset($data[$v['ebay_id']])) unset($data[$v['ebay_id']]);										
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
											
											//$this->_logaction('eBayEvents', $action[$kk], array($field[$kk] => $local[$k][$kk]), array($field[$kk] => $vv), (int)$k, $ebid, 0);
										}
									
									//printcool ('update(\'ebay\' ,'.$v.', array(\'e_id\' => '.(int)$k.')');

									//$this->db->update('ebay' ,$v, array('e_id' => (int)$k));
										
										
									
									}
									$stats['mod'] = count($mod);
									$stats['notfnd'] = count($data);
									$stats['dup'] = count($dup);
									
									$stats['run'] = CurrentTimeR();
									printcool ($stats);
									break;
									$stats['duplist'] = '';
									if ($stats['dup'] > 0) $stats['duplist'] = serialize($dup);
									$stats['notfndlist'] = array();
	
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
								if (!write_file($this->config->config['ebaypath'].'/shipping.txt', $responseXml)) GoMail(array ('msg_title' => 'Unable to write Shippinh.txt @ '.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime()), '365@1websolutions.net', $this->config->config['no_reply_email']);
								else {}//GoMail(array ('msg_title' => 'Shipping written @ '.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime()), '365@1websolutions.net', $this->config->config['no_reply_email']);
							}
	
						
	
	}

	
	function CleanHistory()
	{
		/*$this->load->model('Mystart_model'); 
		$this->Mystart_model->DeleteOlderHistory(60);
		$m = array ('msg_title' => 'Admin history older than 60 days has been purged @ '.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime());					
		$this->db->insert('admin_history', $m); 
		*/
		//GoMail($m, '', $this->config->config['no_reply_email']);
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
				//GoMail($m, '', $this->config->config['no_reply_email']);
			}
			
		GoMail(array ('msg_title' => $echo.' @'.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime()), '365@1websolutions.net', $this->config->config['no_reply_email']);
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
					
					//GoMail($hmsg, '365@1websolutions.net', $this->config->config['no_reply_email']);
				
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
					
					//GoMail($hmsg, '365@1websolutions.net', $this->config->config['no_reply_email']);
				
				if ($k == 'Sold') $type = 'Q';
				$this->db->insert('ebay_actionlogtest', array('atype' => $type, 'e_id' => (int)$eid, 'ebay_id' => (int)$itemid, 'time' => CurrentTimeR(), 'datafrom' => $datafrom[$k], 'datato' => $v, 'field' => $k, 'admin' => $admin, 'trans_id' => (int)$transid, 'ctrl' => $location)); 			
			}
		}
}


}