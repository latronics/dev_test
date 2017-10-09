<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//update

function FindInDrive()
{	
$this->Auth_model->CheckListings();


	$string = trim($this->input->post('gsearch'));
	if ($string == '') exit('No search');
	echo 'Testing';	
}
function SaveGS($id = 0, $page = 1)
{
$this->Auth_model->CheckListings();


	if ((int)$id > 0)
	{
	
		$this->db->select('gsid1, gsid2, gsid3, gsid4, gsid5, ebay_id');
		$this->db->where('e_id', (int)$id);
		$gss = $this->db->get('ebay');
		if ($this->gss->num_rows() > 0) 
		{
			$r = $this->query->row_array();						
		}
	 $this->db->update('ebay', array('gsid1' => (int)$this->input->post('gsid1'), 'gsid2' => (int)$this->input->post('gsid2'), 'gsid3' => (int)$this->input->post('gsid3'), 'gsid4' => (int)$this->input->post('gsid4'), 'gsid5' => (int)$this->input->post('gsid5')), array('e_id' => (int)$id));
	 	
		$this->_logaction('SaveGoogleSheet', 'G',array('gsid1' => $r['gsid1']), array('gsid1' => (int)$this->input->post('gsid1')), $id, $r['ebay_id'], 0);
		$this->_logaction('SaveGoogleSheet', 'G',array('gsid2' => $r['gsid2']), array('gsid2' => (int)$this->input->post('gsid2')), $id, $r['ebay_id'], 0);
		$this->_logaction('SaveGoogleSheet', 'G',array('gsid3' => $r['gsid3']), array('gsid3' => (int)$this->input->post('gsid3')), $id, $r['ebay_id'], 0);
		$this->_logaction('SaveGoogleSheet', 'G',array('gsid4' => $r['gsid4']), array('gsid4' => (int)$this->input->post('gsid4')), $id, $r['ebay_id'], 0);
		$this->_logaction('SaveGoogleSheet', 'G',array('gsid5' => $r['gsid5']), array('gsid5' => (int)$this->input->post('gsid5')), $id, $r['ebay_id'], 0);
	 	
	$this->session->set_flashdata('success_msg', 'SUCCESS - Updated Sheets for Ebay item '.(int)$id);
	$this->session->set_flashdata('action', (int)$id);
	}
	 Redirect('Myebay/ListItems/'.(int)$page);
}

/*function test1()
{

		$submitbcns = '';
		$submitbcnscount = $this->_RealCount($submitbcns);			
		$submitbcns = explode(',', $submitbcns);
		
		$oldtransactionbcn = '';
		$oldtransactionbcn = explode(',', rtrim(',', $oldtransactionbcn));	
		
		$listingbcns = '';
		$listingbcnsoldcount = $this->_RealCount($listingbcns);
		$listingbcnsold = $listingbcns;		
		$listingbcns = explode(',', $listingbcns);
		
		if ($listingbcnsoldcount > 0)
		{
			foreach ($listingbcns as $k => $v)
			{
				foreach ($submitbcns as $rk => $rv)
				{
					if (trim($rv) == trim($v)) unset($listingbcns[$k]);
					//LOG MATCHED
				}
			}
		}
		
		if ($submitbcnscount > 0)
		{
			foreach ($oldtransactionbcn as $k => $v)
			{
				foreach ($submitbcns as $rk => $rv)
				{
					if (trim($rv) == trim($v)) unset($oldtransactionbcn[$k]);
					//LOG RETURNED
				}
			}
			
			foreach ($oldtransactionbcn as $v) $listingbcns[] = $v;
		}
		
		$oldtransactionbcn = implode(',', array_map('trim', $oldtransactionbcn));
		$submitbcns = implode(',', array_map('trim', $submitbcns));
		$listingbcns = implode(',', array_map('trim', $listingbcns));	
		$listingbcncount = $this->_RealCount($listingbcns);
		
		//DIMITRI - We must keep what's been submited, even if it's not matched in the listing. There will be cases this will be needed.
		$this->db->update('ebay_transactions', array('sn' => $submitbcns, 'mark' => 1), array('rec' => (int)$rec));
		$this->_logaction('TransactionView', 'B',array('Transaction BCN' => $oldtransactionbcn), array('Transaction BCN' => $submitbcns), $res['e_id'], $res['ebay_id'], $rec);

		$this->db->update('ebay', array('e_part' => $listingbcns, 'e_qpart' => $listingbcncount), array('e_id' => $res['e_id']));
		$this->_logaction('TransactionView', 'B',array('BCN' => $listingbcnsold), array('BCN' => $listingbcns), $res['e_id'], $res['ebay_id'], $rec);
		$this->_logaction('TransactionView', 'B',array('BCN Count' => $listingbcnsoldcount), array('BCN Count' => $listingbcncount), $res['e_id'], $res['ebay_id'], $rec);
				
		//DIMITRI 22.07.2014 - KHIM, i've commented your code because some of thing here shouldn't be done. Unique - let the administrators see if there are duplicates. No piece of data, even duplicate should be automatically removed. Sorting aswell. There's a log of changes in which it's preferable for visual purposes to have everything like it originally was, with just the difference in data. At some point the admins will have a hard time tracking changes if we reorder the bcns, or if were trying to find a missing bcn by hand. 
		
		//if($bcns != ''){
		//	$bcns = explode(",", $bcns);
		//	array_push($bcns, $_POST['oldbcn']);
		//	$bcns = array_filter(array_map('trim', $bcns));
		//	//$bcns = array_diff($bcns, array(trim($_POST['oldbcn'], " ")));
		//	$bcns = array_unique($bcns);
		//	sort($bcns);
		//	$bcns = implode(", ", $bcns);
		//	$bcns = $bcns . ", ";
		//}else{
		//	$bcns = $_POST['oldbcn'];
		//}
	
}*/












/*
if ((int)$rec != 0 && isset($_POST['bcn']))
{
	$this->db->select('qty');
	$this->db->where('rec', (int)$rec);
	$t = $this->db->get('ebay_transactions');
	if ($t->num_rows() > 0) $tr = $t->row_array();
	else { echo 'Transaction data not found. Contact administrator'; exit(); }
	
	

	if ($isarray) $submitbcns = commadesep(implode(',', $_POST['bcn']));    
	else $submitbcns = commadeseptrim((string)$_POST['bcn']);                     
	$submitbcnscount = $this->_RealCount($submitbcns);			
	$submitbcns = explode(',', $submitbcns);
		
	$oldtransactionbcn = commadesep(trim((string)$_POST['oldbcn']));
	$oldtransactionbcn = explode(',', $oldtransactionbcn);

	if ($submitbcnscount == 0) $this->db->insert('admin_history', array ('msg_type' => 1, 
												  'msg_title' => '<span style="color:blue;">Transaction Submitted BCN Value</span> <span style="color:red;">IS EMPTY</span>', 
												  'msg_body' => '', 
												  'msg_date' => CurrentTime(),
												  'e_id' => 0,
												  'itemid' => 0,
												  'trec' => $rec,
												  'admin' => $this->session->userdata['ownnames'],
												  'sev' => 1												  
												  ));
	
	if ($submitbcnscount != $tr['qty']) $this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => '<span style="color:blue;">Transaction Submitted BCN Value</span> <span style="color:red;">DOES NOT MATCH QUANTITY ('.$submitbcnscount.'/'.$tr['qty'].')</span> on record.', 'msg_body' => '', 'msg_date' => CurrentTime(),
												  'e_id' => 0,
												  'itemid' => 0,
												  'trec' => $rec,
												  'admin' => $this->session->userdata['ownnames'],
												  'sev' => 1));

	$this->db->select('e.e_id, e.e_part, e.ebay_id, t.admin, t.revs');
	$this->db->where('t.itemid = e.ebay_id');
	$this->db->where('t.rec', (int)$rec);
	$q = $this->db->get('ebay as e, ebay_transactions as t');
	if ($q->num_rows() == 0) 
	{
		$this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => '<span style="color:blue;">Transaction BCN Update</span> found <span style="color:red;">no ItemID match</span> in eBay listings.', 'msg_body' => '', 'msg_date' => CurrentTime(),
												  'e_id' => 0,
												  'itemid' => 0,
												  'trec' => $rec,
												  'admin' => $this->session->userdata['ownnames'],
												  'sev' => 1));
	}
	else
	{	
		$res = $q->row_array();
		 
		$listingbcns = commadesep($res['e_part']);
		$listingbcnsoldcount = $this->_RealCount($listingbcns);
		$listingbcnsold = $listingbcns;		
		$listingbcns = explode(',', $listingbcns);
				
		$matched = array();
		
		
		if ($listingbcnsoldcount > 0)
		{
			foreach ($listingbcns as $k => $v)
			{
				foreach ($submitbcns as $rk => $rv)
				{
					if (trim($rv) == trim($v)) 
					{
						$matched[] = trim($listingbcns[$k]);
						unset($listingbcns[$k]);
					}
					
				}
			}
			
				
		}
		
		if ($listingbcnsoldcount == 0 || (count($matched) == 0))
		{
			$this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => '<span style="color:blue;">Transaction BCN Update</span> found <span style="color:red;">no available BCNs</span> in listings.', 'msg_body' => '', 'msg_date' => CurrentTime(),
												  'e_id' => $res['e_id'],
												  'itemid' => $res['ebay_id'],
												  'trec' => $rec,
												  'admin' => $this->session->userdata['ownnames'],
												  'sev' => 1));			
		}	
		
		if ($submitbcnscount > 0)
		{
			foreach ($oldtransactionbcn as $k => $v)
			{
				foreach ($submitbcns as $rk => $rv)
				{
					if (trim($rv) == trim($v)) unset($oldtransactionbcn[$k]);
				}
			}
			
			if (count($oldtransactionbcn) > 0)
				{
					foreach ($oldtransactionbcn as $v)
						{
							$listingbcns[] = $v;
						}
				}
		}
		
		sort($matched);
		$matched = implode(', ', $matched);
		
		$oldtransactionbcn = rtrim(implode(', ', array_map('trim', $oldtransactionbcn)), ',');
		sort($submitbcns);
		$submitbcns = rtrim(implode(', ', array_map('trim', $submitbcns)), ',');
		sort($listingbcns);
		$listingbcns = rtrim(implode(', ', array_map('trim', $listingbcns)), ',');	
		$listingbcncount = $this->_RealCount($listingbcns);
		
		$this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => '<span style="color:blue;">Transaction BCN Updated</span> to <span style="color:#FF9900;">"'.$submitbcns.'"</span> (Matched: <span style="color:#FF9900;">'.$matched.'</span>) [Old value: <span style="color:#FF9900;">'.$oldtransactionbcn.'</span>]', 'msg_body' => '', 'msg_date' => CurrentTime(),
												  'e_id' => $res['e_id'],
												  'itemid' => $res['ebay_id'],
												  'trec' => $rec,
												  'admin' => $this->session->userdata['ownnames'],
												  'sev' => ''));
												 
		$res['revs']++;										 
		if ($res['admin'] == '') $res['admin'] = '('.$res['revs'].') '.$this->session->userdata['ownnames'];
		else $res['admin'] = '('.$res['revs'].') '.$this->session->userdata['ownnames'].', '.$res['admin'];

		$this->db->update('ebay_transactions', array('sn' => commasep(commadesep($submitbcns)), 'mark' => 1, 'admin' => $res['admin'], 'revs' => $res['revs']), array('rec' => (int)$rec));
		$this->_logaction('TransactionView', 'B', array('Transaction BCN' => commasep(commadesep($oldtransactionbcn))), array('Transaction BCN' => commasep(commadesep($submitbcns))), $res['e_id'], $res['ebay_id'], $rec);

		$this->db->update('ebay', array('e_part' => commasep(commadesep($listingbcns)), 'e_qpart' => commasep(commadesep($listingbcncount))), array('e_id' => $res['e_id']));
		$this->_logaction('TransactionView', 'B',array('BCN' => commasep(commadesep($listingbcnsold))), array('BCN' => commasep(commadesep($listingbcns))), $res['e_id'], $res['ebay_id'], $rec);
		$this->_logaction('TransactionView', 'B',array('BCN Count' => $listingbcnsoldcount), array('BCN Count' => $listingbcncount), $res['e_id'], $res['ebay_id'], $rec);
		
		if ($listingbcncount > 1) $this->ReviseEbayDescription($res['e_id']);
	}	

	$this->session->set_flashdata('action', (int)$rec);
	
	$sortstring = $this->session->userdata['sortstring'];
	if ($sortstring != '') echo '<span style="font-size:14px; font-weight:bold; color:#0066C;"><br><br>Redirecting back in 2 seconds...</span><script type="text/JavaScript">
<!--
setTimeout("location.href = \''.Site_url().'Myebay/SortOrders/'.$sortstring.'#'.(int)$rec.'\';",4000);
-->
</script>';
	else echo '<span style="font-size:14px; font-weight:bold; color:#0066C;"><br><br>Redirecting back in 2 seconds...</span><script type="text/JavaScript">
<!--
setTimeout("location.href = \''.Site_url().'Myebay/GetOrders/#'.(int)$rec.'\';",4000);
-->
</script>';
	*/

}

}


/*function GetEbayStore($display = TRUE)
{
		if ($display)
		{
		set_time_limit(1500); 
		require_once($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
		}
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
		
		$xml = simplexml_load_string($responseXml);
		if ($display)
		{
			$this->mysmarty->assign('list', $xml->Store->CustomCategories->CustomCategory);
			$this->mysmarty->view('myebay/myebay_store.html');		
		}
		else return $xml->Store->CustomCategories->CustomCategory;
}*/

	
////////////TEST


function test()
	{
	
	exit();
	
	/*
		$this->load->helper('directory');
		$this->load->helper('file');
$list = array();
		$dir = directory_map($this->config->config['paths']['imgebay']);
		foreach ($dir as $kd => $vd)
		{
		
			if (!is_array($vd))
			{
				if ((substr($vd, -5) == '1.JPG') || (substr($vd, -5) == '1.jpg') || (substr($vd, -5) == '1.gif') || (substr($vd, -5) == '1.GIF') || (substr($vd, -5) == '1.png') || (substr($vd, -5) == '1.PNG')) $list[] = $vd;
				else { }
			}
		}		
			
			printcool ($list);
			
	*/	
			
			
		$this->db->select("e_id, idpath, e_img1, e_img2, e_img3, e_img4");	
		
		/*$factor = 2;
		$range = $factor*100;
		$do = array ($range-100, $range);
		$this->db->where('e_id >=', $do[0]);
		$this->db->where('e_id <', $do[1]);
		printcool ($factor.' | '.$do[0].' - '.$do[1]);	
		*/

		$this->db->where('e_id >=', 4000);
		$this->db->where('e_id <', 5000);
		$this->query = $this->db->get('ebay');
		$d = $this->query->result_array();

		/*foreach($d as $k => $v)
		{
		
				printcool ($v['e_id'].' - '.ceil($v['e_id'] / 100).'/');
		}
		break;*/
		foreach($d as $k => $v)
		{
		
		exit();
			//$this->db->update('ebay', array('idpath' => str_replace('/', '', idpath((int)$v['e_id']))), array('e_id' => (int)$v['e_id']));
			//printcool (str_replace('/', '', idpath((int)$v['e_id'])));
			
			$loop = array(1,2,3,4);
			foreach ($loop as $lk => $lv)
			{
				if ($v['e_img'.$lv] != '')
				{				
					$this->_CheckImageDirExist(idpath((int)$v['e_id']));				
					
					if (read_file($this->config->config['paths']['imgebay'].'/Ebay_'.$v['e_img'.$lv]))
					{
						if (!copy($this->config->config['paths']['imgebay'].'/Ebay_'.$v['e_img'.$lv], $this->config->config['paths']['imgebay'].'/'.idpath((int)$v['e_id']).'Ebay_'.$v['e_img'.$lv])) {
							echo "failed to copy ".$this->config->config['paths']['imgebay'].'/Ebay_'.$v['e_img'.$lv]."...\n";
						}
					}
					
					if (read_file($this->config->config['paths']['imgebay'].'/'.$v['e_img'.$lv]))
					{
						if (!copy($this->config->config['paths']['imgebay'].'/'.$v['e_img'.$lv], $this->config->config['paths']['imgebay'].'/'.idpath((int)$v['e_id']).$v['e_img'.$lv])) {
							echo "failed to copy ".$this->config->config['paths']['imgebay'].$v['e_img'.$lv]."...\n";
						}
					}
					
					if (read_file($this->config->config['paths']['imgebay'].'/thumb_'.$v['e_img'.$lv]))
					{
						if (!copy($this->config->config['paths']['imgebay'].'/thumb_'.$v['e_img'.$lv], $this->config->config['paths']['imgebay'].'/'.idpath((int)$v['e_id']).'thumb_'.$v['e_img'.$lv])) {
							echo "failed to copy ".$this->config->config['paths']['imgebay'].'/thumb_'.$v['e_img'.$lv]."...\n";
						}
					}
					
					if (read_file($this->config->config['paths']['imgebay'].'/thumb_main_'.$v['e_img'.$lv]))
					{
						if (!copy($this->config->config['paths']['imgebay'].'/thumb_main_'.$v['e_img'.$lv], $this->config->config['paths']['imgebay'].'/'.idpath((int)$v['e_id']).'thumb_main_'.$v['e_img'.$lv])) {
							echo "failed to copy ".$this->config->config['paths']['imgebay'].'/thumb_main_'.$v['e_img'.$lv]."...\n";
						}
					}
					
				}					
			}								
		}
	}


