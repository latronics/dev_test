<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mypartners_model extends Model 
{
    function Mypartners_model()
    {
        parent::Model();
    }


function ListAll()
	{	
		$this->db->order_by("ordering", "ASC");
		$this->query = $this->db->get('partners');
		if ($this->query->num_rows() > 0) {
			return $this->query->result_array();
			}	
	}	
	
function GetMaxOrder()
	{
		$this->db->select_max('ordering');
		$this->query = $this->db->get('partners');
		if ($this->query->num_rows() > 0) 
			{
			$this->result = $this->query->row_array();
			}

		$this->result['ordering']++;
		return $this->result['ordering'];
	}
function Delete($id)
	{	
		$this->db->where('rid', (int)$id);
		$this->result = $this->db->delete('partners'); 
		return $this->result;
	}
function GetOldImage($id) 
	{	
		$this->db->select('logo');
		$this->db->where('rid', (int)$id);
		$this->query = $this->db->get('partners');
		if ($this->query->num_rows() > 0) 
			{
			$this->result = $this->query->row_array();
			return $this->result['logo'];
			}
		else
			{
			return false;
			}
		
	}
function MakeVisible($id)
	{
		$this->db->update('partners', array('visible' => 1), array('rid' => (int)$id));
	}
function MakeNotVisible($id)
	{
		$this->db->update('partners', array('visible' => 0), array('rid' => (int)$id));
	}
function Update($id, $data)
	{
		$this->db->update('partners', $data, array('rid' => (int)$id));
	}
function Insert($data)
	{
		$this->db->insert('partners', $data);
	}
function ChangeOrder($id, $order)
	{			
		$this->db->update('partners', array('ordering' => (int)$order), array('rid' => (int)$id));		
	}
	
function ReOrder()
	{	
		$this->db->select('rid, ordering');
		$this->db->order_by("ordering", "ASC");
		$this->query = $this->db->get('partners');
		if ($this->query->num_rows() > 0) 
			{	
			$this->db_data = $this->query->result_array();
			$roll = 1;			
			foreach ($this->db_data as $udb) 
				{					
				$this->db->update('partners', array('ordering' => $roll), array('rid' => (int)$udb['rid']));
				$roll++;
				}
			}
		}

}
?>