<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mywarehouselocations_model extends Model 
{
    function Mywarehouselocations_model()
    {
        parent::Model();
    }
	
	
	function GetNavigation()
	{
		$this->db->orderby('loc_id', 'ASC');
		$l = $this->db->get('locations');
		$loc = array();
		if ($l->num_rows() > 0)
		{
			foreach ($l->result_array() as $ll)
			{
				$loc[$ll['cabid']][$ll['loc_id']] = $ll['loc_name'];	
			}
		}
		$this->db->orderby('cont_id', 'ASC');
		$c = $this->db->get('locations_containers');
		$cont = array();
		if ($c->num_rows() > 0)
		{
			foreach ($c->result_array() as $cc)
			{
				$cont[$cc['loc_id']][$cc['cont_id']] = $cc['cont_name'];
				$contnames[$cc['cont_id']] = $cc['cont_name'];
			}
		}
		
		$this->db->select('loc_id, cont_id');
		$this->db->where('loc_id !=', '0');
		$q = $this->db->get('warehouse');
		if ($q->num_rows() > 0)
		{
			$bcns = $q->result_array();
			foreach ($bcns as $r)
			{
				if (isset($countloc[$r['loc_id']])) $countloc[$r['loc_id']]++;
				else $countloc[$r['loc_id']] = 1;
				if ($r['cont_id'] > 0)
				{
					if (isset($countcont[$r['cont_id']])) $countcont[$r['cont_id']]++;
					else $countcont[$r['cont_id']] = 1;	
				}
	
			}				
		}
		
		return array('locations' => $loc, 'containers' => $cont, 'containernames' => $contnames, 'countloc' =>$countloc, 'countcont' => $countcont);
	}
	function NextContainer()
	{
		$this->db->select('cont_id');
		$this->db->orderby('cont_id', 'DESC');
		$c = $this->db->get('locations_containers', 1);
		$cc = 1;
		if ($c->num_rows() >0) { $cc = $c->row_array(); $cc = $cc['cont_id']+1; }
		return $cc;
	}
	function GetLocation($loc_name)
	{
		$this->db->select('loc_id, loc_name');
		$this->db->where('loc_name', trim(ucwords($loc_name)));
		$this->db->or_where('loc_name', strtoupper(trim($loc_name)));
		$this->db->or_where('loc_name', strtolower(trim($loc_name)));
		$l = $this->db->get('locations', 1);
		if ($l->num_rows() >0)
		 {
			  $ll = $l->row_array();
			  return ($ll['loc_id']);
		 }
	}
	function NewLocation($loc_name)
	{
		$this->db->insert('locations', array('loc_name' => trim(ucwords($loc_name))));
		return $this->db->insert_id();
	}
	function GetContainer($cont_id)
	{
		$this->db->select('cont_id, cont_name');
		$this->db->where('cont_id', (int)$cont_id);
		$l = $this->db->get('locations_containers', 1);
		if ($l->num_rows() >0)
		 {			  
		 		$lc = $l->row_array();
			  return ($lc['cont_id']);
		 }
	}
	function GetContainerLocation($cont_id)
	{
		$this->db->select('loc_id');
		$this->db->where('cont_id', (int)$cont_id);
		$l = $this->db->get('locations_containers', 1);
		if ($l->num_rows() >0)
		 {
			  $lc = $l->row_array();
			  return ($lc['loc_id']);
		 }
	}
	function NewContainer($loc_id= 0)
	{
		$this->db->insert('locations_containers', array('loc_id' => (int)$loc_id));
		$id = $this->db->insert_id();
		$this->db->update('locations_containers', array('cont_name' => $id), array('cont_id' => (int)$id));
		return $id;
	}
	function UpdateContainer($cont_id, $loc_id)
	{
		$this->db->update('locations_containers', array('loc_id' => (int)$loc_id), array('cont_id' => (int)$cont_id));	
	}
	function AddBCNs($bcns, $cont_id, $loc_id = 0)
	{
		$bcns = explode(PHP_EOL, trim($bcns));
		$thebcns = false;
		foreach ($bcns as $b)
		{
			if (trim($b) != '') $thebcns[] = $b;	
		}
		if (is_array($thebcns) && count($thebcns) > 0)
		{
			$this->db->select('wid, bcn, location, loc_id, cont_id');
			$c = 0;
			foreach($thebcns as $bcn)
			{
			if ($c == 0) $this->db->where('bcn', trim($bcn));
			else $this->db->or_where('bcn', trim($bcn));
			$c++;
			}
			$b = $this->db->get('warehouse');
			if ($b->num_rows() > 0)
			{
				if (isset($_POST['ccprint']) && (int)$_POST['ccprint'] == 1)
				{
					$sessbcns = array();
				}
				$cnt = $b->num_rows();
				
				if ($cont_id == 0)
				{
					$this->db->select('loc_name');
					$this->db->where('loc_id', (int)$loc_id);
					$l = $this->db->get('locations', 1);
					$locationname = '?';
					if ($l->num_rows() >0)
					 {
						  $ll = $l->row_array();
						  $locationname = $ll['loc_name'];
					 }				
				}
				else $locationname = $cont_id;

				if (isset($this->session->userdata['admin_id'])) $admin = $this->session->userdata['admin_id'];
				else $admin = 'Cron';
				$url = $place = $this->router->method;
				
				foreach ($b->result_array() as $bw)
				{
					$sessbcns[] = $bw['wid'];
					$this->db->update('warehouse', array('cont_id' => $cont_id, 'loc_id' => (int)$loc_id, 'location' => $locationname), array('wid' =>$bw['wid']));	
					$b = $bw;
					if ($b['loc_id'] != $loc_id) $this->db->insert('warehouse_log', array('bcn' => $b['bcn'], 'wid'=> $b['wid'], 'time' => CurrentTime(), 'datafrom' => $b['loc_id'], 'datato' => $loc_id, 'field' => 'loc_id', 'admin' => $admin, 'ctrl' => $place, 'url' => $url, 'year' => date('Y'), 'month' => date('m'), 'day' => date('d')));
				
					if ($b['location'] != $locationname) $this->db->insert('warehouse_log', array('bcn' => $b['bcn'], 'wid'=> $b['wid'], 'time' => CurrentTime(), 'datafrom' => $b['location'], 'datato' => $locationname, 'field' => 'location', 'admin' => $admin, 'ctrl' => $place, 'url' => $url, 'year' => date('Y'), 'month' => date('m'), 'day' => date('d')));
					
					if ($b['cont_id'] != $cont_id) $this->db->insert('warehouse_log', array('bcn' => $b['bcn'], 'wid'=> $b['wid'], 'time' => CurrentTime(), 'datafrom' => $b['cont_id'], 'datato' => $cont_id, 'field' => 'cont_id', 'admin' => $admin, 'ctrl' => $place, 'url' => $url, 'year' => date('Y'), 'month' => date('m'), 'day' => date('d')));						
					
				}
				if (isset($_POST['ccprint']) && (int)$_POST['ccprint'] == 1)
				{
					$this->session->set_flashdata('sessbcns', $sessbcns);			
				}	
				return $cnt;
			}				
		}
	}
	function ChangeBCNsContainer($cont_id, $loc_id)
	{
		$bcns = $this->GetContainerBCNs($cont_id);
		if ($bcns)
		{
			$cnt = 0;
			
			if (isset($this->session->userdata['admin_id'])) $admin = $this->session->userdata['admin_id'];
			else $admin = 'Cron';
			$url = $place = $this->router->method;			
				
			foreach ($bcns as $b)
			{
				$this->db->update('warehouse', array('loc_id' => (int)$loc_id, 'cont_id' => $cont_id, 'location' => $cont_id), array('wid' =>$b['wid']));	
				
				if ($b['loc_id'] != $loc_id) $this->db->insert('warehouse_log', array('bcn' => $b['bcn'], 'wid'=> $b['wid'], 'time' => CurrentTime(), 'datafrom' => $b['loc_id'], 'datato' => $loc_id, 'field' => 'loc_id', 'admin' => $admin, 'ctrl' => $place, 'url' => $url, 'year' => date('Y'), 'month' => date('m'), 'day' => date('d')));
				
				if ($b['location'] != $cont_id) $this->db->insert('warehouse_log', array('bcn' => $b['bcn'], 'wid'=> $b['wid'], 'time' => CurrentTime(), 'datafrom' => $b['location'], 'datato' => $cont_id, 'field' => 'location', 'admin' => $admin, 'ctrl' => $place, 'url' => $url, 'year' => date('Y'), 'month' => date('m'), 'day' => date('d')));
					
				if ($b['cont_id'] != $cont_id) $this->db->insert('warehouse_log', array('bcn' => $b['bcn'], 'wid'=> $b['wid'], 'time' => CurrentTime(), 'datafrom' => $b['cont_id'], 'datato' => $cont_id, 'field' => 'cont_id', 'admin' => $admin, 'ctrl' => $place, 'url' => $url, 'year' => date('Y'), 'month' => date('m'), 'day' => date('d')));			
				$cnt++;
			}
			return $cnt;
		}
	}
	function GetLocationBCNs($loc_id)
	{
		$sql = 'SELECT w.wid, w.bcn, w.cont_id, w.loc_id, w.title, w.status, w.listingid, w.location, w.sold_id, w.channel, w.aucid, w.mfgpart, e.ebended, e.e_id FROM (warehouse w) LEFT JOIN ebay e ON `e`.`e_id` = `w`.listingid WHERE `w`.`deleted` = 0 AND `w`.`nr` = 0  AND `w`.`loc_id` = "'.(int)$loc_id.'" ORDER BY cont_id ASC';			
			$q =  $this->db->query($sql);
			if ($q->num_rows() > 0)
			{
				return $q->result_array();
			}		
	}
	function GetContainerBCNs($cont_id)
	{
		if (trim($cont_id) == '') return false;
			$sql = 'SELECT w.wid, w.bcn, w.cont_id, w.loc_id, w.title, w.status, w.listingid, w.location, w.sold_id, w.channel, w.aucid, w.mfgpart, e.ebended, e.e_id FROM (warehouse w) LEFT JOIN ebay e ON `e`.`e_id` = `w`.listingid WHERE `w`.`deleted` = 0 AND `w`.`nr` = 0  AND  `w`.`cont_id` = "'.(int)$cont_id.'"'; 
			$q =  $this->db->query($sql);
			if ($q->num_rows() > 0)
			{
				return $q->result_array();
			}				
	}
	function GetSessBCNs($sesarray)
	{
		$sql = 'SELECT wid, bcn, title,  aucid, mfgpart FROM warehouse WHERE `deleted` = 0 AND `nr` = 0  AND (';			
		
			$c = 0;
			foreach ($sesarray as $s)
			{
				if ($c == 0)  $sql .= '`wid` = "'.(int)$s.'"';
				else $sql .= ' OR `wid` = "'.(int)$s.'"';
				$c++;
			}
			$sql .= ')';
			$q =  $this->db->query($sql);
			if ($q->num_rows() > 0)
			{
				return $q->result_array();
			}
	}
}
?>