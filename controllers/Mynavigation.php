<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mynavigation extends Controller {

function Mynavigation()
	{
		parent::Controller();
		$this->load->model('Mynavigation_model'); 
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
		$this->mysmarty->assign('area', 'Navigation');
	}
	
function index()
	{
        $page_checker = $this->uri->segment(1);
        $this->mysmarty->assign('page_checker', $page_checker);
		$this->mysmarty->view('mynavigation/mynavigation_main.html');
	}
	
function SelectMenu($id = '') {
	$this->session->set_userdata('menuid', (int)$id);
	$this->_CheckMenuID();
	Redirect("/Mynavigation/Show");
	
	}
function Show($level1 = '', $level2 = '', $sortby = '') 
	{	
		$this->_CheckMenuID();
		
		$this->_Levelize((int)$level1, (int)$level2);		
		$this->sortby = CleanInput($sortby);
		$this->mysmarty->assign('sortby', $this->sortby);	
	
		$this->mysmarty->assign('list', $this->Mynavigation_model->ListItems($this->l1, $this->l2, $this->sortby));	
		$this->mysmarty->view('mynavigation/mynavigation_show.html');
	}
	
function Delete ($id = '')
	{
		$this->_CheckMenuID();
		
		$this->id = (int)$id;
		if ($this->id > 0) 
			{
			$this->Mynavigation_model->Delete($this->id);
			}
		Redirect("/Mynavigation/Show/".$this->session->flashdata('l1')."/".$this->session->flashdata('l2'));
		break;	
	}
function ChangeOrder ($id = '')
	{	
		$this->_CheckMenuID();
		
		$this->id = (int)$id;
		$this->order = (int)$this->input->post('s_order', TRUE);
		if ($this->id > 0) $this->Mynavigation_model->ChangeOrder($this->id, $this->order);
		Redirect("/Mynavigation/Show/".$this->session->flashdata('l1')."/".$this->session->flashdata('l2'));
		break;
	}
function ReOrder ($l1 = '', $l2 = '', $sortby = '')
	{	
		$this->_CheckMenuID();
		
		$this->l1 = (int)$l1;
		$this->l2 = (int)$l2;
		$this->_CheckCorrectLevels($this->l1,$this->l2);
		$this->sortby = CleanInput($sortby);
		$this->Mynavigation_model->ReOrder($this->l1, $this->l2, $this->sortby);
		Redirect("/Mynavigation/Show/".$this->session->flashdata('l1')."/".$this->session->flashdata('l2'));
		break;
	}
function MakeVisible ($id = '')
	{	
		$this->_CheckMenuID();
		
		$this->id = (int)$id;
		if ($this->id > 0) $this->Mynavigation_model->MakeVisible($this->id);
		Redirect("/Mynavigation/Show/".$this->session->flashdata('l1')."/".$this->session->flashdata('l2'));
		break;
	}
	function MakeNotVisible ($id = '')
	{	
		$this->_CheckMenuID();
		
		$this->id = (int)$id;
		if ($this->id > 0) $this->Mynavigation_model->MakeNotVisible($this->id);
		Redirect("/Mynavigation/Show/".$this->session->flashdata('l1')."/".$this->session->flashdata('l2'));
		break;	
	}
function Edit($level1 = '', $level2 = '', $itemid = '')
	{	
		$this->_CheckMenuID();
		
		$this->id = (int)$itemid;
		$this->l1 = (int)$level1;
		$this->l2 = (int)$level2;
		$this->_Levelize($this->l1,$this->l2);
		
		$this->displays = $this->Mynavigation_model->GetItem($this->id);


		$this->load->library('form_validation');

					$this->form_validation->set_rules('s_order', 'Order', 'trim|required|xss_clean');
					$this->form_validation->set_rules('s_seourl', 'Sef Url', 'trim|min_length[3]|xss_clean');
					if ($this->displays['s_type'] == 's') {
						$this->form_validation->set_rules('s_body', 'Body', 'trim');
						$this->form_validation->set_rules('s_link', 'Body', 'trim|xss_clean');						
						$this->form_validation->set_rules('s_title', 'Title', 'trim|required|min_length[3]|xss_clean');
						
					} else {
						$this->form_validation->set_rules('s_title', 'Title', 'trim|required|min_length[3]|xss_clean');
					}
			

											$this->inputdata = array(										
													's_order' => $this->input->post('s_order', TRUE),
													's_body' => $this->input->post('s_body'),													
													's_link' => $this->input->post('s_link', TRUE)
													);														
											
											$this->inputdata['s_title'] = $this->input->post('s_title', TRUE);
																						

								if (strlen($this->_CleanSef($this->input->post('s_seourl', TRUE))) == 0) {
											$this->inputdata['s_seourl'] = $this->_CleanSef($this->inputdata['s_title']);
										    } else {
											$this->inputdata['s_seourl'] = $this->_CleanSef($this->input->post('s_seourl', TRUE));
											}										
								$this->seoexists = $this->Mynavigation_model->CheckSurlUpdateExists($this->inputdata['s_seourl'], $this->id);	

										
		if (($this->form_validation->run() == FALSE) || $this->seoexists)
			{	
				if ($this->displays['s_type'] == 's') {
				require_once($this->config->config['pathtopublic'].'/fckeditor/fckeditor.php');
				$this->editor = new FCKeditor('s_body');				
				$this->editor->Width = "650";
				$this->editor->Height = "400";
				
				if (count($_POST) >	0) 
					{
					$this->editor->Value = $this->inputdata['s_body'];
					$this->inputdata['s_body'] = $this->editor->CreateHtml();	
					}
				else
					{
					$this->editor->Value = $this->displays['s_body'];
					$this->displays['s_body'] = $this->editor->CreateHtml();
					}
								
				}
				
				$this->mysmarty->assign('displays', $this->displays);
						
				$this->mysmarty->assign('inputdata', $this->inputdata);				
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);
				
				if ($this->seoexists) $this->mysmarty->assign('errorseo', 'This Url already exists. Urls must be unique');
				$this->mysmarty->view('mynavigation/mynavigation_edit.html');
				exit();
			}
			else 
			{		
								$this->db_data = array(	
										   	's_order' => (int)$this->form_validation->set_value('s_order'),
											's_link' => $this->form_validation->set_value('s_link'),
											's_body' => $this->form_validation->set_value('s_body'),
											's_menu' => (int)$this->session->userdata('menuid')
											);
					
								
											$this->db_data['s_title'] = $this->form_validation->set_value('s_title');
							
								if (strlen($this->_CleanSef($this->input->post('s_seourl', TRUE))) == 0) {
											$this->db_data['s_seourl'] = $this->_CleanSef($this->db_data['s_title']);
											} else {
											$this->db_data['s_seourl'] = $this->_CleanSef($this->form_validation->set_value('s_seourl'));
											}
							
								if (strlen($this->db_data['s_seourl']) < 3) $this->db_data['s_seourl'] = 'URL-'.$this->id; 
							
						$this->Mynavigation_model->Update($this->id, $this->db_data);
						
						redirect("/Mynavigation/Show/".$this->l1."/".$this->l2."/"); break; exit();									
			}
}

function Add($level1 = '', $level2 = '', $sm = '')
	{	
		$this->_CheckMenuID();
		
		$this->l1 = (int)$level1;
		$this->l2 = (int)$level2;
		if ($sm == 'Multiple') 
		{
			$this->multiple = TRUE;
			$this->mysmarty->assign('multiple', $this->multiple);
		}
		
		$this->_Levelize($this->l1,$this->l2);
		$this->displays = $this->Mynavigation_model->GetMaxOrder($this->l1,$this->l2);
		
		

		$this->load->library('form_validation');

					$this->form_validation->set_rules('s_order', 'Order', 'trim|required|xss_clean');
					$this->form_validation->set_rules('s_seourl', 'Sef Url', 'trim|min_length[3]|xss_clean');
					if (!isset($this->multiple)) {
						
						$this->form_validation->set_rules('s_body', 'Body', 'trim');						
						$this->form_validation->set_rules('s_link', 'Link', 'trim|xss_clean');
						$this->form_validation->set_rules('s_title', 'Title', 'trim|required|min_length[3]|xss_clean');
			
					} else {
						$this->form_validation->set_rules('s_title', 'Title', 'trim|required|min_length[3]|xss_clean');
					}
	   										$this->inputdata = array(										
													's_order' => $this->input->post('s_order', TRUE),
													's_body' => $this->input->post('s_body'),
													's_link' => $this->input->post('s_link', TRUE)
													);														
											
											$this->inputdata['s_title'] = $this->input->post('s_title', TRUE);
											

								if (strlen($this->_CleanSef($this->input->post('s_seourl', TRUE))) == 0) {
											$this->inputdata['s_seourl'] = $this->_CleanSef($this->inputdata['s_title']);
										    } else {
											$this->inputdata['s_seourl'] = $this->_CleanSef($this->input->post('s_seourl', TRUE));
											}
								$this->seoexists = $this->Mynavigation_model->CheckSurlExists($this->inputdata['s_seourl']);	
					
		if (($this->form_validation->run() == FALSE) || $this->seoexists)
			{	
			if (!isset($this->multiple)) {
				require_once($this->config->config['pathtopublic'].'/fckeditor/fckeditor.php');
				$this->editor = new FCKeditor('s_body');				
				$this->editor->Width = "650";
				$this->editor->Height = "400";
				
				if (count($_POST) >	0) 
					{
					$this->editor->Value = $this->inputdata['s_body'];
					$this->inputdata['s_body'] = $this->editor->CreateHtml();	
					}
				else
					{
					$this->editor->Value = '';
					$this->displays['s_body'] = $this->editor->CreateHtml();
					}
								
				}
				
				$this->mysmarty->assign('displays', $this->displays);			
			
				$this->mysmarty->assign('inputdata', $this->inputdata);				
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);
				
				if ($this->seoexists) $this->mysmarty->assign('errorseo', 'This Url already exists. Urls must be unique');
				$this->mysmarty->view('mynavigation/mynavigation_add.html');
				exit();
			}
			else 
			{		
								$this->db_data = array(	
										   	's_order' => (int)$this->form_validation->set_value('s_order'),
										   	's_body' => (int)$this->form_validation->set_value('s_body'),
										   	's_link' => (int)$this->form_validation->set_value('s_link'),
											's_visible' => 0,
 											's_menu' => (int)$this->session->userdata('menuid')
											);

								if (!isset($this->multiple)) {
										$this->db_data['s_type'] = 's';
									}
									else 
									{
										$this->db_data['s_type'] = 'm';
									}
								

											$this->db_data['s_title'] = $this->form_validation->set_value('s_title');
											$this->db_data['s_body'] = $this->form_validation->set_value('s_body');
											$this->db_data['s_link'] = $this->form_validation->set_value('s_link');
							
								if (strlen($this->_CleanSef($this->input->post('s_seourl', TRUE))) == 0) {
											$this->db_data['s_seourl'] = $this->_CleanSef($this->db_data['s_title']);
											} else {
											$this->db_data['s_seourl'] = $this->_CleanSef($this->form_validation->set_value('s_seourl'));
											}
											
										if ($this->l2 > 0) {
											$this->db_data['s_level'] = '2';
											$this->db_data['s_levelparentid'] = $this->l2;
										}
										elseif ($this->l1 > 0) {
											$this->db_data['s_level'] = '1';
											$this->db_data['s_levelparentid'] = $this->l1;
										}
										else {
											$this->db_data['s_level'] = '0';
											$this->db_data['s_levelparentid'] = '0';
										}

								if (strlen($this->db_data['s_seourl']) < 3) $this->db_data['s_seourl'] = 'URL-'.rand(1, 9999999); 

						$this->Mynavigation_model->Insert($this->db_data);
						
						redirect("/Mynavigation/Show/".$this->l1."/".$this->l2."/"); break; exit();								
			}
}
	
	
function _CleanSef ($string) {
	$this->inputstring = str_replace(" ", "-", $string);
	$this->inputstring = str_replace("_", "-", $this->inputstring);
	$this->cyrchars = array('А','Б','В','Г','Д','Е','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ч','Ц','Ш','Щ','Ъ','Ь','Ю','Я','а','б','в','г','д','е','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ч','ц','ш','щ','ъ','ь','ю','я');							 
	$this->latinchars = array('A','B','V','G','D','E','J','Z','I','I','K','L','M','N','O','P','R','S','T','U','F','H','CH','TS','SH','SHT','U','U','JU','YA','a','b','v','g','d','e','j','z','i','i','k','l','m','n','o','p','r','s','t','u','f','h','ch','ts','sh','sht','u','u','ju','ya');							 
	$this->inputstring = str_replace($this->cyrchars, $this->latinchars, $this->inputstring);	
	$this->inputstring = str_replace('---', '-', $this->inputstring);	
	$this->inputstring = str_replace('--', '-', $this->inputstring);
	$this->inputstring = ereg_replace("[^A-Za-z0-9\-]", "", $this->inputstring);		
	return $this->inputstring;
	}
	
function _StructureURL($str = '', $title = '') {	
		$this->seourl = $this->_CleanSef($str);
		if (strlen($this->seourl) > 3 )
		{
			return $this->seourl;
		}
		else
		{	
			$this->title = $this->_CleanSef($title);
			return $this->title;	
		}
	}
function _GetRepositoryTitle($id) {
		$this->reptitle = $this->Mynavigation_model->GetTitle((int)$id);
		return $this->reptitle;
}
function _Levelize($level1, $level2)
	{
		$this->l1 = (int)$level1;
		$this->l2 = (int)$level2;
		$this->_CheckCorrectLevels($this->l1,$this->l2);
		
		$this->mysmarty->assign('l1',$this->l1);
		$this->mysmarty->assign('l2',$this->l2);
		$this->session->set_flashdata('l1', $this->l1);
		$this->session->set_flashdata('l2', $this->l2);
		
		if ($this->l1 > 0)$this->mysmarty->assign('l1_title',$this->Mynavigation_model->GetTitle($this->l1));
		if ($this->l2 > 0)$this->mysmarty->assign('l2_title',$this->Mynavigation_model->GetTitle($this->l2));	
	}
	
function _CheckCorrectLevels($l1 = '',$l2 = '')
	{
	if (((int)$l1 == 0) && ((int)$l2 > 0))
			{
			Redirect("/Mynavigation/Show");
			break; exit();
			}	
	}

function _CheckMenuID() 
		{
		if (((int)$this->session->userdata('menuid') == 0) || ((int)$this->session->userdata('menuid') > 3)) {
			Redirect("/Mynavigation/");
			break;
			exit;		
		}	
		}
	
}
