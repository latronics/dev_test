<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Myforms_model extends Model 
{
    function Myforms_model()
    {
        parent::Model();
    }

function ListItems($page = '')
	{
		$this->db->select("fc_id, user_id, names, email, date, code");
		$this->db->order_by("date", "DESC");
		if ((int)$page > 0) $page = $page - 1;
		$this->db->limit(25, (int)$page*25);
		$this->query = $this->db->get('form_contact');
		
			$this->countall = $this->db->count_all_results('form_contact');
			$this->pages = ceil($this->countall/25);
			for ( $counter = 1; $counter <= $this->pages ; $counter++) 
			{
			$this->pagearray[] = $counter;
			}
			
		

		if ($this->query->num_rows() > 0) 
			{
			return array('results' => $this->query->result_array(), 'pages' => $this->pagearray);
			}	
	}

function ListItemsNP()
	{
		$this->db->select("fc_id, user_id, names, email, date, code");
		$this->db->order_by("date", "DESC");
		$this->query = $this->db->get('form_contact');
		
		if ($this->query->num_rows() > 0) 
			{
			return $this->query->result_array();
			}	
	}

function GetForm($id = '')
	{

		$this->db->where('fc_id', (int)$id);
		$this->query = $this->db->get('form_contact');

		if ($this->query->num_rows() > 0) 
			{
				return $this->query->row_array();
			}	
	}
function GetFrontForm($code = '', $id = '')
	{

		$this->db->where('fc_id', (int)$id);
		$this->db->where('code', $code);
		$this->query = $this->db->get('form_contact');

		if ($this->query->num_rows() > 0) 
			{
				return $this->query->row_array();
			}	
	}
function GetFormReplies($id)
	{
		$this->db->where('f_id', (int)$id);
		$this->db->order_by('fc_id', 'DESC');		
		$cquery = $this->db->get('form_contact_comm');
		if ($cquery->num_rows() > 0) return $cquery->result_array();
	}

function Delete($id = '')
	{
		$this->db->where('fc_id', (int)$id);
		$this->db->delete('form_contact');
	}
}
?>
