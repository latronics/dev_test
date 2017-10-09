<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Myfaq_model extends Model 
{
    function Myfaq_model()
    {
        parent::Model();
    }


function ListItems($cat = '', $sortby = '')
	{	
		$this->cat = (int)$cat;
		$this->db->select("f_id, f_cat, f_title, f_order, f_visibility, f_top");		
		if ($this->cat > 0) $this->db->where('f_cat', $this->cat);		
		$this->_SortBy($sortby);
		$this->query = $this->db->get('faq');

		if ($this->query->num_rows() > 0) 
			{
			return $this->query->result_array();
			}	
	}
function GetItem($id)
	{
		$this->db->where('f_id', (int)$id);
		$this->query = $this->db->get('faq');
		if ($this->query->num_rows() > 0) 
			{
			$this->result = $this->query->row_array();
			}
		return $this->result;
	}	
function GetMaxOrder($by)
	{
		$this->db->select_max('f_order');
		$this->db->where('f_cat', (int)$by);
		$this->query = $this->db->get('faq');
		if ($this->query->num_rows() > 0) 
			{
			$this->result = $this->query->row_array();
			}

		$this->result['f_order']++;
		return $this->result;
	}
function Delete($id)
	{	
		$this->db->where('f_id', (int)$id);
		$this->result = $this->db->delete('faq'); 
		return $this->result;
	}
function MakeVisible($id)
	{
		$this->db->update('faq', array('f_visibility' => 1), array('f_id' => (int)$id));
	}
function MakeNotVisible($id)
	{
		$this->db->update('faq', array('f_visibility' => 0), array('f_id' => (int)$id));
	}
function MakeTop($id)
	{
		$this->db->update('faq', array('f_top' => 1), array('f_id' => (int)$id));
	}
function MakeNotTop($id)
	{
		$this->db->update('faq', array('f_top' => 0), array('f_id' => (int)$id));
	}
function Update($id, $data)
	{
		$this->db->update('faq', $data, array('f_id' => (int)$id));		
	}
function Insert($data)
	{
		$this->db->insert('faq', $data);
	}
function ChangeOrder($id, $order)
	{			
		$this->db->update('faq', array('f_order' => (int)$order), array('f_id' => (int)$id));		
	}
function ReOrder($by, $sortby = '')
	{	
		$this->by = (int)$by;
	    
		if ($this->by > 0) 
		{	
		$this->db->select('f_id, f_order');
		$this->db->where('f_cat', $this->by);
		
		$this->_SortBy($sortby);
		
		$this->query = $this->db->get('faq');
		if ($this->query->num_rows() > 0) 
			{	
			$this->db_data = $this->query->result_array();
			$roll = 1;			
			foreach ($this->db_data as $udb) 
				{					
				$this->db->update('faq', array('f_order' => $roll), array('f_id' => (int)$udb['f_id']));
				$roll++;
				}
			}
		}
	}

function GetAllCategories() 
	{
	$this->db->order_by("f_cattitle", "ASC");
	$this->query = $this->db->get('faq_categories');
		if ($this->query->num_rows() > 0) 
			{
			return $this->query->result_array();
			}
	}
function GetCategory($id)
	{
		$this->db->where('f_catid', (int)$id);
		$this->query = $this->db->get('faq_categories');
		if ($this->query->num_rows() > 0) 
			{
			$this->result = $this->query->row_array();
			return $this->result;
			}
	}
function UpdateCategory($id, $data)
	{
		$this->db->update('faq_categories', $data, array('f_catid' => (int)$id));		
	}
function InsertCategory($data)
	{
		$this->db->insert('faq_categories', $data);
	}
function DeleteCategory($id)
	{	
		$this->db->where('f_catid', (int)$id);
		$this->result = $this->db->delete('faq_categories'); 
		return $this->result;
	}
function GetOldImage($id) 
	{	
		$this->db->select('f_img');
		$this->db->where('f_catid', (int)$id);
		$this->query = $this->db->get('faq_categories');
		if ($this->query->num_rows() > 0) 
			{
			$this->result = $this->query->row_array();
			return $this->result['f_img'];
			}
		
	}

	
// Helpful functions // 
function _SortBy($sortby = '') 
	{
	switch ($sortby) {
				case 'By_Id_Ascending':
				$this->db->order_by("f_id", "ASC");
			break;
				case 'By_Id_Descending':
				$this->db->order_by("f_id", "DESC");				
			break;
				case 'By_Title_Ascending':
				$this->db->order_by("f_title", "ASC");				
			break;
				case 'By_Title_Descending':
				$this->db->order_by("f_title", "DESC");				
			break;
				case 'By_Visible_Ascending':
				$this->db->order_by("f_visibility", "ASC");				
			break;
				case 'By_Visible_Descending':
				$this->db->order_by("f_visibility", "DESC");				
			break;
				case 'By_Top_Ascending':
				$this->db->order_by("f_top", "ASC");				
			break;
				case 'By_Top_Descending':
				$this->db->order_by("f_top", "DESC");				
			break;
				case 'By_Category_Ascending':
				$this->db->order_by("f_cat", "ASC");				
			break;
				case 'By_Category_Descending':
				$this->db->order_by("f_cat", "DESC");				
			break;
				case 'By_Order_Descending':
				$this->db->order_by("f_order", "DESC");	
			break;
				case 'By_Order_Ascending':
				default:
				$this->db->order_by("f_order", "ASC");	
			break;
			}
	}



}
?>