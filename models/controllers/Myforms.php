<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Myforms extends Controller {

function Myforms()
	{
		parent::Controller();
		$this->load->model('Myforms_model'); 
		$this->load->model('Auth_model');
		$this->Auth_model->VerifyAdmin();
		$this->Auth_model->CheckRole();


		if ($this->session->userdata['admin_id'] == 7) { echo 'Sorry, you don\'t have clearance for here.';exit();}
		if ($this->session->userdata['admin_id'] == 9) { echo 'Sorry, you don\'t have clearance for here.'; exit();}
		if ($this->session->userdata['admin_id'] == 10) { echo 'Sorry, you don\'t have clearance for here.'; exit();}
		
		$this->load->model('Myproducts_model');
		$this->mysmarty->assign('zero', $this->Myproducts_model->CheckWeightZero());
		
		$this->mysmarty->assign('session',$this->session->userdata);
		$this->mysmarty->assign('error_msg',$this->session->flashdata('error_msg'));
		$this->mysmarty->assign('success_msg',$this->session->flashdata('success_msg'));
		$this->mysmarty->assign('area', 'Forms');

	}
	
function index()
	{	
		$this->data = $this->Myforms_model->ListItems();
		$this->mysmarty->assign('list', $this->data['results']);
		$this->mysmarty->assign('pages', $this->data['pages']);
		$this->mysmarty->assign('page', 0);
		
		$this->db->select('f_id');
		$this->db->distinct();
		$query = $this->db->get('form_contact_comm');
		if ($query->num_rows() > 0)
			{
				foreach ($query->result_array() as $f)
				{
				$matched[$f['f_id']] = TRUE;
				}
			}
		else $matched = array();
		
		$this->mysmarty->assign('replies', $matched);
		
		$this->session->unset_userdata('page');
	$this->mysmarty->view('myforms/myforms_show.html');
	}

function Show($page = '') {
	
		$this->data = $this->Myforms_model->ListItems((int)$page);
		
		$this->db->select('f_id');
		$this->db->distinct();
		$query = $this->db->get('form_contact_comm');
		if ($query->num_rows() > 0)
			{
				foreach ($query->result_array() as $f)
				{
				$matched[$f['f_id']] = TRUE;
				}
			}
		else $matched = array();
		
		
		$this->mysmarty->assign('replies', $matched);
		
		$this->mysmarty->assign('list', $this->data['results']);
		$this->mysmarty->assign('pages', $this->data['pages']);
		$this->mysmarty->assign('page', (int)$page);

		$this->session->set_userdata('page', (int)$page);

		$this->mysmarty->view('myforms/myforms_show.html');
	}
	
function View($id = '') {
		if ((int)$id == 0) { Redirect("/Myforms"); exit;}
		$data = $this->Myforms_model->GetForm((int)$id);
		$this->mysmarty->assign('view', $data);
		
		$this->db->where('f_id', (int)$id);
		$this->db->order_by('fc_id', 'DESC');		
		$cquery = $this->db->get('form_contact_comm');
		
		$msgs = 0;
		if ($cquery->num_rows() > 0) {
				$comm = $cquery->result_array();
				$msgs = count($comm);	
			}
		else $comm = FALSE;

		if (isset($_POST['msg']))
			{
				
				$cdata['f_msg'] = $this->input->post('msg',TRUE);
				
				$cdata['f_id'] = (int)$id;
				$cdata['f_owner'] = 'admin';
				$cdata['f_time'] = CurrentTime();
							
				$this->db->insert('form_contact_comm', $cdata);
				
				$re = '';

				if ($msgs > 0)
					{
						$i = 1;
						while ($i <= $msgs) {
							$re = 'RE: '.$re;
							$i++;
						}	
					}
		
						$msg_data = array ('msg_title' => $re.'Reply regarding contact form message.',
												'msg_body' => $cdata['f_msg'],
												'msg_date' => CurrentTime()
											);
								$msg_data['msg_body'] .= '<br>TO REPLY to this message, follow this link:<Br><br><a href="'.Site_url().'contactreply/'.$data['code'].'/'.$data['fc_id'].'" target="_blank">'.Site_url().'contactreply/'.$data['code'].'/'.$data['fc_id'].'</a>';
						
							GoMail($msg_data, $data['email']);
							
					Redirect ('Myforms/View/'.$id);
					
			}
		
		
			require_once($this->config->config['pathtopublic'].'/fckeditor/fckeditor.php');
				
				$this->editor = new FCKeditor('msg');
				$this->editor->Width = "650";
				$this->editor->Height = "300";
				$this->editor->ToolbarSet	= 'Basic' ;
				$this->editor->Value = '';
			
		$this->mysmarty->assign('msg', $this->editor->CreateHtml());
		$this->mysmarty->assign('cid', (int)$id);		
		$this->mysmarty->assign('comm', $comm);
		
		$this->mysmarty->view('myforms/myforms_view.html');
	}

function Delete ($id = '')
	{
		$this->id = (int)$id;
		if ($this->id > 0) 
			{
			$this->Myforms_model->Delete($this->id);
			}
		Redirect("/Myforms/Show/".(int)$this->session->userdata('page'));
	}
	
}
