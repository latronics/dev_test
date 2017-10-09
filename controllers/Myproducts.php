<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Myproducts extends Controller {

function Myproducts()
	{
		parent::Controller();
		$this->load->model('Myproducts_model');
		$this->load->model('Auth_model');
		$this->Auth_model->VerifyAdmin();
		if ($this->session->userdata['admin_id'] == 7) { echo 'Sorry, you don\'t have clearance for here.';exit();}
		if ($this->session->userdata['admin_id'] == 10) { echo 'Sorry, you don\'t have clearance for here.'; exit();}

		$this->load->model('Myproducts_model');
		$this->mysmarty->assign('zero', $this->Myproducts_model->CheckWeightZero());

		$this->mysmarty->assign('session',$this->session->userdata);
		$this->mysmarty->assign('error_msg',$this->session->flashdata('error_msg'));
		$this->mysmarty->assign('success_msg',$this->session->flashdata('success_msg'));
		$this->mysmarty->assign('area', 'Products');
		$this->mysmarty->assign('prdpath', $this->config->config['wwwpath']['imgproducts']);
	}

function index()
	{
		$this->mysmarty->assign('catlist', $this->Myproducts_model->GetAllCategories());
		$this->mysmarty->assign('catitemammounts', $this->Myproducts_model->GetAllAmmounts());
		$this->mysmarty->view('myproducts/myproducts_main.html');
	}

function VisibilityQuantity ($lastid = 0)
	{
		$this->mysmarty->assign('lastid', (int)$lastid);
		$this->mysmarty->assign('list', $this->Myproducts_model->GetVizZero());
		$this->mysmarty->view('myproducts/myproducts_vizzero.html');
	}
function Zero ($lastid = 0)
	{
		$this->mysmarty->assign('lastid', (int)$lastid);
		$this->mysmarty->assign('list', $this->Myproducts_model->GetZero());
		$this->mysmarty->view('myproducts/myproducts_zero.html');
	}
function UpdateVizZero($id = 0)
	{

		if (isset($_POST['p_quantity']))
		{
			$quantity = (float)$this->input->post('p_quantity');
			if ((int)$id > 0 && $quantity > 0)
				{
					$this->db->update('products', array('p_quantity' => $quantity),  array('p_id' => (int)$id));
				}
		}


		Redirect('Myproducts/VisibilityQuantity#'.(int)$id);
	}
function UpdateZero($id = 0)
	{
		if (isset($_POST['p_lbs']) || isset($_POST['p_oz']))
		{
			$lbs = (float)$this->input->post('p_lbs');
			$oz = (float)$this->input->post('p_oz');
			if ((int)$id > 0 && ($lbs > 0 || $oz > 0))
				{
					$this->db->update('products', array('p_lbs' => $lbs, 'p_oz' => $oz),  array('p_id' => (int)$id));
				}
		}
		if (isset($_POST['p_price']))
		{
			$price = (float)$this->input->post('p_price');
			if ((int)$id > 0 && $price > 0)
				{
					$this->db->update('products', array('p_price' => $price),  array('p_id' => (int)$id));
				}
		}
		if (isset($_POST['p_quantity']))
		{
			$quantity = (float)$this->input->post('p_quantity');
			if ((int)$id > 0 && $quantity > 0)
				{
					$this->db->update('products', array('p_quantity' => $quantity),  array('p_id' => (int)$id));
				}
		}


		Redirect('Myproducts/Zero/'.(int)$id);
	}
function Warehouse($sortby = '')
	{
		$this->sortby = CleanInput($sortby);

		$this->mysmarty->assign('area', 'Warehouse');
		$session_search = $this->session->userdata('last_string');

		if (isset($_POST['search'])) $string = $this->input->post('search', TRUE);
		elseif ($session_search) $string = $this->session->userdata('last_string');
		else $string = '';

		$this->session->set_userdata('last_string', $string);
		$this->mysmarty->assign('string', $string);

		$this->mysmarty->assign('list', $this->Myproducts_model->GetWarehouse($string, $this->sortby));
		$this->mysmarty->view('myproducts/myproducts_warehouse.html');
	}
function CleanWarehouseSearch()
	{
		$this->session->unset_userdata('last_string');
		Redirect('Myproducts/Warehouse');
	}
function Weight()
	{

				/*$this->movedata = $this->Myproducts_model->GetWeight();
				foreach ($this->movedata as $key => $value)
				{
					$this->db->update('products', array('p_origweight' => $value['p_weight']), array('p_id' => $value['p_id']));
				}*/

				/*$this->movedata = $this->Myproducts_model->GetWeight();
				foreach ($this->movedata as $key => $value)
				{
					if ($value['p_weight'] < 10) $value['p_weight'] = sprintf('%.1f', $value['p_weight']*453.59237);
					$this->db->update('products', array('p_weight' => $value['p_weight']), array('p_id' => $value['p_id']));
				}*/

				/*$this->movedata = $this->Myproducts_model->GetWeight();
				foreach ($this->movedata as $key => $value)
				{
					if ($value['p_weight'] < 10) $value['p_weight'] = sprintf('%.1f', $value['p_weight']*453.59237);

					$value['lbs'] = $value['p_weight']*0.00220462262;
					$tmp = explode('.', $value['lbs']);
					$value['lbs'] = $tmp[0];
					$tmp[1] =  '0.'.$tmp[1];
					$value['oz'] = sprintf("%.2f", (16*$tmp[1]));
					$this->db->update('products', array('p_lbs' => $value['lbs'], 'p_oz' => $value['oz']), array('p_id' => $value['p_id']));
				}*/


		$this->mysmarty->assign('list', $this->Myproducts_model->GetWeight());
		$this->mysmarty->view('myproducts/myproducts_weight.html');
	}

function DoSef()
	{
		$array = $this->Myproducts_model->GetAllToSef();
		foreach ($array as $key => $value)
		{
			if ($value['p_sef'] == '')
			{
			$value['p_sef'] = $this->_CleanSef($value['p_title']);
			$this->checkexists =  $this->Myproducts_model->CheckProductSefExists($value['p_sef']);
			if ($this->checkexists) $this->pref = rand(1,9).rand(1,9).rand(1,9); else $this->pref = '';
			$value['p_sef'] = $value['p_sef'].$this->pref;
			$this->Myproducts_model->UpdateSef($value);
			}
		}

		Redirect('/Myproducts');
	}
function DoProfit()
	{
		$array = $this->Myproducts_model->GetAllPriceData();
		foreach ($array as $key => $value)
		{
			$value['p_profit'] = (float)$value['p_price'] - (float)$value['p_theirprice'];
			$this->Myproducts_model->UpdateProfit($value);
		}

		Redirect('/Myproducts');
	}
function AllVisible($cat = 0)
	{
	if ((int)$cat > 0) {
		$this->Myproducts_model->AllVisible((int)$cat);
		if ($cat == 34) $this->RecreateSpecialTopImgsEbay();
	}
	Redirect('Myproducts/Show/'.(int)$cat);
	}
function AllInvisible($cat = 0)
	{
	if ((int)$cat > 0) {
		$this->Myproducts_model->AllInvisible((int)$cat);
		if ($cat == 34) $this->RecreateSpecialTopImgsEbay();
	}
	Redirect('Myproducts/Show/'.(int)$cat);
	}
function Search()
	{
		$session_search = $this->session->userdata('last_string');
		if (isset($_POST['search'])) $string = $this->input->post('search', TRUE);
		elseif ($session_search) $string = $this->session->userdata('last_string');
		else Redirect('/Myproducts');

		$this->productlist = $this->Myproducts_model->Search($string);
		$this->mysmarty->assign('list', $this->productlist);
		$this->mysmarty->assign('string', $string);
		$this->session->set_userdata('last_string', $string);
		$this->mysmarty->view('myproducts/myproducts_searchlist.html');

	}
function GetCategoryXML($catid = 0)
	{
	if ((int)$catid > 0)
		{
		$this->productlist = $this->Myproducts_model->ListXMLItems((int)$catid);
		$this->cat = $this->Myproducts_model->GetCategory((int)$catid);
		$feed = '<?xml version="1.0"?> 
					<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">
					<channel>
					<title>'.CleanXML($this->cat['p_cattitle']).'</title>
					<link>http://www.365laptoprepair.com</link>
					<description>Products of category '.CleanXML($this->cat['p_cattitle']).'</description>';
		if ($this->productlist) foreach ($this->productlist as $key => $value)
			{
			$feed .= "
			<item>
				<title>".substr(CleanXML($value['p_title']), 0, 70)."</title>
				<link>".Site_url().'Product/'.$value['p_sef']."</link>
				<g:id>".$value['p_id']."</g:id>
				<g:price>".$value['p_price']."</g:price>
				<g:condition>".strtolower($value['p_condition'])."</g:condition>
				<g:product_type>Electronics &gt; Computers &gt; Laptops &gt; ".$this->cat['p_cattitle']."</g:product_type>
				<g:image_link>".Site_url()."content/products/thumb_main_".$value['p_img1']."</g:image_link>
				<description>".CleanXML(strip_tags($value['p_desc']))."</description>
			</item>
";
			}

		$feed .= '
				</channel>
				</rss>';
		// echo str_replace('{br}', '<br>', htmlspecialchars($feed));
		//$this->load->helper('file');
		//delete_files($this->config->config['paths']['xml']);
		//write_file($this->config->config['paths']['xml'].'/products_'.(int)$catid.'.xml', $feed);
		//write_file($this->config->config['paths']['xml'].'/index.html', ' :) ');

		$this->load->helper('download');
		$name = 'products_'.(int)$catid.'.xml';
		force_download($name, $feed);

		}
	}
function Feeds()
	{
		$this->load->helper('file');
		$dir = get_dir_file_info($this->config->config['paths']['feeds']);
		sort ($dir);
		$this->mysmarty->assign('feedpath', $this->config->config['wwwpath']['feeds']);
		$this->mysmarty->assign('feeds', $dir);
		$this->mysmarty->view('myproducts/myproducts_feeds.html');
	}
function AddCategory()
	{
			$this->load->library('form_validation');
			$this->form_validation->set_rules('p_cattitle', 'Category Title', 'trim|required|min_length[3]|xss_clean');

					$this->inputdata['p_cattitle'] = $this->input->post('p_cattitle', TRUE);
					$this->inputdata['p_parent'] = (int)$this->input->post('p_parent', TRUE);
					$this->inputdata['p_columns'] = (int)$this->input->post('p_columns', TRUE);

			if ($this->form_validation->run() == FALSE)
			{
				$this->mysmarty->assign('inputdata', $this->inputdata);
				$this->mysmarty->assign('catlist', $this->Myproducts_model->GetAllCategories());
				$this->mysmarty->assign('catitemammounts', $this->Myproducts_model->GetAllAmmounts());
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);
				$this->mysmarty->view('myproducts/myproducts_main.html');
				exit();
			}
			else
			{


				$this->db_data['p_cattitle'] = $this->form_validation->set_value('p_cattitle');
				$this->db_data['p_parent'] = (int)$this->input->post('p_parent', TRUE);
				$this->db_data['p_columns'] = (int)$this->input->post('p_columns', TRUE);
				$this->checkexists =  $this->Myproducts_model->CheckCategorySefExists($this->_CleanSef($this->form_validation->set_value('p_cattitle')));

				if ($this->checkexists) $this->pref = rand(1,9).rand(1,9).rand(1,9); else $this->pref = '';
				$this->db_data['p_sef'] = $this->_CleanSef($this->form_validation->set_value('p_cattitle').$this->pref);



				$insid = $this->Myproducts_model->InsertCategory($this->db_data);

				if ($_FILES['p_img']['name'] != '')
								{
									$this->load->library('upload');
									$newname = (int)$insid.'_'.substr($this->db_data['p_sef'], 0, 210);
									$image = $this->_UploadImage ('p_img', $this->config->config['paths']['imgproducts'], TRUE, $this->config->config['sizes']['productimg']['width'], $this->config->config['sizes']['productimg']['height'], FALSE, TRUE, $newname);

									$this->db_data['p_img'] = $image;
									//$watermark = TRUE;
								}
				Redirect("/Myproducts");
			}
	}
function UpdateCategory($id)
	{
	$this->id = (int)$id;
	if ($this->id > 0)
		{
			$this->load->library('form_validation');
			$this->form_validation->set_rules('p_cattitle', 'Category Title', 'trim|required|min_length[3]|xss_clean');

					$this->inputdata[$this->id] = array(
											'p_catid' => $this->id,
											'p_cattitle' => $this->input->post('p_cattitle', TRUE),
											'p_order' => (int)$this->input->post('p_order', TRUE)
											);
			if ($this->form_validation->run() == FALSE)
			{
				$this->mysmarty->assign('inputdata', $this->inputdata);
				$this->mysmarty->assign('catlist', $this->Myproducts_model->GetAllCategories());

				$this->mysmarty->assign('errors', array( $this->id => $this->form_validation->_error_array));
				$this->mysmarty->view('myproducts/myproducts_main.html');
				exit();
			}
			else
			{
				$this->db_data['p_cattitle'] = $this->form_validation->set_value('p_cattitle');
				$this->checkexists =  $this->Myproducts_model->CheckCategorySefExists($this->_CleanSef($this->form_validation->set_value('p_cattitle')), $this->id);
				if ($this->checkexists) $this->pref = $this->id; else $this->pref = '';
				$this->db_data['p_sef'] = $this->_CleanSef($this->form_validation->set_value('p_cattitle').$this->pref);
				$this->db_data['p_order'] = (int)$this->input->post('p_order', TRUE);

				if ($_FILES['p_img']['name'] != '')
								{
									$this->load->library('upload');
									$newname = (int)$this->id.'_'.substr($this->db_data['p_sef'], 0, 210);
									$image = $this->_UploadImage ('p_img', $this->config->config['paths']['imgproducts'], TRUE, $this->config->config['sizes']['productimg']['width'], $this->config->config['sizes']['productimg']['height'], FALSE, TRUE, $newname);
									if ($image)
									{
										$oldimage = $this->Myproducts_model->GetOldImage($this->id);
										if ($oldimage != '' && $image != $oldimage)
											{
											unlink($this->config->config['paths']['imgproducts'].'/'.$oldimage);
											unlink($this->config->config['paths']['imgproducts'].'/thumb_'.$oldimage);
											unlink($this->config->config['paths']['imgproducts'].'/thumb_main_'.$oldimage);
											}

										$this->db_data['p_img'] = $image;
										//$watermark = TRUE;
									}
								}

				$this->Myproducts_model->UpdateCategory($this->id, $this->db_data);
				Redirect("/Myproducts"); exit();
			}
		}
	}
function EditCategoryBody($id = 0)
	{
	$this->id = (int)$id;
	if ($this->id > 0)
		{
			if (isset($_POST['p_body']) || isset($_POST['p_cation']))
				{
					$this->Myproducts_model->UpdateBodyCategory($this->id, $this->input->post('p_body', TRUE), $this->input->post('p_caption', TRUE), $this->input->post('p_columns', TRUE) );
				}
				$getdata = $this->Myproducts_model->GetBodyCategory($this->id);

				$this->mysmarty->assign('p_columns', $getdata['p_columns']);
					require_once($this->config->config['pathtopublic'].'/fckeditor/fckeditor.php');

					$this->editor = new FCKeditor('p_body');
					$this->editor->Width = "650";
					$this->editor->Height = "400";
					$this->editor->Value = $getdata['p_body'];
					$this->mysmarty->assign('p_body', $this->editor->CreateHtml());

					$this->editor2 = new FCKeditor('p_caption');
					$this->editor2->Width = "400";
					$this->editor2->Height = "300";
					$this->editor2->Value = $getdata['p_caption'];
					$this->mysmarty->assign('p_caption', $this->editor2->CreateHtml());


					$this->mysmarty->assign('title', $getdata['p_cattitle']);
					$this->mysmarty->assign('id', (int)$this->id);
				$this->mysmarty->view('myproducts/myproducts_catbody.html');
		}
	}
function DeleteCategory($id)
	{
		$this->id = (int)$id;
		if (($this->id > 0) && ($this->id != '34'))
				{
				$this->DeleteImageInCategory($this->id, FALSE);
				$this->Myproducts_model->DeleteCategory($this->id);
				}
		Redirect("/Myproducts");
	}
function DeleteImageInCategory($id, $redirect=true)
	{
		$this->id = (int)$id;
		if ($this->id > 0)
				{
				$this->img = $this->Myproducts_model->DeleteoldImage($this->id);
				if ($this->img != '') unlink($this->config->config['paths']['imgproducts'].'/'.$this->img);
				}
		if($redirect) Redirect("/Myproducts");
	}
function TopCat ($id = '')
	{
		$this->id = (int)$id;
		if ($this->id > 0) $this->Myproducts_model->MakeCatTop($this->id);
		Redirect("/Myproducts");
	}
function UnTopCat ($id = '')
	{
		$this->id = (int)$id;
		if ($this->id > 0) $this->Myproducts_model->UnCatTop($this->id);
		Redirect("/Myproducts");
	}
function VisCat ($id = '')
	{
		$this->id = (int)$id;
		if ($this->id > 0) $this->Myproducts_model->MakeCatVis($this->id);
		Redirect("/Myproducts");
	}
function UnVisCat ($id = '')
	{
		$this->id = (int)$id;
		if ($this->id > 0) $this->Myproducts_model->UnCatVis($this->id);
		Redirect("/Myproducts");
	}

function AllZeroCatInvis($cat = 0)
	{
		if ((int)$cat > 0)
		{
			$this->Myproducts_model->AllZeroCatInvis((int)$cat);
		}
		Redirect("/Myproducts");
	}


function Show($cat = '', $sortby = '')
	{
		$this->cat = (int)$cat;

		$this->sortby = CleanInput($sortby);
		$this->session->set_flashdata('cat', $this->cat);
		$this->session->set_flashdata('sortby', '/'.$this->sortby);

		$this->mysmarty->assign('cat', $this->cat);
		$this->mysmarty->assign('count', $this->Myproducts_model->CountProducts());
		$this->mysmarty->assign('category', $this->Myproducts_model->GetCategory($this->cat));

		$this->productlist = $this->Myproducts_model->ListItems($this->cat, $this->sortby);

		$this->mysmarty->assign('list', $this->productlist);
		$this->mysmarty->assign('catlist', $this->Myproducts_model->GetAllListCategories());
		$this->mysmarty->assign('catitemammounts', $this->Myproducts_model->GetAllAmmounts());
		$this->mysmarty->assign('pTypes', array (0 => 'Product', 1 => 'Predefined Repair', '2' => 'Custom Repair'));
		$this->mysmarty->view('myproducts/myproducts_show.html');

	}


function MakeVisible ($id = '', $from = '')
	{
		$this->id = (int)$id;
		if ($this->id > 0) {
			$this->cat = $this->Myproducts_model->MakeVisible($this->id);
			if ($this->cat == 34) $this->RecreateSpecialTopImgsEbay();
		}

		if ($from == 'Search') Redirect("/Myproducts/Search");
		elseif ($from == 'Warehouse') Redirect("/Myproducts/Warehouse#".$this->id);
		elseif ($from != '') Redirect("/Myproducts/".$from."#".$this->id);
		else Redirect("/Myproducts/Show/".$this->cat.'/'.$this->session->flashdata('sortby'));
	}
function MakeNotVisible ($id = '', $from = '')
	{
		$this->id = (int)$id;
		if ($this->id > 0) {
			$this->cat = $this->Myproducts_model->MakeNotVisible($this->id);
			if ($this->cat == 34) $this->RecreateSpecialTopImgsEbay();
		}

		if ($from == 'Search') Redirect("/Myproducts/Search");
		elseif ($from == 'Warehouse') Redirect("/Myproducts/Warehouse#".$this->id);
		elseif ($from != '') Redirect("/Myproducts/".$from."#".$this->id);
		else Redirect("/Myproducts/Show/".$this->cat.'/'.$this->session->flashdata('sortby'));
	}

function MakeTop ($catid = '')
	{
		$this->id = (int)$catid;
		if ($this->id > 0) {
			$this->cat = $this->Myproducts_model->MakeTop($this->id);
			if ($this->cat == 34) $this->RecreateSpecialTopImgsEbay();
		}
		Redirect("/Myproducts/Show/".$this->cat.'/'.$this->session->flashdata('sortby'));
	}
function UnTop ($id = '')
	{
		$this->id = (int)$id;
		if ($this->id > 0){
			$this->cat = $this->Myproducts_model->UnTop($this->id);
			if ($this->cat == 34) $this->RecreateSpecialTopImgsEbay();
		}

		Redirect("/Myproducts/Show/".$this->cat.'/'.$this->session->flashdata('sortby'));
	}


function MakeNew ($id = '')
	{
		$this->id = (int)$id;
		if ($this->id > 0) $this->cat = $this->Myproducts_model->MakeNew($this->id);
		Redirect("/Myproducts/Show/".$this->cat.'/'.$this->session->flashdata('sortby'));
	}
function UnNew ($id = '')
	{
		$this->id = (int)$id;
		if ($this->id > 0) $this->cat = $this->Myproducts_model->UnNew($this->id);
		Redirect("/Myproducts/Show/".$this->cat.'/'.$this->session->flashdata('sortby'));
	}

function InStock ($id = '')
	{
		$this->id = (int)$id;
		if ($this->id > 0) $this->cat = $this->Myproducts_model->MakeInStock($this->id);

		Redirect("/Myproducts/Show/".$this->cat.'/'.$this->session->flashdata('sortby'));
	}
function OutStock ($id = '')
	{
		$this->id = (int)$id;
		if ($this->id > 0) $this->cat = $this->Myproducts_model->MakeOutStock($this->id);
		Redirect("/Myproducts/Show/".$this->cat.'/'.$this->session->flashdata('sortby'));
	}
function Delete ($id = '', $from = '')
	{
		$this->id = (int)$id;
		if ($this->id > 0)
			{
			$this->DeleteImageInProduct($this->id, '1', TRUE);
			$this->DeleteImageInProduct($this->id, '2', TRUE);
			$this->DeleteImageInProduct($this->id, '3', TRUE);
			$this->DeleteImageInProduct($this->id, '4', TRUE);
			$this->DeleteAdInProduct($this->id, TRUE);
			$this->cat = $this->Myproducts_model->Delete($this->id);
			if ($this->cat == 34) $this->RecreateSpecialTopImgsEbay();
			}
		if ($from == 'Search') Redirect("/Myproducts/Search");
		else Redirect("/Myproducts/Show/".$this->cat.'/'.$this->session->flashdata('sortby'));
	}
function ChangeOrder ($id = '')
	{
		$this->id = (int)$id;
		$this->order = (int)$this->input->post('p_order', TRUE);
		if ($this->id > 0) {

			$this->Myproducts_model->ChangeOrder($this->id, $this->order);
			if (isset($_POST['p_catid']) && $_POST['p_catid'] > 0)
				{
				$this->Myproducts_model->ChangeCategory($this->id, (int)$_POST['p_catid']);
				}
			$cat = $this->Myproducts_model->GetPCATFromId($this->id);
			if ($cat == 34) $this->RecreateSpecialTopImgsEbay();
		}
		Redirect("/Myproducts/Show/".$this->session->flashdata('cat'));
	}

function ChangeQuantity ($id = '')
	{
		$this->id = (int)$id;
		$this->quantity = (int)$this->input->post('p_quantity', TRUE);
		$this->pending = (int)$this->input->post('p_pendquant', TRUE);
		if ($this->id > 0) {

			$this->Myproducts_model->ChangeQuantity($this->id, $this->quantity, $this->pending);
		}
		Redirect("/Myproducts/Warehouse#".$this->id);
	}

function ChangeCategory ($id = '')
	{
		$this->id = (int)$id;
		$this->cat = (int)$this->input->post('p_catid', TRUE);
		if ($this->id > 0) {
			$this->Myproducts_model->ChangeCategory($this->id, (int)$this->cat);
		}
		Redirect("/Myproducts/Show/".$this->session->flashdata('cat'));
	}
function ReOrder ($by = '', $sortby = '')
	{
		$this->by = (int)$by;
		$this->sortby = CleanInput($sortby);
		if ($this->by > 0) $this->Myproducts_model->ReOrder($this->by, $this->sortby);
		if ($this->by == 34) $this->RecreateSpecialTopImgsEbay();
		Redirect("/Myproducts/Show/".$this->by);

	}
function ReOrderCat ()
	{
		$this->Myproducts_model->ReOrderCat();

		Redirect("/Myproducts");

	}

function AddFrom($itemid = '')
	{
		$this->id = (int)$itemid;

		if ($this->id > 0) {

		$this->mysmarty->assign('catlist', $this->Myproducts_model->GetAllCategories());
		$this->mysmarty->assign('catitemammounts', $this->Myproducts_model->GetAllAmmounts());

		$this->mysmarty->assign('pTypes', array (0 => 'Product', 1 => 'Predefined Repair', '2' => 'Custom Repair'));

		$this->displays = $this->Myproducts_model->GetItem($this->id);


		$this->load->library('form_validation');

		$this->form_validation->set_rules('p_title', 'Title', 'trim|required|min_length[3]|xss_clean');
		$this->form_validation->set_rules('p_order', 'Order', 'trim|required|xss_clean');
		if ($this->displays['p_type'] == 2) $this->form_validation->set_rules('p_price', 'Price', 'trim|required|xss_clean');
		else $this->form_validation->set_rules('p_price', 'Price', 'trim|required|xss_clean|callback__checkifzero');
		$this->form_validation->set_rules('p_lbs', 'Lbs', 'trim|required|xss_clean');
		$this->form_validation->set_rules('p_oz', 'Oz', 'trim|required|xss_clean');
		$this->form_validation->set_rules('p_condition', 'Condition', 'trim|required|xss_clean');
		$this->form_validation->set_rules('p_cat', 'Category', 'trim|required|xss_clean');
		$this->form_validation->set_rules('p_theirprice', 'Distributers', 'trim|xss_clean');

		if ((int)$this->displays['p_type'] == '1') //((int)$this->displays['p_type'] == '34')
		{
			$this->form_validation->set_rules('p_toptxt', 'Toplist Text', 'trim|xss_clean');
		}

		if ($this->form_validation->run() == FALSE)
			{

				$this->inputdata = array(
											'p_title' => addslashes($this->input->post('p_title', TRUE)),
											'p_cat' => (int)$this->input->post('p_cat'),
											'p_order' => (int)$this->input->post('p_order'),
											'p_price' => (float)PriceUnification($this->input->post('p_price')),
											'p_lbs' => (int)$this->input->post('p_lbs'),
											'p_oz' => (float)$this->input->post('p_oz'),
											'p_quantity' => (int)$this->input->post('p_quantity'),
											'p_pendquant' => (int)$this->input->post('p_pendquant'),
											'p_theirprice' => (float)PriceUnification($this->input->post('p_theirprice')),
											'p_condition' => $this->input->post('p_condition'),
											'p_desc' => $this->input->post('p_desc', TRUE)
											);
				if (isset($_POST['p_freegrship'])) $this->inputdata['p_freegrship'] = 1;
				else $this->inputdata['p_freegrship'] = 0;
				if (isset($_POST['p_shipping'])) $this->inputdata['p_shipping'] = 1;
				else $this->inputdata['p_shipping'] = 0;

				if ((int)$this->displays['p_type'] == '1') //((int)$this->displays['p_cat'] == '34')
				{
					$this->inputdata['p_toptxt'] = $this->input->post('p_toptxt', TRUE);

				}


				require_once($this->config->config['pathtopublic'].'/fckeditor/fckeditor.php');

				$this->editor = new FCKeditor('p_desc');

				$this->editor->Width = "650";
				$this->editor->Height = "400";

				if (count($_POST) >	0)
					{
						$this->editor->Value = $this->inputdata['p_desc'];
						$this->inputdata['p_desc'] = $this->editor->CreateHtml();
					}
				else
					{
						$this->editor->Value = $this->displays['p_desc'];
						$this->displays['p_desc'] = $this->editor->CreateHtml();
					}


				if ((int)$this->displays['p_type'] == '1') //((int)$this->displays['p_cat'] == '34')
				{

					$this->editortoptxt = new FCKeditor('p_toptxt');

					$this->editortoptxt->Width = "650";
					$this->editortoptxt->Height = "400";

					if (count($_POST) >	0)
						{
							$this->editortoptxt->Value = $this->inputdata['p_toptxt'];
							$this->inputdata['p_toptxt'] = $this->editortoptxt->CreateHtml();
						}
					else
						{
							$this->editortoptxt->Value = $this->displays['p_toptxt'];
							$this->displays['p_toptxt'] = $this->editortoptxt->CreateHtml();
						}


				}
				$this->mysmarty->assign('displays', $this->displays);
				$this->mysmarty->assign('catlist', $this->Myproducts_model->GetAllListCategories());
				$this->mysmarty->assign('inputdata', $this->inputdata);
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);
				$this->mysmarty->assign('addfrom', TRUE);
				$this->mysmarty->view('myproducts/myproducts_edit.html');
				exit();
			}
			else
			{

					$this->db_data = array(
											'p_title' => addslashes($this->form_validation->set_value('p_title')),
											'p_type' => $this->displays['p_type'],
											'p_cat' => (int)$this->form_validation->set_value('p_cat'),
											'p_order' => (int)$this->form_validation->set_value('p_order'),
											'p_price' => (float)PriceUnification($this->form_validation->set_value('p_price')),
											'p_lbs' => (int)$this->form_validation->set_value('p_lbs'),
											'p_oz' => (float)PriceUnification($this->form_validation->set_value('p_oz')),
											'p_quantity' => (int)$this->input->post('p_quantity', TRUE),
											'p_pendquant' => (int)$this->input->post('p_pendquant', TRUE),
											'p_condition' => $this->input->post('p_condition', TRUE),
											'p_theirprice' => (float)PriceUnification($this->form_validation->set_value('p_theirprice')),
											'p_desc' => $this->input->post('p_desc', TRUE)
											);

					$this->db_data['p_sef'] = $this->_CleanSef($this->form_validation->set_value('p_title'));

					$this->_lbsozunify();

					if ((float)$this->db_data['p_theirprice'] == 0) $this->db_data['p_theirprice'] = $this->db_data['p_price'];
					$this->db_data['p_profit'] = $this->db_data['p_price'] - $this->db_data['p_theirprice'];

					if (isset($_POST['p_freegrship'])) $this->db_data['p_freegrship'] = 1;
					else $this->db_data['p_freegrship'] = 0;
					if (isset($_POST['p_shipping'])) $this->db_data['p_shipping'] = 1;
					else $this->db_data['p_shipping'] = 0;

					if ((int)$this->displays['p_cat'] == '1')
					{
						$this->db_data['p_toptxt'] = $this->form_validation->set_value('p_toptxt');
					}


						$this->checkexists =  $this->Myproducts_model->CheckProductSefExists($this->db_data['p_sef']);
						if ($this->checkexists) $this->pref = rand(1,9).rand(1,9).rand(1,9); else $this->pref = '';
						$this->db_data['p_sef'] = $this->db_data['p_sef'].$this->pref;

					$this->newid = $this->Myproducts_model->Insert($this->db_data);

					$this->productimages = array(1,2,3,4);

					$this->load->library('upload');

					$watermark = FALSE;
					foreach($this->productimages as $value)
					{			if ($_FILES['p_img'.$value]['name'] != '')
								{

									$this->newdb_data['p_origimg'.$value] = $this->_clean_file_name($_FILES['p_img'.$value]['name']);

									$newname[$value] = (int)$this->id.'_'.substr($this->db_data['p_sef'], 0, 210).'_'.$value;

									$image[$value] = $this->_UploadImage ('p_img'.$value, $this->config->config['paths']['imgproducts'], TRUE, $this->config->config['sizes']['productimg']['width'], $this->config->config['sizes']['productimg']['height'], FALSE, TRUE, $newname[$value]);
									if ($image[$value]) {

										$this->newdb_data['p_img'.$value] = $image[$value];
										$watermark = TRUE;
									}
								}
								elseif ($this->displays['p_img'.$value] != '')
									{
										$file[$value] = $this->config->config['paths']['imgproducts'].'/'.$this->displays['p_img'.$value];
										$newfile[$value] = $this->config->config['paths']['imgproducts'].'/'.(int)$this->newid.'_'.$this->displays['p_img'.$value];
										if (!copy($file[$value], $newfile[$value])) {
											echo "failed to copy $file...\n";
										}

										$this->newdb_data['p_img'.$value] = (int)$this->newid.'_'.$this->displays['p_img'.$value];

										$file[$value] = $this->config->config['paths']['imgproducts'].'/thumb_'.$this->displays['p_img'.$value];
										$newfile[$value] = $this->config->config['paths']['imgproducts'].'/thumb_'.(int)$this->newid.'_'.$this->displays['p_img'.$value];
										if (!copy($file[$value], $newfile[$value])) {
											echo "failed to copy $file...\n";
										}

										$file[$value] = $this->config->config['paths']['imgproducts'].'/thumb_main_'.$this->displays['p_img'.$value];
										$newfile[$value] = $this->config->config['paths']['imgproducts'].'/thumb_main_'.(int)$this->newid.'_'.$this->displays['p_img'.$value];
										if (!copy($file[$value], $newfile[$value])) {
											echo "failed to copy $file...\n";
										}
									}
					}

					if (((int)$this->displays['p_type'] == '1') && $_FILES['p_ad']['name'] != '')
								{
									$adimage = $this->_UploadImage ('p_ad', $this->config->config['paths']['imgproducts'], FALSE, '123', '123',TRUE);
									if ($adimage) {
										$this->newdb_data['p_ad'] = $adimage;
									}
								}
					elseif (((int)$this->displays['p_type'] == '1') && $this->displays['p_ad'] != '')
								{
									$this->newdb_data['p_ad'] = $this->displays['p_ad'];
									$filead = $this->config->config['paths']['imgproducts'].'/'.$this->displays['p_ad'];
									$newfilead = $this->config->config['paths']['imgproducts'].'/'.(int)$this->newid.'_'.$this->displays['p_ad'];
										if (!copy($filead, $newfilead)) {
											echo "failed to copy $file...\n";
										}
								}

						if (isset($this->newdb_data)) $this->Myproducts_model->Update((int)$this->newid, $this->newdb_data);

						$this->RecreateSpecialTopImgsEbay();

						if ($watermark) Redirect('Myproducts/DoWaterMark/'.$this->displays['p_cat'].'/'.(int)$this->newid);
						else redirect("Myproducts/Show/".$this->displays['p_cat']); exit();
			}
	}
	else {
		if ($from == 'Search') Redirect("/Myproducts/Search");
		else redirect("/Myproducts/Show/".$this->displays['p_cat']."/");
	}
}
function Edit($itemid = '', $from ='')
	{

		$this->id = (int)$itemid;
		$this->cat = (int)$this->session->flashdata('cat');

		if ($this->id > 0) {

		if ($from == 'Search') $this->mysmarty->assign('from', '/Search');

		$this->mysmarty->assign('cat', $this->cat);
		$this->mysmarty->assign('catlist', $this->Myproducts_model->GetAllCategories());
		$this->mysmarty->assign('catitemammounts', $this->Myproducts_model->GetAllAmmounts());

		$this->mysmarty->assign('pTypes', array (0 => 'Product', 1 => 'Predefined Repair', '2' => 'Custom Repair'));

		$this->session->set_flashdata('cat', $this->cat);

		$this->displays = $this->Myproducts_model->GetItem($this->id);


		$this->load->library('form_validation');

		$this->form_validation->set_rules('p_title', 'Title', 'trim|required|min_length[3]|xss_clean');
		$this->form_validation->set_rules('p_order', 'Order', 'trim|required|xss_clean');
		if ($this->displays['p_type'] == 2) $this->form_validation->set_rules('p_price', 'Price', 'trim|required|xss_clean');
		else $this->form_validation->set_rules('p_price', 'Price', 'trim|required|xss_clean|callback__checkifzero');
		$this->form_validation->set_rules('p_lbs', 'Lbs', 'trim|required|xss_clean');
		$this->form_validation->set_rules('p_oz', 'Oz', 'trim|required|xss_clean');
		$this->form_validation->set_rules('p_condition', 'Condition', 'trim|required|xss_clean');
		$this->form_validation->set_rules('p_theirprice', 'Distributers', 'trim|xss_clean');

		if ((int)$this->displays['p_type'] == '1') //((int)$this->displays['p_type'] == '34')
		{
			$this->form_validation->set_rules('p_toptxt', 'Toplist Text', 'trim|xss_clean');
		}

		if ($this->form_validation->run() == FALSE)
			{
				$this->inputdata = array(
											'p_title' => addslashes($this->input->post('p_title', TRUE)),
											'p_order' => (int)$this->input->post('p_order'),
											'p_price' => (float)PriceUnification($this->input->post('p_price')),
											'p_lbs' => (int)$this->input->post('p_lbs'),
											'p_oz' => (float)$this->input->post('p_oz'),
											'p_quantity' => (int)$this->input->post('p_quantity'),
											'p_pendquant' => (int)$this->input->post('p_pendquant'),
											'p_theirprice' => (float)PriceUnification($this->input->post('p_theirprice')),
											'p_condition' => $this->input->post('p_condition'),
											'p_desc' => $this->input->post('p_desc', TRUE)
											);
				if (isset($_POST['p_freegrship'])) $this->inputdata['p_freegrship'] = 1;
				else $this->inputdata['p_freegrship'] = 0;
				if (isset($_POST['p_shipping'])) $this->inputdata['p_shipping'] = 1;
				else $this->inputdata['p_shipping'] = 0;

				if ((int)$this->displays['p_type'] == '1') //((int)$this->displays['p_cat'] == '34')
				{
					$this->inputdata['p_toptxt'] = $this->input->post('p_toptxt', TRUE);

				}


				require_once($this->config->config['pathtopublic'].'/fckeditor/fckeditor.php');

				$this->editor = new FCKeditor('p_desc');

				$this->editor->Width = "650";
				$this->editor->Height = "400";

				if (count($_POST) >	0)
					{
						$this->editor->Value = $this->inputdata['p_desc'];
						$this->inputdata['p_desc'] = $this->editor->CreateHtml();
					}
				else
					{
						$this->editor->Value = $this->displays['p_desc'];
						$this->displays['p_desc'] = $this->editor->CreateHtml();
					}


				if ((int)$this->displays['p_type'] == '1') //((int)$this->displays['p_cat'] == '34')
				{

					$this->editortoptxt = new FCKeditor('p_toptxt');

					$this->editortoptxt->Width = "650";
					$this->editortoptxt->Height = "400";

					if (count($_POST) >	0)
						{
							$this->editortoptxt->Value = $this->inputdata['p_toptxt'];
							$this->inputdata['p_toptxt'] = $this->editortoptxt->CreateHtml();
						}
					else
						{
							$this->editortoptxt->Value = $this->displays['p_toptxt'];
							$this->displays['p_toptxt'] = $this->editortoptxt->CreateHtml();
						}


				}
				$this->mysmarty->assign('displays', $this->displays);

				$this->mysmarty->assign('inputdata', $this->inputdata);
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);
				$this->mysmarty->view('myproducts/myproducts_edit.html');
				exit();
			}
			else
			{

					$this->db_data = array(
											'p_title' => addslashes($this->form_validation->set_value('p_title')),
											'p_order' => (int)$this->form_validation->set_value('p_order'),
											'p_price' => (float)PriceUnification($this->form_validation->set_value('p_price')),
											'p_lbs' => (int)$this->form_validation->set_value('p_lbs'),
											'p_oz' => (float)PriceUnification($this->form_validation->set_value('p_oz')),
											'p_quantity' => (int)$this->input->post('p_quantity', TRUE),
											'p_pendquant' => (int)$this->input->post('p_pendquant', TRUE),
											'p_condition' => $this->input->post('p_condition', TRUE),
											'p_theirprice' => (float)PriceUnification($this->form_validation->set_value('p_theirprice')),
											'p_desc' => $this->input->post('p_desc', TRUE)
											);

					if ($this->id != 435) $this->db_data['p_sef'] = $this->_CleanSef($this->form_validation->set_value('p_title'));

					$this->_lbsozunify();

					if ((float)$this->db_data['p_theirprice'] == 0) $this->db_data['p_theirprice'] = $this->db_data['p_price'];
					$this->db_data['p_profit'] = $this->db_data['p_price'] - $this->db_data['p_theirprice'];

					if (isset($_POST['p_freegrship'])) $this->db_data['p_freegrship'] = 1;
					else $this->db_data['p_freegrship'] = 0;
					if (isset($_POST['p_shipping'])) $this->db_data['p_shipping'] = 1;
					else $this->db_data['p_shipping'] = 0;

					if ((int)$this->displays['p_cat'] == '1')
					{
						$this->db_data['p_toptxt'] = $this->form_validation->set_value('p_toptxt');
					}

					if ($this->id != 435)
					{
						$this->checkexists =  $this->Myproducts_model->CheckProductSefExists($this->db_data['p_sef'], $this->id);
						if ($this->checkexists) $this->pref = $this->id; else $this->pref = '';
						$this->db_data['p_sef'] = $this->db_data['p_sef'].$this->pref;
					}

					$this->productimages = array(1,2,3,4);

					$this->load->library('upload');

					$watermark = FALSE;
					foreach($this->productimages as $value)
					{			if ($_FILES['p_img'.$value]['name'] != '')
								{

									$this->db_data['p_origimg'.$value] = $this->_clean_file_name($_FILES['p_img'.$value]['name']);

									if ($this->id == 435) $newname[$value] = (int)$this->id.'_laptoprepairform_'.$value;
									else $newname[$value] = (int)$this->id.'_'.substr($this->db_data['p_sef'], 0, 210).'_'.$value;

									$image[$value] = $this->_UploadImage ('p_img'.$value, $this->config->config['paths']['imgproducts'], TRUE, $this->config->config['sizes']['productimg']['width'], $this->config->config['sizes']['productimg']['height'], FALSE, TRUE, $newname[$value]);
									if ($image[$value]) {
										$oldimage[$value] = $this->Myproducts_model->GetOldProductImage($this->id, $value);
										if ($oldimage[$value] != '' && $image[$value] != $oldimage[$value]) {
											unlink($this->config->config['paths']['imgproducts'].'/'.$oldimage[$value]);
											unlink($this->config->config['paths']['imgproducts'].'/thumb_'.$oldimage[$value]);
											unlink($this->config->config['paths']['imgproducts'].'/thumb_main_'.$oldimage[$value]);
											}

										$this->db_data['p_img'.$value] = $image[$value];
										$watermark = TRUE;
									}
								}
					}

					if (((int)$this->displays['p_type'] == '1') && $_FILES['p_ad']['name'] != '')
								{
									$adimage = $this->_UploadImage ('p_ad', $this->config->config['paths']['imgproducts'], FALSE, '123', '123',TRUE);
									if ($adimage) {
										$adoldimage = $this->Myproducts_model->GetOldProductImageAd($this->id);
										if ($adoldimage != '') {
											unlink($this->config->config['paths']['imgproducts'].'/'.$adoldimage);
											}
										$this->db_data['p_ad'] = $adimage;
									}
								}


						$this->Myproducts_model->Update((int)$this->id,$this->db_data);

						$this->RecreateSpecialTopImgsEbay();

						if ($from == 'Search') Redirect("Myproducts/Search");
						elseif ($watermark) Redirect('Myproducts/DoWaterMark/'.$this->displays['p_cat'].'/'.(int)$this->id);
						else redirect("Myproducts/Show/".$this->displays['p_cat']); exit();
			}
	}
	else {
		if ($from == 'Search') Redirect("/Myproducts/Search");
		else redirect("/Myproducts/Show/".$this->cat."/");
	}
}
function DoWaterMark($cat, $id, $place = 1)
	{
		$img = $this->Myproducts_model->GetOldProductImage((int)$id, $place);

		if ($img)
		{
			$this->_WaterMark('bottom', 'right', 'wm_original_bottom.png', $this->config->config['paths']['imgproducts'], $img);
			$this->_WaterMark('middle', 'center', 'wm_original_center.png', $this->config->config['paths']['imgproducts'], $img);
			$this->_WaterMark('bottom', 'right', 'wm_bigtn_bottom.png', $this->config->config['paths']['imgproducts'], 'thumb_main_'.$img);
			$this->_WaterMark('middle', 'center', 'wm_bigtn_center.png', $this->config->config['paths']['imgproducts'], 'thumb_main_'.$img);
			$this->_WaterMark('bottom', 'right', 'wm_smalltn_bottom.png', $this->config->config['paths']['imgproducts'], 'thumb_'.$img);
		}
	$place++;

	if ($place >4) redirect("/Myproducts/Show/".(int)$cat);
	else Redirect('Myproducts/DoWaterMark/'.(int)$cat.'/'.(int)$id.'/'.$place);



	}




function Add($cat, $type)
	{
		$this->cat = (int)$cat;
		$this->type =(int)$type;

		if ($this->cat > 0) {

		$this->mysmarty->assign('cat', $this->cat);
		$this->mysmarty->assign('catlist', $this->Myproducts_model->GetAllCategories());
		$this->mysmarty->assign('catitemammounts', $this->Myproducts_model->GetAllAmmounts());
		$this->session->set_flashdata('cat', $this->cat);

		$this->load->library('form_validation');

		$this->form_validation->set_rules('p_title', 'Title', 'trim|required|min_length[3]|xss_clean');
		$this->form_validation->set_rules('p_order', 'Order', 'trim|required|xss_clean');
		if ($this->type == 2) $this->form_validation->set_rules('p_price', 'Price', 'trim|required|xss_clean');
		else $this->form_validation->set_rules('p_price', 'Price', 'trim|required|xss_clean|callback__checkifzero');


		$this->form_validation->set_rules('p_lbs', 'Lbs', 'trim|required|xss_clean');
		$this->form_validation->set_rules('p_oz', 'Oz', 'trim|required|xss_clean');
		$this->form_validation->set_rules('p_theirprice', 'Distributers', 'trim|xss_clean');
		$this->form_validation->set_rules('p_condition', 'Condition', 'trim|xss_clean');

		if ((int)$this->type == 1) //((int)$this->cat == '34')
		{
			$this->form_validation->set_rules('p_toptxt', 'Toplist Text', 'trim|xss_clean');
		}

		if ($this->form_validation->run() == FALSE)
			{
				$this->inputdata = array(
									'p_title' => addslashes($this->input->post('p_title', TRUE)),
									'p_order' => (int)$this->input->post('p_order'),
									'p_price' => (float)PriceUnification($this->input->post('p_price')),
									'p_theirprice' => (float)PriceUnification($this->input->post('p_theirprice')),
									'p_lbs' => (int)$this->input->post('p_lbs'),
									'p_oz' => (float)$this->input->post('p_oz'),
									'p_quantity' => (int)$this->input->post('p_quantity'),
									'p_condition' => $this->input->post('p_condition'),
									'p_desc' => $this->input->post('p_desc', TRUE)
									);

				if (isset($_POST['p_freegrship'])) $this->inputdata['p_freegrship'] = 1;
				if (isset($_POST['p_shipping'])) $this->inputdata['p_shipping'] = 1;

				if ($this->type == 1) //((int)$this->cat == '34')
				{
					$this->inputdata['p_toptxt'] = $this->input->post('p_toptxt', TRUE);
				}


				$this->displays = $this->Myproducts_model->GetMaxOrder($this->cat);

				require_once($this->config->config['pathtopublic'].'/fckeditor/fckeditor.php');

				$this->editor = new FCKeditor('p_desc');
				if ((count($_POST) >	0) OR ($this->type == 1) OR ($this->type == 2)) $this->editor->Value = $this->inputdata['p_desc'];
				else $this->editor->Value = '<strong>Manufacturer:</strong>
<br>
<br>
<strong>Part Number:</strong>
<br>
<br>
<strong>Compatibility:</strong>
<br>
<br>
<strong>Package:</strong>
<br>
<br>
<strong>Description:</strong>
<br>
<br>';
				$this->editor->Width = "650";
				$this->editor->Height = "400";
				$this->inputdata['p_desc'] = $this->editor->CreateHtml();

				if ($this->type == 1) //((int)$this->cat == '34')
				{

					$this->editortoptxt = new FCKeditor('p_toptxt');
					if (count($_POST) >	0) $this->editortoptxt->Value = $this->inputdata['p_toptxt'];
					else $this->editortoptxt->Value = '';
					$this->editortoptxt->Width = "650";
					$this->editortoptxt->Height = "400";
					$this->inputdata['p_toptxt'] = $this->editortoptxt->CreateHtml();


				}


				$this->mysmarty->assign('displays', $this->displays);

				$this->mysmarty->assign('inputdata', $this->inputdata);
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);
				$this->mysmarty->assign('pTypes', array (0 => 'Product', 1 => 'Predefined Repair', '2' => 'Custom Repair'));
				$this->mysmarty->assign('type', $this->type);
				$this->mysmarty->view('myproducts/myproducts_add.html');
				exit();
			}
			else
			{
					$this->db_data = array(
											'p_title' => addslashes($this->form_validation->set_value('p_title')),
											'p_type' => $this->type,
											'p_sef' => $this->_CleanSef($this->form_validation->set_value('p_title')),
											'p_cat' => $this->cat,
											'p_order' => (int)$this->form_validation->set_value('p_order'),
											'p_price' => (float)PriceUnification($this->form_validation->set_value('p_price')),
											'p_theirprice' => (float)PriceUnification($this->form_validation->set_value('p_theirprice')),
											'p_lbs' => (int)$this->form_validation->set_value('p_lbs'),
											'p_oz' => (float)PriceUnification($this->form_validation->set_value('p_oz')),
											'p_quantity' => (int)$this->input->post('p_quantity', TRUE),
											'p_condition' => $this->input->post('p_condition', TRUE),
											'p_desc' => $this->input->post('p_desc', TRUE)
											);
					$this->_lbsozunify();

					if ((float)$this->db_data['p_theirprice'] == 0) $this->db_data['p_theirprice'] = $this->db_data['p_price'];
					$this->db_data['p_profit'] = $this->db_data['p_price'] - $this->db_data['p_theirprice'];

					if ($this->type == 1) //((int)$this->cat == '34')
					{
						$this->db_data['p_toptxt'] = $this->form_validation->set_value('p_toptxt');
						if (isset($_POST['p_freegrship'])) $this->db_data['p_freegrship'] = 1;
						else $this->db_data['p_freegrship'] = 0;
					}
					else
					{
						if (isset($_POST['p_shipping'])) $this->db_data['p_shipping'] = 1;
						else $this->db_data['p_shipping'] = 0;
					}

					$this->checkexists =  $this->Myproducts_model->CheckProductSefExists($this->db_data['p_sef']);
					if ($this->checkexists) $this->pref = rand(1,9).rand(1,9).rand(1,9); else $this->pref = '';
					$this->db_data['p_sef'] = $this->db_data['p_sef'].$this->pref;


					$this->load->library('upload');


					if (($this->type == 1) && $_FILES['p_ad']['name'] != '')
								{
									$adimage = $this->_UploadImage ('p_ad', $this->config->config['paths']['imgproducts'], FALSE, '123', '123',TRUE);
									if ($adimage) $this->db_data['p_ad'] = $adimage;
								}

						$this->newid = $this->Myproducts_model->Insert($this->db_data);

					///Update Images
						$this->productimages = array(1,2,3,4);
						$watermark = FALSE;
						foreach($this->productimages as $value)
						{			if ($_FILES['p_img'.$value]['name'] != '')
									{
										$this->newdb_data['p_origimg'.$value] = $this->_clean_file_name($_FILES['p_img'.$value]['name']);
										$newname[$value] = (int)$this->newid.'_'.substr($this->db_data['p_sef'], 0, 210).'_'.$value;
										$image[$value] = $this->_UploadImage ('p_img'.$value, $this->config->config['paths']['imgproducts'], TRUE, $this->config->config['sizes']['productimg']['width'], $this->config->config['sizes']['productimg']['height'], FALSE, TRUE, $newname[$value]);
										if ($image[$value]) {
											$this->newdb_data['p_img'.$value] = $image[$value];
											$watermark = TRUE;
										}
									}

						}
						if (isset($this->newdb_data)) $this->Myproducts_model->Update((int)$this->newid, $this->newdb_data);

						$this->RecreateSpecialTopImgsEbay();

						if ($watermark) Redirect('Myproducts/DoWaterMark/'.$this->cat.'/'.(int)$this->newid);
						else redirect("Myproducts/Show/".$this->cat."/");
			}
	}
	else {
		redirect("/Myproducts");
	}
}
function DeleteImageInProduct($id = '', $place = '', $nogo = FALSE)
	{
		$this->id = (int)$id;
		$this->place = (int)$place;
		if (($this->id > 0) && ($this->place > 0))
				{
				$this->img = $this->Myproducts_model->DeleteProductImage($this->id, $this->place);
				if ($this->img != '') {
					unlink($this->config->config['paths']['imgproducts'].'/'.$this->img);
					unlink($this->config->config['paths']['imgproducts'].'/thumb_'.$this->img);
					unlink($this->config->config['paths']['imgproducts'].'/thumb_main_'.$this->img);

					}
				}
		if (!$nogo) {
		$this->session->set_flashdata('cat', $this->session->flashdata('cat'));
		Redirect("/Myproducts/Edit/".$this->id);
		}
	}

function DeleteAdInProduct($id = '', $nogo = FALSE)
	{
		$this->id = (int)$id;
		if ($this->id > 0)
				{
				$this->img = $this->Myproducts_model->DeleteAdImage($this->id);
				if ($this->img != '') {
					unlink($this->config->config['paths']['imgproducts'].'/'.$this->img);
					}
				}

		if (!$nogo) {
		$this->session->set_flashdata('cat', $this->session->flashdata('cat'));
		Redirect("/Myproducts/Edit/".$this->id);
		}
	}
	///////////////////////////

function RecreateSpecialTopImgsEbay()
	{

		$this->listing = $this->Myproducts_model->GetTopSpecialsAds();
		foreach ($this->listing as $key => $value)
		{
			if ($value['p_ad'] != '')
			{
			$name = explode('.', $value['p_ad']);
			if (!copy($this->config->config['paths']['imgproducts'].'/'.$value['p_ad'], $this->config->config['paths']['imgebayspecials'].'/special'.($key+1).'.'.$name[1])) {
				echo "failed to copy file...\n";
				break;
			}
			$this->iconfig['image_library'] = 'gd2';
			$this->iconfig['source_image']	= $this->config->config['paths']['imgebayspecials'].'/special'.($key+1).'.'.$name[1];
			$this->iconfig['create_thumb'] = FALSE;
			$this->iconfig['maintain_ratio'] = TRUE;
			$this->iconfig['width']	= $this->config->config['sizes']['ebayspecialimg']['width'];
			$this->iconfig['height'] = $this->config->config['sizes']['ebayspecialimg']['height'];
			$this->load->library('image_lib');
			$this->image_lib->initialize($this->iconfig);
			$this->imagesresult = $this->image_lib->resize();
			if ( $this->imagesresult != '1') { printcool ($this->image_lib->display_errors()); exit; }
			$this->image_lib->clear();

			}
		}

	}


function ViewProducts($id = '')	{
		$this->id = (int)$id;
		if ($this->id == 0) exit();

		$this->mysmarty->assign('list', $this->Myproducts_model->ListProducts((int)$id));
		$this->mysmarty->assign('alllist', $this->Myproducts_model->ListAllProducts((int)$id));
		$this->mysmarty->assign('sid', $this->id);
		$this->mysmarty->view('myproducts/myproducts_listassoc.html');
}
function AddProduct($sid) {
		if ((int)$sid == 0) exit();

		if (isset($_POST)) {
		$this->Myproducts_model->AddProducts((int)$this->input->post('p_id'), (int)$sid);
		Redirect("/Myproducts/ViewProducts/".$sid);
		}

}
function DeleteProducts($sid, $spid)
{
		$this->Myproducts_model->DeleteProducts((int)$spid);
		Redirect("/Myproducts/ViewProducts/".$sid);
}

function _checkifzero($str)
	{
		if ((float)$str == 0)
		{
			$this->form_validation->set_message('_checkifzero', 'The %s field can not be zero');
			return FALSE;
		}
		else
		{
			return TRUE;
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

function _lbsozunify()
	{
	$addlbs = 0;
	if ($this->db_data['p_oz'] > 16)
		{
		while ($this->db_data['p_oz'] > 16)
			{
			$this->db_data['p_oz'] = $this->db_data['p_oz'] - 16;
			$addlbs++;
			}
		}
	$this->db_data['p_lbs'] = $this->db_data['p_lbs'] + $addlbs;
	}

function _UploadImage ($fieldname = '', $configpath = '', $thumb = FALSE, $width = '', $height = '', $justupload = FALSE, $wm = FALSE, $filename = FALSE, $singlefile = FALSE)
	{
		if (($fieldname != '') || ($configpath != '') || ((int)$width != 0) || ((int)$height != 0))
		{

						$uconfig['upload_path'] = $configpath;
						$uconfig['allowed_types'] = 'gif|jpg|png|bmp';
						$uconfig['remove_spaces'] = TRUE;
						$uconfig['max_size'] = '1900';
						$uconfig['max_filename'] = '240';
						if ($filename)$uconfig['file_name'] = $filename;
						//printcool ($filename);
						//printcool( $uconfig);

						$this->upload->initialize($uconfig);
						$this->uploadresult = $this->upload->do_upload($fieldname);
						$processimgdata = $this->upload->data();
						//printcool($processimgdata['file_name']);
						if ( !$this->uploadresult) { printcool ($this->upload->display_errors()); exit; }

						if (!$justupload) {
						if (($processimgdata['image_width'] > $width) || ($processimgdata['image_height'] > $height))
						{
								$this->iconfig['image_library'] = 'gd2';
								$this->iconfig['source_image']	= $configpath.'/'.$processimgdata['file_name'];
								if (!$thumb) $this->iconfig['create_thumb'] = FALSE;
								else $this->iconfig['create_thumb'] = TRUE;
								$this->iconfig['maintain_ratio'] = TRUE;

								$this->iconfig['width']	= $width;
								$this->iconfig['height'] = $height;

							$this->load->library('image_lib');
							$this->image_lib->initialize($this->iconfig);
							$this->imagesresult = $this->image_lib->resize();
							if ( $this->imagesresult != '1') { printcool ($this->image_lib->display_errors()); exit; }
							$this->image_lib->clear();

							if (!$singlefile) {
							$this->nconfig['image_library'] = 'gd2';
								$this->nconfig['source_image']	= $configpath.'/'.$processimgdata['file_name'];
								$this->nconfig['maintain_ratio'] = TRUE;
								$this->nconfig['new_image'] = 'main_'.$processimgdata['file_name'];
								$this->nconfig['width']	= '200';
								$this->nconfig['height'] = '200';


							$this->image_lib->initialize($this->nconfig);
							$this->imagesresult = $this->image_lib->resize();
							if ( $this->imagesresult != '1') { printcool ($this->image_lib->display_errors()); exit; }
							$this->image_lib->clear();
							}
						}

						}
		//sleep(0.5);
		return ($processimgdata['file_name']);
		}
}
function _WaterMark($val, $hal, $wm, $path = '', $file = '')
{
							$this->load->library('image_lib');
							$config['source_image']	= $path.'/'.$file;
							$config['wm_type'] = 'overlay';
							$config['wm_overlay_path'] = $this->config->config['pathtopublic'].'/images/'.$wm;
							$config['wm_vrt_alignment'] = $val;
							$config['wm_hor_alignment'] = $hal;
							$config['create_thumb'] = FALSE;
							$config['wm_padding'] = '0';
							//printcool ($config);
							$this->image_lib->initialize($config);
							$this->image_lib->watermark();
							//printcool ($this->image_lib->display_errors());
							$this->image_lib->clear();

}

function _clean_file_name($filename)
	{
		$bad = array(
						"<!--",
						"-->",
						"'",
						"<",
						">",
						'"',
						'&',
						'$',
						'=',
						';',
						'?',
						'/',
						"%20",
						"%22",
						"%3c",		// <
						"%253c", 	// <
						"%3e", 		// >
						"%0e", 		// >
						"%28", 		// (
						"%29", 		// )
						"%2528", 	// (
						"%26", 		// &
						"%24", 		// $
						"%3f", 		// ?
						"%3b", 		// ;
						"%3d"		// =
					);

		$filename = str_replace($bad, '', $filename);

		return stripslashes($filename);
	}

}
