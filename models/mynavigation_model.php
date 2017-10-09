<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mynavigation_model extends Model 
{
    function Mynavigation_model()
    {
        parent::Model();
    }


function ListItems($level1 = '', $level2 = '', $sortby = '')
	{	
		$this->l1 = (int)$level1;
		$this->l2 = (int)$level2;
		//$this->db->select("p_id, p_cat, p_title, p_order, p_visibility");		
		$this->_Levelize($this->l1, $this->l2);	
		$this->_SortBy($sortby);
		$this->db->order_by("s_order", "ASC");
		$this->db->where('s_menu', (int)$this->session->userdata('menuid'));
		$this->query = $this->db->get('navigation');

		if ($this->query->num_rows() > 0) 
			{
			return $this->query->result_array();
			}	
	}
function GetTitle($id)
	{
		$this->db->select('s_title');
		$this->db->where('s_id', (int)$id);
		$this->query = $this->db->get('navigation');
		if ($this->query->num_rows() > 0) 
			{
			$this->result = $this->query->row_array();
			}
		return $this->result['s_title'];
		
	}
function GetItem($id)
	{
		$this->db->where('s_id', (int)$id);
		$this->query = $this->db->get('navigation');
		if ($this->query->num_rows() > 0) 
			{
			$this->result = $this->query->row_array();
			return $this->result;
			}
		
		
	}	
function GetMaxOrder($l1,$l2)
	{
		$this->db->select_max('s_order');
		$this->_Levelize($l1,$l2);
		$this->db->where('s_menu', (int)$this->session->userdata('menuid'));
		$this->query = $this->db->get('navigation');
		if ($this->query->num_rows() > 0) 
			{
			$this->result = $this->query->row_array();
			
			$this->result['s_order']++;
			return $this->result;
		}
	}
function CheckSurlExists($str)
	{
		$this->db->select('s_seourl');
		$this->db->where('s_seourl', $str);
		$this->query = $this->db->get('navigation');
		if ($this->query->num_rows() > 0) 
			{
			return TRUE;
			}
		else
			{
			return FALSE;	
			}	
	}
function CheckSurlUpdateExists($str, $id = '')
	{
		$this->db->select('s_id');
		$this->db->where('s_seourl', $str);
		$this->query = $this->db->get('navigation');
		$found = 0;
		if ($this->query->num_rows() > 0) 
			{
			$this->result = $this->query->result_array();
			foreach ($this->result as $r) 
				{
				if ($r['s_id'] != (int)$id) $found++;
				}			
			}
		if ($found > 0) return TRUE;
		else return FALSE;
	}

function Delete($id)
	{	
		$this->db->where('s_id', (int)$id);
		$this->result = $this->db->delete('navigation'); 
		return $this->result;
	}
function MakeVisible($id)
	{
		$this->db->update('navigation', array('s_visible' => 1), array('s_id' => (int)$id));
	}
function MakeNotVisible($id)
	{
		$this->db->update('navigation', array('s_visible' => 0), array('s_id' => (int)$id));
	}
function Update($id, $data)
	{
		$this->db->update('navigation', $data, array('s_id' => (int)$id));		
	}
function Insert($data)
	{
		$this->db->insert('navigation', $data);
	}
function ChangeOrder($id, $order)
	{			
		$this->db->update('navigation', array('s_order' => (int)$order), array('s_id' => (int)$id));		
	}
function ReOrder($l1, $l2, $sortby = '')
	{	
		$this->_Levelize($l1,$l2);
	    
		$this->db->select('s_id, s_order');
		$this->_SortBy($sortby);
		$this->db->where('s_menu', (int)$this->session->userdata('menuid'));
		
		$this->query = $this->db->get('navigation');
		if ($this->query->num_rows() > 0) 
			{	
			$this->db_data = $this->query->result_array();
			$roll = 1;			
			foreach ($this->db_data as $udb) 
				{					
				$this->db->update('navigation', array('s_order' => $roll), array('s_id' => (int)$udb['s_id']));
				$roll++;
				}
			}
	}
	
// Helpful functions // 
function _SortBy($sortby = '') 
	{
	switch ($sortby) {
				case 'By_Id_Ascending':
				$this->db->order_by("s_id", "ASC");
			break;
				case 'By_Id_Descending':
				$this->db->order_by("s_id", "DESC");				
			break;
				case 'By_Title_Ascending':
				$this->db->order_by("s_title", "ASC");				
			break;
				case 'By_Title_Descending':
				$this->db->order_by("s_title", "DESC");				
			break;
				case 'By_Type_Ascending':
				$this->db->order_by("s_type", "ASC");				
			break;
				case 'By_Type_Descending':
				$this->db->order_by("s_type", "DESC");				
			break;
				case 'By_Visible_Ascending':
				$this->db->order_by("s_visible", "ASC");				
			break;
				case 'By_Visible_Descending':
				$this->db->order_by("s_visible", "DESC");				
			break;
				case 'By_Order_Descending':
				$this->db->order_by("s_order", "DESC");	
			break;
				case 'By_Order_Ascending':
				default:
				$this->db->order_by("s_order", "ASC");	
			break;
			}
	}

function _Levelize($l1 = '', $l2 = '') 
	{
		$this->l1 = (int)$l1;
		$this->l2 = (int)$l2;
		if (($this->l1 == 0) && ($this->l2 > 0))
			{
			Redirect("/Mynavigation/Show");
			break; exit();
			}
				
	if ($this->l2 > 0) 
		{
		$this->db->where('s_level', '2');
		$this->db->where('s_levelparentid', $this->l2);
		}
		elseif ($this->l1 > 0)
		{
		$this->db->where('s_level', '1');
		$this->db->where('s_levelparentid', $this->l1);	
		}
		else 
		{
		$this->db->where('s_level', '0');
		$this->db->where('s_levelparentid', '0');	
		}		
	}

}
?>