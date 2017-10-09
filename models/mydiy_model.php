<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Mydiy_model extends Model 
{
    function Mydiy_model()
    {
        parent::Model();
    }
function ListItems($cat = '', $sortby = '')
	{	
		$this->cat = (int)$cat;
		$this->db->select("d_id, d_cat, d_title, d_order, d_visibility, d_top");		
		if ($this->cat > 0) $this->db->where('d_cat', $this->cat);		
		$this->_SortBy($sortby);
		$this->query = $this->db->get('diy');
		if ($this->query->num_rows() > 0) 
			{
			return $this->query->result_array();
			}	
	}
function GetItem($id)
	{
		$this->db->where('d_id', (int)$id);
		$this->query = $this->db->get('diy');
		if ($this->query->num_rows() > 0) 
			{
			$this->result = $this->query->row_array();
			}
		return $this->result;
	}	
function GetMaxOrder($by)
	{
		$this->db->select_max('d_order');
		$this->db->where('d_cat', (int)$by);
		$this->query = $this->db->get('diy');
		if ($this->query->num_rows() > 0) 
			{
			$this->result = $this->query->row_array();
			}
		$this->result['d_order']++;
		return $this->result;
	}
function Delete($id)
	{	
		$this->db->where('d_id', (int)$id);
		$this->result = $this->db->delete('diy'); 
		return $this->result;
	}
function MakeVisible($id)
	{
		$this->db->update('diy', array('d_visibility' => 1), array('d_id' => (int)$id));
	}
function MakeNotVisible($id)
	{
		$this->db->update('diy', array('d_visibility' => 0), array('d_id' => (int)$id));
	}
function MakeTop($id)
	{
		$this->db->update('diy', array('d_top' => 1), array('d_id' => (int)$id));
	}
function MakeNotTop($id)
	{
		$this->db->update('diy', array('d_top' => 0), array('d_id' => (int)$id));
	}
function Update($id, $data)
	{
		$this->db->update('diy', $data, array('d_id' => (int)$id));		
	}
function Insert($data)
	{
		$this->db->insert('diy', $data);
	}
function ChangeOrder($id, $order)
	{			
		$this->db->update('diy', array('d_order' => (int)$order), array('d_id' => (int)$id));		
	}
function ReOrder($by, $sortby = '')
	{	
		$this->by = (int)$by;
	    
		if ($this->by > 0) 
		{	
		$this->db->select('d_id, d_order');
		$this->db->where('d_cat', $this->by);
		
		$this->_SortBy($sortby);
		
		$this->query = $this->db->get('diy');
		if ($this->query->num_rows() > 0) 
			{	
			$this->db_data = $this->query->result_array();
			$roll = 1;			
			foreach ($this->db_data as $udb) 
				{					
				$this->db->update('diy', array('d_order' => $roll), array('d_id' => (int)$udb['d_id']));
				$roll++;
				}
			}
		}
	}
function GetAllCategories() 
	{
	$this->db->order_by("d_cattitle", "ASC");
	$this->query = $this->db->get('diy_categories');
		if ($this->query->num_rows() > 0) 
			{
			return $this->query->result_array();
			}
	}
function GetCategory($id)
	{
		$this->db->where('d_catid', (int)$id);
		$this->query = $this->db->get('diy_categories');
		if ($this->query->num_rows() > 0) 
			{
			$this->result = $this->query->row_array();
			return $this->result;
			}
	}
function UpdateCategory($id, $data)
	{
		$this->db->update('diy_categories', $data, array('d_catid' => (int)$id));		
	}
function InsertCategory($data)
	{
		$this->db->insert('diy_categories', $data);
	}
function DeleteCategory($id)
	{	
		$this->db->where('d_catid', (int)$id);
		$this->result = $this->db->delete('diy_categories'); 
		return $this->result;
	}
function GetOldImage($id) 
	{	
		$this->db->select('d_img');
		$this->db->where('d_catid', (int)$id);
		$this->query = $this->db->get('diy_categories');
		if ($this->query->num_rows() > 0) 
			{
			$this->result = $this->query->row_array();
			return $this->result['d_img'];
			}
		
	}
	
// Helpful functions // 
function _SortBy($sortby = '') 
	{
	switch ($sortby) {
				case 'By_Id_Ascending':
				$this->db->order_by("d_id", "ASC");
			break;
				case 'By_Id_Descending':
				$this->db->order_by("d_id", "DESC");				
			break;
				case 'By_Title_Ascending':
				$this->db->order_by("d_title", "ASC");				
			break;
				case 'By_Title_Descending':
				$this->db->order_by("d_title", "DESC");				
			break;
				case 'By_Visible_Ascending':
				$this->db->order_by("d_visibility", "ASC");				
			break;
				case 'By_Visible_Descending':
				$this->db->order_by("d_visibility", "DESC");				
			break;
				case 'By_Top_Ascending':
				$this->db->order_by("d_top", "ASC");				
			break;
				case 'By_Top_Descending':
				$this->db->order_by("d_top", "DESC");				
			break;
				case 'By_Category_Ascending':
				$this->db->order_by("d_cat", "ASC");				
			break;
				case 'By_Category_Descending':
				$this->db->order_by("d_cat", "DESC");				
			break;
				case 'By_Order_Descending':
				$this->db->order_by("d_order", "DESC");	
			break;
				case 'By_Order_Ascending':
				default:
				$this->db->order_by("d_order", "ASC");	
			break;
			}
	}
}
?>