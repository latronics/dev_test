<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mywarehouse_model extends Model 
{
    function Mywarehouse_model()
    {
        parent::Model();
    }
	function getAudits($page, $actionid = 0, $wlisting = 0)
	{	
		if ((int)$page > 0) $page--;
		
		if ((int)$actionid > 0)
		{
			$this->db->where('action_id', (int)$actionid);
			if ((int)$wlisting > 0) { $this->db->or_where('cur_eid', (int)$actionid);}
			else $this->db->where('wlisting', 0);
		}					
		$this->db->order_by("wal_id", "DESC");
		$this->db->limit(300, (int)$page*300);
		$this->query = $this->db->get('warehouse_audits');
		if ((int)$actionid > 0)
		{
			$this->db->where('action_id', (int)$actionid);
			if ((int)$wlisting > 0) $this->db->where('wlisting', 1);
			else $this->db->where('wlisting', 0);
		}			
		$countall = $this->db->count_all_results('warehouse_audits');
		$pages = ceil($countall/300);
		for ( $counter = 1; $counter <= $pages ; $counter++) $pagearray[] = $counter;
		if ($this->query->num_rows() > 0) return array('results' => $this->query->result_array(), 'pages' => $pagearray);		
	}
	function DistinctAllAucId()
	{
		$this->db->select("distinct aucid", false);
		$this->query = $this->db->get('warehouse');
		if ($this->query->num_rows() > 0) return $this->query->num_rows();
		else return 0;
	}
	function processbcnsfromorder($id, $type = 'ebay')
	{
		$on = TRUE;
		$debug = false;
		$warehouse = array();
		if ($type == 'ebay')
		{
			$this->db->select('paid,fee,shipping,paidtime,paydata,itemid,buyerid,buyeremail,sn,asc,ssc');	
			$this->db->where('rec', (int)$id);
			$q = $this->db->get('ebay_transactions');
			if ($q->num_rows() > 0) 
			{
				$res = $q->row_array();
				if (trim($res['sn']) != '')
				{
					$res['sn'] = explode(',', $res['sn']);
					foreach ($res['sn'] as $w)
					{
						$warehouse[] = array(				
									'bcn' => trim($w),
									'sold_date' => $res['paidtime'],
									'sold' => 'eBay', 
									'paid' => $res['paid'],
									'shipped' => $res['ssc'],
									'ordernotes' => 'Transaction '.$id,
									'sellingfee' => $res['fee'],		
									'status' => 'Sold'	
									);				
					}
				}
			}
		}
		elseif ($type == 'website')
		{
			$this->db->select('endprice, endprice_delivery, complete_time, order');
			$this->db->where('oid', (int)$id);
			$t = $this->db->get('orders');
			if ($t->num_rows() > 0) 
			{
				 $tr = $t->row_array();
				 $tr['order'] = unserialize($tr['order']);
				 if (count($tr['order']) >0)
				 {
					foreach ($tr['order'] as $k => $v)
					{
					 	if (trim($v['sn']) != '')
						{
							$bcns = explode(',', $v['sn']);
							if (count($bcns) > 0)
							{
								foreach ($bcns as $w)
								{
									$warehouse[] = array(				
												'bcn' => trim($w),
												'sold_date' => $tr['complete_time'],
												'sold' => 'LaTronics', 
												'paid' => $tr['endprice'],
												'shipped' => $tr['endprice_delivery'],
												'ordernotes' => 'Order '.$id,//
												'sellingfee' => 0,			
												'status' => 'Sold'	
												);				
								}							
							}
						}
					}
				 }				
			}			
		}
		
		if (count($warehouse) > 0)
		{
			if ($debug) printcool ($warehouse);
			$this->load->model('Auth_model');
			foreach ($warehouse as $w)
			{
				$this->db->select('wid, ordernotes, cost, status, status_notes, sold_date, sold, paid, shipped, sellingfee, netprofit');
				$this->db->where('bcn', trim($w['bcn']));
				$query = $this->db->get('warehouse');
				if ($debug) printcool ($w);
				if ($query->num_rows() > 0) 
				{
					$whitem = $query->row_array();
					if ($debug) printcool ($whitem);
					$bcn = trim($w['bcn']);
					unset($w['bcn']);
					$update = array();
					foreach ($w as $wk => $wv)
					{						
						if ($w[$wk] != $whitem[$wk])
						{							
							switch ($wk) 
							{
							case 'ordernotes':
								if ($whitem[$wk] != '') $update[$wk] = $w[$wk].' | '.$whitem[$wk]; 
								else $update[$wk] = $w[$wk];
								break;
							case 'status':
								$update[$wk] = $w[$wk];
								//if ($whitem['status_notes'] != '') $update['status_notes'] = 'Updated from "'.$whitem['status'].'" by OrderBCNUpdate | '.$whitem['status_notes'];
								//else
								 $update['status_notes'] = 'Updated from "'.$whitem['status'].'" by OrderBCNUpdate';
								break;							
							default:
								$update[$wk] = $w[$wk];
							}					
						}	
					}
					$update['netprofit'] = $w['paid']-($whitem['cost']+$w['shipped']+$w['sellingfee']);
					$flash = '';
					foreach ($update as $field => $value)
					{
						if ($update[$field] != $whitem[$field])
						{
							if ($debug) printcool ($bcn.'|'.$whitem['wid'].'|'.$field.'|'.$whitem[$field].'|'.$update[$field]);
							elseif ($on && !$debug) $this->Auth_model->wlog($bcn, $whitem['wid'], $field, $whitem[$field], $update[$field]);
							$flash .= strtoupper($field).' Updated to '.$update[$field].' for BCN '.$bcn.'<Br>';
						}
					}
					if ($debug) printcool ($update);
					elseif ($on && !$debug) $this->db->update('warehouse', $update, array('wid' => (int)$whitem['wid']));
					$this->session->set_flashdata('success_msg', $flash);	
				}				
			}			 
		}		
	}
	function getskudata($id)
	{
		$this->db->where('wsid', (int)$id);
		$s = $this->db->get('warehouse_sku');
		if ($s->num_rows() > 0) return $s->row_array();
	}
	function getnextsku()
	{
		$this->db->select('seq');
		$this->db->where('is_p', 1);
		$this->db->order_by("seq", "DESC");
		$this->db->limit(1);
		$this->query = $this->db->get('warehouse_sku');
		if ($this->query->num_rows() > 0)
		{	$sku = $this->query->row_array();
			return ((int)$sku['seq']);
		}
		else return 0;
	}
	function GetBCNDetails($id)
	{
		$this->db->where('wid', (int)$id);
		$this->query = $this->db->get('warehouse');
		if ($this->query->num_rows() > 0) 
		{
			$bcn = $this->query->row_array();
			$logs = false;
				$this->db->where('wid', (int)$id);
				$this->db->order_by("wl_id", "DESC");
				$this->wquery = $this->db->get('warehouse_log');
				if ($this->wquery->num_rows() > 0) 
				{
					$logs = $this->wquery->result_array();
				}
			return array('bcn' => $bcn, 'logs' => $logs);
		}				
	}
	function getsku($id)
	{
		$this->db->select('name');
		$this->db->where('wsid', (int)$id);
		$this->query = $this->db->get('warehouse_sku');
		if ($this->query->num_rows() > 0) 
		{
			$sku = $this->query->row_array();
			return ($sku['name']);
		}else return 'None';
	}
	function GetSKUImage($id)
	{
		$this->db->select('img');
		$this->db->where('wsid', (int)$id);
		$this->query = $this->db->get('warehouse_sku');
		if ($this->query->num_rows() > 0)
		{
			$img  = $this->query->row_array();
			if (trim($img['img']) != '') return trim($img['img']);
		}
	}
	function getlistingskucount($sku)
	{
		$this->db->select('listing');
		$this->db->where('wsid', (int)$sku);
		$q1 = $this->db->get('warehouse_sku_listing');
		if ($q1->num_rows() > 0)
		{
			//$this->db->select("wid");
			//$start = 1;
			$sqlstr = 'WHERE `deleted` = 0 AND `nr` = 0 AND `vended` = 0 AND (';
			foreach ($q1->result_array() as $l)
			{
				//if ($start == 1) $this->db->where('listingid', (int)$l['listing']);
				//else $this->db->or_where('listingid', (int)$l['listing']);
				
				$sqlstr .= 'listingid = '.(int)$l['listing'].' OR ';				
				//$start++;
			}
		}
		else return 0;
		$sqlstr = rtrim( $sqlstr,'OR ');		 
		$q = $this->db->query('SELECT wid FROM warehouse '.$sqlstr.')');
		//$q = $this->db->get('warehouse');	
		if ($q->num_rows() > 0) return ($q->num_rows());
		else return 0;		
	}
	function getlistingandskucount($sku)
	{
		$this->db->select('listing');
		$this->db->where('wsid', (int)$sku);
		$q1 = $this->db->get('warehouse_sku_listing');
		$bcn = 0;
		if ($q1->num_rows() > 0)
		{
			$listings = $q1->num_rows();
			//$this->db->select("wid");
			//$start = 1;
			$sqlstr = 'WHERE `deleted` = 0 AND `nr` = 0 AND `vended` = 0 AND (';
			foreach ($q1->result_array() as $l)
			{
				//if ($start == 1) $this->db->where('listingid', (int)$l['listing']);
				//else $this->db->or_where('listingid', (int)$l['listing']);
				
				$sqlstr .= 'listingid = '.(int)$l['listing'].' OR ';				
				//$start++;
			}	
			$sqlstr = rtrim( $sqlstr,'OR ').')';	
			$q = $this->db->query('SELECT wid FROM warehouse '.$sqlstr);
			//$q = $this->db->get('warehouse');	
			if ($q->num_rows() > 0) $bcn = $q->num_rows();
		}
		else $listings = 0;

		return array('listings' => $listings, 'bcn' => $bcn );
		
	}
	function seeksku ($label = '')
	{
		$this->db->select('wsid');
		$this->db->where('name', trim($label));
		$q1 = $this->db->get('warehouse_sku');
		if ($q1->num_rows() > 0)
		{
			$wsid = $q1->row_array();
			return ($wsid['wsid']);
		}
		else
		{
			$this->db->insert('warehouse_sku', array('name' => trim($label), 'title' => ' ', 'nfg' => 1, 'datetime' => CurrentTime(), 'admin' => (int)$this->session->userdata['admin_id']));
			return $this->db->insert_id();
		}
	}
	function GetLog($date, $admin)
	{
		if ($admin) $this->db->where('admin', (int)$admin);
		$date = explode('/', $date);
		$this->db->where('day', (int)$date[1]);
		$this->db->where('month', (int)$date[0]);
		$this->db->where('year', (int)$date[2]);

		$this->db->order_by("wl_id", "DESC");
		$wl = $this->db->get('warehouse_log');
		if ($wl->num_rows() > 0) 
		{
			return $wl->result_array();
		}		
	}
	function GetBCNLog($wid, $date, $admin)
	{
		$this->db->where('wid', (int)$wid);
		if ($date)
		{
			$date = explode('/', $date);
			$this->db->where('day', (int)$date[1]);
			$this->db->where('month', (int)$date[0]);
			$this->db->where('year', (int)$date[2]);	
		}
		if ($admin) $this->db->where('admin', (int)$admin);
		$this->db->order_by("wl_id", "DESC");
		$wl = $this->db->get('warehouse_log');
		if ($wl->num_rows() > 0) 
		{
			return $wl->result_array();
		}		
	}
	function GetOrder($id)
	{
		$this->db->where("woid", (int)$id);
		$o = $this->db->get('warehouse_orders');
		if ($o->num_rows() > 0) 
		{
			return $o->row_array();
		}	
	}
	function GetWIDs($wids = array())
	{
		if (count($wids) == 0) return false;
		$this->db->select('wid, sku , bcn, title, paid, shipped, orderid, ordernotes');
		$this->db->order_by("wid", "DESC");
		$c = 1;
		foreach ($wids as $k => $v)
		{
			if ($c == 0) $this->db->where('wid', (int)$v);
			else $this->db->or_where('wid', (int)$v);
			$c++;
		}
		$this->query = $this->db->get('warehouse');
		if ($this->query->num_rows() > 0) 
		{
			return $this->query->result_array();
		}		
	}
	function getwarehousepricing($auction)
	{
		$this->db->select('wid, cost, bcn');
		$this->db->where("bcn_p3", NULL);	
		$this->db->where("deleted", 0);	
		$this->db->where("nr", 0);		
		$this->db->where('waid', (int)$auction);
		$this->db->order_by("wid", "DESC");
		$this->query = $this->db->get('warehouse');
		if ($this->query->num_rows() > 0) 
		{
			return $this->query->result_array();
		}			
		
	}
	function getcostdata($auction)
	{
		$this->db->select("wcost");
		$this->db->where("waid", (int)$auction);
		$this->query = $this->db->get('warehouse_auctions');
		if ($this->query->num_rows() > 0) 
		{
			$c = $this->query->result_array();
			if ($c['wcost'] == '') return '';
			else return ' | '.$c['wcost'];
		}
	}
	function GetOrderWIDs($id)
	{
		$this->db->select('wid, sku , bcn, title, paid, shipped, orderid, ordernotes');
		$this->db->order_by("wid", "DESC");
		$this->db->where('orderid', (int)$id);
		$this->query = $this->db->get('warehouse');
		if ($this->query->num_rows() > 0) 
		{
			return $this->query->result_array();
		}		
	}
	function SearchListings($string)
	{	
		$sql = 'SELECT e_id, e_title, e_img1, idpath FROM ebay WHERE (';	
		$cn = 1;
		$es = explode(' ', trim($string));
		foreach ($es as $e)
		{
			if ($cn == 1)  $sql .= '(`e_title` LIKE "%'.trim($e).'%")';
			else $sql .= ' AND (`e_title` LIKE "%'.trim($e).'%")';
			/*
			if ($cn == 1)  $sql .= '(`e_title` LIKE "%'.trim($e).'%")';
			else $sql .= ' AND (`e_title` LIKE "%'.trim($e).'%")';
			*/
		$cn++;
		}
		$sql .= ') ORDER BY e_id DESC';
		$this->query = $this->db->query($sql);
		if ($this->query->num_rows() > 0)
		{
			$d['l'] = array();
			$d['w'] = array();
			$sql = 'SELECT wid, bcn, listingid FROM warehouse WHERE `deleted` = 0 AND `nr` = 0 AND `vended` = 0 AND (';
			$c = 1;
			foreach($this->query->result_array() as $r)
			{
				$d['l'][] = $r;	
				if ($c == 1) $sql .= '`listingid` = '.(int)$r['e_id'];
				else $sql .= ' AND `listingid` = '.(int)$r['e_id'];
				$c++;				
			}
			$sql .= ') ORDER BY waid DESC, generic ASC, bcn ASC';
			$q =  $this->db->query($sql);
			
			if ($q->num_rows() > 0)
			{
				foreach ($q->result_array() as $b)
				{
					$d['w'][$b['listingid']][] = $b;
				}
			}	
			return $d;		
		}
	}
	function SearchBCN($string)
	{	
		$this->db->select("wid, bcn, title");	
		$this->db->like('title', $string);
		$this->db->order_by("wid", "DESC");
		$this->query = $this->db->get('warehouse');
		if ($this->query->num_rows() > 0) return $this->query->result_array();
	}
	function PartingBCN($wid)
	{
		$this->db->select("wid, waid, bcn, bcn_p1, bcn_p2, bcn_p3, aucid, status, status_notes, prevstatus");	
		$this->db->where('wid', (int)$wid);

		$this->query = $this->db->get('warehouse');
		if ($this->query->num_rows() > 0)
		{
			$found = $this->query->row_array();
			$this->db->select("bcn_p3");	
			$this->db->where('bcn_p1', (int)$found['bcn_p1']);
			$this->db->where('bcn_p2', (int)$found['bcn_p2']);
			$this->db->order_by("wid", "DESC");
			$f = $this->db->get('warehouse');
			if ($f->num_rows() > 0)
			{
				$r = $f->row_array();
				$found['bcn_p3'] = $r['bcn_p3'];
				return $found;
			}	
		}
	}
	function GetSkuTitle($sku)
	{
		$this->db->select('title');
		$this->db->where('wsid', (int)$sku);
		$q = $this->db->get('warehouse_sku');
		if ($q->num_rows() > 0)
		{
			$name = $q->row_array();
			return ($name['title']);
		}
		else return '';
	}
	function SearchSKUS($str)
	{

		if (trim($str) == '') $srch = '';
		else $srch = ' AND (`title` LIKE "%'.$str.'%" OR `name` LIKE "%'.$str.'%" OR `upc` LIKE "%'.$str.'%") ';
		

		//$this->db->where('parent', 0);
		//$this->db->where('is_p', 0);	
		//$this->db->order_by('wsid', 'ASC');
		//$q = $this->db->get('warehouse_sku');
		$q = $this->db->query('SELECT * FROM warehouse_sku WHERE `is_p` = 0 AND `parent` = 0 '.$srch.' ORDER BY `wsid` ASC');
		$children = false;
		$parted = array();
		$bcncnt = array();
		$lstcnt = array();
		
		if ($q->num_rows() > 0)
		{
			//$c = 1; 
			$fnd = $q->result_array();	
		
			//$fnd[] = array('wsid' => 0);
			$csql = '';
			
			foreach ($fnd as $r)
			{
				$csql .= 'parent = '.(int)$r['wsid'].' OR ';
				//if ($c == 1) $this->db->where('parent', $r['wsid']);
				//else $this->db->or_where('parent', $r['wsid']);
				//$c++;
				$parents[$r['wsid']] = $r;
				if (trim($r['status']) == 'Parted' && $r['is_p'] == 0)
				{
					if (isset($parted[$r['sku']])) $parted[$r['sku']]++;
					else $parted[$r['sku']] = 1;
				}
			}
			$qp = $this->db->query('SELECT * FROM warehouse_sku WHERE `is_p` = 1 AND ('.rtrim($csql, ' OR ').') ORDER BY `wsid` ASC');
			//$qp = $this->db->get('warehouse_sku');
			
			if ($qp->num_rows() > 0)
			{					
				foreach ($qp->result_array() as $rp)
				{
					$children[$rp['parent']][$rp['wsid']] = $rp;
				}
			}
						
			
		}
		$revparents = array();
		
		//if (trim($str) != '') $this->db->like('title', $str);
		//$this->db->where('is_p', 1);
		//$this->db->order_by('wsid', 'ASC');
		//$qpl = $this->db->get('warehouse_sku');
		$qpl = $this->db->query('SELECT * FROM warehouse_sku WHERE `is_p` = 1 '.$srch.' ORDER BY `wsid` ASC');
		//$qpl = $this->db->query('SELECT * FROM warehouse_sku WHERE `is_p` != 0 AND `title` LIKE % '.$str.' %');
			if ($qpl->num_rows() > 0)
			{					
				foreach ($qpl->result_array() as $rp)
				{
					$children[$rp['parent']][$rp['wsid']] = $rp;
					$revparents[$rp['parent']] = TRUE;
				}
			}
		unset($revparents[0]);
		if (count($revparents) > 0)
		{
			$rsql = 'wsid = '.(int)$r['wsid'].' OR ';
			$rqp = $this->db->query('SELECT * FROM warehouse_sku WHERE `is_p` = 0 AND ('.rtrim($rsql, ' OR ').') ORDER BY `wsid` ASC');
			if ($rqp->num_rows() > 0)
				{					
					foreach ($rqp->result_array() as $rrp)
					{
						$parents[$rrp['wsid']] = $rrp;
						if (trim($rpp['status']) == 'Parted' && $rpp['is_p'] == 0)
						{
							if (isset($parted[$rpp['sku']])) $parted[$rpp['sku']]++;
							else $parted[$rpp['sku']] = 1;
						}
					}
				}
		}
		
		//$qp = $this->db->query('SELECT * FROM warehouse_sku WHERE `is_p` = 1 AND ('.rtrim($csql, ' OR ').') ORDER BY `wsid` ASC');
		//if (trim($str) != '') $this->db->like('title', $str);
		//$this->db->where('is_p', 1);
		//$this->db->where('parent', 0);
		//$this->db->order_by('wsid', 'ASC');
		//$qpl = $this->db->get('warehouse_sku');
		$qpl = $this->db->query('SELECT * FROM warehouse_sku WHERE `is_p` = 1 AND `parent` = 0 '.$srch.' ORDER BY `wsid` ASC');
		//$qpl = $this->db->query('SELECT * FROM warehouse_sku WHERE `is_p` != 0 AND `title` LIKE % '.$str.' %');
			if ($qpl->num_rows() > 0)
			{					
				foreach ($qpl->result_array() as $rp)
				{
					$children[$rp['parent']][$rp['wsid']] = $rp;
				}
			}
					
		if (count($children) > 0)
		{	
			
			
			
			$r2cnt = 1;
			$this->db->select("distinct l.*, e_id", false);
								
			foreach ($children as $r2) 
			{ 
				foreach ($r2 as $rr2) 
				{
				if ($r2cnt == 1) $this->db->where('l.wsid', $rr2['wsid']); 
				else $this->db->or_where('l.wsid', $rr2['wsid']); 
				$r2cnt++;
				}
			}			
				
			$this->db->order_by("l.wslid", "DESC");
			$this->db->join('ebay e', 'l.listing = e.e_id', 'LEFT');
			$q2 = $this->db->get('warehouse_sku_listing l');
			if ($q2->num_rows() > 0)
			{
				$res2 = $q2->result_array();
				$sqlstr = 'WHERE (';
				foreach ($res2 as $r3) 
				{
					$sqlstr .= 'listingid = '.(int)$r3['listing'].' OR ';
					$full2[$r3['wsid']][$r3['listing']] = TRUE;
					
					if (!isset($lstcnt[$r3['wsid']])) $lstcnt[$r3['wsid']] = 0;	
					$lstcnt[$r3['wsid']]++;

				}
				$sqlstr = rtrim( $sqlstr,'OR ');
				$q3 = $this->db->query('SELECT wid, listingid, status, sku, psku FROM warehouse '.$sqlstr.') AND `deleted` = 0 AND `nr` = 0 AND `vended` = 0 ORDER BY wid DESC');
				if ($q3->num_rows() > 0)
				{
					$res3 = $q3->result_array();
					foreach ($res3 as $fr3)
					{	
						if ((int)$fr3['psku'] > 0)
						{
							if (!isset($bcncnt[$fr3['psku']])) $bcncnt[$fr3['psku']] = 0;	
							$bcncnt[$fr3['psku']]++;
						}
						else
						{
							if (!isset($bcncnt[$fr3['sku']])) $bcncnt[$fr3['sku']] = 0;	
							$bcncnt[$fr3['sku']]++;
						}						
					}
				}
			}					
			//printcool (array('lstcnt' => $lstcnt, 'bcncnt' => $bcncnt, 'parted' => $parted));
			return array('parents' => $parents, 'children' => $children, 'lstcnt' => $lstcnt, 'bcncnt' => $bcncnt, 'parted' => $parted);
		}
	}
	function GetSkusAndListingsAndBCNs($wid, $sku = false)
	{
		$res1 = $full2 = $full3 = $bcncnt = false;

		if (!$sku)
		{
		$this->db->where('parent', (int)$wid);
		$this->db->order_by("wsid", "DESC");
		$q1 = $this->db->get('warehouse_sku');
		}
		else $res1[$sku] = array('wsid' => $sku);
		if ((isset($q1) && $q1->num_rows() > 0) || $sku)
		{
			if (!$sku) foreach ($q1->result_array() as $r1) $res1[$r1['wsid']] = $r1;
			
			$r2cnt = 1;
			
			$this->db->select("distinct l.*, e_title, e_condition, `Condition`, idpath, e_img1, ebay_submitted, ebay_id, ebended, endedreason", false);
						
			foreach ($res1 as $r2) 
			{ 
				if ($r2cnt == 1) $this->db->where('l.wsid', $r2['wsid']); 
				else $this->db->or_where('l.wsid', $r2['wsid']); 
				$r2cnt++;
			}			
			
			$this->db->order_by("l.wslid", "DESC");
			$this->db->join('ebay e', 'l.listing = e.e_id', 'LEFT');
			$q2 = $this->db->get('warehouse_sku_listing l');

			//$this->db->order_by("wslid", "DESC");
			//$q2= $this->db->get('warehouse_sku_listing');
			if ($q2->num_rows() > 0)
			{
				$res2 = $q2->result_array();
				//$this->db->select("wid, waid, bcn, bcn_p1, bcn_p2, bcn_p3, psku, listingid, aucid, status");
				$r3cnt = 1;		
				$sqlstr = 'WHERE (';
				foreach ($res2 as $r3) 
				{
					//if ($r3cnt == 1) $this->db->where('listingid', (int)$r3['listing']);
					//else $this->db->or_where('listingid', (int)$r3['listing']);
					$sqlstr .= 'listingid = '.(int)$r3['listing'].' OR ';	
					$r3cnt++;
					
					
					$r3['e_title'] = substr($r3['e_title'],0,120);
					if ($r3['Condition'] == '1000') $r3['Condition'] = 'New';
		  			elseif ($r3['Condition'] == '1500') $r3['Condition'] = 'New other (see details)';
		    		elseif ($r3['Condition'] == '1750') $r3['Condition'] = 'New with defects';
				    elseif ($r3['Condition'] == '2000') $r3['Condition'] = 'Manufacturer refurbished';
				    elseif ($r3['Condition'] == '2500') $r3['Condition'] = 'Seller refurbished';
				    elseif ($r3['Condition'] == '3000') $r3['Condition'] = 'Used';
				  	elseif ($r3['Condition'] == '4000') $r3['Condition'] = 'Very Good';
				    elseif ($r3['Condition'] == '5000') $r3['Condition'] = 'Good';
				    elseif ($r3['Condition'] == '6000') $r3['Condition'] = 'Acceptable';
				    elseif ($r3['Condition'] == '7000') $r3['Condition'] = 'For parts or not working';
					else $r3['Condition'] = 'Undefined ('.$r3['Condition'].')';
					$full2[$r3['wsid']][$r3['listing']] = $r3;
				}
				$sqlstr = rtrim( $sqlstr,'OR ');
				$q3 = $this->db->query('SELECT wid, waid, bcn,oldbcn, bcn_p1, bcn_p2, bcn_p3, psku, listingid, aucid, status ,title, status_notes, generic, regen, history, audit, location, waid, channel FROM warehouse '.$sqlstr.') AND `deleted` = 0 AND `nr` = 0 AND `vended` = 0 ORDER BY wid DESC');
				//$this->db->order_by("wid", "DESC");
				//$q3= $this->db->get('warehouse');
				if ($q3->num_rows() > 0)
				{
					$res3 = $q3->result_array();
					foreach ($res3 as $fr3)
					{	
						$full3[$fr3['listingid']][] = $fr3;
					}
				}
				if (isset($full2))
				{
					foreach ($full2 as $k => $v)
					{
						$bcncnt[$k] = 0;
						foreach ($v as $vv)
						{
							if (isset($full3[$vv['listing']])) $bcncnt[$k] = $bcncnt[$k] + count($full3[$vv['listing']]);
						}
					}					
				}
			}	
		}

		return array('sku' => $res1, 'listings' => $full2, 'bcn' => $full3, 'bcncnt' => $bcncnt);
	
	}
	function GetUpdateLocation($wid)
	{
		$res1 = $full2 = false;

		$this->db->where('parent', (int)$wid);
		$this->db->order_by("wsid", "DESC");
		$q1 = $this->db->get('warehouse_sku');
		if ($q1->num_rows() > 0)
		{
			foreach ($q1->result_array() as $r1) $res1[$r1['wsid']] = $r1;
			$r2cnt = 1;
			
			$this->db->select("distinct l.*, e_title", false);
						
			foreach ($res1 as $r2) 
			{ 
				if ($r2cnt == 1) $this->db->where('l.wsid', $r2['wsid']); 
				else $this->db->or_where('l.wsid', $r2['wsid']); 
				$r2cnt++;
			}			
			
			$this->db->order_by("l.wslid", "DESC");
			$this->db->join('ebay e', 'l.listing = e.e_id', 'LEFT');
			$q2 = $this->db->get('warehouse_sku_listing l');
			if ($q2->num_rows() > 0)
			{
				$res2 = $q2->result_array();	
				
				$r3cnt = 1;		
				$sqlstr = 'WHERE (';
				foreach ($res2 as $r3) 
				{
					$sqlstr .= 'listingid = '.(int)$r3['listing'].' OR ';	
					$r3cnt++;
					
					$full2[$r3['wsid']][$r3['listing']] = $r3;
				}
				$sqlstr = rtrim( $sqlstr,'OR ');
				$q3 = $this->db->query('SELECT wid, bcn, location FROM warehouse '.$sqlstr.') AND `deleted` = 0 AND `nr` = 0 AND `vended` = 0 ORDER BY wid DESC');
				
				if ($q3->num_rows() > 0)
				{
					return $q3->result_array();					
				}
				
			}	
		}
	}
	function GetSKUS()
	{
		$this->db->select("distinct s.*, bcn", false);
		$this->db->order_by("wid", "DESC");
		$this->db->join('warehouse w', 's.wid = w.wid', 'LEFT');
		$q = $this->db->get('warehouse_sku s');
		if ($q->num_rows() > 0) return $q->result_array();	
	}
	function getbcnsforskulisting($sku, $listing)
	{
		/*$this->db->where('listing', $listing);
		$this->db->where('sku', $sku);
		$this->query = $this->db->get('warehouse_sku_listing_bcn');
		if ($this->query->num_rows() > 0) return $this->query->result_array();		*/
		
		
		/*
		
		$this->db->select("distinct b.*, bcn", false);
		$this->db->where('b.listing', $listing);
		$this->db->where('b.sku', $sku);
		$this->db->where('b.deleted', 0);
		$this->db->order_by("wid", "DESC");
		$this->db->join('warehouse w', 'b.wid = w.wid', 'LEFT');
		$q = $this->db->get('warehouse_sku_listing_bcn b');
		
		*/
				
		$this->db->select("wid, oldbcn, bcn, title, status, status_notes, generic, regen, history, waid, audit, location, channel");
		$this->db->where('listingid', $listing);
		//$this->db->where('bcn_p3 !=', '');
		//$this->db->where('waid >', '148');
		//$this->db->where('psku', $sku);
		$this->db->where('deleted', 0);
		$this->db->where('nr', 0);
		$this->db->where('vended', 0);
		$this->db->order_by("wid", "DESC");
		$q = $this->db->get('warehouse');
		if ($q->num_rows() > 0) return $q->result_array();	
		
	}
	function getskubcns($id, $is_p)
	{
		$this->db->select("wid, bcn, status, listingid, sku, psku");
		$this->db->where('deleted', 0);
		$this->db->where('nr', 0);
		if ((int)$is_p == 1) $this->db->where('psku', (int)$id);
		else $this->db->where('sku', (int)$id);
		$this->db->order_by("wid", "DESC");
		$q = $this->db->get('warehouse');
		if ($q->num_rows() > 0) return $q->result_array();			
	}
	function getskulistings($id)
	{
			$this->db->select("distinct l.*, e.*", false);
			$this->db->where('l.wsid', (int)$id); 			
			$this->db->order_by("e.e_id", "DESC");
			$this->db->join('ebay e', 'l.listing = e.e_id', 'LEFT');
			$q2 = $this->db->get('warehouse_sku_listing l');
			if ($q2->num_rows() > 0)
			{
				$r = $q2->result_array();	
				foreach ($r as $rr)	$idarray[] = $rr['e_id'];	
				if (isset($idarray))
				{
					$CI =& get_instance();
					$CI->load->model('Myseller_model'); 	
					$CI->Myseller_model->getBase($idarray);
					$CI->Myseller_model->getOnHold($idarray);				
				}
				return $r;
			}		
	}
	function GetListingTitleAndCondition($listing, $onlytitle = false)
	{
		if ($onlytitle) $this->db->select("e_title");	
		else $this->db->select("e_title, e_condition, Condition, e_img1, idpath, ebay_submitted, ebay_id, ebended, endedreason");	
		$this->db->where('e_id', (int)$listing);
		$this->query = $this->db->get('ebay');
		if ($this->query->num_rows() > 0) 
		{
			if ($onlytitle)
			{
				$t = $this->query->row_array();
				return $t['e_title'];
			}
			else return $this->query->row_array();			
		}
	}	
	function HandleAuction($data)
	{
		$this->db->select("waid, wcost, wdate , wvendor, wnotes");
		$this->db->where("wtitle", trim($data['wtitle']));
		$this->db->where("deleted", 0);		
		$this->query = $this->db->get('warehouse_auctions');
		if ($this->query->num_rows() > 0) 
		{
			$a = $this->query->row_array();
			
			if ($a['wcost'] != '') $a['wcost'] = $data['wcost']+$a['wcost'];
			//else $a['wcost'] = $data['wcost'];
			
			$a['shipping'] = $data['shipping']+$a['shipping'];
			$a['expenses'] = $data['expenses']+$a['expenses'];
			
			if ($a['wdate'] != '') $a['wdate'] = $data['wdate'].' | '.$a['wdate'];
			else $a['wdate'] = $data['wdate'];
			
			if ($a['wvendor'] != '') $a['wvendor'] = $data['wvendor'].' | '.$a['wvendor'];
			else $a['wvendor'] = $data['wvendor'];
			
			if ($a['wnotes'] != '') $a['wnotes'] = $data['wnotes'].'<br>---<br>'.$a['wnotes'];
			else $a['wnotes'] = $data['wnotes'];
			
			if ($a['wnotes'] != '') $a['wnotes'] = $data['wnotes'].'<br>---<br>'.$a['wnotes'];
			else $a['wnotes'] = $data['wnotes'];
			
			$this->db->update('warehouse_auctions', $a, array('waid' => (int)$a['waid']));
			return $a['waid'];
		}
		else
		{
			$this->db->insert('warehouse_auctions', $data);
	 		return $this->db->insert_id(); 
		}
		
	}
	function GetList($page = '', $cat)
	{
		$limit = 50;
		if ((int)$page > 0) $page--;
		$this->db->order_by("waid", "DESC");
		$this->db->where("deleted", 0);
		$this->db->where("wacat", (int)$cat);				
		
		$this->db->limit($limit, (int)$page*$limit);
		$this->query = $this->db->get('warehouse_auctions');
		
		$this->db->where("deleted", 0);
		$this->db->where("wacat", (int)$cat);
		
		$countall = $this->db->count_all_results('warehouse_auctions');
		$pages = ceil($countall/$limit);
		
		for ( $counter = 1; $counter <= $pages ; $counter++) $pagearray[] = $counter;
		if ($this->query->num_rows() > 0) 
		{
			$this->auctionresults = $this->query->result_array();
		
			$this->doaccounting();
			
			//printcool (array('cntaccounting' => $this->cntaccounting, 'cntaccounting_sold' => $this->cntaccounting_sold, 'cntaccounting_notsold' => $this->cntccounting_notsold, 'cnt' => $this->cnt, 'cnt_sold' => $this->cnt_sold, 'cnt_notsold' => $this->cnt_notsold));
			
			$this->calc->results = $this->auctionresults;
			$this->calc->pages = $pagearray;
			//printcool ($this->calc);
			return $this->calc;
			//return array('results' => $this->auctionresults, 'auctionshipping' => $this->auctionshipping, 'auctionexpenses' => $this->auctionexpenses, 'eachauctionshipping' => $this->eachauctionshipping, 'eachauctionexpenses' => $this->eachauctionexpenses, 'accounting' => $this->accounting, 'accounting_sold' => $this->accounting_sold, 'accounting_notsold' => $this->accounting_notsold, 'accounting_ns_hold' => $this->accounting_ns_hold, 'accounting_ns_other' => $this->accounting_ns_other, 'location' => $this->location, 'locationsum' => $this->locationsum, 'sn' => $this->sn, 'snsum' => $this->snsum,  'sumaccounting' => $this->sumaccounting, 'sumaccounting_sold' => $this->sumaccounting_sold, 'sumaccounting_notsold' => $this->sumaccounting_notsold, 'sumaccounting_ns_hold' => $this->sumaccounting_ns_hold, 'sumaccounting_ns_other' => $this->sumaccounting_ns_other, 'statuses' => $this->statuses, 'sumstatuses' => $this->sumstatuses, 'pages' => $pagearray, 'cntaccounting' => $this->cntaccounting, 'cntaccounting_sold' => $this->cntaccounting_sold, 'cntaccounting_notsold' => $this->cntaccounting_notsold, 'cntaccounting_ns_hold' => $this->cntaccounting_ns_hold,'cntaccounting_ns_other' => $this->cntaccounting_ns_other,'cnt' => $this->cnt, 'cnt_sold' => $this->cnt_sold, 'cnt_notsold' => $this->cnt_notsold, 'cnt_ns_hold' => $this->cnt_ns_hold, 'cnt_ns_other' => $this->cnt_ns_other);	
		}
	}
	function RunAccountingLoop($i, $arr, $cnt)
	{
		$this->calc->per->{(int)$i['waid']}->{$cnt}++;
		
		if (floater($i['paid']) > 0)$this->calc->per->{(int)$i['waid']}->{$arr}['paid'] = $this->calc->per->{(int)$i['waid']}->{$arr}['paid']+floater($i['paid']);		
		else $this->calc->per->{(int)$i['waid']}->{$arr}['emptypaid']++;								
		
		if (floater($i['cost']) > 0) $this->calc->per->{(int)$i['waid']}->{$arr}['cost'] = $this->calc->per->{(int)$i['waid']}->{$arr}['cost']+floater($i['cost']);
		else $this->calc->per->{(int)$i['waid']}->{$arr}['emptycost']++;								
		
		$this->calc->per->{(int)$i['waid']}->{$arr}['shipped'] = $this->calc->per->{(int)$i['waid']}->{$arr}['shipped']+floater($i['shipped']);
		$this->calc->per->{(int)$i['waid']}->{$arr}['shipped_actual'] = $this->calc->per->{(int)$i['waid']}->{$arr}['shipped_actual']+floater($i['shipped_actual']);
		$this->calc->per->{(int)$i['waid']}->{$arr}['shipped_inbound'] = $this->calc->per->{(int)$i['waid']}->{$arr}['shipped_inbound']+floater($i['shipped_inbound']);										
		$this->calc->per->{(int)$i['waid']}->{$arr}['sellingfee'] = $this->calc->per->{(int)$i['waid']}->{$arr}['sellingfee']+floater($i['sellingfee']);
		
		if (floater($i['netprofit']) == 0) $this->calc->per->{(int)$i['waid']}->{$arr}['emptynetprofit']++;
		else $this->calc->per->{(int)$i['waid']}->{$arr}['netprofit'] = $this->calc->per->{(int)$i['waid']}->{$arr}['netprofit']+floater($i['netprofit']);
		
		if (isset($this->calc->per->{(int)$i['waid']}->{$arr}['cnetprofit'])) $this->calc->per->{(int)$i['waid']}->{$arr}['cnetprofit'] = $this->calc->per->{(int)$i['waid']}->{$arr}['cnetprofit']+(floater($i['paid']) - floater($i['cost']));
		else $this->calc->per->{(int)$i['waid']}->{$arr}['cnetprofit'] = floater($i['paid']) - floater($i['cost']);
		
	}
	function doaccounting()
	{
		$wnavfrom = $this->session->userdata('wnavfrom');
		$wnavto = $this->session->userdata('wnavto');
		$winitial = $this->session->userdata('winitial');
		
		if ($winitial) $datefield = 'trans_mk';
		else $datefield = 'setshipped';
				if ($wnavfrom && $wnavto) 
				{
					$from = explode('/',$wnavfrom);
					$wnavfrom = mktime(23, 59, 59, $from[0], $from[1], $from[2]);
					$to = explode('/', $wnavto);			
					$wnavto = mktime(0, 0, 0, $to[0], $to[1], $to[2]);		
					
				}		
		$dataset = array('', '_sold', '_notsold','_ns_hold','_ns_other');
		$yn = array('Y' => 0, 'N' => 0);
		$extraset = array('statuses' => array(), 'location'=>$yn, 'sn'=>$yn);
		
			
		if (isset($this->auctionresults))
		{
			if ($this->session->userdata['type'] == 'master' || ($this->session->userdata['type'] != 'master' && $this->session->userdata['accounting'] == 1))
			{	
				$auctions = array();
				
				$this->calc->sum->auctionshipping = 0;
				$this->calc->sum->auctionexpenses = 0;
				
				foreach ($this->auctionresults as $ar)
				{
					 $auctions[] = $ar['waid'];
					 $this->calc->sum->auctionshipping = $this->calc->sum->auctionshipping + $ar['shipping'];
					 $this->calc->sum->auctionexpenses = $this->calc->sum->auctionexpenses + $ar['expenses'];					 
					 
					$this->calc->per->{(int)$ar['waid']}->auctionshipping = $ar['shipping'];
					$this->calc->per->{(int)$ar['waid']}->auctionexpenses = $ar['expenses'];
				}
				
				if (count($auctions) >0)
				{
					$showparts = (int)$this->session->userdata('showparts');
					$showparents = (int)$this->session->userdata('showparents');
					$fsql = '';
					if (!$showparts && !$showparents)
					{ 
						$showparents = TRUE;
						$this->session->set_userdata('showparents', 1);					
					}
					if (!$showparts) $fsql .= ' AND `bcn_p3` IS NULL';
					if (!$showparents) $fsql .= ' AND `bcn_p3` IS NOT NULL'; 
		
					$sql = 'SELECT waid, bcn, wid, paid, cost, netprofit, deleted, status, location, sn, shipped, shipped_actual, shipped_inbound, sellingfee, setshipped, trans_mk FROM warehouse WHERE `nr` = 0 AND  `deleted` = 0 '.$fsql.' AND (';
					
					$cnt = 1;
					$emptystart = array('paid' => (float)0, 'emptypaid' => 0, 'totalcost'=> 0, 'cost' => (float)0, 'shipped' => (float)0, 'shipped_actual' => (float)0, 'shipped_inbound' => (float)0, 'sellingfee' => (float)0, 'totalcost'=> (float)0,'emptycost' => 0, 'netprofit' => (float)0, 'emptynetprofit' => 0);
					
					
						
					foreach ($auctions as $a)
					{
						if ($cnt == 1) $sql .= '`waid` = '.(int)$a;
						else $sql .= ' OR `waid` = '.(int)$a;
						$cnt++;
						foreach ($dataset as $ds)
						{
							$nsa = 'accounting'.$ds;
							$nsc = 'cnt'.$ds;
							 $this->calc->per->{(int)$a}->{$nsa} = $emptystart;
							 $this->calc->per->{(int)$a}->{$nsc} = 0;
						}
						foreach ($extraset as $ek => $es)
						{							
							 $this->calc->per->{(int)$a}->{$ek} = $es;
						}
						/*
						$this->calc->accounting[(int)$a] = $emptystart;
						$this->calc->accounting_sold[(int)$a] = $emptystart;
						$this->calc->accounting_notsold[(int)$a] = $emptystart;
						$this->calc->accounting_ns_hold[(int)$a] = $emptystart;
						$this->calc->accounting_ns_other[(int)$a] = $emptystart;
						$this->calc->cnt[(int)$a] = 0;
						$this->calc->cnt_sold[(int)$a] = 0;
						$this->calc->cnt_notsold[(int)$a] = 0;
						$this->calc->cnt_ns_hold[(int)$a] = 0;
						$this->calc->cnt_ns_other[(int)$a] = 0;
						$this->calc->statuses[(int)$a] = array();
						$this->calc->location[(int)$a] = array('Y' => 0, 'N' => 0);
						$this->calc->locationsum = array('Y' => 0, 'N' => 0);
						$this->calc->sn[(int)$a] = array('Y' => 0, 'N' => 0);
						$this->calc->snsum = array('Y' => 0, 'N' => 0);
						*/
						
					}
					$sql .= ') ORDER BY `wid` DESC';
					$aq = $this->db->query($sql);
					
					if ($aq->num_rows() > 0) 
					{	
						foreach ($dataset as $ds)
						{
							$sa = 'accounting'.$ds;
							$ca = 'cnt'.$ds;
							 $this->calc->sum->{$sa} = $emptystart;
							 $this->calc->sum->{$ca} = 0;
						}
						foreach ($extraset as $ek => $es)
						{							
							 $this->calc->per->{(int)$a}->{$ek} = $es;
						}
						/*	
						$this->calc->sumaccounting = $emptystart;
						$this->calc->sumaccounting_sold = $emptystart;
						$this->calc->sumaccounting_notsold = $emptystart;
						$this->calc->sumaccounting_ns_hold = $emptystart;
						$this->calc->sumaccounting_ns_other = $emptystart;
						
						$this->calc->cntaccounting = 0;
						$this->calc->cntaccounting_sold = 0;
						$this->calc->cntaccounting_notsold = 0;
						$this->calc->cntaccounting_ns_hold = 0;
						$this->calc->cntaccounting_ns_other = 0;
						*/
						$cnnn = 0;
						foreach ($aq->result_array() as $i)
						{
							if ($i['deleted'] == 0)
							{
								$this->RunAccountingLoop($i, 'accounting', 'cnt');							
		
								if (($winitial && ($i['status'] == 'Sold' || $i['status'] == 'On Hold')) || $i['status'] == 'Sold')
								{									
									if ($wnavfrom && $wnavto) 
									{
										if ($i[$datefield] >= $wnavto && $i[$datefield] <= $wnavfrom)
										{
											$this->RunAccountingLoop($i, 'accounting_sold', 'cnt_sold');										
										}
									}
									else
									{
										$this->RunAccountingLoop($i, 'accounting_sold', 'cnt_sold');		
									}
								}
								else
								{
									$this->RunAccountingLoop($i, 'accounting_notsold', 'cnt_notsold');	
									
									if ($i['status'] == 'On Hold')
									{
										$this->RunAccountingLoop($i, 'accounting_ns_hold', 'cnt_ns_hold');											
									}
									else
									{
										$this->RunAccountingLoop($i, 'accounting_ns_other', 'cnt_ns_other');													
									}									
								}				
								if ($i['status'] == '')
								{
									if (isset($this->calc->per->{(int)$i['waid']}->statuses['Empty'])) $this->calc->per->{(int)$i['waid']}->statuses['Empty']++;
									else $this->calc->per->{(int)$i['waid']}->statuses['Empty'] = 1;									
								}
								elseif ($i['status'] == 'Sold' && $wnavfrom && $wnavto)
								{
									if ($i[$datefield] >= $wnavto && $i[$datefield] <= $wnavfrom) 
									{
										if (isset($this->calc->per->{(int)$i['waid']}->statuses[$i['status']])) $this->calc->per->{(int)$i['waid']}->statuses[$i['status']]++;
										else $this->calc->per->{(int)$i['waid']}->statuses[$i['status']] = 1;
									}
								}
								else
								{
										if (isset($this->calc->per->{(int)$i['waid']}->statuses[$i['status']])) $this->calc->per->{(int)$i['waid']}->statuses[$i['status']]++;
										else $this->calc->per->{(int)$i['waid']}->statuses[$i['status']] = 1;
								}
								if (trim($i['location']) == '') $this->calc->per->{(int)$i['waid']}->location['N']++;
								else $this->calc->per->{(int)$i['waid']}->location['Y']++; 
								
								if (trim($i['sn']) == '') $this->calc->per->{(int)$i['waid']}->sn['N']++;
								else $this->calc->per->{(int)$i['waid']}->sn['Y']++;
							}	
						}
						
						
						
							
						
						foreach ($dataset as $ds)
						{
							$theacc = 'accounting'.$ds;
							$thecnt = 'cnt'.$ds;

							foreach ($this->calc->per as $k => $v)
							{
								foreach ($v as $kk => $vv)
								{
									if ($kk == $thecnt) $this->calc->sum->{$thecnt} = (float)$this->calc->sum->{$thecnt}+(float)$vv;
									elseif ($kk == $theacc)
																		
									foreach ($vv as $kkk => $vvv)
									{						
										$this->calc->sum->{$theacc}[$kkk] = (float)$this->calc->sum->{$theacc}[$kk]+(float)$vvv;																			
									}
								}	
								foreach ($extraset as $ek => $es)
								{	
									 if (isset($v->$ek))
									 {										 
										foreach ($v->$ek as $kkk => $vvv)
										{						
											$this->calc->sum->{$ek}[$kkk] = $this->calc->sum->{$ek}[$kk]+(float)$vvv;																			
										}				
									 }
								}
															
							}
						}
						/*
						foreach ($this->calc->accounting as $k => $v)
								{
									$this->calc->cntaccounting = $this->calc->cntaccounting+$this->cnt[$k];									
									foreach ($v as $kk => $vv)
									{						
										$this->calc->sumaccounting[$kk] = $this->calc->sumaccounting[$kk]+$vv;																			
									}
								}	
						
						
													
						foreach ($this->calc->accounting_sold as $k => $v)
								{
									$this->calc->cntaccounting_sold = $this->calc->cntaccounting_sold+$this->calc->cnt_sold[$k];
									foreach ($v as $kk => $vv)
									{						
										$this->calc->sumaccounting_sold[$kk] = $this->calc->sumaccounting_sold[$kk]+$vv;
									}
								}								
						foreach ($this->calc->accounting_notsold as $k => $v)
								{
									$this->calc->cntaccounting_notsold = $this->calc->cntaccounting_notsold+$this->cnt_notsold[$k];									
									foreach ($v as $kk => $vv)
									{						
										$this->calc->sumaccounting_notsold[$kk] = $this->calc->sumaccounting_notsold[$kk]+$vv;
									}
								}								
						foreach ($this->calc->accounting_ns_hold as $k => $v)
								{
									$this->calc->cntaccounting_ns_hold = $this->calc->cntaccounting_ns_hold+$this->calc->cnt_ns_hold[$k];									
									foreach ($v as $kk => $vv)
									{						
										$this->calc->sumaccounting_ns_hold[$kk] = $this->calc->sumaccounting_ns_hold[$kk]+$vv;
									}
								}
						foreach ($this->calc->accounting_ns_other as $k => $v)
								{
									$this->calc->cntaccounting_ns_other = $this->calc->cntaccounting_ns_other+$this->calc->cnt_ns_other[$k];									
									foreach ($v as $kk => $vv)
									{						
										$this->calc->sumaccounting_ns_other[$kk] = $this->calc->sumaccounting_ns_other[$kk]+$vv;
									}
								}
						
							*/	
							
							//	printcool ($this->calc);	
					}
					foreach ($this->calc->per as $k => $v) $this->mysmarty->assign('cal', array($k =>(array)$v));	
				}
			}
		}		
	}
	
	/*function GetList($page = '')
	{
		$limit = 50;
		if ((int)$page > 0) $page--;		
		$this->db->select("distinct aucid", false);
		$this->db->order_by("wid", "DESC");
		$this->db->limit($limit, (int)$page*$limit);
		$this->query = $this->db->get('warehouse');

		$pages = ceil($this->DistinctAllAucId()/$limit);
		for ( $counter = 1; $counter <= $pages ; $counter++) $pagearray[] = $counter;
		if ($this->query->num_rows() > 0) 
		{
			$r = array();

			foreach ($this->query->result_array() as $k => $l)
			{
				if ($k == 0) $this->db->where("aucid", trim($l['aucid']));
				else $this->db->orwhere("aucid", trim($l['aucid']));
			}
			$this->db->order_by("wid", "DESC");
			$this->lquery = $this->db->get('warehouse');
			
			foreach ($this->lquery->result_array() as $lk => $ll)
			{
				$ll['dates'] = unserialize($ll['dates']);
				$r[trim($ll['aucid'])][] = $ll;
			}
			
			return array('results' => $r, 'pages' => $pagearray);	
		}
	}*/
	
	function GetBCNs($page = '')
	{
		$limit = 500;
		if ((int)$page > 0) $page--;
		$this->db->order_by("wid", "DESC");
		$this->db->where("deleted", 0);
		$this->db->limit($limit, (int)$page*$limit);
		$this->query = $this->db->get('warehouse');
		$this->db->where("deleted", 0);
		$countall = $this->db->count_all_results('warehouse');
		$pages = ceil($countall/$limit);		
		for ( $counter = 1; $counter <= $pages ; $counter++) $pagearray[] = $counter;
		if ($this->query->num_rows() > 0) 
		{
			$r = array();
			foreach ($this->query->result_array() as $k => $l)
			{
				$l['dates'] = unserialize($l['dates']);
				$h[$k] = array('wid' => $l['wid'], 'bcn' => $l['bcn']);
				$r[$k] = $l;
			}			
			return array('results' => $r, 'headers' =>  $h, 'pages' => $pagearray);	
		}
	}
	function GetLast()
	{
		$this->db->select("distinct insid", false);
		$this->db->order_by("insid", "DESC");
		$this->db->limit(5);
		$this->db->where("deleted", 0);
		$this->query = $this->db->get('warehouse');
		if ($this->query->num_rows() > 0) 
		{
			$ins = array();
			$this->db->select('wid, insid, aucid, mfgpart, bcn, sku');
			foreach ($this->query->result_array() as $k => $l)
			{
				if ($k == 0) $this->db->where("insid", (int)$l['insid']);
				else $this->db->orwhere("insid", (int)$l['insid']);
			}
			$this->db->order_by("wid", "DESC");

			$this->lquery = $this->db->get('warehouse');
			
			foreach ($this->lquery->result_array() as $lk => $ll)
			{
				$ins[(int)$ll['insid']][] = $ll;
			}
			foreach ($ins as $ik => $iv)
			{
				$inscn[$ik] = count($iv);
			}			
			return array('data' => $ins, 'count' => $inscn);	
		}		
	}
	function GetStatusNotes($id)
	{
		$this->db->select('status_notes');
		$this->db->where('wid', (int)$id);
		$this->query = $this->db->get('warehouse');
		if ($this->query->num_rows() > 0) 
		{
			$s = $this->query->row_array();
			return ($s['status_notes']);
		}
	}
	function GetFound($str, $listingid)
	{
		$this->db->like("aucid", $str);
		$this->db->or_like("lot", $str);
		$this->db->or_like("oldbcn", $str);
		$this->db->or_like("mfgpart", $str);
		$this->db->or_like("mfgname", $str);
		$this->db->or_like("psku", $str);
		$this->db->or_like("sku", $str);
		$this->db->or_like("sn", $str);
		$this->db->or_like("bcn", $str);
		$this->db->or_like("title", $str);
		$this->db->or_like("location", $str);
		$this->db->order_by("wid", "DESC");
		$this->query = $this->db->get('warehouse');
		if ($this->query->num_rows() > 0) 
		{
			foreach ($this->query->result_array() as $k => $l)
			{
				if ($l['deleted'] == 0)
				{
					if (($listingid && (int)$listingid != $l['listingid']) || !$listingid)
					{
						$l['dates'] = unserialize($l['dates']);
						$r[$k] = $l;
					}
				}
			}
			return $r;
			
		}
	}
	function GetAttachedBcns($listingid)
	{
		$this->db->where("listingid", $listingid);
		$this->db->where("deleted", 0);
		$this->db->order_by("wid", "DESC");
		$this->query = $this->db->get('warehouse');
		if ($this->query->num_rows() > 0) 
		{
			foreach ($this->query->result_array() as $k => $l)
			{
					$l['dates'] = unserialize($l['dates']);
					$r[$k] = $l;
			}
			return $r;			
		}
	}
	function GetWarehouseItems($aucid)
	{
		$this->db->where("waid", $aucid);
		$this->db->where("deleted", 0);
		$this->db->order_by("wid", "DESC");
		$this->query = $this->db->get('warehouse');
		if ($this->query->num_rows() > 0) 
		{
			foreach ($this->query->result_array() as $k => $l)
			{
					$l['dates'] = unserialize($l['dates']);
					$r[$k] = $l;
			}
			return $r;			
		}
		
		
	}
	function AttachBcnToListing($wid, $listingid)
	{
		$this->db->update('warehouse', array('listingid' => $listingid, 'status' => 'Listed', 'listed' => 'eBay '.$listingid, 'listed_date' => CurrentTime()), array('wid' => (int)$wid));
	}
	function DettachBcnFromListing($wid, $listingid)
	{
		$this->db->update('warehouse', array('listingid' => 0, 'status' => 'Not Listed', 'listed' => '', 'listed_date' => ''), array('wid' => (int)$wid));
		//UPDATE LOG WITH LISTING CHANGE 
	}
	function GetPacks($aucid)
	{
		$this->db->where("waid", (int)$aucid);
		$this->db->where("deleted", 0);
		$this->db->order_by("wid", "DESC");
		$this->query = $this->db->get('warehouse');
		if ($this->query->num_rows() > 0) 
		{
			$r = array();		
			foreach ($this->query->result_array() as $k => $l)
			{
				$l['dates'] = unserialize($l['dates']);
				//$r[$l['wid']] = $l;
				$h[$k] = array('wid' => $l['wid'], 'bcn' => $l['bcn']);
				$r[$k] = $l;
				
			}			
			return array('data' => $r, 'headers' =>  $h);			
		}
	}
	function GetTesting($id = '')
	{
		if ((int)$id == 0) $this->db->limit(500);
		$this->db->order_by("wid", "DESC");
		$this->db->where("deleted", 0);
		$this->db->where("nr", 0);
		if ((int)$id > 0) $this->db->where("waid", (int)$id);
		$this->query = $this->db->get('warehouse');
		if ($this->query->num_rows() > 0) 
		{
			$r = array();		
			foreach ($this->query->result_array() as $k => $l)
			{
				$l['dates'] = unserialize($l['dates']);
				//$r[$l['wid']] = $l;
				$h[$k] = array('wid' => $l['wid'], 'bcn' => $l['bcn']);
				$r[$k] = $l;
				
			}			
			return array('data' => $r, 'headers' =>  $h);
		}
	}
	
	function GetSingle($id = '')
	{		
		$this->db->where("deleted", 0);

		$this->db->where("wid", (int)$id);
		$this->query = $this->db->get('warehouse');
		if ($this->query->num_rows() > 0) 
		{
			$rt = $this->query->row_array();		
			$rt['dates'] = unserialize($rt['dates']);
			//$r[$l['wid']] = $l;
			$h[0] = array('wid' => $rt['wid'], 'bcn' => $rt['bcn']);
			$r[0] = $rt;
			return array('data' => $r, 'headers' =>  $h);
		}
	}
	
	function GetAccounting($id = '', $status = '')
	{
	
		//$this->db->where("waid", (int)$id);
		
		$sql = 'SELECT * FROM warehouse WHERE `deleted` = 0 AND `nr` = 0'; 
		
		
		if (is_array($id))
		{
			$sql .= " AND (";
			foreach($id as $i) $sql .= '`waid` = '.$i['waid'].' OR '; 
			$sql = rtrim($sql, ' OR ');
			$sql .= ")";
		}
		else $sql .= ' AND `waid` = '.$id;
		
		$showparts = (int)$this->session->userdata('showparts');
		$showparents = (int)$this->session->userdata('showparents');
		
		if (!$showparts && !$showparents)
		{
			 $showparents = TRUE;
			 $this->session->set_userdata('showparents', 1);
		}
		if (!$showparts) $sql .= ' AND `bcn_p3` IS NULL';
		if (!$showparents) $sql .= ' AND `bcn_p3` IS NOT NULL'; 
		
		if ($status != '')
		{
			switch ($status)
			{
				case 'location':
				$sql .= " AND `location` IS NOT NULL";
				//$this->db->where("location !=", '');
				break;
				case 'nolocation':
				$sql .= " AND `location` IS NULL";
				//$this->db->where("location", '');
				break;
				case 'sn':
				$sql .= " AND `sn` IS NOT NULL";
				//$this->db->where("sn !=", '');
				break;
				case 'nosn':
				$sql .= " AND `sn` IS NULL";
				//$this->db->where("sn !=", '');
				break;
				default: $sql .= " AND `status` = '".$status."'"; //$this->db->where("status", $status);
			}	
			$winitial = $this->session->userdata('winitial');
			
			if ($status == 'Sold')
			{
				$wnavfrom = $this->session->userdata('wnavfrom');
				$wnavto = $this->session->userdata('wnavto');
			
				if ($wnavfrom && $wnavto) 
				{
					$from = explode('/',$wnavfrom);
					$wnavfrom = mktime(23, 59, 59, $from[0], $from[1], $from[2]);
					$to = explode('/', $wnavto);			
					$wnavto = mktime(0, 0, 0, $to[0], $to[1], $to[2]);					
					
					if ($winitial) $datefield = 'trans_mk';
					else $datefield = 'setshipped';
		
					$sql .= ' AND `'.$datefield.'` >= '.$wnavto.' AND `'.$datefield.'` <= '.$wnavfrom;
					
				}		
			}
		}
		
		$sql = $sql.' ORDER BY wid DESC';
		//printcool ($sql);
		$this->query = $this->db->query($sql);
		if ($this->query->num_rows() > 0) 
		{
			
			$r = array();	
			foreach ($this->query->result_array() as $k => $l)
			{
				//if (!$showparts && $l['bcn_p3'] != '')
				//{
				//}
				//else
				//{
					$l['dates'] = unserialize($l['dates']);
					//$r[$l['wid']] = $l;
					$h[$k] = array('wid' => $l['wid'], 'bcn' => $l['bcn']);
					$r[$k] = $l;
				//}
				
			}			
			return array('data' => $r, 'headers' =>  $h);
		}
	}
	
	function GetReturnData($wid = '')
	{
		
		$this->db->select('wid, sku, bcn, sn, title, location, cust_return, cust_reason, cust_xtrcost, cust_status, vendor_return, vendor_reason')	;
		$this->db->where("wid", (int)$wid);
		$this->query = $this->db->get('warehouse');
		if ($this->query->num_rows() > 0) 
		{
			return $this->query->row_array();
		}
		
	}
	function GetLabel($id = '')
	{
		if ((int)$id == 0)$limit = 500;
		$this->db->order_by("wid", "DESC");
		$this->db->limit($limit);
		$this->db->where("deleted", 0);
		if ((int)$id > 0) $this->db->where("waid", (int)$id);
		$this->query = $this->db->get('warehouse');
		if ($this->query->num_rows() > 0) 
		{
			return $this->query->result_array();
		}
	}
	function GetField($field = '', $id, $multiple = false)
	{
		$this->db->select($field);
		$this->db->where("wid", (int)$id);
		$this->query = $this->db->get('warehouse');
		if ($this->query->num_rows() > 0) 
		{
			$f = $this->query->row_array(); 
			if ($multiple) return $f;
			else  return $f[$field];
		}
		else return 'ERROR';
	}
	function Delete($id)
	{	
		$this->db->where('wid', (int)$id);
		$this->db->where("deleted", 0);
		$this->db->delete('warehouse'); 
	}
	function Update($id, $data)
	{
		$this->db->update('warehouse', $data, array('wid' => (int)$id));
	}
	function Insert($data)
	{
		$this->db->insert('warehouse', $data);
		return $this->db->insert_id();
	}
	function GetNextBcn($mmy)
	{
		$this->db->select('bcn_p2');		
		$this->db->where('bcn_p1', $mmy);
		$this->db->where('bcn_p3', NULL);
		$this->db->order_by("wid", "DESC");
		//$this->db->limit(1);
		$this->query = $this->db->get('warehouse');
		if ($this->query->num_rows() > 0)
		{
			$res = $this->query->row_array();
			$res = (int)$res['bcn_p2']+1;
		}
		else $res = 1;
		return $res;
	}
	function CheckBCNDoesNotExists($bcn)
	{
		$this->db->select('wid');
		$this->db->where("bcn", trim($bcn));
		$this->query = $this->db->get('warehouse');
		if ($this->query->num_rows() > 0) return TRUE;
		else false;		
	}
	function GetBCNListingID($wid)
	{
		$this->db->select('bcn, listingid');
		$this->db->where("wid", (int)$wid);
		$this->query = $this->db->get('warehouse');
		if ($this->query->num_rows() > 0) { $lid = $this->query->row_array(); return array('cur_eid' => $lid['listingid'], 'bcn' => $lid['bcn']); }
		else return array('cur_eid' => 0, 'bcn' => NULL);		
	}
	function GetNextInsertOrder()
	{
		$this->db->select('insid');
		$this->db->where("deleted", 0);
		$this->db->order_by("insid", "DESC");
		$this->db->limit(1);
		$this->query = $this->db->get('warehouse');
		if ($this->query->num_rows() > 0)
		{
			$res = $this->query->row_array();			
			if ($res['insid'] == 0) $res = 1;
			else $res = $res['insid']+1;			
		}
		else $res = 1;
		return $res;
	}
	function GetLastAuc()
	{
		$this->db->select('waid');
		$this->db->where("deleted", 0);
		$this->db->order_by("waid", "DESC");
		$this->db->limit(1);
		$this->query = $this->db->get('warehouse_auctions');
		if ($this->query->num_rows() > 0)
		{
			$res = $this->query->row_array();			
			return $res['waid'];
		}
	}	
	function GetAuction($auc)
	{
		$this->db->where("waid", $auc);
		$this->db->where("deleted", 0);
		$this->query = $this->db->get('warehouse_auctions');
		if ($this->query->num_rows() > 0)
		{
			return $this->query->row_array();			
		}
	}
	function AuctionNameToId($auc)
	{
		$this->db->select('waid');
		$this->db->where("wtitle", $auc);
		$this->query = $this->db->get('warehouse_auctions');
		if ($this->query->num_rows() > 0)
		{
			$ac = $this->query->row_array();			
			return (int)($ac['waid']);
		}
	}
	function AuctionIdToName($auc)
	{
		$this->db->select('wtitle');
		$this->db->where("waid", $auc);
		$this->query = $this->db->get('warehouse_auctions');
		if ($this->query->num_rows() > 0)
		{
			$ac = $this->query->row_array();			
			return $ac['wtitle'];
		}
	}
	function GetAdminList()
	{
		$this->db->select("admin_id, ownnames");
		$this->query = $this->db->get('administrators');
		
		if ($this->query->num_rows() > 0) 
		{
			foreach ($this->query->result_array() as $a) $adm[$a['admin_id']] = $a['ownnames'];	
			return $adm;
		}
	}
	function GetAuctionCategories()
	{
		$query = $this->db->get('warehouse_auction_categories');		
		if ($query->num_rows() > 0) 
		{
			foreach ($query->result_array() as $c)
			{
				$ac[$c['wacat_id']] = $c;	
			}
			return $ac;
		}		
	}
	function GetAllAuctions()
	{
		$this->db->select('waid, wtitle');
		$this->db->order_by('waid', 'DESC');
		$query = $this->db->get('warehouse_auctions');		
		if ($query->num_rows() > 0) 
		{
			foreach ($query->result_array() as $c)
			{
				$ac[$c['waid']] = $c['wtitle'];	
			}			
			return $ac;
		}		
	}
	function wid2bcn($id = '')
	{
		$this->db->select('bcn');
		$this->db->where("wid", (int)$id);
		$this->query = $this->db->get('warehouse');
		if ($this->query->num_rows() > 0)
		{
			$wb = $this->query->row_array();			
			return trim($wb['bcn']);
		}
	}
	function waid2bcn($id = '')
	{
		$this->db->select('bcn');
		$this->db->where("waid", (int)$id);
		$this->query = $this->db->get('warehouse');
		if ($this->query->num_rows() > 0)
		{
			$wb = $this->query->row_array();			
			return trim($wb['bcn']);
		}
	}
	function bcn2wid($bcn = '')
	{
		$this->db->select('wid');
		$this->db->where("bcn", $bcn);
		$this->query = $this->db->get('warehouse');
		if ($this->query->num_rows() > 0)
		{
			$wb = $this->query->row_array();			
			return trim($wb['wid']);
		}
	}
	function GetSelection($from, $to, $id, $sales)
	{																														//AND `vended` = 0 AND `status` != "Sold"
		$sql = 'SELECT wid, bcn, oldbcn, title, status, sold_id, status_notes, generic, waid, channel, listingid FROM warehouse WHERE `deleted` = 0 AND `nr` = 0  AND (';			
		if (is_array($from))
		{
			if (count($from) == 0 || count($to) == 0) return array();
			$c = 0;
			foreach ($from as $f)
			{
				if (trim($f) != '')
				{
					if ($c == 0)  $sql .= '`bcn` LIKE "%'.trim($f).'%" OR `lot` LIKE "%'.trim($f).'%" OR `oldbcn` LIKE "%'.trim($f).'%"';
					else $sql .= ' OR `bcn` LIKE "%'.trim($f).'%" OR `lot` LIKE "%'.trim($f).'%" OR `oldbcn` LIKE "%'.trim($f).'%"';
					$c++;
				}
			}
		}
		else
		{		
			if (trim($to) != '')
			{	
				if (trim($from) == '') return false;
				$tmp = explode('-', $from);
				if (!isset($tmp[1])) return false;			
				$tmp[1] =  ereg_replace("[^0-9]", "", $tmp[1]);					
				if (isset($tmp[0]) && isset($tmp[1]) && is_numeric($to) && is_numeric($tmp[1]) && $to > $tmp[1]) 
				{
					$c=0;
					while ($tmp[1] <= $to)
						{							
							if ($c == 0) $sql .= '`bcn` = "'.$tmp[0].'-'.$tmp[1].'" OR `lot` = "'.$tmp[0].'-'.$tmp[1].'" OR `oldbcn` = "'.$tmp[0].'-'.$tmp[1].'"';
							else $sql .= ' OR `bcn` = "'.$tmp[0].'-'.$tmp[1].'" OR `lot` = "'.$tmp[0].'-'.$tmp[1].'" OR `oldbcn` = "'.$tmp[0].'-'.$tmp[1].'"';
							$c++;															
							$tmp[1]++;							
						}
				}
				else return false;
			}
			else
			{
				 $sql .= '`bcn` LIKE "%'.trim($from).'%" OR `lot` LIKE "%'.trim($from).'%"  OR `oldbcn` LIKE "%'.trim($from).'%"';
			}
		}
		
		$sql .= ')';
		if ($id > 0)
		{//`channel` != '.(int)$sales.' 
			//if ($sales != '')  $sql .= ' AND (`vended` = 0 AND `sold_id` != '.$id.')';
			if ($sales != '')  $sql .= ' AND (`sold_id` != '.$id.')';
			else $sql .= ' AND `listingid` != '.$id;
		}
		//echo $sql;
		$q =  $this->db->query($sql);
		if ($q->num_rows() > 0)
		{
			return $q->result_array();
		}
	}
	function getbcnattachdata($id = '')
	{
		//$this->db->select('wid, listingid, status, status_notes');
		$this->db->where("wid", (int)$id);
		$query = $this->db->get('warehouse');
		if ($query->num_rows() > 0)
		{		
			return $query->row_array();
		}
	}	
	function ReProcessNetProfit($wid)
	{
		$this->db->select('wid, paid, cost, sellingfee, shipped_actual');
		$this->db->where("wid", (int)$wid);
		$query = $this->db->get('warehouse');
		//GoMail(array ('msg_title' => 'ReProcessNetProfit Run @ '.CurrentTime(), 'msg_body' => printcool ($wid, true,'wid'), 'msg_date' => CurrentTime()), 'errors@la-tronics.com', $this->config->config['no_reply_email']);
		if ($query->num_rows() > 0)
		{		
			$wid = $query->row_array();
			$data['netprofit'] = sprintf("%01.2f", (((float)$wid['paid']+(float)$wid['shipped'])-((float)$wid['cost']+(float)$wid['sellingfee']+(float)$wid['shipped_actual'])));	
			$this->db->update('warehouse', $data, array('wid'=> (int)$wid['wid']));
			//GoMail(array ('msg_title' => 'ReProcessNetProfit Saved @ '.CurrentTime(), 'msg_body' => printcool ($data, true,'data'), 'msg_date' => CurrentTime()), 'errors@la-tronics.com', $this->config->config['no_reply_email']);
		}
		
	}
	function getsaleattachdata($channel, $id, $listingid, $remove)

	{
		if ($channel == 1)
					{
						if ((int)$remove == 0) $this->db->select('datetime, mkdt, qty,mark, paid,fee,shipping,paidtime,paydata,itemid,buyerid,buyeremail,sn,asc,ssc');	
						else  $this->db->select('qty, mark');
						$this->db->where('et_id', (int)$id);
						$q = $this->db->get('ebay_transactions');
						if ($q->num_rows() > 0) 
						{
							$sale = $q->row_array();
							if ((int)$remove == 0) 
							{
								if ($sale['qty'] > 1) $data = array(	
												'trans_date' => $sale['datetime'],
												'trans_mk' => $sale['mkdt'], 				
												'sold_date' => $sale['paidtime'], 
												'paid' => sprintf("%01.2f", (float)$sale['paid']/$sale['qty']),
												'shipped' => sprintf("%01.2f", (float)$sale['ssc']/$sale['qty']),
												'shipped_actual' => sprintf("%01.2f", (float)$sale['asc']/$sale['qty']),
												//'ordernotes' => 'Transaction '.$id,
												'sellingfee' => sprintf("%01.2f", (float)$sale['fee']/$sale['qty'])
												);
								
								else $data = array(	
												'trans_date' => $sale['datetime'],
												'trans_mk' => $sale['mkdt'],			
												'sold_date' => $sale['paidtime'], 
												'paid' => $sale['paid'],
												'shipped' => $sale['ssc'],
												'shipped_actual' => $sale['asc'],
												//'ordernotes' => 'Transaction '.$id,
												'sellingfee' => $sale['fee']
												);
												
							}
							$data['qty'] = $sale['qty'];
							$data['mark'] = $sale['mark'];
						}
						 
					}
					elseif ($channel == 2) 
					{
						$this->db->select('time, submittime, endprice, endprice_delivery, complete_time, order, mark');
						$this->db->where('oid', (int)$id);
						$q = $this->db->get('orders');
						if ($q->num_rows() > 0) 
						{
							
							$sale = $q->row_array();							 
							$data['mark'] = $sale['mark'];							
							$sale['order'] = unserialize($sale['order']);
							$data['qty'] = 0;

							 if (count($sale['order']) > 0)
							 {
								foreach ($sale['order'] as $k => $v)
								{									
									if ($k == $listingid) $data['qty'] = $v['quantity'];
								}
							 }
							 
							 if ((int)$remove == 0)
							 {
								  if ($data['qty'] > 1) $data = array(
												'trans_date' => $sale['time'],
												'trans_mk' => $sale['submittime'],
												'sold_date' => $sale['complete_time'],
												'paid' =>  sprintf("%01.2f", (float)$sale['endprice']/$data['qty']),
												'shipped' =>  sprintf("%01.2f", (float)$sale['endprice_delivery']/$data['qty']),
												'shipped_actual' => sprintf("%01.2f", (float)$sale['endprice_delivery']/$data['qty']),
												//'ordernotes' => 'Order '.$id,
												'sellingfee' => 0,
												'mark' => $data['mark'],
												'qty' => $data['qty']
												);
								else $data = array(
												'trans_date' => $sale['time'],
												'trans_mk' => $sale['submittime'],
												'sold_date' => $sale['complete_time'],
												'paid' => $sale['endprice'],
												'shipped' => $sale['endprice_delivery'],
												'shipped_actual' => $sale['endprice_delivery'],
												//'ordernotes' => 'Order '.$id,
												'sellingfee' => 0,
												'mark' => $data['mark'],
												'qty' => $data['qty']
												);
							}
						}
					}
					elseif ($channel == 4)
					{
						
							$dbo = $this->GetOrder((int)$id);
							$data = array(				
												'sold_date' => CurrentTime(), 
												//'paid' => $dbo['paid'],
												//'shipped' => $dbo['shipped'],
												//'ordernotes' => 'Warehouse Order '.$id,
												'sellingfee' => 0
												);
							$data['qty'] = -1;
							$data['mark'] = $dbo['shipped'];
						
					}
	if (isset($data)) return $data;
	}

}
?>