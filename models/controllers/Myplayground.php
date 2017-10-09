<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Myplayground extends Controller {

function Myplayground()
	{
		parent::Controller();		
		$this->load->model('Myebay_model'); 
		$this->load->model('Auth_model');
		$this->Auth_model->VerifyAdmin();
		
		//if ($this->session->userdata['admin_id'] == 7) { echo 'Sorry, you don\'t have clearance for here.';exit();}
		//if ($this->session->userdata['admin_id'] == 10) { echo 'Sorry, you don\'t have clearance for here.'; exit();}
		$this->load->model('Myproducts_model');
		$this->mysmarty->assign('zero', $this->Myproducts_model->CheckWeightZero());
		
		$this->mysmarty->assign('session',$this->session->userdata);
		$this->mysmarty->assign('action',$this->session->flashdata('action'));
		$this->mysmarty->assign('gotoebay',$this->session->flashdata('gotoebay'));
		$this->mysmarty->assign('error_msg',$this->session->flashdata('error_msg'));
		$this->mysmarty->assign('success_msg',$this->session->flashdata('success_msg'));
		$this->mysmarty->assign('area', 'Ebay');	
		$this->mysmarty->assign('ebupd', TRUE);
		
		$this->actabrv = array('e_img1' => 'Image 1', 'e_img2' => 'Image 2', 'e_img3' => 'Image 3', 'e_img4' => 'Image 4', 'quantity' => 'Local Quantity', 'e_part' => 'BCN', 'e_qpart' => 'BCN Count', 'buyItNowPrice' => 'Price', 'e_title' => 'Title', 'e_sef' => 'SEF URL', 'e_condition' => 'Condition', 'e_model' => 'Model', 'e_compat' => 'Compatibility', 'ebayquantity' => 'Local eBay Quantity', 'Ebay Quantity' => 'Local eBay Quantity', 'idpath' => 'Image Dir.', 'e_desc' => 'Descripion', 'upc' => 'UPC', 'e_manuf' => 'Brand', 'e_package' => 'Package', 'location' => 'Location', 'pCTitle' => 'Pri.Cat. Title', 'ebay_submitted' => 'Submitted','ebay_id' => 'eBay ID', 'sn' => 'Transaction BCN', 'asc' => 'ActShipCost', 'storeCatTitle' => 'Store Category', 'storeCatID' => 'Store Cat. ID');


	}

function index()
{
echo 'Hello world';	
	
}
function testsalewh()
{
	$this->load->model('Mywarehouse_model');
	$this->Mywarehouse_model->processbcnsfromorder(37931, 'ebay');
}
function FixEnded()
{
	$this->db->where('etype', 'a');
	$query = $this->db->get('ebay_live');

		if ($query->num_rows() > 0) 
			{
				foreach ($query->result_array() as $row)
				{
					$active[$row['itemid']] = true;
				}
				printcool ($active);
			}
	$this->db->select('e_id, ebay_id, ebended');
	//$this->db->where('ebended !=', '');
	$r = $this->db->get('ebay');		
	if ($r->num_rows() > 0)
	{ 	
		foreach ($r->result_array() as $rk => $rv)
		{
			if (isset($active[$rv['ebay_id']])) 
			{
				$this->db->update('ebay', array('ebended' => NULL), array('e_id' => $rv['e_id']));
				//printcool ($rv);
			}
			else
			{
				
				$this->db->update('ebay', array('ebended' => 'Ended By eBay Live Script', 'sitesell' => 0), array('e_id' => $rv['e_id']));
			}
		}
	}
	
}

function AutoMatchBCNs()
{
	$this->db->select('e_id, ebay_id, e_title, e_part, old_e_part');
	$this->db->where('e_part !=', '');
	$e = $this->db->get('ebay');		
	if ($e->num_rows() > 0)
	{ 	//printcool ($e->num_rows());
		$newtry = $e->result_array();
		$cnt = 1;
		
		
		foreach ($newtry as $k =>$n)
		{
			
			$newtry[$k]['e_part'] = $n['e_part'] = explode(',', $n['e_part']);			
			
			foreach ($n['e_part'] as $kk => $nn)
			{
				$this->db->select('wid, listingid, bcn');
				$this->db->where('deleted', 0);
				
				$this->db->where('bcn', trim($nn));	
				$w = $this->db->get('warehouse');
				if ($w->num_rows() > 0)
				{ 
				//printcool (trim($nn));
				//printcool ($newtry[$k]['e_part'][$kk]);
					$w = $w->row_array();
					$newtry[$k]['n_e_part'][$kk] = $w['bcn'];
					unset($newtry[$k]['e_part'][$kk]);
					//printcool ($newtry[$k]['e_part'][$kk]);
				}
			}
		}
		
		
		
		foreach ($newtry as $k => $v)
		{
			
			if ($v['old_e_part'] != '') printcool ('-------'.$v['old_e_part']);	
			if (is_array($v['e_part'])) 
			{
				foreach ($v['e_part'] as $ek => $ev)
				{
					if (trim($ev) == '' || trim($ev) == ' ') unset($newtry[$k]['e_part'][$ek]);	
				}
			}
			$newtry[$k]['e_part'] = implode(',', $newtry[$k]['e_part']);
			if (!isset($newtry[$k]['n_e_part'])) $newtry[$k]['n_e_part'] = array();
			
			$newtry[$k]['n_e_part_count'] = count($newtry[$k]['n_e_part']);
			$newtry[$k]['n_e_part']  = implode(',', $newtry[$k]['n_e_part']);
			
			$process[$v['e_id']] = array('old_e_part' => $newtry[$k]['e_part'], 'e_part' => $newtry[$k]['n_e_part'], 'e_qpart' => $newtry[$k]['n_e_part_count']);
			
			
			//$this->db->update('ebay', array(), array('e_id' => $v['e_id']));
			
		}
		//foreach ($process as $pk => $pv) $this->db->update('ebay', $pv, array('e_id' => $pk));
		printcool ($process);
		//printcool ($newtry);
	}
	exit();
	
	$this->db->select('e_id, ebay_id, e_title, e_part, old_e_part');
	$this->db->where('e_part !=', '');
	$e = $this->db->get('ebay');		
	if ($e->num_rows() > 0)
	{ 	printcool ($e->num_rows());
		foreach ($e->result_array() as $r)
		{
			//printcool ($r['e_qpart']);
			$bcn = explode(',', $r['e_part']);
			
			foreach ($bcn as $b)
			{
				//printcool ($b);
				$localbcn[trim($b)] = array(
											'listingid' => $r['e_id'],
											'localTitle' => $r['e_title'],
											'old' => $r['old_e_part']
											);	
			}	
			
		}
		
		
		
	$this->db->select('wid, listingid, bcn, title');
	$this->db->where('deleted', 0);
	$w = $this->db->get('warehouse');		
	if ($w->num_rows() > 0)
	{ 	
	printcool ($w->num_rows());
	printcool (count($localbcn));
		foreach ($w->result_array() as $b)
		{
			if (isset($localbcn[trim($b['bcn'])]))
			{ 

				$keep[trim($b['bcn'])] = $localbcn[trim($b['bcn'])];
				$keep[trim($b['bcn'])]['listingidwarehouse'] = $b['listingid'];
				$keep[trim($b['bcn'])]['warehousetitle'] = $b['title'];
				$keep[trim($b['bcn'])]['wid'] = $b['wid'];

				if ($b['listingid'] != $localbcn[trim($b['bcn'])]['listingid']) $keep[trim($b['bcn'])]['DONE'] = 0;
				else $keep[trim($b['bcn'])]['DONE'] = 1;
				unset($localbcn[trim($b['bcn'])]);
				//$this->db->update('warehouse', array('listingid' => $keep[trim($b['bcn'])]['listingid']), array('wid' => (int)$b['wid']));
				$applied[$keep[trim($b['bcn'])]['listingid']][] = trim($b['bcn']);
			}
			
		}
		
		foreach ($localbcn as $k => $v)
		{
			
			$oldbcn[$v['listingid']][] = $k;		
			
		}
		//printcool (count($localbcn));
		//ksort($localbcn);
		//printcool ($localbcn);
		
		
		printcool ($oldbcn);
		foreach ($oldbcn as $ok => $ov)
		{		
			//printcool (implode(',', $ov));
			
			//printcool ("update('ebay', array('old_e_part' => ".implode(',', $ov).", 'e_part' => '', 'e_qpart' => 0), array('e_id' => ".(int)$ok.")");
			//$res = $this->db->update('ebay', array('old_e_part' => implode(',', $ov), 'e_part' => '', 'e_qpart' => 0), array('e_id' => (int)$ok));	
			//printcool ($res);
		}
		
		
		printcool ($applied);
		foreach ($applied as $ak => $av)
		{
			//printcool (implode(',', $av));
			//printcool (count($av));
			//printcool ($ak);
			
			//$this->db->update('ebay', array('e_part' => implode(',', $av), 'e_qpart' => count($av)), array('e_id' => (int)$ak));			
		}
		
		
		
		
		
	}
echo "<table border=\"1\">";
echo '<tr>';
echo '<th>BCN</th>';

foreach($keep[key($keep)] as $kl => $element)
	{
		echo '<th>'.$kl.'</th>';

	}
	echo '</tr>';
	foreach($keep as $k => $v)
	{
			
		echo '<tr>';
		echo '<td>'.$k.'</td>';
		foreach ($v as $kl => $element)
		{
			echo '<td>'.$element.'</td>';
		}
		
		
		echo '</tr>';
	}
	
	echo "<table>";
	}
	
}



function GetSellerList()
{
	
	require($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
		
	
		$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
		$requestXmlBody .= '<GetSellerListRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
		$requestXmlBody .= '<DetailLevel>ItemReturnAttributes</DetailLevel>';
		$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
		$requestXmlBody .= "<Version>$compatabilityLevel</Version>
		<EndTimeFrom>".date('Y-m-d H:i:s', strtotime("-10 days"))."</EndTimeFrom>
  <EndTimeTo>".date('Y-m-d H:i:s')."</EndTimeTo>
  <IncludeVariations>TRUE</IncludeVariations>
  <GranularityLevel>Fine</GranularityLevel>
  <Pagination>
    <EntriesPerPage>100</EntriesPerPage>
    <PageNumber>1</PageNumber>
  </Pagination>";						
		$requestXmlBody .= '</GetSellerListRequest>';
		$verb = 'GetSellerList';
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
		$responseXml = $session->sendHttpRequest($requestXmlBody);
		if(stristr($responseXml, 'HTTP 404') || $responseXml == '') die('<P>Error sending request');
		$xml = simplexml_load_string($responseXml);
		printcool ($xml);
	
	
}
function makeebayactivestiesell()
{
	exit();
	//$this->db->update('ebay', array('sitesell' => 0));
	$this->db->select('e_id');
	$this->db->where('ebay_id !=', 0);
		$this->db->where('ebended', NULL);
		$q = $this->db->get('ebay');
		if ($q->num_rows() > 0)  
		{
			$cnt = 1;
			foreach ($q->result_array() as $e)
			{
				$this->db->update('ebay', array('sitesell' => 1), array('e_id' => $e['e_id']));
				//printcool ($e);
				//$cnt++;
			}
			
		}	
	
}
function fixoldbcns()
{
	exit();
	$this->db->select('e_id, e_part, e_qpart, old_e_part');
	$this->db->where('old_e_part !=', '');
		$this->db->where('ebended', NULL);
		$q = $this->db->get('ebay');
		if ($q->num_rows() > 0)  
		{
			$cnt = 1;
			foreach ($q->result_array() as $e)
			{
				$go = false;
				$list = explode (',', $e['old_e_part']);
				foreach ($list as $l)
				{
					$this->db->select('wid, lot, listingid, bcn, oldbcn');
					$this->db->where('lot', trim($l));
					$l = $this->db->get('warehouse');
					if ($l->num_rows() > 0)  
						{
							$wh = $l->row_array();
							printcool ($wh);
							$whe = explode (',', preg_replace('/\s+/', '', $e['e_part']));
							$whe[] = $wh['bcn'];
				printcool ($whe);
							foreach($whe as $k => $v)
							{
								if (trim($v) == '') unset($whe[$k]);	
							}
							$whce = count($whe);
							$whe = implode (',', $whe);
							$e['e_part'] = $whe;
							$e['e_qpart'] = $whce;						
							printcool ("('warehouse', array('listingid' => ".$e['e_id']."), array('wid' => ".$wh['wid'].")");							
							//$this->db->update('warehouse', array('listingid' => $e['e_id']), array('wid' => $wh['wid']));
							$go = TRUE;
						}						
				}
				if ($go) printcool ("('e_part' => ".rtrim($e['e_part'], ',').", 'e_qpart' => ".$e['e_qpart']."), array('e_id' => ".$e['e_id'].")");
				//if ($go) $this->db->update('ebay', array('e_part' => rtrim($e['e_part'], ','), 'e_qpart' => $e['e_qpart']), array('e_id' => $e['e_id']));
			}
				
				//printcool ($bcns);
				//$cnt++;

			
		}	
	
}
function bcncountcorrect()
{
	$this->db->select("e_id, ebay_id, e_part, e_qpart, quantity, ebayquantity");
	$this->db->where('e_part !=', '');
		$this->db->order_by("e_id", "DESC");
		$q = $this->db->get('ebay');
		if ($q->num_rows() > 0)  
		{
			foreach ($q->result_array() as $e)
			{
				printcool($e['e_part']);
				printcool(count(explode(',', $e['e_part'])));				
				//$this->db->update('ebay', array('e_qpart' => count(explode(',', $e['e_part']))), array('e_id' => $e['e_id']));
			}
		}	
}
function checkqidlistingexists()
{
	$this->db->select('wid, bcn, listingid');
	$this->db->where('listingid !=', 0);
	$this->db->where('aucid >', 148);
	$q = $this->db->get('warehouse');
		if ($q->num_rows() > 0)  
		{
			foreach ($q->result_array() as $w)
			{
				$this->db->select('e_id, e_part');
				$this->db->where('e_id', $w['listingid']);
				
				$eq = $this->db->get('ebay');
					if ($eq->num_rows() > 0)  
					{
						$res = $eq->row_array();
						$res['e_part'] = explode(',', $res['e_part']);
						if (!in_array($w['bcn'], $res['e_part'])) printcool ($w['bcn'], false, 'NO MATCH');
						else printcool ($w['bcn'], false, 'YES MATCH');
					}
			}
		}
	
}


function GetMyeBaySelling()
{exit();
	$ebl = array('active' => false, 'sold' => false, 'unsold' => false);
	$query = $this->db->get('ebay_live');
	foreach ($query->result_array() as $r)
	{
		if ($r['etype'] == 's') $ebl['sold'][$r['ebtrid']] = $r;
		elseif ($r['etype'] == 'u') $ebl['unsold'][$r['itemid']] = $r;
		else $ebl['active'][$r['itemid']] = $r;
	}
	
	
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
			{
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
	
	
	$this->db->select("e_id, ebay_id, e_title, quantity, ebayquantity, ebended");		
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
	
	//printcool ($itemids);
	//printcool ($mitemids);
	
	echo '<h1>Total matched in our DB: '.count($mitemids).' &nbsp;&nbsp;&nbsp;&nbsp; Not Matched in DB: '.count($itemids).'</h1>';
	echo '<table border="1"><tr><th>ItemID</th><th>Ebay Q. data</th><th>Loc. Q & Loc. EbQ.</th><th>Title (Red if not matched)</th><th>Admin ID</th><th>Local Ended / eBay Ended</th></tr>';		
	echo '<tr><td colspan="6"><h1>Active ('.$activecount.'):</h4></td></tr>';
	foreach($list['active'] as $k=>$v)
	{
		foreach ($v as $vv)
		{
			foreach ($vv->Item as $i)
			{
				echo '<tr>';
				
				$titlecolor = "";
				if (isset($mitemids[(int)$i->ItemID])  && (trim($i->Title) != trim($mitemids[(int)$i->ItemID]['e_title']))) $titlecolor = "style='color:red;'";
				if (isset($ebl['active'][(int)$i->ItemID]))
				{
					unset($ebl['active'][(int)$i->ItemID]);
					echo '<td>X</td>';	
				}
				else echo '<td style="color:red;">NOMATCH</td>';
				
		
		
				echo '<td><a target="_blank" href="http://www.ebay.com/itm/'.$i->ItemID.'">'.$i->ItemID.'</a></td><td>'.$i->QuantityAvailable.' available of '.$i->Quantity.'</td>';
				
				if (isset($mitemids[(int)$i->ItemID])) echo "<td>".$mitemids[(int)$i->ItemID]['quantity']." / ".$mitemids[(int)$i->ItemID]['ebayquantity']."</td>";
				else echo "<td></td>";
				
				echo '<td '.$titlecolor.'>'.$i->Title.'</td>';
				if (isset($mitemids[(int)$i->ItemID])) echo '<td><a target="_blank" href="'.Site_url().'/Myebay/showitem/'.$mitemids[(int)$i->ItemID]['e_id'].'" style="color:green;">'.$mitemids[(int)$i->ItemID]['e_id'].'</a></td>';
				else echo '<td><a target="_blank" href="http://www.ebay.com/itm/'.$i->ItemID.'" style="color:red;">Not Matched</a></td>';
				
				if (isset($mitemids[(int)$i->ItemID]) && $mitemids[(int)$i->ItemID]['ebended'] !='') echo '<td style="color:red;">Local Ended</td>';
				else echo '<td></td>';
				
				echo '</tr>';		
			}
		echo '<br clear="all">';
		}
	}
	
	echo '<tr><td colspan="6"><h1>Sold (60 Days):</h4></td></tr>';
	foreach($list['sold'] as $k=>$v)
	{
		
		foreach ($v->OrderTransaction as $i)
			{
				echo '<tr>';
				
				$titlecolor = "";
					
				if (isset($mitemids[(int)$i->Transaction->Item->ItemID])  && (trim($i->Transaction->Item->Title) != trim($mitemids[(int)$i->Transaction->Item->ItemID]['e_title']))) $titlecolor = "style='color:red;'";
				if (isset($ebl['sold'][(int)$i->Transaction->TransactionID]))
				{
					unset($ebl['sold'][(int)$i->Transaction->TransactionID]);
					echo '<td>X</td>';	
				}
				else echo '<td style="color:red;">NOMATCH</td>';
				echo '<td><a target="_blank" href="http://www.ebay.com/itm/'.$i->Transaction->Item->ItemID.'">'.$i->Transaction->Item->ItemID.'</a></td><td>'.$i->Transaction->QuantityPurchased.' / total sold '.$i->Transaction->Item->SellingStatus->QuantitySold.' of '.$i->Transaction->Item->Quantity.'</td>';
				
				
				if (isset($mitemids[(int)$i->Transaction->Item->ItemID])) echo "<td>".$mitemids[(int)$i->Transaction->Item->ItemID]['quantity']." / ".$mitemids[(int)$i->Transaction->Item->ItemID]['ebayquantity']."</td>";
				else echo "<td></td>";
				
				echo '<td '.$titlecolor.'>'.$i->Transaction->Item->Title.'</td>';
				if (isset($mitemids[(int)$i->Transaction->Item->ItemID])) echo '<td><a target="_blank" href="'.Site_url().'/Myebay/showitem/'.$mitemids[(int)$i->Transaction->Item->ItemID]['e_id'].'" style="color:green;">'.$mitemids[(int)$i->Transaction->Item->ItemID]['e_id'].'</a></td>';	
				else echo '<td style="color:red;"><a target="_blank" href="http://www.ebay.com/itm/'.$i->Transaction->Item->ItemID.'" style="color:red;">Not Matched</a></td>';
					
				if (isset($mitemids[(int)$i->Transaction->Item->ItemID]) && $mitemids[(int)$i->Transaction->Item->ItemID]['ebended'] !='') echo '<td style="color:red;">Local Ended&nbsp;&nbsp;&nbsp;';
				else echo '<td>';
				
				echo str_replace('T', ' ' , str_replace('Z', '' , $i->Transaction->CreatedDate)).'- '.$i->Transaction->TransactionID.'</td>';
				
				echo '</tr>';	
			}
		echo '<br clear="all">';		
	}
	
	echo '<tr><td colspan="6"><h1>Unsold (60 Days):</h4></td></tr>';
	foreach($list['unsold'] as $k=>$v)
	{
		
		foreach ($v->Item as $i)
			{	echo '<tr>';
			
				$titlecolor = "";
				if (isset($ebl['unsold'][(int)$i->ItemID]))
				{
					unset($ebl['unsold'][(int)$i->ItemID]);
					echo '<td>X</td>';	
				}else echo '<td style="color:red;">NOMATCH</td>';
				if (isset($mitemids[(int)$i->ItemID])  && (trim($i->Title) != trim($mitemids[(int)$i->ItemID]['e_title']))) $titlecolor = "style='color:red;'";
				echo '<td><a target="_blank" href="http://www.ebay.com/itm/'.$i->ItemID.'">'.$i->ItemID.'</a></td><td>'.$i->QuantityAvailable.' available of '.$i->Quantity.'</td>';

				if (isset($mitemids[(int)$i->ItemID])) echo "<td>".$mitemids[(int)$i->ItemID]['quantity']." / ".$mitemids[(int)$i->ItemID]['ebayquantity']."</td>";
				else echo "<td></td>";

				echo '<td '.$titlecolor.'>'.$i->Title.'</td>';
				if (isset($mitemids[(int)$i->ItemID])) echo '<td><a target="_blank" href="'.Site_url().'/Myebay/showitem/'.$mitemids[(int)$i->ItemID]['e_id'].'" style="color:green;">'.$mitemids[(int)$i->ItemID]['e_id'].'</a></td>';
				else echo '<td style="color:red;"><a target="_blank" href="http://www.ebay.com/itm/'.$i->ItemID.'" style="color:red;">Not Matched</a></td>';
				
				echo '<td>';
				if (isset($mitemids[(int)$i->ItemID]) && $mitemids[(int)$i->ItemID]['ebended'] !='') echo '<span style="color:red;">Local Ended</span>';
				echo '&nbsp;&nbsp;&nbsp;'.str_replace('T', ' ' , str_replace('Z', '' , $i->ListingDetails->EndTime));
				echo '</td>';
				echo '</tr>';
			}
		echo '<br clear="all">';		
	}
	
	echo '</table>';		
	printcool ($ebl);
	//printcool ($list);
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
  <ModTimeTo>".date('Y-m-d H:i:s')."</ModTimeTo><IncludeWatchCount>FALSE</IncludeWatchCount></GetSellerEventsRequest>";
						$verb = 'GetSellerEvents';
						$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
						$responseXml = $session->sendHttpRequest($requestXmlBody);
						if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
							die('<P>Error sending request');
						
						$xml = simplexml_load_string($responseXml);

						printcool ($xml);

}
function recountghost()
{
	$this->db->select('e_id, e_part, ngen');
	$this->db->where('e_part !=', '');
	$q = $this->db->get('ebay');
		if ($q->num_rows() > 0)  
		{			
			foreach ($q->result_array() as $e)
			{   
				$ngen = 0;
				$bcns = explode (',', $e['e_part']);
				foreach ($bcns as $b)
				{
					if (substr(trim($b), 0, 1) == 'G') $ngen++;					
				}
				$this->db->update('ebay', array('ngen' => $ngen), array('e_id' => $e['e_id']));
			}			
		}		
}
function GhostPopulate()
	{
		$id = 14900;
		$this->db->select("e_id, e_part, e_qpart, ebay_id, quantity, ngen");	
		$this->db->where("e_id", (int)$id);
		$this->db->limit(1);
		$r = $this->db->get('ebay');
		if ($r->num_rows() > 0)
		{ 
			$res = $r->row_array(); 
			$old['e_part'] = $res['e_part'];
			$old['e_qpart'] = $res['e_qpart'];
			
			$res['e_qpart'] = count(explode(',', $res['e_part']));
			$res['quantity'] = 9;
			printcool($res);
			if ((int)$res['e_qpart'] != (int)$res['quantity'])
			{
				if ((int)$res['quantity'] > (int)$res['e_qpart'])
				{
					$this->db->select("bcn");
					$this->db->where('waid' , 0);
					$this->db->order_by("wid", "DESC");
					$w = $this->db->get('warehouse', 1);

					if ($w->num_rows() > 0)
					{
						 $snext = $w->row_array();
						 $snext = str_replace('G', '', $snext['bcn']);
					}
					
					$val = $res['quantity']-$res['e_qpart'];
					$ngen = 0;					
					$ngenarray = '';
					if ($val > 0)
					{
						$ngen = $val;
						$loop = 0;
						
						while ($loop < $ngen)
						{
							$loop++;
							$next = $snext;
							$next = (int)str_replace ('G', '', $next);
							$next = "G".($next+1);
							$snext = $next;
							$ngenarray .= ''.$next.',';				
							
							$wh =array(
							'waid' => 0,
							'listingid' => $res['e_id'],
							'aucid' => 'G',
							'dates' => serialize(array('created' => CurrentTime(), 'createdstamp' => mktime())),
							'adminid' => (int)$this->session->userdata['admin_id'],
							'bcn' => $next
							);
							printcool ($wh);//$this->db->insert('warehouse', $wh);											
						}
						echo ('Created '.$loop.' Ghost BCNS');//$this->session->set_flashdata('success_msg', 'Created '.$loop.' Ghost BCNS');
					}
					
					$res['e_part'] = rtrim($res['e_part'], ',').',';
					printcool ($ngenarray);
					printcool ($res['e_part']);
					if ($ngenarray != '')
					{
						$new['e_part'] = $res['e_part'].rtrim($ngenarray, ',');
						$new['e_qpart'] = count(explode(',', ($res['e_part'].rtrim($ngenarray, ','))));
						printcool ("".$new['e_part']." [] ".$new['e_qpart']." [] ".($res['ngen']+(int)$ngen)." [] ".$res['e_id']."");
						 //$this->db->update('ebay', array('e_part' => $new['e_part'], 'e_qpart' => $new['e_qpart'], 'ngen' => ($res['ngen']+(int)$ngen)), array('e_id' => $res['e_id']));
							 
						 echo 'Updated Listing BCNS';//$this->session->set_flashdata('success_msg', 'Updated Listing BCNS');
					}
				}
				else
				{
					$bcnarray = explode(',', $res['e_part']);
					foreach ($bcnarray as $b)
					{
						if (substr($b, 0, 1) == 'G') $ghost[] = $b;
						else $actualbcn[] = $b;						
					}
					printcool ($ghost);
					printcool ($actualbcn);
					$val = $res['e_qpart']-$res['quantity'];
					if (count($ghost) < $val) echo 'More BCNs than available ghost items for removal.'; //$this->session->set_flashdata('error_msg', 'More BCNs than available ghost items for removal.');
					$pval = $val;
					$removed = '';
					printcool (count($ghost));
					printcool ($pval);
					$rem = 0;
					while ($rem < $pval)
					{	
						 $key = (count($ghost))-1;
						 $removed .= $ghost[$key].', ';
						 $dbr = $ghost[$key];
						 unset($ghost[$key]);
						 printcool (trim($dbr), false, 'DEL');//$this->db->update('warehouse', array('deleted' => 1), array('bcn' => trim($dbr), 'waid' => 0));
						 $rem++;
					}
					$removed = rtrim($removed, ', ');
					echo 'Removed Ghost BCNS '.$removed; //$this->session->set_flashdata('success_msg', 'Removed Ghost BCNS '.$removed);
					$ready = implode(',', $actualbcn);					
					if (count($ghost) > 0) $ready .= ','.implode(',', $ghost);
					$new['e_part'] = $ready;
					$new['e_qpart'] = count(explode(',', $ready));
					printcool ("update('ebay', array('e_part' => ".$new['e_part'].", 'e_qpart' => ".$new['e_qpart'].", 'ngen' => ".count($ghost)."), array(''e_id' => ".$res['e_id']."));");
					//$this->db->update('ebay', array('e_part' => new['e_part'], 'e_qpart' => new['e_qpart'], 'ngen' => count($ghost)), array('e_id' => $res['e_id']));
					
				}
				
				$this->db->insert('ebay_actionlog', array('atype' => 'B', 'e_id' => (int)$res['e_id'], 'ebay_id' => (int)$res['ebay_id'], 'time' => CurrentTimeR(), 'datafrom' => $old['e_part'], 'datato' => $new['e_part'], 'field' => 'e_part', 'admin' => $this->session->userdata['ownnames'], 'trans_id' => 0, 'ctrl' => 'UpdateQuantityGhostCheck')); 
				$this->db->insert('ebay_actionlog', array('atype' => 'B', 'e_id' => (int)$res['e_id'], 'ebay_id' => (int)$res['ebay_id'], 'time' => CurrentTimeR(), 'datafrom' => $old['e_qpart'], 'datato' => $new['e_qpart'], 'field' => 'e_qpart', 'admin' => $this->session->userdata['ownnames'], 'trans_id' => 0, 'ctrl' => 'UpdateQuantityGhostCheck')); 
				   
			}
		}
		
		
	}
	
	
	
	
	
	
	

	
	
}