<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Myebay_model extends Model 
{
    function Myebay_model()
    {
        parent::Model();
    }
function RecordAction($eid = 0, $ebayid = 0, $datafrom = '', $datato = '', $adminid = 0, $transid = 0, $ctrl = '')
{
	$this->db->insert('ebay_actionlog',array('e_id' => $eid, 'ebay_id' => $ebayid, 'time' => CurrentTimeR(), 'datafrom' => $datafrom, 'datato' => $datato, 'admin_id' => $adminid, 'trans_id' => $transid, 'ctrl' => $ctrl));
}
function ListItems2($string, $where = '', $ended = '', $zero = '', $mm = '', $bcnmm, $sitesell, $page, $category_id, $storeCatId)
	{	
		if ((int)$page > 0) $page--;
		$this->db->select("e_id, dbrel, e_title, e_part, e_qpart, old_e_part, admin_id, storeCatTitle, pCTitle, 
                            listingDuration, startPrice, buyItNowPrice,  quantity, ebayquantity, 
                            ebay_submitted, e_img1, idpath, link, ebay_id, submitlog, gsid1, gsid2, gsid3, 
                            gsid4, gsid5, autorev, autorevtxt, ebsold, sitesell, nwm, ebended, endedreason, 
                            e_ebayq, ngen, price_ch1, price_ch2, price_ch3, qn_ch1, qn_ch2, qn_ch3, audit, 
                            ostock, ooskeepalive, lock_google_cat, lock_ebay_cat, lock_amazon_cat");	

		if ($where == 1) $wl = 'e_part';
		elseif ($where == 2) $wl = 'ebay_id';
		elseif ($where == 3) $wl = 'e_id';
		elseif ($where == 4) $wl = 'storeCatID';
		elseif ($where == 5) $wl = 'old_e_part';

        elseif ($where == 6) $wl = 'primaryCategory = '.$category_id.' and storeCatID = '.$storeCatId;
        elseif ($where == 7) $wl = 'categoryEbaySecondaryId = '.$category_id.' and storeCatID = '.$storeCatId;
        elseif ($where == 8) $wl = 'categoryAmazonId = '.$category_id.' and storeCatID = '.$storeCatId;
        elseif ($where == 9) $wl = 'categoryGoogleId = '.$category_id.' and storeCatID = '.$storeCatId;

		else $wl = 'e_title';

        //echo '<p>storeCatId check '.$wl;

        //if (storeCatId != '')
        //{ 
            $this->db->where($wl);
        //}   	
        
        //        if ($where == 2 || $where == 3 || $where == 4)
        //        {
        //            $this->db->where($wl, trim(stripslashes($string)));	
					
        //        }
        //        else
        //        {
        //            $string = str_replace("'", "", $string);
        //            $string = str_replace('"', "", $string);
        //            $string = str_replace('&quot;', "", $string);			
				
        //            $string = explode(' ',trim($string));
        //            //$c=1;
        //            foreach ($string as $s)
        //            {
        //                $this->db->like($wl, $s);
        //                //if ($where == 1) $this->db->like('old_e_part', $s);	
        //                //printcool ($s);
        //                /*
        //                if ($c == 1) $this->db->like($wl, $s);	
        //                else $this->db->or_like($wl, $s);	*/
        //                //$c++;
        //            }
        //        }
        //    }
            
		if ($zero != '') $this->db->where('quantity <=', 0);
		if ($ended != '') $this->db->where('ebended !=', '');
		if ($mm != '') $this->db->where('quantity != ebayquantity');		
		if ($bcnmm != '') $this->db->where('quantity != e_qpart');

        if ($category == 1) $this->db->where('primaryCategory != e_qpart');
        if ($category == 2) $this->db->where('primaryCategory != e_qpart');


		
		if ($sitesell == 1) $this->db->where('sitesell', 1);
		elseif ($sitesell === 0) $this->db->where('sitesell', 0);		
					
	//if ($this->session->userdata['admin_id'] == 1) printcool ($viewport);
		$this->db->order_by("e_id", "DESC");
		$this->db->limit(30, (int)$page*30);

         
        
		$this->query = $this->db->get('ebay');

        //echo "<p>".$this->db->last_query();

        //echo '<p>From eBay Model: '.$this->db->last_query();

		//if ($this->session->userdata['admin_id'] == 1) printcool ($this->db);
        //if ($string != '')
        //    { 
        //        if ($where == 2 || $where == 3 || $where == 4)
        //        {
        //            $this->db->where($wl, trim($string));	
        //        }
        //        else
        //        {
        //        //$c=1;
        //        foreach ($string as $s)
        //        {
        //            $this->db->like($wl, $s, FALSE);	
        //            /*
        //            if ($c == 1) $this->db->like($wl, $s);	
        //            else $this->db->or_like($wl, $s);	*/
        //            //$c++;
        //        }
        //        }
        //    }	
        //if ($ended != '') $this->db->where('ebended !=', '');
        //if ($zero != '') $this->db->where('quantity <=', 0);
        //if ($mm != '') $this->db->where('quantity != ebayquantity');		
        //if ($bcnmm != '') $this->db->where('quantity != e_qpart');	
        //if ($sitesell == 1) $this->db->where('sitesell', 1);
        //elseif ($sitesell === 0) $this->db->where('sitesell', 0);
        $this->db->where($wl);
		$countall = $this->db->count_all_results('ebay', FALSE);

        //$countall=$this->db->count_all_results('ebay', FALSE);
       //$countall = $this->query->num_rows();
        

		$pages = ceil($countall/30);
		for ( $counter = 1; $counter <= $pages ; $counter++) $pagearray[] = $counter;
		
		if ($this->query->num_rows() > 0)
		{
			$r = $this->query->result_array();
			
			$this->db->select('wsid, listing');
			$sku = 1;
			foreach ($r as $rr)
			{
				$idarray[] = $rr['e_id'];	
				if ($sku == 1) $this->db->where('listing', $rr['e_id']);
				else $this->db->or_where('listing', $rr['e_id']);
				$sku++;
			}
			$s = $this->db->get('warehouse_sku_listing');
			if ($s->num_rows() > 0)
				{
					foreach ($s->result_array() as $ss)
					{
						$sk[$ss['listing']][] = $ss['wsid'];	
					}
					$this->mysmarty->assign('listingskus', $sk);
				}
			if (isset($idarray))
			{
				$CI =& get_instance();
				$CI->load->model('Myseller_model'); 	
				$CI->Myseller_model->getBase($idarray);
				$CI->Myseller_model->getOnHold($idarray);
				$CI->Myseller_model->countSales($idarray);
				//$CI->Myseller_model->getEmptySales($idarray, 1);	
				$CI->load->model('Myautopilot_model');
				$CI->Myautopilot_model->GetListingRules($idarray);					
				$CI->Myautopilot_model->GetCompetitorRules($idarray);
			}
			
			$return = array('results' => $r, 'pages' => $pagearray);	
			 
			$this->db->where('sitesell', 1);
			$count['onsite'] = $this->db->count_all_results('ebay');
			$this->db->where('sitesell', 0);
			$count['offsite'] = $this->db->count_all_results('ebay');
			
			$this->db->where('ebay_id !=', 0);
			$count['onebay'] = $this->db->count_all_results('ebay');
			$this->db->where('ebay_id', 0);
			$count['notonebay'] = $this->db->count_all_results('ebay');
			
			$this->db->where('ebended', NULL);
			$count['notended'] = $this->db->count_all_results('ebay');
			$this->db->where('ebended !=',  '');
			$count['ended'] = $this->db->count_all_results('ebay');
			$count['all'] = $this->db->count_all_results('ebay');
			$return['count'] = $count;
			
            //printcool($return);

			return $return;
			 
		}
		
		
		
		
		
	}
function getOrderReturn($id, $type)
{
	switch ($type)
	{
		case 1: 
			$this->db->select('return_id, returned, returned_notes, returned_time, returned_recieved, returned_refunded, returned_extracost');
			$this->db->where('et_id', (int)$id);
			$o = $this->db->get('ebay_transactions');
			if ($o->num_rows() > 0)	return $o->row_array();	
		break;
		case 2: 
			$this->db->select('return_id, returned, returned_notes, returned_time, returned_recieved, returned_refunded, returned_extracost');
			$this->db->where('oid', (int)$id);
			$o = $this->db->get('orders');
			if ($o->num_rows() > 0)	return $o->row_array();	
		break;
		case 4: 
			$this->db->select('return_id, returned, returned_notes, returned_time, returned_recieved, returned_refunded, returned_extracost');
			$this->db->where('woid', (int)$id);
			$o = $this->db->get('warehouse_orders');
			if ($o->num_rows() > 0)	return $o->row_array();	
		break;
		default: return false;	
	}
}
function SetOrderReturn($id, $type, $data)
{

	switch ($type)
	{
		case 1:
		    foreach ($data as $dk => $dv) $this->db->set($dk, $dv);
            $this->db->where('et_id',(int)$id);
			$this->db->update('ebay_transactions');
		break;
		case 2:
            foreach ($data as $dk => $dv) $this->db->set($dk, $dv);
            $this->db->where('oid',(int)$id);
            $this->db->update('orders');

		break;
		case 4:
            foreach ($data as $dk => $dv) $this->db->set($dk, $dv);
            $this->db->where('woid',(int)$id);
            $this->db->update('warehouse_orders');

		break;
		default: return false;	
	}
}
function getBcnsFromWids($wids, $extrafields = false)
{
	//if ($extrafields) $sql = 'SELECT wid, bcn, sku, title, cost,  status, status_notes, returned,returned_notes,returned_time,returned_recieved,returned_refunded,returned_extracost,returnstatus, sold_id, sold_subid';
	//else 
		$sql = 'SELECT wid, bcn, status, location, return_id, returned, returned_notes,returned_time,returned_recieved,returned_refunded,returned_extracost,returnstatus';
		$sql .= ' FROM warehouse WHERE `deleted` = 0 AND `nr` = 0 AND (';
		$c = 1;
		foreach ($wids as $w)
		{
			if ($c == 1) $sql .= '`wid` = '.(int)$w;
			else $sql .= ' OR `wid` = '.(int)$w;
			$c++;
		}
		$sql .= ')';
		$q =  $this->db->query($sql);	
		if ($q->num_rows() > 0)
		{
			foreach ($q->result_array() as $qr)
			{
				$array[$qr['wid']] = $qr;
			}	
			return $array;
		}	
}
function getOrderReturnedBCN($returnid, $extrafields = false, $page = false)
{
	if ($extrafields) $sql = 'SELECT wid, bcn, sku, location, title, cost,  status, status_notes, returned,returned_notes,returned_time,returned_recieved,returned_refunded,returned_extracost,returnstatus, sold_id, sold_subid';
	else $sql = 'SELECT wid, bcn, status, location, returned,returned_notes,returned_time,returned_recieved,returned_refunded,returned_extracost,returnstatus';

	if ($page)
	{ 
	
		$sql .= ' FROM warehouse WHERE `deleted` = 0 AND `nr` = 0 AND  `return_id` != 0';
		
		$pagearray = false;
		
			$limit = 1000;
			//printcool ($page);
			 $sql .= ' LIMIT '.(((int)$page-1)*$limit.', '.$limit);
			 $this->db->where('nr', 0);
			 $this->db->where('deleted', 0);
			 $this->db->where('return_id !=', 0);
			 $ret = $this->db->count_all_results('warehouse');
			
			 $pages = ceil($ret/$limit);

			for ( $counter = 1; $counter <= $pages ; $counter++) $pagearray[] = $counter;	
	}
	else $sql .= ' FROM warehouse WHERE `deleted` = 0 AND `nr` = 0 AND  `return_id` = '.(int)$returnid;
	
	
	
		$q =  $this->db->query($sql);	
		if ($q->num_rows() > 0)
		{
			foreach ($q->result_array() as $qr)
			{
				if ($page) $array[] = $qr;
				else  $array[$qr['wid']] = $qr;
			}	

			if ($page) return array('data' => $array, 'pages' => $pagearray);
			else return $array;
		}
}
function PreGetOrderReturnedBCN($orderid, $ordertype)
{
		$sql = 'SELECT wid, bcn, status, returned, location, returned_notes,returned_time,returned_recieved,returned_refunded,returned_extracost,returnstatus';
		$sql .= ' FROM warehouse WHERE `deleted` = 0 AND `nr` = 0 AND  `sold_id` = '.(int)$orderid.' AND  `channel` = '.(int)$ordertype;
		$q =  $this->db->query($sql);	
		if ($q->num_rows() > 0)
		{
			foreach ($q->result_array() as $qr)
			{
				$array[$qr['wid']] = $qr;
			}	
			return $array;
		}
}
function getReturnID($id, $type)
{
	$this->db->select('vid');
	$this->db->where('orderid', (int)$id);
	$this->db->where('channel', (int)$type);	
	$v = $this->db->get('returns');
	if ($v->num_rows() > 0)
	{
		$vv = $v->row_array();
		return $vv['vid'];	
	}
}
function GetAdminList()
	{
		$this->db->select("admin_id, ownnames,email");
		$this->query = $this->db->get('administrators');
		
		if ($this->query->num_rows() > 0) 
		{
			foreach ($this->query->result_array() as $a) $adm[$a['admin_id']] = $a['ownnames'];	
			return $adm;
		}
	}
function GetTopSpecialAds()
	{
		$this->db->select('p_ad');
		$this->db->where('p_cat', '34');
		$this->db->where('p_visibility', '1');
		$this->db->where('p_top', '1');
		$this->db->limit(4);
		$this->db->order_by("p_order", "ASC");
		$this->pquery = $this->db->get('products');
		if ($this->pquery->num_rows() > 0) 
			{
				$this->presult = $this->pquery->result_array();
				foreach ($this->presult as $key => $value)	
					{
					$this->presult[$key]['p_ad'] = explode('.', $value['p_ad']);
					}
				return $this->presult;
			}		
	}	
function ListXMLItems()
	{	
		$this->db->select("e_id, e_title, e_desc, idpath, e_img1, Condition, quantity, buyItNowPrice, e_manuf, e_part, upc, gtaxonomy, weight_kg, qn_ch1, price_ch1");			
		$this->db->where('ebay_id !=', 0);
		//$this->db->where('quantity >', 0);
		$this->db->where('price_ch1 !=', 0);
		$this->db->where('weight_kg !=', 0);
		//$this->db->where('e_desc !=', '');
		//$this->db->where('gtaxonomy !=', '');
		$this->db->order_by("e_id", "DESC");
		$q = $this->db->get('ebay');

		if ($q->num_rows() > 0)  return $q->result_array();	
	}
function ListQuantities()
{
		$this->db->select("e_id, e_title, quantity, qn_ch1, qn_ch2, qn_ch3, price_ch1, price_ch2, price_ch3, ebayquantity");			
		$this->db->where('ebay_id !=', 0);
		$this->db->order_by("e_id", "DESC");
		$q = $this->db->get('ebay');

		if ($q->num_rows() > 0)  return $q->result_array();	
}
function Viewport($view, $page = 1)
{
	/*$this->db->select("e_id, dbrel, e_title, e_part, e_qpart, old_e_part, admin_id, storeCatTitle, pCTitle, listingDuration, startPrice, buyItNowPrice,  quantity, ebayquantity, ebay_submitted, e_img1, idpath, link, ebay_id, submitlog, gsid1, gsid2, gsid3, gsid4, gsid5, autorev, autorevtxt, ebsold, sitesell, nwm, ebended, endedreason, e_ebayq");	
	
	
	$this->db->order_by("e_id", "DESC");
		$this->db->limit(30, (int)$page*30);
		$this->query = $this->db->get('ebay');
		*/
		
	$tdf =46800; 
		$from = explode('/', $this->session->userdata('navfrom'));
		$ofrom = mktime(23, 59, 59, $from[0], $from[1], $from[2])+$tdf;
		$to = explode('/', $this->session->userdata('navto'));			
		$oto = mktime(0, 0, 0, $to[0], $to[1], $to[2])+$tdf;
		
	$sql = "SELECT e_id, dbrel, e_title, e_part, e_qpart, old_e_part, admin_id, storeCatTitle, pCTitle, listingDuration, startPrice, buyItNowPrice,  quantity, ebayquantity, ebay_submitted, e_img1, idpath, link, ebay_id, submitlog, gsid1, gsid2, gsid3, gsid4, gsid5, autorev, autorevtxt, ebsold, sitesell, nwm, ebended, endedreason, e_ebayq, ngen,  price_ch1, price_ch2, price_ch3, qn_ch1, qn_ch2, qn_ch3, audit, ostock, ooskeepalive ";
	$csql = "SELECT e_id ";
	$page = $page - 1;
	$oby = ' ORDER BY e_id DESC LIMIT '.((int)$page*30).', 30';
	switch ($view)
	{
		case "ActiveWebsite": 
		$specs = 'FROM ebay WHERE `sitesell` = 1';		
		break;
		
		case "ActiveEbayLocal": 
		$specs = 'FROM ebay WHERE `ebay_id` != 0  AND `ebended` IS NULL';		
		break;
		
		case "ActiveEbayLocalBCNQTYMatch": 
		$specs = 'FROM ebay WHERE `ebay_id` != 0  AND `ebended` IS NULL AND `qn_ch2` = `ebayquantity`';		
		break; 
		
		case "ActiveEbayLocalBCNQTYMisMatch": 
		$specs = 'FROM ebay WHERE `ebay_id` != 0  AND `ebended` IS NULL AND `qn_ch2` != `ebayquantity`';	
		break;
		case "ActiveEbayActiveBCNQTYMisMatch":
		$specs = 'FROM ebay WHERE `ebay_id` != 0  AND `ebended` IS NULL AND `quantity` != `ebayquantity`';	
		break;
		case "ActiveEbayAssignedMisMatch":
		$specs = 'FROM ebay WHERE `ebay_id` != 0  AND `ebended` IS NULL AND `e_qpart` != `ebayquantity`';	
		break;
		case "ActiveEbayLocalGhost": 
		$specs = 'FROM ebay WHERE `ebay_id` != 0  AND `ebended` IS NULL AND `ngen` > 0';
		break;
		
		case "OutOfStock": 
		$specs = 'FROM ebay WHERE `ebay_id` != 0  AND `ebended` IS NULL AND `ebayquantity` = 0 AND `ooskeepalive` = 0';
		break;
		
		case "OosKeepAlive": 
		$specs = 'FROM ebay WHERE `ebay_id` != 0  AND `ebended` IS NULL AND `ebayquantity` = 0 AND `ooskeepalive` = 1';
		break;
		
		case "ActiveEbayLocalGreens": 
		$sql = 'SELECT distinct e.* FROM (ebay e) LEFT JOIN warehouse w ON e.e_id = w.listingid WHERE `e`.`ebay_id` != 0 AND `e`.`ebended` IS NULL  AND `w`.`deleted` = 0  AND `w`.`nr` = 0 AND `w`.`vended` = 0 AND `w`.`generic` = 1 AND `w`.`regen` = 1'; 
		$spec = '';
		$csql = str_replace('e.*,', 'e.e_id', $sql);
		//$oby = ' ORDER BY e.e_id DESC LIMIT '.((int)$page*30).', 30';
		break;
		
		case "InActiveWebsite": 
		$specs = 'FROM ebay WHERE sitesell = 0';
		break;
		
		case "InActiveEbayLocal": 

		$specs = 'FROM ebay WHERE (ebay_id = 0  OR ebended IS NOT NULL)';
		break;
		
		case "InActiveEbayLocalBCNQTYMatch": 
		$specs = 'FROM ebay WHERE (ebay_id = 0  OR ebended IS NOT NULL) AND qn_ch2 = ebayquantity';
		break; 
		
		case "InActiveEbayLocalBCNQTYMisMatch": 
		$specs = 'FROM ebay WHERE (ebay_id = 0  OR ebended IS NOT NULL) AND qn_ch2 != ebayquantity';
		break;
		
		case "InActiveEbayLocalGhost":
		$specs = 'FROM ebay WHERE (ebay_id = 0  OR ebended IS NOT NULL) AND ngen > 0  AND e_qpart > 0';
		break;
		
		case "NeedsRelisting":
		//$specs = 'FROM ebay WHERE ebended IS NOT NULL AND quantity > 0';
		$sql = 'SELECT distinct e.* FROM (ebay e) LEFT JOIN warehouse w ON e.e_id = w.listingid WHERE `e`.`ebended` IS NOT NULL AND `w`.`deleted` = 0  AND `w`.`nr` = 0 AND `w`.`vended` = 0'; 
		$specs = '';
		$csql = str_replace('e.*,', 'e.e_id', $sql);
		break;
                case "NeverList":
                $sql = 'SELECT distinct e.* FROM (ebay e) WHERE ebended IS NULL AND ebay_id = 0'; 
		$specs = '';
		$csql = str_replace('e.*,', 'e.e_id', $sql);
                break;
                case "nlnotitle":
                $sql = 'SELECT distinct e.* FROM (ebay e) WHERE e_title = ""'; 
		$specs = '';
		$csql = str_replace('e.*,', 'e.e_id', $sql);    
                break;
                case "nlbcn":                
                $sql = 'SELECT distinct e.* FROM (ebay e) LEFT JOIN warehouse w ON e.e_id = w.listingid WHERE `e`.`ebended` IS NULL AND `e`.`ebay_id` = 0 AND `w`.`deleted` = 0  AND `w`.`nr` = 0 AND `w`.`vended` = 0'; 
		$specs = '';
		$csql = str_replace('e.*,', 'e.e_id', $sql);    
                break;
		case "Autopilot":
		$sql = 'SELECT distinct e.* FROM (ebay e) LEFT JOIN autopilot_rules a ON e.e_id = a.e_id WHERE e.e_id = a.e_id ORDER BY runnextmk DESC'; 
		$specs = '';
		$oby = ',e_id DESC LIMIT '.((int)$page*30).', 30';
		$csql = str_replace('e.*,', 'e.e_id', $sql);
		break;
		case "Debugpilot":
		$sql = 'SELECT distinct e.* FROM (ebay e) LEFT JOIN autopilot_rules a ON e.e_id = a.e_id WHERE e.e_id = a.e_id AND a.runnextmk >= 1486511545 AND a.runnextmk <= '.mktime().' ORDER BY runnextmk DESC'; 
		$specs = '';
		$oby = ',e_id DESC LIMIT '.((int)$page*30).', 30';
		$csql = str_replace('e.*,', 'e.e_id', $sql);
		break;
		case "Expiredpilot":
		$sql = 'SELECT distinct e.* FROM (ebay e) LEFT JOIN autopilot_rules a ON e.e_id = a.e_id WHERE e.e_id = a.e_id AND a.runnextmk < '.mktime().'  ORDER BY runnextmk DESC'; 
		$specs = '';
		$oby = ',e_id DESC LIMIT '.((int)$page*30).', 30';
		$csql = str_replace('e.*,', 'e.e_id', $sql);
		break;
		
		case "Competitor":
		$sql = 'SELECT distinct e.* FROM (ebay e) LEFT JOIN competitor_rules c ON e.e_id = c.e_id WHERE e.e_id = c.e_id ORDER BY runnextmk DESC'; 
		$specs = '';
		$oby = ',e_id DESC LIMIT '.((int)$page*30).', 30';
		$csql = str_replace('e.*,', 'e.e_id', $sql);
		break;
		
		case "Dispose":
		$specs = 'FROM ebay WHERE `ebay_id` != 0  AND `ebended` IS NULL AND `dispose` > 0';		
		break;
		
		case "Ebayspec":
		$specs = 'FROM ebay WHERE `ebay_id` != 0  AND `ebended` IS NULL AND `eBay_specs` IS NOT NULL';		
		
		break;
		
		case "ChannelUnmatch":
		$specs = 'FROM ebay WHERE (`ebayquantity` !=  `qn_ch1` ||  `ebayquantity` !=  `qn_ch2`) AND `ebended` IS NULL AND `ebay_id` != 0';		
		
		break;
		case "Autopiloted30":
		
		
		break;
		case "Audited":
		$specs = 'FROM ebay WHERE audit IS NOT NULL AND `ebay_id` != 0  AND `ebended` IS NULL ';
		break;
		
		case "NotAudited":
		$specs = 'FROM ebay WHERE audit IS NULL AND `ebay_id` != 0  AND `ebended` IS NULL';
		break;
		 
		case "BcnsAudited":
		$sql = 'SELECT distinct e.* FROM (ebay e) LEFT JOIN warehouse w ON e.e_id = w.listingid WHERE `e`.`ebended` IS NOT NULL AND `w`.`deleted` = 0  AND `w`.`nr` = 0 AND `w`.`vended` = 0 AND `w`.`auditmk` <= '.$ofrom.' AND `w`.`auditmk` >= '.$oto; 
		$spec = '';
		$csql = str_replace('e.*,', 'e.e_id', $sql);
		break;
		
		case "BcnsNotAudited":
		$sql = 'SELECT distinct e.* FROM (ebay e) LEFT JOIN warehouse w ON e.e_id = w.listingid WHERE `e`.`ebended` IS NOT NULL AND `w`.`deleted` = 0  AND `w`.`nr` = 0 AND `w`.`vended` = 0 AND (`w`.`auditmk` > '.$ofrom.' OR `w`.`auditmk` < '.$oto.')'; 
		$spec = '';
		$csql = str_replace('e.*,', 'e.e_id', $sql);
		break;
		
		
		case "MisMatched": 
		$sql = 'SELECT distinct e.* FROM (ebay e) LEFT JOIN warehouse w ON e.e_id = w.listingid WHERE `e`.`ebay_id` != 0 AND `e`.`ebended` IS NULL  AND `w`.`deleted` = 0  AND `w`.`nr` = 0 AND `w`.`vended` = 0 AND `w`.`status` = "Mismatch"'; 
		$spec = '';
		$csql = str_replace('e.*,', 'e.e_id', $sql);
		break;
	
		case "NoTransBCNs":		
		$q = $this->db->query('SELECT et_id, e_id FROM ebay_transactions WHERE `e_id` != 0 AND `notpaid` = 0 AND `refunded` = 0 AND `pendingpay` = 0 AND `mkdt` <= '.$ofrom.' AND `mkdt` >= '.$oto);
		$wh = 'SELECT listingid, sold_id FROM warehouse WHERE sold_id != 0  AND listingid != 0 AND (';
	
		foreach ($q->result_array() as $t)
		{
			$wh .= 'sold_id = '.$t['et_id'].' OR ';
			$lst[$t['e_id']][$t['et_id']] = TRUE;
		}
		$w = $this->db->query(rtrim($wh, ' OR ').')');
		foreach ($w->result_array() as $s)
		{
			if (isset($lst[$s['listingid']][$s['sold_id']])) unset($lst[$s['listingid']][$s['sold_id']]);
		}
		foreach ($lst as $k => $v)
		{
			if (count($v) == 0) unset($lst[$k]);	
		}
		$sql = 'SELECT * FROM ebay WHERE `ebay_id` != 0 AND `ebended` IS NULL AND (';
		foreach ($lst as $lk => $lc)
		{
			 $sql .= 'e_id = '.$lk.' OR ';
		}
		$sql = rtrim($sql, ' OR ').')';
		$spec = '';
		$csql = str_replace('*', 'e_id', $sql);
		//printcool ($lst);
		$this->mysmarty->assign('listingorders', $lst);
                
		break;
		
	
	}
	$query = $sql.$specs.$oby;
	//printcool ($query);
	$cquery = $csql.$specs;
	$q = $this->db->query($query);
		
	if ($q->num_rows() > 0)
		{
			$cq = $this->db->query($cquery);
			$pages = ceil($cq->num_rows()/30);
			for ( $counter = 1; $counter <= $pages ; $counter++) $pagearray[] = $counter;
			$r = $q->result_array();
			foreach ($r as $rr)	$idarray[] = $rr['e_id'];	
			if (isset($idarray))
			{
				$CI =& get_instance();
				$CI->load->model('Myseller_model'); 	
				$CI->Myseller_model->getBase($idarray);
				$CI->Myseller_model->getOnHold($idarray);	
				$CI->Myseller_model->countSales($idarray);
				//$CI->Myseller_model->getEmptySales($idarray, 1);		
				$CI->load->model('Myautopilot_model'); 	
				$CI->Myautopilot_model->GetListingRules($idarray);				
				$CI->Myautopilot_model->GetCompetitorRules($idarray);	
			}
			return array('results' => $r, 'pages' => $pagearray, 'total' => $cq->num_rows());	
		}
}

function ListItems($string, $where = '', $ended = '', $zero = '', $mm = '', $bcnmm, $sitesell, $page)
	{	
		if ((int)$page > 0) $page--;
		$this->db->select("*");	
		if ($where == 1) $wl = 'e_part';
		elseif ($where == 2) $wl = 'ebay_id';
		elseif ($where == 3) $wl = 'e_id';
		elseif ($where == 4) $wl = 'storeCatID';
		elseif ($where == 5) $wl = 'old_e_part';
		else $wl = 'e_title';
		if ($string != '')
			{ 
				if ($where == 2 || $where == 3 || $where == 4)
				{
					$this->db->where($wl, trim(stripslashes($string)));	
					
				}
				else
				{
				$string = str_replace("'", "", $string);
				$string = str_replace('"', "", $string);
				$string = str_replace('&quot;', "", $string);			
				
				$string = explode(' ',trim($string));
				//$c=1;
					foreach ($string as $s)
					{
					$this->db->like($wl, $s);
					//if ($where == 1) $this->db->like('old_e_part', $s);	
					//printcool ($s);
					/*
					if ($c == 1) $this->db->like($wl, $s);	
					else $this->db->or_like($wl, $s);	*/
					//$c++;
					}
				}
			}
		if ($zero != '') $this->db->where('quantity <=', 0);
		if ($ended != '') $this->db->where('ebended !=', '');
		if ($mm != '') $this->db->where('quantity != ebayquantity');		
		if ($bcnmm != '') $this->db->where('quantity != e_qpart');	
		
		if ($sitesell == 1) $this->db->where('sitesell', 1);
		elseif ($sitesell === 0) $this->db->where('sitesell', 0);		
					
	//if ($this->session->userdata['admin_id'] == 1) printcool ($viewport);
		$this->db->order_by("e_id", "DESC");
		$this->db->limit(30, (int)$page*30);
		$this->query = $this->db->get('ebay');
		//if ($this->session->userdata['admin_id'] == 1) printcool ($this->db);
		if ($string != '')
			{ 
				if ($where == 2 || $where == 3 || $where == 4)
				{
					$this->db->where($wl, trim($string));	
				}
				else
				{
				//$c=1;
				foreach ($string as $s)
				{
				$this->db->like($wl, $s);	
				/*
				if ($c == 1) $this->db->like($wl, $s);	
				else $this->db->or_like($wl, $s);	*/
				//$c++;
				}
				}
			}	
		if ($ended != '') $this->db->where('ebended !=', '');
		if ($zero != '') $this->db->where('quantity <=', 0);
		if ($mm != '') $this->db->where('quantity != ebayquantity');		
		if ($bcnmm != '') $this->db->where('quantity != e_qpart');	
		if ($sitesell == 1) $this->db->where('sitesell', 1);
		elseif ($sitesell === 0) $this->db->where('sitesell', 0);

		$countall = $this->db->count_all_results('ebay');
		$pages = ceil($countall/30);
		for ( $counter = 1; $counter <= $pages ; $counter++) $pagearray[] = $counter;
		
		if ($this->query->num_rows() > 0)
		{
			$r = $this->query->result_array();

			$this->db->select('wsid, listing');
			$sku = 1;

			foreach ($r as $rr)
			{
				$idarray[] = $rr['e_id'];
				if ($sku == 1) $this->db->where('listing', $rr['e_id']);
				else $this->db->or_where('listing', $rr['e_id']);
				$sku++;
			}
			$s = $this->db->get('warehouse_sku_listing');
			if ($s->num_rows() > 0)
				{
					foreach ($s->result_array() as $ss)
					{
						$sk[$ss['listing']][] = $ss['wsid'];
					}
					$this->mysmarty->assign('listingskus', $sk);
				}
			if (isset($idarray))
			{
				$CI =& get_instance();
				$CI->load->model('Myseller_model');
				$CI->Myseller_model->getBase($idarray);
				$CI->Myseller_model->getOnHold($idarray);
				$CI->Myseller_model->countSales($idarray);
				//$CI->Myseller_model->getEmptySales($idarray, 1);
				$CI->load->model('Myautopilot_model');
				$CI->Myautopilot_model->GetListingRules($idarray);
				$CI->Myautopilot_model->GetCompetitorRules($idarray);
			}

			$return = array('results' => $r, 'pages' => $pagearray);

			$this->db->where('sitesell', 1);
			$count['onsite'] = $this->db->count_all_results('ebay');
			$this->db->where('sitesell', 0);
			$count['offsite'] = $this->db->count_all_results('ebay');

			$this->db->where('ebay_id !=', 0);
			$count['onebay'] = $this->db->count_all_results('ebay');
			$this->db->where('ebay_id', 0);
			$count['notonebay'] = $this->db->count_all_results('ebay');

			$this->db->where('ebended', NULL);
			$count['notended'] = $this->db->count_all_results('ebay');
			$this->db->where('ebended !=',  '');
			$count['ended'] = $this->db->count_all_results('ebay');
			$count['all'] = $this->db->count_all_results('ebay');
			$return['count'] = $count;




			return $return;

		}
		
		
		
		
		
	}
function GetTaxonomyValue($full = false)
	{
		//$this->db->select("gtaxonomy");
		
		if ($full) $this->db->where('gtaxonomy !=', '');
		else $this->db->where('gtaxonomy', '');
		return $this->db->count_all_results('ebay');
			
	}
function GetActionLog($string, $where = '',$page, $actabbr = array())
	{	
		if ((int)$page > 0) $page--;
		
		$abr = array_flip($actabbr);		

		if ($where == 2 && isset($abr[$string])) $string = $abr[$string];
		if ($where == 4 && $string == 'None') $string = 0;		
		if ($where == 1) $wl = 'ebay_id';
		elseif ($where == 2) $wl = 'field';	
		elseif ($where == 3) $wl = 'admin';
		elseif ($where == 4) $wl = 'trans_id';
		elseif ($where == 5) $wl = 'ctrl';
		elseif ($where == 6) $wl = 'time';
		elseif ($where == 7 && ($string == 'M' || $string == 'Q' || $string == 'B' || $string == 'G')) $wl = 'atype';
		else $wl = 'e_id';
		if ($string != '' && $where == 6) $this->db->like($wl, trim($string));					
		elseif ($string != '') $this->db->where($wl, trim($string));					
		$this->db->order_by("al_id", "DESC");
		$this->db->limit(300, (int)$page*300);
		$this->query = $this->db->get('ebay_actionlog');
		
		if ($where == 1) $wl = 'ebay_id';
		elseif ($where == 2) $wl = 'field';
		elseif ($where == 3) $wl = 'admin';
		elseif ($where == 4) $wl = 'trans_id';
		elseif ($where == 5) $wl = 'ctrl';
		elseif ($where == 6) $wl = 'time';
		else $wl = 'e_id';		
		if ($string != '' && $where == 6) $this->db->like($wl, trim($string));					
		elseif ($string != '') $this->db->where($wl, trim($string));				
		$countall = $this->db->count_all_results('ebay_actionlog');
		$pages = ceil($countall/300);
		for ( $counter = 1; $counter <= $pages ; $counter++) $pagearray[] = $counter;
		if ($this->query->num_rows() > 0) return array('results' => $this->query->result_array(), 'pages' => $pagearray);	
	}
	
function GetActionLogS($search = array(),$page, $actabbr = array())
	{	
		if ((int)$page > 0) $page--;
		
		$abr = array_flip($actabbr);		
		if (is_array ($search) && count($search) > 0)
		{
			foreach ($search as $k => $v)
			{
				if (trim($v) != '')
				{
					if ($k == 'id' && trim($v) != '') $this->db->where('al_id', trim($v));
					elseif ($k == 'local' && trim($v) != '') $this->db->where('e_id', trim($v));	
					elseif ($k == 'ebay' && trim($v) != '') $this->db->where('ebay_id', trim($v));	
					elseif ($k == 'field' && trim($v) != '') $this->db->where('field', trim($v));	
					elseif ($k == 'admin' && trim($v) != '') $this->db->where('admin', trim($v));	
					elseif ($k == 'trans' && trim($v) != '') $this->db->where('trans_id', trim($v));
					elseif ($k == 'action' && trim($v) != '') $this->db->where('ctrl', trim($v));	
					elseif ($k == 'type' && trim($v) != '') $this->db->where('atype', trim($v));	
					elseif ($k == 'time' && trim($v) != '') $this->db->like('time', trim($v));					
					elseif ($k == 'from' && trim($v) != '') $this->db->like('datafrom', trim($v));					
					elseif ($k == 'to' && trim($v) != '') $this->db->like('datato', trim($v));	
				}		
			}
		}
			
		$this->db->order_by("al_id", "DESC");
		$this->db->limit(300, (int)$page*300);
		$this->query = $this->db->get('ebay_actionlog');
		
		if (is_array ($search) && count($search) > 0)
		{
			foreach ($search as $k => $v)
			{
				if (trim($v) != '')
				{
					if ($k == 'id' && trim($v) != '') $this->db->where('al_id', trim($v));
					elseif ($k == 'local' && trim($v) != '') $this->db->where('e_id', trim($v));	
					elseif ($k == 'ebay' && trim($v) != '') $this->db->where('ebay_id', trim($v));	
					elseif ($k == 'field' && trim($v) != '') $this->db->where('field', trim($v));	
					elseif ($k == 'admin' && trim($v) != '') $this->db->where('admin', trim($v));	
					elseif ($k == 'trans' && trim($v) != '') $this->db->where('trans_id', trim($v));
					elseif ($k == 'action' && trim($v) != '') $this->db->where('ctrl', trim($v));	
					elseif ($k == 'type' && trim($v) != '') $this->db->where('atype', trim($v));	
					elseif ($k == 'time' && trim($v) != '') $this->db->like('time', trim($v));					
					elseif ($k == 'from' && trim($v) != '') $this->db->like('datafrom', trim($v));					
					elseif ($k == 'to' && trim($v) != '') $this->db->like('datato', trim($v));	
				}		
			}
		}
					
		$countall = $this->db->count_all_results('ebay_actionlog');
		$pages = ceil($countall/300);
		for ( $counter = 1; $counter <= $pages ; $counter++) $pagearray[] = $counter;
		if ($this->query->num_rows() > 0) return array('results' => $this->query->result_array(), 'pages' => $pagearray);	
	}
	
function SwapSiteSellVal($id)
	{
		$this->db->select("sitesell");
		$this->db->where('e_id', (int)$id);
		$q = $this->db->get('ebay');
		if ($q->num_rows() > 0) 
			{
				$r = $q->row_array();
				
				if ($r['sitesell'] == 0) $this->db->update('ebay', array('sitesell' => 1), array('e_id' => (int)$id));
				else $this->db->update('ebay', array('sitesell' => 0), array('e_id' => (int)$id));
				return true;
			}

	}
function GetActionLogItem($id)
	{	
		$this->db->where('e_id', (int)$id);	
		$this->db->order_by("al_id", "DESC");
		$this->query = $this->db->get('ebay_actionlog');
		if ($this->query->num_rows() > 0)
		{
			foreach($this->query->result_array() as $r)
			{
				$dates = ProcessDate($r['time']);
				$r['time'] = $dates['display'];	
				$mk = $dates['mk'];	
				$r['t'] = 'E';
				while(isset($l[$mk])) $mk++;
				$l[$mk] = $r;					
			}			
		}
		
		$this->db->where('eid', (int)$id);	
		$this->db->order_by("erlid", "DESC");
		$this->query = $this->db->get('ebay_revise_log');
		if ($this->query->num_rows() > 0)
		{
			foreach($this->query->result_array() as $r)
			{
				$dates = ProcessDate($r['attime']);
				$r['attime'] = $dates['display'];	
				$mk = $dates['mk'];	
				$r['t'] = 'R';
				while(isset($l[$mk])) $mk++;
				$l[$mk] = $r;				
			}			
		}
		
		$this->db->where('e_id', (int)$id);	
		$this->db->order_by("ec_id", "DESC");
		$this->query = $this->db->get('ebay_cron');
		if ($this->query->num_rows() > 0)
		{
			foreach($this->query->result_array() as $r)
			{
				$dates = ProcessDate($r['time']);
				$r['time'] = $dates['display'];	
				$mk = $dates['mk'];	
				$r['t'] = 'C';
				while(isset($l[$mk])) $mk++;
				$l[$mk] = $r;				
			}			
		}
		
		$this->db->select('wid');
		$this->db->where('listingid', (int)$id);
		$this->db->where('deleted', 0);
		$this->db->where('nr', 0);		
		$this->query = $this->db->get('warehouse');
		if ($this->query->num_rows() > 0)
		{
			$sql = 'SELECT * FROM warehouse_log WHERE ';
			foreach($this->query->result_array() as $r)
			{//printcool ($r);
			$sql .= '(wid = '.$r['wid'].' AND (field = "bcn" OR field = "status" OR field = "sold_id" OR field = "ordernotes" OR field = "channel" OR field = "listingid")) OR';				
			}
			$sql = rtrim($sql, 'OR');
			$sql .= ' ORDER BY wl_id DESC';
			$this->query = $this->db->query($sql);
			if ($this->query->num_rows() > 0)
			{
				$c = 0;
				foreach($this->query->result_array() as $r)
				{
					$dates = ProcessDate($r['time']);
					$r['time'] = $dates['display'];								
					$mk = $dates['mk'];	
					$r['t'] = 'W';
					while(isset($l[$mk])) $mk++;
					$l[$mk] = $r;
				}
			}
		}
		$sql = 'SELECT * FROM warehouse_log WHERE field = "listingid" AND datafrom = '.(int)$id.' ORDER BY wl_id DESC';			
			$this->query = $this->db->query($sql);
			if ($this->query->num_rows() > 0)
			{
				foreach($this->query->result_array() as $r)
				{
					$dates = ProcessDate($r['time']);
					$r['time'] = $dates['display'];								
					$mk = $dates['mk'];	
					$r['t'] = 'W';
					while(isset($l[$mk])) $mk++;
					$l[$mk] = $r;
				}
			}
		if (isset($l) && $l) krsort($l);
		return $l;
	}
function GetStoreFirstProduct($storeid = '')
	{
		$this->db->select("ebay_id");
		$this->db->where('storeCatID', (int)$storeid);		
		$this->db->where("ebay_id !=", 0);
		$this->db->order_by("e_id", "DESC");
		$this->db->limit(1);
		$r = $this->db->get('ebay');
		if ($r->num_rows() > 0) { $res = $r->row_array(); return $res['ebay_id']; }		
	}
function GetImage($id, $pos)
	{
		$this->db->select("e_img".$pos);
		$this->db->where("e_id", (int)$id);
		$this->db->limit(1);
		$r = $this->db->get('ebay');
		if ($r->num_rows() > 0) { $res = $r->row_array(); return $res['e_img'.$pos]; }		
	}
function EbayFromID($id = '')
	{
		$this->db->select("ebay_id");
		$this->db->where('e_id', (int)$id);		
		$r = $this->db->get('ebay');
		if ($r->num_rows() > 0) { $res = $r->row_array(); return $res['ebay_id']; }		
		else return 0;
	}
function GetFirstProduct()
	{
		$this->db->select("ebay_id");
		$this->db->where("ebay_id !=", 0);
		$this->db->order_by("e_id", "DESC");
		$this->db->limit(1);
		$r = $this->db->get('ebay');
		if ($r->num_rows() > 0) { $res = $r->row_array(); return $res['ebay_id']; }		
	}
function GeteBayID($id)
	{
		$this->db->select('ebay_id');
		$this->db->where('e_id', (int)$id);
		$query = $this->db->get('ebay');
		if ($q->num_rows() > 0) 

			{
				$res = $r->row_array(); return $res['ebay_id'];
			}
}
function GetItem($id)
	{
	    $this->db->join("warehouse_sku_categories","warehouse_sku_categories.wsc_id = ebay.storeCatID","left");
		$this->db->where('ebay.e_id', (int)$id);
		$this->query = $this->db->get('ebay');
		if ($this->query->num_rows() > 0) 
			{
			$this->result = $this->query->row_array();			
			if ($this->result['PaymentMethod'] == 'PayPal' ) $this->result['PaymentMethod'] = array ('PayPal' => 'on');
			elseif (strlen($this->result['PaymentMethod']) > 6) $this->result['PaymentMethod'] = unserialize($this->result['PaymentMethod']);
			if (strlen($this->result['shipping']) > 7) $this->result['shipping'] = unserialize($this->result['shipping']);
			$this->result['PaymentMethods'] = $this->result['PaymentMethod'];
			//$this->result['e_title'] = htmlspecialchars($this->result['e_title']);
			
		return $this->result;
		}
	}	
	
function GetStoreItemValues($storeid)
	{
	
		$this->db->where('storeCatID', (int)$storeid);
		$this->db->order_by("e_id", "DESC");
		$this->query = $this->db->get('ebay');
		if ($this->query->num_rows() > 0) 
			{
			$tmp = $this->query->row_array();			
			if ($tmp['PaymentMethod'] == 'PayPal' ) $this->tresult['PaymentMethod'] = array ('PayPal' => 'on');
			elseif (strlen($tmp['PaymentMethod']) > 6) $this->tresult['PaymentMethod'] = unserialize($tmp['PaymentMethod']);
			if (strlen($tmp['shipping']) > 7) $this->tresult['shipping'] = unserialize($tmp['shipping']);
			$this->tresult['PaymentMethods'] = $this->tresult['PaymentMethod'];
			$this->tresult['pCTitle'] = $tmp['pCTitle'];
			$this->tresult['primaryCategory'] = $tmp['primaryCategory'];
			$this->tresult['storeCatID'] = $tmp['storeCatID'];
			$this->tresult['storeCatTitle'] = $tmp['storeCatTitle'];
			$this->tresult['e_id'] = $tmp['e_id'];
			
			
		
			return $this->tresult;
			}
		
	
	}
	
function GetItemItemValues($eid)
	{
	
		$this->db->where('e_id', (int)$eid);		
		$this->query = $this->db->get('ebay');
		if ($this->query->num_rows() > 0) 
			{
			$tmp = $this->query->row_array();			
			if ($tmp['PaymentMethod'] == 'PayPal' ) $this->tresult['PaymentMethod'] = array ('PayPal' => 'on');
			elseif (strlen($tmp['PaymentMethod']) > 6) $this->tresult['PaymentMethod'] = unserialize($tmp['PaymentMethod']);
			if (strlen($tmp['shipping']) > 7) $this->tresult['shipping'] = unserialize($tmp['shipping']);
			$this->tresult['PaymentMethods'] = $this->tresult['PaymentMethod'];
			$this->tresult['pCTitle'] = $tmp['pCTitle'];
			$this->tresult['primaryCategory'] = $tmp['primaryCategory'];
			$this->tresult['storeCatID'] = $tmp['storeCatID'];
			$this->tresult['storeCatTitle'] = $tmp['storeCatTitle'];
			$this->tresult['e_id'] = $tmp['e_id'];
			
		
			return $this->tresult;
			}
		
	
	}
function GetSimilar($storcat = 0, $id = 0)
{
		$this->db->select("e_id, e_title, e_part, e_qpart, admin_id, storeCatTitle, pCTitle, listingDuration, startPrice, buyItNowPrice,  quantity, ebayquantity, ebay_submitted, e_img1, idpath, link, ebay_id, submitlog, gsid1, gsid2, gsid3, gsid4, gsid5");	
		$this->db->where('storeCatID', (int)$storcat);
		$this->db->where('e_id !=', (int)$id);
		$this->db->where('sitesell', 1);
		$this->db->order_by("e_id", "DESC");
		$this->db->limit(3);
		$this->query = $this->db->get('ebay');
		if ($this->query->num_rows() > 0) return $this->query->result_array();			
}
function GetSetting($key = '')
	{
		$this->db->where('skey', $key);
		$this->query = $this->db->get('settings');
		if ($this->query->num_rows() > 0) 
			{
			$r = $this->query->row_array();
			return $r['svalue'];
			}
	}	
function GetSheets()
{
	$this->db->select("gs_id, gs_title");
	$this->db->order_by("gs_title", "ASC");
	$q = $this->db->get('gs_sheets');
	if ($q->num_rows() > 0) return $q->result_array();

}
function Delete($id)
	{	
		$this->db->where('e_id', (int)$id);
		$this->db->delete('ebay'); 
	}
function Update($id, $data)
	{
		$this->db->update('ebay', $data, array('e_id' => (int)$id));
	}
function Insert($data)
	{
		$this->db->insert('ebay', $data);
		return $this->db->insert_id();
	}
function GetOldEbayImage($id, $place) 
	{	
		$this->db->select('e_img'.(int)$place);
		$this->db->where('e_id', (int)$id);
		$this->query = $this->db->get('ebay');
		if ($this->query->num_rows() > 0) 
			{
			$this->result = $this->query->row_array();
			return $this->result['e_img'.(int)$place];
			}
	}	
function DeleteEbayImage($id, $place) 
	{	
		$this->db->select('e_img'.(int)$place);
		$this->db->where('e_id', (int)$id);
		$this->query = $this->db->get('ebay');
		if ($this->query->num_rows() > 0) 
			{
			$this->result = $this->query->row_array();			
			$this->data = array ('e_img'.(int)$place => '');	
			$this->db->update('ebay', $this->data, array('e_id' => (int)$id));			
			return $this->result['e_img'.(int)$place];
		}
	}
function CheckSefExists($sef, $id = '')
	{
		$this->db->where('e_sef', $sef);
		if ($id != '') $this->db->where('e_id != '.(int)$id);
		$this->query = $this->db->get('ebay');
		if ($this->query->num_rows() > 0) 
			{
			$result = $this->query->row_array();
			return $result['e_sef'];
			}	
	}	
function GetEbayDataCategories($catid = 0)
	{
		if ($catid == 0) $this->db->where('catID = parentID');
		else {
				$this->db->where('parentID', (int)$catid);	
				$this->db->where('LeafCategory', 1);
			}				
			
		$q = $this->db->get('ebaydata_categories');
		if ($q->num_rows() > 0) 
		{
			foreach ($q->result_array() as $k => $v)
				{
					if ($v['parentID'] == $v['catID']) $cts[0][] = $v;
					elseif ($v['LeafCategory'] == 1) $cts[$v['parentID']][] = $v;				
				}
				
			return $cts;
		}
	}
function GetDistinctUsedEbayCategories()
	{
		$this->db->select('primaryCategory, pCTitle');
		$this->db->distinct();
		$q = $this->db->get('ebay');
		if ($q->num_rows() > 0) 
		{
			foreach ($q->result_array() as $k => $v)
				{
					if ($v['primaryCategory'] != 0 && $v['pCTitle'] != '') $loop[$v['primaryCategory']] = $v['pCTitle'];
				}

			asort($loop);	

			foreach ($loop as $k => $v)
			{
				$cts[0][] = array('catID' => $k, 'catName' => $v);
			}		
			
			return $cts;
		}
		/*$this->db->select('primaryCategory, pCTitle');
		$this->db->distinct();
		$q = $this->db->get('ebay');
		if ($q->num_rows() > 0) 
		{
			foreach ($q->result_array() as $k => $v)
				{
					if ($v['primaryCategory'] != 0 && $v['pCTitle'] != '') $cts[0][] = array('catID' => $v['primaryCategory'],
										'catName' => $v['pCTitle']);
					
					//$cts[0][] = array('catID' => $v['primaryCategory'],
					//					'catName' => $this->GetEbayCategoryTitle($v['primaryCategory'])
					//					);		
				}
			ksort($cts);
			return $cts;
		}*/
	}
function GetEbayCategoryData($catid)
	{
		$this->db->where('catID', (int)$catid);
		$q = $this->db->get('ebaydata_categories');
		if ($q->num_rows() > 0) return $q->row_array();	
	}
function GetEbayCategoryTitle($catid)
	{
		/*$this->db->select('catName');
		$this->db->where('catID', (int)$catid);
		$q = $this->db->get('ebaydata_categories');
		if ($q->num_rows() > 0) 
		{
			$c = $q->row_array();	
			return $c['catName'];
		}
		else return '';*/
		
		$this->db->select('catID, catName, parentID, CategoryLevel');
		$this->db->where('catID', (int)$catid);
		$q = $this->db->get('ebaydata_categories');
		$string = '';
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
					$string = $c['catName'].':'.$string;
					}			
			}
		}
		return $string;

	}
}
?>