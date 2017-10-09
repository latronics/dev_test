<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Home extends Controller {

	function Home()
	{
		parent::Controller();	
		$this->load->model('Menus_model');	
		$this->load->model('Product_model');	
		$this->StoreCart = $this->Product_model->GetStoreCart();
		$this->mysmarty->assign('StoreCart', $this->StoreCart);
		$this->Menus_model->GetStructure();		
		$this->Product_model->GetStructure();
		
		$this->mysmarty->assign('session',$this->session->userdata);
		
		if (isset($this->session->userdata['user_id'])) 
		{
			$this->load->model('Start_model');
			$this->load->model('Auth_model');
			$this->Auth_model->VerifyUser();
			$this->load->model('Myorders_model'); 
			$this->mysmarty->assign('statuses', $this->Myorders_model->GetStatuses());
			
		    $this->mysmarty->assign('myorders', $this->Product_model->ListMyOrders($this->session->userdata['email']));
		}
		
		
		if (isset($this->session->userdata['cart']) && ($this->session->userdata['cart'] != '') )
		{
			$this->mysmarty->assign('cartsession',$this->session->userdata['cart']);
		}
		
		$this->mysmarty->assign('session',$this->session->userdata);
	}
		
	function index() 
	{
		if ($this->StoreCart)
		{
		$this->newindex();
		exit();	
		}
		$get = $this->Product_model->GetEbayListings(0);
			$this->mysmarty->assign('ebay', $get['result']);
			$this->mysmarty->assign('pages', $get['pages']);
			$this->mysmarty->assign('page', 0);
			$this->mysmarty->assign('productview', 'ebaylisting');
			$this->mysmarty->assign('imgebaypath', $this->config->config['wwwpath']['imgebay']);			
			
			$this->mysmarty->view('welcome/welcome_main.html');
	}
	function newindex()
	{
		$this->db->where("notebay", 0);
		$this->db->orderby('listorder', 'ASC');
		$categories = $this->db->get("warehouse_sku_categories")->result_array();
		$this->mysmarty->assign('dbstore', $categories);
	
			$this->mysmarty->assign('ebay', $this->Product_model->GetLatestEbayListings());
			$this->mysmarty->assign('productview', 'updatedhome');
			$this->mysmarty->assign('imgebaypath', $this->config->config['wwwpath']['imgebay']);
			$this->mysmarty->view('welcome/welcome_main.html');
	
	}


}
