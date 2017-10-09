<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Forms extends Controller {

	function Forms()
	{
		parent::Controller();	
		$this->load->model('Menus_model');	
		$this->load->model('Product_model');
		$this->Menus_model->DoTracking();
		$this->Menus_model->GetStructure();		
		$this->Product_model->GetStructure('top');
		
		if (isset($this->session->userdata['user_id'])) {
			$this->load->model('Start_model');
			$this->load->model('Auth_model');
			$this->Auth_model->VerifyUser();	
			$this->mysmarty->assign('session',$this->session->userdata);
		}
			if (isset($this->session->userdata['cart']) && ($this->session->userdata['cart'] != '') )
				{
				$this->mysmarty->assign('cartsession',$this->session->userdata['cart']);
				$this->mysmarty->assign('carttotal', $this->_CartTotal());
				}		
	}
		
	function index() 
	{
		header('Location:'.$this->config->config['base_url']);
	}
	
	
	
	
	/*
	
	function Menu($place = '', $level1 = '', $level2 = '') 
	{
		if ($level1 == '') {goto("/"); break; exit();}
		if ((int)$place == 0) {goto("/"); break; exit();}
				
		$this->l1 = CleanInput($level1);
		$this->submenu = $this->Menus_model->GetSubMenu((int)$place, '1', $this->l1);
		$this->mysmarty->assign('l1string', $this->l1);
		$this->mysmarty->assign('submenu', array((int)$place => $this->submenu));
														
		if ($level2 != '') 
			{ 
			$this->l2 = CleanInput($level2);
		   	$this->subsubmenu = $this->Menus_model->GetSubMenu((int)$place,'2', $this->l2);
			$this->mysmarty->assign('l2string', $this->l2);
			$this->mysmarty->assign('subsubmenu', array((int)$place => $this->subsubmenu));											   
			}
		
		$this->mysmarty->assign('innerview', 'menu');
		$this->mysmarty->view('welcome/welcome_main.html');
		
	}
	function Item($s_sefurl)
	{
		$this->sefstring = CleanInput($s_sefurl);

		$this->item = $this->Menus_model->GetItem($this->sefstring);

		if ($this->item['s_link'] != '') {
			
			$this->urlparts = parse_url($this->item['s_link']);
			$this->urlparts['home'] = $this->urlparts['scheme'].'://'.$this->urlparts['host'];

			if ($this->urlparts['home'] == $this->config->config['base_url']) { 
			header('Location:'.$this->config->config['base_url'].$this->urlparts['path']);
			//echo ($this->config->config['base_url'].'/'.$this->config->config['language_abbr'].$this->urlparts['path']);
			exit;
			}			
			else {header('Location:'.$this->item['s_link']);}		
			exit;			
		}
		
		$this->mysmarty->assign('item', $this->item);

		switch($this->item['s_level']) {
				
				case 2:
				
					$this->subsubmenu[$this->item['s_menu']] = $this->Menus_model->GetSubMenu($this->item['s_menu'], '2', '', $this->item['s_levelparentid']);
					$this->mysmarty->assign('l3string', $this->sefstring);
					$this->mysmarty->assign('subsubmenu', $this->subsubmenu);
					
					$this->l1parent = $this->Menus_model->GetParent((int)$this->item['s_levelparentid']);
					$this->submenu[$this->item['s_menu']] = $this->Menus_model->GetSubMenu($this->item['s_menu'], '1', '', $this->l1parent['s_levelparentid']);
					$this->mysmarty->assign('l2string', $this->l1parent['s_seourl']);				
					$this->mysmarty->assign('submenu', $this->submenu);
					
					$this->l0parent = $this->Menus_model->GetParent((int)$this->l1parent['s_levelparentid']);
					$this->mysmarty->assign('l1string', $this->l0parent['s_seourl']);
				break;
				case 1:	
					$this->mysmarty->assign('l1string', $this->Menus_model->GetParentSef((int)$this->item['s_levelparentid']));
					$this->mysmarty->assign('l2string', $this->sefstring);

					$this->mysmarty->assign('submenu', array($this->item['s_menu'] => $this->Menus_model->GetSubMenu($this->item['s_menu'], '1', '', $this->item['s_levelparentid'])));
				
				break;
				default:
				$this->mysmarty->assign('l1string', $this->sefstring);
			}
			

		
		$this->mysmarty->assign('innerview', 'viewopen');		
		$this->mysmarty->view('welcome/welcome_main.html');
		
		
		
	}
	
	
//////////////////////// Add-ons 
	
	
	
	function Faq($action = '', $id = '') {
		
		if ($action == 'View') {
			if ((int)$id == 0)	{goto("Faq/Categories"); break; exit();}
			$this->faq = $this->Menus_model->GetFaq((int)$id);
			$this->faqcatopen = $this->Menus_model->GetOpenFaqCat((int)$this->faq['f_cat']);
			$this->mysmarty->assign('faqcatopen', $this->faqcatopen);
			$this->mysmarty->assign('faq', $this->faq);
			$this->mysmarty->assign('innerview', 'faqview');		
			
		}
		elseif ($action == 'Categories') {
		 $this->mysmarty->assign('faqcat', $this->Menus_model->ListFaqCategories());
		 $this->mysmarty->assign('innerview', 'faqcat');
			
		}
		else {
			if ((int)$id == 0)	{goto("Faq/Categories"); break; exit();}
			$this->faqcatopen = $this->Menus_model->GetOpenFaqCat((int)$id);
		   $this->faqlist = $this->Menus_model->ListFaq((int)$id);
		   $this->mysmarty->assign('faqlist', $this->faqlist);
		   $this->mysmarty->assign('faqcatopen', $this->faqcatopen['f_cattitle']);
		   		$this->mysmarty->assign('innerview', 'faqlist');		
		   
		}
		
		$this->mysmarty->view('welcome/welcome_main.html');
		
	}
		function News($action = '', $id = '') {

		if ($action == 'View') {
			
			$this->mysmarty->assign('newsview', $this->Menus_model->GetNews((int)$id));
			$this->mysmarty->assign('innerview', 'newsview');				
		
			$this->mysmarty->view('welcome/welcome_main.html');
		}
		else {
			
			$this->mysmarty->assign('newslist', $this->Menus_model->ListNews());
			$this->mysmarty->assign('innerview', 'newslist');				
		
			$this->mysmarty->view('welcome/welcome_main.html');
		}
	}
	
	
	
function Newsletter () 
	{
	//$this->lang->load($this->config->config['lang_uri_abbr'][$this->config->config['language_abbr']].'/email');
	$this->load->library('form_validation');
	$this->fieldnames = array (
							   'bg' => array (
											  'email' => 'Е-мейл адрес'
											  ),
							   'en' => array (
											  'email' => 'Email Address'
											  )
							   );
	
	$this->messages = array (
							   'bg' => array (
											  'suc-unsc' => 'Успешно оптисан',
											  'eml-no-reg' => 'Този е-мейл не е регистриран',
											  'eml-reg' => 'Този е-мейл вече е регистриран',
											  'suc-sucr' => 'Успешно записан'
											  ),
							   'en' => array (
											  'suc-unsc' => 'Succesfully Unsubscribed'
											  ,
											  'eml-no-reg' => 'E-Mail is not registered',
											  'eml-reg' => 'E-Mail is already registered',
											  'suc-sucr' => 'Succesfully Subscribed'
											  )
							   );
	
	$this->form_validation->set_rules('newsletter', $this->fieldnames[$this->config->config['language_abbr']]['email'], 'trim|required|min_length[7]|valid_email|xss_clean');	
	if ($this->form_validation->run() == FALSE)
			{	
				
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);		
				$this->mysmarty->assign('innerview', 'newsletter');
				
				
				
				$this->mysmarty->view('welcome/welcome_main.html');
				exit();

			}
			else 
			{	
			
				$this->checkemail = $this->Menus_model->FindNewsletterEmail($this->form_validation->set_value('newsletter'));
				if ($this->input->post('unsubscribe') == 'on') {
					if ($this->checkemail) 
					{
						$this->Menus_model->UnsubscribeEmail((int)$this->checkemail['n_id']);
						
						$this->_DoForm($this->form_validation->set_value('newsletter'), 'newsletterunsubscribe');
						
						$this->mysmarty->assign('success', $this->messages[$this->config->config['language_abbr']]['suc-unsc']);
						$this->mysmarty->assign('innerview', 'newsletter');

						
						
						$this->mysmarty->view('welcome/welcome_main.html');
						exit();	
					}
					else {					
						
						$this->mysmarty->assign('errors', array('newsletter' => $this->messages[$this->config->config['language_abbr']]['eml-no-reg']));		
						$this->mysmarty->assign('innerview', 'newsletter');
						
						
						
						$this->mysmarty->view('welcome/welcome_main.html');
						exit();	
					}
					
				}		

				if ($this->checkemail) 
				{
				$this->mysmarty->assign('errors', array('newsletter' => $this->messages[$this->config->config['language_abbr']]['eml-reg']));		
				$this->mysmarty->assign('innerview', 'newsletter');
				
				
				
				$this->mysmarty->view('welcome/welcome_main.html');
				exit();						
				}
				
				$this->Menus_model->SubscribeEmail($this->form_validation->set_value('newsletter'));
				
				$this->_DoForm($this->form_validation->set_value('newsletter'), 'newslettersubscribe');
				
				$this->mysmarty->assign('success', $this->messages[$this->config->config['language_abbr']]['suc-sucr']);
				$this->mysmarty->assign('innerview', 'newsletter');
				
				
				
				$this->mysmarty->view('welcome/welcome_main.html');
				exit();
			}
	}

function Unsubscribe($code = '') 
	{
	if ((int)$code == '') {goto(""); exit;}
	
	$this->messages = array (
							   'bg' => array (
											  'suc-unsc' => 'Успешно оптисан',
											  'eml-no-reg' => 'Този е-мейл не е регистриран',
											  'eml-reg' => 'Този е-мейл вече е регистриран',
											  'suc-sucr' => 'Успешно записан'
											  ),
							   'en' => array (
											  'suc-unsc' => 'Succesfully Unsubscribed'
											  ,
											  'eml-no-reg' => 'E-Mail is not registered',
											  'eml-reg' => 'E-Mail is already registered',
											  'suc-sucr' => 'Succesfully Subscribed'
											  )
							   );
	
	$this->checknumber = $this->Menus_model->FindNewsletterCode($code);

	if ($this->checknumber) 
					{	
						$this->Menus_model->UnsubscribeEmail((int)$this->checknumber['n_id']);
						
						$this->_DoForm($this->checknumber['n_email'], 'newsletterunsubscribe');
						
						$this->mysmarty->assign('success', $this->messages[$this->config->config['language_abbr']]['suc-unsc']);
						$this->mysmarty->assign('innerview', 'newsletter');

						
						
						$this->mysmarty->view('welcome/welcome_main.html');
						exit();	
					}
					else
					{
						$this->mysmarty->assign('errors', array('newsletter' => $this->messages[$this->config->config['language_abbr']]['eml-no-reg']));		
						$this->mysmarty->assign('innerview', 'newsletter');
						
						
						
						$this->mysmarty->view('welcome/welcome_main.html');
						exit();	
					}

					
	}
function Contact () 
	{
		
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
	$this->form_validation->set_rules('Code', $this->fieldnames[$this->config->config['language_abbr']]['Code'], 'trim|required|min_length[3]|xss_clean');
	
	$this->captcha = $this->Captcha_model->CheckCaptcha($this->input->post('Code', TRUE));
	
	if (($this->form_validation->run() == FALSE) || !$this->captcha)
	
			{	
				$this->inputdata = array(
										 'name' => $this->input->post('name', TRUE),
										 'email' => $this->input->post('email', TRUE),
										 'body' => $this->input->post('body', TRUE)
										 );
				
				$this->Captcha_model->DoCaptcha();
				
				if ((!$this->captcha) && (count($_POST) > 0)) {
					if ($this->config->config['language_abbr'] == 'en') $this->mysmarty->assign('errorcaptcha', 'Invalid Security Code');
					else $this->mysmarty->assign('errorcaptcha', 'Невалиден код');
					
				}
					
				$this->mysmarty->assign('inputdata', $this->inputdata);		
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);		
				$this->mysmarty->assign('innerview', 'contact');
				
				$this->mysmarty->view('welcome/welcome_main.html');
				exit();

			}
			else 
			{	
						if (isset($this->session->userdata['user_id'])) {
							
						$this->usercdata = $this->Menus_model->GetUserContactData((int)$this->session->userdata['user_id']);
						
						$this->formdata = array (
											'user_id' => (int)$this->usercdata['user_id'],
											'names'=> $this->usercdata['fname'].' '.$this->usercdata['lname'],
											'email'=> $this->usercdata['email'],
											'date'=> CurrentTime(),
											'contents' => $this->form_validation->set_value('body')
												 );
						}
						else 
						{
						$this->formdata = array (
											'user_id' => 0,
											'names'=> $this->form_validation->set_value('name'),
											'email'=> $this->form_validation->set_value('email'),
											'date'=> CurrentTime(),
											'contents' => $this->form_validation->set_value('body')
												 );
						}						
						
						$this->_DoForm($this->formdata, 'contact');
						$this->mysmarty->assign('innerview', 'contactok');
						$this->mysmarty->view('welcome/welcome_main.html');
						exit();	
				}
	}

function Sitemap ()
	{	
		$this->subtree['top'] = $this->Menus_model->GetSubtree(1);
		$this->subtree['left'] = $this->Menus_model->GetSubtree(2);		
		$this->subtree['footer'] = $this->Menus_model->GetSubtree(3);	
		$this->subsubtree['top'] = $this->Menus_model->GetSubSubtree(1);
		$this->subsubtree['left'] = $this->Menus_model->GetSubSubtree(2);		
		$this->subsubtree['footer'] = $this->Menus_model->GetSubSubtree(3);
		$this->mysmarty->assign('subtree', $this->subtree);	
		$this->mysmarty->assign('subsubtree', $this->subsubtree);	
		$this->mysmarty->assign('innerview', 'sitemap');
		$this->mysmarty->view('welcome/welcome_main.html');
	}
	
function Poll ($action = '') 
	{
	
	if ($action == 'Submit') {
		$this->answer = (int)$this->input->post('pollanswer');

		if ($this->answer == '0') 
		{
		  if($this->config->config['language_abbr'] == 'bg') {
			$this->session->set_flashdata('pollerror', 'Моля изберете отговор...');
			}
			else
			  {
			  $this->session->set_flashdata('pollerror', 'Please select an answer...');
			  }
			goto("/Poll/Error"); 
			break; 
			exit();	
		}
		
		$this->allowed = $this->Menus_model->CountPollAnswers();

		if (count($this->allowed) < 2) { goto("/"); break; exit();}

		if ($this->allowed[$this->answer] == 1)
			{
			$this->activepoll = $this->Menus_model->GetActivePollID();
			$this->alreadyanswered = $this->Menus_model->CheckIfHasAnswered($this->input->ip_address(), $this->activepoll['p_id']);
								 
			if (!$this->alreadyanswered)
			
				{
					$this->Menus_model->InsertPollAnswer($this->activepoll['p_id'], CurrentTime(), $this->answer, $this->input->ip_address());
					goto("/Poll/ThankYou"); 
					break; 
					exit();
					
				}
				else
				{
				  if($this->config->config['language_abbr'] == 'bg') {
					$this->session->set_flashdata('pollerror', 'Вече сте отговорили на тази анкета');
					}
					else{
          $this->session->set_flashdata('pollerror', 'You have already taken this poll');
          }
					goto("/Poll/Error"); 
					break; 
					exit();
					
				}
			}
			else 
			{
			 if($this->config->config['language_abbr'] == 'bg') {
						$this->session->set_flashdata('pollerror', 'Невалиден отговор');
					}
					else{
         	$this->session->set_flashdata('pollerror', 'Unavailable answer');
          }
			
				goto("/Poll/Error"); 
				break; 
				exit();
			}
	}
	elseif ($action == 'Error') 
	{
		if ($this->session->flashdata('pollerror') == '') {goto(""); break; exit();}
		$this->mysmarty->assign('innerview', 'pollresult');
		$this->mysmarty->assign('pollerror', $this->session->flashdata('pollerror'));	
		$this->mysmarty->assign('polltype', 'error');
		$this->mysmarty->view('welcome/welcome_main.html');
	}
	else 
	{	
		$this->mysmarty->assign('innerview', 'pollresult');
		$this->mysmarty->assign('polltype', 'ok');	
		$this->mysmarty->view('welcome/welcome_main.html');
	}
	
}


//////////////////////////////////////////

function _DoForm ($data, $type = '') {
	
	switch ($type) {
		
		case 'contact':
						$this->formcontactid = $this->Menus_model->SaveContactForm($data);

						$this->msg_data = array ('msg_title' => 'Contact form data from '.$data['names'].' @ '.FlipDateMail($data['date']),
											'msg_body' => $data['contents'],
											'msg_date' => $data['date']
											);	
						
						GoMail($this->msg_data, '', $data['email']);
						$this->msg_data['msg_body'] = 'Please see <a href="'.Site_url().'Myforms/View/'.$this->formcontactid.'">form data</a>';
	break;
		case 'newsletterunsubscribe':
					
						$this->msg_data = array ('msg_title' => 'Newsletter Unsubscription @ '.FlipDateMail(CurrentTime()),
											'msg_body' => 'E-mail "'.$data.'" has unsubscribed.',
											'msg_date' => CurrentTime()
											);	
						
						GoMail($this->msg_data);
		
	break;

		case 'newslettersubscribe':
				
					
						$this->msg_data = array ('msg_title' => 'New Newsletter Subscription @ '.FlipDateMail(CurrentTime()),
											'msg_body' => 'E-mail "'.$data.' has subscribed.',
											'msg_date' => CurrentTime()
											);	
						
						GoMail($this->msg_data);
	break;
	
	default:	
	}
	

	$this->Menus_model->InsertHistoryData($this->msg_data);
	
	}	
	function _CartTotal() 
	{
	$this->data = $this->session->userdata['cart'];
	$total = 0;
	foreach ($this->data as $key => $value) 
		{
		$total = $total + ((int)$value['quantity'] * (float)$value['p_price']);
		}
	return (float)$total;
	}*/
}
