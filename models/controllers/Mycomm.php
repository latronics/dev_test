<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Mycomm extends Controller {
function Mycomm()
	{
		parent::Controller();
		$this->_Start();
		$this->mysmarty->assign('area', 'COMM');
		if ($this->session->userdata['admin_id'] != '1' && $this->session->userdata['admin_id'] != 2 && $this->session->userdata['admin_id'] != 3 && $this->session->userdata['admin_id'] != 6 && $this->session->userdata['admin_id'] != 8 && $this->session->userdata['admin_id'] != 9 && $this->session->userdata['admin_id'] != 10) { echo 'Sorry, you don\'t have clearance for here.';exit();}
		
		$this->load->model('Myproducts_model');
		$this->mysmarty->assign('zero', $this->Myproducts_model->CheckWeightZero());
		
	}
	
function index()
	{	
		$this->db->select("c_id, c_datetime, c_title, c_from, c_for, c_deleted, c_lastreply_from");	
		$this->db->where('c_parent', '0');
		$this->db->where('c_deleted', '0');
		$this->db->order_by("c_id", "DESC");
		$this->query = $this->db->get('comm');
		
		if ($this->query->num_rows() > 0) $data = $this->query->result_array();
		else $data = FALSE;
		
		$this->mysmarty->assign('comm', $data);
		
		$this->db->select("ownnames");	
		$this->db->where('active', '1');
		$this->db->where('type = "master"');
		$this->db->order_by("admin_id", "ASC");
		$this->query = $this->db->get('administrators');
		
		if ($this->query->num_rows() > 0) $admins = $this->query->result_array();
		else $admins = FALSE;
		
		$this->mysmarty->assign('admins', $admins);		
		
		$this->mysmarty->view($this->go['sctr'].'/'.$this->go['sctr'].'_main.html');
	}
function Completed()
	{	
		$this->db->select("c_id, c_datetime, c_title, c_from, c_for, c_deleted, c_lastreply_from");	
		$this->db->where('c_parent', '0');		
		$this->db->order_by("c_deleted", "ASC");
		$this->db->order_by("c_id", "DESC");
		$this->query = $this->db->get('comm');
		
		if ($this->query->num_rows() > 0) $data = $this->query->result_array();
		else $data = FALSE;
		
		$this->mysmarty->assign('comm', $data);
		
		$this->db->select("ownnames");	
		$this->db->where('active', '1');
		$this->db->where('type = "master"');
		$this->db->order_by("admin_id", "ASC");
		$this->query = $this->db->get('administrators');
		
		if ($this->query->num_rows() > 0) $admins = $this->query->result_array();
		else $admins = FALSE;
		
		$this->mysmarty->assign('admins', $admins);		
		
		$this->mysmarty->view($this->go['sctr'].'/'.$this->go['sctr'].'_main.html');
	}

function NewTask()
	{

		if (isset($_POST['c_title'])) $this->titlelen = strlen($_POST['c_title']);
		else $this->titlelen = 0;
		if ($this->titlelen > 0)
		{
				$this->db_data['c_datetime'] = CurrentTime();
				$this->db_data['c_from'] = $this->session->userdata['ownnames'];
				$this->db_data['c_for'] = $this->input->post('c_for', TRUE);				
				$this->db_data['c_title'] = $this->input->post('c_title', TRUE);
				
				$this->db->insert('comm', $this->db_data);
				
				$this->db->select('email');
				$this->db->where('active', '1');
				$this->db->where('ownnames', $this->db_data['c_for']);
				$this->query = $this->db->get('administrators', 1);
				if ($this->query->num_rows() > 0) 
				{		

						$this->admindata = $this->query->row_array();
						
						$this->msg_data = array ('msg_title' => 'New Task Post from '.$this->db_data['c_from'].' @ '.FlipDateMail(CurrentTime()),
											'msg_body' => '<html><body>'.$this->db_data['c_title'].'
											</body></html>',
											'msg_date' => CurrentTime()
											);
						GoMail($this->msg_data, $this->admindata['email']);						
							
				}
				$this->session->set_flashdata('success_msg', 'Task posted & sent notification.');	
				
			
			
		}
	
	$this->_GoHome();
	}
	
function OpenTask($cid = '') 
{
		$this->_GoHome((int)$cid);
		
		$this->db->select("c_id, c_datetime, c_title, c_from, c_for, c_deleted");	
		$this->db->where('c_parent', '0');
		$this->db->where('c_id', (int)$cid);
		$this->query = $this->db->get('comm');
		
		if ($this->query->num_rows() > 0) $parent = $this->query->row_array();
		else $parent = FALSE;
		
		$this->mysmarty->assign('parent', $parent);
		
		$this->db->select("c_id, c_datetime, c_body, c_from");
		$this->db->where('c_parent', (int)$cid);
		$this->db->order_by("c_id", "DESC");
		$this->query = $this->db->get('comm');
		
		if ($this->query->num_rows() > 0) $data = $this->query->result_array();
		else $data = false;
			
		require_once($this->config->config['pathtopublic'].'/fckeditor/fckeditor.php');
				
				$this->editor = new FCKeditor('c_body');
				$this->editor->Value = '';				
				$this->editor->Width = "650";
				$this->editor->Height = "400";
				$this->editor->ToolbarSet	= 'Default' ;				
				
		$this->mysmarty->assign('posts', $data);
		$this->mysmarty->assign('cid', (int)$cid);
		$this->mysmarty->assign('message', $this->editor->CreateHtml());
		$this->mysmarty->view($this->go['sctr'].'/'.$this->go['sctr'].'_todos.html');

}	
	
function PostMessage($cid)
	{
		$this->_GoHome((int)$cid);
		
		if (isset($_POST['c_body'])) $this->bodylen = strlen($_POST['c_body']);
		else $this->bodylen = 0;
		
		if ($this->bodylen > 6)
		{	
				$this->db->select("c_title");	
				$this->db->where('c_parent', '0');
				$this->db->where('c_id', (int)$cid);
				$this->query = $this->db->get('comm');
		
				if ($this->query->num_rows() > 0) $parent = $this->query->row_array();
				else $parent['c_title'] = '';
		
				$this->db_data['c_datetime'] = CurrentTime();
				$this->db_data['c_parent'] = (int)$cid;
				$this->db_data['c_from'] = $this->session->userdata['ownnames'];
				$this->db_data['c_body'] = $this->input->post('c_body', TRUE);
				
				$this->db->insert('comm', $this->db_data);	
				
				$this->db->update('comm', array('c_lastreply_from' => $this->session->userdata['ownnames'], 'c_datetime' => CurrentTime()), array('c_id' => (int)$cid));
				
				$this->db->select('c_for, c_from');
				$this->db->where('c_id', (int)$cid);

				$this->query = $this->db->get('comm', 1);
				if ($this->query->num_rows() > 0) 
				{	
					$this->userownnames = $this->query->row_array();						
					$this->db->select('email');
					$this->db->where('active', '1');
					$this->db->where('ownnames', $this->userownnames['c_for']);
					$this->query = $this->db->get('administrators', 1);
					if ($this->query->num_rows() > 0) 
					{		
	
							$this->admindata = $this->query->row_array();
							
							if ($this->session->userdata['ownnames'] == $this->userownnames['c_for'])
							{							
							$this->msg_data = array ('msg_title' => 'Copy of Your Message Post for  "'.$parent['c_title'].'" @ '.FlipDateMail(CurrentTime()),
												'msg_body' => '<html><body>'.$this->db_data['c_body'].'
												</body></html>',
												'msg_date' => CurrentTime()
												);
							}
							else
							{
								$this->msg_data = array ('msg_title' => 'New Message Post for "'.$parent['c_title'].'" @ '.FlipDateMail(CurrentTime()),
												'msg_body' => '<html><body>From: '.$this->db_data['c_from'].'<br><br>'.$this->db_data['c_body'].'
												</body></html>',
												'msg_date' => CurrentTime()
												);
							}
							GoMail($this->msg_data, $this->admindata['email']);						
								
					}
					
					$this->db->select('email');
					$this->db->where('active', '1');
					$this->db->where('ownnames', $this->userownnames['c_from']);
					$this->query = $this->db->get('administrators', 1);
					if ($this->query->num_rows() > 0) 
					{		
	
							$this->admindata = $this->query->row_array();
							
							if ($this->session->userdata['ownnames'] == $this->userownnames['c_from'])
							{							
							$this->msg_data = array ('msg_title' => 'Copy of Your Message Post for  "'.$parent['c_title'].'" @ '.FlipDateMail(CurrentTime()),
												'msg_body' => '<html><body>'.$this->db_data['c_body'].'
												</body></html>',
												'msg_date' => CurrentTime()
												);
							}
							else
							{
								$this->msg_data = array ('msg_title' => 'New Message Post for "'.$parent['c_title'].'" @ '.FlipDateMail(CurrentTime()),
												'msg_body' => '<html><body>From: '.$this->db_data['c_from'].'<br><br>'.$this->db_data['c_body'].'
												</body></html>',
												'msg_date' => CurrentTime()
												);
							}
							
							GoMail($this->msg_data, $this->admindata['email']);						
								
					}
					
					
					$this->session->set_flashdata('success_msg', 'Message posted & sent notification.');	
				}
				else
				{
					$this->session->set_flashdata('error_msg', 'Cannot find user email assigned to task.');		
				}			
		}
		else
		{
			$this->session->set_flashdata('error_msg', 'Minumum body length is atleast 10 characters.');		
		}
	Redirect('Mycomm/OpenTask/'.(int)$cid);
				
	}
function DeleteMessage($parent = '', $cid = '')
	{
		$this->db->where('c_id', (int)$cid);
		$this->db->where('c_parent', (int)$parent);
		$this->db->delete('comm');
		Redirect('Mycomm/OpenTask/'.(int)$parent);
	}
function Delete ($cid = '') 
{
		$this->db->update('comm', array('c_deleted' => 1), array('c_id' => (int)$cid));
		$this->_GoHome();
}
	
	
////
	
function _GoHome($id = '') {
		if ((int)$id == 0) { 
			Redirect("/".$this->go['ctr']);
			exit();
			}
}
	
function _Start() {
		$this->load->model('Auth_model');
		$this->Auth_model->VerifyAdmin();	
		$this->mysmarty->assign('session',$this->session->userdata);
		$this->mysmarty->assign('error_msg',$this->session->flashdata('error_msg'));
		$this->mysmarty->assign('success_msg',$this->session->flashdata('success_msg'));
		$this->go = DoGo($this->router->class, $this->router->method);	
		$this->mysmarty->assign('go', $this->go);	
}
}