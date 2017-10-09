<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Myseller_model extends Model 
{
    function Myseller_model()
    {
        parent::Model();
		
		
    }
function sellingfields()
{
	return 'cost, paid,paid_date, shipped, shipped_actual, shipped_inbound, sellingfee, netprofit, paypal_fee, vended, channel';	
}
function assignchannels()
{
	$this->sellerchannels = array(0 => 'Pool', 1 => 'eBay', 2 => 'WebSite', 3 => 'Amazon',  4 => 'Warehouse');
	$this->mysmarty->assign('base_channels', $this->sellerchannels);			
}
function assignstatuses()
{
		$this->sellerstatuses = array(
								'accountingstring' => '"Not Listed","Listed","Mismatch", "On Hold","Sold","Returned", "FBA"',
								'accountingarray' => array("Not Listed","Listed","Mismatch","On Hold","Sold","Returned","FBA"),
								'listingarray' => array("Not Listed","Listed"),
								'listing2array' => array("Not Tested","Repairing","Parts Needed","To be Parted","Needs Loading","Parted","TechParted","Ready to Sell","Scrap","Not Listed","Listed","Mismatch","FBA"),
								'returnarray' => array("Not Tested","Not Listed","Scrap","Listed", "Ready to Sell","Decision Needed"),
								'testingstring' => '"Not Tested","Repairing","Parts Needed","To be Parted","Needs Loading","Parted","TechParted","Ready to Sell","Scrap"',
								'allstring' => '"Not Tested","Repairing","Parts Needed","To be Parted","Needs Loading","Parted","TechParted","Ready to Sell","Scrap","Not Listed","Listed","On Hold","Sold","Returned","Mismatch","FBA"',
								'allarray' => array("Not Tested","Repairing","Parts Needed","To be Parted","Needs Loading","Parted","TechParted","Ready to Sell","Scrap","Not Listed","Listed","On Hold","Sold","Returned","Mismatch","FBA")								
								);
		$this->mysmarty->assign('sellerstatuses', $this->sellerstatuses);
		return $this->sellerstatuses;	
}
function getListingBcns($id = 0)
	{
		$this->db->select('wid, oldbcn, bcn, title, status, status_notes, generic, regen, history, waid');
		$this->db->where('deleted', 0);
		$this->db->where('nr', 0);
		$this->db->where('listingid', (int)$id);
		$q = $this->db->get('warehouse');
		if ($q->num_rows() > 0)
		{
			return $q->result_array();			
		}		
	}
function searchforbcns($array)
	{
		$this->db->select('wid, oldbcn, bcn, title, status, status_notes, generic, regen, history, waid, listingid');
		$this->db->where('deleted', 0);
		$this->db->where('nr', 0);
		$c = 1;
		foreach ($array as $a)
		{
			if ($c == 1) $this->db->where('bcn', trim($a));
			else $this->db->or_where('bcn', trim($a));
			$c++;
		}
		$q = $this->db->get('warehouse');
		if ($q->num_rows() > 0)
		{
			return $q->result_array();			
		}	
		
	}
function getBase($idarray, $return = false)
{
	$sql = 'SELECT wid, oldbcn, bcn, title, cost, status, status_notes, location, audit, generic, regen, history, waid, channel, listingid FROM warehouse WHERE `deleted` = 0 AND `nr` = 0 AND `vended` = 0 AND (';
		$c = 1;
		foreach ($idarray as $a)
		{
			if ($c == 1) $sql .= '`listingid` = '.(int)$a;
			else $sql .= ' OR `listingid` = '.(int)$a;
			$c++;
		}
		$sql .= ') ORDER BY FIELD (status, "Listed","Not Listed","Not Tested","Mismatch","Repairing","Parts Needed","To be Parted","Needs Loading","Parted","TechParted","Ready to Sell","FBA","Scrap"), generic ASC, bcn ASC';
		$q =  $this->db->query($sql);
		$active = array();
		$bcns = 0;

		if ($q->num_rows() > 0)
		{
			foreach ($q->result_array() as $b)
			{//printcool ($b);
				if ($return)
				{
					$r[$b['wid']] = $b;	
				}
				else
				{
				$bl[$b['listingid']][$b['wid']] = $b;		
					//if ($b['status'] == 'Listed' || $b['status'] == 'Mismatch')	
					if ($b['status'] == 'Listed')					
					{
					if (isset($active[$b['listingid']])) $active[$b['listingid']]++;
					else $active[$b['listingid']] = 1;
					}
				$waid[$b['waid']] = TRUE;
				if (isset($cn[$b['listingid']][$b['channel']])) $cn[$b['listingid']][$b['channel']]++;
				else $cn[$b['listingid']][$b['channel']] = 1;
				
				if ($b['waid'] == 0)
				{
					if (isset($g[$b['listingid']])) $g[$b['listingid']]++;
					else $g[$b['listingid']] = 1;
				}
				$bcns++;
				}
			}
			
			if ($return) { return $r; exit(); }
			
			$this->mysmarty->assign('base_bcns', $bl);
			$this->mysmarty->assign('active_bcns', $active);
			if ($b['waid'] == 0) $this->mysmarty->assign('base_ghostcount', $g);
			$this->mysmarty->assign('base_count_channels', $cn);
			
			$this->db->select('waid, wtitle');
			$c = 1;
			foreach ($waid as $k => $a)
			{

				if ($c == 1) $this->db->where('waid', (int)$k);
				else $this->db->or_where('waid', (int)$k);
				$c++;

			}
			$ac = $this->db->get('warehouse_auctions');
			if ($ac->num_rows() > 0)
			{
				foreach ($ac->result_array() as $acv)
				{
					$al[$acv['waid']] = $acv['wtitle'];						
				}
				$this->mysmarty->assign('base_auctions', $al);
				$this->mysmarty->assign('base_count_bcn', $bcns);
				$this->assignchannels();		
			}
		}	
		
		
	
}

function getOnHold($idarray, $return = false)
{
	$sql = 'SELECT wid, oldbcn, bcn, title, status, status_notes, location, audit, generic, regen, history, waid, channel, listingid, sold_id, trans_date, trans_mk FROM warehouse WHERE `deleted` = 0 AND `nr` = 0 AND `status` = "On Hold" AND (';
		$c = 1;
		foreach ($idarray as $a)
		{
			if ($c == 1) $sql .= '`listingid` = '.(int)$a;
			else $sql .= ' OR `listingid` = '.(int)$a;
			$c++;
		}
		$sql .= ') ORDER BY waid DESC, generic ASC, bcn ASC';
		$q =  $this->db->query($sql);
		if ($q->num_rows() > 0)
		{
			foreach ($q->result_array() as $b)
			{
				if ($return)
				{
					$r[$b['wid']] = $b;	
				}
				else
				{
				$bl[$b['listingid']][$b['wid']] = $b;
				}
			}

			if ($return) { return $r; exit(); }
			$this->mysmarty->assign('onhold', $bl);
		}	
}

function getSales($idarray,$channel = 1, $return=false, $flatten = false)
{
		$sql = 'SELECT wid, oldbcn, bcn, title, status, status_notes, generic, regen, history, waid, channel, sold_id, sold_subid, listingid, '.$this->sellingfields().' , trans_date, trans_mk, 
		return_id, 
		returned,
		returned_time,
		returned_recieved,
		returned_refunded,
		returned_extracost,
		ebayReturnId
		 FROM warehouse WHERE `deleted` = 0 AND `nr` = 0 AND `vended` != 0 AND `channel` = '.(int)$channel.' AND (';
		$c = 1;
		foreach ($idarray as $a)
		{
			if ($c == 1) $sql .= '`sold_id` = '.(int)$a;
			else $sql .= ' OR `sold_id` = '.(int)$a;
			$c++;
		}
		$sql .= ')';
		$q =  $this->db->query($sql);
		$bcns = 0;
		if ($q->num_rows() > 0)
		{
			$g =array();
			foreach ($q->result_array() as $b)
			{
				if ($flatten) $bl[] = $b;
				else 
				{
				if ((int)$channel == 2) $bl[$channel][$b['sold_id']][$b['sold_subid']][$b['wid']] = $b;
				else $bl[$channel][$b['sold_id']][$b['wid']] = $b;
				$waid[$b['waid']] = TRUE;
				if ($b['waid'] == 0)
				{
					if ((int)$channel == 2)
					{
						if (isset($g[$channel][$b['sold_id']])) $g[$channel][$b['sold_id']][$b['sold_subid']]++;
						else $g[$channel][$b['sold_id']][$b['sold_subid']] = 1;	
					}
					else
					{
						if (isset($g[$channel][$b['sold_id']])) $g[$channel][$b['sold_id']]++;
						else $g[$channel][$b['sold_id']] = 1;
					}
				}
				$bcns++;			
				}
			}
			if ($return) { return $bl; exit();}
			else $this->mysmarty->assign('base_bcns'.$channel, $bl);
			$this->mysmarty->assign('base_ghostcount'.$channel, $g);
		
			$this->db->select('waid, wtitle');
			$c = 1;
			foreach ($waid as $k => $a)
			{
				if ($c == 1) $this->db->where('waid', (int)$k);
				else $this->db->or_where('waid', (int)$k);
				$c++;
			}
			$ac = $this->db->get('warehouse_auctions');
			if ($ac->num_rows() > 0)
			{
				foreach ($ac->result_array() as $acv)
				{
					$al[$acv['waid']] = $acv['wtitle'];						
				}
				$this->mysmarty->assign('base_auctions'.$channel, $al);
				$this->mysmarty->assign('base_count_bcn'.$channel, $bcns);
				$this->assignchannels();	
			}
			return ($bcns);
		}	
		
		
	
}
function getReturns($idarray,$channel = 1, $flat = false )//,$return = false
{
		$sql = 'SELECT wid, bcn, status, location, returned,returned_notes,returned_time,returned_recieved,returned_refunded,returned_extracost,returnstatus, return_id';
		$sql .= ' FROM warehouse WHERE `deleted` = 0 AND `nr` = 0 AND (';
		$c = 1;
		$m = 0;//printcool ($idarray);
		foreach ($idarray as $a)
		{
			if ((int)$a > 0)
			{
			if ($c == 1) $sql .= '`return_id` = '.(int)$a;
			else $sql .= ' OR `return_id` = '.(int)$a;
			$c++;
			$m++;
			}
		}
		$sql .= ')';
		if ($m == 0) return false;
		$q =  $this->db->query($sql);
		$bcns = 0;
		if ($q->num_rows() > 0)
		{
			foreach ($q->result_array() as $b)
			{				
				if ($flat) $bl[$b['wid']] = $b;
				else $bl[$channel][$b['return_id']][$b['wid']] = $b;
			}
			//if ($return) { return $bl; exit();}
			if ($flat) $this->mysmarty->assign('returnbcns', $bl);			
			else $this->mysmarty->assign('returnbcns'.$channel, $bl);			
			
		}	
}
	function getNewReturns($idarray,$channel = 1, $flat = false )//,$return = false
	{


		$sql = 'SELECT * FROM ebay_refunds WHERE ';
		$c = 1;
		foreach ($idarray as $a)
		{
			if ((int)$a > 0)
			{
				if ($c == 1) $sql .= '`returnId` = '.(int)$a;
				else $sql .= ' OR `returnId` = '.(int)$a;
				$c++;
			}
		}
		if ($c == 1)
		{
			$this->mysmarty->assign('refunds'.$channel, array());
			return false;
		}		$q =  $this->db->query($sql);
		if ($q->num_rows() > 0)
		{
			foreach ($q->result_array() as $v)
			{
				$r[$v['returnId']] = $v;
				$r[$v['returnId']]['responseHistory'] = unserialize($v['responseHistory']);
				if ($v['itemizedRefundDetail'] != '') $r[$v['returnId']]['itemizedRefundDetail'] = unserialize($v['itemizedRefundDetail']);

			}
			$this->mysmarty->assign('refunds'.$channel, $r);


		}
	}
/*
function getReturns2($idarray,$channel = 1)
{
		$sql = 'SELECT wid, bcn, status, location, returned,returned_notes,returned_time,returned_recieved,returned_refunded,returned_extracost,returnstatus, return_id';
		$sql .= ' FROM warehouse WHERE `deleted` = 0 AND `nr` = 0 AND (';
		$c = 1;
		$m = 0;
		foreach ($idarray as $a)
		{
			if ((int)$a > 0)
			{
			if ($c == 1) $sql .= '`return_order_id` = '.(int)$a;
			else $sql .= ' OR `return_order_id` = '.(int)$a;
			$c++;
			$m++;
			}
		}
		$sql .= ') AND `return_order_channel` = '.(int)$channel;
		printcool ($sql);
		if ($m == 0) return false;
		$q =  $this->db->query($sql);
		$bcns = 0;
		if ($q->num_rows() > 0)
		{printcool ($q->result_array());
			foreach ($q->result_array() as $b)
			{				
				$bl[$channel][$b['return_id']][$b['wid']] = $b;
			}
			$this->mysmarty->assign('returnbcns', $bl);			
		}	
}*/
function countSales($idarray)
{
    $sql = 'SELECT DISTINCT e.et_id AS orderkey, e.e_id as eid, "1" as channel FROM (ebay_transactions e) WHERE ';
    $c=1;
    foreach ($idarray as $a)
    {        
       if ($c == 1) $sql .= 'e_id = '.(int)$a.' ';
       else $sql .= 'OR e_id = '.(int)$a.' ';	
       $c++;			
    }
    $sql .= 'UNION ALL ';
    $sql .= 'SELECT DISTINCT o.woid AS orderkey, w.listingid as eid, "4" as channel FROM (warehouse_orders o) LEFT JOIN warehouse w ON o.woid = w.sold_id WHERE w.vended > 0 AND w.deleted = 0 AND w.nr = 0 AND w.channel = 4 AND ';
    $c=1; 
    foreach ($idarray as $a)
    {        
        if ($c == 1) $sql .= 'w.listingid = '.(int)$a.' ';
        else $sql .= 'OR w.listingid = '.(int)$a.' ';
        $c++;			
    }
    $sql .= 'UNION ALL ';
    $sql .= 'SELECT DISTINCT oid as orderkey, eids as eid, "2" as channel  FROM orders WHERE ';
    $c=1;
    foreach ($idarray as $a)
    {       
        if ($c == 1)$sql .=  'eids LIKE "%|'.(int)$a.'|%" ';
        else $sql .=  'OR eids LIKE "%|'.(int)$a.'|%" ';
        $c++;			
    }
   // printcool($sql);
    $q =  $this->db->query($sql);
   // printcool($q);
    $cnt = array();
    if ($q->num_rows() > 0) 
    {
        foreach ($q->result_array() as $ords)
        {
                //printcool($ords);
                if(isset($cnt[$ords['eid']])) $cnt[$ords['eid']]++;
                else $cnt[$ords['eid']] = 1;
                /*if ($ords['channel'] == 1))
                {
                    if(isset($cnt[$ords['eid']])) $cnt[$ords['e_id']]++;
                    else $cnt[$ords['e_id']] = 1;
                }
                if (isset($ords['eids']))
                {
                    $tmp = explode('|', str_replace('||', '',$ords['eids']));				
                    foreach ($tmp as $e)
                    {
                        if ((int)$e > 0)
                        {
                            if(isset($cnt[(int)$e])) $cnt[(int)$e]++;
                            else $cnt[(int)$e] = 1;
                        }                                            
                    }
                }
                if (isset($ords['listingid']))
                {
                    if(isset($cnt[(int)$ords['listingid']])) $cnt[(int)$ords['listingid']]++;
                    else $cnt[(int)$ords['listingid']] = 1;
                }*/
        }
    }       
    $this->mysmarty->assign('eidorders', $cnt);
   // printcool ($cnt);
    //exit();
            
		/*$sql = 'SELECT e_id FROM ebay_transactions WHERE (';
		$c = 1;		
		foreach ($idarray as $a)
		{
			$cnt[(int)$a] = 0;
			if ($c == 1) $sql .= '`e_id` = '.(int)$a;
			else $sql .= ' OR `e_id` = '.(int)$a;			
			$c++;			
		}
		//printcool ($listings);
		$sql .= ')';	
		
		$q =  $this->db->query($sql);
		$cnt = array();
		if ($q->num_rows() > 0) 
		{
			foreach ($q->result_array() as $ords)
			{
				$cnt[$ords['e_id']]++;				
			}
		}
		$sql = 'SELECT eids FROM orders WHERE (';
		$c = 1;		
		foreach ($idarray as $a)
		{
			if ($c == 1) $sql .= "`eids` LIKE '%|".$a."|%'";
			else $sql .= " OR `eids` LIKE '%|".$a."|%'";
			$c++;
			
		}
		
		$sql .= ')';	
			
		$q =  $this->db->query($sql);		
		if ($q->num_rows() > 0) 
		{
			foreach ($q->result_array() as $ords)
			{
				$tmp = explode('|', str_replace('||', '',$ords['eids']));
				
				foreach ($tmp as $e)
				{
					if ((int)$e > 0) $cnt[(int)$e]++; 	
				}
			}
		}
		$sql = "SELECT `sold_id`, `listingid` FROM warehouse WHERE `channel` = 4  AND `vended` != 0 AND `nr` != 0 AND `deleted` != 0 AND (";
		$c = 1;
		foreach ($idarray as $a)
		{
			if ($c == 1) $sql .= "`listingid` = ".(int)$a;
			else $sql .= " OR `listingid` = ".(int)$a;
			$c++;
		}
		$sql .= ')';
		$oq = $this->db->query($sql);
		$orderids = array();
		if ($oq->num_rows() > 0)
		{
			foreach ($oq->result_array() as $orq)
			{
				$orderids[$orq['listingid']] = $orq['listingid'];	
			}	
			foreach ($orderids as $ords)
			{
				$cnt[$ords]++;				
			}				
		}$this->mysmarty->assign('eidorders', $cnt);
		*/
		
}
function getEmptySales($idarray,$channel = 1)
{		return false;

		$sql = 'SELECT et_id, e_id FROM ebay_transactions WHERE (';
		$c = 1;		
		foreach ($idarray as $a)
		{
			if ($c == 1) $sql .= '`e_id` = '.(int)$a;
			else $sql .= ' OR `e_id` = '.(int)$a;			
			$c++;
			$listings[$a] = array();
		}
		//printcool ($listings);
		$sql .= ') ORDER BY `rec` DESC';	
		
		$q =  $this->db->query($sql);
		
		if ($q->num_rows() > 0) 
		{
			$sql = 'SELECT wid, listingid, sold_id FROM warehouse WHERE `deleted` = 0 AND `nr` = 0 AND `vended` != 0 AND `channel` = '.(int)$channel.' AND (';
			$c = 1;
			foreach ($q->result_array() as $v)
			{
				$listings[$a][(int)$v['et_id']] = 0;
				if ($c == 1) $sql .= '`sold_id` = '.(int)$v['et_id'];
				else $sql .= ' OR `sold_id` = '.(int)$v['et_id'];
				$c++;				
			}
		

			$sql .= ')';
			$q =  $this->db->query($sql);
			
			if ($q->num_rows() > 0)
			{
				foreach ($q->result_array() as $b)
				{
					//$listings[$b['listingid']][$b['sold_id']][] = $b;
					$listings[$b['listingid']][$b['sold_id']]++;
				}
				
				
			}
		 $this->mysmarty->assign('listingorders', $listings);		
		}
}
function getSalesListings($idarray, $return = false, $count= false, $eachcount=true)
{
	//$sql = 'SELECT wid, bcn, title, status, status_notes, generic, waid, channel, listingid FROM warehouse WHERE `deleted` = 0 AND `nr` = 0 AND `vended` = 0 AND (';
	if ($eachcount) $extra = ', listingid ';
	else $extra = '';
	if ($count || $eachcount) $sql = 'SELECT wid '.$extra.'FROM warehouse WHERE `deleted` = 0 AND `nr` = 0 AND `vended` = 0 AND (';
	else $sql = 'SELECT wid, oldbcn, bcn, title, status, status_notes, location, audit, generic, regen, history, waid, channel, listingid FROM warehouse WHERE `deleted` = 0 AND `nr` = 0 AND `vended` = 0 AND (';

		$c = 1;
		if (isset($idarray[0])) unset($idarray[0]);
		if (count($idarray) > 0)
		{
		foreach ($idarray as $a => $v)
		{
			if ($c == 1) $sql .= '`listingid` = '.(int)$a;
			else $sql .= ' OR `listingid` = '.(int)$a;
			$c++;
		}
		$sql .= ')';
		$q =  $this->db->query($sql);
		if ($q->num_rows() > 0)
		{
			if ($count) return $q->num_rows();
			else
			{
				foreach ($q->result_array() as $b)
				{
					if ($eachcount)
					{
						if (isset($bl[$b['listingid']])) $bl[$b['listingid']]++;
						else $bl[$b['listingid']] = 1;
					}
					else $bl[$b['wid']] = $b;						
				}

				if ($return) return $bl;
				else $this->mysmarty->assign('avail_bcns', $bl); 
			}
		}	
		} else $this->mysmarty->assign('avail_bcns', array());
}

function getChannelData($listingid)
{
	$this->db->select('price_ch1, price_ch2, price_ch3, qn_ch1, qn_ch2, qn_ch3');
	$this->db->where('e_id', (int)$listingid);
	$e = $this->db->get('ebay');
	if ($e->num_rows() > 0)
	{
		$ed = $e->row_array();
		foreach ($ed as $k => $v) $this->mysmarty->assign(str_replace('ch', '', $k), $v);
	}
}	
function AutoGhoster($listingid, $qn, $bcns = false)
{
	$CI =& get_instance();
    $CI->load->model('Mywarehouse_model');
	$CI->load->model('Auth_model');
	//if ((int)$listingid == 14516) GoMail(array ('msg_title' => '14516 BCNS AUTOGHOSTER @ '.CurrentTime(), 'msg_body' => printcool ($bcns, true, 'bcns'), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
	//if ((int)$listingid == 14516) GoMail(array ('msg_title' => '14516 AutoGHOSTER QN @ '.CurrentTime(), 'msg_body' => printcool ((int)$qn, true, 'qn'), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
	if ($bcns && count($bcns) > 0)
	{
		foreach ($bcns as $b)
		{
			//if ($start <= $qn)
			if ((int)$qn != 0 && $b['status'] == 'Mismatch')
			{
				$CI->Auth_model->wlog($b['bcn'], (int)$b['wid'], 'status', $b['status'], 'Listed');
				$update['status_notes'] = 'Changed from: '.$b['status'];
				$update['status'] = 'Listed';
				//printcool ($update);
				//printcool ($b['bcn']);
				$this->db->insert('ebay_cron', array('e_id' => (int)$listingid, 'data' => 'BCNRegen from '.(int)$listingid.' - UnMismatch ID:'.$b['wid'], 'time' => CurrentTime(), 'ts' => mktime()));
			
				$this->db->update('warehouse', $update, array('wid' => (int)$b['wid']));		
				//$start++;	
				$qn--;
			}
		}
	}
	
	//if ((int)$listingid == 14516) GoMail(array ('msg_title' => '14516 AutoGHOSTER QN PRE GHOST CREATE @ '.CurrentTime(), 'msg_body' => printcool ((int)$qn, true, 'qn'), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
	
	$title = $CI->Mywarehouse_model->GetListingTitleAndCondition($listingid, true);
	$this->db->select("bcn");
		$this->db->where('waid' , 0);
		$this->db->where('generic' , 1);
		$this->db->where('bcn_p1' , "G");
		//$this->db->order_by("bcn_p2", "DESC");
		$this->db->order_by("wid", "DESC");
		$w = $this->db->get('warehouse', 1);
		if ($w->num_rows() > 0)
		{
			$next = $w->row_array();
			$next = (int)str_replace('G', '', trim($next['bcn']));		
//printcool ($next);
			$amount = (int)$qn;
			
			$start = 1;
			while ($start <= $amount)
						{
							
							$next++;
							
							/*$this->db->where('bcn', "G".$next);
							$this->db->or_where('lot', "G".$next);
							$this->db->or_where('oldbcn', "G".$next);
							$q = $this->db->get('warehouse');
							if ($q->num_rows() > 0)
							{
								$next++;
							}*/
							$array['waid'] = 0;
							$array['bcn'] = "G".$next;
							$array['bcn_p1'] = "G";
							$array['bcn_p2'] = $next;
							$array['listingid'] = $listingid;
							$array['status'] = 'Listed';
							if ($title) $array['title'] = $title;
							$array['listed_date'] = CurrentTime();							
							$array['createddate'] = CurrentTime();
				 			$array['createddatemk'] = mktime();
							$array['generic'] = 1;
							$array['regen'] = 1;
							//printcool ($array);
							$this->db->insert('ebay_cron', array('e_id' => (int)$listingid, 'data' => 'BCNRegen from '.$listingid.' - GhostGened:'.$array['bcn'], 'time' => CurrentTime(), 'ts' => mktime()));
							
							$this->db->insert('warehouse', $array);
							$array['wid'] = $this->db->insert_id();
							foreach ($array as $k => $v)
							{
								if ($k !='bcn_p1' && $k !='bcn_p2' && $k !='bcn_p3' && $k !='vended')$CI->Auth_model->newlog($array['bcn'], $array['wid'], $k, $v);		
							}
							unset($array);
							$start++;							
						}
		}
		$this->runAssigner($listingid, $amount);
	
}
function AutoUnGhoster($listingid, $qn, $ghostwids, $bcnpool)
{
	if (count($ghostwids) > 0)
	{
		foreach ($ghostwids as $g)
		{
			//printcool ($g['wid']);
			if ((int)$qn != 0)
			{
			$this->db->insert('ebay_cron', array('e_id' => (int)$listingid, 'data' => 'BCNRegen from '.$listingid.' - UnGhost ID:'.$g, 'time' => CurrentTime(), 'ts' => mktime()));
		
			$this->db->update('warehouse',array('deleted' => 1), array('wid' => (int)$g));
			$qn--;
			}
		}
	}

        $runid = $this->que_rev($listingid, 'q', count($bcnpool));
        $url = Site_url().'Cronn/DoRevise/'.$runid;         
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_exec($ch);
        // REVISE  ebay quantity as $qn for listing ID
        // //Commit Revision
        // 
        // 
        // 
        // 
	//$rem = $qn-(count($ghostwids));
	/*$start = 1;
	$CI =& get_instance();
    $CI->load->model('Auth_model');
	foreach ($bcnpool as $b)
	{
		//if ($start <= $qn)
		if ((int)$qn != 0 && $b['status'] != 'FBA')
		{
			$CI->Auth_model->wlog($b['bcn'], (int)$b['wid'], 'status', $b['status'], 'Mismatch');
			$update['status_notes'] = 'Changed from: '.$b['status'];
			$update['status'] = 'Mismatch';
			//printcool ($update);
			//printcool ($b['bcn']);
			$this->db->insert('ebay_cron', array('e_id' => (int)$listingid, 'data' => 'BCNRegen from '.(int)$listingid.' - Mismatched ID: '.$b['wid'], 'time' => CurrentTime(), 'ts' => mktime()));
		
			$this->db->update('warehouse', $update, array('wid' => (int)$b['wid']));		
			GoMail(array ('msg_title' => 'AutoUnGhoster MISTMATCH EID: '.(int)$listingid.', WID: '.(int)$b['wid'].' @ '.CurrentTime(), 'msg_body' => '', 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);	
			//$start++;	
			$qn--;		
		}
	}*/
        
        
        
        
        
	$this->ProcessFinalCounts($listingid);
	
}
function AssignBCN($i, $channel)
{
	$this->channel = (int)$channel;
		log_message('error', 'channel'.$this->channel.'for '.$i['et_id'].' @ '.CurrentTime());
	if ($this->channel == 1)
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
				
				//$data['sold'] = 'eBay';
				//$data['sold_date'] = $i['paidtime'];
				
				/*if ($i['qty'] > 1)
				{
					$data['paid'] = sprintf("%01.2f", (float)$i['paid']/$i['qty']);
					$data['shipped'] = sprintf("%01.2f", (float)$i['ssc']/$i['qty']);
					$data['shipped_actual'] = sprintf("%01.2f", (float)$i['asc']/$i['qty']);
					$data['sellingfee'] = sprintf("%01.2f", (float)$i['fee']/$i['qty']);				
				} 
				else
				{
					$data['paid'] = $i['paid'];
					$data['shipped'] = $i['ssc'];
					$data['shipped_actual'] = $i['asc'];
					$data['sellingfee'] = $i['fee'];		
				}*/
				//$data['ordernotes'] = 'Transaction '.$id;				
				$data['sold_id'] = $i['et_id'];
				$data['soldqn'] = $i['qty'];
				$data['trans_date'] = $i['datetime'];
				$data['trans_mk'] = $i['mkdt'];
				$this->quantity = $i['qty'];
				$this->listingid = $i['e_id'];
				log_message('error', 'in channel 1 for '.$i['et_id'].' @ '.CurrentTime());
	}
	else
	{
				//$data['sold'] = 'WebSite';
				//$data['sold_date'] = CurrentTime();
				$data['sold_id'] = $i['oid'];
				$data['sold_subid'] = $i['e_id'];
				$data['soldqn'] = $i['quantity'];
				$data['trans_date'] = $i['time'];
				$data['trans_mk'] = $i['submittime'];
				//$data['ordernotes'] = 'Order '.$i['oid'];
				/*if ($i['quantity'] > 1)
				{
					$data['paid'] = sprintf("%01.2f", (float)$sale['endprice']/$i['quantity']);
					$data['shipped'] = sprintf("%01.2f", (float)$sale['endprice_delivery']/$i['quantity']);
				}
				else
				{
					$data['paid'] = $sale['endprice'];
					$data['shipped'] = $sale['endprice_delivery'];
				}*/
				
				//$data['ordernotes'] = 'Order '.$id;
				//$data['sellingfee'] = 0;
				$this->quantity = $i['quantity'];
				$this->listingid = $i['e_id'];
				log_message('error', 'in channel 2 for '.$i['oid'].' @ '.CurrentTime());
				//REVISE eBay
				
				
				/*	
				$i['e_id']
				$i['quantity']
				while ($start <= $i['quantity'])
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
				$i['oid']
				*/
	}
	$data['vended'] = 2;
	$data['status'] = 'On Hold';
	$data['channel'] = $this->channel;
	
	$CI =& get_instance();
    $CI->load->model('Auth_model');
	
	$start = 1;
			
	while ($start <= $this->quantity)
	{
			 $sql = 'SELECT * FROM warehouse WHERE `deleted` = 0 AND `nr` = 0 AND `vended` = 0 AND `sold_id` = 0 AND `listingid` = '.$this->listingid.' AND `status` = "Listed" AND (`channel` = 0 OR `channel` = '.$this->channel.') ORDER BY `channel` DESC LIMIT 1';
	
			$query = $this->db->query($sql);
			if ($query->num_rows() == 0) $this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => '<span style="color:red;">Cannot auto allocate BCN in Warehouse</span> from Listing to Order', 'msg_body' => '', 'msg_date' => CurrentTime(), 'e_id' => $this->listingid, 'itemid' => '', 'trec' => $data['sold_id'], 'admin' => 'Auto', 'sev' => 1));
			else 
			{
			$wid = $query->row_array();
			
			$data['status_notes'] = 'Changed from "'.$wid['status'].'" - Channel '.$this->channel.' Assigner';
			$data['prevstatus'] = $wid['status'];
			//if (trim($wid['status_notes']) == '') $data['status_notes'] = $statusnotes;
			//else $data['status_notes'] = $statusnotes.' | '.$wid['status_notes'];
			//printcool ($data);
			$this->db->update('warehouse', $data, array('wid' => (int)$wid['wid']));			
        	 
			foreach ($data as $k => $v)
			{//printcool ($v); printcool ($wid[$k]);
				 if ($v != $wid[$k]) $CI->Auth_model->wlog($wid['bcn'], $wid['wid'], $k, $wid[$k], $v);	
			}	
			}
			$start++;
	}	
	$this->runAssigner($this->listingid, $this->quantity, $data['sold_id']);
}
function runAssigner($listingid, $actionqn, $transaction = '')
{
        $alog=$_SERVER['REQUEST_URI'].'<br>function runAssigner($listingid, $actionqn, $transaction = 0)<br>function runAssigner('.$listingid.', '.$actionqn.', '.$transaction.')';
        $alog.='<Br>POST: '.printcool($_POST,TRUE);	
        if (isset($_POST['soldid']) && $transaction == '') $this->transaction = (int)$_POST['soldid'];
        else $this->transaction = (int)$transaction;
        $alog.='<Br>Transaction: '.$this->transaction;
	$this->actionqn = $actionqn;
        $alog.='<Br>actionqn: '.$this->actionqn;
	$this->listingid = (int)$listingid;
        $alog.='<Br>listingid: '.$this->listingid;
	$this->ch = $this->getListingChannels();
        $alog.='<Br>CH getListingChannels: '.printcool($this->ch,true);
	$this->warehousebcncount = $this->getWareHouseCount();
         $alog.='<Br>warehousebcncount: '.$this->warehousebcncount;
	$this->ch = $this->ProcessChannelCount();
         $alog.='<Br>CH getListingChannels: '.printcool($this->ch,true);
	//GoMail(array ('msg_title' => 'runAssigner LOG @'.CurrentTime(), 'msg_body' => $alog, 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
         $this->ProcessFinalCounts();
	$alog.='<Br>$this->que_rev($this->listingid, \'q\', $this->ch[\'qn_ch1\']);';
         $alog.='<Br>$this->que_rev('.$this->listingid.', q, '.$this->ch['qn_ch1'].')';
         //GoMail(array ('msg_title' => 'runAssigner LOG @'.CurrentTime(), 'msg_body' => $alog, 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
        
         $this->que_rev($this->listingid, 'q', $this->ch['qn_ch1']);
        if ($this->listingid > 0) $this->checkunempty();
}
function checkunempty()
{
    $sql = 'SELECT e_id,quantity,ebayquantity,qn_ch1 FROM ebay WHERE `ebay_id` != 0 AND `ebended` IS NULL AND `quantity` = 0 AND `ebayquantity` != 0 AND `e_id` = '.$this->listingid; 
    $e = $this->db->query($sql);
    //if($e->num_rows() > 0) GoMail(array ('msg_title' => 'checkunempty RUN '.$this->listingid.' @'.CurrentTime(), 'msg_body' => $sql.printcool($e,true), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
      
    if($e->num_rows() > 0)
    {
        $eb= $e->row_array();
        if ($eb['qn_ch1'] != 0) $notice=' *** QNCH1 = '.$eb['qn_ch1'].' *** ';
        else $notice=' *QNCH1-OK*';
        $this->que_rev($this->listingid, 'q', 0);	
        //GoMail(array ('msg_title' => 'checkunempty '.$notice.$this->listingid.' exists @'.CurrentTime(), 'msg_body' => printcool($eb,true), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
       
    }

}
function getListingChannels()
{	
	$this->db->select('qn_ch1, qn_ch2, qn_ch3');
	$this->db->where('e_id', $this->listingid);
	$qnch = $this->db->get('ebay',1);
	if ($qnch->num_rows() > 0)
    {
       // GoMail(array ('msg_title' => 'getListingChannels NOT EMTPY @'.CurrentTime(), 'msg_body' => printcool($qnch->row_array(),true), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);

        return $qnch->row_array();
    }
	else  GoMail(array ('msg_title' => 'getListingChannels EMTPY - '.$_SERVER['REQUEST_URI'].' @'.CurrentTime(), 'msg_body' => printcool($this->listingid,true), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);

}
function getWareHouseCount($listingid = '')
{
	if((int)$listingid > 0) $this->listingid = $listingid;
	$sql = 'SELECT wid FROM warehouse WHERE `deleted` = 0 AND `nr` = 0 AND `vended` = 0 AND `status` = "Listed" AND `listingid` = '.$this->listingid;
	$q =  $this->db->query($sql);
	$this->db->update('ebay', array('quantity' => $q->num_rows()), array('e_id' => $this->listingid));
	if ($q->num_rows() > 0) return $q->num_rows();
	else return 0;		
}
function ProcessChannelCount()
{
	/*
	$this->listingid
	$this->channel
	$this->actionqn = 1/-1
	*/
	
	//$listingid, $channel, $ch, $warehousebcncount
	if (!isset($this->channel)) $this->channel = 'Admin';
	$oldch = $this->ch;
	$start = 1;
	$val = $this->actionqn;	
	if ($val > 0) { $remove = true; $this->warehousebcncount = $this->warehousebcncount+abs($val);}
	else { $remove = false; $this->warehousebcncount = $this->warehousebcncount-abs($val);}
	while ($start <= abs($val))
	{


	if (!isset($this->ch) || (isset($this->ch) && count($this->ch) == 0))
	{
		//GoMail(array ('msg_title' => '$this->ch error @'.CurrentTime(), 'msg_body' => printcool($this->ch,true, 'ch'), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
	}
	if (isset($this->ch) && count($this->ch) > 0) foreach ($this->ch as $k => $v)
	{
		if (($v == 0) && (str_replace('qn_', '', $k) == $this->channel) && ($this->channel != 'Admin')) $this->db->insert('admin_history', array ('msg_type' => 1, 'msg_title' => '<span style="color:red;">Quantity Channel Count 0</span> from Listing to Order', 'msg_body' => '', 'msg_date' => CurrentTime(), 'e_id' => $this->listingid, 'itemid' => '', 'trec' =>'', 'admin' => 'ProcessChannelCount', 'sev' => 5));

		if ($this->channel == 'Admin')
		{//printcool ($this->warehousebcncount);
			if ($v < $this->warehousebcncount) $custom[$k] = true;	
		}
		else
		{
			if ($v < $this->warehousebcncount && str_replace('qn_', '', $k) != $this->channel) $custom[$k] = true;				
		}
			if (!isset($custom[$k]))
			{
				if ($v > $this->warehousebcncount) $v =$this->warehousebcncount; 
				if (isset($this->session->userdata['ownnames'])) $admin = $this->session->userdata['ownnames'];
				else $admin = 'Cron';
				if ($remove) $aq = abs($this->actionqn);				
				else $aq = $this->actionqn;
				$ra['admin'] = $admin;
				$ra['time'] = CurrentTimeR();
				$ra['ctrl'] = 'Channel '.$this->channel.' Qn Assigner';
				$ra['field'] = $k;
				$ra['atype'] = 'Q';
				$ra['e_id'] = (int)$this->listingid;			
				$ra['time'] = CurrentTime();
				if ($this->transaction > 0) $ra['trans_id'] = $this->transaction;
				$ra['datafrom'] = $v;//-$aq;
				$this->ch[$k] = $v-$this->actionqn;
				$ra['datato'] = $this->ch[$k];
				//printcool ($k);printcool ($k);printcool ($aq); printcool($this->actionqn);
				if ($ra['datafrom'] != $ra['datato'])
				{
					 $this->db->insert('ebay_actionlog', $ra);			
					 $this->db->update('ebay', array($k => $ra['datato']), array('e_id' => (int)$this->listingid));
				}
			}
			/*
			foreach ($oldch as $ok => $ov)
			{
				if ($ok != $k && $ok != $this->channel)
				{
					if ($ov > $v) $hasmore[$k] = $ov;
					elseif ($ov < $v) $hasless[$k] = $ov;
					else $hasame[$k] = $ov;
				}
			}
	
			if ($this->actionqn < 0) $direction = $hasless;
			else $direction = $hasmore;
			foreach ($direction as $hk => $hv)
			{
				if (!isset($custom[$hk]))
				{
				$ra['admin'] = 'Auto';
				$ra['time'] = CurrentTimeR();
				$ra['ctrl'] = 'Channel '.$this->channel.' Assigner';
				$ra['field'] = 'qn_ch'.$hk;
				$ra['atype'] = 'Q';
				$ra['local'] = (int)$this->listingid;			
				$ra['time'] = CurrentTime();
				$ra['datafrom'] = $this->ch[$hk];
				$ra['datato'] = ($this->ch[$hk]-$this->actionqn);						
				$this->db->insert('ebay_actionlog', $ra);
				$this->ch[$hk] = $this->ch[$hk]-$this->actionqn;
				}
			}
			foreach ($hasame as $hk => $hv)
			{
				if (!isset($custom[$hk]))
				{
				$ra['admin'] = 'Auto';
				$ra['time'] = CurrentTimeR();
				$ra['ctrl'] = 'Channel '.$this->channel.' Assigner';
				$ra['field'] = 'qn_ch'.$hk;
				$ra['atype'] = 'Q';
				$ra['local'] = (int)$this->listingid;			
				$ra['time'] = CurrentTime();
				$ra['datafrom'] = $this->ch[$hk];
				$ra['datato'] = ($this->ch[$hk]-$this->actionqn);						
				$this->db->insert('ebay_actionlog', $ra);
				$this->ch[$hk] = $this->ch[$hk]-$this->actionqn;
				}
			}
			
				$ra['admin'] = 'Auto';
				$ra['time'] = CurrentTimeR();
				$ra['ctrl'] = 'Channel '.$this->channel.' Assigner';
				$ra['field'] = 'qn_ch'.$this->channel;
				$ra['atype'] = 'Q';
				$ra['local'] = (int)$this->listingid;			
				$ra['time'] = CurrentTime();
				$ra['datafrom'] = $this->ch[$this->channel];
				$ra['datato'] = ($this->ch[$this->channel]-$this->actionqn);						
				$this->db->insert('ebay_actionlog', $ra);
				$this->ch[$this->channel] = $this->ch[$this->channel]-$this->actionqn;	
				
				*/
			
		}
			
		$start++;
	return $this->ch;
	}		
	
}
function ProcessFinalCounts($listingid = false)
{		
	if ($listingid) $this->listingid = (int)$listingid;
	$this->eb['quantity'] = 0;
	//$this->ch['xquantity'] = 0;
	$this->eb['ngen'] = 0;
	$this->eb['e_qpart'] = 0;
	$sql = 'SELECT wid, waid, generic, status FROM warehouse WHERE `deleted` = 0 AND `nr` = 0 AND `vended` = 0 AND `listingid` = '.$this->listingid;
	$cc =  $this->db->query($sql);
	if ($cc->num_rows() > 0)
	{
		foreach ($cc->result_array() as $c)
		{
			if ($c['status'] == 'Listed') $this->eb['quantity']++;
			//$this->ch['xquantity']++;
			$this->eb['e_qpart']++;	
			if ($c['generic'] != 0) $this->eb['ngen']++;
					
		}
	}
	//printcool ($this->ch);
	$this->db->update('ebay', $this->eb, array('e_id' => (int)$this->listingid));
}
function que_rev($eid, $etype, $val, $admin = false)
{
	$this->db->select('er_id');
	$this->db->where('e_id', (int)$eid);
	$this->db->where('e_type', $etype);
	$r = $this->db->get('ebay_revise');
	if (isset($this->session->userdata['ownnames'])) $admin = $this->session->userdata['ownnames'];
	else 
	{
		if (!$admin) $admin = 'Cron';
	}
	if ($r->num_rows() > 0)
	{
		$d = $r->row_array();
		
		$this->db->update('ebay_revise', array('e_val'=> trim($val)), array('er_id' => $d['er_id'], 'place'=> $this->router->class.'/'.$this->router->method, 'admin' => $admin));
	}
	else $this->db->insert('ebay_revise', array('e_id' => (int)$eid, 'e_type' => $etype, 'e_val'=> trim($val),'place'=> $this->router->class.'/'.$this->router->method, 'admin' => $admin));	
	
        
        if (isset($d['er_id'])) $runid = $d['er_id'];
	else $runid = $this->db->insert_id();
                
        if ($etype == 'p') 
	{
		
        $url = Site_url().'Cronn/DoRevise/'.$runid;         
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_exec($ch);
	}
        return $runid;
}
function UpdateSoldQN($channel, $soldid, $subid = 0)
{
	$xtra = '';
	if ((int)$subid > 0) $xtra = ' AND `sold_subid` = "'.(int)$subid.'"';
	$sql = 'SELECT wid FROM warehouse WHERE `deleted` = 0 AND `nr` = 0 AND `vended` != 0 AND `channel` = '.$channel.' AND `sold_id` = '.(int)$soldid.$xtra;
	$q =  $this->db->query($sql);
	$soldqn = (int)$q->num_rows();
	if ((int)$subid > 0) $this->db->update('warehouse', array('soldqn' => $soldqn), array('deleted' => 0, 'nr' => 0, 'channel' => $channel, 'sold_id' => (int)$soldid, 'sold_subid' => (int)$subid));
	else $this->db->update('warehouse', array('soldqn' => $soldqn), array('deleted' => 0, 'nr' => 0, 'channel' => $channel, 'sold_id' => (int)$soldid));	
}
function SaveSoldQN($channel, $soldid, $subid = 0, $soldqn = 0)
{
	$xtra = '';
	if ((int)$subid > 0) $xtra = ' AND `sold_subid` = "'.(int)$subid.'"';
	$sql = 'SELECT wid FROM warehouse WHERE `deleted` = 0 AND `nr` = 0 AND `vended` != 0 AND `soldqn` != '.(int)$soldqn.' AND `channel` = '.$channel.' AND `sold_id` = '.(int)$soldid.$xtra;
	$q =  $this->db->query($sql);
	if ((int)$q->num_rows() > 0)
	{
		if ((int)$subid > 0) $this->db->update('warehouse', array('soldqn' => $soldqn), array('deleted' => 0, 'nr' => 0, 'channel' => $channel, 'sold_id' => (int)$soldid, 'sold_subid' => (int)$subid));
		else $this->db->update('warehouse', array('soldqn' => $soldqn), array('deleted' => 0, 'nr' => 0, 'channel' => $channel, 'sold_id' => (int)$soldid));	
	}	
}
function UpdateFixedQNField($listingid)
{
	$sql = 'SELECT wid FROM warehouse WHERE `deleted` = 0 AND `nr` = 0 AND `vended` = 0 AND `listingid` != '.(int)$soldqn.' AND `channel` = '.$channel.' AND `sold_id` = '.(int)$soldid.$xtra;
	$q =  $this->db->query($sql);
	if ((int)$q->num_rows() > 0)
	{
		if ((int)$subid > 0) $this->db->update('warehouse', array('soldqn' => $soldqn), array('deleted' => 0, 'nr' => 0, 'channel' => $channel, 'sold_id' => (int)$soldid, 'sold_subid' => (int)$subid));
		else $this->db->update('warehouse', array('soldqn' => $soldqn), array('deleted' => 0, 'nr' => 0, 'channel' => $channel, 'sold_id' => (int)$soldid));	
	}	
}
function Details_Sold($data,$wid,$soldid, $channel, $onlyupdate = false)
{	
	$CI =& get_instance();
	$CI->load->model('Auth_model');	
	
	$paid = $data['paid']+$data['shipped_actual'];
	if ($onlyupdate) $placelog = 'Handle BCN '.$wid;
        else $placelog = 'BCN Sales Attach (Sold)';
	$insert = array('w_id'=>(int)$wid, 'sold_id' => (int)$data['sold_id'] ,'channel' => (int)$data['channel'] ,'uts' => (int )$data['trans_mk'] ,'created' => $data['trans_date'] ,'fee' => floater($data['sellingfee']) ,'paid' => floater($paid), 'ctrl'=> $placelog);
	
	if (isset($data['paypal_fee'])) $insert['paypal_fee'] = floater($data['paypal_fee']);
	
	$this->db->select('td_id');
	$this->db->where('wid', (int)$wid);
        $this->db->where('sold_id', (int)$soldid);
        $this->db->where('channel', (int)$channel);
      
	$found = $this->db->get('warehouse');	
	if ($found->num_rows > 0)
	{ 
                foreach ($found->result_array() as $fra)
                {
                    $f = $fra;	
                }
                $wid_td_id = $f['td_id'];
		if ($wid_td_id > 0)
		{
			 $this->db->where('td_id', $wid_td_id);
			 $td = $this->db->get('transaction_details');
			 if ($td->num_rows > 0)
			 {
				$trdet = $td->row_array();
				foreach ($insert as $k => $v)
				{
					if ($trdet[$k] == $v) unset($insert[$k]);	
				}
				if (count($insert) > 0) 
				{
					$this->db->update('transaction_details', $insert,array('td_id' => $wid_td_id));
					$this->db->insert('infolog',  array('msg_type' => 'DTS_S','msg_title' => 'Details_Sold UPDATE ('.(int)$wid.') '.$_SERVER['REQUEST_URI'].' @ '.CurrentTime(), 'msg_body' => printcool ($insert, true, '$insert').printcool ($trdet, true, 'trdet'), 'msg_date' => CurrentTime(), 'msg_ts' => mktime()
				 ));
				}
			 }
			 elseif (!$onlyupdate) 
			 {
				 $this->db->insert('transaction_details',$insert);			 
				 $td_id = $this->db->insert_id();
				$this->db->update('warehouse',array('td_id' => $td_id), array('wid' => (int)$wid));
				
				$CI->Auth_model->wlog('N-A', $wid, 'td_id', $wid_td_id, $td_id);
				$this->db->insert('infolog',  array('msg_type' => 'DTS_S','msg_title' => 'Details_Sold 1 ('.(int)$wid.') '.$_SERVER['REQUEST_URI'].' @ '.CurrentTime(), 'msg_body' => printcool ($insert, true, '$insert').printcool ($data, true, 'data'), 'msg_date' => CurrentTime(), 'msg_ts' => mktime()
				 ));
			 }
		}
		elseif (!$onlyupdate)
		{
			$this->db->insert('transaction_details',$insert);
			$td_id = $this->db->insert_id();
			$this->db->update('warehouse',array('td_id' => $td_id), array('wid' => (int)$wid));
			
			$CI->Auth_model->wlog('N-A', $wid, 'td_id', 0, $td_id);
			$this->db->insert('infolog',  array('msg_type' => 'DTS_S','msg_title' => 'Details_Sold 2 ('.(int)$wid.') '.$_SERVER['REQUEST_URI'].' @ '.CurrentTime(), 'msg_body' => printcool ($insert, true, '$insert').printcool ($data, true, 'data'), 'msg_date' => CurrentTime(), 'msg_ts' => mktime()
				 ));
		}
	}
	
}
function Details_Removed_Sold($wid, $sold_id, $channel)
{
	$this->db->select('td_id');
	$this->db->where('w_id', (int)$wid);
	$this->db->where('sold_id',(int)$sold_id);
	$this->db->where('channel',(int)$channel);
	$found = $this->db->get('transaction_details');	
	if ($found->num_rows > 0)
	{ 
			$f = $found->row_array();			
			$this->db->where('td_id',(int)$f['td_id']);
			$this->db->delete('transaction_details');
			$this->db->update('warehouse',array('td_id' => 0), array('wid' => $f['wid']));
			$CI =& get_instance();
			$CI->load->model('Auth_model');	
			$CI->Auth_model->wlog('', $f['wid'], 'td_id', $f['td_id'], 0);
			
			$this->db->insert('infolog',  array('msg_type' => 'DTS_RSLD','msg_title' => 'Details_Removed_Sold Deleted ('.$f['wid'].') '.$_SERVER['REQUEST_URI'].' @ '.CurrentTime(), 'msg_body' => printcool ( $f['td_id'], true, 'tdid').printcool ($wid, true, '$wid').printcool ($sold_id,true,'$sold_id').printcool($channel, true, '$channel'), 'msg_date' => CurrentTime(), 'msg_ts' => mktime()
				 ));	
	}
	else $this->db->insert('infolog',  array('msg_type' => 'DTS_RSLD','msg_title' => 'Details_Removed_Sold Missing ('.(int)$wid.') '.$_SERVER['REQUEST_URI'].' @ '.CurrentTime(), 'msg_body' => printcool ($wid, true, '$wid').printcool ($sold_id,true,'$sold_id').printcool($channel, true, '$channel'), 'msg_date' => CurrentTime(), 'msg_ts' => mktime()
				 ));

}
function Details_Returned($data, $update = false)
{
	/*
	 *
	 * 'w_id'=>(int)$data['wid'],
	'sold_id' => (int)$data['sold_id'] ,
	'channel' => (int)$data['channel'] ,
	'uts' => (int )$data['uts'] ,
	'created' => $data['created'] ,
	'ctrl'=> 'BCN Sales Attach (Return)'
	 */
	$insert = array(

	'returned_amount' => $data['returned_amount'], 
	'extra_cost' => $data['extra_cost'],
	'return_id' => $data['return_id'], 
	'returnID' => $data['returnID']
	);
	
	if ($data['channel'] == 1)
	{
		$insert['returnID'] = $data['returnID'];
		if (isset($insert['refund_date'])) $insert['refund_date'] = 	$data['refund_date'];
		if (isset($insert['refund_date_mk'])) $insert['refund_date_mk'] = 	$data['refund_date_mk'];
		$insert['returned_amount'] = $data['returned_amount'];
		$insert['return_shipping'] = $data['return_shipping'];
		
		$insert['return_total_qty'] = $data['return_total_qty'];
	}	
	
	$this->db->update('transaction_details',$insert,array('w_id' => $data['wid']));
	
	$this->db->insert('infolog',  array('msg_type' => 'DTS_RTN','msg_title' => 'Details_Returned ('.$data['wid'].') '.$_SERVER['REQUEST_URI'].' @ '.CurrentTime(), 'msg_body' => printcool ($insert, true, '$insert').printcool ($data, true, 'data'), 'msg_date' => CurrentTime(), 'msg_ts' => mktime()
				 ));
	

	
}
function NetProfitCalc($paid = 0, $shipping = 0, $inshp = 0, $cost = 0, $fee = 0, $actualshipping = 0,$paypalfee = 0)
{	
	return floater(((float)$paid+(float)$shipping)-((float)$cost+(float)$fee+(float)$inshp+(float)$actualshipping+(float)$paypalfee));
}
function PayPalFee($amount = 0)
{	
	return ((floater($amount)/100)*2.2)+0.30;
}
function HandleBCN($update = array(),$wdata = array(),$wid = false)
{
	$CI =& get_instance();
	$CI->load->model('Auth_model');	
	$log_wdata = $wdata;
	if ($wid && (int)$wid > 0) $id = (int)$wid;	
	else $id = $wdata['wid'];
	
	$CI->db->select('wid, bcn, '.$this->sellingfields());
	$CI->db->where('wid', (int)$id);
	$dbw = $CI->db->get('warehouse');								
	if ($dbw->num_rows() > 0)
	{
		$w = $dbw->row_array();
		if (count($wdata) > 0)
		{
			foreach ($wdata as $k => $v)
			{
				if ($v == $w[$k]) unset($wdata[$k]);	
			}
			if (count($wdata) > 0)
			{
				$this->db->insert('infolog',  array('msg_type' => 'HBCN','msg_title' => 'HandleBCN Unmatch Data '.$id.' '.$_SERVER['REQUEST_URI'].' @'.CurrentTime(),
				 'msg_body' => printcool($wdata, TRUE,'$wdata'). printcool($w, TRUE,'$w'),
				 'msg_date' => CurrentTime(), 'msg_ts' => mktime()
				 ));
			}
		}
	}
	else $this->db->insert('infolog',  array('msg_type' => 'HBCN','msg_title' => 'HandleBCN NOT FOUND WID '.$id.' '.$_SERVER['REQUEST_URI'].' @'.CurrentTime(),
				 'msg_body' => '',
				 'msg_date' => CurrentTime(), 'msg_ts' => mktime()
				 ));
	
	//ssc, asc, if db value is >0 and new value is 0, do not write
	
	$new['paid'] = $w['paid'];
	if (isset($update['paid'])) $new['paid'] = floater($update['paid']);	
		$new['cost'] = $w['cost'];
                
	$new['sellingfee'] = $w['sellingfee'];

            if (isset($update['fee'])) $new['sellingfee'] = floater($update['fee']);
            if (isset($update['sellingfee'])) $new['sellingfee'] = floater($update['sellingfee']);

        $new['shipped'] = $w['shipped'];
	if (isset($update['shipped'])) $new['shipped'] = floater($update['shipped']);
	if (isset($update['ssc'])) $new['shipped'] = floater($update['ssc']);
	
	$new['shipped_actual'] = $w['shipped_actual'];
	if (isset($update['asc'])) $new['shipped_actual'] = floater($update['asc']);
	if (isset($update['shipped_actual'])) $new['shipped_actual'] = floater($update['shipped_actual']);
	
		//$new['netprofit'] = $w['netprofit'];
		//$new['paypal_fee'] = $w['paypal_fee'];
	$new['shipped_inbound'] = $w['shipped_inbound'];
	if (isset($update['shipped_inbound'])) $new['shipped_inbound'] = floater($update['shipped_inbound']);
	
	if (isset($update['paid']) && $update['paid'] != $new['paid']) $new['paid_date'] = CurrentTime();	

	if ($w['shipped'] > 0 && (floater($new['shipped']) == 0 || floater($new['shipped']) == 0.00))
	{
		$this->db->insert('infolog',  array('msg_type' => 'HBCN','msg_title' => 'HandleBCN '.$w['wid'].' Smaller SSC '.$_SERVER['REQUEST_URI'].' @'.CurrentTime(),
				 'msg_body' => printcool($new, TRUE,'new').printcool($w, TRUE,'w'),
				 'msg_date' => CurrentTime(), 'msg_ts' => mktime()
				 ));	
				 
		 $new['shipped'] = $w['shipped'];
	}
	if ($w['shipped_actual'] > 0 && (floater($new['shipped_actual']) == 0 || floater($new['shipped_actual']) == 0.00))
	{
		$this->db->insert('infolog',  array('msg_type' => 'HBCN','msg_title' => 'HandleBCN '.$w['wid'].' Smaller ASC '.$_SERVER['REQUEST_URI'].' @'.CurrentTime(),
				 'msg_body' => printcool($new, TRUE,'new').printcool($w, TRUE,'w'),
				 'msg_date' => CurrentTime(), 'msg_ts' => mktime()
				 ));
				 
		 $new['shipped_actual'] = $w['shipped_actual'];
	}
	if ($w['sellingfee'] > 0 && (floater($new['sellingfee']) == 0 || floater($new['sellingfee']) == 0.00))
	{
		$this->db->insert('infolog',  array('msg_type' => 'HBCN','msg_title' => 'HandleBCN '.$w['wid'].' Smaller Fee '.$_SERVER['REQUEST_URI'].' @'.CurrentTime(),
				 'msg_body' => printcool($new, TRUE,'new').printcool($w, TRUE,'w'),
				 'msg_date' => CurrentTime(), 'msg_ts' => mktime()
				 ));
				 
		 $new['sellingfee'] = $w['sellingfee'];
	}


	if ($w['channel'] == 4) $new['paypal_fee'] =0;
	else $new['paypal_fee'] = $this->PayPalFee(((float)$new['paid']+(float)$new['shipped_actual']));
	$new['netprofit'] = $this->NetProfitCalc((float)$new['paid'], (float)$new['shipped'], (float)$new['shipped_inbound'], (float)$new['cost'], (float)$new['sellingfee'], (float)$new['shipped_actual'],$new['paypal_fee']);
	
	if (count($update) > 0 && count($log_wdata) >0) 
	{
		/*GoMail(array('msg_title' => 'HandleBCN '.$w['wid'].' '.$_SERVER['REQUEST_URI'].' @'.CurrentTime(),
				 'msg_body' => printcool($update, TRUE,'Update'). printcool($log_wdata, TRUE,'log_wdata'). printcool($new, TRUE,'new'),
				 'msg_date' => CurrentTime()			
				 ), $this->config->config['support_email'], $this->config->config['no_reply_email']);
			*/	 
				 $this->db->insert('infolog',  array('msg_type' => 'HBCN','msg_title' => 'HandleBCN '.$w['wid'].' '.$_SERVER['REQUEST_URI'].' @'.CurrentTime(),
				 'msg_body' => printcool($update, TRUE,'Update'). printcool($log_wdata, TRUE,'log_wdata'). printcool($new, TRUE,'new'),
				 'msg_date' => CurrentTime(), 'msg_ts' => mktime()
				 ));
	}
	else
	{
		/*GoMail(array('msg_title' => 'HandleBCN '.$w['wid'].' '.$_SERVER['REQUEST_URI'].' @'.CurrentTime(),
				 'msg_body' => printcool($w, TRUE,'w'). printcool($new, TRUE,'new'),
				 'msg_date' => CurrentTime()			
				 ), $this->config->config['support_email'], $this->config->config['no_reply_email']);	*/
				 
				 $this->db->insert('infolog',  array('msg_type' => 'HBCN','msg_title' => 'HandleBCN '.$w['wid'].' '.$_SERVER['REQUEST_URI'].' @'.CurrentTime(),
				 'msg_body' => printcool($w, TRUE,'w'). printcool($new, TRUE,'new'),
				 'msg_date' => CurrentTime(), 'msg_ts' => mktime()
				 ));
	}
	
	
	foreach ($new as $k => $v)
	{
	  if ($v != $w[$k]) $CI->Auth_model->wlog($w['bcn'], $w['wid'], $k, $w[$k], $v);	
	}
	if (count($new) > 0)$this->db->update('warehouse', $new, array('wid' => $w['wid']));
        if ($w['channel'] == 4 && $w['vended'] == 1)
        {
            $this->Details_Sold($new,$id,$w['sold_id'], $w['channel'],TRUE);
        
        }
}
}
?>