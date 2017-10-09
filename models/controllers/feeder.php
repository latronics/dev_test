<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class feeder extends Controller {

	function feeder()
	{		
		parent::Controller();	
						
	}
		
	function index() 
	{
		$this->load->helper('file');
		
		delete_files($this->config->config['paths']['feeds']);
		
		$indexhtml = '<html><head><title>Access Denied</title></head><body><p>You are not allowed to access directories.</p></body></html>';
		write_file($this->config->config['paths']['feeds'].'/index.html', $indexhtml);
		
		$this->load->model('Myproducts_model'); 
		$cats = $this->Myproducts_model->GetAllCategories();
		if (!isset($cats[1])) $cats = $cats[0];
		if ($cats)
		{
			foreach ($cats as $v)
				{
				$productlist = $this->Myproducts_model->ListXMLItems((int)$v['p_catid']);
				if ($productlist)
				{
					$feed = '<?xml version="1.0"?> 
							<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">
							<channel>
							<title>'.CleanXML($v['p_cattitle']).'</title>
							<link>http://www.365laptoprepair.com</link>
							<description>Products of category '.CleanXML($v['p_cattitle']).'</description>';	
				foreach ($productlist as $key => $value)
					{
					if ($value['p_img1'] == '') $value['p_img1'] = 'noimage_lrg.jpg';				
					$feed .= "
					<item>
						<title>".substr(CleanXML($value['p_title']), 0, 70)."</title>
						<link>".Site_url().'Product/'.$value['p_sef']."</link>
						<g:id>".$value['p_id']."</g:id>
						<g:price>".$value['p_price']."</g:price>
						<g:condition>".strtolower($value['p_condition'])."</g:condition>
						<g:product_type>Electronics &gt; Computers &gt; Laptops &gt; ".$v['p_cattitle']."</g:product_type>
						<g:image_link>".Site_url()."content/products/thumb_main_".$value['p_img1']."</g:image_link>
						<description>".CleanXML(strip_tags($value['p_desc']))."</description>
					</item>
					";
					}
					
				$feed .= '
						</channel>
						</rss>';		
				$name = $this->_CleanTitle($v['p_cattitle']).'.xml';
				write_file($this->config->config['paths']['feeds'].'/'.$name, $feed);
				unset($productlist);
				unset($feed);
				}
				}
		echo 'DONE';
	}
}
function _CleanTitle($string)
	{
	$string = str_replace(" ", "-", $string);
	$string = str_replace("_", "-", $string);	
	$string = str_replace('---', '-', $string);	
	$string = str_replace('--', '-', $string);
	$string = ereg_replace("[^A-Za-z0-9\-]", "", $string);	
	$string = strtoupper($string);
	$string = strtolower($string);
	return $string;
	}
}
