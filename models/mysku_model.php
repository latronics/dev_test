<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mysku_model extends Model 
{
    function Mysku_model()
    {
        parent::Model();
    }
	function getnextsku()
	{
		$this->db->select('seq');
		$this->db->order_by("wsid", "DESC");
		$this->db->limit(1);
		$this->query = $this->db->get('warehouse_sku');
		if ($this->query->num_rows() > 0)
		{	$sku = $this->query->row_array();
			return ((int)$sku['seq']);
		}
		else return 0;
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
			$sqlstr = 'WHERE (';
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
		$q = $this->db->query('SELECT wid FROM warehouse '.$sqlstr.') AND bcn_p3 != "" AND waid > 148');
		//$q = $this->db->get('warehouse');	
		if ($q->num_rows() > 0) return ($q->num_rows());
		else return 0;		
	}
	function getlistingandskucount($sku)
	{
		$this->db->select('listing');
		$this->db->where('wsid', (int)$sku);
		$q1 = $this->db->get('warehouse_sku_listing');
		if ($q1->num_rows() > 0)
		{
			$listings = $q1->num_rows();
			//$this->db->select("wid");
			//$start = 1;
			$sqlstr = 'WHERE (';
			foreach ($q1->result_array() as $l)
			{
				//if ($start == 1) $this->db->where('listingid', (int)$l['listing']);
				//else $this->db->or_where('listingid', (int)$l['listing']);
				
				$sqlstr .= 'listingid = '.(int)$l['listing'].' OR ';				
				//$start++;
			}		
		}
		else $listings = 0;
		$sqlstr = rtrim( $sqlstr,'OR ');
		$q = $this->db->query('SELECT wid FROM warehouse '.$sqlstr.') AND bcn_p3 != "" AND waid > 148');
		//$q = $this->db->get('warehouse');	
		if ($q->num_rows() > 0) $bcn = $q->num_rows();
		else $bcn = 0;	
		return array('listings' => $listings, 'bcn' => $bcn );
		
	}
	function GetSkusAndListingsAndBCNs($wid)
	{
		$res1 = $full2 = $full3 = $bcncnt = false;
		
		$this->db->where('wid', (int)$wid);
		$this->db->order_by("wsid", "DESC");
		$q1 = $this->db->get('warehouse_sku');
		if ($q1->num_rows() > 0)
		{
			foreach ($q1->result_array() as $r1) $res1[$r1['wsid']] = $r1;
			$r2cnt = 1;
			
			$this->db->select("distinct l.*, e_title, `Condition`", false);
						
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
				$q3 = $this->db->query('SELECT wid, waid, bcn, bcn_p1, bcn_p2, bcn_p3, psku, listingid, aucid, status FROM warehouse '.$sqlstr.') AND bcn_p3 != "" AND waid > 148  ORDER BY wid DESC');
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
		
		
		$this->db->select("wid, bcn, status");
		$this->db->where('listingid', $listing);
		$this->db->where('bcn_p3 !=', '');
		$this->db->where('waid >', '148');
		//$this->db->where('psku', $sku);
		$this->db->where('deleted', 0);
		$this->db->order_by("wid", "DESC");
		$q = $this->db->get('warehouse');
		if ($q->num_rows() > 0) return $q->result_array();	
		
	}
}
?>