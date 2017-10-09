<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route['default_controller'] = "show/newnewindex";
$route['scaffolding_trigger'] = "";
$route['Home'] = "Products/ShowEbayListings";
$route['Newhome'] = "show/newnewindex";
$route['Ebay'] = "Products/ShowEbayListings/";
$route['Ebay/(:num)'] = "Products/ShowEbayListings/$1";
$route['EbayGallery/(:any)/(:num)'] = "Products/ShowEbayImages/$1/$2";
$route['EbayGallery/(:any)'] = "Products/ShowEbayImages/$1/1";
$route['EbayItem/(:num)'] = "Products/ShowEbayListing/$1";

$route['nEbay'] = "Products/nShowEbayListings/";
$route['nEbay/(:num)'] = "Products/nShowEbayListings/$1";
$route['nEbayItem/(:num)'] = "Products/nShowEbayListing/$1";

$route['TransactionsStart'] = "Cronn/ManualTrans";
$route['store'] = "show/store";
$route['store/(:num)'] = "show/store/listdesc/$1";
$route['store/(:num)/(:num)'] = "show/store/listdesc/$1/$2";

$route['store/listdesc/(:num)'] = "show/store/listdesc/$1";
$route['store/listdesc/(:num)/(:num)'] = "show/store/listdesc/$1/$2";

$route['store/listasc/(:num)'] = "show/store/listasc/$1";
$route['store/listasc/(:num)/(:num)'] = "show/store/listasc/$1/$2";

$route['store/priceasc/(:num)'] = "show/store/priceasc/$1";
$route['store/priceasc/(:num)/(:num)'] = "show/store/priceasc/$1/$2";

$route['store/pricedesc/(:num)'] = "show/store/pricedesc/$1";
$route['store/pricedesc/(:num)/(:num)'] = "show/store/pricedesc/$1/$2";

$route['Search'] = "show/searchitem";


$route['storeitem/(:num)'] = "Products/nNewShowEbayListing/$1";
$route['nstoreitem/(:num)'] = "Products/nNewShowEbayListing/$1";

$route['ebay/(:num)'] = "Products/RedirectShowEbayListings/$1";
$route['ebayitem/(:num)'] = "Products/RedirectShowEbayListing/$1";

$route['nebay/(:num)'] = "Products/nRedirectShowEbayListings/$1";
$route['nebayitem/(:num)'] = "Products/nRedirectShowEbayListing/$1";

$route['Free-Laptop-Repair-Estimate'] = "Products/RequestForm/Repair";
$route['Laptop-Repair-Form'] = "Products/ShowProduct/Laptop_Repair_Form_For_Diagnostic";
$route['Laptop-Repair-Form/(:any)'] = "show";
$route['Laptop-Repair-Form/(:any)/(:any)'] = "show";
$route['Laptop-Repair-Form/(:any)/(:any)/(:num)'] = "show";
$route['Special-Repair-Form/(:num)'] = "show";
$route['Special-Repair-Form/(:num)/(:any)'] = "show";
$route['Special-Repair-Form/(:num)/(:any)/(:any)'] = "show3";
$route['Laptop-Part-Inquiry'] = "Products/RequestForm/Part";






$route['Estimate/(:any)/(:num)'] = "Cart/ProcessEstimate/$1/$2";
$route['Estimate/(:any)'] = "show";
$route['OpenCart'] = "Cart/OpenCart";
$route['Show/(:any)'] = "show/Menu/$1";
$route['Show/(:any)/(:any)'] = "show/Menu/$1/$2";
$route['View/(:any)'] = "show/Item/$1";
$route['Review'] = "show/Item/Review";
//$route['Search/(:any)'] = "Show/Search/$1";

$route['FAQ/View/(:num)'] = "show/Faq/View/$1";
$route['FAQ/Show/(:num)'] = "show/Faq/List/$1";
$route['FAQ/(:any)'] = "show/Faq/Categories/";

$route['Contact'] = "show/Contact";

$route['Sitemap'] = "show/Sitemap";

//$route['Search'] = "show/Search";
//$route['Search/(:any)'] = "show/Search/$1";

$route['NewsView/(:num)'] = "show/News/View/$1";
$route['News/(:any)'] = "show/News/";

$route['Newsletter/Subscription'] = "show/Newsletter/Subscription";
$route['Newsletter/Unsubscribe/(:num)'] = "show/Unsubscribe/$1";

$route['Poll/SubmitAnswer'] = "show/Poll/Submit";
$route['Poll/Error'] = "show/Poll/Error";
$route['Poll/ThankYou'] = "show/Poll/Complete";

///CART
//$route['CartAdd/(:num)'] = "Cart/CartAdd/$1";
//$route['CartAdd/(:num)/(:num)'] = "Cart/CartAdd/$1/$2";
//$route['CartUpdate/(:num)'] = "Cart/CartUpdate/$1";
//$route['CartUpdate/(:num)/(:num)'] = "Cart/CartUpdate/$1/$2";
//$route['CartRemove/(:num)'] = "Cart/CartRemove/$1";
$route['CartAdd'] = "Cart/CartAdd";
$route['CartUpdate'] = "Cart/CartUpdate";
$route['CartRemove'] = "Cart/CartRemove";
$route['CartEmpty'] = "Cart/CartEmpty";
$route['CartShow'] = "Cart/CartShow/";
$route['CartRates'] = "Cart/CartRates/";
$route['RecalcRates'] = "Cart/RecalcRates/";
$route['SelectShipping'] = "Cart/SelectShipping/";
$route['RemoveShipping'] = "Cart/RemoveShipping/";
///
$route['ThankYou'] = "Cart/SayThankYou";
$route['Thanks'] = "Cart/SayThankYou";
$route['CheckOut'] = "Cart/CheckOut";
$route['CheckOut/Delivery'] = "Cart/CheckOutDelivery";
$route['My'] = "Cart/My";
$route['Myaccount'] = "Start/Configure";
$route['My/(:num)'] = "Cart/My/$1";
$route['MakePayment/(:num)/(:any)'] = "Cart/MakePayment/$1/$2";
$route['UpdatePayment/AuthorizeNet/(:num)/(:any)'] = "Cart/MakePayment/$1/$2/1";
$route['UpdatePayment/PayPal/(:num)/(:any)'] = "Cart/MakePayment/$1/$2/2";
$route['PayAmendment/(:any)/(:num)/(:num)/(:any)'] = "Cart/AmendmentPayment/$1/$2/$3/$4";
$route['PaymentResult'] = "Cart/AcceptAuthorizeNet";
$route['PayPalResult'] = "";
$route['PaymentComplete'] = "Cart/AcceptPayPal/".urlencode($_SERVER['REQUEST_URI']);
$route['ProcessCheckOut'] = "Products/ProcessCheckOut/";
$route['OrderComplete/(:num)'] = "Cart/OrderComplete/$1";


$route['paypalprocess/(:any)'] = "pptest/testurl/$1";
$route['paypalcancel/(:any)'] = "pptest/testurl/$1";

$route['paypalprocessp/(:any)/(:any)'] = "pptest/testphp/$1/$2";
$route['paypalcancelp/(:any)/(:any)'] = "pptest/testphp/$1/$2";

////////////////////////////////////////

$route['Products/Sort/(:any)'] = "Products/ProductsSort/$1";
$route['Products/Sort/(:any)/(:num)'] = "Products/ProductsSort/$1/$2";
$route['Products/(:any)'] = "Products/ProductsList/$1";
$route['Product/(:any)'] = "Products/ShowProduct/$1";


$route['Specials/(:num)'] = "Products/ShowSpecial/$1";
$route['Specials'] = "show";

//FIXES
$route['specials/(:num)'] = "Products/ShowSpecial/$1";
$route['specials'] = "show";
$route['ebaygallery/(:any)/(:num)'] = "Products/ShowEbayImages/$1/$2";
$route['ebaygallery/(:any)'] = "Products/ShowEbayImages/$1/1";
$route['ebaygallery'] = "show";
$route['products/(:any)'] = "Products/ProductsList/$1";
$route['product/(:any)'] = "Products/ShowProduct/$1";
$route['free-laptop-repair-estimate'] = "Products/RequestForm/Repair";
$route['laptop-repair-form'] = "Products/ShowProduct/Laptop_Repair_Form_For_Diagnostic";
$route['laptop-part-inquiry'] = "Products/RequestForm/Part";
$route['contact'] = "show/Contact";
$route['contactreply/(:any)/(:num)'] = "show/ContactReply/$1/$2";
///

$route['ProcessCheckOut'] = "Products/ProcessCheckOut/";
$route['OrderComplete/(:num)'] = "Products/OrderComplete/$1";


$route['Download/(:num)'] = "Products/DownloadWhitePaper/$1";
$route['Service/(:num)'] = "Products/ShowSolution/$1";
$route['Services'] = "Products/SolutionsList";
$route['Show-Do-it-yourself/(:num)'] = "Products/ShowDIY/$1";
$route['Do-it-yourself'] = "Products/DIYList";
$route['Whitepapers'] = "Products/Whitepapers";
$route['Partners'] = "Products/Partners";
$route['repair/(:any)'] = "repair/cities/$1";
$route['404'] = "show/NotFound";
///$route['^(?!controller|controller|controller)\S*'] = "article/$1";

$route['(.*)(administrator|joomla|joom|Joomla|joomla1.5|joomla15|joomla2|joomla1|joomla_old)(.*)'] = 'wtf/joomla';
$route['(.*)(cms|main|portal|web|v1|v2|site_old|cms_old|CMS)(.*)'] = 'wtf';

/*
$route['administrator/index.php'] = "wtf/joomla";
$route['joomla/administrator/index.php'] = "wtf/joomla";
$route['site/administrator/index.php'] = "wtf/joomla";
$route['cms/administrator/index.php'] = "wtf/joomla";
$route['home/administrator/index.php'] = "wtf/joomla";
$route['main/administrator/index.php'] = "wtf/joomla";
$route['portal/administrator/index.php'] = "wtf/joomla";
$route['web/administrator/index.php'] = "wtf/joomla";
$route['v1/administrator/index.php'] = "wtf/joomla";
$route['v2/administrator/index.php'] = "wtf/joomla";
$route['j/administrator/index.php'] = "wtf/joomla";
$route['joom/administrator/index.php'] = "wtf/joomla";
$route['Joomla/administrator/index.php'] = "wtf/joomla";
$route['joomla1.5/administrator/index.php'] = "wtf/joomla";
$route['joomla15/administrator/index.php'] = "wtf/joomla";
$route['joomla2/administrator/index.php'] = "wtf/joomla";
$route['joomla1/administrator/index.php'] = "wtf/joomla";
$route['Site/administrator/index.php'] = "wtf/joomla";
$route['site_old/administrator/index.php'] = "wtf/joomla";
$route['cms_old/administrator/index.php'] = "wtf/joomla";
$route['joomla_old/administrator/index.php'] = "wtf/joomla";
$route['CMS/administrator/index.php'] = "wtf/joomla";
$route['test/administrator/index.php'] = "wtf/joomla";
$route['backup/administrator/index.php'] = "wtf/joomla";*/

//$route['(:any)'] = "Products/ShowProduct/$1";

/* End of file routes.php */
/* Location: ./system/application/config/routes.php */