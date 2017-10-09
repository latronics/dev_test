<?php 

function test1()
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
		$this->db->update('ebay_transactions', array('sn' => $submitbcns), array('rec' => (int)$rec));
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
	
}
function TransactionBCNUpdate($rec = 0, $isarray = false)
{

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

		$this->ReviseEbayDescription($res['e_id'], false);
	}	

	

	
	//$updateBcn = 
	
	$this->db->update('ebay_transactions', array('sn' => $remove), array('rec' => (int)$rec));
	$this->_logaction('TransactionView', 'B',array('Transaction BCN' => $oldbcn), array('Transaction BCN' => $remove), $res['e_id'], $res['ebay_id'], $rec);
	$this->session->set_flashdata('action', (int)$rec);
}

Redirect('Myebay/GetEbayTransactions/#'.(int)$rec);

}


