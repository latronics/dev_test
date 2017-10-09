<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mydev extends Controller {

function Mydev()
	{
		parent::Controller();	
		set_time_limit(1500); 		
		$this->load->model('Myebay_model'); 
	}
	
function index()
	{	
		echo '<h1>365 EBAY TESTER</h1><br><br>';	
		ini_set('include_path', ini_get('include_path') . ':.');

				// Load general helper classes for eBay SOAP API
				require_once $this->config->config['ebaypath'].'eBaySOAP/eBaySOAP.php';
				
				// Load developer-specific configuration data from ini file
				$config = parse_ini_file($this->config->config['ebaypath'].'ebay.ini', true);
				$site = $config['settings']['site'];
				$compatibilityLevel = $config['settings']['compatibilityLevel'];
				
				
				$dev = $config[$site]['devId'];
				$app = $config[$site]['appId'];
				$cert = $config[$site]['cert'];
				$token = $config[$site]['authToken'];
				$location = $config[$site]['gatewaySOAP'];
				
				// Create and configure session
				$session = new eBaySession($dev, $app, $cert);
				$session->token = $token;
				$session->site = 0; // 0 = US;
				$session->location = $location;
				
				// Make an AddItem API call and print Listing Fee and ItemID
				try {
					$client = new eBaySOAP($session);
				
					$PrimaryCategory = array('CategoryID' => 357);
				
					$Item = array('ListingType' => 'Chinese',
								  'Currency' => 'USD',
								  'Country' => 'US',
								  'PaymentMethods' => 'PaymentSeeDescription',
								  'RegionID' => 0,
								  'ListingDuration' => 'Days_3',
								  'Title' => 'The new item',
								  'Description' => "It's a great new item",
								  'Location' => "San Jose, CA",
								  'Quantity' => 1,
								  'StartPrice' => 24.99,
								  'PrimaryCategory' => $PrimaryCategory,
								 );
				
					$params = array('Version' => $compatibilityLevel, 'Item' => $Item);
					$results = $client->AddItem($params);
				
					// The $results->Fees['ListingFee'] syntax is a result of SOAP classmapping
					print "Listing fee is: " . $results->Fees['ListingFee'] . " <br> \n";
				
					print "Listed Item ID: " . $results->ItemID . " <br> \n";
					 
					print "Item was listed for the user associated with the auth token <br>\n";
				
				} catch (SOAPFault $f) {
					print $f; // error handling
				}
				
				// Uncomment below to view SOAP envelopes
				// print "Request: \n".$client->__getLastRequest() ."\n";
				// print "Response: \n".$client->__getLastResponse()."\n";
	
	
	}

function AddDirect($catID = 0)
	{
	//FOR REFERENCE
	
	require_once($this->config->config['ebaypath'].'get-common/keys.php');
	require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');

	$catID = (int)$catID;
	
	//SiteID must also be set in the Request's XML
	//SiteID = 0  (US) - UK = 3, Canada = 2, Australia = 15, ....
	//SiteID Indicates the eBay site to associate the call with
	
	$catTreeDoc = NULL;  //declared here for wider variable scope (avoid having to parse more than once)
	
	if($catID == 0)
	{
		//see if CatTree.xml file exists
		if(!file_exists($this->config->config['ebaypath'].'/ebayxmls/CatTree.xml'))
		{   //file does not exists -> request and save
			echo '<P><B>Downloading new category tree...</B>';
			$catTreeDoc = $this->_getEntireCategoryTree($devID, $appID, $certID, $compatabilityLevel,
												$this->config->config['ebaysiteid'], $userToken, $serverUrl);
			echo '<P><B>Latest Category Tree Downloaded.</B>';
		}
		else
		{
	
			//Get the online version number
			$onlineVersion = $this->_getOnlineTreeVersion($devID, $appID, $certID, $compatabilityLevel,
												$this->config->config['ebaysiteid'], $userToken, $serverUrl);
			//get local version number
				
			$catTreeDoc = new DOMDocument();
			$catTreeDoc->load($this->config->config['ebaypath'].'/ebayxmls/CatTree.xml');
			$localVersionNode = $catTreeDoc->getElementsByTagName('CategoryVersion');
			$localVersion = $localVersionNode->item(0)->nodeValue;

			//if version numbers are different
			if( $onlineVersion != $localVersion)
			{	
				echo '<P><B>Downloading new category tree...</B>';
				//download and save new category tree
				$catTreeDoc = $this->_getEntireCategoryTree($devID, $appID, $certID, $compatabilityLevel,
												$this->config->config['ebaysiteid'], $userToken, $serverUrl);
				echo '<P><B>Latest Category Tree Downloaded.</B>';
			}
		}

		//show the top-level domains
		
		//$this->CategoriesPutDB($catTreeDoc);
		$this->_displayTopLevelCategories($catTreeDoc);
	}
	else //category has been selected
	{		
		$catTreeDoc = new DOMDocument();
		$catTreeDoc->load($this->config->config['ebaypath'].'/ebayxmls/CatTree.xml');
		
		if($this->_isLeafCategory($catTreeDoc, $catID))
		{
			echo "<P><B>YOU MAY SUBMIT TO THIS CATEGORY : $catID</B>";
		}
		else
		{
			$this->_displaySubCategories($catTreeDoc, $catID);
		}
	}

}
function Add($catID = 0)
	{	
	require_once($this->config->config['ebaypath'].'get-common/keys.php');
	require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
		
	$catID = (int)$catID;
	
	$categories = $this->Myebay_model->GetEbayDataCategories((int)$catID);

	//printcool ($this->Myebay_model->GetEbayDataCategories($catID));
	if($catID != 0)
	{
		$category = $this->Myebay_model->GetEbayCategoryData((int)$catID);
		/*
		$catTreeDoc = new DOMDocument();
		$catTreeDoc->load($this->config->config['ebaypath'].'/ebayxmls/CatTree.xml');
		
		if($this->_isLeafCategory($catTreeDoc, $catID))
		{
			echo "<P><B>YOU MAY SUBMIT TO THIS CATEGORY : $catID</B>";
		}
		else
		{
			$this->_displaySubCategories($catTreeDoc, $catID);
		}
		*/
	}
?>
            <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
            <HTML>
            <HEAD>
            <META http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
            <TITLE>AddItem</TITLE>
            <script type="text/javascript" src="http://www.la-tronics.com/js/chars.js"></script> 
            <script type="text/javascript" src="http://www.la-tronics.com/ck/ckeditor.js"></script>
            <link href="<?php Site_url(); ?>css/admin.css" rel="stylesheet" type="text/css">
            </HEAD>
            <BODY style="background:none;">
            <FORM action="<?php Site_url(); ?>Mydev/Add" method="post">            
            <TABLE cellpadding="2" border="0">
                <TR>
                    <TD>listingType</TD>
                    <TD>
                      <select name="listingType">
                        <option value="Chinese">Chinese</option>
                        <option value="Dutch">Dutch</option>
                        <option value="FixedPriceItem">Fixed Price Item</option>
                        <option value="StoresFixedPrice">Stores Fixed Price</option>
                      </select>
                    </TD>
                </TR>
                <TR>
                    <TD valign="top">primaryCategory</TD>
                    <TD><?php if (isset($category)) echo 'Category: '.$category['catName']; ?><Br>
                      <select name="primaryCategory">
                      <?php foreach ($categories as $cat) { echo '<option value="'.$cat['catID'].'">'.$cat['catName'].'</option>'; } ?>
                      </select>
                    </TD>
                </TR>
                <TR>
                    <TD>itemTitle</TD>
                    <TD><INPUT id="count1" type="text" name="itemTitle" value="TEST IN SANDBOX BEFORE PROD - DO NOT BID" size=30 onFocus="countChars('count1','char_count1',140)" onKeyDown="countChars('count1','char_count1',140)" onKeyUp="countChars('count1','char_count1',140)"> <span id="char_count1" class="countbox"></span></TD>
                </TR>
                <TR>
                    <TD>itemDescription</TD>
                    <TD><INPUT id="count2" type="text" name="itemDescription" value="TEST IN SANDBOX BEFORE PROD - DO NOT BID - This will incur prod listing fees" size=30 onFocus="countChars('count2','char_count2',140)" onKeyDown="countChars('count2','char_count2',140)" onKeyUp="countChars('count2','char_count2',140)"> <span id="char_count2" class="countbox"></span></TD>
                </TR>
                <TR>
                  <TD>listingDuration</TD>
                    <TD>
                      <select name="listingDuration">
                        <option value="Days_1">1 day</option>
                        <option value="Days_3">3 days</option>
                        <option value="Days_5">5 days</option>
                        <option value="Days_7">7 days</option>
                      </select>
                      (defaults to GTC = 30 days for Store)
                    </TD>
                </TR>
                <TR>
                    <TD>startPrice</TD>
                    <TD><INPUT type="text" name="startPrice" value="<?php echo rand(1,200) / 100 ?>"></TD>
                </TR>
                <TR>
                    <TD>buyItNowPrice</TD>
                    <TD><INPUT type="text" name="buyItNowPrice" value="<?php echo rand(299,599) / 100; ?>"> (set to 0.0 for Store)</TD>
                </TR>
                <TR>
                    <TD>quantity</TD>
                    <TD><INPUT type="text" name="quantity" value="1"> (must be 1 for Chinese)</TD>
                </TR>	
                <TR>
                  <TD>PaymentMethod</TD>
                    <TD>
                    <input type="checkbox" name="PaymentMethods[PayPal]"  id="PayPal"><label for="PayPal"> Paypal</label>&nbsp;&nbsp;&nbsp;
                    
                    <input type="checkbox" name="PaymentMethods[VisaMC]"  id="VisaMC"><label for="VisaMC"> VisaMC</label>&nbsp;&nbsp;&nbsp;
                    
                    <input type="checkbox" name="PaymentMethods[AmEx]"  id="AmEx"><label for="AmEx"> AmEx</label>&nbsp;&nbsp;&nbsp;
                    
                    <input type="checkbox" name="PaymentMethods[Discover]"  id="Discover"><label for="Discover"> Discover</label>&nbsp;&nbsp;&nbsp;
                    
                    <input type="checkbox" name="PaymentMethods[Diners]"  id="Diners"><label for="Diners"> Diners</label>&nbsp;&nbsp;&nbsp;
                    </TD>
                </TR>
                <tr><td colspan="2"><hr></td></tr>   
                <tr><td>Subtitle:</td><td><input type="text" size="20" /></td></tr>    
                <tr><td>Condition:</td><td><select><option>used</option></select></td></tr>    
                <tr><td colspan="2">Condition Description: <span id="char_count3" class="countbox"></span></td></tr>    
                <tr><td colspan="2">    
                <textarea id="count3" style="width:500px; height:60px;" onFocus="countChars('count3','char_count3',1000)" onKeyDown="countChars('count3','char_count3',1000)" onKeyUp="countChars('count3','char_count3',1000)"></textarea></td></tr>    
                <tr><td colspan="2">Item specific:<br><br>
                Screen size, Compatible Brand, Display Tech, Aspect Ratio, Max Resolution, Compatible Product Line, Compatible Model, MPN, Screen Finish, Country Manufacturer.</td>
             </tr>
             <tr><td colspan="2"> 
             PHOTO 1 <input type="file"> &nbsp;&nbsp;PHOTO 2 <input type="file"><br><br>PHOTO 3 <input type="file"> &nbsp;&nbsp;PHOTO 4 <input type="file">
             </td></tr> 
             <tr><td colspan="2">Describe the item your selling:<br><br>
             <div style="width:750px;">
               <textarea class="ckeditor" name="description"></textarea>
               </div>
             </td></tr>
                <TR>
                    <TD colspan="2" align="right"><INPUT type="submit" name="submit" value="AddItem"></TD>
                </TR>
            </TABLE>
            </FORM>
	<?php
	if(isset($_POST['listingType']))
	{
	     ini_set('magic_quotes_gpc', false);    // magic quotes will only confuse things like escaping apostrophe
		//Get the item entered
        $listingType     = $_POST['listingType'];
        $primaryCategory = $_POST['primaryCategory'];
        $itemTitle       = $_POST['itemTitle'];
        if(get_magic_quotes_gpc()) {
            // print "stripslashes!!! <br>\n";
            $itemDescription = stripslashes($_POST['itemDescription']);
        } else {
            $itemDescription = $_POST['itemDescription'];
        }
        $itemDescription = $_POST['itemDescription'];
        $listingDuration = $_POST['listingDuration'];
        $startPrice      = $_POST['startPrice'];
        $buyItNowPrice   = $_POST['buyItNowPrice'];
        $quantity        = $_POST['quantity'];
		$PaymentMethods  = $_POST['PaymentMethods'];
        var_dump ($PaymentMethods);
        if ($listingType == 'StoresFixedPrice') {
          $buyItNowPrice = 0.0;   // don't have BuyItNow for SIF
          $listingDuration = 'GTC';
        }
        
        if ($listingType == 'Dutch') {
          $buyItNowPrice = 0.0;   // don't have BuyItNow for Dutch
        }
        	
		if (isset($PaymentMethods))
		{
			$paymentsnippet = '';
			foreach ($PaymentMethods as $k => $v)
			{
				$paymentsnippet .= '<PaymentMethods>'.$k.'</PaymentMethods>';
				if ($k == 'PayPal') $paymentsnippet .= '<PaymentMethods>'.$k.'</PaymentMethods>';
			}
		}
		printcool($paymentsnippet);
	
	break;
	
		//SiteID must also be set in the Request's XML
		//SiteID = 0  (US) - UK = 3, Canada = 2, Australia = 15, ....
		//SiteID Indicates the eBay site to associate the call with
		//the call being made:
		$verb = 'AddItem';
		
		//$ShippingServiceOptions = new ShippingServiceOptionsType(); 
		//printcool($ShippingServiceOptions);		

		$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
		$requestXmlBody .= '<AddItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
		$requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';
		$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
		$requestXmlBody .= "<Version>$compatabilityLevel</Version>";
		$requestXmlBody .= "<Item>
    <Title>My very first test listing</Title>
    <Description>
      This is My very first test listing
    </Description>
    <PrimaryCategory>
      <CategoryID>".$primaryCategory."</CategoryID>
    </PrimaryCategory>
    <StartPrice>1.0</StartPrice>
    <Country>US</Country>
    <Currency>USD</Currency>
    <DispatchTimeMax>3</DispatchTimeMax>
    <ListingDuration>Days_7</ListingDuration>
    <ListingType>FixedPriceItem</ListingType>
    <PaymentMethods>VisaMC</PaymentMethods>    
    <PictureDetails>
      <PictureURL>http://pics.ebay.com/aw/pics/dot_clear.gif</PictureURL>
    </PictureDetails>
    <PostalCode>95125</PostalCode>
    <Quantity>1</Quantity>
    <ReturnPolicy>
      <ReturnsAcceptedOption>ReturnsAccepted</ReturnsAcceptedOption>
      <RefundOption>MoneyBack</RefundOption>
      <ReturnsWithinOption>Days_30</ReturnsWithinOption>
      <Description>If you are not satisfied, return for refund.</Description>
      <ShippingCostPaidByOption>Buyer</ShippingCostPaidByOption>
    </ReturnPolicy>
    <ShippingDetails>
      <ShippingType>Flat</ShippingType>
      <ShippingServiceOptions>
        <ShippingServicePriority>1</ShippingServicePriority>
        <ShippingService>USPSMedia</ShippingService>
        <ShippingServiceCost>2.50</ShippingServiceCost>
      </ShippingServiceOptions>
    </ShippingDetails>
    <Site>US</Site>
  </Item>";//<PayPalEmailAddress>magicalbookseller@yahoo.com</PayPalEmailAddress>
		/*$requestXmlBody .= '<Item>';
		$requestXmlBody .= '<Site>US</Site>';
		$requestXmlBody .= '<PrimaryCategory>';
		$requestXmlBody .= "<CategoryID>$primaryCategory</CategoryID>";
		$requestXmlBody .= '</PrimaryCategory>';
		$requestXmlBody .= "<BuyItNowPrice currencyID=\"USD\">$buyItNowPrice</BuyItNowPrice>";
		$requestXmlBody .= '<Country>US</Country>';
		$requestXmlBody .= '<Currency>USD</Currency>';
		$requestXmlBody .= "<ListingDuration>$listingDuration</ListingDuration>";
        $requestXmlBody .= "<ListingType>$listingType</ListingType>";
		$requestXmlBody .= '<Location><![CDATA[San Jose, CA]]></Location>';
		$requestXmlBody .= '<PaymentMethods>'.$PaymentMethods.'</PaymentMethods>';
		$requestXmlBody .= '<ShippingDetails ShippingServiceOptions=\"FreeShipping\">FreeShipping</ShippingDetails>';		
		$requestXmlBody .= "<Quantity>$quantity</Quantity>";
		$requestXmlBody .= "<ShippingDetails><ShippingServiceOptions><ShippingServicePriority>1</ShippingServicePriority><ShippingService>UPSGround</ShippingService><ShippingServiceCost>0.00</ShippingServiceCost><ShippingServiceAdditionalCost>0.00</ShippingServiceAdditionalCost></ShippingServiceOptions> 
    </ShippingDetails> ";
		$requestXmlBody .= '<RegionID>0</RegionID>';
		$requestXmlBody .= "<StartPrice>$startPrice</StartPrice>";
		$requestXmlBody .= '<ShippingTermsInDescription>True</ShippingTermsInDescription>';
		$requestXmlBody .= "<Title><![CDATA[$itemTitle]]></Title>";
		$requestXmlBody .= "<Description><![CDATA[$itemDescription]]></Description>";
		$requestXmlBody .= '</Item>';*/
		$requestXmlBody .= '</AddItemRequest>';
        //Create a new eBay session with all details pulled in from included keys.php
        $session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
		
		//send the request and get response
		$responseXml = $session->sendHttpRequest($requestXmlBody);
		if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
			die('<P>Error sending request');
		
		//Xml string is parsed and creates a DOM Document object
		$responseDoc = new DomDocument();
		$responseDoc->loadXML($responseXml);
			
		//get any error nodes
		$errors = $responseDoc->getElementsByTagName('Errors');
		
		//if there are error nodes
		if($errors->length > 0)
		{
			echo '<P><B>eBay returned the following error(s):</B>';
			//display each error
			//Get error code, ShortMesaage and LongMessage
			$code     = $errors->item(0)->getElementsByTagName('ErrorCode');
			$shortMsg = $errors->item(0)->getElementsByTagName('ShortMessage');
			$longMsg  = $errors->item(0)->getElementsByTagName('LongMessage');
			//Display code and shortmessage
			echo '<P>', $code->item(0)->nodeValue, ' : ', str_replace(">", "&gt;", str_replace("<", "&lt;", $shortMsg->item(0)->nodeValue));
			//if there is a long message (ie ErrorLevel=1), display it
			if(count($longMsg) > 0)
				echo '<BR>', str_replace(">", "&gt;", str_replace("<", "&lt;", $longMsg->item(0)->nodeValue));
	
		} else { //no errors
            
			//get results nodes
            $responses = $responseDoc->getElementsByTagName("AddItemResponse");
            foreach ($responses as $response) {
              $acks = $response->getElementsByTagName("Ack");
              $ack   = $acks->item(0)->nodeValue;
              echo "Ack = $ack <BR />\n";   // Success if successful
              
              $endTimes  = $response->getElementsByTagName("EndTime");
              $endTime   = $endTimes->item(0)->nodeValue;
              echo "endTime = $endTime <BR />\n";
              
              $itemIDs  = $response->getElementsByTagName("ItemID");
              $itemID   = $itemIDs->item(0)->nodeValue;
              echo "itemID = $itemID <BR />\n";
              
              $linkBase = "http://cgi.sandbox.ebay.com/ws/eBayISAPI.dll?ViewItem&item=";
              echo "<a href=$linkBase" . $itemID . ">$itemTitle</a> <BR />";
              
              $feeNodes = $responseDoc->getElementsByTagName('Fee');
              foreach($feeNodes as $feeNode) {
                $feeNames = $feeNode->getElementsByTagName("Name");
                if ($feeNames->item(0)) {
                    $feeName = $feeNames->item(0)->nodeValue;
                    $fees = $feeNode->getElementsByTagName('Fee');  // get Fee amount nested in Fee
                    $fee = $fees->item(0)->nodeValue;
                    if ($fee > 0.0) {
                        if ($feeName == 'ListingFee') {
                          printf("<B>$feeName : %.2f </B><BR>\n", $fee); 
                        } else {
                          printf("$feeName : %.2f <BR>\n", $fee);
                        }      
                    }  // if $fee > 0
                } // if feeName
              } // foreach $feeNode
            
            } // foreach response
            
		} // if $errors->length > 0
	}
?>
</BODY>
</HTML>
<?	
}	





/**	isLeafCategory
	Returns true if te category given is a leaf category in the category tree specified
	Input:	$tree - a DOM Document represnting the category tree
			$catID - the ID of the category to see if it is a Leaf
*/
function _isLeafCategory($tree, $catID)
{
	//get all the categories and go through each one
	$categories = $tree->getElementsByTagName('Category');
	foreach($categories as $cat)
	{	
		//get the categories ID
		$catIDNode = $cat->getElementsByTagName('CategoryID');
		//if the category id is the one we want
		if($catIDNode->item(0)->nodeValue == $catID)
		{
			$leafNode = $cat->getElementsByTagName('LeafCategory');
			//if LeafCategory = return true, otherwise return false
			if($leafNode->item(0)->nodeValue == "1" || $leafNode->item(0)->nodeValue == "true")
				return true;
			else
				return false;
		}
	}
}


/**	displaySubCategories
	Displays the Subcategories of the given category from the given category tree
*/
function _displaySubCategories($tree, $parentCategoryID)
{
	//get all the categories and go through each one
	$categories = $tree->getElementsByTagName('Category');
	echo '<P><B>Subcategories Categories</B><BR>Please Select:<BR>';
	foreach($categories as $cat)
	{	
		//get ParentID
		$parentIDNode = $cat->getElementsByTagName('CategoryParentID');
		//If parentID is the one we want then dusplay this category
		if($parentIDNode->item(0)->nodeValue == $parentCategoryID)
		{
			//get ID and name and display as link
			$catIDNode = $cat->getElementsByTagName('CategoryID');
			$catNameNode = $cat->getElementsByTagName('CategoryName');
			echo '<BR><A href="'.Site_url().'Mydev/Add/', $catIDNode->item(0)->nodeValue, '">', $catNameNode->item(0)->nodeValue,'</A>';
		}
	}
}

/**	displayTopLevelCategories
	Displays the Top-Level categories from the given category tree
*/
function _displayTopLevelCategories($tree)
{	
	//get all the categories and go through each one
	$categories = $tree->getElementsByTagName('Category');
	echo '<P><B>Main Categories</B><BR>Please Select:<BR>';
	$catlist = '';
	foreach($categories as $cat)
	{	
		//get the ID and ParentID
		$catIDNode = $cat->getElementsByTagName('CategoryID');
		$parentIDNode = $cat->getElementsByTagName('CategoryParentID');
		//if ID equals ParentID then it is a Top-Level category
		
		if($catIDNode->item(0)->nodeValue == $parentIDNode->item(0)->nodeValue)
		{	
			//get name and display as link
			$catNameNode = $cat->getElementsByTagName('CategoryName');
			//$this->load->model('Myebay_model'); 
			//$this->Myebay_model->GetItem($this->id);	
			echo '<BR><A href="'.Site_url().'Mydev/Add/', $catIDNode->item(0)->nodeValue, '">', $catNameNode->item(0)->nodeValue,'</A>';

			$catlist[$catIDNode->item(0)->nodeValue] = $catNameNode->item(0)->nodeValue;
		}
	}
	printcool ($catlist);
}
	
/**	getEntireCategoryTree
	Retrieves the entire category tree from eBay API and saves it locally in an XML file
*/
function _getEntireCategoryTree($devID, $appID, $certID, $compatabilityLevel, $siteID, $userToken, $serverUrl)
{
	//Build the request Xml string
	$requestXmlBody = '<?xml version="1.0" encoding="utf-8" ?>';
	$requestXmlBody .= '<GetCategoriesRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
	$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
	$requestXmlBody .= "<DetailLevel>ReturnAll</DetailLevel>"; //get the entire tree
	$requestXmlBody .= "<Item><Site>$siteID</Site></Item>";
	$requestXmlBody .= "<ViewAllNodes>1</ViewAllNodes>"; //Gets all nodes not just leaf nodes
	$requestXmlBody .= '</GetCategoriesRequest>';
	
	//Create a new eBay session with all details pulled in from included keys.php
	$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $siteID, 'GetCategories');
	$responseXml = $session->sendHttpRequest($requestXmlBody);
	if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
		die('<P>Error sending request');
	
	//Xml string is parsed and creates a DOM Document object
	$responseDoc = new DomDocument();
	$responseDoc->loadXML($responseXml);
	
	//save the tree to local file
	$responseDoc->save('CatTree.xml');
	
	//return the DOM Document
	return $responseDoc;
}


/**	getOnlineTreeVersion
	Returns the Version number of the Category tree that is currently available online
*/
function _getOnlineTreeVersion($devID, $appID, $certID, $compatabilityLevel, $siteID, $userToken, $serverUrl)
{
	//Build the request Xml string
	$requestXmlBody = '<?xml version="1.0" encoding="utf-8" ?>';
	$requestXmlBody .= '<GetCategoriesRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
	$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
	$requestXmlBody .= "<Item><Site>$siteID</Site></Item>";
	$requestXmlBody .= "<ViewAllNodes>0</ViewAllNodes>";
	$requestXmlBody .= '</GetCategoriesRequest>';
	
	//Create a new eBay session with all details pulled in from included keys.php
	$session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $siteID, 'GetCategories');
	//send the request
	$responseXml = $session->sendHttpRequest($requestXmlBody);
	if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
		die('<P>Error sending request');
	
	//Xml string is parsed and creates a DOM Document object
	$responseDoc = new DomDocument();
	$responseDoc->loadXML($responseXml);
	
	//get the version name
	$version = $responseDoc->getElementsByTagName('CategoryVersion');
	
	//return the version
	return $version->item(0)->nodeValue;
}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/////////// OLDER STUFF
	
	
	
	
	
	/*
function index()
	{	
		$session_search = $this->session->userdata('last_string');

		if (isset($_POST['search'])) $string = $this->input->post('search', TRUE);		
		elseif ($session_search) $string = $this->session->userdata('last_string');
		else $string = '';
		
		$this->session->set_userdata('last_string', $string);
		$this->mysmarty->assign('string', $string);		
		$this->mysmarty->assign('list', $this->Myebay_model->ListItems($string));	
		$this->mysmarty->view('myebay/myebay_show.html');

	}
function CleanSearch()
	{
		$this->session->unset_userdata('last_string');
		Redirect('Myebay');
	}
function GetSource($itemid = '')
	{
		$this->id = (int)$itemid;
		if ($this->id == 0)	Redirect('Myebay');		
		$this->displays = $this->Myebay_model->GetItem($this->id);			
		$this->_GetSpecialAndTree();
		$this->mysmarty->assign('displays', $this->displays);
		$this->mysmarty->view('myebay/myebay_source.html');
	}

function Delete ($id = '')
	{
		$this->id = (int)$id;
		if ($this->id > 0)
			{
			$this->DeleteImageInEbay($this->id, '1', TRUE);
			$this->DeleteImageInEbay($this->id, '2', TRUE);
			$this->DeleteImageInEbay($this->id, '3', TRUE);
			$this->DeleteImageInEbay($this->id, '4', TRUE);
			$this->Myebay_model->Delete($this->id);
			}
		Redirect("/Myebay");
	}
function Edit($itemid = '')
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
									'e_desc' => $this->input->post('e_desc', TRUE)
									);
								
				require_once($this->config->config['pathtopublic'].'/fckeditor/fckeditor.php');
					
				$this->editor = new FCKeditor('e_desc');
				
				$this->editor->Width = "350";
				$this->editor->Height = "220";
				$this->editor->ToolbarSet = "Small";
				
				if (count($_POST) >	0) 
					{
						$this->editor->Value = $this->inputdata['e_desc'];
						$this->inputdata['e_desc'] = $this->editor->CreateHtml();				
					}
				else 
					{
						$this->editor->Value = $this->displays['e_desc'];
						$this->displays['e_desc'] = $this->editor->CreateHtml();
					}
				
				$this->mysmarty->assign('displays', $this->displays);
				
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
											'e_desc' => $this->form_validation->set_value('e_desc')
											);
								
					$this->checkexists =  $this->Myebay_model->CheckSefExists($this->db_data['e_sef'], $this->id);
					if ($this->checkexists) $this->pref = rand(1,9).rand(1,9).rand(1,9); else $this->pref = '';
					$this->db_data['e_sef'] = $this->db_data['e_sef'].$this->pref;
					
					$this->productimages = array(1,2,3,4);
					
					$this->load->library('upload');
					
					$watermark = FALSE;
					foreach($this->productimages as $value)
					{			if ($_FILES['e_img'.$value]['name'] != '') 
								{
									$newname[$value] = (int)$this->id.'_'.substr($this->_CleanSef($this->db_data['e_title']), 0, 210).'_'.$value;
									$image[$value] = $this->_UploadImage ('e_img'.$value, $this->config->config['paths']['imgebay'], TRUE, $this->config->config['sizes']['ebayimg']['width'], $this->config->config['sizes']['ebayimg']['height'], FALSE, TRUE, $newname[$value]);				
									if ($image[$value]) {
										$oldimage[$value] = $this->Myebay_model->GetOldEbayImage($this->id, $value);
										if ($oldimage[$value] != '' && $image[$value] != $oldimage[$value]) {
											if ($value == 1) unlink($this->config->config['paths']['imgebay'].'/Ebay_'.$oldimage[$value]);
											unlink($this->config->config['paths']['imgebay'].'/'.$oldimage[$value]);
											unlink($this->config->config['paths']['imgebay'].'/thumb_'.$oldimage[$value]);
											unlink($this->config->config['paths']['imgebay'].'/thumb_main_'.$oldimage[$value]);
											}									
										
										$this->db_data['e_img'.$value] = $image[$value];
										$watermark = TRUE;
									}	
								}
					}
				
						$this->Myebay_model->Update((int)$this->id,$this->db_data);
						
						if ($watermark) Redirect('Myebay/DoWaterMark/'.(int)$this->id);
						else  redirect("Myebay/GetSource/".(int)$this->id);					
			}
	}
	else {
			redirect("/Myebay");
	}
}
function DoWaterMark($id, $place = 1)
	{
		$img = $this->Myebay_model->GetOldEbayImage((int)$id, $place);
		
		if ($place == 1 && $img)
		{
			if (!copy($this->config->config['paths']['imgebay'].'/'.$img, $this->config->config['paths']['imgebay'].'/Ebay_'.$img)) {
				echo "failed to copy file...\n";
				break;
			}
			$this->iconfig['image_library'] = 'gd2';
			$this->iconfig['source_image']	= $this->config->config['paths']['imgebay'].'/Ebay_'.$img;
			$this->iconfig['create_thumb'] = FALSE;
			$this->iconfig['maintain_ratio'] = TRUE;
			$this->iconfig['width']	= '600';
			$this->load->library('image_lib'); 
			$this->image_lib->initialize($this->iconfig);
			$this->imagesresult = $this->image_lib->resize();
			if ( $this->imagesresult != '1') { printcool ($this->image_lib->display_errors()); exit; }
			$this->image_lib->clear();
			
			$this->_WaterMark('bottom', 'right', 'wm_original_ebay.png', $this->config->config['paths']['imgebay'], 'Ebay_'.$img);
		}
		if ($img)
		{
			$this->_WaterMark('bottom', 'right', 'wm_original_bottom.png', $this->config->config['paths']['imgebay'], $img);
			$this->_WaterMark('middle', 'center', 'wm_original_center.png', $this->config->config['paths']['imgebay'], $img);
			$this->_WaterMark('bottom', 'right', 'wm_bigtn_bottom.png', $this->config->config['paths']['imgebay'], 'thumb_main_'.$img);
			$this->_WaterMark('middle', 'center', 'wm_bigtn_center.png', $this->config->config['paths']['imgebay'], 'thumb_main_'.$img);
			$this->_WaterMark('bottom', 'right', 'wm_smalltn_bottom.png', $this->config->config['paths']['imgebay'], 'thumb_'.$img);	
		}
	$place++;
	
	if ($place >4) redirect("/Myebay/GetSource/".(int)$id);
	else Redirect('Myebay/DoWaterMark/'.(int)$id.'/'.$place);
	
	}
function Add()
	{	
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
									'e_desc' => $this->input->post('e_desc', TRUE)
									);
				
				if (count($_POST) == 0) $this->inputdata['e_shipping'] = 'United States Postal Service.
We ship Internationally.
We use primarily USPS and FedEx';
								
				require_once($this->config->config['pathtopublic'].'/fckeditor/fckeditor.php');
				
				$this->editor = new FCKeditor('e_desc');
				if (count($_POST) >	0) $this->editor->Value = $this->inputdata['e_desc'];
				else $this->editor->Value = '';
				$this->editor->Width = "350";
				$this->editor->Height = "220";
				$this->editor->ToolbarSet = "Small";				
				$this->inputdata['e_desc'] = $this->editor->CreateHtml();
					
				$this->mysmarty->assign('inputdata', $this->inputdata);				
				$this->mysmarty->assign('errors', $this->form_validation->_error_array);
				$this->mysmarty->view('myebay/myebay_add.html');
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
											'e_desc' => $this->form_validation->set_value('e_desc')
											);
					
					$this->checkexists =  $this->Myebay_model->CheckSefExists($this->db_data['e_sef']);
					if ($this->checkexists) $this->pref = rand(1,9).rand(1,9).rand(1,9); else $this->pref = '';
					$this->db_data['e_sef'] = $this->db_data['e_sef'].$this->pref;
					
					$this->load->library('upload');
					
						$this->newid = $this->Myebay_model->Insert($this->db_data);
						
					///Update Images	
						$this->productimages = array(1,2,3,4);
						$watermark = FALSE;
						foreach($this->productimages as $value)
						{			if ($_FILES['e_img'.$value]['name'] != '') 
									{
										$newname[$value] = (int)$this->newid.'_'.substr($this->_CleanSef($this->db_data['e_title']), 0, 210).'_'.$value;
										$image[$value] = $this->_UploadImage ('e_img'.$value, $this->config->config['paths']['imgebay'], TRUE, $this->config->config['sizes']['ebayimg']['width'], $this->config->config['sizes']['ebayimg']['height'], FALSE, TRUE, $newname[$value]);				
										if ($image[$value]) {
											$this->newdb_data['e_img'.$value] = $image[$value];					
											$watermark = TRUE;	
										}
									}	
	
						}	
						if (isset($this->newdb_data)) $this->Myebay_model->Update((int)$this->newid, $this->newdb_data);
						
						if ($watermark) Redirect('Myebay/DoWaterMark/'.(int)$this->newid);
						else redirect("Myebay/GetSource/".(int)$this->newid);							
			}
}

function DeleteImageInEbay($id = '', $place = '', $nogo = FALSE)
	{
		$this->id = (int)$id;
		$this->place = (int)$place;
		if (($this->id > 0) && ($this->place > 0))
				{
				$this->img = $this->Myebay_model->DeleteEbayImage($this->id, $this->place);
				if ($this->img != '') {
					if ($this->place == 1) unlink($this->config->config['paths']['imgebay'].'/Ebay_'.$this->img);
					unlink($this->config->config['paths']['imgebay'].'/'.$this->img);
					unlink($this->config->config['paths']['imgebay'].'/thumb_'.$this->img);
					unlink($this->config->config['paths']['imgebay'].'/thumb_main_'.$this->img);
					
					}
				}
		if (!$nogo) {
		Redirect("/Myebay/Edit/".$this->id);
		}
	}
	
	///////////////////////////
	

function _CleanSef ($string) {
	$this->inputstring = str_replace(" ", "-", $string);
	$this->inputstring = str_replace("_", "-", $this->inputstring);
	$this->cyrchars = array('&#1040;','&#1041;','&#1042;','&#1043;','&#1044;','&#1045;','&#1046;','&#1047;','&#1048;','&#1049;','&#1050;','&#1051;','&#1052;','&#1053;','&#1054;','&#1055;','&#1056;','&#1057;','&#1058;','&#1059;','&#1060;','&#1061;','&#1063;','&#1062;','&#1064;','&#1065;','&#1066;','&#1068;','&#1070;','&#1071;','&#1072;','&#1073;','&#1074;','&#1075;','&#1076;','&#1077;','&#1078;','&#1079;','&#1080;','&#1081;','&#1082;','&#1083;','&#1084;','&#1085;','&#1086;','&#1087;','&#1088;','&#1089;','&#1090;','&#1091;','&#1092;','&#1093;','&#1095;','&#1094;','&#1096;','&#1097;','&#1098;','&#1100;','&#1102;','&#1103;');							 
	$this->latinchars = array('A','B','V','G','D','E','J','Z','I','I','K','L','M','N','O','P','R','S','T','U','F','H','CH','TS','SH','SHT','U','U','JU','YA','a','b','v','g','d','e','j','z','i','i','k','l','m','n','o','p','r','s','t','u','f','h','ch','ts','sh','sht','u','u','ju','ya');							 
	$this->inputstring = str_replace($this->cyrchars, $this->latinchars, $this->inputstring);	
	$this->inputstring = str_replace('---', '-', $this->inputstring);	
	$this->inputstring = str_replace('--', '-', $this->inputstring);
	$this->inputstring = ereg_replace("[^A-Za-z0-9\-]", "", $this->inputstring);
	return $this->inputstring;
	}

function _UploadImage ($fieldname = '', $configpath = '', $thumb = FALSE, $width = '', $height = '', $justupload = FALSE, $wm = FALSE, $filename = FALSE) 
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
		//sleep(0.5);
		return ($processimgdata['file_name']);
		}
}
function _WaterMark($val, $hal, $wm, $path = '', $file = '')
{
							$this->load->library('image_lib'); 
							$config['source_image']	= $path.'/'.$file;
							$config['wm_type'] = 'overlay';
							$config['wm_overlay_path'] = '/home/laptopfi/public_html/365laptoprepair.com/images/'.$wm;
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

function _GetSpecialAndTree()
	{
		$this->load->model('Myproducts_model'); 	
		$this->mysmarty->assign('catlist', $this->Myproducts_model->GetAllCategories());
		$this->mysmarty->assign('specials', $this->Myebay_model->GetTopSpecialAds());		
	}*/
}
