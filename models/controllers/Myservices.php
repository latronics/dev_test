<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Myservices extends Controller {
	 public function __construct()
       {
            parent::__construct();
		$this->_Start();
		$this->mysmarty->assign('area', 'Services');
	}	
function index()
	{	 
		$q = $this->db->query('SELECT * from services ORDER BY dateendmk DESC');		
		if ($q->num_rows() > 0) $this->mysmarty->assign('data', $q->result_array());		 
		else $this->mysmarty->assign('data', false);
		$this->mysmarty->assign('type', 'all');
		$this->mysmarty->view($this->go['sctr'].'/'.$this->go['sctr'].'_main.html');
	}
function Future()
	{
		$q = $this->db->query('SELECT * from services WHERE status = 2 ORDER BY dateendmk DESC');
		if ($q->num_rows() > 0) $this->mysmarty->assign('data', $q->result_array());
		else $this->mysmarty->assign('data', false);
		$this->mysmarty->assign('type', 'future');
		$this->mysmarty->view($this->go['sctr'].'/'.$this->go['sctr'].'_main.html');			
	}
function Active()
	{ 		
		$q = $this->db->query('SELECT * from services WHERE status = 0 ORDER BY dateendmk DESC');
		if ($q->num_rows() > 0) $this->mysmarty->assign('data', $q->result_array());
		else $this->mysmarty->assign('data', false);
		$this->mysmarty->assign('type', 'active');
		$this->mysmarty->view($this->go['sctr'].'/'.$this->go['sctr'].'_main.html');
		
	}
function Past()
	{	
		$q = $this->db->query('SELECT * from services WHERE status = 1 ORDER BY dateendmk DESC');
		if ($q->num_rows() > 0) $this->mysmarty->assign('data', $q->result_array());
		else $this->mysmarty->assign('data', false);
		$this->mysmarty->assign('type', 'past');
		$this->mysmarty->view($this->go['sctr'].'/'.$this->go['sctr'].'_main.html');	
	}
function edit($id = 0)
	{
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('title', 'Description', 'trim|required|xss_clean');
		$this->form_validation->set_rules('provider', 'Provider', 'trim|required|xss_clean');
		$this->form_validation->set_rules('datebegin', 'Initial Date', 'trim|required|xss_clean');
		$this->form_validation->set_rules('dateend', 'Expires', 'trim|required|xss_clean');
		$this->form_validation->set_rules('status', 'Status', 'trim|required|xss_clean');	

		if ($this->form_validation->run() == FALSE)
			{	
			
			$data['sid'] = (int)$id;
			
			if ((int)$id > 0)
				{	 
					$this->db->where('sid', (int)$id);					 
					$query = $this->db->get('services');
					if ($query->num_rows() > 0) $data = $query->row_array();
					else Redirect($this->go['ctr']);
				}						
				if ($_POST) {
								$data = array(
									'title' => $this->input->post('title', true),
									'provider' => $this->input->post('provider', true),	
									'datebegin' => $this->input->post('datebegin', true),	
									'dateend' => $this->input->post('dateend', true),
									'status' => (int)$this->input->post('status', true)
									);								
							}
				$this->mysmarty->assign('data', $data);			
				$this->mysmarty->assign('cal', TRUE);
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);
				$this->mysmarty->view($this->go['sctr'].'/'.$this->go['sctr'].'_edit.html');
				exit();
			}
			else
			{			
				$datebeginmk = explode ('-', $this->form_validation->set_value('datebegin'));
				$dateendmk = explode ('-',$this->form_validation->set_value('dateend'));			

				$this->db_data = array(										    
									'title' => $this->form_validation->set_value('title'),
									'provider' => $this->form_validation->set_value('provider'),
									'datebegin' => $this->form_validation->set_value('datebegin'),
									'datebeginmk' => mktime(0,0,0,$datebeginmk[1],$datebeginmk[0],$datebeginmk[2]),
									'dateend' => $this->form_validation->set_value('dateend'),
									'dateendmk' => mktime(0,0,0,$dateendmk[1],$dateendmk[0],$dateendmk[2]),
									'status' => $this->form_validation->set_value('status') 
							);				
				if ((int)$id == 0) $this->db->insert('services', $this->db_data);					
				else $this->db->update('services', $this->db_data, array('sid' => (int)$id));					
				 
			}
			Redirect($this->go['ctr']);	
	}
function delete($id = 0)
	{
		if ((int)$id > 0) 
		{  
			$this->db->where('sid', (int)$id);		
			$this->db->delete('services');
			
		}
		Redirect('Myservices');		
	}	
function _Start() 
{
		$this->load->model('Auth_model');
		$this->Auth_model->VerifyAdmin();
		$this->mysmarty->assign('session',$this->session->userdata);
		$this->mysmarty->assign('error_msg',$this->session->flashdata('error_msg'));
		$this->mysmarty->assign('success_msg',$this->session->flashdata('success_msg'));
		$this->go = DoGo($this->router->class, $this->router->method);	
		$this->mysmarty->assign('go', $this->go);	
}
}
