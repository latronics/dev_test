<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Mynewsletter extends Controller {

function Mynewsletter()
	{
		parent::Controller();
		$this->load->model('Mynewsletter_model'); 
		$this->load->model('Auth_model');
		$this->Auth_model->VerifyAdmin();	
		if ($this->session->userdata['admin_id'] == 7) { echo 'Sorry, you don\'t have clearance for here.';exit();}
		if ($this->session->userdata['admin_id'] == 9) { echo 'Sorry, you don\'t have clearance for here.'; exit();}
		if ($this->session->userdata['admin_id'] == 10) { echo 'Sorry, you don\'t have clearance for here.'; exit();}
		
		$this->load->model('Myproducts_model');
		$this->mysmarty->assign('zero', $this->Myproducts_model->CheckWeightZero());
		
		$this->mysmarty->assign('session',$this->session->userdata);
		$this->mysmarty->assign('error_msg',$this->session->flashdata('error_msg'));
		$this->mysmarty->assign('success_msg',$this->session->flashdata('success_msg'));
		$this->mysmarty->assign('area', 'Newsletter');
	}
	
function index()
	{
		//FCK LOAD FOR INPUT
		$this->data = $this->Mynewsletter_model->GetAllSubscribers();
		$this->mysmarty->assign('subscribers', $this->data['results']);
		$this->mysmarty->assign('pages', $this->data['pages']);
		$this->mysmarty->assign('page', 0);
		$this->session->unset_userdata('page');
		$this->mysmarty->view('mynewsletter/mynewsletter_main.html');
	}
function Show($page = '')
	{
		$this->data = $this->Mynewsletter_model->GetAllSubscribers((int)$page);
		$this->mysmarty->assign('subscribers', $this->data['results']);
		$this->mysmarty->assign('pages', $this->data['pages']);
		$this->mysmarty->assign('page', (int)$page);
		$this->session->set_userdata('page', (int)$page);
		$this->mysmarty->view('mynewsletter/mynewsletter_main.html');
	}
	
function DeleteSubscriber($id = '')
	{
	if ((int)$id > 0) $this->Mynewsletter_model->DeleteSubscriber((int)$id);
	redirect("/Mynewsletter/Show/".(int)$this->session->userdata('page'));	
	}
function AddSubscriber ()
	{
		
	$this->load->library('form_validation');
		
	$this->form_validation->set_rules('email', 'Email Address', 'trim|required|min_length[7]|valid_email|xss_clean');
	
	if ($this->form_validation->run() == FALSE)
			{
			$this->errormessage = $this->form_validation->_error_array;
			$this->session->set_flashdata('error_msg', $this->errormessage['email']);		
			}
			else 
			{
				$this->checkemail = $this->Mynewsletter_model->FindNewsletterEmail($this->form_validation->set_value('email'));
				
				if ($this->checkemail) 
				{
				$this->session->set_flashdata('error_msg', 'E-Mail is already registered');	
				redirect("/Mynewsletter");					
				exit();
				}				
			$this->Mynewsletter_model->AddSubscriber($this->form_validation->set_value('email'));
			}
	redirect("/Mynewsletter/Show/".(int)$this->session->userdata('page'));
	}
function ActivateSubscriber ($id = '') 
	{
	if ((int)$id > 0) $this->Mynewsletter_model->ActivateSubscriber((int)$id);	
	redirect("/Mynewsletter/Show/".(int)$this->session->userdata('page'));
	}
	
function DeactivateSubscriber ($id = '') 
	{
	if ((int)$id > 0) $this->Mynewsletter_model->DeactivateSubscriber((int)$id);		
	redirect("/Mynewsletter/Show/".(int)$this->session->userdata('page'));
	}
function SendNewsletter ()
	{
	$this->load->library('form_validation');
	$this->form_validation->set_rules('title', 'Newsletter Title', 'trim|required|xss_clean');
	$this->form_validation->set_rules('body', 'Newsletter Body', 'trim|required|xss_clean');
	if ($this->form_validation->run() == FALSE)
			{
			
			require_once($this->config->config['pathtopublic'].'/fckeditor/fckeditor.php');
				
				$this->editor = new FCKeditor('body');
				
				if (count($_POST) >	0) $this->editor->Value = $this->input->post('body');
				else $this->editor->Value = '';
				
				$this->editor->Width = "650";
				$this->editor->Height = "400";
				$this->editor->ToolbarSet	= 'Basic' ;				
				
				$this->inputdata = array (
										'title' => $this->input->post('title', TRUE),										
										'type' => (int)$this->input->post('type'),
										'body' => $this->editor->CreateHtml()
										);							
			
			$this->mysmarty->assign('inputdata', $this->inputdata);				
			$this->mysmarty->assign('errors', $this->form_validation->_error_array);
			$this->mysmarty->view('mynewsletter/mynewsletter_compose.html');
			}
			else 
			{
				$this->senddata = array(
										'title' => $this->form_validation->set_value('title'),
										'body' => $this->form_validation->set_value('body')
										);
				$this->inputdata['type'] = (int)$this->input->post('type');
				if (isset($this->inputdata['type']) && ((int)$this->inputdata['type'] == 1)) $type = 1;
				else $type = 0;	
				
				$this->Mynewsletter_model->SaveNewsletter($this->senddata);
				
				$this->_MailNewsletter($this->senddata, (int)$type);
				
				$this->session->set_flashdata('success_msg', 'Newsletter has been sent.');	

				redirect("/Mynewsletter/Show/".(int)$this->session->userdata('page'));				
			}

	}

function ViewNewsletter($id = '')
	{
		if((int)$id == 0) { redirect("/Mynewsletter"); exit();}
		
		$this->load->library('form_validation');
	$this->form_validation->set_rules('title', 'Newsletter Title', 'trim|required|xss_clean');
	$this->form_validation->set_rules('body', 'Newsletter Body', 'trim|required');
	
	if ($this->form_validation->run() == FALSE)
			{
				
				$this->newsletter = $this->Mynewsletter_model->GetNewsletter((int)$id);
			
				require_once($this->config->config['pathtopublic'].'/fckeditor/fckeditor.php');
				
				$this->editor = new FCKeditor('body');
				$this->editor->Width = "650";
				$this->editor->Height = "400";
				$this->editor->ToolbarSet	= 'Basic' ;
				
				if (count($_POST) >	0) {
						$this->editor->Value = $this->input->post('body');
				}
				else 
				{
					$this->editor->Value = $this->newsletter['na_body'];
					$this->newsletter['na_body'] = $this->editor->CreateHtml();
				}
				
								
				$this->inputdata = array (
										'title' => $this->input->post('title', TRUE),
										'type' => (int)$this->input->post('type'),
										'body' => $this->editor->CreateHtml()
										);							
			
				$this->mysmarty->assign('newsletter', $this->newsletter);
				$this->mysmarty->assign('inputdata', $this->inputdata);				
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);
			
				
				
				$this->mysmarty->view('mynewsletter/mynewsletter_view.html');
				
			}
			else 
			{
				$this->senddata = array(
										'title' => $this->form_validation->set_value('title'),
										'body' => $this->form_validation->set_value('body')
										);
				$this->inputdata['type'] = (int)$this->input->post('type');
				if (isset($this->inputdata['type']) && ((int)$this->inputdata['type'] == 1)) $type = 1;
				else $type = 0;	
				
				$this->Mynewsletter_model->UpdateNewsletter($this->senddata, (int)$id);
				
				$this->_MailNewsletter($this->senddata, (int)$type);

				$this->session->set_flashdata('success_msg', 'Newsletter has been sent.');	

				redirect("/Mynewsletter/Archive/".(int)$this->session->userdata('page'));				
			}
			
		
	}
	

function Archive ($page = '') 
	{
		$this->data = $this->Mynewsletter_model->GetArchive((int)$page);
		$this->mysmarty->assign('archive', $this->data['results']);
		$this->mysmarty->assign('pages', $this->data['pages']);
		$this->mysmarty->assign('page', (int)$page);
		$this->session->set_userdata('page', (int)$page);
		$this->mysmarty->view('mynewsletter/mynewsletter_archive.html');
		
		
	}
	
	
function DeleteArchive($id = '')
	{
	if ((int)$id > 0) $this->Mynewsletter_model->DeleteNewsletter((int)$id);
	redirect("/Mynewsletter/Archive/".(int)$this->session->userdata('page'));	
	}
	
function _MailNewsletter($data, $type = '')
	{

	$this->sendlist = $this->Mynewsletter_model->GetActiveSubscriberEmails((int)$type);

	if (count($this->sendlist) > 0) 
		{				
		
		$msgbody_header = '<html>

<body style="background:#FFF; margin:0; font-family:Tahoma, Verdana, Arial; font-size:12px; color:#565656;">

<div style="margin:5px 10px 5px 10px; background:#8cc540; border:1px solid #f4b43d; padding:5px 10px 5px 10px;">
<div style="float:left; margin-left:20px; height:81px; line-height:81px; vertical-align:middle; font-size:25px; color:#f3f3f3;">Newsletter</div><br clear="all"><br>
<div style="background:#FFF; padding:15px;  border:1px solid #f4b43d;">';
		$msgbody_footer = '</div>
</div>
</body>
</html>
';
						foreach ($this->sendlist as $value) 
							{
							
								$msg_data = array ('msg_title' => $data['title'],
												'msg_body' => $msgbody_header.$data['body'],
												'msg_date' => CurrentTime()
											);
						
						
								if ((int)$type != 1) {
									
									$msg_data['msg_body'] = $msg_data['msg_body'].'<br><br><div style="background:#8a8a8a; float:left; color:#f3f3f3; padding:5px; margin:5px 5px 0px 0px;">To Unsubscribe <a href="'.site_url().'Newsletter/Unsubscribe/'.$value['n_code'].'" style="color:#f3f3f3;">click here</a></div><br clear="all">';
								}
								$msg_data['msg_body'] = $msg_data['msg_body'].$msgbody_footer;				
								
								GoMail($msg_data, $value['n_email'], $this->config->config['newsletter_email']);
								
							}

		}
				
	}



}
