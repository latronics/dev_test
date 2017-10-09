<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Settings_model extends Model 
{
    function Settings_model()
    {
        parent::Model();				
    }
	
	function GetEbaySettings()
	{
		$this->db->select("skey, svalue");
		$this->db->where('sid >=', 13);
		$this->db->where('sid <=', 24);
		$q = $this->db->get('settings');
		$eBaySettings = array();
		
		if ($q->num_rows() > 0) foreach ($q->result_array() as $v) 
		{ 
			if ($v['skey'] == 'eBayAddr') $this->mysmarty->assign($v['skey'], $v['svalue']);	
			else $eBaySettings[$v['skey']] = $v['svalue'];
		}
	
		return $eBaySettings;
	}
	function GetEbayListingAddress()
	{
		$this->db->select("svalue");
		$this->db->where('skey', 'eBayAddr');
		$q = $this->db->get('settings');
		$eBayAddr['svalue'] = '';
		if ($q->num_rows() > 0) $eBayAddr = $q->row_array();
		$this->mysmarty->assign('eBayAddr', $eBayAddr['svalue']);		
	}
	function GetEbayListingLinesAddress()
	{
		$this->db->select("skey, svalue");
		$this->db->where('sid >=', 25);
		$this->db->where('sid <=', 28);
		$q = $this->db->get('settings');
		if ($q->num_rows() > 0) foreach ($q->result_array() as $v) $this->mysmarty->assign($v['skey'], $v['svalue']);	
	}
}
?>