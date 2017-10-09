<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Myautopilot_model extends Model
{
    function Myautopilot_model()
    {
        parent::Model();
    }
	function ResetRules($eid = 0, $desc= '')
	{
		$this->db->where('e_id', (int)$eid);
		$r = $this->db->get('autopilot_rules');
		if ($r->num_rows() > 0)
		{
			foreach ($r->result_array() as $ar)
			{
				if ((int)$ar['hours'] == 1)	
				{
				 $array['runnext'] = date("Y-m-d H:i:s", time()+((int)$ar['daystocheck']*3600));
				 $array['runnextmk'] = mktime()+($ar['daystocheck']*3600);	
				}
				else
				{
				 $array['runnext'] = date('Y-m-d', strtotime("+".(int)$ar['daystocheck']." days"));
				 $array['runnextmk'] = mktime()+($ar['daystocheck']*3600*24);
				}
				$this->db->update('autopilot_rules', $array, array('rid' => $ar['rid']));
				if ($this->config->config['sendemails1']) GoMail(array ('msg_title' => 'Autopilot Rule Reset '.$desc.' ('.$ar['rid'].') @ '.CurrentTime(), 'msg_body' => printcool($array, TRUE, 'Mod').printcool($ar, TRUE, 'Mod'), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
			}
		}
	}
	function LogPriceChange($eid, $from, $to, $admin)
	{
		if ($from != $to) $this->db->insert('autopilot_log', array('apl_listingid' => (int)$eid, 'apl_from' => $from, 'apl_to' => $to, 'apl_adminid' => (int)$admin, 'apl_time' => CurrentTime(), 'apl_tstime' => mktime()));	
	}
	function ShowNoSold($days)
	{
		if((int)$days<1) return;
		$timestamp_today = mktime ();
        $timestamp_days_ago = mktime () - (int)$days * 3600 * 24;
		$sales = $this->db->query("select distinct e_id from ebay_transactions where mkdt between $timestamp_days_ago AND $timestamp_today");	
		if ($sales->num_rows() > 0)
        {
			$saleslist =  $sales->result_array();
		}
		$listings = $this->db->query("SELECT distinct e.e_id, a.daystocheck FROM (ebay e) LEFT JOIN autopilot_rules a ON e.e_id = a.e_id WHERE e.e_id = a.e_id"); // AND a.daystocheck = $days
		if ($listings->num_rows() > 0)
        {//printcool ($listings->result_array());
			foreach ($listings->result_array() as $l)
			{
				$activerules[$l['e_id']] = $l['daystocheck'];	
			}
			foreach ($saleslist as $s)
			{
				if (isset($activerules[$s['e_id']])) unset($activerules[$s['e_id']]);
			}
			return $activerules;
		}
		
	}
	function SavePriceChange($eid =0, $pricefrom = 0, $priceto = 0)
	{
		if (isset($this->session->userdata['admin_id'])) $admin = $this->session->userdata['admin_id'];
		else $admin = 0;
		if ($eid > 0 && $pricefrom != $priceto) $this->db->insert('autopilot_log', array('e_id' => (int)$eid, 'price_from' => $pricefrom, 'price_to' => $priceto, 'dateset' => CurrentTimeR(), 'datesetmk' => mktime(), 'admin' => $admin));
	}
    /*function ListProductsNotSold($period_days)
	{
        if((int)$period_days<1) return;
		$timestamp_today = mktime ();
        $timestamp_days_ago = mktime () - (int)$period_days * 3600 * 24;
        $ListProductsNotSold = $this->db->query("select * from ebay where e_id not in
                (select e_id from ebay_transactions where mkdt between $timestamp_today and $timestamp_days_ago)
                and ebay_id != 0 AND ebended IS NULL AND ebay_msubm<$timestamp_days_ago AND ebay_msubm !=0 LIMIT 5");
		if ($ListProductsNotSold->num_rows() > 0)
        {
			return $ListProductsNotSold->result_array();
		}
	}*/
    function AutopilotUpdater($changevalue=0, $percent=FALSE, $eid = 0, $storecat = 0)
    {
        if($changevalue<0.01 or ($eid === 0 and $storecat ===0)) return;
        if($storecat)//Update exact category prices for products not sold for a period
        {
            if($percent)
            {
                $this->db->set('price_ch1', 'price_ch1'-('price_ch1'/100*$changevalue), FALSE);
                $this->db->where('storeCatID', $storecat);
                $this->db->update('ebay'); // gives UPDATE mytable SET field = field+1 WHERE id = 2
                echo 'Category number '.$row['e_id'].' updated with percentage';
            }
            else
            {
                $this->db->set('price_ch2', 'price_ch2'-$changevalue, FALSE);
                $this->db->where('storeCatID', $storecat);
                $this->db->update('ebay');
                echo 'Category number '.$row['e_id'].' updated with fixed value';
            }
        }
        elseif($eid)//Update one product price for product not sold for a period
        {
            if($percent)
            {
                $this->db->set('price_ch1', 'price_ch1'-('price_ch2'/100*$changevalue), FALSE);
                $this->db->where('e_id', $eid);
                $this->db->update('ebay'); // gives UPDATE mytable SET field = field+1 WHERE id = 2
                echo 'One product number '.$row['e_id'].' updated with percentage';
            }
            else
            {
                $this->db->set('price_ch2', 'price_ch2'-$changevalue, FALSE);
                $this->db->where('e_id', $eid);
                $this->db->update('ebay');
                echo 'One product number '.$row['e_id'].' updated with fixed value';
            }
        }
        else //Update all products prices not sold for a period
        {
            foreach($this->ListProductsNotSold() as $row)
            {
                if($percent)
                {
                    $this->db->set('price_ch1', 'price_ch1'-('price_ch1'/100*$changevalue), FALSE);
                    $this->db->where('e_id', $row['e_id']);
                    $this->db->update('ebay'); // gives UPDATE mytable SET field = field+1 WHERE id = 2
                    echo 'Product number '.$row['e_id'].' updated with percentage';
                }
                else
                {
                    $this->db->set('price_ch1', 'price_ch1'-$changevalue, FALSE);
                    $this->db->where('e_id', $row['e_id']);
                    $this->db->update('ebay');
                    echo 'Product number '.$row['e_id'].' updated with fixed value';
                    //$row['e_id'].'<br>';
                    //$i++;
                }
            }
        }
    }
	function GetListingRules($idarray, $return = false)
	{
		if (count($idarray) == 0) return false;
		$c=1;
		foreach ($idarray as $i)
		{
			if ($c == 1)$this->db->where('e_id', (int)$i);
			else $this->db->or_where('e_id', (int)$i);
			$c++;
		}
		$a = $this->db->get('autopilot_rules');
		if ($a->num_rows() > 0)
		{
			foreach ($a->result_array() as $ar)
			{
				$rules[$ar['e_id']][$ar['rid']] = $ar;
			}
			if ($return) return $rules;
			else $this->mysmarty->assign('autopilotrules', $rules);
		}
	}
	function GetCompetitorRules($idarray, $return = false)
	{
		if (count($idarray) == 0) return false;
		$c=1;
		foreach ($idarray as $i)
		{
			if ($c == 1)$this->db->where('e_id', (int)$i);
			else $this->db->or_where('e_id', (int)$i);
			$c++;
		}
		$a = $this->db->get('competitor_rules');
		if ($a->num_rows() > 0)
		{
			foreach ($a->result_array() as $ar)
			{
				$rules[$ar['e_id']][$ar['cid']] = $ar;
			}
			if ($return) return $rules;
			else $this->mysmarty->assign('competitorrules', $rules);
		}
	}
    function GetRules($page = '', $all = TRUE)
	{
		if ((int)$page > 0) $page = $page - 1;
		$this->db->limit(200, (int)$page*200);
		$this->db->where('predefined', 0);
		$this->db->order_by("runnextmk", "DESC");
		$query = $this->db->get('autopilot_rules');
        $countall = $this->db->count_all_results('autopilot_rules');
        $pages = ceil($countall/200);
        for ( $counter = 1; $counter <= $pages ; $counter++)
        {
			$pagearray[] = $counter;
        }
		if ($query->num_rows() > 0)
        {
			return array('results' => $query->result_array(), 'pages' => $pagearray);
        }
	}
	function GetPredefined()
	{
		$this->db->where('predefined !=', 0);
		$this->db->order_by("daystocheck", "DESC");
		$query = $this->db->get('autopilot_rules');        
		if ($query->num_rows() > 0) return $query->result_array();
	}
	function Logs($page = 0, $eid = 0)
	{
		if ((int)$page > 0) $page = $page - 1;
		$this->db->limit(200, (int)$page*200);
		if ((int)$eid > 0) $this->db->where('apl_listingid', (int)$eid); 
		$this->db->order_by("apl_id", "DESC");
		$query = $this->db->get('autopilot_log');		
		
			if ((int)$eid > 0) $this->db->where('apl_listingid', (int)$eid); 
			$countall = $this->db->count_all_results('autopilot_log');
			$pages = ceil($countall/200);
			for ( $counter = 1; $counter <= $pages ; $counter++) 
			{
				$pagearray[] = $counter;
			}

		if ($query->num_rows() > 0) 
			{
				return array('results' => $query->result_array(), 'pages' => $pagearray);
			}	
		
	}
	function Chart($eid = 0, $days = 0)
	{
		
		if ((int)$days > 0) $from = 'AND mkdt >= '.(mktime()-($days*86400));
		else $from = '';
	
		$sales = $this->db->query("select et_id, datetime, rec, paid, mkdt, buyerid,buyeremail from ebay_transactions where e_id =".(int)$eid." ".$from." ORDER BY et_id DESC");	
		$min = mktime();
		$max = 0;
		$salelist = array();
		if ($sales->num_rows() > 0)
		{
			foreach($sales->result_array() as $s)
			{ 				
			 	//printcool ($s);
				$t = explode(' ', $s['datetime']);
				//printcool ($t);
				$date = explode('-', trim($t[0]));
				$mk = mktime(0,0,0,$date[1],$date[2],$date[0]);
				
				if ($mk < $min) $min = $mk;
				if ($mk > $max) $max = $mk;
				
				$block[$mk]['date'] = $date[0].'-'.$date[1].'-'.$date[2];
				$block[$mk]['sales']['E'.$s['et_id']] = $s;
				$otmatch[$s['et_id']] = $s['mkdt'];
				$idarray[$s['et_id']] = $s['et_id'];
				unset($t);
				
				$salelist[$s['mkdt']] = $s; 
			}
		}
		$sql = 'SELECT  `al_id`, `e_id`, `field`, `datafrom`, `datato`, `time` FROM `ebay_actionlog` WHERE `e_id` = '.(int)$eid.' AND (`field` = "Quantity" || `field` = "quantity" || `field` = "qn_ch1") ORDER BY al_id ASC';
		//$this->db->where('field', 'qn_ch1');	
		//$this->db->where('e_id', (int)$eid);					
		//$this->db->order_by("al_id", "ASC");
		//$qnq = $this->db->get('ebay_actionlog');
		$qnq = $this->db->query($sql);
	
		if ($qnq->num_rows() > 0)
		{
			$qn = $qnq->result_array();
			
			$lq = 0;	
			foreach ($qn as $k => $q)
			{
				//printcool ($q);
				$t = explode(' - ', $q['time']);
				if (count($t) == 1)
				{
					$t = explode(' ', $q['time']);
					$date = explode('-', trim($t[0]));
					if (strtolower($q['field']) == 'qn_ch1')
					{//printcool ($date);
						$mk = mktime(0,0,0,$date[1],$date[2],$date[0]);	
						$block[$mk]['date'] = $date[0].'-'.$date[1].'-'.$date[2];
						if ((int)$q['datato'] < 0) $q['datato'] = 0;
						$lq = $q['datato'];
						$block[$mk]['qn'] = $lq;
					}//else printcool ('nomatch');
				}
				else
				{
					$date = explode('/', trim($t[1]));					
					$mk = mktime(0,0,0,$date[1],$date[0],$date[2]);	
					$block[$mk]['date'] = $date[0].'-'.$date[1].'-'.$date[2];
					if (strtolower($q['field']) == 'quantity')
					{//printcool ($date);
						$mk = mktime(0,0,0,$date[1],$date[0],$date[2]);	
						$block[$mk]['date'] = $date[2].'-'.$date[1].'-'.$date[0];
						if ((int)$q['datato'] < 0) $q['datato'] = 0;
						$lq = $q['datato'];
						$block[$mk]['qn'] = $lq;
					}//else printcool ('nomatch');
				}
				//printcool (strtolower($q['field']));
				//printcool ($mk);
				if ($mk < $min) $min = $mk;
				if ($mk > $max) $max = $mk;
				
				
				
			}
		}
		
		if (isset($idarray))
			{				
				$CI =& get_instance();
				$CI->load->model('Myseller_model'); 	
				$bcns = $CI->Myseller_model->getSales($idarray, 1, TRUE);
				foreach ($bcns[1] as $o => $w)
				{	foreach ($w as $k => $v)
					$salelist[$otmatch[$o]]['bcns'][$v['wid']] = array('bcn' => $v['bcn'], 'cost'=>$v['cost'], 'netprofit' => $v['netprofit']);
				}
				
				
			}
		
		$this->db->select('o.oid,order,time');
		$this->db->where("r.e_id", (int)$eid);	
		if ((int)$days > 0)$this->db->where("submittime >= ", mktime()-($days*86400));
		$this->db->orderby('o.oid', 'ASC');
		$this->db->join('orders o', 'o.oid = r.orderid', 'LEFT');		
		$oquery = $this->db->get('order_listing_rel r');		
		
		if ($oquery->num_rows() > 0)
		{

			foreach($oquery->result_array() as $s)
			{ 				
				$eids = unserialize($s['order']);
				foreach ($eids as $k=>$v)
				{
					if ($k == (int)$eid)
					{//printcool ($s);
						$t = explode(' ', $s['time']);
				
						$date = explode('-', trim($t[0]));
						$time = explode(':', trim($t[1]));
						$mk = mktime(0,0,0,$date[1],$date[2],$date[0]);
						$mkdt  = mktime($time[0],$time[1],$time[2],$date[1],$date[2],$date[0]);
						$block[$mk]['date'] = $date[0].'-'.$date[1].'-'.$date[2];
						$block[$mk]['sales']['W'.$s['oid']] = $s;
						$ootmatch[$s['oid']] = $mkdt;
						$oidarray[$s['oid']] = $s['oid'];
						unset($t);
						$s['paid'] = $v['total'];
						$salelist[$mkdt] = $s; 
					}
				}
			}
		}
		
		
		if (isset($oidarray))
			{				
				$CI =& get_instance();
				$CI->load->model('Myseller_model'); 	
				$bcns = $CI->Myseller_model->getSales($oidarray, 2, TRUE);	
				
				foreach ($bcns[2] as $o => $w)
				{	foreach ($w as $k => $v)
					{
						//printcool ($o); printcool ($w); printcool ($k); printcool ($v);
						foreach ($v as $kk => $vv)
						{
						$salelist[$ootmatch[$o]]['bcns'][$vv['wid']] = array('bcn' => $vv['bcn'], 'cost'=>$vv['cost'], 'netprofit' => $vv['netprofit']);
						}
					}
				}
				
				
			}
		//printcool ($salelist);
		$this->db->select("distinct a.*, rulecreateddate, adminassigned", false);
		$this->db->where("apl_listingid", (int)$eid);
		if ((int)$days > 0) $this->db->where("apl_tstime >=", mktime()-($days*86400));	

		$this->db->orderby('apl_id', 'ASC');
		$this->db->join('autopilot_rules r', 'a.apl_rid = r.rid', 'LEFT');		
		$query = $this->db->get('autopilot_log a');		
		$initprice = FALSE;
		$prices = array();
		if ($query->num_rows() > 0 ) 
		{
			foreach($query->result_array() as $q) 
			{ 							
				//$t = explode(' ', $q['apl_tstime']);
				//$date = explode('-', trim($t[0]));
				//$mk = mktime(0,0,0,$date[1],$date[2],$date[0]);				
				
				$mk = $q['apl_tstime'];
				
				if ($mk < $min) $min = $mk;
				if ($mk > $max) $max = $mk;
				
				if (!$initprice) $initprice = $block[$mk]['prices'][] = $q['apl_from']; 
				//printcool ($q);
				
				/*if ($q['rulecreateddate'] != '')
				{
					$block[$mk]['prices'][] = "{
					y: ".$q['apl_to'].",
					marker: {
						symbol: 'url(http://www.la-tronics.com/images/admin/star.png)'
					}
					}
					";
					
				}
				else*/ 
				
				$block[$mk]['prices'][] = $q['apl_to'];

				if (!isset($block[$mk]['date'])) $block[$mk]['date'] = date("Y-m-d", $mk);
				if (!isset($block[$mk]['sales'])) $block[$mk]['sales'] = array();
				
				$prices[$q['apl_tstime']] = $q; 
				
			}
		}
		else
		{
			$listprices = $this->db->query("select price_ch2 FROM ebay where e_id =".(int)$eid);	
			if ($listprices->num_rows() > 0)
			{
				$lp = $listprices->row_array();
				$initprice = $block[$min]['prices'][] = $lp['price_ch2'];
				
			}
		}
		
		//printcool ($initprice);
		while ($min < $max)
		{
			$min = $min+86400;
			if (!isset($block[$min]))
			{
				$block[$min]['date'] = date("Y-m-d", $min);
				$block[$min]['sales'] = array();
				
			}
		}
		
		
		
		
		
				
		$this->db->where("e_id", (int)$eid);
		$query = $this->db->get('autopilot_rules');		
		if ($query->num_rows() > 0 ) 
		{
			foreach($query->result_array() as $q) 
			{ 				
				$q['rulecreateddatemk'] = explode(' ', $q['rulecreateddate']);
				//printcool ($t);
				$q['rulecreateddatemk'][0] = explode('-', trim($q['rulecreateddatemk'][0]));
				//$q['rulecreateddatemk'][1] = explode(':', trim($q['rulecreateddatemk'][1]));
				$q['rulecreateddatemk'] = mktime(0,0,0,$q['rulecreateddatemk'][0][1],$q['rulecreateddatemk'][0][2],$q['rulecreateddatemk'][0][0]);
				
				
				$block[$q['rulecreateddatemk']]['date'] = date("Y-m-d", $q['rulecreateddatemk']);
				$block[$q['rulecreateddatemk']]['created'] = $q['rid'];

				
			
			}
		}
		ksort($block);
		$totalsales = 0;
		foreach ($block as $k => $v)
		{
				$totalsales = $totalsales + count($v['sales']);
				$block[$k]['totalsales'] = $totalsales;			
		}
				
		foreach ($block as $k => $v)
		{
			//printcool ($block[$k]['prices']);
				if (!isset($block[$k]['prices'])) $block[$k]['prices'][] = $initprice;				
				 
				else $initprice = end($block[$k]['prices']);	
		}
		ksort($salelist);
		//printcool ($block);
		//exit();printcool ();
		return (array('blockdata' => $block, 'prices' => $prices, 'sales' => $salelist));

	}
	function NewChart($listingid = 0,$filtertype = false,$filtersubtype = false)
	{
	    $prq = false;

        $CI =& get_instance();
        $CI->load->model('Myorders_model');
        $sales = $CI->Myorders_model->GetOrders('','',$filtertype,$filtersubtype,TRUE,FALSE,(int)$listingid, TRUE);
        if ($prq) printcool ($sales);
        if (!$sales) $sales = array();

	
//	printcool ($this->session->userdata('dto'));
		//$postto = explode('/', $this->session->userdata('dto'));
		//if (isset($postto[3])) $min = mktime(0, 0, 0, $postto[0], $postto[1], $postto[2]);
		//else $min = (mktime()+$tdf)-1296000;
        
		$min = mktime();
		$max = 0;
		$salelist = array();
		//printcool ($sales);
        foreach($sales as $s)
		{
		 	//printcool ($s);
			//$t = explode(' ', $s['created']);
			//printcool ($t);
			//$date = explode('-', trim($t[0]));
			$mk =$s['timekey'];

			if ($mk < $min) $min = $mk;
			if ($mk > $max) $max = $mk;
				
			$block[$mk]['date'] = date('Y-m-d',$mk);
		    if($s['channel'] == 1)
            {
               $block[$mk]['sales']['E'.$s['orderkey']] = $s;
            }
            elseif($s['channel'] == 2)
            {
               $block[$mk]['sales']['O'.$s['orderkey']] = $s;
            }
            elseif($s['channel'] == 4)
            {
                   $block[$mk]['sales']['W'.$s['orderkey']] = $s;
            }
            $otmatch[$s['orderkey']] = $s['timekey'];
            $idarray[$s['channel']][$s['orderkey']] = $s['orderkey'];
			unset($t);

			$salelist[$s['timekey']] = $s;
		}
		
		//printcool ($block);

        $sql = 'SELECT  `al_id`, `e_id`, `field`, `datafrom`, `datato`, `time` FROM `ebay_actionlog` WHERE `e_id` = '.(int)$listingid.' AND (`field` = "Quantity" || `field` = "quantity" || `field` = "qn_ch1") ORDER BY al_id ASC';
		$qnq = $this->db->query($sql);
	//printcool($min);
	//printcool ($max);
		if ($qnq->num_rows() > 0)
		{
			$qn = $qnq->result_array();
			$lq = 0;	
			foreach ($qn as $k => $q)
			{
				//printcool ($q);
				$t = explode(' - ', $q['time']);
				 
				if (count($t) == 1)
				{
					$t = explode(' ', $q['time']);
					$date = explode('-', trim($t[0]));
					$time = explode(':', trim($t[1]));
				}
				else
				{
					
					$date = explode('/', trim($t[1]));
					$time = explode(':', trim($t[0]));	
				} 
				if ($date[2] > 31) $date = array_reverse($date);
				$mk = mktime(0,0,0,$date[1],$date[2],$date[0]);	
				//if ($mk >= $min)
				//{
				$block[$mk]['date'] = $date[0].'-'.$date[1].'-'.$date[2];
				//printcool ( $date[2].'-'.$date[1].'-'.$date[0]);
				
				if ((int)$q['datato'] < 0) $q['datato'] = 0;
				$lq = $q['datato'];
				$block[$mk]['qn'] = $lq;
				
				if ($mk < $min) $min = $mk;
				if ($mk > $max) $max = $mk;
				//}
			}
		}
		if (isset($idarray))
        {
            foreach ($idarray as $ik => $iv)
            {
               // printcool($iv);
                if (count($iv)>0)
                {
                    $CI =& get_instance();
                    $CI->load->model('Myseller_model');

                        $bcns = $CI->Myseller_model->getSales($iv, $ik, TRUE);
                        //printcool($bcns);
					if (count($bcns) >0 )foreach ($bcns[$ik] as $o => $w)
                        {	foreach ($w as $k => $v)
                            $salelist[$otmatch[$o]]['bcns'][$v['wid']] = array('bcn' => $v['bcn'], 'cost'=>$v['cost'], 'netprofit' => $v['netprofit'],'paid' => $v['paid']);
                        }
                }
            }
        }

        if ($prq) printcool ($salelist);
        if ($prq)  printcool ($min);
        if ($prq) printcool ($max);

		//printcool ($salelist);


        $this->db->select("distinct a.*, rulecreateddate, adminassigned", false);
		$this->db->where("apl_listingid", (int)$listingid);

        /*$sesto = $this->session->userdata('dto');
        $oto = (mktime()+$tdf)-1296000;
        if (isset($_POST['oto']) || $sesto)
        {
            if (isset($_POST['oto']))  $dto = trim($_POST['oto']);
            else $dto = $sesto;
            $postto = explode('/', $dto);
            $oto = mktime(0, 0, 0, $postto[0], $postto[1], $postto[2])+$tdf;
        }

        $this->db->where("apl_tstime >=", $oto);
        */
        /////////////
		$this->db->orderby('apl_id', 'DESC');
		$this->db->join('autopilot_rules r', 'a.apl_rid = r.rid', 'LEFT');		
		$query = $this->db->get('autopilot_log a');		
		$initprice = FALSE;
		$prices = array();
		
		$listprices = $this->db->query("select price_ch1 FROM ebay where e_id =".(int)$listingid);
			if ($listprices->num_rows() > 0)
			{
				$lp = $listprices->row_array();
				$initprice = $block[$min]['prices'][] = $lp['price_ch1'];
			}
			
		if ($query->num_rows() > 0 ) 
		{
			foreach($query->result_array() as $q) 
			{ 							
				//$t = explode(' ', $q['apl_time']);
				//$date = explode('-', trim($t[0]));
				//$mk = mktime(0,0,0,$date[1],$date[2],$date[0]);				
				
				$mk = $q['apl_tstime'];

				if ($mk < $min) $min = $mk;
				if ($mk > $max) $max = $mk;
				
				if (!$initprice) $initprice = $block[$mk]['prices'][] = $q['apl_from']; 
				//printcool ($q);
				
				/*if ($q['rulecreateddate'] != '')
				{
					$block[$mk]['prices'][] = "{
					y: ".$q['apl_to'].",
					marker: {
						symbol: 'url(http://www.la-tronics.com/images/admin/star.png)'
					}
					}
					";
					
				}
				else*/ 
				
				
				/*
				$block[$mk]['prices'][] = $q['apl_to'];

				if (!isset($block[$mk]['date'])) $block[$mk]['date'] = date("Y-m-d", $mk);
				if (!isset($block[$mk]['sales'])) $block[$mk]['sales'] = array();
				
				$prices[$q['apl_tstime']] = $q;*/
			}
		}
		else
		{	
			$listprices = $this->db->query("select price_ch1 FROM ebay where e_id =".(int)$listingid);
			if ($listprices->num_rows() > 0)
			{
				$lp = $listprices->row_array();
				$initprice = $block[$min]['prices'][] = $lp['price_ch1'];
			}
		}
		
		
		////////////
		 $sql = 'SELECT  `al_id`, `e_id`, `field`,`ctrl`, `datafrom`, `datato`, `time`,`admin` FROM `ebay_actionlog` WHERE `e_id` = '.(int)$listingid.' AND `field` = "price_ch1"  ORDER BY al_id ASC'; //AND `ctrl` != "Autopilot"
		$prq = $this->db->query($sql);
	//printcool($min);
	//printcool ($max);
		if ($qnq->num_rows() > 0)
		{
			$pr = $prq->result_array();
			
			foreach ($pr as $k => $q)
			{
				//printcool ($q);
				$t = explode(' - ', $q['time']);
				 
				if (count($t) == 1)
				{
					$t = explode(' ', $q['time']);
					$date = explode('-', trim($t[0]));
					$time = explode(':', trim($t[1]));
				}
				else
				{
					
					$date = explode('/', trim($t[1]));
					$time = explode(':', trim($t[0]));	
				} 
				if ($date[2] > 31) $date = array_reverse($date);
				$mk = mktime(0,0,0,$date[1],$date[2],$date[0]);	
			 
				$block[$mk]['date'] = $date[0].'-'.$date[1].'-'.$date[2];
				 $mkm = mktime($time[0],$time[1],$time[2],$date[1],$date[2],$date[0]);	
				 if (!$initprice) $initprice = $block[$mkm]['prices'][] = $q['datafrom']; 
				if ($q['datato'] != $q['datafrom'])
				{
				$price = $q['datato'];
				$block[$mkm]['prices'][] = $price;

				if (!isset($block[$mk]['date'])) $block[$mk]['date'] = date("Y-m-d", $mk);
				if (!isset($block[$mk]['sales'])) $block[$mk]['sales'] = array();
				
				$prices[$mkm] = $q;
		
				if ($mk < $min) $min = $mk;
				if ($mk > $max) $max = $mk; 
				}
				 
			}
		}
		
		//////////
		
		//printcool($block);
		//printcool ($min);
		//printcool ($initprice);
		while ($min < $max)
		{
			$min = $min+86400;
			if (!isset($block[$min]))
			{
				$block[$min]['date'] = date("Y-m-d", $min);
				$block[$min]['sales'] = array();
			}
		}
				
		$this->db->where("e_id", (int)$listingid);
		$query = $this->db->get('autopilot_rules');		
		if ($query->num_rows() > 0 ) 
		{
			foreach($query->result_array() as $q) 
			{ 				
				$q['rulecreateddatemk'] = explode(' ', $q['rulecreateddate']);
				//printcool ($t);
				$q['rulecreateddatemk'][0] = explode('-', trim($q['rulecreateddatemk'][0]));
				//$q['rulecreateddatemk'][1] = explode(':', trim($q['rulecreateddatemk'][1]));
				$q['rulecreateddatemk'] = mktime(0,0,0,$q['rulecreateddatemk'][0][1],$q['rulecreateddatemk'][0][2],$q['rulecreateddatemk'][0][0]);
				
				
				$block[$q['rulecreateddatemk']]['date'] = date("Y-m-d", $q['rulecreateddatemk']);
				$block[$q['rulecreateddatemk']]['created'] = $q['rid'];
			}
		}
		ksort($block);
		$totalsales = 0;
		foreach ($block as $k => $v)
		{
				$totalsales = $totalsales + count($v['sales']);
				$block[$k]['totalsales'] = $totalsales;			
		}
		foreach ($block as $k => $v)
		{
			//printcool ($block[$k]['prices']);
				if (!isset($block[$k]['prices'])) $block[$k]['prices'][] = $initprice;
				else $initprice = end($block[$k]['prices']);	
		}
		ksort($salelist);
		//printcool ($block);
		//exit();printcool ();
		krsort($prices);
		return (array('blockdata' => $block, 'prices' => $prices, 'sales' => $salelist));

	}
	function gChart($eid = 0)
	{
		$sales = $this->db->query("select et_id, datetime, rec, paid, mkdt from ebay_transactions where e_id =".(int)$eid." ORDER BY et_id ASC");	
		$return['results'] = array();
		$saleslist = array();
		$return['mix']= array();
		$now = mktime();
		if ($sales->num_rows() > 0)
		{
			foreach($sales->result_array() as $s)
			{ 
			 	$saleslist[$s['mkdt']] = $s; 
				$return['mix'][$s['mkdt']]['sale'] = $s; 
			}
		}
		$return['sales'] = $saleslist;
		//printcool ($saleslist);
		
		$this->db->where("apl_listingid", (int)$eid);
		$this->db->orderby('apl_id', 'ASC');
		$query = $this->db->get('autopilot_log');		
		$initprice = FALSE;
		if ($query->num_rows() > 0 ) 
		{
			foreach($query->result_array() as $q) 
			{ 
				$return['results'][$q['apl_tstime']] = $q; 
				$return['mix'][$q['apl_tstime']]['log'] = $q; 
				if (!$initprice) $initprice = $q['apl_from'];
			}
		}
		ksort($return['mix']);
		//printcool ($return['mix']);
		$start = 0;
		$tomix['price'] = 0;
		$tomix['sale'] = 0;
		
		foreach ($return['mix'] as $m)
		{
				if ($start == 0)
				{
					 $tomix['price'] =  $initprice;
				}
	
				if (isset($m['log'])) 
				{
					$tomix['price'] = $m['log']['apl_to'];
					$start = ceil(($now - $m['log']['apl_tstime'])/3600/24);
				}
				elseif (isset($m['sale'])) 
				{
					$tomix['sale']++;// = $m['sale']['paid'];
					$start = ceil(($now - $m['sale']['mkdt'])/3600/24);
				}
				
				$mix[] = array($start,(float)$tomix['price'],(int)$tomix['sale']);
				//$start++;
		}
		//printcool ($mix);
		$return['mix'] = $mix;
		return ($return);

	}
    
}
?>