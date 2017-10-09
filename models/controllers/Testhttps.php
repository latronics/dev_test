<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Testhttps extends Controller {

	function Testhttps()
	{
		parent::Controller();	
		exit();
	}
		
	function index() 
	{
		
	}
function Contact () 
	{
printcool($_POST);
	$uri = explode('/', $_SERVER['HTTP_REFERER']);	
	if ($uri[2] == 'www.ebay.com' || $uri[2] == 'ebay.com') 
	{
		header('Location: '.$_SERVER['HTTP_REFERER']);
		exit();
	}
	$this->load->library('form_validation');
	$this->load->model('Captcha_model');
	
	$this->fieldnames = array (
							   'bg' => array (
											  'name' => 'Имена',											  
											  'email' => 'Е-мейл адрес',
											  'body' => 'Съобшение',
											  'Code' => 'Код'
											  ),
							   'en' => array (
											  'name' => 'Names',											  
											  'email' => 'Е-mail address',
											  'body' => 'Message',
											  'Code' => 'code'
											  )
							   );
	
	$this->mysmarty->assign('tmactive', 'contact');
	
	if (!isset($this->session->userdata['user_id'])) 
		{		
		$this->form_validation->set_rules('name', $this->fieldnames[$this->config->config['language_abbr']]['name'], 'trim|required|min_length[5]|xss_clean');	
		$this->form_validation->set_rules('email', $this->fieldnames[$this->config->config['language_abbr']]['email'], 'trim|required|min_length[7]|valid_email|xss_clean');
		}
		
	$this->form_validation->set_rules('body', $this->fieldnames[$this->config->config['language_abbr']]['body'], 'trim|required|xss_clean');	
	
	
	$this->captcha = $this->Captcha_model->CheckCaptcha();

	if (($this->form_validation->run() == FALSE) || !$this->captcha)
	
			{	
				$this->inputdata = array(
										 'name' => $this->input->post('name', TRUE),
										 'email' => $this->input->post('email', TRUE),
										 'body' => $this->input->post('body', TRUE)
										 );
				
				$this->Captcha_model->DoCaptcha();
				
				if ((!$this->captcha) && (count($_POST) > 0)) {
					if ($this->config->config['language_abbr'] == 'en') $this->mysmarty->assign('errorcaptcha', 'Please specify if you are human');
					else $this->mysmarty->assign('errorcaptcha', 'Невалиден код');
					
				}
				
				$this->mysmarty->assign('inputdata', $this->inputdata);		
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);		
				$this->mysmarty->assign('innerview', 'contact');
				
				$this->mysmarty->view('welcome/welcome_main_test.html');
				exit();

			}
			else 
			{	
					$this->load->helper('arithmetic');
					$this->insertdata['code'] = rand_string(50);
					
						if (isset($this->session->userdata['user_id'])) {
							
						$this->usercdata = $this->Menus_model->GetUserContactData((int)$this->session->userdata['user_id']);
						
						$this->formdata = array (
											'user_id' => (int)$this->usercdata['user_id'],
											'names'=> $this->usercdata['fname'].' '.$this->usercdata['lname'],
											'email'=> $this->usercdata['email'],
											'date'=> CurrentTime(),
											'contents' => $this->form_validation->set_value('body'),
											'code' => $this->insertdata['code']
											
												 );
						}
						else 
						{
						$this->formdata = array (
											'user_id' => 0,
											'names'=> si($this->form_validation->set_value('name')),
											'email'=> si($this->form_validation->set_value('email')),
											'date'=> CurrentTime(),
											'contents' => si($this->form_validation->set_value('body')),
											'code' => $this->insertdata['code']
												 );
						}						
					
						$this->mysmarty->assign('innerview', 'contactok');
						$this->mysmarty->view('welcome/welcome_main_test.html');
						exit();	
				}
	}

}
