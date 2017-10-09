<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


function getzero()
{
		$this->Auth_model->CheckListings();
		
		$this->db->select("e_id");		
		$this->db->order_by("e_id", "DESC");
		$this->db->where("quantity", 0);
		$this->query = $this->db->get('ebay');

		if ($this->query->num_rows() > 0)  printcool ($this->query->result_array());
		
}

function GetBCNS()
	{
		$this->Auth_model->CheckListings();
		
		$this->db->select("e_part");		
		$this->db->order_by("e_id", "DESC");
		$this->query = $this->db->get('ebay');

		if ($this->query->num_rows() > 0) 
		{			
			//	printcool ($this->query->result_array());
			foreach ($this->query->result_array() as $v)
			{

				if ($v['e_part'] != '') 
				{

					$p = explode(',', $v['e_part']);
					//printcool ($p);
					
					foreach ($p as $pp)
					{
						
						echo $pp.'<br>';	
					}
				}
			}
		}

	}
function GetBCNSBig()
	{
		$this->Auth_model->CheckListings();
		
		$this->db->select("e_part, e_id, e_title");		
		$this->db->order_by("e_id", "DESC");
		$this->query = $this->db->get('ebay');
		echo '<table><tr><th>ID</th><th>BCN</th><th>Title</th></tr>';
		if ($this->query->num_rows() > 0) 
		{			
			//	printcool ($this->query->result_array());
			foreach ($this->query->result_array() as $v)
			{
			echo '<tr>';
				if ($v['e_part'] != '') 
				{

					$p = explode(',', $v['e_part']);
					//printcool ($p);
					
					foreach ($p as $pp)
					{
						echo '<tr><td>'.$v['e_id'].'</td><td>';
						echo $pp;	
						echo '</td><td>'.$v['e_title'].'</td></tr>';
					}
				}
			}
		}
		echo '</table>';
	}


function ActionLogold($page = 1)
	{
$this->Auth_model->CheckListings();

			
		$session_search = $this->session->userdata('alast_string');
		$session_where = $this->session->userdata('alast_where');

		if (isset($_POST['search'])) $string = $this->input->post('search', TRUE);		
		elseif ($session_search) $string = $this->session->userdata('last_string');
		else $string = '';
		if (isset($_POST['where']) && $_POST['where'] < 8) $where = (int)$this->input->post('where', TRUE);		
		elseif ($session_where) $where = $this->session->userdata('last_where');
		else $where = '';
		
		//printcool ($string);
		if (!$_POST && $string == '' && $where == '') $this->session->set_userdata('page', $page);
		$this->session->set_userdata('alast_string', $string);
		$this->mysmarty->assign('string', $string);	
		$this->session->set_userdata('alast_where', $where);
		$this->mysmarty->assign('where', $where);	
		
		$this->mysmarty->assign('adm', $this->Myebay_model->GetAdminList());			
		$data = $this->Myebay_model->GetActionLog($string, $where, $page, $this->actabrv);
		if ($data['results'])
		{
			foreach ($data['results'] as $k => $v)
			{
				$time = explode('-', $v['time']);
				$data['results'][$k]['date'] = trim($time[1]);				
			}
		}	
		$this->mysmarty->assign('list', $data['results']);
		$this->mysmarty->assign('pages', $data['pages']);
		$this->mysmarty->assign('page', (int)$page);
		
		$this->mysmarty->assign('abbr', $this->actabrv);
		
		$this->mysmarty->view('myebay/myebay_actionlog.html');
	}


function FindInDrive()
{	
$this->Auth_model->CheckListings();


	$string = trim($this->input->post('gsearch'));
	if ($string == '') exit('No search');
	echo 'Testing';	
}
function SaveGS($id = 0, $page = 1)
{
	/* 
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
	}*/
	 Redirect('Myebay/ListItems/'.(int)$page);
}

function UpdateQuantityAndBCN($id = 0, $page = '')
{exit();
$this->Auth_model->CheckListings();

	$this->skipreq = true;
	$this->UpdateBCN((int)$id, (int)$page, TRUE);
	$this->UpdateQuantity((int)$id, (int)$page, TRUE);
	
	$this->_GhostPopulate((int)$id);
	
	//$this->ReviseEbayDescription((int)$id, false, false);
	//$this->EbayInventoryUpdate((int)$id, false);
	
	echo '<span style="font-size:14px; font-weight:bold; color:#0066C;"><br><br>Redirecting back in 2 seconds...</span><script type="text/JavaScript">
<!--
setTimeout("location.href = \''.Site_url().'Myebay/ListItems/'.(int)$page.'#'.(int)$id.'\';",4000);
-->
</script>';
	
	//Redirect('Myebay/ListItems/'.(int)$page.'#'.(int)$id);

}
function UpdateQuantity($id = 0, $page = '', $noredirect = FALSE)
{
$this->Auth_model->CheckListings();



if ((int)$id != 0 && $page != '' && isset($_POST['quantity']))
{
	$this->db->update('ebay', array('quantity' => (int)$_POST['quantity'], 'ebayquantity' => (int)$_POST['ebayquantity']), array('e_id' => (int)$id));
	//		$this->Myebay_model->EbayFromID((int)$id);
	$this->_logaction('UpdateQuantity', 'Q',array('quantity' => $_POST['oldquantity']), array('quantity' => $_POST['quantity']), $id, (int)$_POST['itemid'], 0);	
	
	$this->session->set_flashdata('action', (int)$id);
}

if (!$noredirect) Redirect('Myebay/ListItems/'.(int)$page.'#'.(int)$id);

}

function UpdateBCN($id = 0, $page = '', $noredirect = FALSE)
{
$this->Auth_model->CheckListings();

 
if ((int)$id != 0 && $page != '' && isset($_POST['bcn']))
{
	$this->db->update('ebay', array('e_part' => commasep(commadesep((string)$_POST['bcn'])), 'e_qpart' => $this->_RealCount(commasep(commadesep((string)$_POST['bcn'])))), array('e_id' => (int)$id));
	
	$this->_logaction('UpdateBCN', 'B',array('BCN' => commasep(commadesep($_POST['oldbcn']))), array('BCN' => commasep(commadesep((string)$_POST['bcn']))), $id, (int)$_POST['itemid'], 0);	
	$this->_logaction('UpdateBCN', 'B',array('BCN Count' => $this->_RealCount(commasep(commadesep((string)$_POST['oldbcn'])))), array('BCN Count' => $this->_RealCount(commasep(commadesep((string)$_POST['bcn'])))), (int)$id, $_POST['itemid'], 0);	
	
	$this->session->set_flashdata('action', (int)$id);
}

if (!$noredirect) Redirect('Myebay/ListItems/'.(int)$page.'#'.(int)$id);

}

function _GhostPopulate($id = 0)
	{
		$this->db->select("e_id, e_part, e_qpart, ebay_id, quantity, ngen");	
		$this->db->where("e_id", (int)$id);
		$this->db->limit(1);
		$r = $this->db->get('ebay');
		if ($r->num_rows() > 0)
		{ 
			$res = $r->row_array(); 
			$old['e_part'] = $res['e_part'];
			$old['e_qpart'] = $res['e_qpart'];
			
			if (trim($res['e_part']) == '') $res['e_qpart'] = 0;
			else $res['e_qpart'] = count(explode(',', $res['e_part']));

			//$res['quantity'] = 9;
			//printcool($res);
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
						 $snext = str_replace('G', '', trim($snext['bcn']));
					}

					$val = (int)$res['quantity']-(int)$res['e_qpart'];
					$ngen = 0;										
					$ngenarray = '';
					
					/*$bcnarray = explode(',', $res['e_part']);
					foreach ($bcnarray as $b)
					{
						if (substr(trim($b), 0, 1) == 'G') $ghost[] = trim($b);
						else $actualbcn[] = trim($b);						
					}*/
					
					if ($val > 0)
					{
						$loop = 0;
						$ngen = $val;
						
						
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
							//printcool ($wh);
							$this->db->insert('warehouse', $wh);											
						}
						//echo ('Created '.$loop.' Ghost BCNS');
						$this->session->set_flashdata('success_msg', 'Created '.$loop.' Ghost BCNS - '.ltrim(rtrim($ngenarray, ','), ','));
					}
					
					$res['e_part'] = ltrim(rtrim($res['e_part'], ','), ',');
					$ngenarray = ltrim(rtrim($ngenarray, ','), ',');
					
					//printcool ($ngenarray);
					//printcool ($res['e_part']);
					if ($ngenarray != '')
					{
						if (trim($res['e_part']) != '') $res['e_part'] = $res['e_part'].',';
						$new['e_part'] = $res['e_part'].$ngenarray;
						$new['e_qpart'] = count(explode(',', $new['e_part']));
						
						$countrealngen = explode(',', $new['e_part']);
						$res['ngen'] = array();
						foreach ($countrealngen as $b)
						{
							if (substr(trim($b), 0, 1) == 'G') $res['ngen'][] = trim($b);					
						}
						$res['ngen'] = count($res['ngen']);
						//printcool ("".$new['e_part']." [] ".$new['e_qpart']." [] ".($res['ngen']+(int)$ngen)." [] ".$res['e_id']."");
						$this->db->update('ebay', array('e_part' => $new['e_part'], 'e_qpart' => $new['e_qpart'], 'ngen' => ($res['ngen'])), array('e_id' => $res['e_id']));
							 
						 //echo 'Updated Listing BCNS';
						 $this->session->set_flashdata('success_msg', 'Updated Listing BCNS. Added '.$ngen.' Ghost BCNS');
					}
				}
				else
				{
					$bcnarray = explode(',', $res['e_part']);
					foreach ($bcnarray as $b)
					{
						if (substr(trim($b), 0, 1) == 'G') $ghost[] = trim($b);
						else $actualbcn[] = trim($b);						
					}
					//printcool ($ghost);
					//printcool ($actualbcn);
					$val = $res['e_qpart']-$res['quantity'];
					if (count($ghost) < $val) $this->session->set_flashdata('error_msg', 'More BCNs than available ghost items for removal -  ('.count($ghost).') ('.$val.')');// echo 'More BCNs than available ghost items for removal.'
					$removed = '';
					//printcool (count($ghost));
					//printcool ($pval);
					$rem = 0;
					while ($rem < $val)
					{	
						 $key = (count($ghost))-1;
						 $removed .= $ghost[$key].', ';
						 $dbr = $ghost[$key];
						 unset($ghost[$key]);
						 //printcool (trim($dbr), false, 'DEL');
//						 $this->db->update('warehouse', array('deleted' => 1), array('bcn' => trim($dbr), 'waid' => 0));
						 $rem++;
					}
					$removed = rtrim($removed, ', ');
					//echo 'Removed Ghost BCNS '.$removed; 
					$this->session->set_flashdata('success_msg', 'Removed Ghost BCNS '.$removed);
					if (count($actualbcn) > 0) $ready = implode(',', $actualbcn);
					else $ready = '';	
					if ($ready != '') $ready .= ',';	
					if (count($ghost) > 0) $ready .= implode(',', $ghost);
					$new['e_part'] = $ready;
					$new['e_qpart'] = count(explode(',', $ready));
					//printcool ("update('ebay', array('e_part' => ".$new['e_part'].", 'e_qpart' => ".$new['e_qpart'].", 'ngen' => ".count($ghost)."), array(''e_id' => ".$res['e_id']."));");
					$this->db->update('ebay', array('e_part' => $new['e_part'], 'e_qpart' => $new['e_qpart'], 'ngen' => count($ghost)), array('e_id' => $res['e_id']));
					
				}
				
				$this->db->insert('ebay_actionlog', array('atype' => 'B', 'e_id' => (int)$res['e_id'], 'ebay_id' => (int)$res['ebay_id'], 'time' => CurrentTimeR(), 'datafrom' => $old['e_part'], 'datato' => $new['e_part'], 'field' => 'e_part', 'admin' => $this->session->userdata['ownnames'], 'trans_id' => 0, 'ctrl' => 'UpdateQuantityGhostCheck')); 
				$this->db->insert('ebay_actionlog', array('atype' => 'B', 'e_id' => (int)$res['e_id'], 'ebay_id' => (int)$res['ebay_id'], 'time' => CurrentTimeR(), 'datafrom' => $old['e_qpart'], 'datato' => $new['e_qpart'], 'field' => 'e_qpart', 'admin' => $this->session->userdata['ownnames'], 'trans_id' => 0, 'ctrl' => 'UpdateQuantityGhostCheck')); 
				   
			}
		}
		
		
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
function TransactionBCNUpdate($rec = 0, $isarray = false)
{
$this->Auth_model->CheckOrders();


set_time_limit(180);
		ini_set('mysql.connect_timeout', 180);
		ini_set('max_execution_time', 180);  
		ini_set('default_socket_timeout', 180); 
// DIMITER CHANGES HERE, IMPLODING POSTED BCNS BEFORE PROCESSING CONTINUES, USING $isarray TO DEFIN 17.7.2014 -> 30.8.2014

if ((int)$rec != 0 && isset($_POST['bcn']))
{
	$this->db->select('qty, paidtime, asc, paid, fee, shipping, paidtime, itemid, ssc');
	$this->db->where('rec', (int)$rec);
	$t = $this->db->get('ebay_transactions');
	if ($t->num_rows() > 0) $tr = $t->row_array();
	else { echo 'Transaction data not found. Contact administrator'; exit(); }
	
	
	/////////////
	if ($isarray) $submitbcns = commadesep(implode(',', $_POST['bcn']));    
	else $submitbcns = commadesep(trim((string)$_POST['bcn']));                   
	$submitbcnscount = $this->_RealCount($submitbcns);			
	$submitbcns = explode(',', $submitbcns);
		
	$oldtransactionbcn = commadesep(trim((string)$_POST['oldbcn']));
	$oldtransactionbcn = explode(',', $oldtransactionbcn);
	//////////
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
						//LOG MATCHED
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
		
		$this->load->model('Mywarehouse_model'); 
		$this->Mywarehouse_model->processbcnsfromorder((int)$rec, 'ebay');
		
		$this->db->update('ebay_transactions', array('sn' => commasep(commadesep($submitbcns)), 'mark' => 1, 'admin' => $res['admin'], 'revs' => $res['revs']), array('rec' => (int)$rec));
		$this->_logaction('TransactionView', 'B', array('Transaction BCN' => commasep(commadesep($oldtransactionbcn))), array('Transaction BCN' => commasep(commadesep($submitbcns))), $res['e_id'], $res['ebay_id'], $rec);
		
		//
		
		
		$this->db->update('ebay', array('e_part' => commasep(commadesep($listingbcns)), 'e_qpart' => $listingbcncount, 'ngen' => $this->_CountGhosts(commasep(commadesep($listingbcns)))), array('e_id' => $res['e_id']));
		$this->_logaction('TransactionView', 'B',array('BCN' => commasep(commadesep($listingbcnsold))), array('BCN' => commasep(commadesep($listingbcns))), $res['e_id'], $res['ebay_id'], $rec);
		$this->_logaction('TransactionView', 'B',array('BCN Count' => $listingbcnsoldcount), array('BCN Count' => $listingbcncount), $res['e_id'], $res['ebay_id'], $rec);
			
			
			
		$search_term = commasep(commadesep($submitbcns));		
		$workdata = array('newvals' => array(
											 array('name' => 'shippingcost',
												   'value' =>  $tr['asc']
												   ), 
											 array('name' => 'pricesold', 
												   'value' => $tr['paid']
												   ),
											 array('name' => 'wheresold', 
												   'value' =>'eBay ('.(int)$rec.')'
												   ),
											 array('name' => 'datesold', 
												   'value' => $tr['paidtime']
												   )
											 ), 
						  'origin' => (int)$rec, 
						  'origin_type' => 'TransactionBCNUpdate', 
						  'admin' => $this->session->userdata['ownnames'],
						  'gdrv' =>$this->Auth_model->gDrv()
						  );
		
		/*if (trim($search_term) != '')
							{
								$this->load->library('Googledrive');
								$this->load->library('Googlesheets');
								$res = $this->googlesheets->ProcessUpdate($this->googlesheets->PopulateWorksheetInfo($this->googledrive->FindInDrive(explode(',', $search_term))), $workdata);
								if ($res) $this->session->set_flashdata('success_msg', $res); 
							}*/
		
		
		
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
	
		//echo "bcnsRecycled: " . $bcns . "\n\n\n";	  	
		//$this->db->update('ebay', array('e_part' => $bcns, 'e_qpart' => $this->_RealCount($bcns)), array('e_id' => $res['e_id']));	
		
		 //$this->_logaction('TransactionView', 'B',array('BCN' => $_POST['oldbcn']), array('BCN' => $bcns), $res['e_id'], $res['ebay_id'], $rec);
		 //$this->_logaction('TransactionView', 'B',array('BCN Count' => $this->_RealCount($_POST['oldbcn'])), array('BCN Count' => $this->_RealCount($bcns)), $res['e_id'], $res['ebay_id'], $rec);

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
	
	
	//Redirect('Myebay/GetEbayTransactions/#'.(int)$rec);

}

}

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

function OrderBCNUpdate($rec = 0, $ebid = 0, $isarray = false)
{
$this->Auth_model->CheckOrders();


	
	set_time_limit(180);
		ini_set('mysql.connect_timeout', 180);
		ini_set('max_execution_time', 180);  
		ini_set('default_socket_timeout', 180); 
		
	if ((int)$rec != 0 && $ebid != 0 && isset($_POST['bcn']))
	{
		$this->db->select('order, endprice, endprice_delivery, time');
		$this->db->where('oid', (int)$rec);
		$t = $this->db->get('orders');
		if ($t->num_rows() == 0) { echo 'Order data not found. Contact administrator'; exit(); }
		else $tr = $t->row_array();
		
		$matchproduct = false;
		$tr['order'] = unserialize($tr['order']);
		foreach ($tr['order'] as $k => $v)
		{
			if ($k == $ebid)
			{				
				$matchproduct = true;
				
					if ($isarray) $submitbcns = commadesep(implode(',', $_POST['bcn']));    
					else $submitbcns = commadesep(trim((string)$_POST['bcn']));                   
					$submitbcnscount = $this->_RealCount($submitbcns);			
					$submitbcns = explode(',', $submitbcns);
						
					$oldtransactionbcn = commadesep(trim((string)$_POST['oldbcn']));
					$oldtransactionbcn = explode(',', $oldtransactionbcn);
				
					if ($submitbcnscount == 0) $this->db->insert('admin_history', array ('msg_type' => 1, 
																  'msg_title' => '<span style="color:blue;">Order Submitted BCN Value</span> <span style="color:red;">IS EMPTY</span>', 
																  'msg_body' => '', 
																  'msg_date' => CurrentTime(),
																  'e_id' => 0,
																  'itemid' => 0,
																  'trec' => $rec,
																  'opr' => $ebid,
																  'admin' => $this->session->userdata['ownnames'],
																  'sev' => 1												  
																  ));
					
					if ($submitbcnscount != $v['quantity']) $this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => '<span style="color:blue;">Order Submitted BCN Value</span> <span style="color:red;">DOES NOT MATCH QUANTITY ('.$submitbcnscount.'/'.$v['quantity'].')</span> on record.', 'msg_body' => '', 'msg_date' => CurrentTime(),
																  'e_id' => 0,
																  'itemid' => 0,
																  'trec' => $rec,
																  'opr' => $ebid,
																  'admin' => $this->session->userdata['ownnames'],
																  'sev' => 1));
				
					$this->db->select('e_id, e_part, ebay_id');
					$this->db->where('e_id', (int)$ebid);
					$q = $this->db->get('ebay');
					if ($q->num_rows() == 0) 
					{
						$this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => '<span style="color:blue;">Order BCN Update</span> found <span style="color:red;">no ItemID match</span> in eBay listings.', 'msg_body' => '', 'msg_date' => CurrentTime(),
																  'e_id' => 0,
																  'itemid' => 0,
																  'trec' => $rec,
																  'opr' => $ebid,
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
							foreach ($listingbcns as $lk => $lv)
							{
								foreach ($submitbcns as $rk => $rv)
								{
									if (trim($rv) == trim($lv)) 
									{
										$matched[] = trim($listingbcns[$lk]);
										unset($listingbcns[$lk]);
									}
									
								}
							}
							
								
						}
						
						if ($listingbcnsoldcount == 0 || (count($matched) == 0))
						{
							$this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => '<span style="color:blue;">Order BCN Update</span> found <span style="color:red;">no available BCNs</span> in listings.', 'msg_body' => '', 'msg_date' => CurrentTime(),
																  'e_id' => $res['e_id'],
																  'itemid' => $res['ebay_id'],
																  'trec' => $rec,
																  'opr' => $ebid,
																  'admin' => $this->session->userdata['ownnames'],
																  'sev' => 1));			
						}	
						
						if ($submitbcnscount > 0)
						{
							foreach ($oldtransactionbcn as $ok => $ov)
							{
								foreach ($submitbcns as $rk => $rv)
								{
									if (trim($rv) == trim($ov)) unset($oldtransactionbcn[$ok]);
								}
							}
							
							if (count($oldtransactionbcn) > 0)
								{
									foreach ($oldtransactionbcn as $otv)
										{
											$listingbcns[] = $otv;
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
						
						$this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => '<span style="color:blue;">Order BCN Updated</span> to <span style="color:#FF9900;">"'.$submitbcns.'"</span> (Matched: <span style="color:#FF9900;">'.$matched.'</span>) [Old value: <span style="color:#FF9900;">'.$oldtransactionbcn.'</span>]', 'msg_body' => '', 'msg_date' => CurrentTime(),
																  'e_id' => $res['e_id'],
																  'itemid' => $res['ebay_id'],
																  'trec' => $rec,
																  'opr' => $ebid,
																  'admin' => $this->session->userdata['ownnames'],
																  'sev' => ''));									 
						$v['revs']++;										 
						if ($v['admin'] == '') $v['admin'] = '('.$v['revs'].') '.$this->session->userdata['ownnames'];
						else $v['admin'] = '('.$v['revs'].') '.$this->session->userdata['ownnames'].', '.$v['admin'];
				
				
				/////////////////////////////
				/////////////////////////////
				/////////////////////////////
				/////////////////////////////
				/////////////////////////////
				/////////////////////////////
				
				$tr['order'][$k]['sn'] = commasep(commadesep($submitbcns));				
				$tr['order'][$k]['mark'] = 1;
				$tr['order'][$k]['admin'] = $v['admin'];
				$tr['order'][$k]['revs'] = $v['revs'];
				 
				$tr['order'] = serialize($tr['order']);
				$this->db->update('orders', array('order' => $tr['order'], 'mark' => 1), array('oid' => (int)$rec));
						
				$this->load->model('Mywarehouse_model'); 
				$this->Mywarehouse_model->processbcnsfromorder((int)$rec, 'website');
				
				
						$this->_logaction('TransactionView', 'B', array('Order BCN' => commasep(commadesep($oldtransactionbcn))), array('Order BCN' => commasep(commadesep($submitbcns))), $res['e_id'], $res['ebay_id'], $rec, $k);
				
				
				
				
				$this->db->update('ebay', array('e_part' => commasep(commadesep($listingbcns)), 'e_qpart' => $listingbcncount, 'ngen' => $this->_CountGhosts(commasep(commadesep($listingbcns)))), array('e_id' => $res['e_id']));
						
						//function _logaction($location = '', $type = 'M', $datafrom = '', $datato = '', $eid = '', $itemid = '', $transid = '', $key = '')					
						
						
						$this->_logaction('TransactionView', 'B', array('BCN' => commasep(commadesep($listingbcnsold))), array('BCN' => commasep(commadesep($listingbcns))), $res['e_id'], $res['ebay_id'], $rec, $k);
						$this->_logaction('TransactionView', 'B', array('BCN Count' => $listingbcnsoldcount), array('BCN Count' => $listingbcncount), $res['e_id'], $res['ebay_id'], $rec, $k);
						
						
						
						
				if ($listingbcncount > 1) $this->ReviseEbayDescription($res['e_id']);
				else $this->_EndeBayListing($res['ebay_id'], $res['e_id'], (int)$rec);
				
							$search_term = commasep(commadesep($submitbcns));		
							$workdata = array('newvals' => array(
													 array('name' => 'shippingcost',
														   'value' =>  $tr['endprice_delivery']
														   ), 
													 array('name' => 'pricesold', 
														   'value' => $tr['endprice']
														   ),
													 array('name' => 'wheresold', 
														   'value' =>'LaTronics ('.(int)$rec.')'
														   ),
													 array('name' => 'datesold', 
														   'value' => $tr['time']
														   )
													 ), 
								  'origin' => (int)$rec, 
								  'origin_type' => 'OrderBCNUpdate', 
								  'admin' => $this->session->userdata['ownnames'],
								  'gdrv' =>$this->Auth_model->gDrv()
								  );
				
				/*if (trim($search_term) != '')
							{
								$this->load->library('Googledrive');
								$this->load->library('Googlesheets');
								$res = $this->googlesheets->ProcessUpdate($this->googlesheets->PopulateWorksheetInfo($this->googledrive->FindInDrive(explode(',', $search_term))), $workdata);
								if ($res) $this->session->set_flashdata('success_msg', $res); 
							}*/
							
				}	
				/////////////////////////////
				/////////////////////////////
				/////////////////////////////
				/////////////////////////////
				/////////////////////////////
				/////////////////////////////
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
					
					
					//Redirect('Myebay/GetEbayTransactions/#'.(int)$rec);
				
				
				
				
				
				
				
				
				
				
				
				
				
				
	
		}
		if ($matchproduct == false)  { echo 'Order data not found. Contact administrator'; exit(); }
	
	
	
	
	
	
	
		
	
	}
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



function TransactionBCNUpdateOldVersion($rec = 0, $isarray = false)
{
exit();
// DIMITER CHANGES HERE, IMPLODING POSTED BCNS BEFORE PROCESSING CONTINUES, USING $isarray TO DEFIN 17.7.2014
	
if ((int)$rec != 0 && isset($_POST['bcn']))
{
	$this->db->select('qty');
	$this->db->where('rec', (int)$rec);
	$t = $this->db->get('ebay as e, ebay_transactions as t');
	if ($t->num_rows() > 0) $tr = $t->row_array();
	else { echo 'Transaction data not found. Contact administrator'; exit(); }
	
	$oldbcn = (string)$_POST['oldbcn'];                     /// <---
	if ($isarray) $remove = implode(',', $_POST['bcn']);    /// <---
	else $remove = (string)$_POST['bcn'];                   /// <---
	$removecount = $this->_RealCount($remove);
	
	if ($removecount == 0) $this->db->insert('admin_history', array ('msg_type' => 1, 
												  'msg_title' => '<span style="color:blue;">Transaction Submitted BCN Value</span> <span style="color:red;">IS EMPTY</span>', 
												  'msg_body' => '', 
												  'msg_date' => CurrentTime(),
												  'e_id' => 0,
												  'itemid' => 0,
												  'trec' => $rec,
												  'admin' => $this->session->userdata['ownnames'],
												  'sev' => 1												  
												  ));
	
	if ($removecount != $tr['qty']) $this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => '<span style="color:blue;">Transaction Submitted BCN Value</span> <span style="color:red;">DOES NOT MATCH QUANTITY ('.$removecount.'/'.$tr['qty'].')</span> on record.', 'msg_body' => '', 'msg_date' => CurrentTime(),
												  'e_id' => 0,
												  'itemid' => 0,
												  'trec' => $rec,
												  'admin' => $this->session->userdata['ownnames'],
												  'sev' => 1));

	$this->db->select('e.e_id, e.e_part, e.ebay_id');
	$this->db->where('t.itemid = e.ebay_id');
	$this->db->where('t.rec', (int)$rec);
	$q = $this->db->get('ebay as e, ebay_transactions as t');
	if ($q->num_rows() == 0) $this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => '<span style="color:blue;">Transaction BCN Update</span> found <span style="color:red;">no ItemID match</span> in eBay listings.', 'msg_body' => '', 'msg_date' => CurrentTime(),
												  'e_id' => 0,
												  'itemid' => 0,
												  'trec' => $rec,
												  'admin' => $this->session->userdata['ownnames'],
												  'sev' => 1));
		
	else
	{	
		$res = $q->row_array();
		 
		$remove = explode(',', rtrim($remove,','));
		$bcns = rtrim($res['e_part'],',');
		$bcncount = $this->_RealCount($bcns);			
		$bcns = explode(',', $bcns);
		$matched = array();				
		if ($bcncount > 0)
		{
			foreach ($bcns as $k => $v)
			{
				foreach ($remove as $rk => $rv)
				{
					if (trim($rv) == trim($v))
					{
						unset($bcns[$k]);
						$matched[$rk] = trim($rv);
					}
				}
			}		
		$matched = implode(',', $matched);		
		}
		else 
		{
			$this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => '<span style="color:blue;">Transaction BCN Update</span> found <span style="color:red;">no available BCNs</span> in listings.', 'msg_body' => '', 'msg_date' => CurrentTime(),
												  'e_id' => $res['e_id'],
												  'itemid' => $res['ebay_id'],
												  'trec' => $rec,
												  'admin' => $this->session->userdata['ownnames'],
												  'sev' => 1));						
			$matched = '<span style="color:red">NONE</span>';			
		}		
		$remove = implode(',', $remove);
		$bcns = implode(',', $bcns);	
		if ($matched == '') $matched = '<span style="color:red">NONE</span>';
		$this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => '<span style="color:blue;">Transaction BCN Updated</span> to <span style="color:#FF9900;">"'.$remove.'"</span> (Matched: <span style="color:#FF9900;">'.$matched.'</span>)', 'msg_body' => '', 'msg_date' => CurrentTime(),
												  'e_id' => $res['e_id'],
												  'itemid' => $res['ebay_id'],
												  'trec' => $rec,
												  'admin' => $this->session->userdata['ownnames'],
												  'sev' => ''));
		/* put the old bcn back into the available bucket */	

		/** 
		 * beautify the BCN list
		 * add the old bcn back to the available list 
		 ***/	
		if($bcns != ''){
			$bcns = explode(",", $bcns);
			array_push($bcns, $_POST['oldbcn']);
			$bcns = array_filter(array_map('trim', $bcns));
			//$bcns = array_diff($bcns, array(trim($_POST['oldbcn'], " ")));
			$bcns = array_unique($bcns);
			sort($bcns);
			$bcns = implode(", ", $bcns);
			$bcns = $bcns . ", ";
		}else{
			$bcns = $_POST['oldbcn'];
		}
	
		//echo "bcnsRecycled: " . $bcns . "\n\n\n";	  	
		$this->db->update('ebay', array('e_part' => $bcns, 'e_qpart' => $this->_RealCount($bcns)), array('e_id' => $res['e_id']));	
		
		 $this->_logaction('TransactionView', 'B',array('BCN' => $_POST['oldbcn']), array('BCN' => $bcns), $res['e_id'], $res['ebay_id'], $rec);
		 $this->_logaction('TransactionView', 'B',array('BCN Count' => $this->_RealCount($_POST['oldbcn'])), array('BCN Count' => $this->_RealCount($bcns)), $res['e_id'], $res['ebay_id'], $rec);

		$this->ReviseEbayDescription($res['e_id'], false, false);
	}	

	

	
	//$updateBcn = 
	
	$this->db->update('ebay_transactions', array('sn' => $remove, 'mark' => 1), array('rec' => (int)$rec));
	$this->_logaction('TransactionView', 'B',array('Transaction BCN' => $oldbcn), array('Transaction BCN' => $remove), $res['e_id'], $res['ebay_id'], $rec);
	$this->session->set_flashdata('action', (int)$rec);
}

//Redirect('Myebay/GetEbayTransactions/#'.(int)$rec);

	echo '<span style="font-size:14px; font-weight:bold; color:#0066C;"><br><br>Redirecting back in 2 seconds...</span><script type="text/JavaScript">
<!--
setTimeout("location.href = \''.Site_url().'Myebay/GetOrders/#'.(int)$rec.'\';",4000);
-->
</script>';
}

function _RealCount($array)
{

	if ($array != '') return count(explode(',', $array));
	else return 0;
}

function updateghostbcn($id = '',$otype ='')
{
	//echo  'Under Dev';
	//exit();
	if ((int)$otype != 2) $otype = 1;
	$val = trim($this->input->post('val', true));
	$oval = trim($this->input->post('oval', true));
	$eid = (int)$this->input->post('eid', true);
	if ((int)$otype != 2)
	{
		$this->db->select('sn');
		$this->db->where('rec', (int)$id);
		$q = $this->db->get('ebay_transactions');
		if ($q->num_rows() > 0) 
		{
			$res = $q->row_array();
			$bcn = trim($res['sn']);
			$oldbcn = $bcn;
			$bcn = explode(',', $bcn);
			if (count($bcn) > 0)
			{
				$change = 0;
				foreach ($bcn as $k => $b)
				{
					$bcn[$k] = trim($b);
					if (trim($b) == trim($oval)) { $bcn[$k] = trim($val); $change++; }
				}
				
				if ($change > 0)
				{		
				//printcool (implode(', ', $bcn));
				//printcool ((int)$id);			
					$this->db->update('ebay_transactions', array('sn' => implode(', ', $bcn)), array('rec' => (int)$id));
					$this->db->update('warehouse', array('bcn' => trim($val)), array('bcn' => trim($oval)));
					$this->_logaction('eBayOrderBCNSwap', 'B', array('BCN' => $oldbcn), array('BCN' => implode(', ', $bcn)), 0, 0, (int)$id);
					$this->load->model('Auth_model');
					$this->Auth_model->wlog(trim($oval), 0, 'bcn', trim($oval), $val);		
					echo ($val);
				}
				else echo ($oval);
			}
		}
		else echo ($oval);
	}
	else
	{
		$this->db->select('order');
		$this->db->where('oid', (int)$id);
		$t = $this->db->get('orders');
		if ($t->num_rows() == 0) { echo 'Error'; exit(); }
		else $tr = $t->row_array();
		
		$tr['order'] = unserialize($tr['order']);
		foreach ($tr['order'] as $k => $v)
		{
			if ($k == $eid)
			{				
				$oldbcn = $bcn = trim($v['sn']);
					$bcn = explode(',', $bcn);
					if (count($bcn) > 0)
					{
						$change = 0;
						foreach ($bcn as $kb => $b)
						{
							$bcn[$kb] = trim($b);
							if (trim($b) == trim($oval)) 
							{ 
								$bcn[$kb] = trim($val); $change++; 
							}
							$newbcn = $tr['order'][$eid]['sn'] = implode(', ', $bcn);
						}
						
						if ($change > 0)
						{	
						//printcool ($tr['order']);	
						//printcool ((int)$id);			
							$this->db->update('orders', array('order' => serialize($tr['order'])), array('oid' => (int)$id));
							$this->db->update('warehouse', array('bcn' => trim($val)), array('bcn' => trim($oval)));
							$this->_logaction('WebsiteOrderBCNSwap', 'B', array('BCN' => $oldbcn), array('BCN' => $newbcn), 0, 0, (int)$id, $k);
							$this->load->model('Auth_model');
							$this->Auth_model->wlog(trim($oval), 0, 'bcn', trim($oval), $val);
							echo ($val);
						}
						else echo ($oval);
					}				
			}
		}
		if ($change == 0) echo ($oval);
	}
	
}
function SerialUpdate()
{
$this->Auth_model->CheckListings();


	if ($this->input->post('serial') != '')
	{
		$ser = 	$ser = $this->input->post('serial', TRUE);
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
									$ser[$ks][] = $st[0].$tmp[0].$tmpA[0];								
									$tmp[0]++;	
								}
						$ser[$ks] = implode(', ', $ser[$ks]);
					}
					else
					{ 
						if (is_array($st)) $ser[$ks] = implode('_', $st);
						else $ser[$ks] = $st; 
					}
				}			
			}
		}
		$ser = implode(', ', $ser);
		//if (substr($str, -2) == ', ') echo substr($str, 0, -2);
		echo ((string)$ser);	
		
		/*
			foreach ($st as $stt)
			{
			 $se = explode('-', $stt[1]);
			 printcool ($se);
			 if (is_array($se))
				{
					if ($se[0] == $s)
					{
						$str .= $se[0].', ';
					}
					else
					{
					//printcool ($se);
						foreach ($se as $ks => $ss)
						{	
							$sstr = preg_replace("/[^a-zA-Z]/", "", $ss);		
							$ss = preg_replace("/[^0-9]/", "", $ss); //printcool ($ss); printcool ($s[$ks]);
							
							if (isset($se[$ks+1]) && ($ss < preg_replace("/[^0-9]/", "", str_replace(' ','', $se[$ks+1])))) 
							{
								while ($ss <= preg_replace("/[^0-9]/", "", str_replace(' ','', $se[$ks+1])))
								{
									$str .= $sstr.$ss.', ';								
									$ss++;	
								}	
							}						
						}
					}
				}
				}*/		
	}
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
					$tmpA[0] =  ereg_replace("[^A-Za-z]", "", $tmp[0]);
					$tmpA[1] =  ereg_replace("[^A-Za-z]", "", $tmp[1]);
					$tmp[0] =  ereg_replace("[^0-9]", "", $tmp[0]);
					$tmp[1] =  ereg_replace("[^0-9]", "", $tmp[1]);
					if (isset($tmp[0]) && isset($tmp[1]) && is_numeric($tmp[0]) && is_numeric($tmp[1]) && $tmp[1] > $tmp[0]) 
					{
						$ser[$ks] = array();
						while ($tmp[0] <= $tmp[1])
								{
									$ser[$ks][] = $st[0].$tmp[0].$tmpA[0];								
									$tmp[0]++;	
								}
						$ser[$ks] = implode(', ', $ser[$ks]);
					}
					else
					{ 
						if (is_array($st)) $ser[$ks] = implode('_', $st);
						else $ser[$ks] = $st; 
					}
				}			
			}
		}
		$ser = implode(', ', $ser);
		return (string)$ser;
	}		
}
	
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



function BCNFromEbay($id, $page = 1, $save = false)
{
$this->Auth_model->CheckListings();


		$this->db->select("ebay_id, e_part, e_qpart");
		$this->db->where('e_id', (int)$id);	;
		$q = $this->db->get('ebay');
		if ($q->num_rows() > 0) 
		{
		
		$r = $q->row_array();
		if ((int)$r['ebay_id'] == 0)
		{
			echo 'No Item ID';
			exit();
		}
		require_once($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
		$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
		$requestXmlBody .= '<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
		$requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';
		$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
		$requestXmlBody .= "<Version>$compatabilityLevel</Version>";						
		$requestXmlBody .= '<ItemID>'.(int)$r['ebay_id'].'</ItemID></GetItemRequest>';
		$verb = 'GetItem';
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
		$responseXml = $session->sendHttpRequest($requestXmlBody);
		if(stristr($responseXml, 'HTTP 404') || $responseXml == '') die('<P>Error sending request');
		$xml = simplexml_load_string($responseXml);
		if ((string)$xml->Item->ItemID == '') { echo 'ERROR: Invalid Item ID...<a href="javascript:history.back()">Back</a>'; exit();}
		
		
		$txtdescrp = str_replace('<br clear="all" />', '', str_replace('<br>', '', str_replace('<br />', '', (string)$xml->Item->Description)));
		
		$bcn = explode('<span class="ebay_bold">BCN:</span>', $txtdescrp);	
		$bcn = explode('</div>', $bcn[1]);
		
		
		
		
		/*
		if (!isset($bcn[1])) 
		{
			
				if($id == 14033) printcool ($bcn);
			//$bcn = explode('<span class="ebay_bold">BCN:</span><br>', (string)$xml->Item->Description);	
			
			
				
						/*if (isset($bcn[1])) 
							{printcool ($bcn);
								$bcn = explode('<br clear="all">', $bcn[0]);
								if (is_array($bcn))
								{
									printcool ($bcn);
									
									
								
								}
							}
						else 
						{
								$hmsg = array ('msg_title' => 'BCN PARSE FAIL', 'msg_body' => $bcn, 'msg_date' => CurrentTime());
								GoMail($hmsg, '365@1websolutions.net', $this->config->config['no_reply_email']);	
						}*/
		//}
		//else $bcn = explode('<br clear="all" />', $bcn[1]);
		//*/
		$bcn = trim(commasep(commadesep($bcn[0])));
		$bcnt = explode(',', $bcn);
		if (count ($bcnt) > 1)
		{

			foreach ($bcnt as $v)
			{
					$bcnstr[] = trim(ltrim(rtrim($v)));				
			}
			
			if (count($bcnstr) > 0) $bcn = rtrim(implode(', ', $bcnstr));
		}
				//if($id == 14033) printcool ($bcn);
						if(!$save) echo '
						<table cellpadding="2" cellspacing="2" border="0">
						<tr><td>Local BCN:</td><td>'.$r['e_part'].'</td></tr>
						<tr><td>Local BCN Count:</td><td>'.$r['e_qpart'].'</td></tr>
						<tr><td>eBay BCN:</td><td>'.$bcn.'</td></tr>
						<tr><td>eBay BCN Count:</td><td>'.$this->_RealCount($bcn).'</td></tr>						
						</table>';
						
	if(!$save) echo '<br><br><span style="color:red;">IS THIS CORRECT ?</span><br><form method="post" action="'.Site_url().'Myebay/BCNFromEbay/'.(int)$id.'/'.(int)$page.'/TRUE"><input type="submit" value="YES" />&nbsp;&nbsp;<a href="'.Site_url().'Myebay/ListItems/'.(int)$page.'/#'.(int)$id.'">NO</a></form>';
	else {
			
			$data = array('e_part' => $bcn, 'e_qpart' => $this->_RealCount($bcn));
										
			$this->db->update('ebay', $data, array('e_id' => (int)$id));			
										
			$hmsg = array ('msg_title' => 'Item BCNs Updated from eBay @ AdminID '.(int)$id, 'msg_body' => '', 'msg_date' => CurrentTime(),
												  'e_id' => (int)$id,
												  'itemid' => $r['ebay_id'],
												  'trec' => 0,
												  'admin' => $this->session->userdata['ownnames'],
												  'sev' => '');
						
			$this->_logaction('UpdateBCN', 'B',array('BCN' => commasep(commadesep($r['e_part']))), array('BCN' => commasep(commadesep($bcn))), $id, (int)$r['ebay_id'], 0);	
			$this->_logaction('UpdateBCN', 'B',array('BCN Count' => $this->_RealCount(commasep(commadesep($r['e_qpart'])))), array('BCN Count' => $this->_RealCount(commasep(commadesep($bcn)))), (int)$id, (int)$r['ebay_id'], 0);
						
						
					$this->db->insert('admin_history', $hmsg); 
					
					$this->session->set_flashdata('success_msg', 'Item BCNs for '.(int)$id.' taken from eBay');
					$this->session->set_flashdata('action', (int)$id);
					
					Redirect ('Myebay#'.(int)$id);
				}
		
		
	}

}

function TestCategories()
{

exit();
/*$catid = '16145';

		$this->db->select('catID, catName, parentID, CategoryLevel');
		$this->db->where('catID', (int)$catid);
		$q = $this->db->get('ebaydata_categories');
		if ($q->num_rows() > 0) 
		{
			$c = $q->row_array();	
			$string = $c['catName'];
			
			while ($c['CategoryLevel'] > 1)
			{
				$this->db->select('catID, catName, parentID, CategoryLevel');			
				$this->db->where('catID', (int)$c['parentID']);
				$q = $this->db->get('ebaydata_categories');
				if ($q->num_rows() > 0) 
					{
					$c = $q->row_array();
					$string = $c['catName'].' / '.$string;
					}			
			}
		}
		
		echo $string;
		*/
		
		$this->db->select("e_id, primaryCategory, pCTitle");		
		//$this->db->limit(1000);
		$this->db->where('primaryCategory > ' , 0);
		//$this->db->where('e_id > ' , 6000);
		$this->db->order_by("e_id", "DESC");
		$this->query = $this->db->get('ebay');
		$count = 0;
		if ($this->query->num_rows() > 0)
		{
			foreach ($this->query->result_array() as $r)
			{
				if (strlen($r['pCTitle']) < 61)
				{
					$count++;
					printcool ($r);
					//printcool ($this->Myebay_model->GetEbayCategoryTitle($r['primaryCategory']));
					//$this->db->update('ebay', array('pCTitle' => $this->Myebay_model->GetEbayCategoryTitle($r['primaryCategory'])), array('e_id' => (int)$r['e_id']));
				 }
				}
		 
		 }
		 echo $count;
}

function testorderdata()
{

$d= 'a:1:{i:14346;a:12:{s:8:"quantity";i:1;s:7:"e_title";s:72:"Acer Aspire 3000 3500 5000 CPU Cooling Fan + Heatsink 36ZL5TATN01 Tested";s:5:"e_sef";s:71:"Acer-Aspire-3000-3500-5000-CPU-Cooling-Fan--Heatsink-36ZL5TATN01-Tested";s:6:"e_img1";s:83:"14346_Acer-Aspire-3000-3500-5000-CPU-Cooling-Fan--Heatsink-36ZL5TATN01-Tested_1.JPG";s:6:"idpath";s:3:"144";s:2:"sn";s:0:"";s:4:"revs";i:0;s:5:"admin";s:0:"";s:13:"buyItNowPrice";d:500;s:8:"shipping";a:4:{s:7:"exclude";s:3:"Yes";s:15:"locationexclude";s:7:"Africa,";s:8:"domestic";a:4:{i:1;a:4:{s:19:"ShippingServiceCost";s:1:"0";s:29:"ShippingServiceAdditionalCost";s:1:"0";s:12:"FreeShipping";s:2:"on";s:15:"ShippingService";s:14:"USPSFirstClass";}i:2;a:3:{s:19:"ShippingServiceCost";s:4:"4.95";s:29:"ShippingServiceAdditionalCost";s:1:"0";s:15:"ShippingService";s:12:"USPSPriority";}i:3;a:3:{s:19:"ShippingServiceCost";s:2:"21";s:29:"ShippingServiceAdditionalCost";s:1:"0";s:15:"ShippingService";s:15:"USPSExpressMail";}i:4;a:3:{s:19:"ShippingServiceCost";s:0:"";s:29:"ShippingServiceAdditionalCost";s:0:"";s:15:"ShippingService";s:0:"";}}s:13:"international";a:4:{i:1;a:4:{s:19:"ShippingServiceCost";s:2:"10";s:29:"ShippingServiceAdditionalCost";s:1:"0";s:14:"ShipToLocation";s:9:"Worldwide";s:15:"ShippingService";s:31:"USPSFirstClassMailInternational";}i:2;a:4:{s:19:"ShippingServiceCost";s:2:"25";s:29:"ShippingServiceAdditionalCost";s:1:"0";s:14:"ShipToLocation";s:9:"Worldwide";s:15:"ShippingService";s:45:"USPSPriorityMailInternationalFlatRateEnvelope";}i:3;a:4:{s:19:"ShippingServiceCost";s:2:"22";s:29:"ShippingServiceAdditionalCost";s:1:"0";s:14:"ShipToLocation";s:2:"CA";s:15:"ShippingService";s:45:"USPSPriorityMailInternationalFlatRateEnvelope";}i:4;a:4:{s:19:"ShippingServiceCost";s:0:"";s:29:"ShippingServiceAdditionalCost";s:0:"";s:14:"ShipToLocation";s:0:"";s:15:"ShippingService";s:0:"";}}}s:18:"totalweight_custom";i:0;s:5:"total";d:500;}}';

printcool (unserialize($d));
}

function testkeys()

{
	require_once($this->config->config['ebaypath'].'get-common/keys.php');
}