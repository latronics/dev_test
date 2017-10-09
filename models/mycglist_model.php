<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mycglist_model extends Model 
{
    function Mycglist_model()
    {
        parent::Model();
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
	
function ListItems($string)
	{	
		$this->db->select("c_id, c_title");		
		if ($string != '') $this->db->like('c_title', $string);	
		$this->db->order_by("c_id", "DESC");
		$this->query = $this->db->get('cglist');

		if ($this->query->num_rows() > 0) 
			{
			return $this->query->result_array();
			}	
	}
function GetItem($id)
	{
		$this->db->where('c_id', (int)$id);
		$this->query = $this->db->get('cglist');
		if ($this->query->num_rows() > 0) 
			{
			$this->result = $this->query->row_array();
			//$this->result['e_title'] = htmlspecialchars($this->result['e_title']);
			}
		return $this->result;
	}	
	
function Delete($id)
	{	
		$this->db->where('c_id', (int)$id);
		$this->db->delete('cglist'); 
	
	}
function Update($id, $data)
	{
		$this->db->update('cglist', $data, array('c_id' => (int)$id));
	}
function Insert($data)
	{
		$this->db->insert('cglist', $data);
		return $this->db->insert_id();
	}
}
?>