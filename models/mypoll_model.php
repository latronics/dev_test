<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mypoll_model extends Model 
{
    function Mypoll_model()
    {
        parent::Model();
    }


function ListItems()
	{	
		$this->db->select("p_id, title, active, date");	
		$this->db->order_by("date", "DESC");				
		$this->query = $this->db->get('poll');

		if ($this->query->num_rows() > 0) 
			{
			return $this->query->result_array();
			}	
	}
function GetResults($id = '')
	{	
		$this->db->select("answer");
		$this->db->where('p_id', (int)$id);
		$this->db->order_by("answer", "ASC");
		$this->query = $this->db->get('poll_results');

		if ($this->query->num_rows() > 0) 
			{
			$this->answers = $this->query->result_array();


			foreach ($this->answers as $value)
				{
					if (isset($this->answersum[$value['answer']])) $this->answersum[$value['answer']]++;					
					else ($this->answersum[$value['answer']] = 1);					
				}			
			return $this->answersum;
			}	
	}	
function GetItem($id)
	{
		$this->db->where('p_id', (int)$id);
		$this->query = $this->db->get('poll');
		if ($this->query->num_rows() > 0) 
			{
			$this->result = $this->query->row_array();
			}
		return $this->result;
	}	
function Delete($id)
	{	
		$this->db->where('p_id', (int)$id);
		$this->db->delete('poll'); 
		$this->DeleteResults((int)$id);
			
	}
function DeleteResults($id ='') 
	{
	$this->db->where('p_id', (int)$id);
	$this->db->delete('poll_results'); 	
	}
	
function MakeVisible($id)
	{
		$this->db->update('poll', array('active' => 1), array('p_id' => (int)$id));
		$this->db->update('poll', array('active' => 0), array('p_id !=' => (int)$id));		
		
	}
function MakeNotVisible($id)
	{
		$this->db->update('poll', array('active' => 0), array('p_id' => (int)$id));
	}
function Update($id, $data)
	{
		$this->db->update('poll', $data, array('p_id' => (int)$id));		
	}
function Insert($data)
	{
		$this->db->insert('poll', $data);
	}
	


}
?>