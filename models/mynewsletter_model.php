<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mynewsletter_model extends Model 
{
    function Mynewsletter_model()
    {
        parent::Model();
    }


function DeleteSubscriber($id = '')
	{
		$this->db->where('n_id', (int)$id);
		$this->db->delete('newsletter_subscribers');	
	}
function DeleteNewsletter($id = '')
	{
		$this->db->where('na_id', (int)$id);
		$this->db->delete('newsletter_archive');	
	}
	
function ActivateSubscriber($id = '')
	{
		$this->db->update('newsletter_subscribers', array('n_confirmed' => 1), array('n_id' => (int)$id));
	}
function DeactivateSubscriber($id = '')
	{
		$this->db->update('newsletter_subscribers', array('n_confirmed' => 0), array('n_id' => (int)$id));
	}
function AddSubscriber($email, $lang)
	{	
		$this->db->insert('newsletter_subscribers', array('n_email' => $email, 'n_code' => mt_rand(100000000000000000, 999999999999999999), 'n_confirmed' => 1, 'n_regdate' => CurrentTime()));
	}

function GetAllSubscribers($page = '') 
	{	
			if ((int)$page > 0) $page = $page - 1;
			$this->db->limit(15, (int)$page*15);
			$this->query = $this->db->get('newsletter_subscribers');
		
			$this->countall = $this->db->count_all_results('newsletter_subscribers');
			$this->pages = ceil($this->countall/15);
			for ( $counter = 1; $counter <= $this->pages ; $counter++) 
			{
			$this->pagearray[] = $counter;
			}
			

		if ($this->query->num_rows() > 0) 
			{
			return array('results' => $this->query->result_array(), 'pages' => $this->pagearray);
			}
	}
function GetActiveSubscriberEmails($type = '') 
	{	
		if ((int)$type == 1) {
		$this->db->select('email AS n_email');
		$this->db->where('active', 1);
		$this->query = $this->db->get('users');	
		}
		else
		{
		$this->db->select('n_email, n_code');
		$this->db->where('n_confirmed', 1);
		$this->query = $this->db->get('newsletter_subscribers');
		}
		if ($this->query->num_rows() > 0) 
			{
			return $this->query->result_array();
			}
	}
function FindNewsletterEmail($email = '') {
		$this->db->select('n_id');
		$this->db->where('n_email', $email);
		$this->query = $this->db->get('newsletter_subscribers', 1);
		
		if ($this->query->num_rows() > 0) 
			{
				$this->nresult =  $this->query->row_array();
				return $this->nresult['n_id'];
			}
			else
			{
				return false;
			}
	}

function SaveNewsletter($data) {

		$this->db->insert('newsletter_archive', array('na_title' => $data['title'], 'na_body' => $data['body'], 'na_created' => CurrentTime()));

	}
	
function UpdateNewsletter($data, $id) {
	
		$this->db->update('newsletter_archive', array('na_title' => $data['title'], 'na_body' => $data['body'], 'na_created' => CurrentTime()), array('na_id' => (int)$id));

	}	
function GetArchive($page = '') 
	{	
			if ((int)$page > 0) $page = $page - 1;
			$this->db->limit(15, (int)$page*15);
			$this->query = $this->db->get('newsletter_archive');
		
			$this->countall = $this->db->count_all_results('newsletter_archive');
			$this->pages = ceil($this->countall/15);
			for ( $counter = 1; $counter <= $this->pages ; $counter++) 
			{
			$this->pagearray[] = $counter;
			}
			
			
		

		if ($this->query->num_rows() > 0) 
			{
			return array('results' => $this->query->result_array(), 'pages' => $this->pagearray);
			}
	}
function GetNewsletter($id)
	{
		$this->db->where('na_id', (int)$id);
		$this->query = $this->db->get('newsletter_archive', 1);
		
		if ($this->query->num_rows() > 0) 
			{
				return $this->query->row_array();
			}
	}
}
?>