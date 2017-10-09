<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Mystart extends Controller {

	function Mystart()
	{
		parent::Controller();
		//LOAD SESSION
		$this->load->model('Myproducts_model');
		$this->mysmarty->assign('zero', $this->Myproducts_model->CheckWeightZero());

        $this->load->library('pagination');

		$this->load->model('Mystart_model'); 
		$this->load->model('Auth_model');
		$this->Auth_model->VerifyAdmin();

		$this->mysmarty->assign('session',$this->session->userdata);
		$this->mysmarty->assign('error_msg',$this->session->flashdata('error_msg'));
		$this->mysmarty->assign('success_msg',$this->session->flashdata('success_msg'));
		
		
		
	}
	function testget()
	{
	
	printcool ($_GET);
		
	}
	function index()
	{	
		$this->history =  $this->Mystart_model->GetHistory();
        $total_pages = 0;
        foreach($this->history['pages'] as $pages)
        {
            $total_pages = $pages;
        }

        $config['uri_segment'] = 3;
        $config['base_url'] = base_url() . 'Mystart/ShowHistory/';
        $config['display_pages'] = FALSE;
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul><!--pagination-->';
        $config['first_link'] = '&laquo; First';
        $config['first_tag_open'] = '<li class="prev page">';
        $config['first_tag_close'] = '</li>' . "\n";
        $config['last_link'] = 'Last &raquo;';
        $config['last_tag_open'] = '<li class="next page">';
        $config['last_tag_close'] = '</li>' . "\n";
        $config['next_link'] = 'Next &rarr;';
        $config['next_tag_open'] = '<li class="next page">';
        $config['next_tag_close'] = '</li>' . "\n";
        $config['prev_link'] = '&larr; Previous';
        $config['prev_tag_open'] = '<li class="prev page">';
        $config['prev_tag_close'] = '</li>' . "\n";
        $config['cur_tag_open'] = '<li class="active"><a href="">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li class="page">';
        $config['num_tag_close'] = '</li>' . "\n";
        $config['page_query_string'] = FALSE;

        $config['total_rows'] = $total_pages;

        $config['per_page'] = 1;
        $config['use_page_numbers'] = TRUE;


        $page_checker = $this->uri->segment(1);
        $this->mysmarty->assign('page_checker', $page_checker);

        $this->pagination->initialize($config);
        $this->mysmarty->assign('pagination', $this->pagination->create_links());
        $this->mysmarty->assign('total_rows', $total_pages * 200);
        $this->mysmarty->assign('total_pages', $total_pages);
		$this->mysmarty->assign('history', $this->history['results']);
		$this->mysmarty->assign('pages', $this->history['pages']);
		$this->mysmarty->assign('page', 0);
		$this->session->unset_userdata('page');
				
		$this->mysmarty->view('mystart/mystart_main.html');
	}
	
	function ShowHistory($page = '') 
	{
	    if($page == '')
        {
            $page = 1;
        }else
        {
            $page++;
        }
		$this->history =  $this->Mystart_model->GetHistory((int)$page);

        $total_pages = 0;
        foreach($this->history['pages'] as $pages)
        {
            $total_pages = $pages;
        }

        $config['uri_segment'] = 3;
        $config['base_url'] = base_url() . 'Mystart/ShowHistory/';
        $config['display_pages'] = FALSE;
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul><!--pagination-->';
        $config['first_link'] = '&laquo; First';
        $config['first_tag_open'] = '<li class="prev page">';
        $config['first_tag_close'] = '</li>' . "\n";
        $config['last_link'] = 'Last &raquo;';
        $config['last_tag_open'] = '<li class="next page">';
        $config['last_tag_close'] = '</li>' . "\n";
        $config['next_link'] = 'Next &rarr;';
        $config['next_tag_open'] = '<li class="next page">';
        $config['next_tag_close'] = '</li>' . "\n";
        $config['prev_link'] = '&larr; Previous';
        $config['prev_tag_open'] = '<li class="prev page">';
        $config['prev_tag_close'] = '</li>' . "\n";
        $config['cur_tag_open'] = '<li class="active"><a href="">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li class="page">';
        $config['num_tag_close'] = '</li>' . "\n";
        $config['page_query_string'] = FALSE;

        $config['total_rows'] = $total_pages;

        $config['per_page'] = 1;
        $config['use_page_numbers'] = TRUE;

        $this->pagination->initialize($config);
        $this->mysmarty->assign('pagination', $this->pagination->create_links());
        $this->mysmarty->assign('total_rows', $total_pages * 200);
        $this->mysmarty->assign('total_pages', $total_pages);

		$this->mysmarty->assign('history', $this->history['results']);
		$this->mysmarty->assign('pages', $this->history['pages']);
		$this->mysmarty->assign('page', (int)$page);
		$this->session->set_userdata('page', (int)$page);

		$this->mysmarty->assign('nosmallmenu', TRUE);
		$this->mysmarty->view('mystart/mystart_main.html');
		
	}
	
	function Problematic($page = 1) 
	{

        if($page == '')
        {
            $page = 1;
        }else
        {
            $page++;
        }
		$this->history =  $this->Mystart_model->GetHistory((int)$page, false);


        $total_pages = 0;
        foreach($this->history['pages'] as $pages)
        {
            $total_pages = $pages;
        }

        $config['uri_segment'] = 3;
        $config['base_url'] = base_url() . 'Mystart/Problematic/';
        $config['display_pages'] = FALSE;
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul><!--pagination-->';
        $config['first_link'] = '&laquo; First';
        $config['first_tag_open'] = '<li class="prev page">';
        $config['first_tag_close'] = '</li>' . "\n";
        $config['last_link'] = 'Last &raquo;';
        $config['last_tag_open'] = '<li class="next page">';
        $config['last_tag_close'] = '</li>' . "\n";
        $config['next_link'] = 'Next &rarr;';
        $config['next_tag_open'] = '<li class="next page">';
        $config['next_tag_close'] = '</li>' . "\n";
        $config['prev_link'] = '&larr; Previous';
        $config['prev_tag_open'] = '<li class="prev page">';
        $config['prev_tag_close'] = '</li>' . "\n";
        $config['cur_tag_open'] = '<li class="active"><a href="">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li class="page">';
        $config['num_tag_close'] = '</li>' . "\n";
        $config['page_query_string'] = FALSE;

        $config['total_rows'] = $total_pages;

        $config['per_page'] = 1;
        $config['use_page_numbers'] = TRUE;

        $this->pagination->initialize($config);
        $this->mysmarty->assign('pagination', $this->pagination->create_links());
        $this->mysmarty->assign('total_rows', $total_pages * 200);
        $this->mysmarty->assign('total_pages', $total_pages);
		$this->mysmarty->assign('history', $this->history['results']);
		$this->mysmarty->assign('pages', $this->history['pages']);
		$this->mysmarty->assign('page', (int)$page);
		$this->mysmarty->assign('problematic', TRUE);
		$this->session->set_userdata('page', (int)$page);

		$this->mysmarty->assign('nosmallmenu', TRUE);
		$this->mysmarty->view('mystart/mystart_main.html');
	}
	function NoListingBcns()
	{
		$this->history =  $this->Mystart_model->GetNoBcnHistory((int)$page, false);
		$this->mysmarty->assign('history', $this->history['results']);
		$this->mysmarty->assign('pages', $this->history['pages']);
		$this->mysmarty->assign('page', (int)$page);
		$this->mysmarty->assign('problematic', TRUE);
		$this->session->set_userdata('page', (int)$page);

		$this->mysmarty->assign('nosmallmenu', TRUE);
		$this->mysmarty->view('mystart/mystart_main.html');
		
	}
	function PurgeHistory()
	{
		if ($this->session->userdata['admin_id'] == 7) { echo 'Sorry, you don\'t have clearance for here.';exit();}	
		$this->Mystart_model->PurgeHistory();
		Redirect("/Mystart");
	}
	function DeleteOlderHistory()
	{	
		if ($this->session->userdata['admin_id'] == 7) { echo 'Sorry, you don\'t have clearance for here.';exit();}
		$this->days = (int)$this->input->post("days",TRUE);
		if ($this->days > 0)
		{
			$this->Mystart_model->DeleteOlderHistory($this->days);
		}
		Redirect("/Mystart");
	}
	
	function IpLogs($userid = '', $page = '')
	{	
		if ($this->session->userdata['admin_id'] == 7) { echo 'Sorry, you don\'t have clearance for here.';exit();}
		$this->mysmarty->assign('area', 'iplogs');
		$this->userid = (int)$userid;
		if ($this->userid == 0) $this->mysmarty->assign('allusers', TRUE);	
		$this->logdata = $this->Mystart_model->GetAllLogs($this->userid, (int)$page);
		$this->mysmarty->assign('iplogs', $this->logdata['results']);
		$this->mysmarty->assign('pages', $this->logdata['pages']);
		$this->mysmarty->assign('page', (int)$page);
		$this->userdetails = $this->Mystart_model->GetUserDetails($this->userid);
		$this->mysmarty->assign('userdetails', $this->userdetails);	
		$this->mysmarty->view('mystart/mystart_iplogs.html');
	}
	
	function PurgeIpLogs()
	{
		if ($this->session->userdata['admin_id'] == 7) { echo 'Sorry, you don\'t have clearance for here.';exit();}
		$this->Mystart_model->PurgeIpLogs();
		Redirect("/Mystart/IpLogs");
	}
	function DeleteOlderIpLogs()
	{	
		if ($this->session->userdata['admin_id'] == 7) { echo 'Sorry, you don\'t have clearance for here.';exit();}
		$this->days = (int)$this->input->post("days",TRUE);

		if ($this->days > 0)
		{
					
			$this->Mystart_model->DeleteOlderIpLogs($this->days);
		}
		Redirect("/Mystart/IpLogs");
	}
	
	function AdminLogs($adminid = '', $page = '')
	{
        if($page == '')
        {
            $page = 1;
        }else
        {
            $page++;
        }
		if ($this->session->userdata['admin_id'] == 7) { echo 'Sorry, you don\'t have clearance for here.';exit();}
		$this->mysmarty->assign('area', 'adminlogs');
		$this->adminid = (int)$adminid;
		if ($this->adminid == 0) $this->mysmarty->assign('alladmins', TRUE);
		$this->logdata = $this->Mystart_model->GetAllAdminLogs($this->adminid, (int)$page);

        $total_pages = 0;
        if($this->logdata != false)
        {
        foreach($this->logdata['pages'] as $pages)
        {
        $total_pages = $pages;
        }
        }
        $config['uri_segment'] = 4;
        $config['base_url'] = base_url() . 'Mystart/AdminLogs/0/';
        $config['display_pages'] = FALSE;
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul><!--pagination-->';
        $config['first_link'] = '&laquo; First';
        $config['first_tag_open'] = '<li class="prev page">';
        $config['first_tag_close'] = '</li>' . "\n";
        $config['last_link'] = 'Last &raquo;';
        $config['last_tag_open'] = '<li class="next page">';
        $config['last_tag_close'] = '</li>' . "\n";
        $config['next_link'] = 'Next &rarr;';
        $config['next_tag_open'] = '<li class="next page">';
        $config['next_tag_close'] = '</li>' . "\n";
        $config['prev_link'] = '&larr; Previous';
        $config['prev_tag_open'] = '<li class="prev page">';
        $config['prev_tag_close'] = '</li>' . "\n";
        $config['cur_tag_open'] = '<li class="active"><a href="">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li class="page">';
        $config['num_tag_close'] = '</li>' . "\n";
        $config['page_query_string'] = FALSE;

        $config['total_rows'] = $total_pages;

        $config['per_page'] = 1;
        $config['use_page_numbers'] = TRUE;

        $this->pagination->initialize($config);
        $this->mysmarty->assign('pagination', $this->pagination->create_links());
        $this->mysmarty->assign('total_rows', $total_pages * 200);
        $this->mysmarty->assign('total_pages', $total_pages);


		$this->mysmarty->assign('adminlogs', $this->logdata['results']);
		$this->mysmarty->assign('pages', $this->logdata['pages']);
		$this->mysmarty->assign('page', (int)$page);

        $page_checker = $this->uri->segment(2);
        $this->mysmarty->assign('page_checker', $page_checker);

		$this->admindetails = $this->Mystart_model->GetAdminDetails($this->adminid);
		$this->mysmarty->assign('admindetails', $this->admindetails);	
		$this->mysmarty->view('mystart/mystart_adminlogs.html');
	}
	
	function PurgeAdminLogs()
	{
		if ($this->session->userdata['admin_id'] == 7) { echo 'Sorry, you don\'t have clearance for here.';exit();}
		$this->Mystart_model->PurgeAdminLogs();
		Redirect("/Mystart/AdminLogs");
	}
	function DeleteOlderAdminLogs()
	{	
		if ($this->session->userdata['admin_id'] == 7) { echo 'Sorry, you don\'t have clearance for here.';exit();}
		$this->days = (int)$this->input->post("days",TRUE);
		if ($this->days > 0)
		{
			$this->Mystart_model->DeleteOlderAdminLogs($this->days);
		}
		Redirect("/Mystart/AdminLogs");
	}
	function RemoveImportant($id = '', $page = '')
	{
		$this->db->update('admin_history', array('code' => 0), array('msg_id' => (int)$id));
		Redirect("Mystart/ShowHistory/".$page.'#'.(int)$id);
	}
	function Dismiss($id = '', $page = '')
	{
		$this->db->update('admin_history', array('code' => 2), array('msg_id' => (int)$id));
		Redirect("Mystart/Problematic/".$page);
	}
	function Important($id = '', $page = '')
	{
		$this->db->update('admin_history', array('code' => 1), array('msg_id' => (int)$id));
		Redirect("Mystart/ShowHistory/".$page.'#'.(int)$id);
	}
	
	function Configure() {
		
		$this->mysmarty->assign('area', 'admindb');
		$admindbdata = $this->Mystart_model->FindAdminDetails((int)$this->session->userdata['admin_id']);


        $page_checker = $this->uri->segment(2);
        $this->mysmarty->assign('page_checker', $page_checker);
				$this->load->library('form_validation');
					$this->form_validation->set_message('matches', 'The Passwords do not mach!');
					$this->form_validation->set_rules('oldpass', 'Old Password', 'trim|required|min_length[5]|xss_clean|md5|md5');
					$this->form_validation->set_rules('newpass', 'New Password', 'trim|min_length[5]|xss_clean|matches[newpassretype]|md5|md5');
					$this->form_validation->set_rules('newpassretype', 'New Password Re-Type', 'trim|min_length[5]|matches[newpass]|xss_clean');
					$this->form_validation->set_rules('email', 'E-mail', 'trim|required|valid_email|xss_clean');
					$this->form_validation->set_rules('ownnames', 'Own Names', 'trim|required|min_length[2]|xss_clean');

				if ($this->form_validation->run() == FALSE)
				{					
					$this->mysmarty->assign('admindbdata', $admindbdata);	
					$this->mysmarty->assign('errors', $this->form_validation->_error_array);
					$this->mysmarty->view('mystart/mystart_configure.html');
				exit();
				}
				else 
				{
				$adminid = $this->Mystart_model->CheckPassMatch((int)$admindbdata['admin_id'], $this->form_validation->set_value('oldpass'));
				if ($adminid) 
					{
						if ($this->form_validation->set_value('newpass') != '') 
						{
							$this->Mystart_model->UpdateAllAdminDetails((int)$admindbdata['admin_id'], $this->form_validation->set_value('newpass'), $this->form_validation->set_value('email'), $this->form_validation->set_value('ownnames'));						
						}
						else 
						{
							$this->Mystart_model->UpdateAdminDetails((int)$admindbdata['admin_id'], $this->form_validation->set_value('email'), $this->form_validation->set_value('ownnames'));
						}
					$olddbdata = $admindbdata;
					$admindbdata = $this->Mystart_model->FindAdminDetails((int)$admindbdata['admin_id']);
						$this->load->helper('mailmsg');
						$this->historydata = MailAdminChangeDetailsAdmin($admindbdata,$olddbdata,CurrentTime(),$this->input->ip_address());
						$this->Mystart_model->InsertHistoryData($this->historydata);
					$this->mysmarty->assign('msgok', '<span style="color:#0F0">Success</span>');	
					$this->session->set_userdata('ownnames', $admindbdata['ownnames']);
					$this->session->set_userdata('email', $admindbdata['email']);
					$this->mysmarty->assign('session',$this->session->userdata);
					}
					else 
					{
					$errors['oldpass'] = 'Incorrect Password';
					$this->mysmarty->assign('errors', $errors);	
						
					}
				}


		$this->mysmarty->assign('admindbdata', $admindbdata);	
		$this->mysmarty->view('mystart/mystart_configure.html');
	}
	function DeadEnds()
	{	
		if ($this->session->userdata['admin_id'] == 7) { echo 'Sorry, you don\'t have clearance for here.';exit();}
		$this->dedata = $this->Mystart_model->GetAllDeadEnds();
		$this->mysmarty->assign('deadends', $this->dedata);
		$this->mysmarty->view('mystart/mystart_deadends.html');
	}
	
	function PurgeDeadEnds()
	{
		if ($this->session->userdata['admin_id'] == 7) { echo 'Sorry, you don\'t have clearance for here.';exit();}
		$this->Mystart_model->PurgeDeadEnds();
		Redirect("/Mystart/DeadEnds");
	}
	function DeleteOlderDeadEnds()
	{	
		if ($this->session->userdata['admin_id'] == 7) { echo 'Sorry, you don\'t have clearance for here.';exit();}
		$this->days = (int)$this->input->post("days",TRUE);
		if ($this->days > 0)
		{
			$this->Mystart_model->DeleteOlderDeadEnds($this->days);
		}
		Redirect("/Mystart/DeadEnds");
	}
	function Cleanup()
	{
		//$this->db->like('msg_title', 'Google Spreadsheet Que Insert');
		//$this->db->like('msg_title', 'Google Spreadsheet Update');
		$this->db->like('msg_title', 'Insert GS Empty BCN Array');
		
		$this->query = $this->db->get('admin_history');
		if ($this->query->num_rows() > 0) 
		{		
		 $res = $this->query->result_array();
		 printcool ($res);
		 foreach ($res as $k => $v)
		 {
		 	//$this->db->where('msg_id', (int)$v['msg_id']);
			//$this->db->delete('admin_history'); 
		 }
		 
		 
		}
	}
	
}
