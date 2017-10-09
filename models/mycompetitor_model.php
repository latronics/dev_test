<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Mycompetitor_model extends Model
{
    function Mycompetitor_model()
    {
        parent::Model();
    }
	function ResetRules($eid = 0)
	{
		$this->db->where('e_id', (int)$eid);
		$r = $this->db->get('competitor_rules');
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
				$this->db->update('competitor_rules', $array, array('cid' => $ar['cid']));
				GoMail(array ('msg_title' => 'Competitor Rule Reset ('.$ar['cid'].') @ '.CurrentTime(), 'msg_body' => printcool($array, TRUE, 'Mod'), 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
			}
		}
	}
	function LogPriceChange($eid, $from, $to, $admin)
	{
		if ($from != $to) $this->db->insert('competitor_log', array('apl_listingid' => (int)$eid, 'apl_from' => $from, 'apl_to' => $to, 'apl_adminid' => (int)$admin, 'apl_time' => CurrentTime(), 'apl_tstime' => mktime()));	
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
		$listings = $this->db->query("SELECT distinct e.e_id, a.daystocheck FROM (ebay e) LEFT JOIN competitor_rules a ON e.e_id = a.e_id WHERE e.e_id = a.e_id"); // AND a.daystocheck = $days
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
		if ($eid > 0 && $pricefrom != $priceto) $this->db->insert('competitor_log', array('e_id' => (int)$eid, 'price_from' => $pricefrom, 'price_to' => $priceto, 'dateset' => CurrentTimeR(), 'datesetmk' => mktime(), 'admin' => $admin));
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
		//$this->db->where('predefined', 0);
		//$this->db->order_by("daystocheck", "DESC");
		$query = $this->db->get('competitor_rules');
        $countall = $this->db->count_all_results('competitor_rules');
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
 
	function Logs($page = 0, $eid = 0)
	{
		if ((int)$page > 0) $page = $page - 1;
		$this->db->limit(200, (int)$page*200);
		if ((int)$eid > 0) $this->db->where('apl_listingid', (int)$eid); 
		$this->db->order_by("apl_id", "DESC");
		$query = $this->db->get('competitor_rules_log');		
		
			if ((int)$eid > 0) $this->db->where('apl_listingid', (int)$eid); 
			$countall = $this->db->count_all_results('competitor_rules_log');
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
    
}
?>