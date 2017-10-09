<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tester extends Controller {

	function Tester()
	{
		parent::Controller();	
	}
	
	function index()
	{
           
	echo 'Nothing here';				
	}
	function Email()
	{
	
		$this->msg_data = array ('msg_title' => 'Test e-mail from '.$this->config->config['sitename'],
											'msg_body' => 'Test e-mail from '.$this->config->config['sitename'],
											'msg_date' => CurrentTime()
											);			
		
		GoMail($this->msg_data, 'drusev82@yahoo.com', '', 1);
		GoMail($this->msg_data, 'drusev82@hotmail.com', '', 1);
		GoMail($this->msg_data, 'reece@abv.bg', '', 1);
		GoMail($this->msg_data, 'mr.reece@gmail.com', '', 1);
		GoMail($this->msg_data, 'info@1websolutions.net', '', 1);
		
		$this->msg_data = array ('msg_title' => 'Проба кирилица емейл от '.$this->config->config['sitename'],
											'msg_body' => 'Проба кирилица емейл от '.$this->config->config['sitename'],
											'msg_date' => CurrentTime()
											);			
		
		GoMail($this->msg_data, 'drusev82@yahoo.com', '', 1);
		GoMail($this->msg_data, 'drusev82@hotmail.com', '', 1);
		GoMail($this->msg_data, 'reece@abv.bg', '', 1);
		GoMail($this->msg_data, 'mr.reece@gmail.com', '', 1);
		GoMail($this->msg_data, 'info@1websolutions.net', '', 1);
		
		echo 'All is sent';
		$this->email->print_debugger();
	}
function QpartUpdate()
{

		$this->db->select("e_id, e_part");		
		$this->db->order_by("e_id", "DESC");
		$this->db->where('e_part !=', '');
		$this->query = $this->db->get('ebay');
		if ($this->query->num_rows() > 0) $res = $this->query->result_array();
		foreach ($res as $k => $r)
		{
			$res[$k]['e_qpart'] = count(explode(',', $r['e_part']));
			
			$this->db->update('ebay', array('e_qpart' => count(explode(',', $r['e_part']))), array('e_id' => (int)$r['e_id']));
		}
		//printcool ($res);
		
}

function SerialProcess()
{
		$this->db->select("e_id, e_part");		
		$this->db->order_by("e_id", "DESC");
		$this->db->limit(500);
		$this->query = $this->db->get('ebay');
		if ($this->query->num_rows() > 0) $res = $this->query->result_array();
		foreach ($res as $k => $r)
		{
		$res[$k]['su'] = $this->_SerialSave($r['e_part']);
		}
		printcool ($res);
		
}

function _SerialSave($ser = '')
{
	if ($ser != '')
	{
		$ser = str_replace(' ','',$ser);
		$str = $ser;
		$ser = explode(',', $ser);	
		if (is_array($ser))
		{	
			foreach ($ser as $ks => $s)
			{		
				$st = explode('_', $s);		
				if (is_array($st) && count($st) > 1)
				{
					$tmp = explode ('-', $st[1]);
					if (isset($tmp[0]) && isset($tmp[1]) && is_numeric($tmp[0]) && is_numeric($tmp[1]) && $tmp[1] > $tmp[0]) 
					{
						$ser[$ks] = array();
						while ($tmp[0] <= $tmp[1])
								{
									$ser[$ks][] = $st[0].'-'.$tmp[0];								
									$tmp[0]++;	
								}
						$ser[$ks] = implode(',', $ser[$ks]);
					}
					else
					{ 
						if (is_array($st)) $ser[$ks] = implode('_', $st);
						else $ser[$ks] = $st; 
					}
				}			
			}
		}
		$ser = implode(',', $ser);
		return $ser;	
	}	
}


function SerialStringProcess($ser = '')
{
$ser = 'SAM_3218T-3220T';
	if ($ser != '')
	{
		$ser = str_replace(' ','',$ser);
		$str = $ser;
		$ser = explode(',', $ser);	

		if (is_array($ser))
		{	
			foreach ($ser as $ks => $s)
			{		
				$st = explode('_', $s);		
				if (is_array($st) && count($st) > 1)
				{
					$tmp = explode ('-', $st[1]);
					$tmpA[0] =  ereg_replace("[^A-Za-z]", "", $tmp[0]);
					$tmpA[1] =  ereg_replace("[^A-Za-z]", "", $tmp[1]);
					$tmp[0] =  ereg_replace("[^0-9]", "", $tmp[0]);
					$tmp[1] =  ereg_replace("[^0-9]", "", $tmp[1]);
					if (isset($tmp[0]) && isset($tmp[1]) && is_numeric($tmp[0]) && is_numeric($tmp[1]) && $tmp[1] > $tmp[0]) 
					{
						$ser[$ks] = array();
						while ($tmp[0] <= $tmp[1])
								{
									$ser[$ks][] = $st[0].'-'.$tmp[0].$tmpA[0];								
									$tmp[0]++;	
								}
						$ser[$ks] = implode(',', $ser[$ks]);
					}
					else
					{ 
						if (is_array($st)) $ser[$ks] = implode('_', $st);
						else $ser[$ks] = $st; 
					}
				}			
			}
		}
		$ser = implode(',', $ser);
		echo $ser;	
	}	
}
function testpending()
{
	
	set_time_limit(600);
		ini_set('mysql.connect_timeout', 600);
		ini_set('max_execution_time', 600);  
		ini_set('default_socket_timeout', 600); 
		require($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');

		$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
		$requestXmlBody .= '<GetItemTransactionsRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
		$requestXmlBody .= '<DetailLevel>ItemReturnAttributes</DetailLevel>';
		$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
		$requestXmlBody .= '<IncludeContainingOrder>true</IncludeContainingOrder>';
		$requestXmlBody .= "<ItemID>201678574002</ItemID>";
		$requestXmlBody .= "<TransactionID>1476697689010</TransactionID> 
		</GetItemTransactionsRequest>";	
		$verb = 'GetItemTransactions';

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
				printcool(simplexml_load_string($responseXml));	
			}
	
}

function CronnGetMyeBaySelling()
{

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
						printcool($r->num_rows(), false, '$r->num_rows()');
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
						printcool($r->num_rows(), false, '$r->num_rows()');
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
			//GoMail(array ('msg_title' => '$v issue @ '.CurrentTime(), 'msg_body' => explore($v, FALSE, '$v').printcool($k, true, '$k'), 'msg_date' => CurrentTime()), 'errors@la-tronics.com', $this->config->config['no_reply_email']);
		
			//GoMail(array ('msg_title' => '$v issue @ '.CurrentTime(), 'msg_body' => explore($list['active'][$k], FALSE, '$list[active][$k]'), 'msg_date' => CurrentTime()), 'errors@la-tronics.com', $this->config->config['no_reply_email']);
		
		   // echo ('$v is bugged');
		}
		if (count($v) > 0) foreach ($v as $vv)
		{ 
			foreach ($vv->Item as $i)
			{	
				$itemid = (int)$i->ItemID;
				
				if (isset($mitemids[$itemid]) && $mitemids[$itemid]['sitesell'] == 0)
				{
					$mitemids[$itemid]['sitesell'] = 1;
					//$this->db->update('ebay', array('sitesell' => 1), array('e_id' => (int)$mitemids[$itemid]['e_id']));
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
				
				//$this->db->insert('ebay_live', $data);
				
				if ((int)$data['eid'] > 0 && isset($sellingonsite[$data['eid']]))  unset($sellingonsite[$data['eid']]);
				
				//if ((int)$data['eid'] == 14516) GoMail(array ('msg_title' => '1. 14516 FOUND @'.CurrentTime(), 'msg_body' => printcool($data,true), 'msg_date' => CurrentTime()), 'errors@la-tronics.com', $this->config->config['no_reply_email']);
				if (isset($mitemids[$itemid]))
				{
					
						
						//if ((int)$data['eid'] == 14516) GoMail(array ('msg_title' => '2. 14516 $i->QuantityAvailable/$mitemids[$itemid][\'ebayquantity\'] @'.CurrentTime(), 'msg_body' => printcool((int)$i->QuantityAvailable,true, '(int)$i->QuantityAvailable').printcool((int)$mitemids[$itemid]['ebayquantity'],true, '$mitemids[$itemid][\'ebayquantity\']'), 'msg_date' => CurrentTime()), 'errors@la-tronics.com', $this->config->config['no_reply_email']);
						
						
						
						if ((int)$i->QuantityAvailable != (int)$mitemids[$itemid]['ebayquantity'] && $mitemids[$itemid]['ebended'] == '')
						{printcool ($i,false,'$i 1');
						printcool ($i,false,'$mitemids 1');
							if ((int)$data['eid'] > 0) 
							{
								//$this->db->insert('ebay_cron', array('e_id' => $data['eid'], 'data' => 'eBayQN: '.$mitemids[$itemid]['ebayquantity'].' - To: '.(int)$i->QuantityAvailable.' - (ref data: QuantityAvailable(eBay):'.$i->QuantityAvailable.', @ GetMyEbaySelling)', 'time' => CurrentTime(), 'ts' => mktime()));
							
							$ra['admin'] = 'Cron eBaySelling';
							$ra['time'] = CurrentTime();
							$ra['ctrl'] = 'CronnGetMyeBaySelling';
							$ra['field'] = 'ebayquantity';
							$ra['atype'] = 'Q';
							$ra['e_id'] = (int)$data['eid'];
							$ra['ebay_id'] = $itemid;
							$ra['datafrom'] = $mitemids[$itemid]['ebayquantity'];
							$ra['datato'] = (int)$i->QuantityAvailable;
										
							//$this->db->insert('ebay_actionlog', $ra); 
							unset($ra);
							//$this->db->update('ebay', array('ebayquantity' => (int)$i->QuantityAvailable), array('e_id' => (int)$data['eid']));
							}
						}
						//if ((int)$data['eid'] == 14516) GoMail(array ('msg_title' => '3. 14516 $i->QuantityAvailable/3x @'.CurrentTime(), 'msg_body' => printcool((int)$i->QuantityAvailable,true, '(int)$i->QuantityAvailable').printcool($mitemids[$itemid],true, '$mitemids[$itemid]'), 'msg_date' => CurrentTime()), 'errors@la-tronics.com', $this->config->config['no_reply_email']);
						
						if ((((int)$i->QuantityAvailable != $mitemids[$itemid]['qn_ch1']) || ((int)$i->QuantityAvailable != $mitemids[$itemid]['qn_ch2']) || ((int)$i->QuantityAvailable != $mitemids[$itemid]['quantity'])) && $mitemids[$itemid]['ebended'] == '')
						{
							printcool ($i,false,'$i 2');
						printcool ($i,false,'$mitemids 2');
							//if ((int)$data['eid'] == 14516) GoMail(array ('msg_title' => '4. 14516  3x ENTERED @'.CurrentTime(), 'msg_body' => printcool((int)$i->QuantityAvailable,true, '(int)$i->QuantityAvailable').printcool($mitemids[$itemid],true, '$mitemids[$itemid]'), 'msg_date' => CurrentTime()), 'errors@la-tronics.com', $this->config->config['no_reply_email']);
							
							if ((int)$i->QuantityAvailable != $mitemids[$itemid]['qn_ch1'])
							{
							if ((int)$data['eid'] > 0) 
							{	//$this->db->insert('ebay_cron', array('e_id' => $data['eid'], 'data' => 'QnCh1: '.$mitemids[$itemid]['qn_ch1'].' - To: '.(int)$i->QuantityAvailable.' - (ref data: QuantityAvailable(eBay):'.$i->QuantityAvailable.', @ GetMyEbaySelling)', 'time' => CurrentTime(), 'ts' => mktime()));
							
							$ra['admin'] = 'Cron eBaySelling';
							$ra['time'] = CurrentTime();
							$ra['ctrl'] = 'CronnGetMyeBaySelling';
							$ra['field'] = 'qn_ch1';
							$ra['atype'] = 'Q';
							$ra['e_id'] = (int)$data['eid'];
							$ra['ebay_id'] = $itemid;
							$ra['datafrom'] = $mitemids[$itemid]['qn_ch1'];
							$ra['datato'] = (int)$i->QuantityAvailable;
										
							//$this->db->insert('ebay_actionlog', $ra); 
							unset($ra);
							//$this->db->update('ebay', array('qn_ch1' => (int)$i->QuantityAvailable), array('e_id' => (int)$data['eid']));
							}
							}
							
							if ((int)$i->QuantityAvailable != $mitemids[$itemid]['qn_ch2'])
							{
							if ((int)$data['eid'] > 0) 
							{
								//$this->db->insert('ebay_cron', array('e_id' => $data['eid'], 'data' => 'QnCh2: '.$mitemids[$itemid]['qn_ch2'].' - To: '.(int)$i->QuantityAvailable.' - (ref data: QuantityAvailable(eBay):'.$i->QuantityAvailable.', @ GetMyEbaySelling)', 'time' => CurrentTime(), 'ts' => mktime()));
						
							$ra['admin'] = 'Cron eBaySelling';
							$ra['time'] = CurrentTime();
							$ra['ctrl'] = 'CronnGetMyeBaySelling';
							$ra['field'] = 'qn_ch2';
							$ra['atype'] = 'Q';
							$ra['e_id'] = (int)$data['eid'];
							$ra['ebay_id'] = $itemid;
							$ra['datafrom'] = $mitemids[$itemid]['qn_ch2'];
							$ra['datato'] = (int)$i->QuantityAvailable;
										
							//if ((int)$ra['e_id'] != 0) $this->db->insert('ebay_actionlog', $ra); 
							unset($ra);
							//$this->db->update('ebay', array('qn_ch2' => (int)$i->QuantityAvailable), array('e_id' => (int)$data['eid']));
							}
							}
							
							
							//if ((int)$data['eid'] == 14516) GoMail(array ('msg_title' => '5. 14516 PRE BCN REGEN @'.CurrentTime(), 'msg_body' => printcool((int)$i->QuantityAvailable,true, '(int)$i->QuantityAvailable').printcool($mitemids[$itemid],true, '$mitemids[$itemid]'), 'msg_date' => CurrentTime()), 'errors@la-tronics.com', $this->config->config['no_reply_email']);
							//if ((int)$data['eid'] > 0)// $this->_BCNRegen((int)$data['eid']);
						}	
						
											
						unset($itemid);
					}
				}
			}
		}
	
	
	
}

function _RealCount($array)
{

	if ($array != '') return count(explode(',', $array));
	else return 0;
}


function GetOrders()
{
set_time_limit(1500); 
		require_once($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
		$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
		$requestXmlBody .= '<GetOrdersRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
		$requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';//ItemReturnAttributes
		$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
		$requestXmlBody .= "<Version>$compatabilityLevel</Version>";
		$requestXmlBody .= '
		 <IncludeFinalValueFee>'.TRUE.'</IncludeFinalValueFee>
		 <CreateTimeFrom>'.date("Y-m-d H:i:s", strtotime("-10 Days")).'</CreateTimeFrom>
  <CreateTimeTo>'.date("Y-m-d H:i:s").'</CreateTimeTo>
  <NumberOfDays>10</NumberOfDays>
	  <OrderRole>Seller</OrderRole>
	  <OrderStatus>Active</OrderStatus>
		<Pagination>
		<EntriesPerPage>200</EntriesPerPage>
		</Pagination>
		</GetOrdersRequest>';
		/*
  <IncludeFinalValueFee> boolean </IncludeFinalValueFee>
  <ListingType> ListingTypeCodeType </ListingType>
	<OrderIDArray> OrderIDArrayType
    <OrderID> OrderIDType (string) </OrderID>
    <!-- ... more OrderID values allowed here ... -->
  </OrderIDArray>
  
  
  <OutputSelector> string </OutputSelector>
  <!-- ... more OutputSelector values allowed here ... -->
    */
				
		
		$verb = 'GetOrders';

		//Create a new eBay session with all details pulled in from included keys.php
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
				
		//send the request and get response
		$responseXml = $session->sendHttpRequest($requestXmlBody);
		if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
			die('<P>Error sending request');
		
		$xml = simplexml_load_string($responseXml);
		printcool ($xml);
		//foreach ($xml->OrderArray->Order as $t) printcool ($t);

}

function DoTransfer()
{

								$bcns = 'b310,b311,b312,b313';
								$bcncount = $this->_RealCount($bcns);	
								$bcns = explode(',',$bcns);
								
								printcool ($bcns);		
								printcool ($bcncount);		
								printcool ('BCN MOVE BEGIN');
								
								$start = 1;
								printcool ($start);
								$moved = array();
								$unavailble = 0;
								while ($start <= 6)
								{
									
									if (isset($bcns[$bcncount-1]))
									{
									$moved[] = trim($bcns[$bcncount-1]);
									unset($bcns[$bcncount-1]);
									$bcncount = count($bcns);
									printcool ($start);
									}
									else
									{
										$unavailable++;
									}
									$start++;
									printcool ($moved);
									printcool ($bcncount);						
								

								}
								$moved = implode(',', $moved);
								
										printcool ('BCN MOVE END');
								printcool ($unavailable);
								printcool ($moved);
								
								exit();


								$remove = 'b311';
								$remove = explode(',', $remove);
								printcool ($remove);
								break;
								$bcn = 'b310,b311,b312,b313';
								$bcncount = $this->_RealCount($bcn);					

								$bcns = explode(',', $bcn);
								printcool ($bcns);		
								printcool ('BCN MOVE BEGIN');
								foreach ($bcns as $k => $v)
								{
									if ($remove == trim($v))
									{
										$moved = trim($v);
										 unset($bcns[$k]);
									}								
								}
								
								//$this->db->update('ebay_transactions', array('sn' => $moved), array('rec' => (int)$i['rec']));
								//$this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => '<span style="color:blue;">Transaction '.$i['rec'].' BCN Updated</span> to <span style="color:#FF9900;">"'.$moved.'"</span>', 'msg_body' => '', 'msg_date' => CurrentTime()));
								printcool ($moved);
								
								$bcns = implode(',', $bcns);
								printcool ($bcns);
								
								



}
}