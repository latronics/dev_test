<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Mycatmappings extends Controller 
{
	
	function Mycatmappings()
	{
		parent::Controller();

		$this->load->model('Auth_model');
		$this->load->model('Myebay_model');
		$this->load->model('Product_model');
		//$this->load->helper('url'); 

		$this->Auth_model->VerifyAdmin();
		$this->mysmarty->assign('session',$this->session->userdata);
		$this->mysmarty->assign('action',$this->session->flashdata('action'));
		$this->mysmarty->assign('error_msg',$this->session->flashdata('error_msg'));
		$this->mysmarty->assign('success_msg',$this->session->flashdata('success_msg'));
        $this->mysmarty->assign('searchcat', TRUE);

       // $this->mysmarty->assign('wrapbootstrap', TRUE);


        //We need loaded <link rel="stylesheet" href="http://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
        //<script src="http://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
        //so qta must be TRUE

        $this->mysmarty->assign('gta', TRUE);

		$this->mysmarty->assign('newlayout', TRUE);
		$this->mysmarty->assign('jslog', TRUE);

        

	}
	function index()
	{


	}
    function EditCategory($e_id)
	{
        if(isset($_POST['eid_cat']))
        {
           //echo '<p>We have POST eid='.$_POST['eid_cat'];
            
            $query = $this->db->query('select storeCatID, primaryCategory, categoryEbaySecondaryId, categoryEbaySecondaryTitle, categoryAmazonId, categoryGoogleId from ebay where e_id='.$_POST['eid_cat']);

            $row = $query->row(0);
            $row->storeCatID;
            $row->primaryCategory;
            $row->secondaryCategoryId;
            $row->categoryAmazonId;
            $row->categoryGoogleId;

            if(isset($_POST['CatStore']) AND isset($_POST['StoreCatTitle']) AND $_POST['CatStore']!=0 AND (int)$_POST['CatStore']!=(int)$row->storeCatID)
            {
                $this->db->set('storeCatID', $_POST['CatStore'] , FALSE);
                $this->db->set('storeCatTitle', $_POST['StoreCatTitle']);
                $this->db->where('e_id', (int)$_POST['eid_cat']);
                $this->db->update('ebay');   
            }

            if(isset($_POST['eBayPrimCatTitle']) AND isset($_POST['CatPrimEbay']) AND $_POST['CatPrimEbay']!=0 AND $_POST['CatPrimEbay']!=$row->primaryCategory)
            {
               
                $this->db->set('primaryCategory', $_POST['CatPrimEbay'] , FALSE);
                $this->db->set('pCTitle', $_POST['eBayPrimCatTitle']);
                $this->db->where('e_id', (int)$_POST['eid_cat']);
                $this->db->update('ebay'); 
            }
            
            if(isset($_POST['eBaySecCatTitle']) AND isset($_POST['CatSecEbay']) AND $_POST['CatSecEbay']!=0 AND $_POST['CatSecEbay']!= $row->secondaryCategoryId)
            {
            
                $this->db->set('categoryEbaySecondaryId', $_POST['CatSecEbay'] , FALSE);
                $this->db->set('categoryEbaySecondaryTitle', $_POST['eBaySecCatTitle']);

                $this->db->where('e_id', (int)$_POST['eid_cat']);
                $this->db->update('ebay'); 
            }
            
            if(isset($_POST['CatAmazon']) AND $_POST['CatAmazon']!=0 AND $_POST['CatAmazon']!=$row->categoryAmazonId)
            {
            
                 $this->db->set('categoryAmazonId', $_POST['CatAmazon'] , FALSE);
                 $this->db->where('e_id', (int)$_POST['eid_cat']);
                 $this->db->update('ebay'); 
            }

            if(isset($_POST['CatGoogle']) AND $_POST['CatGoogle']!=0 AND $_POST['CatGoogle']!=$row->categoryGoogleId AND isset($_POST['gtaxonomyTitle']))
            {
                 $this->db->set('categoryGoogleId', $_POST['CatGoogle'] , FALSE);
                 $this->db->set('gtaxonomy', $_POST['gtaxonomyTitle']);
                 $this->db->where('e_id', (int)$_POST['eid_cat']);
                 $this->db->update('ebay'); 
            }
             
            $this->db->set('primaryCategory', $_POST['CatPrimEbay'] , FALSE);
                $this->db->set('pCTitle', $_POST['eBayPrimCatTitle']);
                $this->db->where('e_id', (int)$_POST['eid_cat']);
                $this->db->update('ebay'); 
         
            
        }
        else //IF NO POST then we HAVE COME FROM http://www.vic.la-tronics.com/Mysku/Listing
        {
            //echo 'NO POST and e_id is '.$e_id;

            $this->mysmarty->assign('e_id', $e_id);

            $queryeStore = $this->db->query('select id, id_store, store_cat_title from categories_store');
		    $queryeGoogle = $this->db->query('select id, id_google, google_cat_title from categories_google');
		    $queryeAmazon = $this->db->query('select id, id_amazon, amazon_cat_title from categories_amazon');
            $queryeBay1 = $this->db->query('select distinct primaryCategory, pCTitle from ebay where primaryCategory is not null and primaryCategory<>0 and pCTitle is not null');
            $queryeBay2 = $this->db->query('select distinct categoryEbaySecondaryId, categoryEbaySecondaryTitle from ebay where categoryEbaySecondaryId is not null and categoryEbaySecondaryId<>0 and categoryEbaySecondaryTitle is not null');


		    //printcool($queryeBay->result_array());
		    foreach ( $queryeStore->result_array() as $row)
		    { 
			    $storeCategories[$row['id_store']]=$row['store_cat_title']; 
           
		    }
            $storeCategories[0]='';

		    foreach ($queryeGoogle->result_array() as $row)
		    { 
			    $googleCategories[$row['id_google']]=$row['google_cat_title']; 
           
		    }
             $googleCategories[0]='';

		    foreach ($queryeAmazon->result_array() as $row)
		    { 
			    $amazonCategories[$row['id_amazon']]=$row['amazon_cat_title']; 
           
		    }
            $amazonCategories[0]='';

            foreach ($queryeBay1->result_array() as $row)
		    { 
			    $ebayCategories1[$row['primaryCategory']]=$row['pCTitle']; 
          
		    }
		    $ebayCategories1[0]='';

            foreach ($queryeBay2->result_array() as $row)
		    { 
			    $ebayCategories2[$row['categoryEbaySecondaryId']]=$row['categoryEbaySecondaryTitle']; 
          
		    }
		    $ebayCategories2[0]='';
		  
            //printcool($ebayCategories);


            $query = $this->db->query('select storeCatID, primaryCategory, categoryEbaySecondaryId,categoryEbaySecondaryTitle, categoryAmazonId, categoryGoogleId from ebay where e_id='.$e_id);

            $row = $query->row(0);
            $this->mysmarty->assign('mySelectStore',($row->storeCatID=='')? 0:$row->storeCatID);
            $this->mysmarty->assign('mySelectEbayFirst',($row->primaryCategory=='')? 0:$row->primaryCategory);
            $this->mysmarty->assign('mySelectEbaySecond',($row->categoryEbaySecondaryId=='')? 0:$row->categoryEbaySecondaryId);
            $this->mysmarty->assign('mySelectAmazon',($row->categoryAmazonId=='')? 0:$row->categoryAmazonId);
            $this->mysmarty->assign('mySelectGoogle',($row->categoryGoogleId=='')? 0:$row->categoryGoogleId);
           // echo '<p>'.$row->storeCatID;
		    $this->mysmarty->assign('myCatsStore', $storeCategories);
            $this->mysmarty->assign('myCatsEbay1', $ebayCategories1);
            $this->mysmarty->assign('myCatsEbay2', $ebayCategories2);
            $this->mysmarty->assign('myCatsAmazon',$amazonCategories);
            $this->mysmarty->assign('myCatsGoogle', $googleCategories);

            $this->mysmarty->assign('myCatsGoogle1', json_encode($googleCategories));

            $this->mysmarty->assign('searchcat','Computers');
            $this->mysmarty->view('mycategories/mycategories_mapping.html');
        }

         
    }
    
	function GetCategories()
	{
		//Computers/Tablets & Networking #58058
        //Consumer Electronics #293
        //Cameras & Photo #625
        //Cell Phones & Accessories #15032

        set_time_limit(180);
		ini_set('mysql.connect_timeout', 180);
		ini_set('max_execution_time', 180);  
		ini_set('default_socket_timeout', 180); 
		
		require_once($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
		
		$verb = 'GetCategories';
		
		//Create a new eBay session with all details pulled in from included keys.php
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);

		$requestXmlBody  = '<?xml version="1.0" encoding="utf-8"?>
							<GetCategoriesRequest xmlns="urn:ebay:apis:eBLBaseComponents">
							  <RequesterCredentials>
								<eBayAuthToken>'.$userToken.'</eBayAuthToken>
							  </RequesterCredentials>
							   <DetailLevel>ReturnAll</DetailLevel>
                               <CategoryParent>58058</CategoryParent>
							</GetCategoriesRequest>';
        // <LevelLimit>2</LevelLimit><CategorySiteID>58058</CategorySiteID>
		$responseXml = $session->sendHttpRequest($requestXmlBody);
		if(stristr($responseXml, 'HTTP 404') || $responseXml == '') die('<P>Error sending request');
		$xml = simplexml_load_string($responseXml);

		//printcool($xml);

		//$cats = $xml->CategoryArray;
        $cats = $xml;
		//echo '<select id="primaryCategory" name="primaryCategory">';
		
		//printcool($cats); 

		foreach ($cats->CategoryArray->Category as $c)
		{
			//$c = $this->_XML2Array($c);

           // printcool($c); 

           // $y = $c->CategoryName;
            //['CategoryName'];

            //echo '<br>Category = '.$c->CategoryID;
            $data = array(
                        'id_ebay' => $c->CategoryID,
                        'ebay_cat_title' => "'".$c->CategoryName."'"
                        );
            $this->db->insert('categories_ebay', $data);
           //$this->db->update('categories_ebay', array('id_ebay' => (int)$c->CategoryID),  array('ebay_cat_title' => $c->CategoryName));
           //$this->db->set('id_ebay', (int)$c->CategoryID);
           //$this->db->set('ebay_cat_title', $c->CategoryName);
           //$this->db->insert('categories_ebay'); 
           //$this->db->update('categories_ebay', $data);
           
		}
	
        echo '<br>Ready';
	}

	function GetSuggestedCategoriesPrimary($searchstring = '')
	{
		if (isset($_POST['src'])) $searchstring = trim($_POST['src']);
		if ($searchstring == '') return 'No search string inputed';
	
		//echo '<input id="catsearch" name="catsearch" value="'.$searchstring.'" style="width:250px;">&nbsp;&nbsp;<a href="javascript:void(0)" onClick="var csrc = document.getElementById(\'catsearch\').value; SelectShipping(csrc)"><img src="'.base_url().'images/admin/b_search.png" /> Get eBay Suggested</a><br><br>';
	
		set_time_limit(180);
		ini_set('mysql.connect_timeout', 180);
		ini_set('max_execution_time', 180);  
		ini_set('default_socket_timeout', 180); 
		
		require_once($this->config->config['ebaypath'].'get-common/keys.php');
		require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
		
		$verb = 'GetSuggestedCategories';
		
		//Create a new eBay session with all details pulled in from included keys.php
		$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
		
		$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
		$requestXmlBody .= '<GetSuggestedCategoriesRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
		$requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';
		$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
		$requestXmlBody .= '<ErrorHandling>BestEffort</ErrorHandling>';
		$requestXmlBody .= '<Query>'.$searchstring.'</Query>'; 
		$requestXmlBody .= "<Version>$compatabilityLevel</Version>";
		$requestXmlBody .=  '</GetSuggestedCategoriesRequest>';			
		$responseXml = $session->sendHttpRequest($requestXmlBody);
		if(stristr($responseXml, 'HTTP 404') || $responseXml == '') die('<P>Error sending request');
		$xml = simplexml_load_string($responseXml);
		$cats = $xml->SuggestedCategoryArray->SuggestedCategory;
		
        echo '<select id="ebay1" name="ebay1">';
		
        foreach ($cats as $c)
		{
			$c = $this->_XML2Array($c);
			$c = $c['Category'];
			$cgcstr = '';
			echo '<option value="'.$c['CategoryID'].'">';
						
            if(isset($c['CategoryParentID']) && (count($c['CategoryParentID']) > 0))
                {
                    /*foreach ($c['CategoryParentID'] as $k => $v)
                    {
                        echo $c['CategoryParentName'][$k].' <strong>&rArr;</strong> ';		//('.$v.')						
                    }*/							
                    if (is_array($c['CategoryParentID'])) foreach ($c['CategoryParentName'] as $v)
                        {
                        $cgcstr .= $v.' <strong>&rArr;</strong> ';
                        echo $v.' <strong>&rArr;</strong> ';	
                        }//('.$v.')	
                    else 
                    {
                        $cgcstr .= $c['CategoryParentName'].' <strong>&rArr;</strong> ';
                        echo $c['CategoryParentName'].' <strong>&rArr;</strong> ';	
                    }
                }

					
		//echo '<strong><input onlick="javascript:void(0)" onClick="SaveShipping('.(int)$c['CategoryID'].', '.$c['CategoryName'].', '.$searchstring.')" type="radio" id="'.(int)$c['CategoryID'].'" value="'.$c['CategoryName'].'" name="primaryCategory" /> <label for="'.(int)$c['CategoryID'].'"></label></strong>
		$cgcstr .= $c['CategoryName'];
		echo '<strong>'.$c['CategoryName'].'</strong>';
		echo '</option>';
				
		$gotcats[] = array('catID' => $c['CategoryID'], 'catName' => $cgcstr);
				
		/*
		&nbsp;&nbsp;
		<input type="hidden" id="id'.$c['CategoryID'].'" value="'.$c['CategoryID'].'">
		<input type="hidden" id="name'.$c['CategoryID'].'" value="'.$c['CategoryName'].'">
		<input type="hidden" id="ss'.$c['CategoryID'].'" value="'.$searchstring.'">
				
		&nbsp;&nbsp;<a href="javascript:void(0)" onclick="var cid = document.getElementById(id'.$c['CategoryID'].').value; var cname = document.getElementById(name'.$c['CategoryID'].').value; var css = document.getElementById(\ss'.$c['CategoryID'].').value; SaveShipping(cid, cname, css)" style=" color:#0099FF;">SELECT</a><Br><br>';*/
			// ('.$c['CategoryID'].')
		}
		echo '</select>';
		//printcool ($xml);
				
		if (isset($gotcats) && (count($gotcats) > 0)) $this->session->set_userdata(array('gotcats' => $gotcats));
	
	}

    function GetSuggestedCategoriesSecondary($searchstring = '')
	{
		if (isset($_POST['src'])) $searchstring = trim($_POST['src']);
		if ($searchstring == '') return 'No search string inputed';
	
		//echo '<input id="catsearch" name="catsearch" value="'.$searchstring.'" style="width:250px;">&nbsp;&nbsp;<a href="javascript:void(0)" onClick="var csrc = document.getElementById(\'catsearch\').value; SelectShipping(csrc)"><img src="'.base_url().'images/admin/b_search.png" /> Get eBay Suggested</a><br><br>';
	
		set_time_limit(180);
        ini_set('mysql.connect_timeout', 180);
        ini_set('max_execution_time', 180);  
        ini_set('default_socket_timeout', 180); 
		
        require_once($this->config->config['ebaypath'].'get-common/keys.php');
        require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
		
        $verb = 'GetSuggestedCategories';
		
        //Create a new eBay session with all details pulled in from included keys.php
        $session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
		
		$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
		$requestXmlBody .= '<GetSuggestedCategoriesRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
		$requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';
		$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
		$requestXmlBody .= '<ErrorHandling>BestEffort</ErrorHandling>';
		$requestXmlBody .= '<Query>'.$searchstring.'</Query>'; 
		$requestXmlBody .= "<Version>$compatabilityLevel</Version>";
		$requestXmlBody .=  '</GetSuggestedCategoriesRequest>';			
		$responseXml = $session->sendHttpRequest($requestXmlBody);
		if(stristr($responseXml, 'HTTP 404') || $responseXml == '') die('<P>Error sending request');
		
        $xml = simplexml_load_string($responseXml);
		$cats = $xml->SuggestedCategoryArray->SuggestedCategory;
		echo '<select id="ebay2" name="ebay2">';
		
        foreach ($cats as $c)
		{
			$c = $this->_XML2Array($c);
			$c = $c['Category'];
			$cgcstr = '';
			echo '<option value="'.$c['CategoryID'].'">';
						
            if(isset($c['CategoryParentID']) && (count($c['CategoryParentID']) > 0))
				{
					/*foreach ($c['CategoryParentID'] as $k => $v)
					{
						echo $c['CategoryParentName'][$k].' <strong>&rArr;</strong> ';		//('.$v.')						
					}*/							
					if (is_array($c['CategoryParentID'])) foreach ($c['CategoryParentName'] as $v)
						{
						$cgcstr .= $v.' <strong>&rArr;</strong> ';
						echo $v.' <strong>&rArr;</strong> ';	
						}//('.$v.')	
					else 
					{
						$cgcstr .= $c['CategoryParentName'].' <strong>&rArr;</strong> ';
						echo $c['CategoryParentName'].' <strong>&rArr;</strong> ';	
					}
				}

					
		//echo '<strong><input onlick="javascript:void(0)" onClick="SaveShipping('.(int)$c['CategoryID'].', '.$c['CategoryName'].', '.$searchstring.')" type="radio" id="'.(int)$c['CategoryID'].'" value="'.$c['CategoryName'].'" name="primaryCategory" /> <label for="'.(int)$c['CategoryID'].'"></label></strong>
		$cgcstr .= $c['CategoryName'];
		echo '<strong>'.$c['CategoryName'].'</strong>';
		echo '</option>';
				
		$gotcats[] = array('catID' => $c['CategoryID'], 'catName' => $cgcstr);
				
		/*
		&nbsp;&nbsp;
		<input type="hidden" id="id'.$c['CategoryID'].'" value="'.$c['CategoryID'].'">
		<input type="hidden" id="name'.$c['CategoryID'].'" value="'.$c['CategoryName'].'">
		<input type="hidden" id="ss'.$c['CategoryID'].'" value="'.$searchstring.'">
				
		&nbsp;&nbsp;<a href="javascript:void(0)" onclick="var cid = document.getElementById(id'.$c['CategoryID'].').value; var cname = document.getElementById(name'.$c['CategoryID'].').value; var css = document.getElementById(\ss'.$c['CategoryID'].').value; SaveShipping(cid, cname, css)" style=" color:#0099FF;">SELECT</a><Br><br>';*/
			// ('.$c['CategoryID'].')
		}
		echo '</select>';
		//printcool ($xml);
				
		if (isset($gotcats) && (count($gotcats) > 0)) $this->session->set_userdata(array('gotcats' => $gotcats));
	
	}
 
   function _XML2Array($parent)
   {
        $array = array();

        foreach ($parent as $name => $element) {
            ($node = & $array[$name])

                && (1 === count($node) ? $node = array($node) : 1)
                && $node = & $node[];

            $node = $element->count() ? $this->_XML2Array($element) : trim($element);
        }

        return $array;
    }

}
