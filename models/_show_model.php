<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Show_model extends Model 
{
    function Show_model()
    {
        parent::Model();
    }


function GetMainMenu ()
	{
		$this->db->where('s_visible', '1');
		$this->db->where('s_level', '0');
		$this->db->order_by("s_order", "ASC");
		$this->query = $this->db->get('structure');

		if ($this->query->num_rows() > 0) 
			{
			$this->mainlevel = $this->query->result_array();
			return $this->mainlevel;
			}
	}
	
function GetParentId($s_title) 
	{
		$this->db->select('s_id, s_type');
		$this->db->where('s_seourl', $s_title);
		$this->db->where('s_visible', '1');
		$this->subquery = $this->db->get('structure', 1);

		if ($this->subquery->num_rows() > 0) 
			{
			$this->sublevelkey = $this->subquery->row_array();
			if ($this->sublevelkey['s_type'] == 'm') return $this->sublevelkey['s_id'];
			}
	}
	
function GetSubMenu ($level = '', $s_title = '', $s_levelparentid = '')
	{	
		if ((int)$s_levelparentid == 0) $this->id = (int)$this->GetParentId($s_title);
		else $this->id = (int)$s_levelparentid;
		
		$this->db->where('s_levelparentid', $this->id);
		$this->db->where('s_level', strval((int)$level));
		$this->db->where('s_visible', '1');
		$this->db->order_by("s_order", "ASC");
		$this->subquerydata = $this->db->get('structure');
			if ($this->subquerydata->num_rows() > 0) 
				{
				$this->sublevel = $this->subquerydata->result_array();
				return $this->sublevel;
				}
	}
}
?>
