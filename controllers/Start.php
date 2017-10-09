<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Start extends Controller {

	function Start()
	{	
		//LOAD SESSION
		parent::Controller();
		$this->load->model('Menus_model');	
		$this->load->model('Product_model');	
		$this->Menus_model->DoTracking();
		$this->Menus_model->GetStructure();		
		$this->Product_model->GetStructure('top');
		
			$this->load->model('Start_model');
			$this->load->model('Auth_model');
			$this->Auth_model->VerifyUser();
			$this->load->model('Myorders_model'); 
			$this->mysmarty->assign('statuses', $this->Myorders_model->GetStatuses());			
		    $this->mysmarty->assign('myorders', $this->Product_model->ListMyOrders($this->session->userdata['email']));
			$this->mysmarty->assign('session',$this->session->userdata);

			if (isset($this->session->userdata['cart']) && ($this->session->userdata['cart'] != '') )
				{
				$this->mysmarty->assign('cartsession',$this->session->userdata['cart']);
				$this->mysmarty->assign('carttotal', $this->_CartTotal());
				}	
	}
	
	function index()
	{	
	$this->mysmarty->assign('innerview', 'usermain');
	$this->mysmarty->view('welcome/welcome_main.html');
//	$this->mysmarty->view('start/start_main.html');
	}
	
	function Configure() {
	
		$userdbdata = $this->Start_model->GetUserDetails((int)$this->session->userdata['user_id']);
		
					$this->mysmarty->assign('ctr', ReturnCountries($this->config->config['language_abbr']));
					$this->mysmarty->assign('sts', ReturnStates());
					
					$this->load->library('form_validation');
					$this->form_validation->set_message('matches', 'The Passwords do not mach!');
					$this->form_validation->set_rules('oldpass', 'Old Password', 'trim|required|min_length[5]|xss_clean|md5|md5');
					$this->form_validation->set_rules('Password', 'New Password', 'trim|min_length[5]|xss_clean|matches[PasswordRetype]|md5|md5');
					$this->form_validation->set_rules('PasswordRetype', 'New Password Re-Type', 'trim|min_length[5]|matches[Password]|xss_clean');
					
					$this->form_validation->set_rules('FirstName', 'First Name', 'trim|required|min_length[3]|xss_clean');
					$this->form_validation->set_rules('LastName', 'Last Name', 'trim|required|min_length[3]|xss_clean');
					$this->form_validation->set_rules('Email', 'E-Mail', 'trim|required|valid_email|xss_clean');
					
					$this->form_validation->set_rules('Telephone', 'Telephone', 'trim|required|min_length[3]|xss_clean');
					$this->form_validation->set_rules('Mobile', 'Mobile', 'trim|min_length[3]|xss_clean');
					
					$this->form_validation->set_rules('Address', 'Address', 'trim|required|min_length[5]|xss_clean');
					$this->form_validation->set_rules('City', 'City', 'trim|required|min_length[3]|xss_clean');
					$this->form_validation->set_rules('PostCode', 'PostCode', 'trim|required|min_length[2]|xss_clean');
					$this->form_validation->set_rules('State', 'State', 'trim|required|xss_clean');
					$this->form_validation->set_rules('Country', 'Country', 'trim|required|min_length[3]|xss_clean');
					
					if (!isset($_POST['same'])) {
						
					$this->form_validation->set_rules('dAddress', 'Address', 'trim|required|min_length[5]|xss_clean');
					$this->form_validation->set_rules('dCity', 'City', 'trim|required|min_length[3]|xss_clean');
					$this->form_validation->set_rules('dPostCode', 'PostCode', 'trim|required|min_length[2]|xss_clean');
					$this->form_validation->set_rules('dState', 'State', 'trim|required|xss_clean');
					$this->form_validation->set_rules('dCountry', 'Country', 'trim|required|min_length[3]|xss_clean');
		
		}

				if ($this->form_validation->run() == FALSE)
				{					
					$this->mysmarty->assign('regdata', $userdbdata);	
					$this->mysmarty->assign('errors', $this->form_validation->_error_array);
					$this->mysmarty->assign('innerview', 'userconfigure');
					$this->mysmarty->view('welcome/welcome_main.html');
					
				exit();
				}
				else 
				{
				$userid = $this->Start_model->CheckPassMatch((int)$userdbdata['user_id'], $this->form_validation->set_value('oldpass'));
				
					if ($this->form_validation->set_value('Email') != $userdbdata['Email']) 
						{
						$this->load->model('Login_model');
						$this->checkemail = $this->Login_model->CheckEmailExists($this->form_validation->set_value('Email'));
						}
					else 
						{
						$this->checkemail = false;
						}

				if (($userid) && (!$this->checkemail))
					{
								$this->details_data = array(											
											 'Telephone' => si($this->form_validation->set_value('Telephone')),	
											 'Mobile' => si($this->form_validation->set_value('Mobile'))
											 
											 );
														
								$this->newuserdata = array(
													 'fname' => si($this->form_validation->set_value('FirstName')),
													 'lname' => si($this->form_validation->set_value('LastName')),
													 'email' => si($this->form_validation->set_value('Email')),	
													 'details' => serialize($this->details_data)
													 );
								if ($this->form_validation->set_value('Password') != '') 
								{
								$this->newuserdata['pass'] = $this->form_validation->set_value('Password');
								}

						
						$this->Start_model->UpdateUserDetails((int)$userdbdata['user_id'], $this->newuserdata);
						
						$this->addressdata = array ('user_id' => (int)$userdbdata['user_id'],
													'ua_type' => 'b',
													'Address' => si($this->form_validation->set_value('Address')),
													'City' => si($this->form_validation->set_value('City')),
													'PostCode' => si($this->form_validation->set_value('PostCode')),
													'Country' => si($this->form_validation->set_value('Country')),
													'State' => si($this->form_validation->set_value('State')),
													);

						$this->Start_model->UpdateUserAddress((int)$userdbdata['user_id'],$this->addressdata);
						if (!isset($_POST['same']) || $_POST['same'] != '1') {
							
						$this->daddressdata = array (
													 'user_id' => (int)$userdbdata['user_id'],
													'ua_type' => 'd',
													'Address' => si($this->form_validation->set_value('dAddress')),
													'City' => si($this->form_validation->set_value('dCity')),
													'PostCode' => si($this->form_validation->set_value('dPostCode')),
													'Country' => si($this->form_validation->set_value('dCountry')),
													'State' => si($this->form_validation->set_value('dState')),
													);
						
						$this->load->model('Login_model');
						//$this->Start_model->SameDeliveryAddress((int)$userdbdata['user_id']);
						$this->db->where('user_id', (int)$userdbdata['user_id']);
						$this->db->where('ua_type', 'd');
						$this->db->delete('users_addresses'); 
						$this->Login_model->InsertUserAddress($this->daddressdata);	
						}
						else
						{
						$this->Start_model->SameDeliveryAddress((int)$userdbdata['user_id']);
						}

					$olddbdata = $userdbdata;
					$userdbdata = $this->Start_model->GetUserDetails((int)$userdbdata['user_id']);
						foreach ($userdbdata as $key => $value) 
							{
							if ($key == 'telephone') $userdbdata['tel'] = $value;				
							else $userdbdata[$key] = $value;
							}
							$this->mysmarty->assign('regdata', $userdbdata);

						$this->load->helper('mailmsg');
						$this->historydata = MailUserChangeDetailsAdmin($userdbdata,$olddbdata,CurrentTime(),$this->input->ip_address());
						$this->Start_model->InsertHistoryData($this->historydata);
						
					$this->mysmarty->assign('msgok', '<div style=" height:20px; line-height:20px; vertical-align:middle; width: 150px; text-align:center; background:#0F0; color:#fff; font-size:14px; font-weight:bold;">Success</div>');
					$this->session->set_userdata('fname', $userdbdata['FirstName']);
					$this->session->set_userdata('lname', $userdbdata['LastName']);
					$this->session->set_userdata('email', $userdbdata['Email']);
					
					$this->mysmarty->assign('session',$this->session->userdata);
					
					$userdbdata = $this->Start_model->GetUserDetails((int)$this->session->userdata['user_id']);
					
					}
					else 
					{
						if (!$userid) $errors['oldpass'] = 'Incorrect Password';
						if ($this->checkemail) $errors['email'] = 'E-Mail Address is already registered';
					$this->mysmarty->assign('errors', $errors);	
						
					}
				}	
		$this->mysmarty->assign('regdata', $userdbdata);
		
		$this->mysmarty->assign('innerview', 'userconfigure');
		$this->mysmarty->view('welcome/welcome_main.html');
		
		//$this->mysmarty->view('start/start_configure.html');
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
//PRIVATE FUNCTIONS
	
	function _CartTotal() 
	{
	$this->data = $this->session->userdata['cart'];
	$total = 0;
	foreach ($this->data as $key => $value) 
		{
		$total = $total + ((int)$value['quantity'] * (float)$value['p_price']);
		}
	return (float)$total;
	}
	
function _MatchDBUserdataToSession($userdbdata) 
	{
			$match = 0;
				foreach ($userdbdata as $key=>$value) 
				{
					if ($value != $this->session->userdata[$key]) $match++;
				}
				if ((int)$match > 0) 
					{
					$this->session->sess_destroy();
					Redirect("/");
					exit;
					}
	}



// END PRIVATE FUNCTIONS

}
