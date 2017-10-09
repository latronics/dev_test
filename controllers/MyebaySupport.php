<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MyebaySupport extends Controller {

function Myebay()
	{
		parent::Controller();		
		$this->load->model('Myebay_model'); 
		$this->load->model('Auth_model');
		$this->Auth_model->VerifyAdmin();
		if ($this->session->userdata['admin_id'] == 7) { echo 'Sorry, you don\'t have clearance for here.';exit();}
		if ($this->session->userdata['admin_id'] == 10) { echo 'Sorry, you don\'t have clearance for here.'; exit();}
		

	}
function index()
	{	
			echo 'HERE';
	}
	



function GetSellerList($page = 1)
{
exit();
//The time range between time from and time to has exceeded 121 days.

require_once($this->config->config['ebaypath'].'get-common/keys.php');
						require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');


						$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
						$requestXmlBody .= '<GetSellerListRequest xmlns="urn:ebay:apis:eBLBaseComponents">
';
						$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
						//$requestXmlBody .= '<DetailLevel>ItemReturnAttributes</DetailLevel>';
						$requestXmlBody .= '<GranularityLevel>Coarse</GranularityLevel>
						 <Pagination>
    <EntriesPerPage>150</EntriesPerPage>
    <PageNumber>'.$page.'</PageNumber>
  </Pagination>
  ';
						
						$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
						$requestXmlBody .= "<Version>$compatabilityLevel</Version>
						 <StartTimeFrom>".date('Y-m-d H:i:s', strtotime("-2 days"))."</StartTimeFrom>
  <StartTimeTo>".date('Y-m-d H:i:s')."</StartTimeTo>";
						
						
						$requestXmlBody .= '</GetSellerListRequest>';
						$verb = 'GetSellerList';
						$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
						$responseXml = $session->sendHttpRequest($requestXmlBody);
						if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
							die('<P>Error sending request');
						
						$xml = simplexml_load_string($responseXml);
printcool ('<a href="http://www.la-tronics.com/MyebaySupport/GetSellerList/'.($page+1).'">'.($page+1).'</a><Br>');
printcool($page);
printcool((int)$xml->PaginationResult->TotalNumberOfPages);
printcool((int)$xml->PaginationResult->TotalNumberOfEntries);

		
//printcool ($xml);

$this->db->select("e_id, startPrice, buyItNowPrice, ebay_id, mods");
$r = $this->db->get('ebay');
if ($r->num_rows() > 0) 
{
	$res = $r->result_array();
	foreach ($res as $r)
	{
		if ($r['ebay_id'] > 0) $items[$r['ebay_id']] = $r;
	}
}

//printcool ($items);
$count = 1;
$updates = 0;
foreach ($xml->ItemArray->Item as $i)
			{
				if (isset($items[(int)$i->ItemID]) && ((string)$i->SellingStatus->ListingStatus == 'Active')) 
				{ 
					$updates++;
					$priceval = preg_replace('/[^0-9\.]/', '', (float)$i->SellingStatus->CurrentPrice);
					$updt = array('from' => $items[(int)$i->ItemID]['buyItNowPrice'], 'to' => $priceval, 'date' => CurrentTime());
					if (strlen($items[(int)$i->ItemID]['mods']) > 15) $items[(int)$i->ItemID]['mods'] = unserialize($items[(int)$i->ItemID]['mods']);
					else $items[(int)$i->ItemID]['mods'] = array();
					 
					$items[(int)$i->ItemID]['mods'][] = $updt;

					//printcool (array('buyItNowPrice' => $priceval, 'startPrice' => $priceval, 'mods' => serialize($items[(int)$i->ItemID]['mods'])));
					
					$this->db->update('ebay', array('buyItNowPrice' => $priceval, 'startPrice' => $priceval, 'mods' => serialize($items[(int)$i->ItemID]['mods'])), array('e_id' => $items[(int)$i->ItemID]['e_id']));
					
					$this->db->insert('ebay_cron', array('e_id' => $items[(int)$i->ItemID]['e_id'], 'data' => serialize($updt), 'time' => CurrentTime()));
					
					//echo $i->ItemID.' - '.$i->SellingStatus->CurrentPrice.'<br>';	
				}
				$count++;
				

			}	
printcool ($count);
printcool ($updates);

//sleep(10);

//if ($page < (int)$xml->PaginationResult->TotalNumberOfPages) Redirect('MyebaySupport/GetSellerList/'.($page+1));
}








function TestItem()
{


require_once($this->config->config['ebaypath'].'get-common/keys.php');
						require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');


						$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
						$requestXmlBody .= '<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
';
						$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
						$requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';
						$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
						$requestXmlBody .= "<Version>$compatabilityLevel</Version>";						
						$requestXmlBody .= '<ItemID>181266429240</ItemID>
						</GetItemRequest>';
						$verb = 'GetItem';
						$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
						$responseXml = $session->sendHttpRequest($requestXmlBody);
						if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
							die('<P>Error sending request');
						
						$xml = simplexml_load_string($responseXml);

printcool ($xml);

}






function GetSellerEvents()
{


require_once($this->config->config['ebaypath'].'get-common/keys.php');
						require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');


						$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
						$requestXmlBody .= '<GetSellerEventsRequest xmlns="urn:ebay:apis:eBLBaseComponents">
';
						$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
						$requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';
						$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
						$requestXmlBody .= "<Version>$compatabilityLevel</Version>";						
						$requestXmlBody .= "<ModTimeFrom>".date('Y-m-d H:i:s', strtotime("-1 days"))."</ModTimeFrom>
  <ModTimeTo>".date('Y-m-d H:i:s')."</ModTimeTo></GetSellerEventsRequest>";
						$verb = 'GetSellerEvents';
						$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
						$responseXml = $session->sendHttpRequest($requestXmlBody);
						if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
							die('<P>Error sending request');
						
						$xml = simplexml_load_string($responseXml);

printcool ($xml);

}

















































function TestCategories()
{

		

exit();
/*$catid = '16145';

		$this->db->select('catID, catName, parentID, CategoryLevel');
		$this->db->where('catID', (int)$catid);
		$q = $this->db->get('ebaydata_categories');
		if ($q->num_rows() > 0) 
		{
			$c = $q->row_array();	
			$string = $c['catName'];
			
			while ($c['CategoryLevel'] > 1)
			{
				$this->db->select('catID, catName, parentID, CategoryLevel');			
				$this->db->where('catID', (int)$c['parentID']);
				$q = $this->db->get('ebaydata_categories');
				if ($q->num_rows() > 0) 
					{
					$c = $q->row_array();
					$string = $c['catName'].' / '.$string;
					}			
			}
		}
		
		echo $string;
		*/
		
		$this->db->select("e_id, primaryCategory, pCTitle");		
		//$this->db->limit(1000);
		$this->db->where('primaryCategory > ' , 0);
		//$this->db->where('e_id > ' , 6000);
		$this->db->order_by("e_id", "DESC");
		$this->query = $this->db->get('ebay');
		$count = 0;
		if ($this->query->num_rows() > 0)
		{
			foreach ($this->query->result_array() as $r)
			{
				if (strlen($r['pCTitle']) < 61)
				{
					$count++;
					printcool ($r);
					//printcool ($this->Myebay_model->GetEbayCategoryTitle($r['primaryCategory']));
					//$this->db->update('ebay', array('pCTitle' => $this->Myebay_model->GetEbayCategoryTitle($r['primaryCategory'])), array('e_id' => (int)$r['e_id']));
				 }
				}
		 
		 }
		 echo $count;
}




function ProcessEbayCategories()
	{

	set_time_limit(1500);
	//echo ini_get('mysql.connect_timeout'); // OUTPUT 60
	ini_set('mysql.connect_timeout',90);	
	//	echo ini_get('mysql.connect_timeout');

	require_once($this->config->config['ebaypath'].'get-common/keys.php');
	require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
	
	$requestXmlBody = '<?xml version="1.0" encoding="utf-8" ?>';
				$requestXmlBody .= '<GetCategoriesRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
				$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
				$requestXmlBody .= "<DetailLevel>ReturnAll</DetailLevel>"; //get the entire tree
				$requestXmlBody .= "<Item><Site>".$this->config->config['ebaysiteid']."</Site></Item>";
				$requestXmlBody .= "<ViewAllNodes>TRUE</ViewAllNodes>"; //Gets all nodes not just leaf nodes
				$requestXmlBody .= '</GetCategoriesRequest>';
				
				//Create a new eBay session with all details pulled in from included keys.php
				$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], 'GetCategories');
				$responseXml = $session->sendHttpRequest($requestXmlBody);
				if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
					die('<P>Error sending request');
				
				//Xml string is parsed and creates a DOM Document object
				$responseDoc = new DomDocument();
				$responseDoc->loadXML($responseXml);
				$responseDoc->save($this->config->config['ebaypath'].'/CatTree.xml');

	
	exit();
	
	/////////////////////
	$catTreeDoc = $this->_getEntireCategoryTree($devID, $appID, $certID, $compatabilityLevel,$this->config->config['ebaysiteid'], $userToken, $serverUrl);
	$categories = $catTreeDoc->getElementsByTagName('Category');
printcool ($categories);
break;
	foreach($categories as $cat)
	{	
		//get the ID and ParentID
		$catIDNode = $cat->getElementsByTagName('CategoryID');
		$catNameNode = $cat->getElementsByTagName('CategoryName');	
		$CategoryLevel = $cat->getElementsByTagName('CategoryLevel');
		$parentIDNode = $cat->getElementsByTagName('CategoryParentID');
		$leafNode = $cat->getElementsByTagName('LeafCategory');
		//if ID equals ParentID then it is a Top-Level category
		if ($leafNode->length > 0) $leafNode = 1;		
		else $leafNode = 0;
	
		$arr[] = array('catID'=>$catIDNode->item(0)->nodeValue,
														'catName'=>$catNameNode->item(0)->nodeValue,
														'CategoryLevel'=>$CategoryLevel->item(0)->nodeValue,
														'parentID'=>$parentIDNode->item(0)->nodeValue,
														'LeafCategory'=>$leafNode);
		
		/*$this->db->trans_start();
		$this->db->insert('ebaydata_categories', array('catID'=>$catIDNode->item(0)->nodeValue,
														'catName'=>$catNameNode->item(0)->nodeValue,
														'CategoryLevel'=>$CategoryLevel->item(0)->nodeValue,
														'parentID'=>$parentIDNode->item(0)->nodeValue,
														'LeafCategory'=>$leafNode)
													);	
		$this->db->trans_complete();*/
			
	}
	
		$doc = new DOMDocument('1.0');
		$doc->formatOutput = true;
		$root = $doc->createElement('root');
		$root = $doc->appendChild($root);
		foreach($arr as $key=>$value)
		{
		   $em = $doc->createElement($key);       
		   $text = $doc->createTextNode($value);
		   $em->appendChild($text);
		   $root->appendChild($em);		
		}			
		
		$doc->save($this->config->config['ebaypath'].'/CatTree.xml');
	
	echo 'COMPLETE';
	
	}



function DoEbayCategories($level = 1)
	{
		// LEVEL 1 - 6
	set_time_limit(1500);
	ini_set('mysql.connect_timeout',90);
	$spool = array();
	$xml = simplexml_load_file($this->config->config['ebaypath'].'/CatTree.xml');
		foreach ($xml->CategoryArray->Category as $cat)
		{
			$spool[(int)$cat->CategoryLevel][] = array('catID'=>(int)$cat->CategoryID,
				  'catName'=>(string)$cat->CategoryName,
				  'CategoryLevel'=>(int)$cat->CategoryLevel,
				  'parentID'=>(int)$cat->CategoryParentID,
				  'LeafCategory'=> (string)$cat->LeafCategory
				 );		
			

			/*
			if ((int)$cat->CategoryLevel == (int)$level) $this->db->insert('ebaydata_categories',  
			array('catID'=>(int)$cat->CategoryID,
				  'catName'=>(string)$cat->CategoryName,
				  'CategoryLevel'=>(int)$cat->CategoryLevel,
				  'parentID'=>(int)$cat->CategoryParentID,
				  'LeafCategory'=> $this->_TrueString($cat->LeafCategory))
				 );			*/
		}	
		
	printcool ($spool);
	}

























function _TrueString($str = '')
						{
							if ($str =='true') return 1;
							else return 0;
						}
						
						
						
						
						
						
						
						
						
						
						
						
						
	/*
function demo()
	{
		set_time_limit(1500); 
						require_once($this->config->config['ebaypath'].'get-common/keys.php');
						require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
				
						$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
						$requestXmlBody .= '<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
';
						$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
						$requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';
						$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
						$requestXmlBody .= "<Version>$compatabilityLevel</Version>";						
						$requestXmlBody .= '<ItemID>181070881135</ItemID>
						</GetItemRequest>';
						$verb = 'GetItem';
						$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
						$responseXml = $session->sendHttpRequest($requestXmlBody);
						if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
							die('<P>Error sending request');
						
						$xml = simplexml_load_string($responseXml);
					
					//printcool ($xml->Item);
					printcool ($xml->Item->Storefront->StoreCategoryID);
					printcool ($xml->Item->PrimaryCategory);
					printcool ($xml->Item->ShippingDetails);
					printcool ($xml->Item->ReturnPolicy);				
					
	}*/
	
	
	
function EditOld($itemid = '', $catID = 0)
	{	
		$this->id = (int)$itemid;
	
		if ($this->id > 0) {
		
		$this->displays = $this->Myebay_model->GetItem($this->id);			
		$this->_GetSpecialAndTree();
		$this->load->library('form_validation');

		$this->form_validation->set_rules('e_title', 'Title', 'trim|required|min_length[3]|xss_clean');
		$this->form_validation->set_rules('e_manuf', 'Manufacturer', 'trim|xss_clean');
		$this->form_validation->set_rules('e_model', 'Model', 'trim|xss_clean');
		$this->form_validation->set_rules('e_part', 'Part Number', 'trim|xss_clean');
		$this->form_validation->set_rules('e_compat', 'Compatibility', 'trim|xss_clean');
		$this->form_validation->set_rules('e_package', 'Package', 'trim|xss_clean');
		$this->form_validation->set_rules('e_condition', 'Condition', 'trim|xss_clean');
		$this->form_validation->set_rules('e_shipping', 'Shipping', 'trim|xss_clean');
		$this->form_validation->set_rules('e_desc', 'Description', 'trim|xss_clean');
		
		$this->form_validation->set_rules('listingType', 'Description', 'trim|xss_clean');
		$this->form_validation->set_rules('primaryCategory', 'Description', 'trim|xss_clean');
		//$this->form_validation->set_rules('pCTitle', 'Description', 'trim|xss_clean');
		$this->form_validation->set_rules('listingDuration', 'Description', 'trim|xss_clean');
		$this->form_validation->set_rules('startPrice', 'Description', 'trim|xss_clean');
		$this->form_validation->set_rules('buyItNowPrice', 'Description', 'trim|xss_clean');
		$this->form_validation->set_rules('quantity', 'Description', 'trim|xss_clean');
		//$this->form_validation->set_rules('PaymentMethod', 'Description', 'trim|xss_clean');
		$this->form_validation->set_rules('Subtitle', 'Description', 'trim|xss_clean');
		$this->form_validation->set_rules('Condition', 'Description', 'trim|xss_clean');
		
		if ($this->form_validation->run() == FALSE)
			{	
				$this->inputdata = array(										
									'e_title' => $this->input->post('e_title', TRUE),
									'e_manuf' => $this->input->post('e_manuf', TRUE),
									'e_model' => $this->input->post('e_model', TRUE),
									'e_part' => $this->input->post('e_part', TRUE),
									'e_compat' => $this->input->post('e_compat', TRUE),
									'e_package' => $this->input->post('e_package', TRUE),
									'e_condition' => $this->input->post('e_condition', TRUE),
									'e_shipping' => $this->input->post('e_shipping', TRUE),
									'e_notice_header' => (int)$this->input->post('e_notice_header', TRUE),
									'e_notice_shipping' => (int)$this->input->post('e_notice_shipping', TRUE),
									'e_shipping' => $this->input->post('e_shipping', TRUE),
									'e_desc' => $this->input->post('e_desc', TRUE),
									
									'listingType' => $this->input->post('listingType', TRUE),
									'primaryCategory' => (int)$this->input->post('primaryCategory', TRUE),
									'listingDuration' => $this->input->post('listingDuration', TRUE),
									'startPrice' => $this->input->post('startPrice', TRUE),
									'buyItNowPrice' => $this->input->post('buyItNowPrice', TRUE),
									'quantity' => (int)$this->input->post('quantity', TRUE),
									'PaymentMethods' => $this->input->post('PaymentMethods', TRUE),
									'Subtitle' => $this->input->post('Subtitle', TRUE),
									'Condition' => $this->input->post('Condition', TRUE)									
									);
				/*if (isset($_POST['primaryCategory'])) $catID = $this->input->post('primaryCategory', TRUE);
				else $catID = $this->displays['primaryCategory'];
				printcool ($this->input->post('primaryCategory', TRUE));
				printcool ($this->displays['primaryCategory']);
				printcool ($catID);*/
				if (!$_POST) $catID = $this->displays['primaryCategory'];
				$this->mysmarty->assign('ebupd', TRUE);
				$this->mysmarty->assign('displays', $this->displays);
				$this->mysmarty->assign('categories', $this->Myebay_model->GetEbayDataCategories((int)$catID));	
				$this->mysmarty->assign('inputdata', $this->inputdata);				
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);
				$this->mysmarty->view('myebay/myebay_edit.html');
				exit();
			}
			else 
			{					
				
					$this->db_data = array(												 
											'e_title' => $this->form_validation->set_value('e_title'),
											'e_sef' => $this->_CleanSef($this->form_validation->set_value('e_title')),
											'e_manuf' => $this->form_validation->set_value('e_manuf'),
											'e_model' => $this->form_validation->set_value('e_model'),
											'e_part' => $this->form_validation->set_value('e_part'),
											'e_compat' => $this->form_validation->set_value('e_compat'),
											'e_package' => $this->form_validation->set_value('e_package'),
											'e_condition' => $this->form_validation->set_value('e_condition'),
											'e_shipping' => $this->form_validation->set_value('e_shipping'),
											'e_notice_header' => (int)$this->input->post('e_notice_header', TRUE),
											'e_notice_shipping' => (int)$this->input->post('e_notice_shipping', TRUE),
											'e_desc' => $this->form_validation->set_value('e_desc'),
											
											'listingType' => $this->form_validation->set_value('listingType'),
											'primaryCategory' => (int)$this->form_validation->set_value('primaryCategory'),
											'pCTitle' => $this->Myebay_model->GetEbayCategoryTitle((int)$this->form_validation->set_value('primaryCategory')),
											'listingDuration' => $this->form_validation->set_value('listingDuration'),
											'startPrice' => $this->form_validation->set_value('startPrice'),
											'buyItNowPrice' => $this->form_validation->set_value('buyItNowPrice'),
											'quantity' => (int)$this->form_validation->set_value('quantity'),
											'PaymentMethod' => serialize($this->input->post('PaymentMethods', TRUE)),
											'Subtitle' => $this->form_validation->set_value('Subtitle'),
											'Condition' => $this->form_validation->set_value('Condition')
									
											);
					
					
					if ($this->db_data['PaymentMethod'] == 'b:0;') $this->db_data['PaymentMethod'] ='';
					
					$this->checkexists =  $this->Myebay_model->CheckSefExists($this->db_data['e_sef'], $this->id);
					if ($this->checkexists) $this->pref = rand(1,9).rand(1,9).rand(1,9); else $this->pref = '';
					$this->db_data['e_sef'] = $this->db_data['e_sef'].$this->pref;
					
					$this->productimages = array(1,2,3,4);
					
					$this->load->library('upload');
					$watermark = FALSE;
					foreach($this->productimages as $value)
					{			if ($_FILES['e_img'.$value]['name'] != '') 
								{
									$this->_CheckImageDirExist(idpath((int)$this->id));
										
									$newname[$value] = (int)$this->id.'_'.substr($this->_CleanSef($this->db_data['e_title']), 0, 210).'_'.$value;
									$image[$value] = $this->_UploadImage ('e_img'.$value, $this->config->config['paths']['imgebay'].'/'.idpath((int)$this->id), TRUE, $this->config->config['sizes']['ebayimg']['width'], $this->config->config['sizes']['ebayimg']['height'], FALSE, TRUE, $newname[$value]);				
									if ($image[$value]) {
										$oldimage[$value] = $this->Myebay_model->GetOldEbayImage($this->id, $value);
										if ($oldimage[$value] != '' && $image[$value] != $oldimage[$value]) {
											if ($value == 1) unlink($this->config->config['paths']['imgebay'].'/'.idpath((int)$this->id).'Ebay_'.$oldimage[$value]);
											unlink($this->config->config['paths']['imgebay'].'/'.idpath((int)$this->id).$oldimage[$value]);
											unlink($this->config->config['paths']['imgebay'].'/'.idpath((int)$this->id).'thumb_'.$oldimage[$value]);
											unlink($this->config->config['paths']['imgebay'].'/'.idpath((int)$this->id).'thumb_main_'.$oldimage[$value]);
											}									
										
										$this->db_data['e_img'.$value] = $image[$value];
										$this->db_data['idpath'] = str_replace('/', '', idpath((int)$this->id));
										$watermark = TRUE;
									}	
								}
					}
				
						$this->Myebay_model->Update((int)$this->id,$this->db_data);
						$this->session->set_flashdata('success_msg', '"'.$this->db_data['e_title'].'" Updated');
						$this->session->set_flashdata('action', (int)$this->id);
						if ($watermark) Redirect('Myebay/DoWaterMark/'.(int)$this->id);
						else  redirect("Myebay/GetSource/".(int)$this->id);					
			}
	}
	else {
			redirect("/Myebay");
	}
}

















function ProcessEbayListing($page = 1)
{
	ini_set('mysql.connect_timeout', 300);
	ini_set('default_socket_timeout', 300);
		
		$this->db->select("e_id, e_title, ebay_id");
		$this->db->where('ebay_id', 0);		
		$this->db->order_by("e_id", "DESC");
		$this->query = $this->db->get('ebay');
		$data = $this->query->result_array();
		echo 'ROWS: '.count($data).'<br>';
	
		set_time_limit(1500); 
		require_once($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');

		$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
		$requestXmlBody .= '<GetSellerListRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
		$requestXmlBody .= '<DetailLevel>ItemReturnAttributes</DetailLevel>';
		$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
		$requestXmlBody .= "<Version>$compatabilityLevel</Version>";
		//http://developer.ebay.com/DevZone/XML/docs/Reference/eBay/types/GranularityLevelCodeType.html
		$dates = array('from' => date('Y-m-d H:i:s', strtotime("-90 days")), 'to' => date('Y-m-d H:i:s'));
		$requestXmlBody .= '
		<GranularityLevel>Fine</GranularityLevel>
		<StartTimeFrom>'.$dates['from'].'</StartTimeFrom>
		<StartTimeTo>'.$dates['to'].'</StartTimeTo>
		<Sort>1</Sort>
		<Pagination>
		<EntriesPerPage>50</EntriesPerPage>
		<PageNumber>'.(int)$page.'</PageNumber>
		</Pagination>
		</GetSellerListRequest>';
		
		$verb = 'GetSellerList';

		//Create a new eBay session with all details pulled in from included keys.php
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);

		//send the request and get response
		$responseXml = $session->sendHttpRequest($requestXmlBody);
		if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
			die('<P>Error sending request');
		
		$xml = simplexml_load_string($responseXml);
		//printcool ($xml);
		//exit();
		$tp = $xml->PaginationResult->TotalNumberOfPages;
		//printcool ($tp);

		$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
		$requestXmlBody .= '<GetStoreRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
		$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
		$requestXmlBody .= "<Version>$compatabilityLevel</Version>";		
		$requestXmlBody .= '<CategoryStructureOnly>TRUE</CategoryStructureOnly>
		<UserID>la.tronics</UserID></GetStoreRequest>';
		
		$verb = 'GetStore';

		//Create a new eBay session with all details pulled in from included keys.php
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
				
		//send the request and get response
		$responseXml = $session->sendHttpRequest($requestXmlBody);
		if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
			die('<P>Error sending request');
		
		$sxml = simplexml_load_string($responseXml);
		$sc = array();
		if (isset($sxml->Store->CustomCategories->CustomCategory))
		{
			foreach ($sxml->Store->CustomCategories->CustomCategory as $s)
			{
				$a = (array)$s;
				$sc[$a['CategoryID']] = $a['Name'];	
			}			
		}
		
		$k = 1;
		foreach($xml->ItemArray->Item as $i)
		{
			$l[$k] = $i;
			$l[$k]->storecat = $sc[(int)$i->Storefront->StoreCategoryID];
			$k++;	
		
			foreach ($data as $key => $v)
			{
				if ($v['e_title'] == $i->Title)
				{
					echo $v['e_id'].',';
					if (isset($sc[(int)$i->Storefront->StoreCategoryID])) $stcat = $sc[(int)$i->Storefront->StoreCategoryID];
					else $stcat = 'UNKNOWN';
					
				$update = array(
				'listingType' => addslashes($i->ListingType),
				'primaryCategory' => $i->PrimaryCategory->CategoryID,
				'pCTitle' => addslashes($i->PrimaryCategory->CategoryName),
				'storeCatID' => $i->Storefront->StoreCategoryID,
				'storeCatTitle' => $stcat,
				'listingDuration' => addslashes($i->ListingDuration),
				'startPrice' => $i->StartPrice,
				'quantity' => $i->Quantity,
				'Condition' => $i->ConditionID,
				'ebay_id' => $i->ItemID,
				'ebay_submitted' => CleanBadDate($i->ListingDetails->StartTime),
				'unsubmited' => 1
				); 
				$this->db->update('ebay', $update, array('e_id' => (int)$v['e_id']));
				
				
				/*
				echo "
				'listingType' => ".$i->ListingType."<br>
				'primaryCategory' => ".$i->PrimaryCategory->CategoryID."<br>
				'pCTitle' => ".$i->PrimaryCategory->CategoryName."<br>
				'storeCatID' => ".$i->Storefront->StoreCategoryID."<br>
				'storeCatTitle' => ".$sc[(int)$i->Storefront->StoreCategoryID]."<br>
				'listingDuration' => ".$i->ListingDuration."<br>
				'startPrice' => ".$i->StartPrice."<br>
				'quantity' => ".$i->Quantity."<br>
				'PaymentMethod' => ".$i->PaymentMethods."<br>
				'Condition' => ".$i->ConditionID."<br>
				'ebay_id' => ".$i->ItemID."<br>
				'ebay_submitted' => ".CleanBadDate($i->ListingDetails->StartTime)."<br>
				'unsubmited' => 1<br>
				<br><br>";
				*/
				}
			
				
			}
			$p = $page + 1;
			//echo 'sleep';<br>
			//sleep(3);
			
		
		}
	if ($p <= $tp) echo '<Br><br><a href="/MyebaySupport/ProcessEbayListing/'.$p.'">'.$p.' of '.$tp.'</a>';
		//printcool ($xml);
		
}


function EbayAutoRefresh($id, $itemid)
{
	
	set_time_limit(90);
						ini_set('mysql.connect_timeout', 90);
						ini_set('max_execution_time', 90);  
						ini_set('default_socket_timeout', 90); 
						require_once($this->config->config['ebaypath'].'get-common/keys.php');
						require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');


						$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
						$requestXmlBody .= '<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
';
						$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
						$requestXmlBody .= '<DetailLevel>ItemReturnAttributes</DetailLevel>';
						$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
						$requestXmlBody .= "<Version>$compatabilityLevel</Version>";						
						$requestXmlBody .= '<ItemID>'.(int)$itemid.'</ItemID>
						</GetItemRequest>';
						$verb = 'GetItem';
						$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
						$responseXml = $session->sendHttpRequest($requestXmlBody);
						if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
							die('<P>Error sending request');
						
						$xml = simplexml_load_string($responseXml);
						
						if ((string)$xml->Item->ItemID == '') { echo 'ERROR: Invalid Item ID...<a href="javascript:history.back()">Back</a>'; exit();}

						$this->load->helper('directory');
						$this->load->helper('file');
						
						$responseXml = read_file($this->config->config['ebaypath'].'/cats.txt');
						$store = simplexml_load_string($responseXml);						
					
						$sc = array();
						if (isset($store->Store->CustomCategories->CustomCategory))
						{
							foreach ($store->Store->CustomCategories->CustomCategory as $s)
							{
								$a = (array)$s;
								$sc[$a['CategoryID']] = $a['Name'];	
								
							}		
							if (!$save && isset($sc[(string)$xml->Item->Storefront->StoreCategoryID])) echo 'eBay Store Category: '.$sc[(string)$xml->Item->Storefront->StoreCategoryID].'<br>';	
						}
						
			
			$data = array('ebay_id' => (string)$xml->Item->ItemID, 
							'ebay_submitted' => 'Manual @ '.CurrentTime().' by '.$admins[$this->session->userdata['admin_id']],
							'quantity' => (string)$xml->Item->Quantity,
							'startPrice' => (string)$xml->Item->StartPrice,
							'buyItNowPrice' => (string)$xml->Item->StartPrice			
							);
			if ($save && isset($sc[(string)$xml->Item->Storefront->StoreCategoryID]))
			{
				$data['storeCatID'] = (string)$xml->Item->Storefront->StoreCategoryID;
				$data['storeCatTitle'] = $sc[(string)$xml->Item->Storefront->StoreCategoryID];
			}
			
			if ($save)
			{
			
				$this->db->select('e_id');
				$this->db->where('e_id', (int)$id);
				$this->db->where('ebay_id', (int)$itemid);
				$query = $this->db->get('ebay');
				if ($query->num_rows() > 0) 
				{
					$ebr = $query->row_array();						
					//$this->db->update('ebay', $data, array('e_id' => $ebr['e_id']));			
										
					
				}
		
			}
}



}