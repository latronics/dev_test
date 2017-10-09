<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Products extends Controller {
function Products()
{
		parent::Controller();
		$this->load->model('Menus_model');
		$this->load->model('Product_model');

		$this->Menus_model->GetStructure();
		$this->Product_model->GetStructure();
		$this->mysmarty->assign('session',$this->session->userdata);
		$this->StoreCart = $this->Product_model->GetStoreCart();
		$this->mysmarty->assign('StoreCart', $this->StoreCart);
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
	$this->mysmarty->assign('products', $this->Product_model->GetProductList());
	$this->mysmarty->assign('productview', 'main');
	$this->mysmarty->view('welcome/welcome_main.html');
}
/*
function ShowSpecial($pos = '')
	{
		$this->posntn = (int)$pos;
		if (($this->posntn == 0) || ($this->posntn > 4)) $this->posntn = 1;
		$this->specials = $this->Product_model->GetTopSpecials();
		$this->ShowProduct($this->specials[$this->posntn-1]['p_sef']);
	}*/

function RedirectShowEbayListings($page = 0)
{
	 Redirect('Ebay/'.(int)$page);
}
function ShowEbayListings($page = 0)
	{
		if ($this->StoreCart)
		{
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: ".$this->config->config['base_url']);
			exit();
		}
			$get = $this->Product_model->GetEbayListings((int)$page);
			$this->mysmarty->assign('ebay', $get['result']);
			$this->mysmarty->assign('pages', $get['pages']);
			$this->mysmarty->assign('page', (int)$page);
			$this->mysmarty->assign('productview', 'ebaylisting');
			$this->mysmarty->assign('imgebaypath', $this->config->config['wwwpath']['imgebay']);

			$this->mysmarty->view('welcome/welcome_main.html');
	}

function nShowEbayListings($page = 0)
	{
		if ($this->StoreCart)
		{
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: ".$this->config->config['base_url']);
			exit();
		}
			$get = $this->Product_model->GetEbayListings((int)$page);
			$this->mysmarty->assign('ebay', $get['result']);
			$this->mysmarty->assign('pages', $get['pages']);
			$this->mysmarty->assign('page', (int)$page);
			$this->mysmarty->assign('productview', 'ebaylisting');
			$this->mysmarty->assign('imgebaypath', $this->config->config['wwwpath']['imgebay']);

			$this->mysmarty->view('welcome/welcome_main_new.html');
	}

function RedirectShowEbayListing($itemid = '')
{
	 Redirect('EbayItem/'.(int)$itemid);
}
function ShowEbayListing($itemid = '')
	{
		if ($this->StoreCart)
		{
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: ".$this->config->config['base_url']."/storeitem/".$itemid);
			exit();
		}
		$this->id = (int)$itemid;
		if ($this->id == 0)	Redirect('');
		$this->load->model('Myebay_model');
		$this->displays = $this->Myebay_model->GetItem($this->id);
		$this->load->model('Settings_model');
		$this->Settings_model->GetEbayListingAddress();
		$this->mysmarty->assign('displays', $this->displays);
		$this->mysmarty->assign('productview', 'showebaylisting');
		$this->mysmarty->assign('imgebaypath', $this->config->config['wwwpath']['imgebay']);
		$this->mysmarty->view('welcome/welcome_main.html');
	}
function nShowEbayListing($itemid = '')
	{
		if ($this->StoreCart)
		{
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: ".$this->config->config['base_url']."/storeitem/".$itemid);
			exit();
		}
		$this->id = (int)$itemid;
		if ($this->id == 0)	Redirect('');
		$this->load->model('Myebay_model');
		$this->displays = $this->Myebay_model->GetItem($this->id);
		$this->load->model('Settings_model');
		$this->Settings_model->GetEbayListingAddress();
		$this->mysmarty->assign('displays', $this->displays);
		$this->mysmarty->assign('similar', $this->Myebay_model->GetSimilar($this->displays['storeCatID'], $this->id));
		$this->mysmarty->assign('productview', 'showebaylisting');
		$this->mysmarty->assign('imgebaypath', $this->config->config['wwwpath']['imgebay']);
		$this->mysmarty->view('welcome/welcome_main_new.html');
	}
function NewShowEbayListing($itemid = '')
	{
		$this->id = (int)$itemid;
		if ($this->id == 0)	Redirect('');
		$this->load->model('Myebay_model');
		$this->displays = $this->Myebay_model->GetItem($this->id);
		$this->load->model('Settings_model');
		$this->Settings_model->GetEbayListingAddress();
		$this->mysmarty->assign('displays', $this->displays);
		$this->mysmarty->assign('similar', $this->Myebay_model->GetSimilar($this->displays['storeCatID'], $this->id));
		$this->mysmarty->assign('productview', 'newshowebaylisting');
		$this->mysmarty->assign('imgebaypath', $this->config->config['wwwpath']['imgebay']);
		$this->mysmarty->view('welcome/welcome_main.html');
	}
    function nNewShowEbayListing($itemid = '')
    {



        $this->id = (int)$itemid;
        if ($this->id == 0)	Redirect('');
        $this->load->model('Myebay_model');
        $this->displays = $this->Myebay_model->GetItem($this->id);
        $this->load->model('Settings_model');
        $this->Settings_model->GetEbayListingAddress();


        //GET DATA TO CREATE CATEGORY HORIZONTAL MENU
        //GET STORECATID FROM PRODUCT
        $this->db->where("e_id", $this->id);
        $product_data = $this->db->get("ebay")->result_object();

        $this->db->where("wsc_id", $product_data[0]->storeCatID);
        $cat_tree_top = $this->db->get("warehouse_sku_categories")->result_array();
        $dad_id = 0;
        $dad_title = 0;
        if($cat_tree_top[0]['wsc_parent'] != 0)
        {
            $this->db->where("wsc_id", $cat_tree_top[0]['wsc_parent']);
            $dad_data = $this->db->get("warehouse_sku_categories")->result_object();
            $dad_id = $dad_data[0]->wsc_id;
            $dad_title = $dad_data[0]->wsc_title;
        }

        $this->mysmarty->assign('dad_title', $dad_title);
        $this->mysmarty->assign('dad_id', $dad_id);
        $this->mysmarty->assign('displays', $this->displays);
        $this->mysmarty->assign('similar', $this->Myebay_model->GetSimilar($this->displays['storeCatID'], $this->id));
        $this->mysmarty->assign('productview', 'showebaylisting');
        $this->mysmarty->assign('imgebaypath', $this->config->config['wwwpath']['imgebay']);
        $this->mysmarty->view('welcome/welcome_main.html');
    }
function ShowEbayImages($sef = '', $image = '')
	{

	//header('Location: '.$_SERVER['HTTP_REFERER']);
	//exit();

		$this->load->model('Product_model');
		$this->sefstring = CleanInput($sef);

		if ($sef == 'Sony-CDX-GT565UP-In-Dash-Radio-Receiver-Pandora-Sirius-USB-AUX-MISSING-ITEMS')
		$this->sefstring = 'Sony-CDX-GT565UP-In-Dash-Radio-CDMP3WMA-AMFM-Player-Pandora-Sirius-USB-AUX';

		if ($sef == 'Sony-CDX-GT565UP-In-Dash-Radio-CDMP3WMA-AMFM-Player-Pandora-Sirius-USB-AUX')
		$this->sefstring = 'Sony-CDX-GT565UP-In-Dash-Radio-CDMP3WMA-AMFM-Car-Stereo-Receiver-USBAUX-In';

		if ($sef == 'HP-Compaq-NX9420-171quot-LCD-Back-Cover-APZKF000C00-Grade-C')
		$this->sefstring = 'HP-Compaq-NX9420-171-LCD-Back-Cover-APZKF000C00-Grade-C';

		if ($sef == 'Samsung-300E-NP300E5C-156quot-Matte-LED-LCD-Screen-N156BGE-L11-Tested')
		$this->sefstring = 'Samsung-300E-NP300E5C-156-Matte-LED-LCD-Screen-N156BGE-L11-Tested';

		if ($sef == 'Toshiba-Satellite-A105-154quot-WXGA-Glossy-Screen-LP154W01-TL-B5-Tested')
		$this->sefstring = 'Toshiba-Satellite-A105-154-WXGA-Glossy-Screen-LP154W01-TL-B5-Tested';




		$this->imagepos = (int)$image;
		if (($this->imagepos == 0) || ($this->imagepos > 4)) $this->imagepos = 1;

		$this->gallery = $this->Product_model->GetEbayGallery($this->sefstring);

		$picname = 'e_img'.$this->imagepos;
		header('Location: '.Site_url().$this->config->config['wwwpath']['imgebay'].'/'.idpath($this->gallery['e_id']).$this->gallery[$picname]);

		exit();


			if ($this->gallery['e_img1'] != '') $this->gallery['size']['e_img1'] = getimagesize($this->config->config['paths']['imgebay'].'/'.idpath($this->gallery['e_id']).$this->gallery['e_img1']);
			if ($this->gallery['e_img2'] != '') $this->gallery['size']['e_img2'] = getimagesize($this->config->config['paths']['imgebay'].'/'.idpath($this->gallery['e_id']).$this->gallery['e_img2']);
			if ($this->gallery['e_img3'] != '') $this->gallery['size']['e_img3'] = getimagesize($this->config->config['paths']['imgebay'].'/'.idpath($this->gallery['e_id']).$this->gallery['e_img3']);
			if ($this->gallery['e_img4'] != '') $this->gallery['size']['e_img4'] = getimagesize($this->config->config['paths']['imgebay'].'/'.idpath($this->gallery['e_id']).$this->gallery['e_img4']);

		if ($this->gallery)
		{
			if (($this->imagepos == 2) && ($this->gallery['e_img2'] == '')) $this->imagepos = 1;
			if (($this->imagepos == 3) && ($this->gallery['e_img3'] == '')) $this->imagepos = 1;
			if (($this->imagepos == 4) && ($this->gallery['e_img4'] == '')) $this->imagepos = 1;

				switch($this->imagepos)
				{
					case 2:
					 $this->imageorder = array('e_img2','e_img1','e_img3','e_img4');
					 $this->urlorder = array(2,1,3,4);
					break;
					case 3:
					 $this->imageorder = array('e_img3','e_img1','e_img2','e_img4');
					 $this->urlorder = array(3,1,2,4);
					break;
					case 4:
					 $this->imageorder = array('e_img4','e_img1','e_img2','e_img3');
					 $this->urlorder = array(4,1,2,3);
					break;
					default:
					 $this->imageorder = array('e_img1','e_img2','e_img3','e_img4');
					 $this->urlorder = array(1,2,3,4);
				}

			$this->mysmarty->assign('images', $this->gallery);
			$this->mysmarty->assign('sef', $this->sefstring);
			$this->mysmarty->assign('imageorder', $this->imageorder);
			$this->mysmarty->assign('urlorder', $this->urlorder);
			$this->mysmarty->assign('imgebaypath', $this->config->config['wwwpath']['imgebay']);
			$this->mysmarty->assign('productview', 'ebayimg');
			$this->mysmarty->view('welcome/welcome_main.html');
			exit();
		}
		Redirect ('');
	}

	/*
function RequestForm($type)
{
	$this->load->library('form_validation');

	$this->form_validation->set_rules('fname', 'First Name', 'trim|required|min_length[2]|xss_clean');
	$this->form_validation->set_rules('lname', 'Last Name', 'trim|required|min_length[2]|xss_clean');
	$this->form_validation->set_rules('tel', 'Telephone', 'trim|required|min_length[5]|xss_clean');
	$this->form_validation->set_rules('email', 'E-mail', 'trim|required|valid_email|xss_clean');
	$this->form_validation->set_rules('brand', 'Laptop/Device Brand', 'trim|required|xss_clean');
	$this->form_validation->set_rules('model', 'Laptop/Device Model', 'trim|required|xss_clean');
	if ($type == 'Part')  $this->form_validation->set_rules('item', 'Part / Part Number', 'trim|required|xss_clean');
	else $this->form_validation->set_rules('item', 'Problem Description', 'trim|required|xss_clean');


				  $this->inputdata = array(
										   'fname' => $this->input->post('fname', TRUE),
										   'lname' => $this->input->post('lname', TRUE),
										   'tel' => $this->input->post('tel', TRUE),
   										   'email' => $this->input->post('email', TRUE),
										   'brand' => $this->input->post('brand', TRUE),
										   'model' => $this->input->post('model', TRUE),
										   'item' => $this->input->post('item', TRUE)
										   );

			if ((!$_POST) && isset($this->session->userdata['user_id'])) {
					$userdbdata = $this->Start_model->GetUserDetails((int)$this->session->userdata['user_id']);
					 $this->inputdata['fname'] = $userdbdata['FirstName'];
					 $this->inputdata['lname'] = $userdbdata['LastName'];
					 $this->inputdata['tel'] = $userdbdata['Telephone'];
					 $this->inputdata['email'] = $userdbdata['Email'];
						}

		 if ($this->form_validation->run() == FALSE)
			{
				$this->mysmarty->assign('inputdata', $this->inputdata);
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);
				if ($type == 'Part') $this->mysmarty->assign('productview', 'partform');
				else $this->mysmarty->assign('productview', 'estform');
				$this->mysmarty->view('welcome/welcome_main.html');
				exit();
			}
			else
			{

				$this->insertdata = array(
											 'date' => CurrentTime(),
											 'fname' => $this->form_validation->set_value('fname'),
										 	 'lname' => $this->form_validation->set_value('lname'),
										 	 'tel' => $this->form_validation->set_value('tel'),
   										 	 'email' => $this->form_validation->set_value('email'),
										 	 'brand' => $this->form_validation->set_value('brand'),
										 	 'model' => $this->form_validation->set_value('model'),
										     'item' => $this->form_validation->set_value('item')
											  );

				if ($type == 'Part')
				{
					$this->insertdata['type'] = 2;
				}
				else
				{
					$this->insertdata['type'] = 1;
					$this->load->helper('arithmetic');
					$this->insertdata['code'] = rand_string(50);

				}
				$this->Product_model->InsertRequestForm($this->insertdata);
				$msgdata = $this->_InquiryForm($type);
				$this->load->model('Login_model');
				$this->Login_model->InsertHistoryData($msgdata);
				Redirect('/ThankYou');
			}

}


function ProductsList($catseftitle = '')
{
		$this->sefstring = CleanInput($catseftitle);
		$this->category = $this->Product_model->GetCategoryData($this->sefstring);
		if (!$this->category) Redirect ('');
		$this->subcategory = $this->Product_model->GetProductCategories((int)$this->category['p_catid']);
		$this->mysmarty->assign('sc', $this->subcategory);

		$this->productlist = $this->Product_model->GetProductList((int)$this->category['p_catid']);
		$this->mysmarty->assign('productlist', $this->productlist);
		$this->mysmarty->assign('productcategory', $this->category);
		$this->mysmarty->assign('imgproductpath', $this->config->config['wwwpath']['imgproducts']);
		$this->mysmarty->assign('productview', 'list');
		$this->mysmarty->view('welcome/welcome_main.html');

		if (isset($this->session->userdata['noadd'])) $this->session->unset_userdata('noadd');
		if (isset($this->session->userdata['unregnoadd'])) $this->session->unset_userdata('unregnoadd');

}
function ShowProduct($prodseftitle)
{

		$this->load->model('Product_model');
		$this->sefstring = CleanInput($prodseftitle);
		$this->product = $this->Product_model->GetProduct($this->sefstring);
		if ($this->product)
		{
			$this->category =  $this->Product_model->GetCategoryData('', $this->product['p_cat']);
			$this->product['p_catid'] = $this->product['p_cat'];
			$this->product['p_cat'] = $this->category['p_cattitle'];
			$this->product['p_seftitle'] = $this->category['p_sef'];
			$this->mysmarty->assign('product', $this->product);
			$this->mysmarty->assign('productcategory', $this->category);
			$this->mysmarty->assign('imgproductpath', $this->config->config['wwwpath']['imgproducts']);

			$this->mysmarty->assign('productview', 'view');

			if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['tel']))
			{
				$this->load->library('form_validation');
				$this->form_validation->set_rules('name', 'Names', 'trim|required|min_length[5]|xss_clean');
				$this->form_validation->set_rules('tel', 'Telephone', 'trim|required|min_length[5]|xss_clean');
				$this->form_validation->set_rules('email', 'Email', 'trim|required|min_length[7]|valid_email|xss_clean');

				if ($this->form_validation->run() == FALSE)
					{
						$this->inputdata = array(
										 'name' => $this->input->post('name', TRUE),
										 'email' => $this->input->post('email', TRUE),
										 'tel' => $this->input->post('tel', TRUE)
										 );

						$this->mysmarty->assign('form', 'show');
						$this->mysmarty->assign('inputdata', $this->inputdata);
						$this->mysmarty->assign('errors', $this->form_validation->_error_array);
					}
					else
					{
						$this->formdata = array (
											'names' => $this->form_validation->set_value('name'),
											'email' => $this->form_validation->set_value('email'),
											'tel' => $this->form_validation->set_value('tel')
											 );
						$this->mysmarty->assign('form', 'ok');

						$this->maildata['msg_date'] = CurrentTime();
						$this->maildata['msg_title'] = 'Quantity unavailable for purchase  @ '.FlipDateMail($this->maildata['msg_date']);
						$this->maildata['msg_body'] = '
						Site visitor '.$this->formdata['names'].' attempted to purchase '.(int)$this->session->userdata['unregnoaddquantity'].' from '.$this->product['p_quantity'].' available quantity of product <a href="'.Site_url().'Myproducts/Edit/'.$this->product['p_id'].'" target="_blank" style="text-decoration:underline; color:blue;">'.$this->sefstring.'</a>.<br><br>
						Email: '.$this->formdata['email'].' - Telephone: '.$this->formdata['tel'].'
						';

						if (isset($this->session->userdata['unregnoaddquantity'])) $this->session->unset_userdata('unregnoaddquantity');

						$this->mailid = 14;
						GoMail ($this->maildata);
						$this->load->model('Login_model');
						$this->Login_model->InsertHistoryData($this->maildata);
					}
			}

			$this->mysmarty->view('welcome/welcome_main.html');

			if (isset($this->session->userdata['noadd'])) $this->session->unset_userdata('noadd');
			if (isset($this->session->userdata['unregnoadd'])) $this->session->unset_userdata('unregnoadd');

			exit();
		}
		redirect("");
}

function SolutionsList()
{
		$this->solutionlist = $this->Product_model->GetSolutionsList();
		$this->mysmarty->assign('solutionslist', $this->solutionlist);
		$this->mysmarty->assign('imgproductpath', $this->config->config['wwwpath']['imgproducts']);
		$this->mysmarty->assign('productview', 'solutionslist');
		$this->mysmarty->view('welcome/welcome_main.html');
}
function ShowSolution($id = '')
{
		$this->solution = $this->Product_model->GetSolution((int)$id);
		if ($this->solution)
		{
			$this->productlist = $this->Product_model->GetSolutionProducts((int)$this->solution['sid']);
			$this->mysmarty->assign('productlist', $this->productlist);
			$this->mysmarty->assign('solution', $this->solution);
			$this->mysmarty->assign('imgproductpath', $this->config->config['wwwpath']['imgproducts']);
			$this->mysmarty->assign('productview', 'solutionview');
			$this->mysmarty->view('welcome/welcome_main.html');
			exit();
		}
		redirect("");
}

function DIYList()
{
		$this->diylist = $this->Product_model->GetDIYList();
		$this->mysmarty->assign('diylist', $this->diylist);
		$this->mysmarty->assign('productview', 'diylist');
		$this->mysmarty->view('welcome/welcome_main.html');
}
function ShowDIY($id = '')
{
		$this->diy = $this->Product_model->GetDIY((int)$id);
		if ($this->diy)
		{
			$this->mysmarty->assign('diy', $this->diy);
			$this->mysmarty->assign('productview', 'diyview');
			$this->mysmarty->view('welcome/welcome_main.html');
			exit();
		}
		redirect("");
}

function Whitepapers()
{
		$this->whitepapers = $this->Product_model->GetWhitePapersList();
		$this->mysmarty->assign('whitepaperslist', $this->whitepapers);
		$this->mysmarty->assign('path', $this->config->config['wwwpath']['fileswhitepapers']);
		$this->mysmarty->assign('productview', 'whitepaperslist');
		$this->mysmarty->view('welcome/welcome_main.html');
}
function DownloadWhitePaper($id = '')
{
	if ((int)$id > 0)
	{
		if (isset($this->session->userdata['user_id']))
		{
			$this->whitepaper = $this->Product_model->GetWhitePapersFile((int)$id);
			$this->filelocation = file_get_contents($this->config->config['wwwpath']['fileswhitepapers']."/".$this->whitepaper); // Read the file's contents
			$this->load->helper('download');
			force_download($this->whitepaper, $this->filelocation );
		}
		else
		{
			$this->whitepapers = $this->Product_model->GetWhitePapersList();
			$this->mysmarty->assign('whitepaperslist', $this->whitepapers);
			$this->mysmarty->assign('path', $this->config->config['wwwpath']['fileswhitepapers']);
			$this->mysmarty->assign('productview', 'whitepaperslist');
			$this->mysmarty->assign('error_msg','Трябва да сте <a href="'.site_url().'Login/Register" class="title">регистриран</a> потребител и да сте <a href="'.site_url().'Login" class="title">влезли в сисемата</a> за да свалите файл.');
			$this->mysmarty->view('welcome/welcome_main.html');
		}
	}
}
function Partners()
{
	$this->partners = $this->Product_model->GetPartners();
	$this->mysmarty->assign('partners', $this->partners);
	$this->mysmarty->assign('path', $this->config->config['wwwpath']['imgpartners']);
	$this->mysmarty->assign('productview', 'partnerslist');
	$this->mysmarty->view('welcome/welcome_main.html');
}

//


function _InquiryForm ($type ='') {

	if ($type == 'Part')  $title = 'Part Inquiry';
	else $title = 'Estimate';

						$data['contents'] = '
						-----------------------<br>
						From: '.$this->insertdata['fname'].' '.$this->insertdata['lname'].'<br>
						Email: '.$this->insertdata['email'].'<Br>
						Tel: '.$this->insertdata['tel'].'<Br>
						Date: '.$this->insertdata['date'].'<br>
						<br>
						Brand: '.$this->insertdata['brand'].'<br>
						Model: '.$this->insertdata['model'].'<br>
						<br>
						'.$title.'<br>
						'.$this->insertdata['item'].'<br>
						-----------------------<Br>';
						$this->msg_data = array ('msg_title' => 'New '.$title.' data from '.$this->insertdata['fname'].' '.$this->insertdata['lname'].' @ '.FlipDateMail($this->insertdata['date']),
											'msg_body' => $data['contents'],
											'msg_date' => $this->insertdata['date']
											);

						$this->mailid = 16;
						GoMail($this->msg_data, '', $this->insertdata['email']);
						return $this->msg_data;
	}

function _DoForm ($data) {

						$data['contents'] = '
						-----------------------<br>
						From: '.$data['names'].'<br>
						Email: '.$data['email'].'<Br>
						Date: '.$data['date'].'<br>
						-----------------------<Br>
						'.$data['contents'];
						$this->msg_data = array ('msg_title' => 'Inquiry data from '.$data['names'].' @ '.FlipDateMail($data['date']),
											'msg_body' => $data['contents'],
											'msg_date' => $data['date']
											);

						$this->mailid = 15;
						GoMail($this->msg_data, $data['email']);
	}
*/
function _CheckLogin()
		{
			if (!isset($this->session->userdata['user_id'])) {Redirect(""); exit();}

		}

}