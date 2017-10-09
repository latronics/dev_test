<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Myservices_model extends Model 
{
    function Myservices_model()
    {
        parent::Model();
    }


function ListItems()
	{	
		$this->db->select("sid, title, visible, top, ordering");	
		$this->db->order_by("ordering", "ASC");
		$this->query = $this->db->get('solutions');

		if ($this->query->num_rows() > 0) 
			{
			return $this->query->result_array();
			}	
	}
function CountProducts() 
	{
	
				$this->db->select("spid, sid");	
				$this->query = $this->db->get('solution_products');
				if ($this->query->num_rows() > 0) 
					{
						foreach ($this->query->result_array() as $key =>$value) 
						{
						if (isset($this->nums[$value['sid']])) $this->nums[$value['sid']]++;
						else $this->nums[$value['sid']] = 1;
						}
					
					return $this->nums;
					}
}
function ListProducts($sid) {
	
	$this->db->select("s.spid, p.p_id, p.p_title");	
	$this->db->order_by("p.p_order", "ASC");
	$this->db->where('s.p_id = p.p_id');
	$this->db->where('s.sid', (int)$sid);
		$this->query = $this->db->get('solution_products AS s, products AS p');

		if ($this->query->num_rows() > 0) 
			{
			foreach ($this->query->result_array() as $key => $value) {
				$this->dataset[$value['p_id']] = $value;				
				}
			return $this->dataset;				
			}	
	}	
	
function ListAllProducts() {	
	$this->db->select("p_id, p_title");	
	$this->db->order_by("p_title", "ASC");
	$this->query = $this->db->get('products');

		if ($this->query->num_rows() > 0) 
			{
			return $this->query->result_array();
			}	
	}
	
function AddProducts($pid, $sid)
	{
	$data['p_id'] = (int)$pid;
	$data['sid'] = (int)$sid;
	$this->db->insert('solution_products', $data);		
	}

function DeleteProducts($spid)
	{
		$this->db->where('spid', (int)$spid);
		$this->result = $this->db->delete('solution_products'); 
	}
function GetItem($id)
	{
		$this->db->where('sid', (int)$id);
		$this->query = $this->db->get('solutions');
		if ($this->query->num_rows() > 0) 
			{
			$this->result = $this->query->row_array();
			}
		return $this->result;
	}	
	
function GetMaxOrder()
	{
		$this->db->select_max('ordering');
		$this->query = $this->db->get('solutions');
		if ($this->query->num_rows() > 0) 
			{
			$this->result = $this->query->row_array();
			}
		else 
			{
			$this->result['ordering'] = 0;
			}
		$this->result['ordering']++;
		return $this->result;
	}
function Delete($id)
	{	
		$this->db->where('sid', (int)$id);
		$this->result = $this->db->delete('solutions'); 
		return $this->result;
	}
function MakeVisible($id)
	{
		$this->db->update('solutions', array('visible' => 1), array('sid' => (int)$id));
	}
function MakeNotVisible($id)
	{
		$this->db->update('solutions', array('visibile' => 0), array('sid' => (int)$id));
	}
function MakeTop($id)
	{
		$this->db->update('solutions', array('top' => 1), array('sid' => (int)$id));
	}
function UnTop($id)
	{
		$this->db->update('solutions', array('top' => 0), array('sid' => (int)$id));
	}
function Update($id, $data)
	{
		$this->db->update('solutions', $data, array('sid' => (int)$id));
	}
function Insert($data)
	{
		$this->db->insert('solutions', $data);
	}
function ChangeOrder($id, $order)
	{			
		$this->db->update('solutions', array('ordering' => (int)$order), array('sid' => (int)$id));		
	}

function GetOldProductImage($id) 
	{	
		$this->db->select('image');
		$this->db->where('sid', (int)$id);
		$this->query = $this->db->get('solutions');
		if ($this->query->num_rows() > 0) 
			{
			$this->result = $this->query->row_array();
			return $this->result['image'];
			}
	}
function DeleteImage($id) 
	{	
		$this->db->select('image');
		$this->db->where('sid', (int)$id);
		$this->query = $this->db->get('solutions');
		if ($this->query->num_rows() > 0) 
			{
			$this->result = $this->query->row_array();			
			$this->data = array ('image' => '');	
			$this->db->update('solutions', $this->data, array('sid' => (int)$id));			
			return $this->result['image'];
		}
	}	
function ReOrder()
	{	

		$this->db->select('sid, ordering');
		$this->db->order_by("ordering", "ASC");
		$this->query = $this->db->get('solutions');
		if ($this->query->num_rows() > 0) 
			{	
			$this->db_data = $this->query->result_array();
			$roll = 1;			
			foreach ($this->db_data as $udb) 
				{					
				$this->db->update('solutions', array('ordering' => $roll), array('sid' => (int)$udb['sid']));
				$roll++;
				}
			}
	}

}
?>