<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mynews_model extends Model 
{
    function Mynews_model()
    {
        parent::Model();
    }


function ListItems($sortby = '')
	{	
		$this->db->select("n_id, n_title, n_date, n_visibility, n_top");		
		$this->_SortBy($sortby);
		$this->query = $this->db->get('news');

		if ($this->query->num_rows() > 0) 
			{
			return $this->query->result_array();
			}	
	}
function GetItem($id)
	{
		$this->db->where('n_id', (int)$id);
		$this->query = $this->db->get('news');
		if ($this->query->num_rows() > 0) 
			{
			$this->result = $this->query->row_array();
			}
		return $this->result;
	}	

function Delete($id)
	{	
		$this->db->where('n_id', (int)$id);
		$this->result = $this->db->delete('news'); 
		return $this->result;
	}
function MakeVisible($id)
	{
		$this->db->update('news', array('n_visibility' => 1), array('n_id' => (int)$id));
	}
function MakeNotVisible($id)
	{
		$this->db->update('news', array('n_visibility' => 0), array('n_id' => (int)$id));
	}
function MakeTop($id)
	{
		$this->db->update('news', array('n_top' => 1), array('n_id' => (int)$id));
		$this->db->update('news', array('n_top' => 0), array('n_id !=' => (int)$id));
	}
function MakeNotTop($id)
	{
		$this->db->update('news', array('n_top' => 0), array('n_id' => (int)$id));
	}
function Update($id, $data)
	{
		$this->db->update('news', $data, array('n_id' => (int)$id));		
	}
function Insert($data)
	{
		$this->db->insert('news', $data);
	}
function GetOldItemImage($id) 
	{	
		$this->db->select('n_img');
		$this->db->where('n_id', (int)$id);
		$this->query = $this->db->get('news');
		if ($this->query->num_rows() > 0) 
			{
			$this->result = $this->query->row_array();
			return $this->result['n_img'];
			}
	}
function DeleteItemImage($id) 
	{	
		$this->db->select('n_img');
		$this->db->where('n_id', (int)$id);
		$this->query = $this->db->get('news');
		if ($this->query->num_rows() > 0) 
			{
			$this->result = $this->query->row_array();			
			$this->data = array ('n_img' => '');	
			$this->db->update('news', $this->data, array('n_id' => (int)$id));			
			return $this->result['n_img'];
		}
	}
	
// Helpful functions // 
function _SortBy($sortby = '') 
	{
	switch ($sortby) {
				case 'By_Id_Ascending':
				$this->db->order_by("n_id", "ASC");
			break;
				case 'By_Id_Descending':
				$this->db->order_by("n_id", "DESC");				
			break;
				case 'By_Title_Ascending':
				$this->db->order_by("n_title", "ASC");				
			break;
				case 'By_Title_Descending':
				$this->db->order_by("n_title", "DESC");				
			break;
				case 'By_Visible_Ascending':
				$this->db->order_by("n_visibility", "ASC");				
			break;
				case 'By_Visible_Descending':
				$this->db->order_by("n_visibility", "DESC");				
			break;
				case 'By_Top_Ascending':
				$this->db->order_by("n_top", "ASC");				
			break;
				case 'By_Top_Descending':
				$this->db->order_by("n_top", "DESC");				
			break;
				case 'By_Date_Ascending':
				$this->db->order_by("n_date", "ASC");	
			break;
				case 'By_Date_Descending':
				default:
				$this->db->order_by("n_date", "DESC");	
			break;
			}
	}



}
?>