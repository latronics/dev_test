<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Mysku extends Controller {

    function Mysku() {


        //exit('Commiting update, please wait  1-2 mins...');
        parent::Controller();

        $this->load->model('Mywarehouse_model');
        $this->load->model('Myebay_model');
        $this->load->model('Auth_model');
        $this->Auth_model->VerifyAdmin();
        $this->Auth_model->CheckWarehouse();

        $this->mysmarty->assign('session', $this->session->userdata);
        $this->mysmarty->assign('action', $this->session->flashdata('action'));
        $this->mysmarty->assign('error_msg', $this->session->flashdata('error_msg'));
        $this->mysmarty->assign('success_msg', $this->session->flashdata('success_msg'));
        $this->mysmarty->assign('area', 'Warehouse');
        $this->mysmarty->assign('hot', TRUE);
        $this->mysmarty->assign('newlayout', TRUE);
        $this->mysmarty->assign('jslog', TRUE);

        $this->load->model('Myseller_model');
        $this->statuses = $this->Myseller_model->assignstatuses();
        $this->warehousefields = array(
            5 => array('sku', 'SKU', '', 1),
            2 => array('bcn', 'BCN', 'min_length[8]|max_length[8]|', 1),
            1 => array('aucid', 'Auction ID', 'required|', 1),
            3 => array('mfgpart', 'MFG Part', 'required|', 1),
            4 => array('mfgname', 'MFG Name', '', 1),
            6 => array('title', 'Title', 'required|', 1),
            7 => array('location', 'Location', '', 0),
            8 => array('notes', 'Notes', '', 0),
            9 => array('problem', 'Problem', '', 0),
            10 => array('tech', 'Tech', '', 0),
            11 => array('dates', 'Dates', '', 0),
            12 => array('repairlog', 'Repair Log', '', 0),
            14 => array('adminid', 'Admin ID', '', 0)
        );
        ksort($this->warehousefields);
        $this->_logallpost();

        //if ((int)$this->session->userdata['admin_id'] == 1) printcool ($this->session->userdata);
    }

    public function CreateCategoryPrivate()
    {
        $parent_id = $this->input->post("parent");
        $category_name = $this->input->post("category_name");

        $this->db->where("wsc_id", $parent_id);
        $category_data = $this->db->get("warehouse_sku_categories")->result_object();
        foreach($category_data as $category_data)
        {
            $newcat = array(
                "wsc_title" => $category_name,
                "wsc_parent" => $parent_id,
                "img" => $category_data->img,
                "brand" => $category_data->brand,
                "model" => $category_data->model,
                "mpn" => $category_data->mpn,
                "upcgtin" => $category_data->upcgtin,
                "audit" => $category_data->audit,
                "auditmk" => $category_data->auditmk,
                "auditadmin" => $category_data->auditadmin,
                "ebaycat" => $category_data->ebaycat,
                "googlecat" => $category_data->googlecat,
                "lbs" => $category_data->lbs,
                "oz" => $category_data->oz,
                "notebay" => $category_data->notebay,
                "wsc_mainparent" => $category_data->wsc_mainparent,
                "is_chanel" => $category_data->is_chanel,
                "level" => $category_data->level
            );
        }
        $this->db->insert("warehouse_sku_categories", $newcat);
    }
    //GENERATE BACKUP FOR WAREHOUSE_SKU_CATEGORIES
    public function getDatabaseBackup() {
        $this->load->dbutil();
        $doc_name = 'DatabaseBackup' . date('YmdHis') . '.sql.gz';



        $prefs = array(
            'tables' => array('warehouse_sku_categories'), // Array of tables to backup.
            'ignore' => array(), // List of tables to omit from the backup
            'format' => 'txt', // gzip, zip, txt
            'filename' => $doc_name, // File name - NEEDED ONLY WITH ZIP FILES
            'add_drop' => TRUE, // Whether to add DROP TABLE statements to backup file
            'add_insert' => TRUE, // Whether to add INSERT data to backup file
            'newline' => "\n"                         // Newline character used in backup file
        );

        $backup = $this->dbutil->backup($prefs);

        // Load the file helper and write the file to your server
        $this->load->helper('file');
        if(write_file(BASEPATH."/warehouse_categories_backups/".$doc_name, $backup))
        {
            echo "BACKUP SUCCESSFULL";
        }else
        {
            echo "FAIL TO BACKUP";
        }


        // Load the download helper and send the file to your desktop
        //$this->load->helper('download');
        //force_download($doc_name, $backup);
    }

    function BCNselection() {
        $id = (int) $this->input->post('listingid', true);
        $this->mysmarty->assign('id', (int) $id);
        $this->mysmarty->assign('hot', TRUE);
        echo $this->mysmarty->fetch('myseller/selection.html');
    }

    function finddup() {
        /*
          $query = $this->db->query('SELECT bcn, COUNT(*) c FROM warehouse GROUP BY bcn HAVING c > 1');
          if ($query->num_rows() > 0)
          {
          printcool ($query->result_array());
          }

         */
        //if ($this->session->userdata['admin_id'] != 1) exit();
        $bcn = $this->Mywarehouse_model->GetNextBcn(115);

        $this->db->select('wid, title, waid, aucid, warranty, deleted, bcn, bcn_p1');
        $this->db->order_by("wid", "ASC");
        $this->query = $this->db->get('warehouse');
        if ($this->query->num_rows() > 0) {
            foreach ($this->query->result_array() as $k => $v) {
                $bcns[$v['bcn']][$v['wid']] = $v;
            }
        }
        echo '<table cellpadding="2" cellspacing="2" border="1"><tr><th>BCN</th><th>Title</th><th>Auction ID</th><th>Auction Title</th><th>New</th><th>Keep</th></tr>';
        foreach ($bcns as $k => $v) {
            //printcool (count($bcns[$k]));
            if (count($bcns[$k]) > 1) {
                foreach ($bcns[$k] as $bk => $bv) {
                    if (trim($bv['warranty']) != 'KEEP') {
                        $e = $this->Mywarehouse_model->CheckBCNDoesNotExists('115-' . $bcn);
                        if (!$e) {
                            //if ($bv['bcn_p1'] != '115')  printcool ($bv['bcn_p1']);
                            echo '<tr><td>' . $bv['bcn'] . '</td><td>' . $bv['title'] . '</td><td>' . $bv['waid'] . '</td><td>' . $bv['aucid'] . '</td><td style="width:200px;">115-' . $bcn . '</td><td>' . $bv['warranty'] . '</td></tr>';
                            //$this->db->update('warehouse', array('bcn' => '115-'.$bcn, 'bcn_p2' => (int)$bcn, 'oldbcn' => $bv['bcn'].' - Dupped'), array('wid' => $bv['wid']));
                        } else
                            echo '<tr><td colspan="6"></td></tr>';
                        $bcn++;
                    } else
                        echo '<tr><td><strong>' . $bv['bcn'] . '</strong></td><td><strong>' . $bv['title'] . '</strong></td><td><strong>' . $bv['waid'] . '</strong></td><td><strong>' . $bv['aucid'] . '</strong></td><td style="width:200px; text-align:right;"><em><strong>' . $bv['bcn'] . '</strong></em></td><td><strong>' . $bv['warranty'] . '</strong></td></tr>';
                }
                //printcool ($bcns[$k]);
            }
        }
        echo '</table>';
    }

    function SetStore($action = 'Add', $categoryID = 0) {
        //http://developer.ebay.com/DevZone/XML/docs/Reference/eBay/SetStoreCategories.html#Request.ItemDestinationCategoryID
        switch ($action) {
            case 'Add':
            case 'Delete':
            case 'Move':
            case 'Rename': continue;
                break;
            default: $action = 'Add';
        }

        $this->Auth_model->CheckListings();
        set_time_limit(120);
        ini_set('mysql.connect_timeout', 120);
        ini_set('max_execution_time', 120);
        ini_set('default_socket_timeout', 120);
        require_once($this->config->config['ebaypath'] . 'get-common/keys.php');
        require_once($this->config->config['ebaypath'] . 'get-common/eBaySession.php');
        $name = $this->input->post('name', true);
        $parent = $this->input->post('to');
        //printcool ($_POST);


        $requestXmlBody = '<?xml version="1.0" encoding="utf-8" ?>';
        $requestXmlBody .= '<SetStoreCategories xmlns="urn:ebay:apis:eBLBaseComponents">';
        $requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
        $requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
        $requestXmlBody .= "<Version>$compatabilityLevel</Version>";
        $requestXmlBody .= "<Action>" . $action . "</Action>";
        if ($action == "Add" || $action == "Move")
            $requestXmlBody .= "<DestinationParentCategoryID>" . $parent . "</DestinationParentCategoryID>";
        $requestXmlBody .= "<StoreCategories>
							<CustomCategory>";
        if ($action !== "Add")
            $requestXmlBody .= "<CategoryID>" . $categoryID . "</CategoryID>";
        if ($action == "Add" || $action == "Rename")
            $requestXmlBody .= "<Name>" . $name . "</Name>";
        $requestXmlBody .= "</CustomCategory>
  							</StoreCategories>";
        $requestXmlBody .= ' </SetStoreCategories>';


        $verb = 'SetStoreCategories';
        $session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
        $responseXml = simplexml_load_string($session->sendHttpRequest($requestXmlBody));
        $status = (string) $responseXml->Status;
        if ($status == 'Complete')
            $this->session->set_flashdata('success_msg', 'SUCCESS');
    }

    function ListItems($page = 1, $page_mode = false) {
        //echo '<p>We have where '.$_POST['where'];
        $this->load->model('Myautopilot_model');
        $this->mysmarty->assign('prules', $this->Myautopilot_model->GetPredefined());

        $this->session->unset_userdata('submitredir');
        $this->mysmarty->assign('floatmenu', TRUE);
        $this->mysmarty->assign('hot', TRUE);
        $this->Auth_model->CheckListings();

        $this->load->model('Myseller_model');
        $this->statuses = $this->Myseller_model->assignstatuses();

        $this->session->unset_userdata('gotcats');
        //$this->db->select('title, content, date');
        //$this->db->distinct();
        //$this->db->get('table');

        if ($page_mode) {
            $session_search = $this->session->userdata('last_string');
            $session_where = $this->session->userdata('last_where');
            $session_zero = $this->session->userdata('last_zero');
            $session_ended = $this->session->userdata('last_ended');
            $session_mm = $this->session->userdata('last_mm');
            $session_bcnmm = $this->session->userdata('last_bcnmm');
            $session_sitesell = $this->session->userdata('last_sitesell');
        } else
            $session_search = $session_where = $session_zero = $session_ended = $session_mm = $session_sitesell = false;

        if (isset($_POST['search']))
            $string = htmlspecialchars(stripslashes($this->input->post('search', TRUE)));
        elseif ($session_search)
            $string = $this->session->userdata('last_string');
        else
            $string = '';

        if (isset($_POST['where']) && $_POST['where'] < 6)
            $where = (int) $this->input->post('where', TRUE);
        elseif ($session_where)
            $where = $this->session->userdata('last_where');
        else
            $where = '';

        if (isset($_POST['ended']))
            $ended = 1;
        elseif ($session_ended)
            $ended = $this->session->userdata('last_ended');
        else
            $ended = FALSE;

        if (isset($_POST['zero']))
            $zero = 1;
        elseif ($session_zero)
            $zero = $this->session->userdata('last_zero');
        else
            $zero = FALSE;
        //printcool ($_POST['zero']);
        if (isset($_POST['mm']))
            $mm = 1;
        elseif ($session_mm)
            $mm = $this->session->userdata('last_mm');
        else
            $mm = FALSE;

        if (isset($_POST['bcnmm']))
            $bcnmm = 1;
        elseif ($session_bcnmm)
            $bcnmm = $this->session->userdata('last_bcnmm');
        else
            $bcnmm = FALSE;

        if (isset($_POST['sitesell']))
            $sitesell = (int) $_POST['sitesell'];
        elseif ($session_sitesell)
            $sitesell = $this->session->userdata('last_sitesell');
        else
            $sitesell = FALSE;

        //printcool ($string);

        $this->session->set_userdata('last_string', $string);
        $this->mysmarty->assign('string', $string);
        $this->session->set_userdata('last_where', $where);
        $this->mysmarty->assign('where', $where);
        $this->session->set_userdata('last_ended', $ended);
        $this->mysmarty->assign('ended', $ended);
        $this->session->set_userdata('last_zero', $zero);
        $this->mysmarty->assign('zero', $zero);
        $this->session->set_userdata('last_mm', $mm);
        $this->mysmarty->assign('mm', $mm);
        $this->session->set_userdata('last_bcnmm', $bcnmm);
        $this->mysmarty->assign('bcnmm', $bcnmm);
        $this->session->set_userdata('last_sitesell', $sitesell);
        $this->mysmarty->assign('sitesell', $sitesell);


        $this->mysmarty->assign('adm', $this->Myebay_model->GetAdminList());

        //$where=3;
        //echo '<p>e_id='.$this->e_id;
        //$string=$this->e_id;
        //Add functionality to search for all kinds of listings category ebay, google, amazon. 
        //    Example:
        //string should be listing e_id where should be 2 (for ebay primary category)
        //$category_id should be the number of ebay primary category
        //function ListItems2($string, $where = '', $ended = '', $zero = '', $mm = '', $bcnmm, $sitesell, $page, $category_id)

        $data = $this->Myebay_model->ListItems($string, $where, $ended, $zero, $mm, $bcnmm, $sitesell, $page);
        if ($string != '' || $where != '' || $ended != '' || $zero != '' || $mm != '' || $bcnmm != '' || $sitesell != '')
            $page_mode = TRUE;
        $this->mysmarty->assign('counted', $data['count']);
        $this->mysmarty->assign('list', $data['results']);
        $this->mysmarty->assign('pages', $data['pages']);
        $this->mysmarty->assign('page', (int) $page);

        //echo '<p>'.$this->db->last_query();		

        $this->load->helper('directory');
        $this->load->helper('file');
        $responseXml = read_file($this->config->config['ebaypath'] . '/cats.txt');
        $sxml = simplexml_load_string($responseXml);
        $sc = array();
        if (isset($sxml->Store->CustomCategories->CustomCategory)) {
            foreach ($sxml->Store->CustomCategories->CustomCategory as $s) {
                $a = (array) $s;
                $sc[$a['CategoryID']] = $a['Name'];
            }
        }
        asort($sc);

        //echo '<p>'.$this->db->last_query();

        $this->mysmarty->assign('store', $sc);
        if (!$page_mode) {
            $this->session->unset_userdata('last_string');
            $this->session->unset_userdata('last_where');
        } else {
            $this->mysmarty->assign('searched', TRUE);
        }

        //$this->mysmarty->assign('catlist', json_encode($treearray));
        //$this->mysmarty->view('mysku/myebay_show_cats.html');
        //$this->mysmarty->view('mysku/myproducts_categories.html');
    }

    function CheckCatProd($id) {
        $this->db->where("wsc_id", $id);
        $check_cat = $this->db->get("warehouse_sku_categories")->num_rows();
        if ($check_cat == 1) {
            echo "Category";
        }
        $this->db->where("e_id", $id);
        $check_prod = $this->db->get("ebay")->num_rows();
        if ($check_prod == 1) {
            echo "Product";
        }
    }

    //function is called from javascript function onAfterDrop in file views\mysku\myproducts_categories.html
    function UpdateListingCategory() {
        $action = $this->input->post("action");
        $error = "";
        $from = $this->input->post('id_listing');
        $old_parent = 0;
        $product_id = 0;
        $category_id = 0;
        $newid = 0;
        $this->db->where("e_id", $from);
        $check_catprod = $this->db->get("ebay")->num_rows();
        if ($check_catprod >= 1) {
            $this->db->where("e_id", $from);
            $old_parent_data = $this->db->get("ebay")->result_object();
            $old_parent = $old_parent_data[0]->storeCatID;
            $product_id = $from;

            $this->db->where("wsc_id", $old_parent_data[0]->storeCatID);
            $old_parent_data2 = $this->db->get("warehouse_sku_categories")->result_object();
            $old_parentname = $old_parent_data2[0]->wsc_title;
        } else {
            $this->db->where("wsc_id", $from);
            $old_parent_data = $this->db->get("warehouse_sku_categories")->result_object();
            $old_parent = $old_parent_data[0]->wsc_parent;
            $old_parentname = $old_parent_data[0]->wsc_title;
            $category_id = $from;
        }
        $to = $this->input->post('id_category');
        $newname = $this->input->post('storeCatTitle');


        //We check $_POST['id_category']!=0 to prevent assigning not existing category
        if ($action == "move") {



            $this->db->where("e_id", $this->input->post('id_listing'));
            $check_eid = $this->db->get("ebay")->num_rows();

            if ($check_eid > 0) {
                //update listing's category
                $this->db->set('storeCatID', $this->input->post('id_category'), FALSE);
                $this->db->set('storeCatTitle', $this->input->post('storeCatTitle'));
                $this->db->where('e_id', $this->input->post('id_listing'));
                if ($this->db->update('ebay')) {
                    $error = "success";
                } else {
                    $error = "failed to move";
                }



                //Now show the moved listing
                $list[] = $this->Myebay_model->GetItem((int) $_POST['id_listing']);
                $this->mysmarty->assign('list', $list);
                $this->mysmarty->assign('е_id', $_POST['id_listing']);
                //echo '<p><font color="red">Listing is moved to category '.$_POST['storeCatTitle'].'</font>';
                $this->mysmarty->view('mysku/myebay_show_cats.html');
                //$this->mysmarty->view('mysku/myproducts_categories.html');
            } else {
                //FOR EBAY CATEGORIES
                $this->db->where("wsc_id", $this->input->post('id_category'));
                $check_parent = $this->db->get("warehouse_sku_categories")->result_object();

                //FOR EBAY CATEGORIES
                $this->db->where("wsc_id", $check_parent[0]->wsc_mainparent);
                $checkebay = $this->db->get("warehouse_sku_categories")->result_object();



                if ($checkebay[0]->wsc_title != "EBAY") {
                    //update category parent from store_categories
                    $this->db->set('dad_cat', $this->input->post('id_category'), FALSE);
                    $this->db->where('id', $this->input->post('id_listing'));
                    $this->db->update('categories_store');

                    //update category parent from warehouse_sku_categories
                    $this->db->set('wsc_parent', $this->input->post('id_category'), FALSE);
                    $this->db->set("wsc_mainparent", $check_parent[0]->wsc_mainparent);
                    $this->db->where('wsc_id', $this->input->post('id_listing'));
                    if ($this->db->update('warehouse_sku_categories')) {
                        $error = "success";
                    } else {
                        $error = "failed to move";
                    }

                    echo "is not a product";
                } else {
                    if ($check_parent[0]->level < 3) {
                        //update category parent from store_categories
                        $this->db->set('dad_cat', $this->input->post('id_category'), FALSE);
                        $this->db->where('id', $this->input->post('id_listing'));
                        $this->db->update('categories_store');

                        //update category parent from warehouse_sku_categories
                        $this->db->set('wsc_parent', $this->input->post('id_category'), FALSE);
                        $this->db->set("wsc_mainparent", $check_parent[0]->wsc_mainparent);
                        $this->db->where('wsc_id', $this->input->post('id_listing'));
                        if ($this->db->update('warehouse_sku_categories')) {
                            $error = "success";
                        } else {
                            $error = "failed to move";
                        }
                    } else {

                        echo "errorlv3";
                    }
                }
            }
        } else if ($action == "shortcut") {
            $id_listing = $this->input->post("id_listing");
            $id_parent = $this->input->post("id_parent");
            $parents = "";
            if (strpos($id_listing, '/SC') != false) {

                $id_listingready = explode("/SC/", $id_listing);
                //GET THE FIELD SC_TO INFO TO ADD THE NEW SHORTCUT
                $this->db->where("e_id", $id_listingready[0]);
                $shortcutinfo = $this->db->get("ebay")->result_object();

                if (strpos($shortcutinfo[0]->sc_to, $id_parent) == false) {
                    $parents = $shortcutinfo[0]->sc_to . $id_parent . ",";

                    //UPDATE THE PARENS USING FIELD SC_TO(SHORTCUT TO) THAT MEANS WHICH CATEGORIES WILL SHOW THIS PRODUCT AS SHORTCUT
                    $this->db->where("e_id", $id_listingready[0]);
                    $this->db->set("sc_to", $parents);
                    $this->db->update("ebay");
                    echo "created";
                    $error = "Shortcut created";
                } else {
                    echo "exists";
                    $error = "Shortcut already exists";
                }
            } else {
                $id_listingready = str_replace("/SC", "", $id_listing);
                //GET THE FIELD SC_TO INFO TO ADD THE NEW SHORTCUT
                $this->db->where("e_id", $id_listingready);
                $shortcutinfo = $this->db->get("ebay")->result_object();

                if (strpos($shortcutinfo[0]->sc_to, $id_parent) == false) {
                    $parents = $shortcutinfo[0]->sc_to . $id_parent . ",";

                    //UPDATE THE PARENS USING FIELD SC_TO(SHORTCUT TO) THAT MEANS WHICH CATEGORIES WILL SHOW THIS PRODUCT AS SHORTCUT
                    $this->db->where("e_id", $id_listingready);
                    $this->db->set("sc_to", $parents);
                    $this->db->update("ebay");
                    echo "created";
                    $error = "Shortcut created";
                } else {
                    echo "exists";
                    $error = "Shortcut already exists";
                }
            }
        } else if ($action == "copy") {

            $this->db->where("e_id", $this->input->post('id_listing'));
            $itemto_copy = $this->db->get("ebay")->result_object();
            foreach ($itemto_copy as $itemto_copy) {




                $to_copy = array(
                    "e_title" => $itemto_copy->e_title,
                    "e_sef" => $itemto_copy->e_sef,
                    "e_manuf" => $itemto_copy->e_manuf,
                    "e_model" => $itemto_copy->e_model,
                    "e_part" => $itemto_copy->e_part,
                    "e_qpart" => $itemto_copy->e_qpart,
                    "e_ebayq" => $itemto_copy->e_ebayq,
                    "old_e_part" => $itemto_copy->old_e_part,
                    "e_compat" => $itemto_copy->e_compat,
                    "e_package" => $itemto_copy->e_package,
                    "e_condition" => $itemto_copy->e_condition,
                    "e_shipping" => $itemto_copy->e_shipping,
                    "e_desc" => $itemto_copy->e_desc,
                    "idpath" => $itemto_copy->idpath,
                    "e_notice_header" => $itemto_copy->e_notice_header,
                    "e_notice_shipping" => $itemto_copy->e_notice_shipping,
                    "primaryCategory" => $itemto_copy->primaryCategory,
                    "pCTitle" => $itemto_copy->pCTitle,
                    "storeCatID" => $this->input->post("id_parent"),
                    "storeCatTitle" => $itemto_copy->storeCatTitle,
                    "categoryEbaySecondaryId" => $itemto_copy->categoryEbaySecondaryId,
                    "categoryEbaySecondaryTitle" => $itemto_copy->categoryEbaySecondaryTitle,
                    "categoryAmazonId" => $itemto_copy->categoryAmazonId,
                    "categoryGoogleId" => $itemto_copy->categoryGoogleId,
                    "listingDuration" => $itemto_copy->listingDuration,
                    "startPrice" => $itemto_copy->startPrice,
                    "buyItNowPrice" => $itemto_copy->buyItNowPrice,
                    "quantity" => $itemto_copy->quantity,
                    "xquantity" => $itemto_copy->xquantity,
                    "ebayquantity" => $itemto_copy->ebayquantity,
                    "ngen" => $itemto_copy->ngen,
                    "PaymentMethod" => $itemto_copy->PaymentMethod,
                    "Subtitle" => $itemto_copy->Subtitle,
                    "Condition" => $itemto_copy->Condition,
                    "upc" => $itemto_copy->upc,
                    "admin_id" => $itemto_copy->admin_id,
                    "created" => $itemto_copy->created,
                    "ebay_submitted" => $itemto_copy->ebay_submitted,
                    "ebay_msubm" => $itemto_copy->ebay_msubm,
                    "ebay_id" => $itemto_copy->ebay_id,
                    "Ack" => $itemto_copy->Ack,
                    "link" => $itemto_copy->link,
                    "InsertionF" => $itemto_copy->InsertionF,
                    "ListingF" => $itemto_copy->ListingF,
                    "unsubmited" => $itemto_copy->unsubmited,
                    "shipping" => $itemto_copy->shipping,
                    "location" => $itemto_copy->location,
                    "ebautrfts" => $itemto_copy->ebautrfts,
                    "submitlog" => $itemto_copy->submitlog,
                    "gsid1" => $itemto_copy->gsid1,
                    "gsid2" => $itemto_copy->gsid2,
                    "gsid3" => $itemto_copy->gsid3,
                    "gsid4" => $itemto_copy->gsid4,
                    "gsid5" => $itemto_copy->gsid5,
                    "mods" => $itemto_copy->mods,
                    "autorev" => $itemto_copy->autorev,
                    "autorevtxt" => $itemto_copy->autorevtxt,
                    "ebsold" => $itemto_copy->ebsold,
                    "sitesell" => $itemto_copy->sitesell,
                    "nwm" => $itemto_copy->nwm,
                    "ebended" => $itemto_copy->ebended,
                    "endedreason" => $itemto_copy->endedreason,
                    "gtaxonomy" => $itemto_copy->gtaxonomy,
                    "weight_oz" => $itemto_copy->weight_oz,
                    "weight_kg" => $itemto_copy->weight_kg,
                    "centralized" => $itemto_copy->centralized,
                    "qn_ch1" => $itemto_copy->qn_ch1,
                    "qn_ch2" => $itemto_copy->qn_ch2,
                    "qn_ch3" => $itemto_copy->qn_ch3,
                    "price_ch1" => $itemto_copy->price_ch1,
                    "price_ch2" => $itemto_copy->price_ch2,
                    "price_ch3" => $itemto_copy->price_ch3,
                    "audit" => $itemto_copy->audit,
                    "auditmk" => $itemto_copy->auditmk,
                    "ostock" => $itemto_copy->ostock,
                    "ooskeepalive" => $itemto_copy->ooskeepalive,
                    "dispose" => $itemto_copy->dispose,
                    "housekeeping" => $itemto_copy->housekeeping,
                    "eBay_specs" => $itemto_copy->eBay_specs,
                    "e_sku" => $itemto_copy->e_sku,
                    "show_banner" => $itemto_copy->show_banner,
                    "lock_google_cat" => $itemto_copy->lock_google_cat,
                    "lock_ebay_cat" => $itemto_copy->lock_ebay_cat,
                    "lock_amazon_cat" => $itemto_copy->lock_amazon_cat,
                    "lock_ebay_cat_private" => $itemto_copy->lock_ebay_cat_private,
                    "lock_google_cat_private" => $itemto_copy->lock_google_cat_private,
                    "lock_amazon_cat_private" => $itemto_copy->lock_amazon_cat_private,
                    "sc_to" => $itemto_copy->sc_to
                );
            }
            if ($this->db->insert("ebay", $to_copy)) {
                $error = "success";
            } else {
                $error = "error to copy";
            }
            $newid = $this->db->insert_id();



            if ($itemto_copy->e_img1 != "") {
                $file = 'ebay_images/' . $itemto_copy->idpath . "/" . $itemto_copy->e_img1;
                $newfile = 'ebay_images/' . $itemto_copy->idpath . "/" . $newid . $itemto_copy->e_img1;
                $file_thumb = 'ebay_images/' . $itemto_copy->idpath . "/thumb_" . $itemto_copy->e_img1;
                $newfile_thumb = 'ebay_images/' . $itemto_copy->idpath . "/thumb_" . $newid . $itemto_copy->e_img1;
                copy($file, $newfile);
                copy($file_thumb, $newfile_thumb);
            }
            if ($itemto_copy->e_img2 != "") {
                $file2 = 'ebay_images/' . $itemto_copy->idpath . "/" . $itemto_copy->e_img2;
                $newfile2 = 'ebay_images/' . $itemto_copy->idpath . "/" . $newid . $itemto_copy->e_img2;
                $file2_thumb = 'ebay_images/' . $itemto_copy->idpath . "/thumb_" . $itemto_copy->e_img2;
                $newfile2_thumb = 'ebay_images/' . $itemto_copy->idpath . "/thumb_" . $newid . $itemto_copy->e_img2;
                copy($file2, $newfile2);
                copy($file2_thumb, $newfile2_thumb);
            }
            if ($itemto_copy->e_img3 != "") {
                $file3 = 'ebay_images/' . $itemto_copy->idpath . "/" . $itemto_copy->e_img3;
                $newfile3 = 'ebay_images/' . $itemto_copy->idpath . "/" . $newid . $itemto_copy->e_img3;
                $file3_thumb = 'ebay_images/' . $itemto_copy->idpath . "/thumb_" . $itemto_copy->e_img3;
                $newfile3_thumb = 'ebay_images/' . $itemto_copy->idpath . "/thumb_" . $newid . $itemto_copy->e_img3;
                copy($file3, $newfile3);
                copy($file3_thumb, $newfile3_thumb);
            }
            if ($itemto_copy->e_img4 != "") {
                $file4 = 'ebay_images/' . $itemto_copy->idpath . "/" . $itemto_copy->e_img4;
                $newfile4 = 'ebay_images/' . $itemto_copy->idpath . "/" . $newid . $itemto_copy->e_img4;
                $file4_thumb = 'ebay_images/' . $itemto_copy->idpath . "/thumb_" . $itemto_copy->e_img4;
                $newfile4_thumb = 'ebay_images/' . $itemto_copy->idpath . "/thumb_" . $newid . $itemto_copy->e_img4;
                copy($file4, $newfile4);
                copy($file4_thumb, $newfile4_thumb);
            }
            if ($itemto_copy->e_img5 != "") {
                $file5 = 'ebay_images/' . $itemto_copy->idpath . "/" . $itemto_copy->e_img5;
                $newfile5 = 'ebay_images/' . $itemto_copy->idpath . "/" . $newid . $itemto_copy->e_img5;
                $file5_thumb = 'ebay_images/' . $itemto_copy->idpath . "/thumb_" . $itemto_copy->e_img5;
                $newfile5_thumb = 'ebay_images/' . $itemto_copy->idpath . "/thumb_" . $newid . $itemto_copy->e_img5;
                copy($file5, $newfile5);
                copy($file5_thumb, $newfile5_thumb);
            }
            if ($itemto_copy->e_img6 != "") {
                $file6 = 'ebay_images/' . $itemto_copy->idpath . "/" . $itemto_copy->e_img6;
                $newfile6 = 'ebay_images/' . $itemto_copy->idpath . "/" . $newid . $itemto_copy->e_img6;
                $file6_thumb = 'ebay_images/' . $itemto_copy->idpath . "/thumb_" . $itemto_copy->e_img6;
                $newfile6_thumb = 'ebay_images/' . $itemto_copy->idpath . "/thumb_" . $newid . $itemto_copy->e_img6;
                copy($file6, $newfile6);
                copy($file6_thumb, $newfile6_thumb);
            }
            if ($itemto_copy->e_img7 != "") {
                $file7 = 'ebay_images/' . $itemto_copy->idpath . "/" . $itemto_copy->e_img7;
                $newfile7 = 'ebay_images/' . $itemto_copy->idpath . "/" . $newid . $itemto_copy->e_img7;
                $file7_thumb = 'ebay_images/' . $itemto_copy->idpath . "/thumb_" . $itemto_copy->e_img7;
                $newfile7_thumb = 'ebay_images/' . $itemto_copy->idpath . "/thumb_" . $newid . $itemto_copy->e_img7;
                copy($file7, $newfile7);
                copy($file7_thumb, $newfile7_thumb);
            }
            if ($itemto_copy->e_img8 != "") {
                $file8 = 'ebay_images/' . $itemto_copy->idpath . "/" . $itemto_copy->e_img8;
                $newfile8 = 'ebay_images/' . $itemto_copy->idpath . "/" . $newid . $itemto_copy->e_img8;
                $file8_thumb = 'ebay_images/' . $itemto_copy->idpath . "/thumb_" . $itemto_copy->e_img8;
                $newfile8_thumb = 'ebay_images/' . $itemto_copy->idpath . "/thumb_" . $newid . $itemto_copy->e_img8;
                copy($file8, $newfile8);
                copy($file8_thumb, $newfile8_thumb);
            }

            $images = array(
                "e_img1" => $newid . $itemto_copy->e_img1,
                "e_img2" => $newid . $itemto_copy->e_img2,
                "e_img3" => $newid . $itemto_copy->e_img3,
                "e_img4" => $newid . $itemto_copy->e_img4,
                "e_img5" => $newid . $itemto_copy->e_img5,
                "e_img6" => $newid . $itemto_copy->e_img6,
                "e_img7" => $newid . $itemto_copy->e_img7,
                "e_img8" => $newid . $itemto_copy->e_img8,
            );
            $this->db->where("e_id", $newid);
            $this->db->update("ebay", $images);
        }
        if ($to == null) {
            $to = $this->input->post("id_parent");
        }
        if ($action == "shortcut") {
            $action = "shortcut created";
        }

        $log = array(
            "user_id" => $this->session->userdata("admin_id"),
            "username" => $this->session->userdata("name"),
            "action" => $action,
            "old_parent" => $old_parent,
            "new_parent" => $to,
            "category_id" => $category_id,
            "product_id" => $product_id,
            "new_productid" => $newid,
            "old_parentname" => $old_parentname,
            "new_parentname" => $newname,
            "status" => $error,
            "date" => date("Y-m-d H:i:s")
        );

        $this->db->insert("warehouse_sku_categories_log", $log);
    }

    function CategoryReport($store_cat_id = 759249013) {
        //echo '<p>Store Cat Id in Report '.$store_cat_id;

        $query = $this->db->query('select "eBayPrimery" as Category_Owner, ' . $store_cat_id . ' as StoreCatId, primaryCategory as Id,pCTitle as Category, count(*) as Listings from ebay
                            where storeCatID = ' . $store_cat_id . '
                            group by primaryCategory
                            union all
                            select "eBaySecondary" as Category_Owner, ' . $store_cat_id . ' as StoreCatId, categoryEbaySecondaryId  as Id, categoryEbaySecondaryTitle, count(*) from ebay  
                            where storeCatID = ' . $store_cat_id . '
                            group by categoryEbaySecondaryId
                            union all
                            select "Amazon" as Category_Owner, ' . $store_cat_id . ' as StoreCatId, categoryAmazonId as Id, amazon_cat_title as Category, count(*) from ebay left join categories_amazon on id_amazon = categoryAmazonId
                            where storeCatID = ' . $store_cat_id . '
                            group by categoryAmazonId
                            union all
                            select "Google" as Category_Owner, ' . $store_cat_id . ' as StoreCatId, categoryGoogleId  as Id, google_cat_title as Category, count(*) from ebay left join categories_google on id_google = categoryGoogleId
                            where storeCatID = ' . $store_cat_id . '
                            group by categoryGoogleId');
        $array_listings_categories = $query->result_array();



        $this->mysmarty->assign('listings_cats_report', $array_listings_categories);

        //echo '<p>'.$this->db->last_query();
        //printcool($array_listings_categories);
    }

    function undo() {
        $log_id = $this->input->post("log_id");

        $this->db->where("id", $log_id);
        $log_data = $this->db->get("warehouse_sku_categories_log")->result_object();
        foreach ($log_data as $log_data) {
            if ($log_data->category_id != 0) {
                $this->db->where("wsc_id", $log_data->category_id);
                $this->db->set("wsc_parent", $log_data->old_parent);
                $this->db->update("warehouse_sku_categories");


                $log = array(
                    "user_id" => $this->session->userdata("admin_id"),
                    "username" => $this->session->userdata("name"),
                    "action" => $log_data->action,
                    "old_parent" => $log_data->new_parent,
                    "new_parent" => $log_data->old_parent,
                    "category_id" => $log_data->category_id,
                    "old_parentname" => $log_data->new_parentname,
                    "new_parentname" => $log_data->old_parentname,
                    "status" => "undo",
                    "date" => date("Y-m-d H:i:s"),
                    "category_name" => $log_data->category_name,
                    "google_lock" => $log_data->google_lock,
                    "amazon_lock" => $log_data->amazon_lock,
                    "ebay_lock" => $log_data->ebay_lock,
                    "amazon_lock_private" => $log_data->amazon_lock_private,
                    "google_lock_private" => $log_data->google_lock_private,
                    "ebay_lock_private" => $log_data->ebay_lock_private
                );

                $this->db->insert("warehouse_sku_categories_log", $log);
            } else if ($log_data->product_id != 0) {


                $this->db->where("e_id", $log_data->product_id);
                if (strpos($log_data->action, "lock") == false) {
                    $this->db->set("storeCatID", $log_data->old_parent);
                }
                $this->db->set("lock_google_cat", $log_data->google_lock);
                $this->db->set("lock_ebay_cat", $log_data->ebay_lock);
                $this->db->set("lock_amazon_cat", $log_data->amazon_lock);
                $this->db->set("lock_amazon_cat_private", $log_data->amazon_lock_private);
                $this->db->set("lock_google_cat_private", $log_data->google_lock_private);
                $this->db->set("lock_ebay_cat_private", $log_data->ebay_lock_private);
                $this->db->update("ebay");



                $log = array(
                    "user_id" => $this->session->userdata("admin_id"),
                    "username" => $this->session->userdata("name"),
                    "action" => $log_data->action,
                    "old_parent" => $log_data->new_parent,
                    "new_parent" => $log_data->old_parent,
                    "product_id" => $log_data->product_id,
                    "old_parentname" => $log_data->new_parentname,
                    "new_parentname" => $log_data->old_parentname,
                    "status" => "undo",
                    "date" => date("Y-m-d H:i:s"),
                    "product_name" => $log_data->product_name,
                    "google_lock" => $log_data->google_lock,
                    "amazon_lock" => $log_data->amazon_lock,
                    "ebay_lock" => $log_data->ebay_lock,
                    "amazon_lock_private" => $log_data->amazon_lock_private,
                    "google_lock_private" => $log_data->google_lock_private,
                    "ebay_lock_private" => $log_data->ebay_lock_private
                );

                $this->db->insert("warehouse_sku_categories_log", $log);
            }
        }
    }

    function UpdateGroupCategory($type = null) {
        $cat_child_id = $this->input->post("cat_child_id");
        $catid = $this->input->post("catid");
        $lock = $this->input->post("lock");
        if ($type == "google") {
            if ($cat_child_id == null) {
                $cat_child_id = 0;
            }
            $this->db->where("storecatid", $catid);
            $this->db->where("categorygoogleid", $cat_child_id);
            $items = $this->db->get("ebay")->result_object();
            foreach ($items as $google_items) {

                $this->db->set("lock_google_cat", $lock);
                $this->db->where("lock_google_cat_private", 0);
                $this->db->where("e_id", $google_items->e_id);
                $this->db->update("ebay");

                $log = array(
                    "user_id" => $this->session->userdata("admin_id"),
                    "username" => $this->session->userdata("name"),
                    "action" => "lockgooglegeneral",
                    "product_id" => $google_items->e_id,
                    "status" => "success",
                    "date" => date("Y-m-d H:i:s"),
                    "product_name" => $google_items->e_title,
                    "google_lock" => $lock,
                    "amazon_lock" => 0,
                    "ebay_lock" => 0,
                    "google_lock_private" => 0,
                    "amazon_lock_private" => 0,
                    "ebay_lock_private" => 0
                );
                $this->db->insert("warehouse_sku_categories_log", $log);
            }
        } else if ($type == "amazon") {
            if ($cat_child_id == null) {
                $cat_child_id = 0;
            }
            $this->db->where("storecatid", $catid);
            $this->db->where("categoryamazonid", $cat_child_id);
            $items = $this->db->get("ebay")->result_object();
            foreach ($items as $amazon_items) {

                $this->db->set("lock_amazon_cat", $lock);
                $this->db->where("lock_amazon_cat_private", 0);
                $this->db->where("e_id", $amazon_items->e_id);
                $this->db->update("ebay");

                $log = array(
                    "user_id" => $this->session->userdata("admin_id"),
                    "username" => $this->session->userdata("name"),
                    "action" => "lockamazongeneral",
                    "product_id" => $amazon_items->e_id,
                    "status" => "success",
                    "date" => date("Y-m-d H:i:s"),
                    "product_name" => $amazon_items->e_title,
                    "google_lock" => $lock,
                    "amazon_lock" => 0,
                    "ebay_lock" => 0,
                    "google_lock_private" => 0,
                    "amazon_lock_private" => 0,
                    "ebay_lock_private" => 0
                );
                $this->db->insert("warehouse_sku_categories_log", $log);
            }
        } else if ($type == "ebay") {
            if ($cat_child_id == null) {
                $cat_child_id = 0;
            }
            $this->db->where("storecatid", $catid);
            $this->db->where("primarycategory", $cat_child_id);
            $items = $this->db->get("ebay")->result_object();
            foreach ($items as $ebay_items) {

                $this->db->set("lock_ebay_cat", $lock);
                $this->db->where("lock_ebay_cat_private", 0);
                $this->db->where("e_id", $ebay_items->e_id);
                $this->db->update("ebay");

                $log = array(
                    "user_id" => $this->session->userdata("admin_id"),
                    "username" => $this->session->userdata("name"),
                    "action" => "lockebaygeneral",
                    "product_id" => $ebay_items->e_id,
                    "status" => "success",
                    "date" => date("Y-m-d H:i:s"),
                    "product_name" => $ebay_items->e_title,
                    "google_lock" => $lock,
                    "amazon_lock" => 0,
                    "ebay_lock" => 0,
                    "google_lock_private" => 0,
                    "amazon_lock_private" => 0,
                    "ebay_lock_private" => 0
                );
                $this->db->insert("warehouse_sku_categories_log", $log);
            }
        }
    }

    function GetSuggestedCategoriesSecondary($searchstring = '') {
        if (isset($_POST['src']))
            $searchstring = trim($_POST['src']);
        if ($searchstring == '')
            return 'No search string inputed';

        //echo '<input id="catsearch" name="catsearch" value="'.$searchstring.'" style="width:250px;">&nbsp;&nbsp;<a href="javascript:void(0)" onClick="var csrc = document.getElementById(\'catsearch\').value; SelectShipping(csrc)"><img src="'.base_url().'images/admin/b_search.png" /> Get eBay Suggested</a><br><br>';

        set_time_limit(180);
        ini_set('mysql.connect_timeout', 180);
        ini_set('max_execution_time', 180);
        ini_set('default_socket_timeout', 180);

        require_once($this->config->config['ebaypath'] . 'get-common/keys.php');
        require_once($this->config->config['ebaypath'] . 'get-common/eBaySession.php');

        $verb = 'GetSuggestedCategories';

        //Create a new eBay session with all details pulled in from included keys.php
        $session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);

        $requestXmlBody = '<?xml version="1.0" encoding="utf-8" ?>';
        $requestXmlBody .= '<GetSuggestedCategoriesRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        $requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
        $requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';
        $requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
        $requestXmlBody .= '<ErrorHandling>BestEffort</ErrorHandling>';
        $requestXmlBody .= '<Query>' . $searchstring . '</Query>';
        $requestXmlBody .= "<Version>$compatabilityLevel</Version>";
        $requestXmlBody .= '</GetSuggestedCategoriesRequest>';
        $responseXml = $session->sendHttpRequest($requestXmlBody);
        if (stristr($responseXml, 'HTTP 404') || $responseXml == '')
            die('<P>Error sending request');

        $xml = simplexml_load_string($responseXml);
        $cats = $xml->SuggestedCategoryArray->SuggestedCategory;
        echo '<select id="ebay2" name="ebay2">';

        foreach ($cats as $c) {
            $c = $this->_XML2Array($c);
            $c = $c['Category'];
            $cgcstr = '';
            echo '<option value="' . $c['CategoryID'] . '">';

            if (isset($c['CategoryParentID']) && (count($c['CategoryParentID']) > 0)) {
                /* foreach ($c['CategoryParentID'] as $k => $v)
                  {
                  echo $c['CategoryParentName'][$k].' <strong>&rArr;</strong> ';		//('.$v.')
                  } */
                if (is_array($c['CategoryParentID']))
                    foreach ($c['CategoryParentName'] as $v) {
                        $cgcstr .= $v . ' <strong>&rArr;</strong> ';
                        echo $v . ' <strong>&rArr;</strong> ';
                    }//('.$v.')
                else {
                    $cgcstr .= $c['CategoryParentName'] . ' <strong>&rArr;</strong> ';
                    echo $c['CategoryParentName'] . ' <strong>&rArr;</strong> ';
                }
            }


            //echo '<strong><input onlick="javascript:void(0)" onClick="SaveShipping('.(int)$c['CategoryID'].', '.$c['CategoryName'].', '.$searchstring.')" type="radio" id="'.(int)$c['CategoryID'].'" value="'.$c['CategoryName'].'" name="primaryCategory" /> <label for="'.(int)$c['CategoryID'].'"></label></strong>
            $cgcstr .= $c['CategoryName'];
            echo '<strong>' . $c['CategoryName'] . '</strong>';
            echo '</option>';

            $gotcats[] = array('catID' => $c['CategoryID'], 'catName' => $cgcstr);

            /*
              &nbsp;&nbsp;
              <input type="hidden" id="id'.$c['CategoryID'].'" value="'.$c['CategoryID'].'">
              <input type="hidden" id="name'.$c['CategoryID'].'" value="'.$c['CategoryName'].'">
              <input type="hidden" id="ss'.$c['CategoryID'].'" value="'.$searchstring.'">

              &nbsp;&nbsp;<a href="javascript:void(0)" onclick="var cid = document.getElementById(id'.$c['CategoryID'].').value; var cname = document.getElementById(name'.$c['CategoryID'].').value; var css = document.getElementById(\ss'.$c['CategoryID'].').value; SaveShipping(cid, cname, css)" style=" color:#0099FF;">SELECT</a><Br><br>'; */
            // ('.$c['CategoryID'].')
        }
        echo '</select>';
        //printcool ($xml);

        if (isset($gotcats) && (count($gotcats) > 0))
            $this->session->set_userdata(array('gotcats' => $gotcats));
    }

//This function is called from JS function SaveManyCategories in file system_myproducts_categories.html
    function EditManyCategories($Store_Cat_Id = null, $categoryEbayPrimaryId = null, $categoryEbaySecondaryId = null, $categoryAmazonId = null, $categoryGoogleId = null) {
        /* With Category_Owner we locate which is the column with category from ebay table which must be
          selected with value of the $Category_Id. So $Category_Id could be any number from columns
          categoryEbaySecondaryId
          primaryCategory
          categoryAmazonId
          categoryGoogleId
          in the ebay table in the database
         */

        //We have to use POST because there are slashes (/) in category names and we cant pass them as parameter in link
        if ($_POST['categoryTitle']) {
            $categoryTitle = $_POST['categoryTitle'];
        }

        //echo '<p>EditManyCategories Category Id = '.$this->session->userdata('Category_Id');
        //echo '<p>EditManyCategories Category Owner = '.$this->session->userdata('Category_Owner');
        //The session values below are set in function ShowListingsInCategoryInCategory()

        if ($this->session->userdata('Category_Owner')) {

            switch ($this->session->userdata('Category_Owner')) {
                case "eBayPrimery":
                    $where_column = 'primaryCategory';
                    $update_value = $categoryEbayPrimaryId;
                    $this->db->set('pCTitle', $categoryTitle, TRUE);
                    $lock_cat_private = "lock_ebay_cat_private";
                    $lock_cat = "lock_ebay_cat";
                    break;
                case "eBaySecondary":
                    $where_column = 'categoryEbaySecondaryId';
                    $update_value = $categoryEbaySecondaryId;
                    $this->db->set('categoryEbaySecondaryTitle', $categoryTitle, TRUE);
                    break;
                case "Amazon":
                    $where_column = 'categoryAmazonId';
                    $update_value = $categoryAmazonId;
                    $lock_cat_private = "lock_amazon_cat_private";
                    $lock_cat = "lock_amazon_cat";
                    break;
                case "Google":
                    $where_column = 'categoryGoogleId';
                    $update_value = $categoryGoogleId;
                    $lock_cat_private = "lock_google_cat_private";
                    $lock_cat = "lock_google_cat";
                    break;
                default:
                    echo "<p>Unknown column to update in function EditManyCategories";
                    return;
            }
            //echo '<p>sessions are ok';
            //$this->db->set('primaryCategory', $categoryEbayPrimaryId, FALSE);
            //$this->db->set('categoryEbaySecondaryId', $categoryEbaySecondaryId, FALSE);
            //$this->db->set('categoryAmazonId', $categoryAmazonId, FALSE);
            //$this->db->set('categoryGoogleId', $categoryGoogleId, FALSE);
            //$this->db->set('storeCatTitle', $_POST['storeCatTitle']); Viz tova posle

            $this->db->set($where_column, $update_value, FALSE);

            $this->db->where('storeCatID', $Store_Cat_Id);
            $this->db->where($lock_cat_private, 0);
            $this->db->where($lock_cat, 0);
            $this->db->where($where_column, $this->session->userdata('Category_Id'));

            $this->db->update('ebay');
            //echo "<p>".$this->db->last_query();
            //Now show the moved listing
            //$this->mysmarty->assign('message', '<font color="red">Listings are/is moved to category '.$categoryTitle.'</font>');
            //$this->mysmarty->view('mysku/myebay_show');
            //$this->mysmarty->view('mysku/myproducts_categories.html');

            unset($cat_to_update);
            $this->mysmarty->assign('cat_to_update', $cat_to_update);

            $this->CategoryReport($Store_Cat_Id);
            $this->mysmarty->view('mysku/mysku_report.html');
        } else {
            echo '<p>Error in many listings categories update!';
        }
    }

    function ShowListingsInCategoryInCategory($categoryid = null, $Category_Owner = null, $page = 1, $page_mode = false, $main_cat = null, $total_listed = 0) {
        $this->load->helper('directory');
        $this->load->helper('file');
        // We have Store Category in Session variable from ShowListingsInCategory function which is executed always first.
        // echo '<p>Search post ShowListingsInCategoryInCategory = '.$_POST['search'];
        //echo '<p>Cat owner ShowListingsInCategoryInCategory = '.$Category_Owner;
        //echo '<p>Category id = '.$categoryid;
        //echo '<p>PAGE ShowListingsInCategoryInCategory = '.$page;

        /* We will need the values below when we want to make bulk update of the selected listings categories in
          function EditManyCategories() in the current file. */

        if ($page == 1)
            $this->session->set_userdata('Category_Owner', $Category_Owner); //Column to update with value from $categoryid
        if ($page == 1)
            $this->session->set_userdata('Category_Id', $categoryid); //Value to update could be any from Amazon, Google, ebay















//$this->mysmarty->assign('storeCatId', $this->session->userdata('StoreCatId'));
        //Important to assign them again because the page is reloaded.
        $this->mysmarty->assign('StoreCatId', $this->session->userdata('StoreCatId'));
        $this->mysmarty->assign('StoreCatIdName', $this->session->userdata('StoreCatIdName'));


        //echo '<p>Category_Owner is '.$this->session->userdata('Category_Owner');
        //echo '<p>Category_ID is '.$this->session->userdata('Category_Id');
        //echo '<p>Store_cat_ID is '.$this->session->userdata('StoreCatId');
        //echo '<p>Searched is '.$page_mode;

        switch ($this->session->userdata('Category_Owner')) {
            case "eBayPrimery":


                // echo '<p>IN CASE eBay_Primery';    
                $cat_to_update = 'eBayPrimery';

                if (!isset($categoryid))
                    $mySelectEbayFirst = $this->session->userdata('Category_Id');
                else
                    $mySelectEbayFirst = $categoryid;

                $this->mysmarty->assign('mySelectEbayFirst', $mySelectEbayFirst);
                $this->db->where("storeCatID", $this->session->userdata('StoreCatId'));
                $this->db->where("primarycategory", $mySelectEbayFirst);
                $get_locks = $this->db->get("ebay")->result_object();
                break;
            case "eBaySecondary":
                $cat_to_update = 'eBaySecondary';

                if (!isset($categoryid))
                    $mySelectEbaySecond = $this->session->userdata('Category_Id');
                else
                    $mySelectEbaySecond = $categoryid;


                $this->mysmarty->assign('mySelectEbaySecond', $mySelectEbaySecond);
                break;
            case "Amazon":


                $cat_to_update = 'Amazon';
                //$mySelectAmazon = $categoryid;

                if (!isset($categoryid))
                    $mySelectAmazon = $this->session->userdata('Category_Id');
                else
                    $mySelectAmazon = $categoryid;

                $this->mysmarty->assign('mySelectAmazon', $mySelectAmazon);
                $this->db->where("storeCatID", $this->session->userdata('StoreCatId'));
                $this->db->where("categoryamazonid", $mySelectAmazon);
                $get_locks = $this->db->get("ebay")->result_object();
                break;
            case "Google":


                $cat_to_update = 'Google';
                // $mySelectGoogle = $categoryid;

                if (!isset($categoryid))
                    $mySelectGoogle = $this->session->userdata('Category_Id');
                else
                    $mySelectGoogle = $categoryid;

                $this->mysmarty->assign('mySelectGoogle', $mySelectGoogle);
                $this->db->where("storeCatID", $this->session->userdata('StoreCatId'));
                $this->db->where("categorygoogleid", $mySelectGoogle);
                $get_locks = $this->db->get("ebay")->result_object();
                break;
            default:
                $cat_to_update = 0;
            //echo '<p>Deafult';  
        }

        $this->mysmarty->assign('cat_to_update', $cat_to_update);

        //echo '<p>Session Category_Owner = '+$this->session->userdata('Category_Owner');
        //$this->session->set_userdata('Store_Cat_Id', $Store_Cat_Id);


        if ($this->session->userdata('StoreCatId') !== null) {
            //echo '<p>VIZ cat='.$_POST['search'];
            //echo '<h2>All listings in category '.$_POST['CategoryName'].'</h2>';
            //echo "<p>Category Id from session is ".$this->session->userdata('Category_Id');

            $this->ListItemsForCategory($page, $page_mode, $this->session->userdata('Category_Id'), $this->session->userdata('Category_Owner'), $this->session->userdata('StoreCatId'));

            //$this->mysmarty->assign('storeCategoryName', $_POST['CategoryName']);

            if ($_POST['CategoryName']) {
                $this->session->set_userdata('storeCategoryName', $_POST['CategoryName']);
                $this->mysmarty->assign('StoreCatId', $this->session->userdata('StoreCatId'));
            } else {
                $this->mysmarty->assign('storeCategoryName', $this->session->userdata('storeCategoryName'));
            }


            $this->mysmarty->assign('showTabCategories', TRUE);
            //$this->mysmarty->assign('list', $list);

            $this->CategoryReport($this->session->userdata('StoreCatId'));

            $queryeStore = $this->db->query('select id, id_store, store_cat_title from categories_store');
            $queryeGoogle = $this->db->query('select id, id_google, google_cat_title from categories_google');
            $queryeAmazon = $this->db->query('select id, id_amazon, amazon_cat_title from categories_amazon');
            $queryeBay1 = $this->db->query('select distinct primaryCategory, pCTitle from ebay where primaryCategory is not null and primaryCategory<>0 and pCTitle is not null');
            $queryeBay2 = $this->db->query('select distinct categoryEbaySecondaryId, categoryEbaySecondaryTitle from ebay where categoryEbaySecondaryId is not null and categoryEbaySecondaryId<>0 and categoryEbaySecondaryTitle is not null');


            //printcool($queryeBay->result_array());
            if ($queryeStore->result_array() != null) {
                foreach ($queryeStore->result_array() as $row) {
                    $this->storeCategories[$row['id_store']] = $row['store_cat_title'];
                }
            }
            $this->storeCategories[0] = '';
            if ($queryeGoogle->result_array() != null) {
                foreach ($queryeGoogle->result_array() as $row) {
                    $this->googleCategories[$row['id_google']] = $row['google_cat_title'];
                }
            }
            $this->googleCategories[0] = '';
            if ($queryeAmazon->result_array() != null) {
                foreach ($queryeAmazon->result_array() as $row) {
                    $this->amazonCategories[$row['id_amazon']] = $row['amazon_cat_title'];
                }
            }
            $this->amazonCategories[0] = '';
            if ($queryeBay1->result_array() != null) {
                foreach ($queryeBay1->result_array() as $row) {
                    $this->ebayCategories1[$row['primaryCategory']] = $row['pCTitle'];
                }
            }
            $this->ebayCategories1[0] = '';
            if ($queryeBay2->result_array() != null) {
                foreach ($queryeBay2->result_array() as $row) {
                    $this->ebayCategories2[$row['categoryEbaySecondaryId']] = $row['categoryEbaySecondaryTitle'];
                }
            }
            $this->ebayCategories2[0] = '';


            //printcool($this->storeCategories);
            //CHECK LOCKS FOR THE LISTINGS IN GOOGLE CAT
            if ($get_locks != null) {
                foreach ($get_locks as $check_google) {
                    if ($check_google->lock_google_cat == 1) {
                        @$lock_google = 1;
                    } else if ($check_google->lock_google_cat == 0) {
                        @$lock_google = 0;
                        break;
                    }
                }

                //CHECK LOCKS FOR THE LISTINGS IN AMAZON CAT
                foreach ($get_locks as $check_amazon) {
                    if ($check_amazon->lock_amazon_cat == 1) {
                        @$lock_amazon = 1;
                    } else if ($check_amazon->lock_amazon_cat == 0) {
                        @$lock_amazon = 0;
                        break;
                    }
                }
                //CHECK LOCKS FOR THE LISTINGS IN EBAY CAT
                foreach ($get_locks as $check_ebay) {
                    if ($check_ebay->lock_ebay_cat == 1) {
                        @$lock_ebay = 1;
                    } else if ($check_ebay->lock_ebay_cat == 0) {
                        @$lock_ebay = 0;
                        break;
                    }
                }
            }
            require_once($this->config->config['ebaypath'] . 'get-common/eBaySession.php');

            $itemID = $this->Myebay_model->GetStoreFirstProduct($main_cat);
            set_time_limit(90);
            ini_set('mysql.connect_timeout', 90);
            ini_set('max_execution_time', 90);
            ini_set('default_socket_timeout', 90);
            require_once($this->config->config['ebaypath'] . 'get-common/keys.php');
            require_once($this->config->config['ebaypath'] . 'get-common/eBaySession.php');

            $requestXmlBody = '<?xml version="1.0" encoding="utf-8" ?>';
            $requestXmlBody .= '<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
            $requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
            $requestXmlBody .= '<DetailLevel>ItemReturnAttributes</DetailLevel>';
            $requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
            $requestXmlBody .= "<Version>$compatabilityLevel</Version>";
            $requestXmlBody .= '<ItemID>' . (int) $itemID . '</ItemID>
						</GetItemRequest>';
            $verb = 'GetItem';
            $session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
            $responseXml = $session->sendHttpRequest($requestXmlBody);
            if (stristr($responseXml, 'HTTP 404') || $responseXml == '')
                die('<P>Error sending request');

            $xml = simplexml_load_string($responseXml);
            //print_r($xml);

            $sd = array();

            if (isset($xml->Item->ShippingDetails->ShippingServiceOptions)) {
                foreach ($xml->Item->ShippingDetails->ShippingServiceOptions as $s) {
                    $sd[(int) $s->ShippingServicePriority] = array('ShippingService' => (string) $s->ShippingService, 'ShippingServiceCost' => (float) $s->ShippingServiceCost, 'ShippingServiceAdditionalCost' => (float) $s->ShippingServiceAdditionalCost, 'FreeShipping' => (string) $s->FreeShipping
                    );
                }
            }
            $is = array();
            if (isset($xml->Item->ShippingDetails->InternationalShippingServiceOption)) {
                foreach ($xml->Item->ShippingDetails->InternationalShippingServiceOption as $s) {
                    $is[(int) $s->ShippingServicePriority] = array('ShippingService' => (string) $s->ShippingService, 'ShippingServiceCost' => (float) $s->ShippingServiceCost, 'ShippingServiceAdditionalCost' => (float) $s->ShippingServiceAdditionalCost, 'ShipToLocation' => (string) $s->ShipToLocation);
                }
            }


            //GET QTT OF LOCK AND UNLOCKED ITEMS
            $google_unlocked = 0;
            $google_locked = 0;
            $ebay_unlocked = 0;
            $ebay_locked = 0;
            $amazon_unlocked = 0;
            $amazon_locked = 0;




            //GOOGLE LOCKS
            $this->db->where("storeCatID", $this->session->userdata('StoreCatId'));
            $this->db->where("categorygoogleid", @$mySelectGoogle);
            $data_google = $this->db->get("ebay")->result_object();
            if (isset($data_google)) {
                foreach ($data_google as $locks_google) {
                    if ($locks_google->lock_google_cat == 0) {
                        $google_unlocked += 1;
                    } else {
                        $google_locked += 1;
                    }
                }
            }
            //EBAY LOCKS
            $this->db->where("storeCatID", $this->session->userdata('StoreCatId'));
            $this->db->where("primaryCategory", @$mySelectEbayFirst);
            $data_ebay = $this->db->get("ebay")->result_object();
            if (isset($data_ebay)) {
                foreach ($data_ebay as $locks_ebay) {
                    if ($locks_ebay->lock_ebay_cat == 0) {
                        $ebay_unlocked += 1;
                    } else {
                        $ebay_locked += 1;
                    }
                }
            }
            //AMAZON LOCKS
            $this->db->where("storeCatID", $this->session->userdata('StoreCatId'));
            $this->db->where("categoryAmazonId", @$mySelectAmazon);
            $data_amazon = $this->db->get("ebay")->result_object();
            if (isset($data_amazon)) {
                foreach ($data_amazon as $locks_amazon) {
                    if ($locks_amazon->lock_amazon_cat == 0) {
                        $amazon_unlocked += 1;
                    } else {
                        $amazon_locked += 1;
                    }
                }
            }

            $mywarehouse_categories = $this->db->get("warehouse_sku_categories")->result_array();

            $this->mysmarty->assign('ebay_locked', $ebay_locked);
            $this->mysmarty->assign('ebay_unlocked', $ebay_unlocked);

            $this->mysmarty->assign('amazon_locked', $amazon_locked);
            $this->mysmarty->assign('amazon_unlocked', $amazon_unlocked);

            $this->mysmarty->assign('google_locked', $google_locked);
            $this->mysmarty->assign('google_unlocked', $google_unlocked);


            $this->mysmarty->assign('total_listed', $total_listed);
            $this->mysmarty->assign('ShippingServices', $sd);
            $this->mysmarty->assign('IntlShippingServices', $is);

            @$this->mysmarty->assign('SellerExcludeShipToLocationsPreference', (string) $xml->Item->ShippingDetails->SellerExcludeShipToLocationsPreference);
            @$this->mysmarty->assign('ExcludeShipToLocation', (array) $xml->Item->ShippingDetails->ExcludeShipToLocation);
            $sresponseXml = read_file($this->config->config['ebaypath'] . '/shipping.txt');
            $shxml = simplexml_load_string($sresponseXml);
            $this->mysmarty->assign('ShippingOptions', $shxml->ShippingServiceDetails);
            $this->mysmarty->assign('shipcount', array(1, 2, 3, 4));
            $this->mysmarty->assign('myCatsStore', $mywarehouse_categories);
            $this->mysmarty->assign('myCatsEbay1', $this->ebayCategories1);
            $this->mysmarty->assign('myCatsEbay2', $this->ebayCategories2);
            $this->mysmarty->assign('myCatsAmazon', $this->amazonCategories);
            $this->mysmarty->assign('myCatsGoogle', $this->googleCategories);
            $this->mysmarty->assign('searchcat', 'Computers');
            $this->mysmarty->assign('page_control', 2);
            $this->mysmarty->assign('lock_google', $lock_google);
            $this->mysmarty->assign('lock_ebay', $lock_ebay);
            $this->mysmarty->assign('lock_amazon', $lock_amazon);

            $this->mysmarty->view('mysku/myebay_show.html');
        }
    }

    //We have two types of paging. One for showing all the listings in store category. The other one is when we want
    //to show paging for listings from specific category like eBay, Amazon,Google within the chosen Store Category.
    //With the $page_control variable we redirect the paging.
    function ListSearch($page = 1, $page_control = 1) {
        //$this->ListItemsForCategory((int)$page, TRUE);
        if ($page_control == 1) {
            $this->ShowListingsInCategory($page, true, null, null, $this->session->userdata('StoreCatId'));
            $this->mysmarty->assign('page_control', $page_control);
        } else {
            $this->ShowListingsInCategoryInCategory($this->session->userdata('Category_Id'), $this->session->userdata('Category_Owner'), $page, true);
            $this->mysmarty->assign('page_control', $page_control);
        }
    }

    function UpdateShipping($e_id = 0) {
        $cat_id = $this->input->post("cat_id");
        $cat_child = $this->input->post("cat_child");
        $type = $this->input->post("type");
        $shipping_exclude = $this->input->post("shipping_exclude");
        $exclude_location = $this->input->post("exclude_location");

        //RECEIVE DOMESTIC SHIPPING INFO
        $domestic_shipping_additional = $this->input->post("domestic_shipping_additional");
        $domestic_shipping_cost = $this->input->post("domestic_shipping_cost");
        $domestic_freeshipping = $this->input->post("domestic_freeshipping");
        $domestic_shipping_service = $this->input->post("domestic_shipping_service");
        parse_str($domestic_shipping_additional, $domestic_shipping_additional);
        parse_str($domestic_shipping_cost, $domestic_shipping_cost);
        parse_str($domestic_freeshipping, $domestic_freeshipping);
        parse_str($domestic_shipping_service, $domestic_shipping_service);
        foreach ($domestic_shipping_cost as $domestic_cost) {
            $domestic_cost1 = $domestic_cost['domestic'][1];
            $domestic_cost2 = $domestic_cost['domestic'][2];
            $domestic_cost3 = $domestic_cost['domestic'][3];
            $domestic_cost4 = $domestic_cost['domestic'][4];
        }
        foreach ($domestic_shipping_additional as $domestic_additional) {
            $domestic_additional1 = $domestic_additional['domestic'][1];
            $domestic_additional2 = $domestic_additional['domestic'][2];
            $domestic_additional3 = $domestic_additional['domestic'][3];
            $domestic_additional4 = $domestic_additional['domestic'][4];
        }
        foreach ($domestic_shipping_service as $domestic_service) {
            $domestic_service1 = $domestic_service['domestic'][1];
            $domestic_service2 = $domestic_service['domestic'][2];
            $domestic_service3 = $domestic_service['domestic'][3];
            $domestic_service4 = $domestic_service['domestic'][4];
        }

        //DOMESTIC ENDS HERE
        //RECEIVE INTERNATIONAL SHIPPING INFO
        $international_shipping_cost = $this->input->post("international_shipping_cost");
        $international_shipping_additional = $this->input->post("international_shipping_additional");
        $international_tolocation = $this->input->post("international_tolocation");
        $international_shipping_service = $this->input->post("international_shipping_service");
        parse_str($international_shipping_cost, $international_shipping_cost);
        parse_str($international_shipping_additional, $international_shipping_additional);
        parse_str($international_tolocation, $international_tolocation);
        parse_str($international_shipping_service, $international_shipping_service);
        foreach ($international_shipping_cost as $international_cost) {

            $international_cost1 = $international_cost['international'][1];
            $international_cost2 = $international_cost['international'][2];
            $international_cost3 = $international_cost['international'][3];
            $international_cost4 = $international_cost['international'][4];
        }
        foreach ($international_shipping_additional as $international_additional) {

            $international_additional1 = $international_additional['international'][1];
            $international_additional2 = $international_additional['international'][2];
            $international_additional3 = $international_additional['international'][3];
            $international_additional4 = $international_additional['international'][4];
        }
        foreach ($international_tolocation as $international_tolocation) {

            $international_tolocation1 = $international_tolocation['international'][1];
            $international_tolocation2 = $international_tolocation['international'][2];
            $international_tolocation3 = $international_tolocation['international'][3];
            $international_tolocation4 = $international_tolocation['international'][4];
        }
        foreach ($international_shipping_service as $international_service) {

            $international_service1 = $international_service['international'][1];
            $international_service2 = $international_service['international'][2];
            $international_service3 = $international_service['international'][3];
            $international_service4 = $international_service['international'][4];
        }
        //INTERNATIONAL ENDS HERE

        $shipping = array(
            "exclude" => $shipping_exclude,
            "locationexclude" => $exclude_location,
            "domestic" => array(
                "1" => array(
                    "ShippingServiceCost" => $domestic_cost1['ShippingServiceCost'],
                    "ShippingServiceAdditionalCost" => $domestic_additional1['ShippingServiceAdditionalCost'],
                    "ShippingService" => $domestic_service1['ShippingService']
                ),
                "2" => array(
                    "ShippingServiceCost" => $domestic_cost2['ShippingServiceCost'],
                    "ShippingServiceAdditionalCost" => $domestic_additional2['ShippingServiceAdditionalCost'],
                    "ShippingService" => $domestic_service2['ShippingService']
                ),
                "3" => array(
                    "ShippingServiceCost" => $domestic_cost3['ShippingServiceCost'],
                    "ShippingServiceAdditionalCost" => $domestic_additional3['ShippingServiceAdditionalCost'],
                    "ShippingService" => $domestic_service3['ShippingService']
                ),
                "4" => array(
                    "ShippingServiceCost" => $domestic_cost4['ShippingServiceCost'],
                    "ShippingServiceAdditionalCost" => $domestic_additional4['ShippingServiceAdditionalCost'],
                    "ShippingService" => $domestic_service4['ShippingService']
                )
            ),
            "international" => array(
                "1" => array(
                    "ShippingServiceCost" => $international_cost1['ShippingServiceCost'],
                    "ShippingServiceAdditionalCost" => $international_additional1['ShippingServiceAdditionalCost'],
                    "ShipToLocation" => $international_tolocation1['ShipToLocation'],
                    "ShippingService" => $international_service1['ShippingService']
                ),
                "2" => array(
                    "ShippingServiceCost" => $international_cost2['ShippingServiceCost'],
                    "ShippingServiceAdditionalCost" => $international_additional2['ShippingServiceAdditionalCost'],
                    "ShipToLocation" => $international_tolocation2['ShipToLocation'],
                    "ShippingService" => $international_service2['ShippingService']
                ),
                "3" => array(
                    "ShippingServiceCost" => $international_cost3['ShippingServiceCost'],
                    "ShippingServiceAdditionalCost" => $international_additional3['ShippingServiceAdditionalCost'],
                    "ShipToLocation" => $international_tolocation3['ShipToLocation'],
                    "ShippingService" => $international_service3['ShippingService']
                ),
                "4" => array(
                    "ShippingServiceCost" => $international_cost4['ShippingServiceCost'],
                    "ShippingServiceAdditionalCost" => $international_additional4['ShippingServiceAdditionalCost'],
                    "ShipToLocation" => $international_tolocation4['ShipToLocation'],
                    "ShippingService" => $international_service4['ShippingService']
                )
            )
        );

        if ($e_id != 0) {
            if ($shipping != null) {
                $this->db->set("shipping", serialize($shipping));
            } else {
                $this->db->set("shipping", 'NULL');
            }
            $this->db->where("e_id", $e_id);
            $this->db->update("ebay");
        } else {
            if ($cat_id != null) {
                if ($shipping != null) {
                    $this->db->set("shipping", serialize($shipping));
                } else {
                    $this->db->set("shipping", 'NULL');
                }
                $this->db->where("storeCatID", $cat_id);
                if ($type == "google") {
                    $this->db->where("categoryGoogleId", $cat_child);
                } else if ($type == "amazon") {
                    $this->db->where("categoryAmazonId", $cat_child);
                } else if ($type == "eBayPrimery") {
                    $this->db->where("primaryCategory", $cat_child);
                }
                $this->db->update("ebay");
            }
        }
    }

    function UpdateShippingPrivate($e_id = 0) {
        $cat_id = $this->input->post("cat_id");
        $cat_child = $this->input->post("cat_child");
        $type = $this->input->post("type");
        $shipping_exclude = $this->input->post("shipping_exclude");
        $exclude_location = $this->input->post("exclude_location");

        //RECEIVE DOMESTIC SHIPPING INFO
        $domestic_shipping_additional = $this->input->post("domestic_shipping_additional");
        $domestic_shipping_cost = $this->input->post("domestic_shipping_cost");
        $domestic_freeshipping = $this->input->post("domestic_freeshipping");
        $domestic_shipping_service = $this->input->post("domestic_shipping_service");
        parse_str($domestic_shipping_additional, $domestic_shipping_additional);
        parse_str($domestic_shipping_cost, $domestic_shipping_cost);
        parse_str($domestic_freeshipping, $domestic_freeshipping);

        parse_str($domestic_shipping_service, $domestic_shipping_service);
        foreach ($domestic_shipping_cost as $domestic_cost) {
            $domestic_cost1 = $domestic_cost['domestic'][1];
            $domestic_cost2 = $domestic_cost['domestic'][2];
            $domestic_cost3 = $domestic_cost['domestic'][3];
            $domestic_cost4 = $domestic_cost['domestic'][4];
        }
        foreach ($domestic_shipping_additional as $domestic_additional) {
            $domestic_additional1 = $domestic_additional['domestic'][1];
            $domestic_additional2 = $domestic_additional['domestic'][2];
            $domestic_additional3 = $domestic_additional['domestic'][3];
            $domestic_additional4 = $domestic_additional['domestic'][4];
        }
        foreach ($domestic_freeshipping as $domestic_freeshipping) {
            $domestic_freeshipping1 = $domestic_freeshipping['domestic'][1];
            $domestic_freeshipping2 = $domestic_freeshipping['domestic'][2];
            $domestic_freeshipping3 = $domestic_freeshipping['domestic'][3];
            $domestic_freeshipping4 = $domestic_freeshipping['domestic'][4];
        }
        foreach ($domestic_shipping_service as $domestic_service) {
            $domestic_service1 = $domestic_service['domestic'][1];
            $domestic_service2 = $domestic_service['domestic'][2];
            $domestic_service3 = $domestic_service['domestic'][3];
            $domestic_service4 = $domestic_service['domestic'][4];
        }

        //DOMESTIC ENDS HERE
        //RECEIVE INTERNATIONAL SHIPPING INFO
        $international_shipping_cost = $this->input->post("international_shipping_cost");
        $international_shipping_additional = $this->input->post("international_shipping_additional");
        $international_tolocation = $this->input->post("international_tolocation");
        $international_shipping_service = $this->input->post("international_shipping_service");
        parse_str($international_shipping_cost, $international_shipping_cost);
        parse_str($international_shipping_additional, $international_shipping_additional);
        parse_str($international_tolocation, $international_tolocation);
        parse_str($international_shipping_service, $international_shipping_service);
        foreach ($international_shipping_cost as $international_cost) {

            $international_cost1 = $international_cost['international'][1];
            $international_cost2 = $international_cost['international'][2];
            $international_cost3 = $international_cost['international'][3];
            $international_cost4 = $international_cost['international'][4];
        }
        foreach ($international_shipping_additional as $international_additional) {

            $international_additional1 = $international_additional['international'][1];
            $international_additional2 = $international_additional['international'][2];
            $international_additional3 = $international_additional['international'][3];
            $international_additional4 = $international_additional['international'][4];
        }
        foreach ($international_tolocation as $international_tolocation) {

            $international_tolocation1 = $international_tolocation['international'][1];
            $international_tolocation2 = $international_tolocation['international'][2];
            $international_tolocation3 = $international_tolocation['international'][3];
            $international_tolocation4 = $international_tolocation['international'][4];
        }
        foreach ($international_shipping_service as $international_service) {

            $international_service1 = $international_service['international'][1];
            $international_service2 = $international_service['international'][2];
            $international_service3 = $international_service['international'][3];
            $international_service4 = $international_service['international'][4];
        }
        //INTERNATIONAL ENDS HERE

        $shipping = array(
            "exclude" => $shipping_exclude,
            "locationexclude" => $exclude_location,
            "domestic" => array(
                "1" => array(
                    "ShippingServiceCost" => $domestic_cost1['ShippingServiceCost'],
                    "ShippingServiceAdditionalCost" => $domestic_additional1['ShippingServiceAdditionalCost'],
                    "ShippingService" => $domestic_service1['ShippingService'],
                    "ShippingFreeShipping" => $domestic_freeshipping1['FreeShipping']
                ),
                "2" => array(
                    "ShippingServiceCost" => $domestic_cost2['ShippingServiceCost'],
                    "ShippingServiceAdditionalCost" => $domestic_additional2['ShippingServiceAdditionalCost'],
                    "ShippingService" => $domestic_service2['ShippingService'],
                    "ShippingFreeShipping" => $domestic_freeshipping2['FreeShipping']
                ),
                "3" => array(
                    "ShippingServiceCost" => $domestic_cost3['ShippingServiceCost'],
                    "ShippingServiceAdditionalCost" => $domestic_additional3['ShippingServiceAdditionalCost'],
                    "ShippingService" => $domestic_service3['ShippingService'],
                    "ShippingFreeShipping" => $domestic_freeshipping3['FreeShipping']
                ),
                "4" => array(
                    "ShippingServiceCost" => $domestic_cost4['ShippingServiceCost'],
                    "ShippingServiceAdditionalCost" => $domestic_additional4['ShippingServiceAdditionalCost'],
                    "ShippingService" => $domestic_service4['ShippingService'],
                    "ShippingFreeShipping" => $domestic_freeshipping4['FreeShipping']
                )
            ),
            "international" => array(
                "1" => array(
                    "ShippingServiceCost" => $international_cost1['ShippingServiceCost'],
                    "ShippingServiceAdditionalCost" => $international_additional1['ShippingServiceAdditionalCost'],
                    "ShipToLocation" => $international_tolocation1['ShipToLocation'],
                    "ShippingService" => $international_service1['ShippingService']
                ),
                "2" => array(
                    "ShippingServiceCost" => $international_cost2['ShippingServiceCost'],
                    "ShippingServiceAdditionalCost" => $international_additional2['ShippingServiceAdditionalCost'],
                    "ShipToLocation" => $international_tolocation2['ShipToLocation'],
                    "ShippingService" => $international_service2['ShippingService']
                ),
                "3" => array(
                    "ShippingServiceCost" => $international_cost3['ShippingServiceCost'],
                    "ShippingServiceAdditionalCost" => $international_additional3['ShippingServiceAdditionalCost'],
                    "ShipToLocation" => $international_tolocation3['ShipToLocation'],
                    "ShippingService" => $international_service3['ShippingService']
                ),
                "4" => array(
                    "ShippingServiceCost" => $international_cost4['ShippingServiceCost'],
                    "ShippingServiceAdditionalCost" => $international_additional4['ShippingServiceAdditionalCost'],
                    "ShipToLocation" => $international_tolocation4['ShipToLocation'],
                    "ShippingService" => $international_service4['ShippingService']
                )
            )
        );

        if ($e_id != 0) {
            if ($shipping != null) {
                $this->db->set("shipping", serialize($shipping));
            } else {
                $this->db->set("shipping", 'NULL');
            }
            $this->db->where("e_id", $e_id);
            $this->db->update("ebay");
        }
    }

    function ListItemsForCategory($page = 1, $page_mode = false, $category_id = null, $Category_Owner = null, $storeCatId = null) {
        //echo '<p>ListItemsForCategory PAGE='.$page;
        //echo '<p>searched='.$page_mode;
        //echo '<p>storeCatId in ListItemsForCategory='.$storeCatId;
        //echo '<p>We have where '.$_POST['where'];
        $this->load->model('Myautopilot_model');
        $this->mysmarty->assign('prules', $this->Myautopilot_model->GetPredefined());

        $this->session->unset_userdata('submitredir');
        $this->mysmarty->assign('floatmenu', TRUE);
        $this->mysmarty->assign('hot', TRUE);
        $this->Auth_model->CheckListings();

        $this->load->model('Myseller_model');
        $this->statuses = $this->Myseller_model->assignstatuses();

        $this->session->unset_userdata('gotcats');
        //$this->db->select('title, content, date');
        //$this->db->distinct();
        //$this->db->get('table');
        //if ($page_mode==true)
        //{
        //    //$session_search = $this->session->userdata('last_string');
        //    //$session_where = $this->session->userdata('last_where');
        //    //$session_zero = $this->session->userdata('last_zero');
        //    //$session_ended = $this->session->userdata('last_ended');
        //    //$session_mm = $this->session->userdata('last_mm');
        //    //$session_bcnmm = $this->session->userdata('last_bcnmm');
        //    //$session_sitesell = $this->session->userdata('last_sitesell');
        //    $search = $this->session->userdata('last_string');
        //    $where = $this->session->userdata('last_where');
        //    $zero = $this->session->userdata('last_zero');
        //    $ended = $this->session->userdata('last_ended');
        //    $mm = $this->session->userdata('last_mm');
        //    $bcnmm = $this->session->userdata('last_bcnmm');
        //    $sitesell = $this->session->userdata('last_sitesell');
        //    $category_id = $this->session->userdata('Category_Id');
        //    $storeCatId = $this->session->userdata('StoreCatId');
        //    $where = $this->session->userdata('where', $where);
        //    $string = $this->session->userdata('string', $string);
        //    //echo '<p>Last session where '.$session_where;
        //}
        //else
        //{
        //    $session_search = $session_where = $session_zero = $session_ended = $session_mm = $session_sitesell = false;
        //}
        //if (isset($_POST['search'])) $string = htmlspecialchars(stripslashes($this->input->post('search', TRUE)));		
        //elseif ($session_search) $string = $this->session->userdata('last_string');
        //else $string = '';
        //if (isset($_POST['where']) && $_POST['where'] < 6) $where = (int)$this->input->post('where', TRUE);		
        //elseif ($session_where) $where = $this->session->userdata('last_where');
        //else $where = '';
        //if (isset($_POST['ended'])) $ended = 1;		
        //elseif ($session_ended) $ended = $this->session->userdata('last_ended');
        //else $ended = FALSE;
        //if (isset($_POST['zero'])) $zero = 1;		
        //elseif ($session_zero) $zero = $this->session->userdata('last_zero');
        //else $zero = FALSE;
        ////printcool ($_POST['zero']);
        //if (isset($_POST['mm'])) $mm = 1;		
        //elseif ($session_mm) $mm = $this->session->userdata('last_mm');
        //else $mm = FALSE;
        //if (isset($_POST['bcnmm'])) $bcnmm = 1;		
        //elseif ($session_bcnmm) $bcnmm = $this->session->userdata('last_bcnmm');
        //else $bcnmm = FALSE;
        //if (isset($_POST['sitesell'])) $sitesell = (int)$_POST['sitesell'];		
        //elseif ($session_sitesell) $sitesell = $this->session->userdata('last_sitesell');
        //else $sitesell = FALSE;
        //printcool ($string);

        $this->session->set_userdata('last_string', $string);


        $this->session->set_userdata('last_where', $where);
        $this->session->set_userdata('last_ended', $ended);
        $this->session->set_userdata('last_zero', $zero);
        $this->session->set_userdata('last_mm', $mm);
        $this->session->set_userdata('last_bcnmm', $bcnmm);
        $this->session->set_userdata('last_sitesell', $sitesell);


        $this->mysmarty->assign('where', $where);
        $this->mysmarty->assign('string', $string);
        $this->mysmarty->assign('ended', $ended);
        $this->mysmarty->assign('zero', $zero);
        $this->mysmarty->assign('mm', $mm);
        $this->mysmarty->assign('bcnmm', $bcnmm);
        $this->mysmarty->assign('sitesell', $sitesell);
        $this->mysmarty->assign('adm', $this->Myebay_model->GetAdminList());

        //$where=3;
        //echo '<p>e_id='.$this->e_id;
        //$string=$this->e_id;
        //Add functionality to search for all kinds of listings category ebay, google, amazon. 
        //    Example:
        //string should be listing e_id where should be 2 (for ebay primary category)
        //$category_id should be the number of ebay primary category
        //echo '<p> Cat Owner in ListItemsForCategory '.$Category_Owner;
        //echo '<p> Searched in ListItemsForCategory '.$page_mode;
        //echo '<p> test isset - '. !isset($page_mode);
        //if ($page_mode==false)
        //{
        switch ($Category_Owner) {
            case "eBayPrimery":
                $where = 6;
                break;
            case "eBaySecondary":
                $where = 7;
                break;
            case "Amazon":
                $where = 8;
                break;
            case "Google":
                $where = 9;
                break;
            default:

            //echo '<p>Deafult';
        }


        //    $this->session->set_userdata('where', $where);
        //    $this->session->set_userdata('string', $string);
        //}
        //else
        //{
        //    $category_id = $this->session->userdata('Category_Id');	
        //    $storeCatId = $this->session->userdata('StoreCatId');	
        //    $where = $this->session->userdata('where');
        //    $string = $this->session->userdata('string');
        //}
        //echo  '<p>last where='.$where;
        //echo  '<p>string '.$string.'<br> where '.$where. '<br> category_id '.$category_id. '<br> storeCatId '.$storeCatId;

        $data = $this->Myebay_model->ListItems2($string, $where, $ended, $zero, $mm, $bcnmm, $sitesell, $page, $category_id, $storeCatId);

        if ($string != '' || $where != '' || $ended != '' || $zero != '' || $mm != '' || $bcnmm != '' || $sitesell != '')
            $page_mode = TRUE;

        $this->mysmarty->assign('counted', $data['count']);
        $this->mysmarty->assign('list', $data['results']);
        $this->mysmarty->assign('pages', $data['pages']);
        $this->mysmarty->assign('page', (int) $page);

        //echo '<p>'.$this->db->last_query();		

        $this->load->helper('directory');
        $this->load->helper('file');
        $responseXml = read_file($this->config->config['ebaypath'] . '/cats.txt');
        $sxml = simplexml_load_string($responseXml);
        $sc = array();
        if (isset($sxml->Store->CustomCategories->CustomCategory)) {
            foreach ($sxml->Store->CustomCategories->CustomCategory as $s) {
                $a = (array) $s;
                $sc[$a['CategoryID']] = $a['Name'];
            }
        }
        asort($sc);

        //echo '<p>'.$this->db->last_query();

        $this->mysmarty->assign('store', $sc);
        if (!$page_mode) {
            $this->session->unset_userdata('last_string');
            $this->session->unset_userdata('last_where');
        } else {
            $this->mysmarty->assign('searched', TRUE);
            $where = $this->session->userdata('last_where');
        }
        //$this->mysmarty->assign('catlist', json_encode($treearray));
        //printcool($list);
        //$this->mysmarty->view('mysku/myebay_show_cats.html');
        //$this->mysmarty->view('mysku/myproducts_categories.html');
    }

    function logger($id = '') {
        if (isset($_POST['date']))
            $date = $this->input->post('date', TRUE);
        else {
            if ((int) $id == 0)
                $date = date('m/d/Y');
            else
                $date = false;
        }

        if (isset($_POST['admin']))
            $admin = $this->input->post('admin', TRUE);
        else
            $admin = false;

        if ((int) $id == 0)
            $list = $this->Mywarehouse_model->GetLog($date, $admin);
        else
            $list = $this->Mywarehouse_model->GetBCNLog((int) $id, $date, $admin);

        $fielset = array(
            'headers' => "'BCN', 'At','What', 'From', 'To', 'Admin', 'Time'",
            /* 'rowheaders' => $list['headers'], */
            'width' => "120, 150, 80, 125, 200, 200, 150",
            'startcols' => 8,
            'startrows' => 10,
            'colmap' => '{readOnly: true, renderer: "html"},{readOnly: true,renderer: "html"},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true},{readOnly: true}');


        $adms = $this->Mywarehouse_model->GetAdminList();

        if ($list) {
            $loaddata = '';
            //['ctrl']
            foreach ($list as $k => $l) {
                $loaddata .= "['<a target=\"_blank\" href=\"/Mywarehouse/bcndetails/" . cstr($l['wid']) . "\" target=\"_blank\">" . cstr($l['bcn']) . "</a>',  '<a href=\"" . $l['url'] . "\" target=\"_blank\">" . cstr(str_replace('/Mywarehouse/', '', $l['url'])) . "</a>', '" . cstr($l['field']) . "', '" . cstr($l['datafrom']) . "', '" . cstr($l['datato']) . "', '" . cstr($adms[$l['admin']]) . "', '" . cstr($l['time']) . "'],
				";
            }
        }

        $this->mysmarty->assign('headers', $fielset['headers']);
        $this->mysmarty->assign('rowheaders', $fielset['rowheaders']);
        $this->mysmarty->assign('width', $fielset['width']);
        $this->mysmarty->assign('startcols', $fielset['startcols']);
        $this->mysmarty->assign('startrows', $fielset['startrows']);
        $this->mysmarty->assign('colmap', $fielset['colmap']);
        $this->mysmarty->assign('loaddata', rtrim($loaddata, ','));
        $this->mysmarty->assign('id', (int) $id);

        $this->mysmarty->assign('admins', $adms);
        $this->mysmarty->assign('admin', $admin);
        $this->mysmarty->assign('date', $date);
        $this->mysmarty->assign('cal', TRUE);
        $this->mysmarty->assign('logger', TRUE);
        $this->mysmarty->view('mywarehouse/logger.html');
    }

    function order($id = 0) {
        $noenter = '
<script type="text/javascript"> 

function stopRKey(evt) { 
  var evt = (evt) ? evt : ((event) ? event : null); 
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null); 
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;} 
} 

document.onkeypress = stopRKey; 
</script> 
';

        $this->load->model('Myseller_model');
        $this->statuses = $this->Myseller_model->assignstatuses();
        $this->mysmarty->assign('nowarehouseorders', TRUE);

        if ((int) $id > 0) {
            //$wids = $this->Mywarehouse_model->GetOrderWIDs((int)$id);	
            //$this->mysmarty->assign('wids', $wids);
            //$this->mysmarty->assign('cwids', count($wids));
            $this->mysmarty->assign('go', TRUE);
            $noenter = '';
            $dbo = $this->Mywarehouse_model->GetOrder((int) $id);

            $idarray[] = $dbo['woid'];
            $this->load->model('Myseller_model');
            $this->Myseller_model->getSales($idarray, 4);

            $this->mysmarty->assign('orderid', (int) $id);
        }
        $o['buyer'] = $this->input->post('buyer', TRUE);
        $o['otype'] = (int) $this->input->post('otype', TRUE);
        $o['paid'] = $this->input->post('paid', TRUE);
        $o['shipped'] = $this->input->post('shipped', TRUE);
        $o['wholeprice'] = (int) $this->input->post('wholeprice', TRUE);
        $o['notes'] = $this->input->post('notes', TRUE);

        if ($o['paid'] == '')
            $o['paid'] = 0;
        else
            $o['paid'] = 1;
        if ($o['shipped'] == '')
            $o['shipped'] = 0;
        else
            $o['shipped'] = 1;

        if ((int) $id > 0 && !$_POST)
            $o = $dbo;
        $this->mysmarty->assign('order', $o);


        $this->load->library('form_validation');
        $this->form_validation->set_rules('buyer', 'Buyer', 'trim|required|xss_clean');
        $this->form_validation->set_rules('otype', 'Order Type', 'trim|required|xss_clean');


        if ($this->form_validation->run() == FALSE) {
            $this->mysmarty->assign('errors', $this->form_validation->_error_array);
        } else {
            $o['time'] = CurrentTime();
            $o['timemk'] = mktime();
            if ((int) $id == 0) {
                $this->db->insert('warehouse_orders', $o);
                $id = $this->db->insert_id();
            } else
                $this->db->update('warehouse_orders', $o, array('woid' => (int) $id));
            $this->session->set_flashdata('success_msg', 'Complete');
            Redirect('Mywarehouse/order/' . (int) $id);
        }

        $this->mysmarty->assign('date', CurrentTime());
        //$this->mysmarty->assign('hot', true);
        $this->mysmarty->assign('noenter', $noenter);

        $this->mysmarty->view('mywarehouse/order.html');
    }

    function DoAudit() {
        if ((int) $this->input->post('id') == 0)
            echo 'ERROR';
        $audit = CurrentTime();
        $this->db->update('warehouse', array('audit' => $audit), array('wid' => (int) $this->input->post('id')));
        echo($audit);
    }

    function partingsearchforlistings($id = '') {
        if ($id != '') {
            if (isset($_POST['fieldvalue'])) {
                $this->mysmarty->assign('search', trim($_POST['fieldvalue']));
                $this->mysmarty->assign('res', $this->Mywarehouse_model->SearchListings(trim($_POST['fieldvalue'])));
            }
            $this->mysmarty->assign('id', trim($id));
            echo $this->mysmarty->fetch('mywarehouse/parting_search.html');
        } else
            echo 'Error';
    }

    //Called from JS function CreateNewStoreCategory() in mysku\myproducts_categories.html
    function CreateCategory($CategoryName, $newId) {

        $cat_dad_id = $this->input->post("cat_dad");
        $MainParent = $this->input->post("mainparent");

        //CHECK MAIN PARENT CHANEL
        $this->db->where("wsc_id", $MainParent);
        $isebay = $this->db->get("warehouse_sku_categories")->result_object();


        if ($isebay[0]->wsc_title != "EBAY") {

            $this->db->where("wsc_id", $cat_dad_id);
            $cat_data = $this->db->get("warehouse_sku_categories")->result_object();
            $cat_dad = $cat_data[0]->wsc_id;
            if (isset($CategoryName)) {


                $this->db->order_by("id_store", "ASC");
                $id_store_data = $this->db->get("categories_store")->result_object();
                $id_store = $id_store_data[0]->id_store + 1;


                $data = array(
                    'id_store' => $id_store,
                    'store_cat_title' => $CategoryName,
                    "dad_cat" => $cat_dad,
                    'mainparent' => $MainParent
                );



                $this->db->insert('categories_store', $data);


                $data2 = array(
                    'wsc_title' => $CategoryName,
                    "wsc_parent" => $cat_dad,
                    'wsc_mainparent' => $MainParent
                );

                $this->db->insert('warehouse_sku_categories', $data2);




                echo "created";
                // echo $this->db->last_query();
                // If you uncomment the function below comment the other code in this function
                //  $this->CreateStoreCategoryInEbay($CategoryName);
            } else {
                echo "<p><font color='red'>Error! No Category Name or Id provided!";
                exit;
            }
        } else {
            $this->db->where("wsc_id", $cat_dad_id);
            $ebaylv = $this->db->get("warehouse_sku_categories")->result_object();
            if ($ebaylv[0]->wsc_title == "EBAY") {
                $data5 = array(
                    'wsc_title' => $CategoryName,
                    "wsc_parent" => $cat_dad_id,
                    'wsc_mainparent' => $MainParent
                );

                $this->db->insert('warehouse_sku_categories', $data5);
                echo "created";
            } else {
                //CHECK FIRST LV
                $this->db->where("wsc_parent", $MainParent);
                $this->db->where("wsc_id", $cat_dad_id);
                $checklv1 = $this->db->get("warehouse_sku_categories")->num_rows();

                if ($checklv1 == 1) {
                    $data3 = array(
                        'wsc_title' => $CategoryName,
                        "wsc_parent" => $cat_dad_id,
                        'wsc_mainparent' => $MainParent
                    );

                    $this->db->insert('warehouse_sku_categories', $data3);
                    echo "created";
                } else {
                    //CHECK SECOND LV
                    $this->db->where("wsc_id", $cat_dad_id);
                    $checklv2 = $this->db->get("warehouse_sku_categories")->result_object();
                    if ($checklv2 != null) {
                        foreach ($checklv2 as $checklv2) {
                            if ($checklv2->wsc_parent != 0) {
                                $this->db->where("wsc_id", $checklv2->wsc_parent);
                                $checklv3 = $this->db->get("warehouse_sku_categories")->result_object();

                                if ($checklv3 != null) {
                                    foreach ($checklv3 as $checklv3) {
                                        $this->db->where("wsc_id", $checklv3->wsc_parent);
                                        $checklv4 = $this->db->get("warehouse_sku_categories")->result_object();

                                        if ($checklv4 != null) {
                                            foreach ($checklv4 as $checklv4) {
                                                $this->db->where("wsc_id", $checklv4->wsc_parent);
                                                $checklv5 = $this->db->get("warehouse_sku_categories")->result_object();
                                                if ($checklv5 != null) {
                                                    echo "YOU CANNOT HAVE MORE THEN 3 LV DEEP INTO EBAY CATEGORY";
                                                } else {
                                                    //THIRD LV
                                                    $data6 = array(
                                                        'wsc_title' => $CategoryName,
                                                        "wsc_parent" => $cat_dad_id,
                                                        'wsc_mainparent' => $MainParent,
                                                        "level" => 3
                                                    );

                                                    $this->db->insert('warehouse_sku_categories', $data6);
                                                    echo "created";
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    //THIRD LV
                                    $data4 = array(
                                        'wsc_title' => $CategoryName,
                                        "wsc_parent" => $cat_dad_id,
                                        'wsc_mainparent' => $MainParent
                                    );

                                    $this->db->insert('warehouse_sku_categories', $data4);
                                    echo "created";
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    function partingrunbcn($id = '', $listing, $wid = '') {

        if ($id != '' && (int) $wid > 0) {
            $lastbcn = $this->Mywarehouse_model->PartingBCN((int) $wid);
            //$this->db->select("wid, bcn, bcn_p1, bcn_p2, bcn_p3");	
            $newbcn['waid'] = $lastbcn['waid'];
            $newbcn['aucid'] = $lastbcn['aucid'];
            $newbcn['bcn_p1'] = $lastbcn['bcn_p1'];
            $newbcn['bcn_p2'] = $lastbcn['bcn_p2'];
            $newbcn['bcn_p3'] = (int) $lastbcn['bcn_p3'];
            $newbcn['bcn_p3'] ++;
            $newbcn['bcn'] = $newbcn['bcn_p1'] . '-' . $newbcn['bcn_p2'] . '-' . $newbcn['bcn_p3'];
            $newbcn['listingid'] = $listing;
            $newbcn['psku'] = $id;
            $newbcn['status'] = 'Not Tested';
            $newbcn['adminid'] = (int) $this->session->userdata['admin_id'];
            $newbcn['dates'] = serialize(array(array('created' => CurrentTime(), 'createdstamp' => mktime())));

            $this->db->insert('warehouse', $newbcn);
            //$this->db->insert('warehouse_sku_listing_bcn', array('sku' => $id, 'listing' => $listing, 'wid' => $this->db->insert_id(), 'datetime' => CurrentTime(), 'admin'=> (int)$this->session->userdata['admin_id']));

            $this->mysmarty->assign('id', (int) $id);
            $this->mysmarty->assign('eid', (int) $listing);
            $res = $this->Mywarehouse_model->getbcnsforskulisting($id, $listing);
            $this->mysmarty->assign('res', $res);

            echo json_encode(array('html' => $this->mysmarty->fetch('mywarehouse/parting_run_bcns.html'), 'allbcnt' => $this->Mywarehouse_model->getlistingskucount((int) $id), 'bcncnt' => json_encode(count($res))));

            /*
              Serverside

              $html = '<div>This is Html</div>';
              $data = json_encode(array('page_title'=>'My Page'));
              $response = array('html'=>$html, 'data'=>$data);
              echo json_encode($response);
              Clientside

              //Ajax success function...

              success: function(serverResponse){
              $("body > .container").html(serverResponse.html);
              var data = JSON.parse(serverResponse.data);
              $("title").html(data.page_title)
              }
             */
        }
    }

    function partingsremovebcn($id = '', $listing, $wid = '') {
        $this->db->update('warehouse', array('listingid' => 0, 'psku' => 0, 'unparted_admin' => (int) $this->session->userdata['admin_id']), array('wid' => (int) $wid));
        $this->mysmarty->assign('id', (int) $id);
        $this->mysmarty->assign('eid', (int) $listing);
        $res = $this->Mywarehouse_model->getbcnsforskulisting($id, $listing);
        $this->mysmarty->assign('res', $res);
        echo json_encode(array('html' => $this->mysmarty->fetch('mywarehouse/parting_run_bcns.html'), 'allbcnt' => $this->Mywarehouse_model->getlistingskucount((int) $id), 'bcncnt' => json_encode(count($res))));
    }

    function partingsearchforbcn($id = '', $listing) {
        if ($id != '') {
            if (isset($_POST['fieldvalue'])) {
                $this->mysmarty->assign('search', trim($_POST['fieldvalue']));
                $this->mysmarty->assign('res', $this->Mywarehouse_model->SearchBCN(trim($_POST['fieldvalue'])));
            }
            $this->mysmarty->assign('eid', (int) $listing);
            $this->mysmarty->assign('id', trim($id));
            echo $this->mysmarty->fetch('mywarehouse/parting_search_bcns.html');
        } else
            echo 'Error';
    }

    function parting($id = '') {
        if ((int) $id > 0) {
            $data = $this->Mywarehouse_model->GetBCNDetails((int) $id);
            $data['bcn']['sku'] = $this->Mywarehouse_model->getsku((int) $data['bcn']['sku']);
            $l = $data['bcn'];
            $this->mysmarty->assign('data', $data);

            $bulk = $this->Mywarehouse_model->GetSkusAndListingsAndBCNs((int) $id);
            $this->mysmarty->assign('bulk', $bulk);

            $numparts = $this->input->post('numparts');
            if ($numparts && (int) $numparts > 0) {
                $this->mysmarty->assign('numparts', (int) $numparts);
                $start = $this->Mywarehouse_model->getnextsku();
                $numparts = $numparts + $start;

                $subbin = false;
                while ($start < (int) $numparts) {
                    $start++;

                    $this->db->insert('warehouse_sku', array('name' => 'SK' . $start, 'is_p' => 1, 'parent' => (int) $data['bcn']['sku'], 'seq' => $start, 'wid' => (int) $id, 'datetime' => CurrentTime(), 'admin' => (int) $this->session->userdata['admin_id']));
                    $subbin[$this->db->insert_id()] = array('bcn' => $data['bcn']['bcn'] . '-' . $start, 'name' => 'SK' . $start);
                }

                $this->mysmarty->assign('subbin', $subbin);
            }

            $this->mysmarty->view('mywarehouse/parting.html');
        }
    }

    function sku() {
        $this->mysmarty->assign('sku', $this->Mywarehouse_model->GetSKUS());
        $this->mysmarty->assign('admins', $this->Mywarehouse_model->GetAdminList());
        $this->mysmarty->view('mywarehouse/sku.html');
    }

    function insertskulisting() {
        if (isset($_POST['sku']) && (int) $_POST['sku'] > 0 && isset($_POST['listing']) && (int) $_POST['listing'] > 0) {
            $this->db->insert('warehouse_sku_listing', array('wsid' => (int) $this->input->post('sku', true), 'listing' => (int) $this->input->post('listing', true), 'datetime' => CurrentTime(), 'admin' => (int) $this->session->userdata['admin_id']));
            $listingdata = $this->Mywarehouse_model->GetListingTitleAndCondition((int) $this->input->post('listing', true));
            if ($listingdata) {
                $condition = 'Undefined (' . $listingdata['Condition'] . ')';
                if ($listingdata['Condition'] == '1000')
                    $condition = 'New';
                if ($listingdata['Condition'] == '1500')
                    $condition = 'New other (see details)';
                if ($listingdata['Condition'] == '1750')
                    $condition = 'New with defects';
                if ($listingdata['Condition'] == '2000')
                    $condition = 'Manufacturer refurbished';
                if ($listingdata['Condition'] == '2500')
                    $condition = 'Seller refurbished';
                if ($listingdata['Condition'] == '3000')
                    $condition = 'Used';
                if ($listingdata['Condition'] == '4000')
                    $condition = 'Very Good';
                if ($listingdata['Condition'] == '5000')
                    $condition = 'Good';
                if ($listingdata['Condition'] == '6000')
                    $condition = 'Acceptable';
                if ($listingdata['Condition'] == '7000')
                    $condition = 'For parts or not working';

                $listingstring = '<br><strong>' . substr($listingdata['e_title'], 0, 120) . '</strong><br>Condition: ' . $condition;

                $res = $this->Mywarehouse_model->getbcnsforskulisting((int) $_POST['sku'], (int) $_POST['listing']);
                $this->mysmarty->assign('res', $res);
                $this->mysmarty->assign('eid', (int) $_POST['listing']);
                $this->mysmarty->assign('id', (int) $this->input->post('sku', true));

                $imgexists = $this->Mywarehouse_model->GetSKUImage((int) $this->input->post('sku', true));
                if (!$imgexists && isset($_POST['imgurl']) && trim($_POST['imgurl']) != '') {
                    $this->db->update('warehouse_sku', array('img' => trim($this->input->post('imgurl', true))), array('wsid' => (int) $this->input->post('sku', true)));
                    $returnimg = trim($this->input->post('imgurl', true));
                } else
                    $returnimg = $imgexists;
                echo json_encode(array('html' => $this->mysmarty->fetch('mywarehouse/parting_run_bcns.html'), 'bcncnt' => count($res), 'allbcnt' => $this->Mywarehouse_model->getlistingskucount((int) $this->input->post('sku', true)), 'img' => $returnimg, 'listingstring' => $listingstring));
            } else
                echo '';
        } else
            echo '';
    }

    /* function testdecode()
      {
      if ($_POST){ echo json_encode(array('html'=> '<html></html>', 'bcncnt'=> 2, 'img' => '/ebay/imgpath.jpg', 'listingstring' => '<br>something to string')); exit();}
      $this->mysmarty->view('mywarehouse/jsontest.html');
      } */

    function test() {
        exit();
        $id = 71;
        $this->db->select('listing');
        $this->db->where('wsid', (int) $id);
        $q1 = $this->db->get('warehouse_sku_listing');
        if ($q1->num_rows() > 0) {
            $this->db->select("wid");
            $start = 1;
            foreach ($q1->result_array() as $l) {
                if ($start == 1)
                    $this->db->where('listingid', (int) $l['listing']);
                else
                    $this->db->or_where('listingid', (int) $l['listing']);
                $start++;
            }
        } else
            return 0;
        $q = $this->db->get('warehouse');
        if ($q->num_rows() > 0)
            echo($q->num_rows());
        else
            echo 0;
    }

    function removeskulisting() {
        if (isset($_POST['sku']) && (int) $_POST['sku'] > 0 && isset($_POST['listing']) && (int) $_POST['listing'] > 0) {
            $this->db->where('wsid', (int) $this->input->post('sku', true));
            $this->db->where('listing', (int) $this->input->post('listing', true));
            $this->db->delete('warehouse_sku_listing');

            $data = $this->Mywarehouse_model->getlistingandskucount((int) $this->input->post('sku', true));
            echo json_encode(array('listings' => $data['listings'], 'allbcnt' => $data['bcn']));

            //echo 'ok';
        } else
            echo '';
    }

    function insertskulistingbcn() {
        if (isset($_POST['wid']) && (int) $_POST['wid'] > 0 && isset($_POST['sku']) && $_POST['sku'] != '' && isset($_POST['listing']) && (int) $_POST['listing'] > 0) {
            $this->db->insert('warehouse_sku_listing_bcn', array('wid' => (int) $_POST['wid'], 'sku' => trim($this->input->post('sku', true)), 'listing' => (int) $_POST['listing'], 'datetime' => CurrentTime(), 'admin' => (int) $this->session->userdata['admin_id']));

            echo 'ok';
        }
    }

    function removeskulistingbcn() {
        if (isset($_POST['wid']) && (int) $_POST['wid'] > 0 && isset($_POST['sku']) && $_POST['sku'] != '' && isset($_POST['listing']) && (int) $_POST['listing'] > 0) {
            $this->db->where('wid', (int) $_POST['wid']);
            $this->db->where('sku', trim($this->input->post('sku', true)));
            $this->db->where('listing', (int) $_POST['listing']);
            $this->db->delete('warehouse_sku_listing_bcn');

            echo 'ok';
        }
    }

    function reattach($wid, $listing, $soldid, $soldsubid = 0, $go = false) {
        $owid = $wid;
        $wid = $this->Mywarehouse_model->bcn2wid(trim($wid));
        Redirect('Mywarehouse/bcndetails/' . $wid);
        $widdata = $this->Mywarehouse_model->getbcnattachdata((int) $wid);
        if (!$go) {
            printcool($widdata['listingid']);
            printcool($widdata['sold_id']);
            printcool($widdata['sold_subid']);
            printcool('<a href="/Mywarehouse/reattach/' . $owid . '/' . $listing . '/' . $soldid . '/' . $soldsubid . '/1">GO</a>');
            printcool($widdata);
        }
        if ($go) {


            if ((int) $soldsubid == 0)
                $channel = 1;
            else
                $channel = 2;
            $this->db->update('warehouse', array('listingid' => (int) $listing, 'sold_id' => (int) $soldid, 'sold_subid' => $soldsubid, 'channel' => $channel, 'vended' => 1), array('wid' => (int) $wid));
            Redirect('Mywarehouse/bcndetails/' . $wid);
        }
    }

    function bcndetails($id = '', $savetype = false) {
        if ((int) $id > 0) {
            $data = $this->Mywarehouse_model->GetBCNDetails((int) $id);

            $this->mysmarty->assign('auction', $this->Mywarehouse_model->GetAuction((int) $data['bcn']['waid']));
            $l = $data['bcn'];
            $this->mysmarty->assign('data', $data);
            $this->mysmarty->assign('hot', TRUE);
            $this->mysmarty->assign('updated', CurrentTime());

            $adms = $this->Mywarehouse_model->GetAdminList();
            $this->mysmarty->assign('admins', $adms);

            if ($savetype && $savetype == 'testing') {
                if (isset($_POST) && $_POST) {
                    $tcolMap = array(
                        0 => 'oldbcn',
                        1 => 'title',
                        2 => 'location',
                        3 => 'sn',
                        4 => 'post',
                        5 => 'battery',
                        6 => 'charger',
                        7 => 'hddstatus',
                        8 => 'problems',
                        9 => 'notes',
                        10 => 'status',
                        11 => 'status_notes',
                        12 => 'partsneeded',
                        13 => 'warranty'
                    );

                    $btcolMap = array(
                        0 => 'Old BCN',
                        1 => 'Title',
                        2 => 'Location',
                        3 => 'SN',
                        4 => 'POST',
                        5 => 'Battery',
                        6 => 'Charger',
                        7 => 'HDD Status',
                        8 => 'Problems',
                        9 => 'Notes',
                        10 => 'Status',
                        11 => 'Status Notes',
                        12 => 'Parts Needed',
                        13 => 'Warranty'
                    );

                    $out = '';
                    $sout = '';
                    foreach ($_POST as $d) {
                        foreach ($d as $dd) {

                            if ($dd[2] != $dd[3]) {
                                $this->Auth_model->wlog($l['bcn'], (int) $id, $tcolMap[$dd[1]], $dd[2], $dd[3]);
                                $out .= ' "' . $btcolMap[$dd[1]] . '" for BCN ' . $l['bcn'] . ' Changed from "' . $dd[2] . '" to "' . $dd[3] . '" ';
                                $sout .= $l['bcn'] . '/"' . $btcolMap[$dd[1]] . '" Changed ';

                                $updt = array($tcolMap[$dd[1]] => $dd[3], 'tech' => (int) $this->session->userdata['admin_id'], 'techlastupdate' => CurrentTime());
                                if ($tcolMap[$dd[1]] == 'status')
                                    $updt['status_notes'] = $this->Mywarehouse_model->GetStatusNotes((int) $id) . ' | Changed from: ' . $dd[2];

                                $this->db->update('warehouse', $updt, array('wid' => (int) $id));


                                $out = array('msg' => $out, 'smsg' => $sout, 'row' => $dd[0], 'col' => $dd['1']);
                            }
                        }
                    }
                    echo json_encode($out);
                    exit();
                }
            }

            $tloaddata .= "['" . cstr($l['oldbcn']) . "', '" . cstr($l['title']) . "', '" . cstr($l['location']) . "', '" . cstr($l['sn']) . "', '" . cstr($l['post']) . "', '" . cstr($l['battery']) . "', '" . cstr($l['charger']) . "', '" . cstr($l['hddstatus']) . "', '" . cstr($l['problems']) . "', '" . cstr($l['notes']) . "',  '" . cstr($l['status']) . "', '" . cstr($l['status_notes']) . "', '" . cstr($l['partsneeded']) . "','" . cstr($l['warranty']) . "', '" . cstr($l['techlastupdate']) . "', '" . $adms[$l['tech']] . "'],
				";


            $tfielset = array('testing' => array(
                    'headers' => "'Old BCN',  'Title','Location', 'SN', 'POST', 'Battery', 'Charger' , 'HDD Status', 'Problems', 'Notes', 'Status', 'Status notes', 'Parts Needed', 'Warranty',  'LastUpdt', 'Tech'",
                    /* 'rowheaders' => $list['headers'], */
                    'width' => "100, 200, 120, 125, 50, 50, 50, 150, 200, 100, 125, 125, 125, 125, 125, 125",
                    'startcols' => 16,
                    'startrows' => 1,
                    'autosaveurl' => '/Mywarehouse/bcndetails/' . (int) $id . '/testing',
                    'colmap' => '{},{},{},{},{type: "checkbox", checkedTemplate: 1, uncheckedTemplate: 0},{type: "checkbox", checkedTemplate: 1, uncheckedTemplate: 0},{type: "checkbox", checkedTemplate: 1, uncheckedTemplate: 0},{},{},{},{type: "dropdown", source: [' . $this->statuses['testingstring'] . ']},{},{},{},{readOnly: true},{readOnly: true}')
            );

            $this->mysmarty->assign('theaders', $tfielset['testing']['headers']);
            $this->mysmarty->assign('trowheaders', $tfielset['testing']['rowheaders']);
            $this->mysmarty->assign('twidth', $tfielset['testing']['width']);
            $this->mysmarty->assign('tstartcols', $tfielset['testing']['startcols']);
            $this->mysmarty->assign('tstartrows', $tfielset['testing']['startrows']);
            $this->mysmarty->assign('tautosaveurl', $tfielset['testing']['autosaveurl']);
            $this->mysmarty->assign('tcolmap', $tfielset['testing']['colmap']);
            $this->mysmarty->assign('tloaddata', rtrim($tloaddata, ','));

            if ($this->session->userdata['type'] == 'master' || ($this->session->userdata['type'] != 'master' && $this->session->userdata['accounting'] == 1)) {
                $aloaddata .= "['" . cstr($l['listed']) . "', '" . cstr($l['listed_date']) . "', '" . cstr($l['sold_date']) . "', '" . cstr($l['sold']) . "', '" . cstr($l['paid']) . "', '" . cstr($l['shipped']) . "','" . cstr($l['shipped_actual']) . "', '" . cstr($l['ordernotes']) . "', '" . cstr($l['sellingfee']) . "', '" . cstr($l['netprofit']) . "', '" . cstr($l['cost']) . "', '" . cstr($l['status']) . "', '" . cstr($l['aupdt']) . "'],
				";
            } else {
                $aloaddata .= "['" . cstr($l['listed']) . "', '" . cstr($l['listed_date']) . "', '" . cstr($l['sold_date']) . "', '" . cstr($l['sold']) . "', '" . cstr($l['paid']) . "',  '" . cstr($l['shipped']) . "', '" . cstr($l['ordernotes']) . "', '" . cstr($l['status']) . "','" . cstr($l['aupdt']) . "'],
				";
            }
            if ($savetype && $savetype == 'accounting') {
                if (isset($_POST) && $_POST) {
                    $acolMap = array(
                        0 => 'listed',
                        1 => 'listed_date',
                        2 => 'sold_date',
                        3 => 'sold',
                        4 => 'paid',
                        5 => 'shipped',
                        6 => 'shipped_actual',
                        7 => 'ordernotes',
                        8 => 'sellingfee',
                        9 => 'netprofit',
                        10 => 'cost',
                        11 => 'status'
                    );
                    $bacolMap = array(
                        0 => 'Where Listed',
                        1 => 'Date Listed',
                        2 => 'Date Sold',
                        3 => 'Where Sold',
                        4 => 'Price Sold',
                        5 => 'Shipping Cost',
                        6 => 'Actual Sh. Cost', //
                        7 => 'Order Notes',
                        8 => 'Selling Fee', //	
                        9 => 'Net Profit', //
                        10 => 'Cost', //				 
                        11 => 'Status'
                    );

                    if ($this->session->userdata['type'] != 'master' && $this->session->userdata['accounting'] == 0) {

                        $acolMap[8] = 'status';
                        unset($acolMap[9]);
                        unset($acolMap[10]);
                        $bacolMap[8] = 'Status';
                        unset($bacolMap[9]);
                        unset($bacolMap[10]);
                    }


                    $out = '';
                    $sout = '';
                    foreach ($_POST as $d) {
                        foreach ($d as $dd) {
                            if ($dd[2] != $dd[3]) {

                                $this->Auth_model->wlog($l['bcn'], (int) $id, $acolMap[$dd[1]], $dd[2], $dd[3]);
                                $out .= ' "' . $bacolMap[$dd[1]] . '" for BCN ' . $l['bcn'] . ' Changed from "' . $dd[2] . '" to "' . $dd[3] . '" ';
                                $sout .= $l['bcn'] . '/"' . $bacolMap[$dd[1]] . '" Changed ';

                                $updt = array($acolMap[$dd[1]] => $dd[3], 'aupdt' => CurrentTime());
                                if ($acolMap[$dd[1]] == 'status')
                                    $updt['status_notes'] = $this->Mywarehouse_model->GetStatusNotes((int) $id) . ' | Changed from: ' . $dd[2];
                                $this->db->update('warehouse', $updt, array('wid' => (int) $id));

                                $out = array('msg' => $out, 'smsg' => $sout, 'row' => $dd[0], 'col' => $dd['1']);
                            }
                        }
                    }
                    echo json_encode($out);
                    exit();
                }
            }
            if ($this->session->userdata['type'] == 'master' || ($this->session->userdata['type'] != 'master' && $this->session->userdata['accounting'] == 1)) {
                $afielset = array('accounting' => array(
                        'headers' => "'Where Listed', 'Date Listed', 'Date Sold', 'Where Sold', 'Price Sold', 'Shipping Cost', 'Actual Sh. Cost', 'Order Notes', 'Selling Fee', 'Net Profit', 'Cost', 'Status', 'Last Upd'",
                        /* 'rowheaders' => $list['headers'], */
                        'width' => "125, 125, 125, 125, 125, 125, 125,125, 125, 125, 125, 125, 125",
                        'startcols' => 13,
                        'startrows' => 1,
                        'autosaveurl' => '/Mywarehouse/bcndetails/' . (int) $id . '/accounting',
                        'colmap' => '{},{},{},{},{},{},{},{},{},{readOnly: true},{},{type: "dropdown", source: [' . $this->statuses['accountingstring'] . ']},{readOnly: true}')
                );
            } else {
                $afielset = array('accounting' => array(
                        'headers' => " 'Where Listed', 'Date Listed', 'Date Sold', 'Where Sold', 'Price Sold', 'Shipping Cost', 'Actual Sh. Cost','Order Notes', 'Status', 'Last Upd'",
                        /* 'rowheaders' => $list['headers'], */
                        'width' => "125, 125, 125, 125, 125, 125, 125, 125, 125, 125",
                        'startcols' => 10,
                        'startrows' => 1,
                        'autosaveurl' => '/Mywarehouse/bcndetails/' . (int) $id . '/accounting',
                        'colmap' => '{},{},{},{},{},{},{},{},{type: "dropdown", source: [' . $this->statuses['accountingstring'] . ']},{readOnly: true}')
                );
            }

            $this->mysmarty->assign('aheaders', $afielset['accounting']['headers']);
            $this->mysmarty->assign('arowheaders', $afielset['accounting']['rowheaders']);
            $this->mysmarty->assign('awidth', $afielset['accounting']['width']);
            $this->mysmarty->assign('astartcols', $afielset['accounting']['startcols']);
            $this->mysmarty->assign('astartrows', $afielset['accounting']['startrows']);
            $this->mysmarty->assign('aautosaveurl', $afielset['accounting']['autosaveurl']);
            $this->mysmarty->assign('acolmap', $afielset['accounting']['colmap']);
            $this->mysmarty->assign('aloaddata', rtrim($aloaddata, ','));

            $tcolMap = array(
                0 => 'bcn',
                1 => 'title',
                2 => 'sn',
                3 => 'post',
                4 => 'battery',
                5 => 'charger',
                6 => 'hddstatus',
                7 => 'problems',
                8 => 'notes',
                9 => 'status',
                10 => 'status_notes',
                11 => 'partsneeded',
                12 => 'warranty'
            );
            $acolMap = array(
                0 => 'listed',
                1 => 'listed_date',
                2 => 'sold_date',
                3 => 'sold',
                4 => 'paid',
                5 => 'shipped',
                6 => 'shipped_actual',
                7 => 'ordernotes',
                8 => 'sellingfee',
                9 => 'netprofit',
                10 => 'cost',
                11 => 'status'
            );

            /* if ($this->session->userdata['type'] != 'master' && $this->session->userdata['accounting'] == 0)
              {
              $acolMap[9] = 'cost';
              $acolMap[10] = 'sold';
              $acolMap[11] = 'status';
              unset($acolMap[12]);
              }
             */
            $history['accounting'] = false;
            $history['testing'] = false;
            $history['other'] = false;

            if ($data['logs'])
                foreach ($data['logs'] as $l) {
                    if (in_array($l['field'], $acolMap)) {
                        if ($this->session->userdata['type'] != 'master' && $this->session->userdata['accounting'] == 0) {
                            if ($l['field'] != 'sellingfee' && $l['field'] != 'shipped' && $l['field'] != 'netprofit' && $l['field'] != 'cost')
                                $history['accounting'][] = $l;
                        } else
                            $history['accounting'][] = $l;
                    } elseif (in_array($l['field'], $tcolMap))
                        $history['testing'][] = $l;
                    else
                        $history['other'][] = $l;
                }
            $this->mysmarty->assign('history', $history);
        }
        $this->mysmarty->view('mywarehouse/details.html');
    }

    function delproc() {
        exit();
        $this->db->where('waid', 183);
        $this->db->delete('warehouse');
        $this->db->where('waid', 183);
        $this->db->delete('s');
    }

    function DeleteAuction($waid = 0) {
        if ((int) $this->session->userdata['admin_id'] == 1 || (int) $this->session->userdata['admin_id'] == 2) {
            $this->Auth_model->wlog($this->Mywarehouse_model->wid2bcn($waid), 0, 'Auction', '', 'DELETED');
            $this->db->update('s', array('deleted' => 1), array('waid' => (int) $waid));
            $this->db->update('warehouse', array('deleted' => 1), array('waid' => (int) $waid));
            Redirect('Mywarehouse');
        } else
            exit('You do not have privileges for this action');
    }

    function saveeditor($complete = false) {
        if ($complete) {
            $this->mysmarty->assign('confirm', TRUE);
            $sessback = $this->_loadsession($this->session->userdata('formfile'));
            $this->mysmarty->assign('data', $sessback['formdata']);
            $this->mysmarty->view('mywarehouse/BulkStockRecieve_preview.html');
        } else {
            $colMap = array(
                0 => 'qty',
                1 => 'cost',
                2 => 'mfgname',
                3 => 'mfgpart',
                4 => 'title',
                5 => 'lot'
            );

            if (isset($_POST['data']) && $_POST['data']) {
                $this->load->helper('security');
                //$cnt = 0;
                foreach ($_POST['data'] as $k => $v) {
                    //$cnt++; 
                    //if ($cnt <= 330)
                    //{
                    foreach ($v as $kk => $vv) {
                        if (isset($colMap[$kk]))
                            $data[$k][$colMap[$kk]] = addslashes(xss_clean($vv));
                    }
                    //}
                }
                $formfile = $this->_savesession(array('formdata' => $data));
                $this->session->set_userdata(array('formfile' => $formfile));
                //$this->session->set_userdata('formdata', $data);
                //json_encode($your_array);
                //$this->mysmarty->assign('data', $data);
                //$out['result'] = $this->mysmarty->fetch('mywarehouse/BulkStockRecieve_preview.html');	
                $out['result'] = 'OK';
                echo json_encode($out);
            } else {
                $out['result'] = 'No Data';
                echo json_encode($out);
            }
            //Gomail(array('msg_title' => 'Save report', 'msg_body' => printcool($data,true)), 'mr.reece@gmail.com');
            /*
              for ($r = 0, $rlen = count($_POST['data']); $r < $rlen; $r++) {
              $rowId = $r + 1;
              for ($c = 0, $clen = count($_POST['data'][$r]); $c < $clen; $c++) {
              if (!isset($colMap[$c])) {
              continue;
              }
             */
        }
    }

    function EditAuction($id = '', $page = 0) {
        if (isset($_POST) && $_POST) {
            $wdata = array();
            if (isset($_POST['wtitle']))
                $wdata['wtitle'] = $this->input->post('wtitle', true);
            if (isset($_POST['wcost']))
                $wdata['wcost'] = (float) str_replace('| ', '', $this->input->post('wcost', true));
            if (isset($_POST['wnotes']))
                $wdata['wnotes'] = $this->input->post('wnotes', true);
            if (isset($_POST['wdate']))
                $wdata['wdate'] = $this->input->post('wdate', true);
            if (isset($_POST['wvendor']))
                $wdata['wvendor'] = $this->input->post('wvendor', true);
            $wdata['wacat'] = (int) $this->input->post('wacat', true);
            if (isset($_POST['shipping']))
                $wdata['shipping'] = (float) $this->input->post('shipping', true);
            if (isset($_POST['wvendor']))
                $wdata['expenses'] = (float) $this->input->post('expenses', true);

            if (count($wdata) > 0) {
                $this->db->update('warehouse_auctions', $wdata, array('waid' => (int) $id));
                $this->session->set_flashdata('success_msg', 'Auction updated');
                /*
                  $olddata  = $this->Mywarehouse_model->GetAuction((int)$id);
                  if ($wdata['wcost'] != $olddata['wcost'])
                  {

                  $this->load->helper('security');
                  $costyes = 0;
                  $quantityyes = 0;
                  $quantityno = 0;
                  $totalitems = 0;

                  if ((float)$wdata['wcost'] > 0)
                  {

                  $datapool = $this->Mywarehouse_model->getwarehousepricing((int)$id);
                  $olddata = $datapool;
                  if (!$datapool) exit('No warehouse items');
                  $totalitems = count($datapool);
                  foreach ($datapool as $k => $d)
                  {
                  if (trim(xss_clean($d['cost'])) != '')
                  {
                  $costyes = $costyes+($d['cost']);
                  $quantityyes++;
                  }
                  else
                  {
                  $quantityno++;
                  }
                  }


                  $costdata = '<sup>Lot Cost:</sup> '.$wdata['wcost'];
                  $spreadprice =  sprintf("%01.4f", ($wdata['wcost']/$totalitems));
                  $costdata .= ' | <sup>Spread Orig:</sup> $'.$spreadprice;
                  $costdata .= ' | <sup>Lot MOD:</sup> $'.sprintf("%01.4f", ($wdata['wcost']-$costyes));
                  if ($quantityyes > 0) $spreadprice = sprintf("%01.4f", (($wdata['wcost']-$costyes)/$quantityno));
                  $costdata .= ' | <sup>Spread MOD:</sup> $'.$spreadprice;
                  $costdata .= ' | <sup>CostYes:</sup> $'.$costyes;
                  $costdata .= ' | <sup>QuantityYes:</sup> '.$quantityyes.' .pcs';
                  $costdata .= ' | <sup>QuantityNO:</sup> '.$quantityno.' .pcs';
                  $costdata .= ' | <sup>TotalItems:</sup> '.$totalitems.' .pcs';

                  //.
                  $this->db->update('warehouse_auctions', array('costdata' => $costdata), array('waid' => $id));

                  }
                  else $spreadprice = '';

                  if ((float)$wdata['wcost'] > 0)
                  {
                  foreach ($datapool as $k => $d)
                  {
                  if (trim(xss_clean($d['cost'])) != '')
                  {
                  $insert['cost'] = trim(xss_clean($d['cost']));
                  $this->Auth_model->wlog($d['bcn'], (int)$d['wid'], 'cost', $olddata[$k]['cost'], $insert['cost'], 'EditAuction'.$id);
                  }
                  else
                  {
                  $insert['cost'] = $spreadprice;
                  $this->Auth_model->wlog($d['bcn'], (int)$d['wid'], 'cost', 'EMPTY', $insert['cost'], 'EditAuction'.$id);

                  }
                  $this->db->update('warehouse', $insert, array('wid' => $d['wid']));
                  unset($insert);
                  }
                  }

                  }
                 */
            }
            Redirect('Mywarehouse/open/' . (int) $page);
        }
        $this->mysmarty->assign('acat', $this->Mywarehouse_model->GetAuctionCategories());
        $this->mysmarty->assign('auction', $this->Mywarehouse_model->GetAuction((int) $id));
        $this->mysmarty->assign('page', $page);
        $this->mysmarty->view('mywarehouse/edit_auction.html');
    }

    function SaveBulkStock() {

        $db['dates'] = serialize(array(array('created' => CurrentTime(), 'createdstamp' => mktime())));
        $db['adminid'] = (int) $this->session->userdata['admin_id'];
        $db['insid'] = (int) $this->Mywarehouse_model->GetNextInsertOrder();
        $db['bcn_p1'] = date("m") . substr(date("y"), 1, 1);

        $datapool = $this->input->post('data');
        //printcool ($datapool); break;
        $datapool = array_reverse($datapool);

        $auction = $this->input->post('auction', true);
        if (!isset($auction['id']) || (isset($auction['id']) && trim($auction['id']) == ''))
            exit('You have not specified Auction ID');

        $this->load->helper('security');

        $adata['wtitle'] = trim(xss_clean($auction['id']));
        $adata['wcost'] = (float) trim(xss_clean($auction['cost']));

        $adata['shipping'] = (float) trim(xss_clean($auction['shipping']));
        $adata['expenses'] = (float) trim(xss_clean($auction['expenses']));

        $adata['wvendor'] = trim(xss_clean($auction['vendor']));
        $adata['wdate'] = CurrentTime();
        $adata['wnotes'] = trim(xss_clean($auction['notes']));
        $adata['wadmin'] = $db['adminid'];


        $db['waid'] = $this->Mywarehouse_model->HandleAuction($adata);

        $nextbcn = $this->Mywarehouse_model->GetNextBcn((int) $db['bcn_p1']);

        //if ((int)$db['bcn_p1'] == 095) $nextbcn = sprintf('%05u',$this->Mywarehouse_model->GetNextBcn((int)$db['bcn_p1']));
        //else $nextbcn = sprintf('%04u',$this->Mywarehouse_model->GetNextBcn((int)$db['bcn_p1']));

        $insertbatch = array();
        $count = 0;

        $costyes = 0;
        $quantityyes = 0;
        $quantityno = 0;
        $totalitems = 0;
        if ((float) $adata['wcost'] > 0) {
            foreach ($datapool as $k => $d) {
                if ((int) $d['qty'] < 1)
                    $d['qty'] = 1;
                $totalitems = $totalitems + $d['qty'];
                if (trim(xss_clean($d['cost'])) != '') {
                    $costyes = $costyes + ($d['cost'] * $d['qty']);
                    $quantityyes = $quantityyes + $d['qty'];
                } else {
                    $quantityno = $quantityno + $d['qty'];
                }
            }
            $costdata = '<sup>Lot Cost:</sup> ' . $adata['wcost'];
            $spreadprice = sprintf("%01.4f", ($adata['wcost'] / $totalitems));
            $costdata .= ' | <sup>Spread Orig:</sup> $' . $spreadprice;
            $costdata .= ' | <sup>Lot MOD:</sup> $' . sprintf("%01.4f", ($adata['wcost'] - $costyes));
            if ($quantityyes > 0)
                $spreadprice = sprintf("%01.4f", (($adata['wcost'] - $costyes) / $quantityno));
            $costdata .= ' | <sup>Spread MOD:</sup> $' . $spreadprice;
            $costdata .= ' | <sup>CostYes:</sup> $' . $costyes;
            $costdata .= ' | <sup>QuantityYes:</sup> ' . $quantityyes . ' .pcs';
            $costdata .= ' | <sup>QuantityNO:</sup> ' . $quantityno . ' .pcs';
            $costdata .= ' | <sup>TotalItems:</sup> ' . $totalitems . ' .pcs';

            $this->db->update('s', array('costdata' => $costdata), array('waid' => $db['waid']));
        } else
            $spreadprice = '';

        foreach ($datapool as $k => $d) {
            if ((int) $d['qty'] < 1)
                $d['qty'] = 1;

            for ($i = $nextbcn; $i < ($nextbcn + (int) $d['qty']); $i++) {
                if ($i > 1) {
                    $insert['dates'] = $db['dates'];
                    $insert['adminid'] = $db['adminid'];
                    $insert['insid'] = $db['insid'];
                    $insert['bcn_p1'] = (int) $db['bcn_p1'];
                    $insert['bcn_p2'] = (int) $i;
                    // $insert['bcn'] = $db['bcn_p1'].'-'.sprintf('%04u', $insert['bcn_p2']);
                    $insert['bcn'] = $db['bcn_p1'] . '-' . $insert['bcn_p2'];
                    $insert['waid'] = $db['waid'];
                    $insert['aucid'] = addslashes($adata['wtitle']);
                    $insert['status'] = 'Not Tested';

                    if (trim(xss_clean($d['cost'])) != '')
                        $insert['cost'] = trim(xss_clean($d['cost']));
                    else
                        $insert['cost'] = $spreadprice;
                    if (trim(xss_clean($d['title'])) != '')
                        $insert['title'] = xss_clean(addslashes(trim($d['title'])));
                    if (trim(xss_clean($d['mfgpart'])) != '')
                        $insert['mfgpart'] = trim(xss_clean($d['mfgpart']));
                    if (trim(xss_clean($d['mfgname'])) != '')
                        $insert['mfgname'] = trim(xss_clean($d['mfgname']));
                    if (trim(xss_clean($d['lot'])) != '')
                        $insert['lot'] = trim(xss_clean($d['lot']));
                    //$insert['test'] = 1;

                    $e = $this->Mywarehouse_model->CheckBCNDoesNotExists($insert['bcn']);
                    if (!$e)
                        $this->db->insert('warehouse', $insert);
                    else
                        exit('DUPLICATE BCN FOUND EXISTS');
                    //$insertbatch[] = $insert;
                    unset($insert);
                    $count++;
                }
            }
            $nextbcn = $i;
        }

        if ($count > 0) {
            //$this->db->insert_batch('warehouse', $insertbatch); 
            $this->session->set_flashdata('success_msg', 'Inserted ' . ($count) . ' inventory items');
        } else
            $this->session->set_flashdata('error_msg', 'No inventory items inserted');

        Redirect('Mywarehouse/RecieveReport/' . $db['waid']);
    }

    function servicedb() {
        $this->db->select("`wid`, `waid`, `deleted`, `aucid`, `title`, `dates`, `adminid`");

        $this->db->where("deleted", 1);
        $this->query = $this->db->get('warehouse');
        if ($this->query->num_rows() > 0) {
            printcool($this->query->result_array());
            foreach ($this->query->result_array() as $k => $v) {
                $this->db->where('wid', (int) $v['wid']);
                //$this->db->delete('warehouse'); 	
            }
        }
    }

    function saveskutitle($id) {
        if ((int) $id == 0)
            echo 'ERROR';
        $this->db->update('warehouse_sku', array('title' => trim($this->input->post('str'))), array('wsid' => (int) $id));
        $this->db->select("title");
        $this->db->where("wsid", (int) $id);
        $this->query = $this->db->get('warehouse_sku');
        if ($this->query->num_rows() > 0) {
            $title = $this->query->row_array();
            echo($title['title']);
        } else
            echo 'ERROR';
    }

    function UpdateFields() {
        if ((int) $this->input->post('id') == 0)
            echo 'ERROR';
        $input = CleanInput(trim($this->input->post('value')));
        //gomail(array('msg_title'=>$this->input->post('field'), 'msg_body' => printcool ($input,true)), 'mr.reece@gmail.com');
        $updatearray[CleanInput(trim($this->input->post('field')))] = $input;
        if (isset($_POST['tech']) && $_POST['tech']) {
            $updatearray['tech'] = (int) $this->session->userdata['admin_id'];
            $updatearray['techlastupdate'] = CurrentTime();
        } else {
            $updatearray['adminid'] = (int) $this->session->userdata['admin_id'];
        }

        $view = false;
        if (isset($_POST['view']))
            $view = trim($this->input->post('view', true));

        $from = trim($this->Mywarehouse_model->GetField(CleanInput(trim($this->input->post('field'))), (int) $this->input->post('id')));

        if ($from != $input) {

            $this->Auth_model->wlog($this->Mywarehouse_model->wid2bcn((int) $this->input->post('id')), (int) $this->input->post('id'), CleanInput(trim($this->input->post('field'))), $from, $input, $view);

            $this->db->update('warehouse', $updatearray, array('wid' => (int) $this->input->post('id')));
        }
        echo($input);
    }

    function RefreshField() {
        if (!isset($_POST['field']))
            exit('ERROR');
        if (trim(CleanInput($_POST['field'] == 'techlastupdate')))
            $field = trim(CleanInput($_POST['field']));
        elseif (trim(CleanInput($_POST['field'] == 'tech')))
            $field = trim(CleanInput($_POST['field']));
        else
            exit('ERROR');
        if ($field)
            echo($this->Mywarehouse_model->GetField($field, (int) $_POST['id']));
    }

    function index() {

        /*
          if ($this->session->userdata['type'] == 'master' || ($this->session->userdata['type'] != 'master' && $this->session->userdata['accounting'] == 1))
          {

          }
         */
        $this->open();
    }

    function open($page = 1, $cat = 0) {
        $this->session->set_userdata('warehouse_area', '');

        $load = $this->Mywarehouse_model->GetList((int) $page, (int) $cat);

        $this->mysmarty->assign('list', $load['results']);
        $this->mysmarty->assign('pages', $load['pages']);
        $this->mysmarty->assign('accounting', $load['accounting']);
        $this->mysmarty->assign('sumaccounting', $load['sumaccounting']);
        if (count($load['statuses']) > 0)
            foreach ($load['statuses'] as $k => $v)
                ksort($load['statuses'][$k]);
        if (is_array($load['sumstatuses']))
            ksort($load['sumstatuses']);
        $this->mysmarty->assign('statuses', $load['statuses']);
        $this->mysmarty->assign('sumstatuses', $load['sumstatuses']);
        $this->mysmarty->assign('location', $load['location']);
        $this->mysmarty->assign('locationsum', $load['locationsum']);
        $this->mysmarty->assign('sn', $load['sn']);
        $this->mysmarty->assign('snsum', $load['snsum']);
        $this->mysmarty->assign('page', (int) $page);
        $this->mysmarty->assign('cat', (int) $cat);
        $this->mysmarty->assign('admins', $this->Mywarehouse_model->GetAdminList());
        $this->mysmarty->assign('acat', $this->Mywarehouse_model->GetAuctionCategories());
        $this->mysmarty->view('mywarehouse/main.html');
    }

    function ModWarehouseCat($id = 0) {
        if ($_POST['wcat'] != '') {
            if ($id == 0)
                $this->db->insert('warehouse_auction_categories', array('wacat_title' => $this->input->post('wcat', TRUE)));
            else
                $this->db->update('warehouse_auction_categories', array('wacat_title' => $this->input->post('wcat', TRUE)), array('wacat_id' => (int) $id));
        }
        Redirect('Mywarehouse/EditAuctionCategories');
        //Redirect('Mywarehouse/open/'.(int)$page);	
    }

    function DeleteAucCategory($wacat = '') {
        $this->db->where('wacat_id', (int) $wacat);
        $this->db->delete('warehouse_auction_categories');
        $this->db->select('waid');
        $this->db->where('wacat', (int) $wacat);
        $d = $this->db->get('warehouse_auctions');
        if ($d->num_rows() > 0) {
            foreach ($d->result_array() as $v) {
                $this->db->update('warehouse_auctions', array('wacat' => 0), array('waid' => $v['waid']));
            }
        }
        Redirect('Mywarehouse/EditAuctionCategories');
    }

    function EditAuctionCategories() {
        $this->mysmarty->assign('acat', $this->Mywarehouse_model->GetAuctionCategories());
        $this->mysmarty->view('mywarehouse/main_cat_auc.html');
    }

    function moveauctions() {
        exit();
        $this->db->select('waid');
        $this->db->where('waid <', 149);
        $this->db->where('waid >', 0);
        $d = $this->db->get('warehouse_auctions');
        if ($d->num_rows() > 0) {
            foreach ($d->result_array() as $v) {
                $this->db->update('warehouse_auctions', array('wacat' => 6), array('waid' => $v['waid']));
            }
        }
    }

    function packs($aucid = '') {
        if (trim($aucid) == '')
            exit('Bad ID');
        $load = $this->Mywarehouse_model->GetPacks(trim($aucid));
        $this->mysmarty->assign('list', $load);
        $this->mysmarty->assign('admins', $this->Mywarehouse_model->GetAdminList());
        $this->mysmarty->assign('auction', $this->Mywarehouse_model->GetAuction(trim($aucid)));
        $this->mysmarty->view('mywarehouse/main_auc.html');
    }

    function testing($id = '', $focus = '') {//if ($this->session->userdata['admin_id'] != 1) exit('Commiting update, back in 5 mins');
        $this->session->set_userdata('warehouse_area', 'testing');
        if ((int) $id == 0)
            $id = $this->Mywarehouse_model->GetLastAuc();
        $list = $this->Mywarehouse_model->GetTesting((int) $id);

        if (isset($_POST) && $_POST) {
            $colMap = array(
                0 => 'GO',
                1 => 'bcn',
                2 => 'oldbcn',
                3 => 'title',
                4 => 'location',
                5 => 'sn',
                6 => 'post',
                7 => 'battery',
                8 => 'charger',
                9 => 'hddstatus',
                10 => 'problems',
                11 => 'notes',
                12 => 'status',
                13 => 'status_notes',
                14 => 'partsneeded',
                15 => 'warranty'
            );

            $bcolMap = array(
                0 => 'GO',
                1 => 'BCN',
                2 => 'Old BCN',
                3 => 'Title',
                4 => 'Location',
                5 => 'SN',
                6 => 'POST',
                7 => 'Battery',
                8 => 'Charger',
                9 => 'HDD Status',
                10 => 'Problems',
                11 => 'Notes',
                12 => 'Status',
                13 => 'Status Notes',
                14 => 'Parts Needed',
                15 => 'Warranty'
            );
            $out = '';
            $sout = '';
            $sessback = $this->_loadsession($this->session->userdata('sessfile'));
            $saveid = $sessback['acclot'];
            $saverel = $sessback['accrel'];

            //printcool ($_POST);
            if ($saveid != (int) $id) {
                echo json_encode('!!!!! CANNOT SAVE. YOU HAVE ANOTHER TESTING EDITOR OPEN !!!!!');
            } //printcool ($_POST);
            else {
                foreach ($_POST as $d) {
                    foreach ($d as $dd) {
                        //$dd[0] ROW
                        //$dd[1] COL
                        //$dd[2] FROM VAL
                        //$dd[3] TO VAL
                        //printcool ($dd);
                        //printcool ($list[(int)$dd[0]][$colMap[$dd[1]]]);
                        //$saverel[(int)$dd[0]]['wid']
                        if ($dd[2] != $dd[3]) {
                            $this->Auth_model->wlog($saverel[(int) $dd[0]]['bcn'], $saverel[(int) $dd[0]]['wid'], $colMap[$dd[1]], $dd[2], $dd[3]);

                            $out .= ' "' . $bcolMap[$dd[1]] . '" for BCN ' . $saverel[(int) $dd[0]]['bcn'] . ' Changed from "' . $dd[2] . '" to "' . $dd[3] . '" ';
                            $sout .= $saverel[(int) $dd[0]]['bcn'] . '/"' . $bcolMap[$dd[1]] . '" Changed ';

                            $this->db->update('warehouse', array($colMap[$dd[1]] => $dd[3], 'tech' => (int) $this->session->userdata['admin_id'], 'techlastupdate' => CurrentTime()), array('wid' => $saverel[(int) $dd[0]]['wid']));

                            $out = array('msg' => $out, 'smsg' => $sout, 'row' => $dd[0], 'col' => $dd['1']);
                        }
                    }
                }
                echo json_encode($out);
            }
        } else {


            //

            $fielset = array('testing' => array(
                    'headers' => "'GO', 'BCN','Old BCN',  'Title','Location', 'SN', 'POST', 'Battery', 'Charger' , 'HDD Status', 'Problems', 'Notes', 'Status', 'Status notes', 'Parts Needed', 'Warranty',  'LastUpdt', 'Tech'",
                    /* 'rowheaders' => $list['headers'], */
                    'width' => "30, 80, 100, 150, 120, 125, 50, 50, 50, 150, 200, 100, 125, 125, 125, 125, 125, 125",
                    'startcols' => 18,
                    'startrows' => count($list['data']),
                    'autosaveurl' => "/Mywarehouse/Testing/" . (int) $id,
                    'colmap' => '{readOnly: true, renderer: "html"},{readOnly: true},{},{},{},{},{type: "checkbox", checkedTemplate: 1, uncheckedTemplate: 0},{type: "checkbox", checkedTemplate: 1, uncheckedTemplate: 0},{type: "checkbox", checkedTemplate: 1, uncheckedTemplate: 0},{},{},{},{type: "dropdown", source: [' . $this->statuses['testingstring'] . ']},{},{},{},{readOnly: true},{readOnly: true}')
            );


            if ($list) {
                $this->_AuctionItemData($list);

                $sesfile = $this->_savesession(array('accrel' => $list['headers'], 'acclot' => (int) $id));
                $this->session->set_userdata(array('sessfile' => $sesfile));
                $loaddata = '';
                $adms = $this->Mywarehouse_model->GetAdminList();
                foreach ($list['data'] as $k => $l) {
                    $loaddata .= "['<a target=\"_blank\" href=\"/Mywarehouse/gotobcn/" . cstr($l['bcn']) . "\"><img src=\"/images/admin/table.png\" border=\"0\"></a>', '" . cstr($l['bcn']) . "', '" . cstr($l['oldbcn']) . "', '" . cstr($l['title']) . "', '" . cstr($l['location']) . "', '" . cstr($l['sn']) . "', '" . cstr($l['post']) . "', '" . cstr($l['battery']) . "', '" . cstr($l['charger']) . "', '" . cstr($l['hddstatus']) . "', '" . cstr($l['problems']) . "', '" . cstr($l['notes']) . "',  '" . cstr($l['status']) . "', '" . cstr($l['status_notes']) . "', '" . cstr($l['partsneeded']) . "','" . cstr($l['warranty']) . "', '" . cstr($l['techlastupdate']) . "', '" . $adms[$l['tech']] . "'],
				";
                }
            }

            $this->mysmarty->assign('headers', $fielset['testing']['headers']);
            $this->mysmarty->assign('rowheaders', $fielset['testing']['rowheaders']);
            $this->mysmarty->assign('width', $fielset['testing']['width']);
            $this->mysmarty->assign('startcols', $fielset['testing']['startcols']);
            $this->mysmarty->assign('startrows', $fielset['testing']['startrows']);
            $this->mysmarty->assign('autosaveurl', $fielset['testing']['autosaveurl']);
            $this->mysmarty->assign('colmap', $fielset['testing']['colmap']);
            $this->mysmarty->assign('loaddata', rtrim($loaddata, ','));
            $this->mysmarty->assign('list', $list['data']);
            $this->mysmarty->assign('copyrows', count($list['data']));
            $this->mysmarty->assign('id', (int) $id);
            $this->mysmarty->assign('atitle', $this->Mywarehouse_model->AuctionIdToName((int) $id));
            $this->mysmarty->assign('focus', (int) $focus);
            $this->mysmarty->assign('admins', $this->Mywarehouse_model->GetAdminList());


            if ((int) $focus > 0)
                $this->SingleTesting((int) $focus);

            $this->mysmarty->view('mywarehouse/testing.html');
        }
    }

    function SingleTesting($id = '') {//if ($this->session->userdata['admin_id'] != 1) exit('Commiting update, back in 5 mins');
        $list = $this->Mywarehouse_model->GetSingle((int) $id);

        if (isset($_POST) && $_POST) {
            $colMap = array(
                0 => 'GO',
                1 => 'bcn',
                2 => 'oldbcn',
                3 => 'title',
                4 => 'location',
                5 => 'sn',
                6 => 'post',
                7 => 'battery',
                8 => 'charger',
                9 => 'hddstatus',
                10 => 'problems',
                11 => 'notes',
                12 => 'status',
                13 => 'status_notes',
                14 => 'partsneeded',
                15 => 'warranty'
            );

            $bcolMap = array(
                0 => 'GO',
                1 => 'BCN',
                2 => 'Old BCN',
                3 => 'Title',
                4 => 'Location',
                5 => 'SN',
                6 => 'POST',
                7 => 'Battery',
                8 => 'Charger',
                9 => 'HDD Status',
                10 => 'Problems',
                11 => 'Notes',
                12 => 'Status',
                13 => 'Status Notes',
                14 => 'Parts Needed',
                15 => 'Warranty'
            );

            $out = '';
            $sout = '';
            foreach ($_POST as $d) {
                foreach ($d as $dd) {
                    //$dd[0] ROW
                    //$dd[1] COL
                    //$dd[2] FROM VAL
                    //$dd[3] TO VAL
                    //printcool ($dd);
                    //printcool ($list[(int)$dd[0]][$colMap[$dd[1]]]);
                    //$saverel[(int)$dd[0]]['wid']

                    if ($dd[2] != $dd[3]) {
                        $this->Auth_model->wlog($list['data'][0]['bcn'], (int) $id, $colMap[$dd[1]], $dd[2], $dd[3]);

                        $out .= ' "' . $bcolMap[$dd[1]] . '" for BCN ' . $list['data'][0]['bcn'] . ' Changed from "' . $dd[2] . '" to "' . $dd[3] . '" ';
                        $sout .= $list['data'][0]['bcn'] . '/"' . $bcolMap[$dd[1]] . '" Changed ';

                        $this->db->update('warehouse', array($colMap[$dd[1]] => $dd[3], 'tech' => (int) $this->session->userdata['admin_id'], 'techlastupdate' => CurrentTime()), array('wid' => (int) $id));
                        $out = array('msg' => $out, 'smsg' => $sout, 'row' => $dd[0], 'col' => $dd['1']);
                    }
                }
            }
            echo json_encode($out);
        } else {
            if ($list) {
                $loaddata = '';
                $adms = $this->Mywarehouse_model->GetAdminList();
                foreach ($list['data'] as $k => $l) {
                    $loaddata .= "['<a target=\"_blank\" href=\"/Mywarehouse/gotobcn/" . cstr($l['bcn']) . "\"><img src=\"/images/admin/table.png\" border=\"0\"></a>', '" . cstr($l['bcn']) . "', '" . cstr($l['oldbcn']) . "', '" . cstr($l['title']) . "', '" . cstr($l['location']) . "', '" . cstr($l['sn']) . "', '" . cstr($l['post']) . "', '" . cstr($l['battery']) . "', '" . cstr($l['charger']) . "', '" . cstr($l['hddstatus']) . "', '" . cstr($l['problems']) . "', '" . cstr($l['notes']) . "', '" . cstr($l['status']) . "', '" . cstr($l['status_notes']) . "', '" . cstr($l['partsneeded']) . "','" . cstr($l['warranty']) . "', '" . cstr($l['techlastupdate']) . "', '" . $adms[$l['tech']] . "'],
				";
                }
            }

            $fielset = array('testing' => array(
                    'headers' => "'GO', 'BCN', 'Old BCN', 'Title', 'Location', 'SN', 'POST', 'Battery', 'Charger' , 'HDD Status', 'Problems', 'Notes', 'Status', 'Status notes', 'Parts Needed', 'Warranty',  'LastUpdt', 'Tech'",
                    /* 'rowheaders' => $list['headers'], */
                    'width' => "30,80, 100, 120, 150, 125, 50, 50, 50, 200, 100, 150, 125, 125, 125, 125, 125, 125",
                    'startcols' => 18,
                    'startrows' => 1,
                    'autosaveurl' => "/Mywarehouse/SingleTesting/" . (int) $id,
                    'colmap' => '{readOnly: true, renderer: "html"},{readOnly: true},{},{},{},{},{type: "checkbox", checkedTemplate: 1, uncheckedTemplate: 0},{type: "checkbox", checkedTemplate: 1, uncheckedTemplate: 0},{type: "checkbox", checkedTemplate: 1, uncheckedTemplate: 0},{},{},{},{type: "dropdown", source: [' . $this->statuses['testingstring'] . ']},{},{},{},{readOnly: true},{readOnly: true}')
            );

            $this->mysmarty->assign('sheaders', $fielset['testing']['headers']);
            $this->mysmarty->assign('srowheaders', $fielset['testing']['rowheaders']);
            $this->mysmarty->assign('swidth', $fielset['testing']['width']);
            $this->mysmarty->assign('sstartcols', $fielset['testing']['startcols']);
            $this->mysmarty->assign('sstartrows', $fielset['testing']['startrows']);
            $this->mysmarty->assign('sautosaveurl', $fielset['testing']['autosaveurl']);
            $this->mysmarty->assign('scolmap', $fielset['testing']['colmap']);
            $this->mysmarty->assign('sloaddata', rtrim($loaddata, ','));
        }
    }

    function fixpaidnosold() {
        exit();
    }

    function SingleAccounting($id = '') { //if ($this->session->userdata['admin_id'] != 1) exit('Commiting update, back in 5 mins');
        $list = $this->Mywarehouse_model->GetSingle((int) $id);

        if (isset($_POST) && $_POST) {
            $colMap = array(
                0 => 'GO',
                1 => 'bcn',
                2 => 'oldbcn',
                3 => 'title',
                4 => 'location',
                5 => 'listed',
                6 => 'listed_date',
                7 => 'sold_date',
                8 => 'sold',
                9 => 'paid',
                10 => 'shipped',
                11 => 'shipped_actual',
                12 => 'ordernotes',
                13 => 'sellingfee',
                14 => 'netprofit',
                15 => 'cost',
                16 => 'status'
            );


            $bcolMap = array(
                0 => 'GO',
                1 => 'BCN',
                2 => 'Old BCN',
                3 => 'Title',
                4 => 'Location',
                5 => 'Where Listed',
                6 => 'Date Listed',
                7 => 'Date Sold',
                8 => 'Where Sold',
                9 => 'Price Sold',
                10 => 'Shipping Cost',
                11 => 'Actual Sh. Cost',
                12 => 'Order Notes',
                13 => 'Selling Fee',
                14 => 'Net Profit',
                15 => 'Cost',
                16 => 'Status'
            );
            if ($this->session->userdata['type'] != 'master' && $this->session->userdata['accounting'] == 0) {
                $colMap[13] = 'status';
                unset($colMap[14]);
                unset($colMap[15]);
                $bcolMap[13] = 'Status';
                unset($bcolMap[14]);
                unset($bcolMap[15]);
            }


            $out = '';
            $sout = '';
            foreach ($_POST as $d) {
                foreach ($d as $dd) {
                    //$dd[0] ROW
                    //$dd[1] COL
                    //$dd[2] FROM VAL
                    //$dd[3] TO VAL
                    //printcool ($dd);
                    //printcool ($list[(int)$dd[0]][$colMap[$dd[1]]]);
                    //$saverel[(int)$dd[0]]['wid']
                    if ($dd[2] != $dd[3]) {
                        $this->Auth_model->wlog($list['data'][0]['bcn'], (int) $id, $colMap[$dd[1]], $dd[2], $dd[3]);

                        $out .= ' "' . $bcolMap[$dd[1]] . '" for BCN ' . $list['data'][0]['bcn'] . ' Changed from "' . $dd[2] . '" to "' . $dd[3] . '" ';
                        $sout .= $list['data'][0]['bcn'] . '/"' . $bcolMap[$dd[1]] . '" Changed ';

                        $updt = array($colMap[$dd[1]] => $dd[3], 'aupdt' => CurrentTime());
                        if ($colMap[$dd[1]] == 'status')
                            $updt['status_notes'] = $this->Mywarehouse_model->GetStatusNotes((int) $id) . ' | Changed from: ' . $dd[2];
                        $this->db->update('warehouse', $updt, array('wid' => (int) $id));

                        $out = array('msg' => $out, 'smsg' => $sout, 'row' => $dd[0], 'col' => $dd['1']);
                    }
                }
            }
            echo json_encode($out);
        } else {
            if ($list) {
                $loaddata = '';
                $adms = $this->Mywarehouse_model->GetAdminList();
                foreach ($list['data'] as $k => $l) {
                    if ($this->session->userdata['type'] == 'master' || ($this->session->userdata['type'] != 'master' && $this->session->userdata['accounting'] == 1)) {
                        $loaddata .= "['<a target=\"_blank\" href=\"/Mywarehouse/gotobcn/" . cstr($l['bcn']) . "\"><img src=\"/images/admin/table.png\" border=\"0\"></a>', '" . cstr($l['bcn']) . "', '" . cstr($l['oldbcn']) . "', '" . cstr($l['title']) . "', '" . cstr($l['location']) . "', '" . cstr($l['listed']) . "', '" . cstr($l['listed_date']) . "', '" . cstr($l['sold_date']) . "', '" . cstr($l['sold']) . "', '" . cstr($l['paid']) . "', '" . cstr($l['shipped']) . "', '" . cstr($l['shipped_actual']) . "', '" . cstr($l['ordernotes']) . "', '" . cstr($l['sellingfee']) . "',  '" . cstr($l['netprofit']) . "', '" . cstr($l['cost']) . "', '" . cstr($l['status']) . "', '" . cstr($l['aupdt']) . "'],
				";
                    } else
                        $loaddata .= "['<a target=\"_blank\" href=\"/Mywarehouse/gotobcn/" . cstr($l['bcn']) . "\"><img src=\"/images/admin/table.png\" border=\"0\"></a>', '" . cstr($l['bcn']) . "', '" . cstr($l['oldbcn']) . "', '" . cstr($l['title']) . "', '" . cstr($l['location']) . "', '" . cstr($l['listed']) . "', '" . cstr($l['listed_date']) . "', '" . cstr($l['sold_date']) . "', '" . cstr($l['sold']) . "', '" . cstr($l['paid']) . "', '" . cstr($l['shipped']) . "',  '" . cstr($l['shipped_actual']) . "','" . cstr($l['ordernotes']) . "', '" . cstr($l['status']) . "', '" . cstr($l['aupdt']) . "'],
				";
                }
            }

            if ($this->session->userdata['type'] == 'master' || ($this->session->userdata['type'] != 'master' && $this->session->userdata['accounting'] == 1)) {
                $fielset = array('accounting' => array(
                        'headers' => "'GO','BCN', 'Old BCN', 'Title', 'Location', 'Where Listed', 'Date Listed', 'Date Sold', 'Where Sold','Price Sold', 'Shipping Cost', 'Actual Sh. Cost', 'Order Notes', 'Selling Fee', 'Net Profit', 'Cost',  'Status', 'Last Upd'",
                        /* 'rowheaders' => $list['headers'], */
                        'width' => "30, 80, 100, 300, 125, 125, 125, 125, 125, 125,125, 125, 125, 125, 125, 125, 125, 125",
                        'startcols' => 18,
                        'startrows' => 1,
                        'autosaveurl' => "/Mywarehouse/SingleAccounting/" . (int) $id,
                        'colmap' => '{readOnly: true, renderer: "html"},{readOnly: true},{},{},{},{},{},{},{},{},{},{},{},{},{readOnly: true},{},{type: "dropdown", source: [' . $this->statuses['accountingstring'] . ']},{readOnly: true}')
                );
            } else {
                $fielset = array('accounting' => array(
                        'headers' => "'GO', 'BCN', 'Old BCN', 'Title', 'Location', 'Where Listed', 'Date Listed', 'Date Sold', 'Where Sold','Price Sold', 'Shipping Cost', 'Actual Sh. Cost','Order Notes',  'Status', 'Last Upd'",
                        /* 'rowheaders' => $list['headers'], */
                        'width' => "30, 80, 100, 300, 125, 125, 125, 125, 125, 125, 125, 125,125, 125",
                        'startcols' => 15,
                        'startrows' => 1,
                        'autosaveurl' => "/Mywarehouse/SingleAccounting/" . (int) $id,
                        'colmap' => '{readOnly: true, renderer: "html"},{readOnly: true},{},{},{},{},{},{},{},{},{},{},{type: "dropdown", source: [' . $this->statuses['accountingstring'] . ']},{},{readOnly: true}')
                );
            }


            $this->mysmarty->assign('sheaders', $fielset['accounting']['headers']);
            $this->mysmarty->assign('srowheaders', $fielset['accounting']['rowheaders']);
            $this->mysmarty->assign('swidth', $fielset['accounting']['width']);
            $this->mysmarty->assign('sstartcols', $fielset['accounting']['startcols']);
            $this->mysmarty->assign('sstartrows', $fielset['accounting']['startrows']);
            $this->mysmarty->assign('sautosaveurl', $fielset['accounting']['autosaveurl']);
            $this->mysmarty->assign('scolmap', $fielset['accounting']['colmap']);
            $this->mysmarty->assign('sloaddata', rtrim($loaddata, ','));

            //$this->mysmarty->assign('focus', (int)$id);
            //$this->mysmarty->view('mywarehouse/accounting.html');
        }
    }

    function Accounting($id = '', $focus = '') { //if ($this->session->userdata['admin_id'] != 1) exit('Commiting update, back in 5 mins');
        if ($id == 0)
            $list = array();
        elseif ($id == '')
            $id = $this->Mywarehouse_model->GetLastAuc();
        $list = $this->Mywarehouse_model->GetAccounting((int) $id);
        //if ($id == 0) printcool ($list);
        $this->session->set_userdata('warehouse_area', 'accounting');

        if (isset($_POST) && $_POST) {
            $colMap = array(
                0 => 'GO',
                1 => 'bcn',
                2 => 'oldbcn',
                3 => 'title',
                4 => 'location',
                5 => 'listed',
                6 => 'listed_date',
                7 => 'sold_date',
                8 => 'sold',
                9 => 'paid',
                10 => 'shipped',
                11 => 'shipped_actual',
                12 => 'ordernotes',
                13 => 'sellingfee',
                14 => 'netprofit',
                15 => 'cost',
                16 => 'status'
            );


            $bcolMap = array(
                0 => 'GO',
                1 => 'BCN',
                2 => 'Old BCN',
                3 => 'Title',
                4 => 'Location',
                5 => 'Where Listed',
                6 => 'Date Listed',
                7 => 'Date Sold',
                8 => 'Where Sold',
                9 => 'Price Sold',
                10 => 'Shipping Cost', //
                11 => 'Actual Sh. Cost', //
                12 => 'Order Notes',
                13 => 'Selling Fee', //	
                14 => 'Net Profit', //
                15 => 'Cost', //				 
                16 => 'Status'
            );

            if ($this->session->userdata['type'] != 'master' && $this->session->userdata['accounting'] == 0) {
                $colMap[13] = 'status';
                unset($colMap[14]);
                unset($colMap[15]);
                $bcolMap[13] = 'Status';
                unset($bcolMap[14]);
                unset($bcolMap[14]);
            }

            $out = '';
            $sout = '';
            $sessback = $this->_loadsession($this->session->userdata('sessfile'));
            $saveid = $sessback['acclot'];
            $saverel = $sessback['accrel'];
            if ($saveid != (int) $id) {
                echo json_encode(array('msg' => '!!!!! CANNOT SAVE. YOU HAVE ANOTHER ACCOUNTING EDITOR OPEN !!!!!'));
            } //printcool ($_POST);
            else {
                foreach ($_POST as $d) {
                    foreach ($d as $dd) {
                        //$dd[0] ROW
                        //$dd[1] COL
                        //$dd[2] FROM VAL
                        //$dd[3] TO VAL
                        //printcool ($dd);
                        //printcool ($list[(int)$dd[0]][$colMap[$dd[1]]]);
                        //$saverel[(int)$dd[0]]['wid']
                        if ($dd[2] != $dd[3]) {
                            $this->Auth_model->wlog($saverel[(int) $dd[0]]['bcn'], $saverel[(int) $dd[0]]['wid'], $colMap[$dd[1]], $dd[2], $dd[3]);

                            $out .= ' "' . $bcolMap[$dd[1]] . '" for BCN ' . $saverel[(int) $dd[0]]['bcn'] . ' Changed from "' . $dd[2] . '" to "' . $dd[3] . '" ';
                            $sout .= $saverel[(int) $dd[0]]['bcn'] . '/"' . $bcolMap[$dd[1]] . '" Changed ';


                            $updt = array($colMap[$dd[1]] => $dd[3], 'aupdt' => CurrentTime());
                            if ($colMap[$dd[1]] == 'status')
                                $updt['status_notes'] = $this->Mywarehouse_model->GetStatusNotes($saverel[(int) $dd[0]]['wid']) . ' | Changed from: ' . $dd[2];
                            $this->db->update('warehouse', $updt, array('wid' => $saverel[(int) $dd[0]]['wid']));

                            $out = array('msg' => $out, 'smsg' => $sout, 'row' => $dd[0], 'col' => $dd['1']);
                        }
                    }
                }
                echo json_encode($out);
            }
        } else {

            if ($list) {
                $this->_AuctionItemData($list);

                $sesfile = $this->_savesession(array('accrel' => $list['headers'], 'acclot' => (int) $id));
                $this->session->set_userdata(array('sessfile' => $sesfile));
                $loaddata = '';
                $adms = $this->Mywarehouse_model->GetAdminList();
                foreach ($list['data'] as $k => $l) {
                    if ($this->session->userdata['type'] == 'master' || ($this->session->userdata['type'] != 'master' && $this->session->userdata['accounting'] == 1)) {
                        $loaddata .= "['<a target=\"_blank\" href=\"/Mywarehouse/gotobcn/" . cstr($l['bcn']) . "\"><img src=\"/images/admin/table.png\" border=\"0\"></a>', '" . cstr($l['bcn']) . "', '" . cstr($l['oldbcn']) . "', '" . cstr($l['title']) . "', '" . cstr($l['location']) . "','" . cstr($l['listed']) . "', '" . cstr($l['listed_date']) . "', '" . cstr($l['sold_date']) . "', '" . cstr($l['sold']) . "', '" . cstr($l['paid']) . "', '" . cstr($l['shipped']) . "', '" . cstr($l['shipped_actual']) . "','" . cstr($l['ordernotes']) . "', '" . cstr($l['sellingfee']) . "', '" . cstr($l['netprofit']) . "', '" . cstr($l['cost']) . "', '" . cstr($l['status']) . "', '" . cstr($l['aupdt']) . "'],
				";
                    } else
                        $loaddata .= "['<a target=\"_blank\" href=\"/Mywarehouse/gotobcn/" . cstr($l['bcn']) . "\"><img src=\"/images/admin/table.png\" border=\"0\"></a>', '" . cstr($l['bcn']) . "', '" . cstr($l['oldbcn']) . "', '" . cstr($l['title']) . "', '" . cstr($l['location']) . "', '" . cstr($l['listed']) . "', '" . cstr($l['listed_date']) . "', '" . cstr($l['sold_date']) . "', '" . cstr($l['sold']) . "', '" . cstr($l['paid']) . "',  '" . cstr($l['shipped']) . "', '" . cstr($l['shipped_actual']) . "', '" . cstr($l['ordernotes']) . "', '" . cstr($l['status']) . "','" . cstr($l['aupdt']) . "'],
				";
                }
            }

            //printcool ($list['headers']);
            $this->mysmarty->assign('list', $list['data']);
            $this->mysmarty->assign('id', (int) $id);
            $this->mysmarty->assign('atitle', $this->Mywarehouse_model->AuctionIdToName((int) $id));
            $this->mysmarty->assign('focus', (int) $focus);
            //$this->mysmarty->assign('admins', $this->Mywarehouse_model->GetAdminList());

            if ($this->session->userdata['type'] == 'master' || ($this->session->userdata['type'] != 'master' && $this->session->userdata['accounting'] == 1)) {

                $fielset = array('accounting' => array(
                        'headers' => "'GO', 'BCN', 'Old BCN', 'Title', 'Location', 'Where Listed', 'Date Listed', 'Date Sold', 'Where Sold', 'Price Sold', 'Shipping Cost','Actual Sh. Cost', 'Order Notes', 'Selling Fee', 'Net Profit', 'Cost', 'Status', 'Last Upd'",
                        /* 'rowheaders' => $list['headers'], */
                        'width' => "30, 80, 100, 300, 125, 125, 125, 125, 125, 125, 125, 125, 125,125, 125, 125, 125, 125",
                        'startcols' => 18,
                        'startrows' => 10,
                        'autosaveurl' => "/Mywarehouse/Accounting/" . (int) $id,
                        'colmap' => '{readOnly: true, renderer: "html"},{readOnly: true},{},{},{},{},{},{},{},{},{},{},{},{},{readOnly: true},{},{type: "dropdown", source: [' . $this->statuses['accountingstring'] . ']},{readOnly: true}')
                );
            } else {
                $fielset = array('accounting' => array(
                        'headers' => "'GO', 'BCN', 'Old BCN', 'Title', 'Location', 'Where Listed', 'Date Listed', 'Date Sold', 'Where Sold', 'Price Sold', 'Shipping Cost','Actual Sh. Cost', 'Order Notes', 'Status', 'Last Upd'",
                        /* 'rowheaders' => $list['headers'], */
                        'width' => "30, 80, 100, 300, 125, 125, 125, 125, 125, 125, 125,125, 125, 125, 125",
                        'startcols' => 15,
                        'startrows' => 10,
                        'autosaveurl' => "/Mywarehouse/Accounting/" . (int) $id,
                        'colmap' => '{readOnly: true, renderer: "html"},{readOnly: true},{},{},{},{},{},{},{},{},{},{},{},{type: "dropdown", source: [' . $this->statuses['accountingstring'] . ']},{readOnly: true}')
                );
            }


            $this->mysmarty->assign('headers', $fielset['accounting']['headers']);
            $this->mysmarty->assign('rowheaders', $fielset['accounting']['rowheaders']);
            $this->mysmarty->assign('width', $fielset['accounting']['width']);
            $this->mysmarty->assign('startcols', $fielset['accounting']['startcols']);
            $this->mysmarty->assign('startrows', $fielset['accounting']['startrows']);
            $this->mysmarty->assign('autosaveurl', $fielset['accounting']['autosaveurl']);
            $this->mysmarty->assign('colmap', $fielset['accounting']['colmap']);
            $this->mysmarty->assign('loaddata', rtrim($loaddata, ','));
            $this->mysmarty->assign('copyrows', count($list['data']));

            if ((int) $focus > 0)
                $this->SingleAccounting((int) $focus);

            $this->mysmarty->view('mywarehouse/accounting.html');
        }
    }

    function _AuctionItemData($list) {
        $idata['location']['N'] = 0;
        $idata['location']['Y'] = 0;
        $idata['sn']['N'] = 0;
        $idata['sn']['Y'] = 0;
        foreach ($list['data'] as $i) {

            if ($i['deleted'] == 0) {

                if ((float) $i['paid'] == 0)
                    $idata['accounting']['emptypaid'] ++;
                else
                    $idata['accounting']['paid'] = $idata['accounting']['paid'] + (float) $i['paid'];

                if ((float) $i['cost'] == 0)
                    $idata['accounting']['emptycost'] ++;
                else
                    $idata['accounting']['cost'] = $idata['accounting']['cost'] + ((float) $i['cost'] + (float) $i['shipped'] + (float) $i['sellingfee']);

                if ((float) $i['netprofit'] == 0)
                    $idata['accounting']['emptynetprofit'] ++;
                else
                    $idata['accounting']['netprofit'] = $idata['accounting']['netprofit'] + (float) $i['netprofit'];
                if (isset($idata['accounting']['cnetprofit']))
                    $idata['accounting']['cnetprofit'] = $idata['accounting']['cnetprofit'] + ((float) $i['paid'] - (float) $i['cost']);
                else
                    $idata['accounting']['cnetprofit'] = (float) $i['paid'] - (float) $i['cost'];

                if ($i['status'] == '') {
                    if (isset($idata['statuses']['Empty']))
                        $idata['statuses']['Empty'] ++;
                    else
                        $idata['statuses']['Empty'] = 1;
                } else {
                    if (isset($idata['statuses'][$i['status']]))
                        $idata['statuses'][$i['status']] ++;
                    else
                        $idata['statuses'][$i['status']] = 1;
                }
                if (trim($i['location']) == '') {
                    $idata['location']['N'] ++;
                    ;
                } else {
                    $idata['location']['Y'] ++;
                }

                if (trim($i['sn']) == '') {
                    $idata['sn']['N'] ++;
                    ;
                } else {
                    $idata['sn']['Y'] ++;
                }

                //ksort($idata['statuses);
            }
        }
        $this->mysmarty->assign('idata', $idata);
    }

    function fix() {
        exit();
        $this->db->select('wid, bcn');
        $this->db->where('wid >=', 15344);
        $this->db->where('wid <=', 15526);
        $this->query = $this->db->get('warehouse');
        if ($this->query->num_rows() > 0) {
            $a = $this->query->result_array();
            foreach ($a as $v => $vv) {
                $this->db->update('warehouse', array('deleted' => 1), array('wid' => $vv['wid']));
            }
        }
    }

    /*
      function Accountingload($id = '')
      {
      if ((int)$id == 0) $id = $this->Mywarehouse_model->GetLastAuc();
      $list = $this->Mywarehouse_model->GetAccounting((int)$id);
      if ($list)
      {
      $this->session->set_userdata(array('accrel' => $list['headers'], 'acclot' => (int)$id));
      $echo = array();

      foreach ($list['data'] as $k => $l)
      {
      foreach($l as $kk => $ll)
      {
      $echo[$k][$kk] = $ll;
      }
      }
      echo json_encode($echo);
      }
      }
     */
    /*
      function Testingload($id = '')
      {
      if ((int)$id == 0) $id = $this->Mywarehouse_model->GetLastAuc();
      $list = $this->Mywarehouse_model->GetTesting((int)$id);
      if ($list)
      {
      $this->session->set_userdata(array('accrel' => $list['headers'], 'acclot' => (int)$id));
      $echo = array();
      $adms = $this->Mywarehouse_model->GetAdminList();
      foreach ($list['data'] as $k => $l)
      {
      foreach($l as $kk => $ll)
      {
      if ($kk == 'tech') $echo[$k][$kk] = $adms[$ll];
      //elseif ($kk == 'post') $echo[$k][$kk] = int2jsonval($ll);
      else $echo[$k][$kk] = $ll;
      }
      }
      echo json_encode($echo);
      }
      }
     */

    function bcnreturn($id = '') {
        if ($_POST) {
            if (isset($_POST['cust_return']))
                $item['cust_return'] = (int) $this->input->post('cust_return');
            if (isset($_POST['cust_status']))
                $item['cust_status'] = (int) $this->input->post('cust_status');
            if (isset($_POST['cust_reason']))
                $item['cust_reason'] = $this->input->post('cust_reason', TRUE);
            if (isset($_POST['cust_xtrcost']))
                $item['cust_xtrcost'] = $this->input->post('cust_xtrcost', TRUE);
            if (isset($_POST['vendor_reason']))
                $item['vendor_reason'] = $this->input->post('vendor_reason', TRUE);
            if (isset($_POST['vendor_return']))
                $item['vendor_return'] = (int) $this->input->post('vendor_return');

            if (isset($item))
                $this->db->update('warehouse', $item, array('wid' => (int) $id));
            $this->mysmarty->assign('saved', TRUE);
        }
        $ret = $this->Mywarehouse_model->GetReturnData((int) $id);
        if (!$ret)
            exit('No item found');
        $this->mysmarty->assign('item', $ret);

        $this->mysmarty->view('mywarehouse/return.html');
    }

    function label($label = '', $focus = '') {
        $this->mysmarty->assign('list', $this->Mywarehouse_model->GetLabel((int) $id));
        $this->mysmarty->assign('id', (int) $id);
        $this->mysmarty->assign('focus', (int) $focus);
        $this->mysmarty->assign('admins', $this->Mywarehouse_model->GetAdminList());
        $this->mysmarty->view('mywarehouse/label.html');
    }

    function bcns($page = 1) {
        $load = $this->Mywarehouse_model->GetBCNs((int) $page);
        $this->mysmarty->assign('list', $load['results']);
        $this->mysmarty->assign('pages', $load['pages']);
        $this->mysmarty->assign('page', (int) $page);
        $this->mysmarty->assign('admins', $this->Mywarehouse_model->GetAdminList());
        $this->mysmarty->view('mywarehouse/bcns.html');
    }

    function BulkStockSaveLastBCN() {
        if (isset($_POST) && $_POST) {
            $colMap = array(
                0 => 'bcn',
                1 => 'aucid',
                2 => 'mfgpart',
                3 => 'mfgname',
                4 => 'sku',
                5 => 'sn',
                6 => 'title',
                7 => 'location'
            );

            $bcolMap = array(
                0 => 'BCN',
                1 => 'AucID',
                2 => 'MFG Part',
                3 => 'MFG Name',
                4 => 'SKU',
                5 => 'SN',
                6 => 'Title',
                7 => 'Location'
            );
            $out = '';
            $sout = '';
            $sessback = $this->_loadsession($this->session->userdata('sessfile'));
            $saveid = $sessback['acclot'];
            $saverel = $sessback['accrel'];
            //printcool ($_POST);
            foreach ($_POST as $d) {
                foreach ($d as $dd) {
                    //$dd[0] ROW
                    //$dd[1] COL
                    //$dd[2] FROM VAL
                    //$dd[3] TO VAL
                    //printcool ($dd);
                    //printcool ($list[(int)$dd[0]][$colMap[$dd[1]]]);
                    //$saverel[(int)$dd[0]]['wid']
                    if ($dd[2] != $dd[3]) {
                        $this->Auth_model->wlog($saverel[(int) $dd[0]]['bcn'], (int) $saverel[(int) $dd[0]]['wid'], $colMap[$dd[1]], $dd[2], $dd[3]);

                        $out .= ' "' . $bcolMap[$dd[1]] . '" for BCN ' . $saverel[(int) $dd[0]]['bcn'] . ' Changed from "' . $dd[2] . '" to "' . $dd[3] . '" ';
                        $sout .= $saverel[(int) $dd[0]]['bcn'] . '/"' . $bcolMap[$dd[1]] . '" Changed ';

                        $this->db->update('warehouse', array($colMap[$dd[1]] => $dd[3]), array('wid' => (int) $saverel[(int) $dd[0]]['wid']));
                        $out = array('msg' => $out, 'smsg' => $sout, 'row' => $dd[0], 'col' => $dd['1']);
                    }
                }
            }
            echo json_encode($out);
        }
    }

    function MassUpdater($complete = false) {

        if ($complete) {
            //printcool ($this->session->userdata('formdata'));
            if (isset($_POST['update']) && $_POST['update']) {
                $poststatus = trim($this->input->post('status', TRUE));
                $postlocation = trim($this->input->post('location', TRUE));
                $update = array();
                if ($poststatus != '')
                    $update['status'] = $poststatus;
                if ($postlocation != '')
                    $update['location'] = $postlocation;
                if (count($update) > 0) {
                    $updated = array();
                    foreach ($_POST['update'] as $k => $v) {
                        $old = $this->Mywarehouse_model->GetField('status, location, status_notes', (int) $v, TRUE);
                        if ($postlocation != '')
                            $this->Auth_model->wlog($k, (int) $v, 'location', $old['location'], $update['location']);
                        if ($poststatus != '') {
                            $this->Auth_model->wlog($k, (int) $v, 'status', $old['status'], $update['status']);
                            $update['status_notes'] = $old['status_note'] . ' | Changed from: ' . $old['status'];
                        }
                        $this->db->update('warehouse', $update, array('wid' => (int) $v));
                        //printcool ($update);
                        $updated[$k] = $v;
                    }
                    $this->mysmarty->assign('updated', $updated);
                }
                $this->mysmarty->assign('poststatus', $poststatus);
                $this->mysmarty->assign('postlocation', $postlocation);
            }

            $this->mysmarty->assign('fromlot', $this->session->userdata('fromlot'));
            $this->mysmarty->assign('result', $this->session->userdata('formdata'));
            $this->mysmarty->assign('location', $this->session->userdata('location'));
            $this->mysmarty->assign('status', $this->session->userdata('status'));
            $this->mysmarty->assign('rows', count($this->session->userdata('formdata')) + 1);
            $this->mysmarty->assign('statuses', $this->statuses['allarray']);
            $this->mysmarty->view('mywarehouse/MassUpdater.html');
        } else {
            if (isset($_POST['data']) && $_POST['data']) {
                $fromlot = array();
                $this->load->helper('security');
                $sql = 'SELECT wid, bcn, oldbcn, lot, location, status FROM warehouse WHERE ';
                $c = 1;
                foreach ($_POST['data'] as $k => $v) {
                    foreach ($v as $kk => $vv) {
                        if (trim($vv) != '') {
                            if ($c == 1)
                                $sql .= '(bcn = "' . addslashes(xss_clean(trim($vv))) . '" OR lot = "' . addslashes(xss_clean(trim($vv))) . '" OR oldbcn = "' . addslashes(xss_clean(trim($vv))) . '") ';
                            else
                                $sql .= 'OR (bcn = "' . addslashes(xss_clean(trim($vv))) . '" OR lot = "' . addslashes(xss_clean(trim($vv))) . '" OR oldbcn = "' . addslashes(xss_clean(trim($vv))) . '") ';
                            $c++;
                            $data[addslashes(xss_clean(trim(strtolower($vv))))] = false;
                        }
                    }
                }
                $w = $this->db->query($sql);
                if ($w->num_rows() > 0) {
                    foreach ($w->result_array() as $wv) {
                        if (isset($data[addslashes(xss_clean(trim(strtolower($wv['lot']))))])) {
                            unset($data[addslashes(xss_clean(trim(strtolower($wv['lot']))))]);
                            $fromlot[$wv['bcn']] = $wv['lot'];
                        }
                        if (isset($data[addslashes(xss_clean(trim(strtolower($wv['oldbcn']))))])) {
                            unset($data[addslashes(xss_clean(trim(strtolower($wv['oldbcn']))))]);
                            $fromlot[$wv['bcn']] = $wv['oldbcn'];
                        }
                        $data[$wv['bcn']] = $wv['wid'];
                        $status[$wv['wid']] = $wv['status'];
                        $location[$wv['wid']] = $wv['location'];
                    }
                }
                $this->session->set_userdata('formdata', $data);
                $this->session->set_userdata('fromlot', $fromlot);
                $this->session->set_userdata('location', $location);
                $this->session->set_userdata('status', $status);
            } else {
                $this->session->unset_userdata('formdata');
                $this->session->unset_userdata('fromlot');
                $this->mysmarty->assign('result', false);
                $this->mysmarty->view('mywarehouse/MassUpdater.html');
            }
        }
    }

    function BulkStockRecieve() {
        $this->session->unset_userdata('formdata');
        // STAGE 1
        $this->load->library('form_validation');

        foreach ($this->warehousefields as $k => $v) {
            if ($v[3] == 1)
                $this->form_validation->set_rules($v[0], $v[1], 'trim|' . $v[2] . 'xss_clean');
        }
        $this->form_validation->set_rules('quantity', 'Quantity', 'trim|is_natural_no_zero|xss_clean');

        if ($this->form_validation->run() == FALSE) {
            foreach ($this->warehousefields as $k => $v)
                $input[$v[0]] = $this->input->post($v[0], TRUE);

            if ($input['bcn'] == '') {
                $bcnp1 = date("m") . substr(date("y"), 1, 1);
                $bcnp2 = sprintf('%04u', $this->Mywarehouse_model->GetNextBcn((int) $bcnp1));
                $input['bcn'] = $bcnp1 . '-' . $bcnp2;
            }
            $this->mysmarty->assign('input', $input);
            $this->mysmarty->assign('fields', $this->warehousefields);

            $load = $this->Mywarehouse_model->GetBCNs(1);
            if ($load) {
                $sesfile = $this->_savesession(array('accrel' => $load['headers'], 'acclot' => 0));
                $this->session->set_userdata(array('sessfile' => $sesfile));

                $loaddata = '';
                $adms = $this->Mywarehouse_model->GetAdminList();
                foreach ($load['results'] as $k => $l) {
                    $loaddata .= "['" . cstr($l['bcn']) . "', '" . cstr($l['aucid']) . "', '" . cstr($l['mfgpart']) . "', '" . cstr($l['mfgname']) . "', '" . cstr($l['sku']) . "', '" . cstr($l['sn']) . "', '" . cstr($l['title']) . "', '" . cstr($l['location']) . "'],
					";
                }
            }
            $fielset = array(
                'headers' => "'BCN', 'AucID', 'MFG Part', 'MFG Name', 'SKU' , 'SN', 'Title', 'Location'",
                /* 'rowheaders' => $list['headers'], */
                'width' => "80, 100, 180, 125, 100, 115, 250, 160",
                'startcols' => 8,
                'startrows' => 10,
                'autosaveurl' => "/Mywarehouse/BulkStockSaveLastBCN/",
                'colmap' => "{readOnly: true},{readOnly: true},{},{},{},{},{},{}"
            );

            $this->mysmarty->assign('headers', $fielset['headers']);
            $this->mysmarty->assign('rowheaders', $fielset['rowheaders']);
            $this->mysmarty->assign('width', $fielset['width']);
            $this->mysmarty->assign('startcols', $fielset['startcols']);
            $this->mysmarty->assign('startrows', $fielset['startrows']);
            $this->mysmarty->assign('autosaveurl', $fielset['autosaveurl']);
            $this->mysmarty->assign('colmap', $fielset['colmap']);
            $this->mysmarty->assign('loaddata', rtrim($loaddata, ','));
            $this->mysmarty->assign('copyrows', count($list['data']));
            $this->mysmarty->assign('bcns', $load['results']);

            $this->mysmarty->assign('quantity', ((int) $this->input->post('quantity') > 0 ? (int) $this->input->post('quantity') : 1));
            $this->mysmarty->assign('errors', $this->form_validation->_error_array);
            $this->mysmarty->assign('last', $this->Mywarehouse_model->GetLast());
            $this->mysmarty->view('mywarehouse/BulkStockRecieve.html');
        } else {

            foreach ($this->warehousefields as $k => $v)
                if ($v[3] == 1)
                    $db[$v[0]] = $this->form_validation->set_value($v[0]);
            $bcnparts = explode('-', $db['bcn']);
            $db['bcn_p1'] = $bcnparts[0];
            $db['bcn_p2'] = $bcnparts[1];
            //$db['bcn_p1'] = substr($db['bcn'], 0, 3);
            //$db['bcn_p2'] = substr($db['bcn'], 4, 4);
            $db['dates'] = serialize(array(array('created' => CurrentTime(), 'createdstamp' => mktime())));
            $db['adminid'] = (int) $this->session->userdata['admin_id'];
            $db['insid'] = (int) $this->Mywarehouse_model->GetNextInsertOrder();
            for ($i = $db['bcn_p2']; $i < ($bcnparts[1] + (int) $this->input->post('quantity')); $i++) {
                if ($i > 1) {
                    $db['bcn_p2'] = sprintf('%04u', $i);
                    $db['bcn'] = $bcnparts[0] . '-' . $db['bcn_p2'];
                }
                $ins[$i] = $this->Mywarehouse_model->Insert($db);
            }
            //$this->session->set_flashdata('success_msg', '"'.$db['title'].'" Inserted as record '.$ins);
            //$this->session->set_flashdata('action', (int)$ins);			
            Redirect('Mywarehouse/BulkStockRecieve#' . (int) $ins);
        }
    }

    function BulkItemRecieve() {
        if (!isset($_POST['bulk']))
            exit('No data submitted');
        $thedata = explode(PHP_EOL, trim($this->input->post('bulk')));

        foreach ($thedata as $k => $v) {
            $v = trim($v);
            if ($v == '')
                unset($thedata[$k]);
            else {
                if (isset($_POST['bcns']))
                    $parseddata[$k]['data'] = explode("\t", $v);
                else
                    $parseddata[$k]['data'] = explode(',', $v);
                foreach ($parseddata[$k]['data'] as $fk => $fv)
                    $parseddata[$k]['data'][$fk] = trim($fv);
                $parseddata[$k]['fieldcount'] = count($parseddata[$k]['data']);
            }
        }
        if (isset($parseddata)) {
            if (!isset($_POST['confirm'])) {
                $this->mysmarty->assign('textareastring', trim($this->input->post('bulk')));
                if (isset($_POST['bcns'])) {
                    $this->mysmarty->assign('bcnview', TRUE);
                }
                $this->mysmarty->assign('parseddata', $parseddata);
                $this->mysmarty->view('mywarehouse/BulkItemRecieve.html');
            } else {
                $db['bcn_p1'] = date("m") . substr(date("y"), 1, 1);
                $db['insid'] = (int) $this->Mywarehouse_model->GetNextInsertOrder();
                $db['adminid'] = (int) $this->session->userdata['admin_id'];

                $bcnp2 = sprintf('%04u', $this->Mywarehouse_model->GetNextBcn((int) $db['bcn_p1']));

                foreach ($parseddata as $k => $v) {
                    //if ((int)$v['data'][0] > 1000) exit('More than 1000 quantity found. Please do not crash the server with such a value!');
                    for ($i = $bcnp2; $i < ($bcnp2 + (int) $v['data'][0]); $i++) {
                        $db['mfgpart'] = $v['data'][1];
                        $db['aucid'] = $v['data'][2];
                        $db['bcn_p2'] = sprintf('%04u', $i);
                        $db['bcn'] = $db['bcn_p1'] . '-' . $db['bcn_p2'];

                        $ins[$i] = $this->Mywarehouse_model->Insert($db);
                    }
                    $bcnp2 = $i;
                }
                Redirect('Mywarehouse/BulkStockRecieve#' . (int) $ins);
            }
        }
    }

    function Ghost() {
        $this->Accounting(0);
    }

    function RecieveReport($id = '') {
        if ((int) $id == 0)
            $id = $this->Mywarehouse_model->GetLastAuc();
        $list = $this->Mywarehouse_model->GetPacks((int) $id);

        if (isset($_POST) && $_POST) {
            $colMap = array(
                0 => 'bcn',
                1 => 'oldbcn',
                2 => 'mfgpart',
                3 => 'mfgname',
                4 => 'psku',
                5 => 'sku',
                6 => 'sn',
                7 => 'title',
                8 => 'nr',
                9 => 'location',
                10 => 'notes',
                11 => 'adminid',
                12 => 'dates'
            );

            $bcolMap = array(
                0 => 'BCN',
                1 => 'Old BCN',
                2 => 'MFG Part',
                3 => 'MFG Name',
                4 => 'PSKU',
                5 => 'SKU',
                6 => 'SN',
                7 => 'Title',
                8 => 'Not Recieved',
                9 => 'Location',
                10 => 'Notes',
                11 => 'Admin',
                12 => 'Dates'
            );
            $out = '';
            $sout = '';
            $sessback = $this->_loadsession($this->session->userdata('sessfile'));
            $saveid = $sessback['acclot'];
            $saverel = $sessback['accrel'];

            //printcool ($_POST);
            foreach ($_POST as $d) {
                foreach ($d as $dd) {
                    //$dd[0] ROW
                    //$dd[1] COL
                    //$dd[2] FROM VAL
                    //$dd[3] TO VAL
                    //printcool ($dd);
                    //printcool ($list[(int)$dd[0]][$colMap[$dd[1]]]);
                    //$saverel[(int)$dd[0]]['wid']
                    if ($dd[2] != $dd[3]) {
                        $this->Auth_model->wlog($saverel[(int) $dd[0]]['bcn'], $saverel[(int) $dd[0]]['wid'], $colMap[$dd[1]], $dd[2], $dd[3]);

                        $out .= ' "' . $bcolMap[$dd[1]] . '" for BCN ' . $saverel[(int) $dd[0]]['bcn'] . ' Changed from "' . $dd[2] . '" to "' . $dd[3] . '" ';
                        $sout .= $saverel[(int) $dd[0]]['bcn'] . '/"' . $bcolMap[$dd[1]] . '" Changed ';

                        $updt = array($colMap[$dd[1]] => $dd[3]);
                        if ($colMap[$dd[1]] == 'nr') {
                            $updt['status_notes'] = $this->Mywarehouse_model->GetStatusNotes($saverel[(int) $dd[0]]['wid']) . ' | Changed from: ' . $dd[2];
                            $updt['status'] = 'Not Recieved';
                        }
                        $this->db->update('warehouse', $updt, array('wid' => $saverel[(int) $dd[0]]['wid']));

                        $out = array('msg' => $out, 'smsg' => $sout, 'row' => $dd[0], 'col' => $dd['1']);
                    }
                }
            }
            echo json_encode($out);
        } else {

            if ($list) {
                $sesfile = $this->_savesession(array('accrel' => $list['headers'], 'acclot' => (int) $id));
                $this->session->set_userdata(array('sessfile' => $sesfile));
                $loaddata = '';
                $adms = $this->Mywarehouse_model->GetAdminList();
                foreach ($list['data'] as $k => $l) {
                    $l['ndates'] = '';
                    if (is_array($l['dates']) && count($l['dates']) > 0)
                        foreach ($l['dates'] as $k => $v) {
                            if (is_array($v))
                                foreach ($v as $kk => $vv) {
                                    if ($kk != 'createdstamp')
                                        $l['ndates'] .= ucwords($kk) . ': ' . $vv . ' |';
                                }
                        }
                    $loaddata .= "['" . cstr($l['bcn']) . "', '" . cstr($l['oldbcn']) . "', '" . cstr($l['mfgpart']) . "', '" . cstr($l['mfgname']) . "', '" . cstr($l['psku']) . "', '" . cstr($l['sku']) . "', '" . cstr($l['sn']) . "', '" . cstr($l['title']) . "', '" . cstr($l['nr']) . "', '" . cstr($l['location']) . "', '" . cstr($l['notes']) . "', '" . $adms[$l['adminid']] . "', '" . cstr($l['ndates']) . "'],
				";
                }
            }

            //printcool ($list['headers']);
            $this->mysmarty->assign('list', $list['data']);
            $this->mysmarty->assign('id', (int) $id);
            $this->mysmarty->assign('atitle', $this->Mywarehouse_model->AuctionIdToName((int) $id));
            //$this->mysmarty->assign('focus', (int)$focus);
            //$this->mysmarty->assign('admins', $this->Mywarehouse_model->GetAdminList());


            /*
              bcn
              mfgpart
              mfgname
              psku
              sku
              sn
              title
              location
              notes
              adminid
              dates
             */
            $fielset = array(
                'headers' => "'BCN', 'Old BCN', 'MFG Part', 'MFG Name','PKU' , 'SKU' , 'SN', 'Title', 'Not Rec.', 'Location', 'Notes', 'Admin', 'Dates'",
                /* 'rowheaders' => $list['headers'], */
                'width' => "80, 100, 125, 125, 100, 100, 115, 180, 80 ,120, 180, 110, 165",
                'startcols' => count($list['data']),
                'startrows' => 10,
                'autosaveurl' => "/Mywarehouse/RecieveReport/" . (int) $id,
                'colmap' => "{readOnly: true},{},{},{},{},{},{},{},{type: 'checkbox', checkedTemplate: 1, uncheckedTemplate: 0},{},{},{readOnly: true},{readOnly: true}"
            );

            $this->mysmarty->assign('headers', $fielset['headers']);
            $this->mysmarty->assign('rowheaders', $fielset['rowheaders']);
            $this->mysmarty->assign('width', $fielset['width']);
            $this->mysmarty->assign('startcols', $fielset['startcols']);
            $this->mysmarty->assign('startrows', $fielset['startrows']);
            $this->mysmarty->assign('autosaveurl', $fielset['autosaveurl']);
            $this->mysmarty->assign('colmap', $fielset['colmap']);
            $this->mysmarty->assign('loaddata', rtrim($loaddata, ','));
            $this->mysmarty->assign('copyrows', count($list['data']));
            $this->mysmarty->view('mywarehouse/RecieveReport.html');
        }
    }

    function gotobcn($bcn = '') {
        if (trim($bcn) != '') {

            Redirect('Mywarehouse/bcndetails/' . $this->Mywarehouse_model->bcn2wid(trim($bcn)));
        }
        //$this->Finder();	
    }

    function savelistingid() {
        if (isset($_POST['soldid']) && isset($_POST['listingid'])) {
            $this->db->update('ebay_transactions', array('e_id' => (int) $_POST['listingid']), array('et_id' => (int) $_POST['soldid']));
            echo 1;
        }
    }

    function selectionsearch($editor = false, $sales = '') {
        if (!isset($_POST['id'])) {
            if (isset($_POST['data']) && count($_POST['data']) > 0) {
                foreach ($_POST['data'] as $p)
                    $from[] = trim($p[0]);
                $to = false;
                $id = (int) $editor;
                if (trim($sales) != '') {
                    $sales = trim($sales);
                    $this->mysmarty->assign('sales', (int) $sales);
                    $this->mysmarty->assign('subid', 0);
                } else
                    $sales = '';
            } else
                exit('Bad Data');
        } else {
            $from = trim($this->input->post('from'));
            $to = trim($this->input->post('to'));
            $id = trim($this->input->post('id'));
            if (trim($_POST['sales']) != '') {
                $sales = trim($this->input->post('sales'));
                $this->mysmarty->assign('sales', (int) $sales);
                $this->mysmarty->assign('subid', 0);
            } else
                $sales = '';
        }

        $res = $this->Mywarehouse_model->GetSelection($from, $to, $id, $sales);

        $this->mysmarty->assign('id', (int) $id);
        if ($res)
            $this->mysmarty->assign('selection', $res);

        $this->Myseller_model->assignchannels();
        if (!isset($_POST['id']))
            echo json_encode($this->mysmarty->fetch('myseller/availbcn.html'));
        else
            echo $this->mysmarty->fetch('myseller/availbcn.html');
    }

    function MakeAllStatus() {
        if (isset($_POST['listingid']) && isset($_POST['status'])) {
            if (in_array(trim($_POST['status']), $this->statuses['listingarray'])) {
                $dbdata = $this->Myseller_model->getBase(array((int) $_POST['listingid']), true);
                foreach ($dbdata as $wid) {
                    if (trim($_POST['status']) != $wid['status']) {
                        $data['status'] = trim($_POST['status']);

                        $statusnotes = 'Changed from "' . $wid['status'] . '" - MakeAllStatus by ' . $this->session->userdata['ownnames'];
                        if (trim($wid['status_notes']) == '')
                            $data['status_notes'] = $statusnotes;
                        else
                            $data['status_notes'] = $statusnotes . ' | ' . $wid['status_notes'];

                        $this->db->update('warehouse', $data, array('wid' => (int) $wid['wid']));

                        $this->Auth_model->wlog($wid['bcn'], $wid['wid'], 'status', $wid['status'], $data['status']);
                    }
                }
                echo $this->_getbcnsnippet((int) $_POST['listingid'], false, 'listing');
            } else
                echo 'Bad Data';
        }
    }

    function SetShipped() {
        if (isset($_POST['soldid']) && isset($_POST['channel'])) {
            $wids = $this->Myseller_model->getSales(array((int) $_POST['soldid']), $_POST['channel'], true, true);

            if ($wids) {
                foreach ($wids as $wid) {
                    $data['status'] = 'Sold';
                    $data['location'] = 'Sold';
                    $data['vended'] = 1;
                    $data['shipped_date'] = CurrentTime();
                    $statusnotes = 'Changed from "' . $wid['status'] . '" - SetShipped by ' . $this->session->userdata['ownnames'];
                    if (trim($wid['status_notes']) == '')
                        $data['status_notes'] = $statusnotes;
                    else
                        $data['status_notes'] = $statusnotes . ' | ' . $wid['status_notes'];

                    //printcool ($data, '', 'update');
                    $this->db->update('warehouse', $data, array('wid' => (int) $wid['wid']));
                    //LOG CHANGES
                    foreach ($data as $k => $v) {//printcool ($v); printcool ($wid[$k]);
                        if ($v != $wid[$k])
                            $this->Auth_model->wlog($wid['bcn'], $wid['wid'], $k, $wid[$k], $v);
                    }
                    $listingid = $wid['listingid'];
                    $subid = $wid['sold_subid'];
                }


                if ($_POST['channel'] == 2) {
                    $this->db->select('admin, revs, order');
                    $this->db->where('oid', (int) $_POST['soldid']);
                    $q = $this->db->get('orders');
                    if ($q->num_rows() > 0) {
                        $res = $q->row_array();
                        $res['revs'] ++;
                        if ($res['admin'] == '')
                            $res['admin'] = '(' . $res['revs'] . ') ' . $this->session->userdata['ownnames'];
                        else
                            $res['admin'] = '(' . $res['revs'] . ') ' . $this->session->userdata['ownnames'] . ', ' . $res['admin'];

                        $res['order'] = unserialize($res['order']);
                        if (is_array($res['order']))
                            foreach ($res['order'] as $k => $ov) {
                                if ($k == $subid)
                                    $qty = $ov['quantity'];
                            }

                        $this->db->update('orders', array('mark' => 1, 'admin' => $res['admin'], 'revs' => $res['revs']), array('oid' => (int) $_POST['soldid']));
                    }
                } elseif ($_POST['channel'] == 4) {
                    $this->db->select('admin, revs');
                    $this->db->where('woid', (int) $_POST['soldid']);
                    $q = $this->db->get('warehouse_orders');
                    if ($q->num_rows() > 0) {
                        $res = $q->row_array();
                        $res['revs'] ++;
                        if ($res['admin'] == '')
                            $res['admin'] = '(' . $res['revs'] . ') ' . $this->session->userdata['ownnames'];
                        else
                            $res['admin'] = '(' . $res['revs'] . ') ' . $this->session->userdata['ownnames'] . ', ' . $res['admin'];
                        $this->db->update('warehouse_orders', array('mark' => 1, 'admin' => $res['admin'], 'revs' => $res['revs']), array('woid' => (int) $_POST['soldid']));
                    }
                } else {
                    $this->db->select('admin, revs, qty');
                    $this->db->where('et_id', (int) $_POST['soldid']);
                    $q = $this->db->get('ebay_transactions');
                    if ($q->num_rows() > 0) {
                        $res = $q->row_array();
                        $res['revs'] ++;
                        $qty = $res['qty'];
                        if ($res['admin'] == '')
                            $res['admin'] = '(' . $res['revs'] . ') ' . $this->session->userdata['ownnames'];
                        else
                            $res['admin'] = '(' . $res['revs'] . ') ' . $this->session->userdata['ownnames'] . ', ' . $res['admin'];
                        $this->db->update('ebay_transactions', array('mark' => 1, 'admin' => $res['admin'], 'revs' => $res['revs']), array('et_id' => (int) $_POST['soldid']));
                    }
                }

                $this->Myseller_model->getSalesListings(array(0 => true, $listingid => true));
                $this->Myseller_model->getSales(array((int) $_POST['soldid']), (int) $_POST['channel']);

                $this->mysmarty->assign('sales', (int) $_POST['channel']);
                $this->mysmarty->assign('eid', $listingid);
                $this->mysmarty->assign('id', (int) $_POST['soldid']);
                if ((int) $_POST['channel'] == 2)
                    $this->mysmarty->assign('subid', (int) $subid);
                $this->mysmarty->assign('quantity', $qty);
                $this->mysmarty->assign('mark', TRUE);
                $this->mysmarty->assign('updatetime', CurrentTimeR());
                echo $this->mysmarty->fetch('myseller/bcnarea.html');
            }
        } else
            echo 'Bad Data';
    }

    function savechanneldata() {
        if (isset($_POST['listingid']) && isset($_POST['channel']) && isset($_POST['val']) && isset($_POST['datatype'])) {
            if (trim($_POST['datatype']) == 'price') {
                $datatype = 'price_ch';
                $val = (float) $_POST['val'];
            } else {
                $datatype = 'qn_ch';
                $val = (int) $_POST['val'];
                $qn = true;
            }

            if ((int) $_POST['channel'] > 3)
                $channel = 3;
            else
                $channel = (int) $_POST['channel'];
            if (isset($qn)) {
                $bcncount = $this->Myseller_model->getSalesListings(array(0 => true, (int) $_POST['listingid'] => true), false, true);
                if ($val > $bcncount) {
                    $diff = $val - $bcncount;
                    $nogo = true;
                }
            }

            $field = $datatype . $channel;
            if (!isset($nogo))
                $this->db->update('ebay', array($field => $val), array('e_id' => (int) $_POST['listingid']));


            if (isset($diff))
                echo $diff; //$this->ListingGhostGen(array('listingid' => (int)$_POST['listingid'], 'qn' => $diff));
            else {
                echo 0;
                if ($channel == 2) {
                    $etype = 'p';
                    if (isset($qn))
                        $etype = 'q';
                    $this->Myseller_model->que_rev((int) $_POST['listingid'], $etype, $val);
                }
            }
        }
    }

    function commit() {
        $db = array('admin_history', 'ebay', 'ebay_actionlog', 'ebay_transactions', 'orders', 'warehouse', 'warehouse_auctions', 'warehouse_log');
        $run = false;
        $allpaths = array(
            '../system_dev/application' => '../system_la/application',
            '../dev/js' => '../public_html/js',
            '../dev/css' => '../public_html/css'
        );

        $donot[] = '/config/config.php';
        $donot[] = '/config/database.php';
        $donot[] = '/libraries/ebay/shipping.txt';
        $donot[] = '/libraries/ebay/trans.txt';
        $donot[] = '/views/header.html';
        $donot[] = '/controllers/show.php';

        foreach ($allpaths as $ak => $av) {
            $devpaths = $this->_getFileList($ak, true);
            $livepaths = $this->_getFileList($av, true);


            foreach ($devpaths as $v)
                $devkeypaths[str_replace($ak, '', $v['name'])] = $v;
            foreach ($livepaths as $v) {
                if (isset($devkeypaths[str_replace($av, '', $v['name'])]) && $v['size'] != $devkeypaths[str_replace($av, '', $v['name'])]['size']) {
                    if (!in_array(str_replace($av, '', $v['name']), $donot)) {
                        if ($run) {

                            if (copy($devkeypaths[str_replace($av, '', $v['name'])]['name'], $v['name'])) {
                                echo "Copied $file...<br>\n";
                            }
                        } else
                            printcool($v['name']);
                    }
                }
            }
        }
    }

    function _getFileList($dir, $recurse = false) {
        $retval = array();

        // add trailing slash if missing
        if (substr($dir, -1) != "/")
            $dir .= "/";

        // open pointer to directory and read list of files
        $d = @dir($dir) or die("getFileList: Failed opening directory $dir for reading");
        while (false !== ($entry = $d->read())) {
            // skip hidden files
            if ($entry[0] == ".")
                continue;
            if (is_dir("$dir$entry")) {
                $retval[] = array(
                    "name" => "$dir$entry/",
                    "type" => filetype("$dir$entry"),
                    "size" => 0,
                    "lastmod" => filemtime("$dir$entry")
                );
                if ($recurse && is_readable("$dir$entry/")) {
                    $retval = array_merge($retval, $this->_getFileList("$dir$entry/", true));
                }
            } elseif (is_readable("$dir$entry")) {
                $retval[] = array(
                    "name" => "$dir$entry",
                    "type" => mime_content_type("$dir$entry"),
                    "size" => filesize("$dir$entry"),
                    "lastmod" => filemtime("$dir$entry")
                );
            }
        }
        $d->close();

        return $retval;
    }

    function BCNListingAttach() {
        if (isset($_POST['wid']) && isset($_POST['listingid']) && isset($_POST['remove'])) {
            $wid = $this->Mywarehouse_model->getbcnattachdata((int) $_POST['wid']);
            $title = $this->Mywarehouse_model->GetListingTitleAndCondition((int) $_POST['listingid'], true);
            if ($wid) {

                if ((int) $_POST['remove'] == 1) {
                    $data['listingid'] = 0;
                    $data['status'] = 'Not Listed';

                    $actionqn = 1;
                } else {
                    $data['listingid'] = (int) $_POST['listingid'];
                    $data['status'] = 'Listed';
                    $data['title'] = $title;

                    $actionqn = -1;
                }
                if ($title != $wid['title']) {
                    $this->Auth_model->wlog($wid['bcn'], $wid['wid'], 'title', $wid['title'], $title);
                }
                $statusnotes = 'Changed from "' . $wid['status'] . '" - Listing ' . $data['listingid'] . ' by ' . $this->session->userdata['ownnames'];
                if (trim($wid['status_notes']) == '')
                    $data['status_notes'] = $statusnotes;
                else
                    $data['status_notes'] = $statusnotes . ' | ' . $wid['status_notes'];

                $this->db->update('warehouse', $data, array('wid' => (int) $_POST['wid']));


                $this->Myseller_model->runAssigner((int) $_POST['listingid'], $actionqn);

                echo $this->_getbcnsnippet((int) $_POST['listingid'], false, 'listing');
            } else
                echo 0;
        } else
            echo 0;
    }

    function test0() {

        $this->Myseller_model->test1();
    }

    function test1() {

        printcool($this->mac);
    }

    function BCNSalesAttach() {
        if (isset($_POST['wid']) && isset($_POST['soldid']) && isset($_POST['subid']) && isset($_POST['channel']) && isset($_POST['remove'])) {
            $wid = $this->Mywarehouse_model->getbcnattachdata((int) $_POST['wid']);
            $data = $this->Mywarehouse_model->getsaleattachdata((int) $_POST['channel'], (int) $_POST['soldid'], $wid['listingid'], (int) $_POST['remove']);
            if ((int) $_POST['remove'] == 0 && ((int) $_POST['quantity'] == $data['qty'] && $data['qty'] >= 0)) {
                echo 2;
                exit();
            }


            //printcool ($wid, '', 'getbcndata');
            if ($wid) {
                //printcool ($data, '', 'getsalesdata');
                $qty = $data['qty'];
                $mark = $data['mark'];
                unset($data['qty']);
                unset($data['mark']);
                $data['channel'] = (int) $_POST['channel'];
                if ((int) $_POST['remove'] == 1) {
                    $data['sold_date'] = '';
                    $data['paid'] = 0;
                    $data['shipped'] = 0;
                    $data['ordernotes'] = '';
                    $data['sellingfee'] = 0;
                    $data['location'] = '';

                    $data['sold_id'] = 0;
                    $data['sold_subid'] = 0;
                    $data['status'] = 'Listed';
                    $data['sold'] = '';
                    $data['vended'] = 0;
                    $actionqn = -1;
                } else {
                    $data['sold_id'] = (int) $_POST['soldid'];
                    $data['sold_subid'] = (int) $_POST['subid'];
                    if ($mark == 1) {
                        $data['status'] = 'Sold';
                        $data['location'] = 'Sold';
                        $data['vended'] = 1;
                    } else {
                        $data['status'] = 'On Hold';
                        //$data['location'] = 'On Hold';
                        $data['vended'] = 2;
                    }
                    $actionqn = 1;
                    if ((int) $_POST['channel'] == 1)
                        $data['sold'] = 'eBay';
                    elseif ((int) $_POST['channel'] == 2)
                        $data['sold'] = 'WebSite';
                    elseif ((int) $_POST['channel'] == 4)
                        $data['sold'] = 'Warehouse';
                }
                //MARK COMPLETE - vended = 1;
                if ($channel == 2)
                    $sdata = 'WebSite Sale ' . (int) $_POST['soldid'] . '/' . (int) $_POST['subid'];
                elseif ($channel == 4)
                    $sdata = 'Warehouse Sale ' . (int) $_POST['soldid'];
                else
                    $sdata = 'eBay Sale ' . (int) $_POST['soldid'];

                $statusnotes = 'Changed from "' . $wid['status'] . '" - ' . $sdata . ' by ' . $this->session->userdata['ownnames'];
                if (trim($wid['status_notes']) == '')
                    $data['status_notes'] = $statusnotes;
                else
                    $data['status_notes'] = $statusnotes . ' | ' . $wid['status_notes'];

                //printcool ($data, '', 'update');
                $this->db->update('warehouse', $data, array('wid' => (int) $_POST['wid']));
                //LOG CHANGES
                foreach ($data as $k => $v) {//printcool ($v); printcool ($wid[$k]);
                    if ($v != $wid[$k])
                        $this->Auth_model->wlog($wid['bcn'], $wid['wid'], $k, $wid[$k], $v);
                }

                if ((int) $wid['listingid'] > 0)
                    $this->Myseller_model->runAssigner($wid['listingid'], $actionqn);

                $this->Myseller_model->getSalesListings(array(0 => true, $wid['listingid'] => true));
                $this->Myseller_model->getSales(array((int) $_POST['soldid']), (int) $_POST['channel']);

                $this->mysmarty->assign('sales', (int) $_POST['channel']);
                $this->mysmarty->assign('eid', $wid['listingid']);
                $this->mysmarty->assign('id', (int) $_POST['soldid']);
                if ((int) $_POST['channel'] == 2)
                    $this->mysmarty->assign('subid', (int) $_POST['subid']);
                $this->mysmarty->assign('quantity', $qty);
                $this->mysmarty->assign('updatetime', CurrentTimeR());
                echo $this->mysmarty->fetch('myseller/bcnarea.html');
            } else
                echo 0;
        } else
            echo 0;
    }

    function ChannelSave() {
        if (isset($_POST['wid']) && isset($_POST['listingid']) && isset($_POST['channel'])) {
            $wid = $this->Mywarehouse_model->getbcnattachdata((int) $_POST['wid']);
            if ($wid) {
                $data['channel'] = (int) $_POST['channel'];

                $this->db->update('warehouse', $data, array('wid' => (int) $_POST['wid']));

                $this->Auth_model->wlog($wid['bcn'], $wid['wid'], 'channel', $wid['channel'], $data['channel']);

                echo $this->_getbcnsnippet((int) $_POST['listingid'], false, 'listing');
            } else
                echo 0;
        } else
            echo 0;
    }

    function StatusSave() {
        if (isset($_POST['wid']) && isset($_POST['listingid']) && isset($_POST['status'])) {
            $wid = $this->Mywarehouse_model->getbcnattachdata((int) $_POST['wid']);
            if ($wid) {
                $data['status'] = trim($_POST['status']);

                $statusnotes = 'Changed from "' . $wid['status'] . '" - StatusSave by ' . $this->session->userdata['ownnames'];
                if (trim($wid['status_notes']) == '')
                    $data['status_notes'] = $statusnotes;
                else
                    $data['status_notes'] = $statusnotes . ' | ' . $wid['status_notes'];


                $this->db->update('warehouse', $data, array('wid' => (int) $_POST['wid']));

                $this->Auth_model->wlog($wid['bcn'], $wid['wid'], 'status', $wid['status'], $data['status']);

                echo $this->_getbcnsnippet((int) $_POST['listingid'], false, 'listing');
            } else
                echo 0;
        } else
            echo 0;
    }

    function UpdateGhost($wid = 0) {
        if (isset($_POST['val']) && trim($_POST['val']) != '') {

            $wid = (int) $wid;
            $val = htmlspecialchars(trim($this->input->post('val', true)));
            $sales = (int) $this->input->post('sales', true);
            $parentwid = $this->Mywarehouse_model->getbcnattachdata($wid);
            $this->db->where('bcn', trim($val));
            $this->db->or_where('lot', trim($val));
            $this->db->or_where('oldbcn', trim($val));
            $q = $this->db->get('warehouse');
            if ($q->num_rows() > 0) {
                $matchedwid = $q->row_array();
                if ($matchedwid['sold_id'] > 0 || ($matchedwid['status'] == "Sold" || $matchedwid['status'] == "On Hold"))
                    echo json_encode(array('msg' => 'Assigned to a sale', 'val' => $parentwid['bcn']));
                elseif ($parentwid && $matchedwid['listingid'] == $parentwid['listingid'])
                    echo json_encode(array('msg' => 'Assigned to same listing', 'val' => $parentwid['bcn']));
                elseif ($parentwid && $matchedwid['listingid'] > 0)
                    echo json_encode(array('msg' => 'Assigned to another listing', 'val' => $parentwid['bcn']));
                else {

                    if ($sales == 0) {
                        $this->db->update('warehouse', array('listingid' => $parentwid['listingid']), array('wid' => $matchedwid['wid']));
                        $this->Auth_model->wlog($matchedwid['bcn'], $matchedwid['wid'], 'listingid', $matchedwid['listingid'], $parentwid['listingid']);
                        $this->db->update('warehouse', array('listingid' => 0, 'deleted' => 1), array('wid' => $parentwid['wid']));
                        $this->Auth_model->wlog($parentwid['bcn'], $parentwid['wid'], 'listingid', $parentwid['listingid'], 0);
                        $this->Auth_model->wlog($parentwid['bcn'], $parentwid['wid'], 'deleted', 0, 1);

                        $this->Myseller_model->getBase(array($parentwid['listingid']));
                        $this->mysmarty->assign('eid', (int) $parentwid['listingid']);
                        $this->mysmarty->assign('id', (int) $parentwid['listingid']);
                        $areaid = $parentwid['listingid'];
                    } else {
                        $data = $this->Mywarehouse_model->getsaleattachdata($parentwid['channel'], $parentwid['sold_id'], $parentwid['listingid'], 0);

                        $qty = $data['qty'];
                        unset($data['qty']);
                        $data['sold_id'] = $parentwid['sold_id'];
                        $data['sold_subid'] = $parentwid['sold_subid'];
                        $data['status'] = $parentwid['status'];
                        $data['sold'] = $parentwid['sold'];
                        $data['vended'] = $parentwid['vended'];
                        $data['channel'] = $parentwid['channel'];

                        $this->db->update('warehouse', $data, array('wid' => $matchedwid['wid']));
                        $this->db->update('warehouse', array('sold_id' => 0, 'deleted' => 1), array('wid' => $parentwid['wid']));
                        $this->Auth_model->wlog($parentwid['bcn'], $parentwid['wid'], 'sold_id', $parentwid['sold_id'], 0);
                        $this->Auth_model->wlog($parentwid['bcn'], $parentwid['wid'], 'deleted', 0, 1);
                        foreach ($data as $k => $v) {//printcool ($v); printcool ($wid[$k]);
                            if ($v != $matchedwid[$k])
                                $this->Auth_model->wlog($matchedwid['bcn'], $matchedwid['wid'], $k, $matchedwid[$k], $v);
                        }

                        $this->Myseller_model->getSalesListings(array(0 => true, $parentwid['listingid'] => true));
                        $this->Myseller_model->getSales(array((int) $parentwid['sold_id']), (int) $parentwid['channel']);

                        $this->mysmarty->assign('sales', (int) $parentwid['channel']);
                        $this->mysmarty->assign('eid', $parentwid['listingid']);
                        $this->mysmarty->assign('id', (int) $parentwid['sold_id']);
                        $areaid = $parentwid['sold_id'];
                        if ($parentwid['channel'] == 2)
                            $this->mysmarty->assign('subid', (int) $parentwid['sold_subid']);
                        $this->mysmarty->assign('quantity', $qty);
                    }

                    $this->mysmarty->assign('updatetime', CurrentTimeR());
                    echo json_encode(array('html' => $this->mysmarty->fetch('myseller/bcnarea.html'), 'areaid' => $areaid));
                }
            } else {
                if ($parentwid) {
                    $this->db->update('warehouse', array('bcn' => $val, 'generic' => 0), array('wid' => $parentwid['wid']));
                    $this->Auth_model->wlog($parentwid['bcn'], $parentwid['wid'], 'bcn', $parentwid['bcn'], $val);
                    echo 1;
                }
            }
        }
    }

    function clean() {
        $this->db->where('wid >', 85485);
        $this->db->delete('warehouse');
    }

    function LoadProductsInCategory($catId) {
        $this->db->select('e_id, e_title, price_ch1')
                ->from('ebay')
                ->where('storeCatID', $catId);

        $query = $this->db->get();

        $title_array = array();

        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {

                $title_array[] = array('id' => $row['e_id'],
                    //'value' => '<a href="'.$site_url.'/Mysku/Listing/'.$row['e_id'].'">'.$row['e_model'].'</a>',
                    'value' => '<a id="mylin1k" onclick="showListing(' . $row['e_id'] . ')">' . $row['e_title'] . '</a>',
                    'open' => false,
                    'product' => substr($row['e_title'], 0, 25),
                    'listing_title' => $row['e_title'],
                );
            }
        } else { //If there is no Items in the category. Drag and Drop Functionality of the tree doesn't work. So we add fake items. They will not be added to the database
            $title_array[] = array('id' => 0,
                //'value' => '<a href="'.$site_url.'/Mysku/Listing/'.$row['e_id'].'">'.$row['e_model'].'</a>',
                'value' => 'Drag Items Here',
                'open' => false,
                'product' => 'No product',
                'listing_title' => 'Drag Items Here'
            );
        }


        //printcool($title_array);
        return $title_array;
    }

    function EditCategory($e_id) {
        if (isset($_POST['eid_cat'])) {
            //echo '<p>We have POST eid='.$_POST['eid_cat'];

            $query = $this->db->query('select storeCatID, primaryCategory, categoryEbaySecondaryId, categoryEbaySecondaryTitle, categoryAmazonId, categoryGoogleId from ebay where e_id=' . $_POST['eid_cat']);

            $row = $query->row(0);
            $row->storeCatID;
            $row->primaryCategory;
            $row->secondaryCategoryId;
            $row->categoryAmazonId;
            $row->categoryGoogleId;

            if (isset($_POST['CatStore']) AND isset($_POST['StoreCatTitle']) AND $_POST['CatStore'] != 0 AND (int) $_POST['CatStore'] != (int) $row->storeCatID) {
                $this->db->set('storeCatID', $_POST['CatStore'], FALSE);
                $this->db->set('storeCatTitle', $_POST['StoreCatTitle']);
                $this->db->where('e_id', (int) $_POST['eid_cat']);
                $this->db->update('ebay');
            }

            if (isset($_POST['eBayPrimCatTitle']) AND isset($_POST['CatPrimEbay']) AND $_POST['CatPrimEbay'] != 0 AND $_POST['CatPrimEbay'] != $row->primaryCategory) {

                $this->db->set('primaryCategory', $_POST['CatPrimEbay'], FALSE);
                $this->db->set('pCTitle', $_POST['eBayPrimCatTitle']);
                $this->db->where('e_id', (int) $_POST['eid_cat']);
                $this->db->update('ebay');
            }

            if (isset($_POST['eBaySecCatTitle']) AND isset($_POST['CatSecEbay']) AND $_POST['CatSecEbay'] != 0 AND $_POST['CatSecEbay'] != $row->secondaryCategoryId) {

                $this->db->set('categoryEbaySecondaryId', $_POST['CatSecEbay'], FALSE);
                $this->db->set('categoryEbaySecondaryTitle', $_POST['eBaySecCatTitle']);

                $this->db->where('e_id', (int) $_POST['eid_cat']);
                $this->db->update('ebay');
            }

            if (isset($_POST['CatAmazon']) AND $_POST['CatAmazon'] != 0 AND $_POST['CatAmazon'] != $row->categoryAmazonId) {

                $this->db->set('categoryAmazonId', $_POST['CatAmazon'], FALSE);
                $this->db->where('e_id', (int) $_POST['eid_cat']);
                $this->db->update('ebay');
            }

            if (isset($_POST['CatGoogle']) AND $_POST['CatGoogle'] != 0 AND $_POST['CatGoogle'] != $row->categoryGoogleId AND isset($_POST['gtaxonomyTitle'])) {
                $this->db->set('categoryGoogleId', $_POST['CatGoogle'], FALSE);
                $this->db->set('gtaxonomy', $_POST['gtaxonomyTitle']);
                $this->db->where('e_id', (int) $_POST['eid_cat']);
                $this->db->update('ebay');
            }

            $this->db->set('primaryCategory', $_POST['CatPrimEbay'], FALSE);
            $this->db->set('pCTitle', $_POST['eBayPrimCatTitle']);
            $this->db->where('e_id', (int) $_POST['eid_cat']);
            $this->db->update('ebay');
        } else { //IF NO POST then we HAVE COME FROM http://www.vic.la-tronics.com/Mysku/Listing
            //echo '<p>WE SHOULD';
            $this->mysmarty->assign('e_id', $e_id);

            $queryeStore = $this->db->query('select id, id_store, store_cat_title from categories_store');
            $queryeGoogle = $this->db->query('select id, id_google, google_cat_title from categories_google');
            $queryeAmazon = $this->db->query('select id, id_amazon, amazon_cat_title from categories_amazon');
            $queryeBay1 = $this->db->query('select distinct primaryCategory, pCTitle from ebay where primaryCategory is not null and primaryCategory<>0 and pCTitle is not null');
            $queryeBay2 = $this->db->query('select distinct categoryEbaySecondaryId, categoryEbaySecondaryTitle from ebay where categoryEbaySecondaryId is not null and categoryEbaySecondaryId<>0 and categoryEbaySecondaryTitle is not null');


            //printcool($queryeBay->result_array());
            foreach ($queryeStore->result_array() as $row) {
                $this->storeCategories[$row['id_store']] = $row['store_cat_title'];
            }
            $this->storeCategories[0] = '';

            foreach ($queryeGoogle->result_array() as $row) {
                $this->googleCategories[$row['id_google']] = $row['google_cat_title'];
            }
            $this->googleCategories[0] = '';

            foreach ($queryeAmazon->result_array() as $row) {
                $this->amazonCategories[$row['id_amazon']] = $row['amazon_cat_title'];
            }
            $this->amazonCategories[0] = '';

            foreach ($queryeBay1->result_array() as $row) {
                $this->ebayCategories1[$row['primaryCategory']] = $row['pCTitle'];
            }
            $this->ebayCategories1[0] = '';

            foreach ($queryeBay2->result_array() as $row) {
                $this->ebayCategories2[$row['categoryEbaySecondaryId']] = $row['categoryEbaySecondaryTitle'];
            }
            $this->ebayCategories2[0] = '';

            //printcool($ebayCategories);


            $query = $this->db->query('select storeCatID, primaryCategory, categoryEbaySecondaryId,categoryEbaySecondaryTitle, categoryAmazonId, categoryGoogleId from ebay where e_id=' . $e_id);

            $mywarehouse_categories = $this->db->get("warehouse_sku_categories")->result_array();

            $row = $query->row(0);
            $this->mysmarty->assign('mySelectStore', ($row->storeCatID == '') ? 0 : $row->storeCatID);
            $this->mysmarty->assign('mySelectEbayFirst', ($row->primaryCategory == '') ? 0 : $row->primaryCategory);
            $this->mysmarty->assign('mySelectEbaySecond', ($row->categoryEbaySecondaryId == '') ? 0 : $row->categoryEbaySecondaryId);
            $this->mysmarty->assign('mySelectAmazon', ($row->categoryAmazonId == '') ? 0 : $row->categoryAmazonId);
            $this->mysmarty->assign('mySelectGoogle', ($row->categoryGoogleId == '') ? 0 : $row->categoryGoogleId);
            // echo '<p>'.$row->storeCatID;
            $this->mysmarty->assign('myCatsStore', $mywarehouse_categories);
            $this->mysmarty->assign('myCatsEbay1', $this->ebayCategories1);
            $this->mysmarty->assign('myCatsEbay2', $this->ebayCategories2);
            $this->mysmarty->assign('myCatsAmazon', $this->amazonCategories);
            $this->mysmarty->assign('myCatsGoogle', $this->googleCategories);
            $this->mysmarty->assign('searchcat', 'Computers');
            //$this->mysmarty->view('mycategories/mycategories_mapping.html');
        }
    }

    function Listing($eid = null) {



        $this->load->helper('directory');
        $this->load->helper('file');
        //GET THE LOCKS FOR THE CATEGORIES
        $this->db->where("e_id", $_POST['eid']);
        $ebay_data = $this->db->get("ebay")->result_object();
        foreach ($ebay_data as $ebay_data_get) {
            $lock_google = $ebay_data_get->lock_google_cat;
            $lock_amazon = $ebay_data_get->lock_amazon_cat;
            $lock_ebay = $ebay_data_get->lock_ebay_cat;
            $shipping = unserialize($ebay_data_get->shipping);
        }

        // Show one specified listing
        if (isset($_POST['eid'])) {

            //echo '<p>We have post='.$_POST['eid'];
            $list[] = $this->Myebay_model->GetItem((int) $_POST['eid']);

            //printcool($list);
            $idarray[] = (int) $_POST['eid'];



            //$this->load->model('Myseller_model'); 	
            //$this->Myseller_model->getBase($idarray);
            //$this->Myseller_model->getOnHold($idarray);
            //$this->Myseller_model->countSales($idarray);
            //$this->Myseller_model->getEmptySales($idarray, 1);	
            //$this->load->model('Myautopilot_model');
            //$this->Myautopilot_model->GetListingRules($idarray);					
            //$this->Myautopilot_model->GetCompetitorRules($idarray);
            $this->mysmarty->assign('list', $list);
            $this->mysmarty->assign('e_id', $_POST['eid']);

            //echo '<p>NEW POST ID '.$_POST['eid'];
            $this->EditCategory($_POST['eid']); //call for inicialisation of catmappings
            //$this->mysmarty->view('mysku/myproducts_categories.html');

            /*
              print_r("Exclude -> ".$shipping['exclude']."<br>");
              print_r("Location Exclude -> ".$shipping['locationexclude']."<br>");
              echo "Domestic -> ";
              print_r($shipping['domestic']);
              echo "<br>International -> ";
              print_r($shipping['international']);
             */

            $sresponseXml = read_file($this->config->config['ebaypath'] . '/shipping.txt');
            $shxml = simplexml_load_string($sresponseXml);


            $sd = array();

            if (isset($shxml->Item->ShippingDetails->ShippingServiceOptions)) {
                foreach ($shxml->Item->ShippingDetails->ShippingServiceOptions as $s) {
                    $sd[(int) $s->ShippingServicePriority] = array('ShippingService' => (string) $s->ShippingService, 'ShippingServiceCost' => (float) $s->ShippingServiceCost, 'ShippingServiceAdditionalCost' => (float) $s->ShippingServiceAdditionalCost, 'FreeShipping' => (string) $s->FreeShipping
                    );
                }
            }
            $is = array();
            if (isset($shxml->Item->ShippingDetails->InternationalShippingServiceOption)) {
                foreach ($shxml->Item->ShippingDetails->InternationalShippingServiceOption as $s) {
                    $is[(int) $s->ShippingServicePriority] = array('ShippingService' => (string) $s->ShippingService, 'ShippingServiceCost' => (float) $s->ShippingServiceCost, 'ShippingServiceAdditionalCost' => (float) $s->ShippingServiceAdditionalCost, 'ShipToLocation' => (string) $s->ShipToLocation);
                }
            }


            $shortcuts = explode(",", $ebay_data[0]->sc_to);
            foreach ($shortcuts as $shortcuts_data) {
                if ($shortcuts_data) {
                    $this->db->where("wsc_id", $shortcuts_data);
                    $categories_data = $this->db->get("warehouse_sku_categories")->result_object();
                    $shortcut_full[] = array(
                        "category_id" => $categories_data[0]->wsc_id,
                        "category_name" => $categories_data[0]->wsc_title
                    );
                }
            }

            $this->mysmarty->assign("shortcuts", $shortcut_full);
            $this->mysmarty->assign('ShippingServices', $sd);

            $this->mysmarty->assign('IntlShippingServices', $is);

            $this->mysmarty->assign('SellerExcludeShipToLocationsPreference', (string) $xml->Item->ShippingDetails->SellerExcludeShipToLocationsPreference);
            $this->mysmarty->assign('ExcludeShipToLocation', (array) $xml->Item->ShippingDetails->ExcludeShipToLocation);

            $this->mysmarty->assign('shipping_exclude', $shipping['exclude']);
            $this->mysmarty->assign('shipping_location_exclude', $shipping['locationexclude']);
            $this->mysmarty->assign('shipping_domestic', $shipping['domestic']);
            $this->mysmarty->assign('shipping_international', $shipping['international']);
            $this->mysmarty->assign('ShippingOptions', $shxml->ShippingServiceDetails);
            $this->mysmarty->assign('shipcount', array(1, 2, 3, 4));
            $this->mysmarty->assign('lock_google', $lock_google);
            $this->mysmarty->assign('lock_amazon', $lock_amazon);
            $this->mysmarty->assign('lock_ebay', $lock_ebay);
            $this->mysmarty->assign('showTabCategories', TRUE);
            $this->mysmarty->view('mysku/myebay_show_cats.html');
            //echo $this->mysmarty->fetch('mysku/myebay_show.html');  
        } // Show one random listing
        else {

            $this->db->order_by("wsc_title");
            $query = $this->db->get('warehouse_sku_categories');

            foreach ($query->result_array() as $row) {
                // echo $row->title;
                $treearray['value'] = 'Category';
                $treearray['id'] = 'Category';
                $treearray['open'] = 'true';

                $data_final[] = array(
                    "id" => $row['wsc_id'],
                    "value" => '<a id="mylin0k" onclick="showcategoryListings(' . $row['wsc_id'] . ',\'' . $row['wsc_title'] . '\')">' . $row['wsc_title'] . '</a>',
                    "parent_id" => $row['wsc_parent'],
                    'listing_title' => $row['wsc_id'],
                    'title' => $row['wsc_title'],
                    "icon" => "folder"
                        //'data' => $this->LoadProductsInCategory($row['id_store'])
                );

                /*   $treearray['data'][] = array(
                  'id' => $row['id_store'],
                  'value' => '<a id="mylin0k" onclick="showcategoryListings(' . $row['id_store'] . ',\'' . $row['store_cat_title'] . '\')">' . substr($row['store_cat_title'], 0, 25) . '</a>',
                  //  'value'=>'<a href="ShowListingsInCategory/1/0/0/'.$row['store_cat_title'].'/'.$row['id_store'].'/" id="mylin0k">'.substr($row['store_cat_title'],0,25).'</a>',
                  'open' => false,
                  'listing_title' => $row['id_store'],
                  'title' => $row['store_cat_title'],
                  'parent_id' => $row['dad_cat'],
                  'data' => $this->LoadProductsInCategory($row['id_store'])

                  ); */

                $catIdarray[] = $row['wsc_id'];
            }
            $products_tree = $this->db->get("ebay")->result_object();
            foreach ($products_tree as $products_tree) {
                if ($products_tree->sc_to != 0) {
                    $shortcuts = explode(",", $products_tree->sc_to);

                    foreach ($shortcuts as $shortcuts) {

                        $data_final[] = array(
                            "id" => $products_tree->e_id . "/SC/" . $shortcuts,
                            "value" => '<a id="mylin1k" onclick="showListing(' . $products_tree->e_id . ')">' . $products_tree->e_title . '</a>',
                            "parent_id" => $shortcuts,
                            "icon" => "shortcut"
                        );
                    }
                }
                $data_final[] = array(
                    "id" => $products_tree->e_id,
                    "value" => '<a id="mylin1k" onclick="showListing(' . $products_tree->e_id . ')">' . $products_tree->e_title . '</a>',
                    "parent_id" => $products_tree->storeCatID
                );
            }


            $data = json_encode($data_final);
            $data = str_replace("][", ",", $data_final);
            //printcool(json_encode($catlist));
            $this->EditCategory(634);
            $this->mysmarty->assign('catlist', json_encode($data));
            $list[] = $this->Myebay_model->GetItem(634); // Just to show something on startup
            $this->mysmarty->assign('list', $list);
            $this->mysmarty->assign('e_id', 634);

            $sresponseXml = read_file($this->config->config['ebaypath'] . '/shipping.txt');
            $shxml = simplexml_load_string($sresponseXml);

            $this->db->where("is_chanel", 1);
            $category_chanels = $this->db->get("warehouse_sku_categories")->result_object();

            //GET LOG DATA
            $this->db->order_by("date", "DESC");
            $log = $this->db->get("warehouse_sku_categories_log")->result_object();

            $this->mysmarty->assign("log", $log);
            $this->mysmarty->assign('category_chanels', $category_chanels);
            $this->mysmarty->assign('ShippingOptions', $shxml->ShippingServiceDetails);
            $this->mysmarty->assign('shipping_exclude', $shipping['exclude']);
            $this->mysmarty->assign('shipping_location_exclude', $shipping['locationexclude']);
            $this->mysmarty->assign('shipping_domestic', $shipping['domestic']);
            $this->mysmarty->assign('shipping_international', $shipping['international']);
            $this->mysmarty->assign('shipcount', array(1, 2, 3, 4));
            $this->mysmarty->assign('showTabCategories', TRUE);
            $this->mysmarty->view('mysku/myproducts_categories.html');
            //$this->mysmarty->view('myebay/myebay_show.html');
        }
    }

    function RemoveShortcut($e_id, $cat_id) {
        $this->db->where("e_id", $e_id);
        $ebay_data = $this->db->get("ebay")->result_object();
        $new_shortcutdata = str_replace($cat_id . ",", "", $ebay_data[0]->sc_to);
        $this->db->where("e_id", $e_id);
        $this->db->set("sc_to", $new_shortcutdata);
        if ($this->db->update("ebay")) {
            echo "success";
            $error = "success";
        } else {
            echo "fail";
            $error = "Problem to remove the shortcut";
        }
        $log = array(
            "user_id" => $this->session->userdata("admin_id"),
            "username" => $this->session->userdata("name"),
            "old_parent" => $cat_id,
            "product_id" => $e_id,
            "status" => $error,
            "action" => "shortcut removed",
            "date" => date("Y-m-d H:i:s")
        );
        $this->db->insert("warehouse_sku_categories_log", $log);
    }

    function RemoveAllShortcuts($e_id) {
        $this->db->where("e_id", $e_id);
        $this->db->set("sc_to", '');
        if ($this->db->update("ebay")) {
            echo "success";
        } else {
            echo "fail";
        }
    }

    function DeleteCategory($cat_id) {
        $password = md5($this->input->post("confirm_password"));

        $this->db->where("admin_id", $this->session->userdata('admin_id'));
        $this->db->where("pass", md5($password));
        $user_row = $this->db->get("administrators")->num_rows();
        if ($user_row == 1) {
            $this->db->where("id_store", $cat_id);
            $this->db->delete("categories_store");

            $this->db->where("wsc_id", $cat_id);
            $this->db->delete("warehouse_sku_categories");
        } else {
            echo "Error";
        }
    }

    function SetBanner() {
        $listing_id = $this->input->post("listing_id");
        $checked = $this->input->post("checked");

        if (($listing_id != null) && ($checked != null)) {
            $this->db->set("show_banner", $checked);
            $this->db->where("e_id", $listing_id);
            $this->db->update("ebay");
        }
    }

    function ShowListingsInCategory($page = 1, $page_mode = false, $storeCatId = null) {
        $this->load->helper('directory');
        $this->load->helper('file');
        //echo '<p>Search $page = '.$page;
        ////echo '<p>Cat owner ShowListingsInCategory = '.$storeCatIdName;
        //echo '<p>Store cat Id = '.$storeCatId;
        //echo '<p>Store cat Id from session = '.$this->session->userdata('StoreCatId');
        //echo '<p>Store cat Name = '.$storeCatIdName;
        //if (isset($_POST['search']))
        //{
        //echo '<p>VIZ cat='.$_POST['search'];
        //echo '<h2>All listings in category '.$storeCatIdName.'</h2>';

        $this->ListItems($page, $page_mode);

        /* Page_mode variable is used to remember that we are in page mode and must take needed Id-s for categories from
          session variables. That is why we save them when page_mode is false. */
        if ($page_mode == false) {
            // MOST IMPORTANT 2 SESSION VARIABLES. SET THEM ONLY FROM HERE.
            $this->session->set_userdata('StoreCatId', $storeCatId);
            $this->session->set_userdata('StoreCatIdName', $_POST['storeCatIdName']);

            $this->mysmarty->assign('StoreCatIdName', $_POST['storeCatIdName']);
            $this->mysmarty->assign('StoreCatId', $storeCatId);
        } else {
            $this->mysmarty->assign('StoreCatIdName', $this->session->userdata('StoreCatIdName'));
            $this->mysmarty->assign('StoreCatId', $this->session->userdata('StoreCatId'));
        }

        echo '<p>' . $storeCatIdName;

        $this->mysmarty->assign('showTabCategories', TRUE);

        $this->CategoryReport($this->session->userdata('StoreCatId'));

        //echo "<p>"."After Category report";

        $queryeStore = $this->db->query('select id, id_store, store_cat_title from categories_store');
        $queryeGoogle = $this->db->query('select id, id_google, google_cat_title from categories_google');
        $queryeAmazon = $this->db->query('select id, id_amazon, amazon_cat_title from categories_amazon');
        $queryeBay1 = $this->db->query('select distinct primaryCategory, pCTitle from ebay where primaryCategory is not null and primaryCategory<>0 and pCTitle is not null');
        $queryeBay2 = $this->db->query('select distinct categoryEbaySecondaryId, categoryEbaySecondaryTitle from ebay where categoryEbaySecondaryId is not null and categoryEbaySecondaryId<>0 and categoryEbaySecondaryTitle is not null');

        $mywarehouse_categories = $this->db->get("warehouse_sku_categories")->result_array();

        //printcool($queryeBay->result_array());
        foreach ($queryeStore->result_array() as $row) {
            $this->storeCategories[$row['id_store']] = $row['store_cat_title'];
        }
        $this->storeCategories[0] = '';

        foreach ($queryeGoogle->result_array() as $row) {
            $this->googleCategories[$row['id_google']] = $row['google_cat_title'];
        }
        $this->googleCategories[0] = '';

        foreach ($queryeAmazon->result_array() as $row) {
            $this->amazonCategories[$row['id_amazon']] = $row['amazon_cat_title'];
        }
        $this->amazonCategories[0] = '';

        foreach ($queryeBay1->result_array() as $row) {
            $this->ebayCategories1[$row['primaryCategory']] = $row['pCTitle'];
        }
        $this->ebayCategories1[0] = '';

        foreach ($queryeBay2->result_array() as $row) {
            $this->ebayCategories2[$row['categoryEbaySecondaryId']] = $row['categoryEbaySecondaryTitle'];
        }
        $this->ebayCategories2[0] = '';


        //printcool($this->storeCategories);
        $sresponseXml = read_file($this->config->config['ebaypath'] . '/shipping.txt');
        $shxml = simplexml_load_string($sresponseXml);
        $this->mysmarty->assign('ShippingOptions', $shxml->ShippingServiceDetails);
        $this->mysmarty->assign('shipcount', array(1, 2, 3, 4));
        $this->mysmarty->assign('myCatsStore', $mywarehouse_categories);
        $this->mysmarty->assign('myCatsEbay1', $this->ebayCategories1);
        $this->mysmarty->assign('myCatsEbay2', $this->ebayCategories2);
        $this->mysmarty->assign('myCatsAmazon', $this->amazonCategories);
        $this->mysmarty->assign('myCatsGoogle', $this->googleCategories);
        $this->mysmarty->assign('searchcat', 'Computers');

        //$this->mysmarty->view('mysku/bulk_categories_update.html');
        $this->mysmarty->assign('page_control', 1);
        $this->mysmarty->view('mysku/myebay_show.html');
        //}
    }

    function ShowPrivateShipping($e_id) {
        $this->db->where("e_id", $e_id);
        $ebay_data = $this->db->get("ebay")->result_object();

        echo json_encode(unserialize($ebay_data[0]->shipping));
    }

    function ListingGhostGen($called = false) {
        if ((!$called && isset($_POST['listingid']) && isset($_POST['amount'])) || ($called && isset($called['listingid']) && isset($called['qn']))) {
            $title = $this->Mywarehouse_model->GetListingTitleAndCondition((int) $_POST['listingid'], true);
            if (!$title)
                exit('0');
            $this->db->select("bcn");
            $this->db->where('waid', 0);
            $this->db->where('generic', 1);
            $this->db->where('bcn_p1', "G");
            //$this->db->order_by("bcn_p2", "DESC");
            $this->db->order_by("wid", "DESC");
            $w = $this->db->get('warehouse', 1);
            if ($w->num_rows() > 0) {
                if (!$called)
                    $listingid = (int) $_POST['listingid'];
                else
                    $listingid = $called['listingid'];
                $next = $w->row_array();
                $next = (int) str_replace('G', '', trim($next['bcn']));
//printcool ($next);
                if (!$called)
                    $amount = (int) $_POST['amount'];
                else
                    $amount = $called['qn'];

                $start = 1;
                while ($start <= $amount) {

                    $next++;

                    $this->db->where('bcn', "G" . $next);
                    $this->db->or_where('lot', "G" . $next);
                    $this->db->or_where('oldbcn', "G" . $next);
                    $q = $this->db->get('warehouse');
                    if ($q->num_rows() > 0) {
                        $next++;
                    }
                    $array['waid'] = 0;
                    $array['bcn'] = "G" . $next;
                    $array['bcn_p1'] = "G";
                    $array['bcn_p2'] = $next;
                    $array['listingid'] = $listingid;
                    $array['status'] = 'Listed';
                    $array['title'] = $title;
                    $array['listed_date'] = CurrentTime();
                    $array['generic'] = 1;
                    $array['adminid'] = (int) $this->session->userdata['admin_id'];
                    $this->db->insert('warehouse', $array);
                    $start++;
                    //printcool ($array);
                }


                $actionqn = 0 - $amount;

                $this->Myseller_model->runAssigner((int) $_POST['listingid'], $actionqn);
                echo $this->_getbcnsnippet($listingid, false, 'listing');
            } else
                echo 0;
        } else
            echo 0;
    }

    function _getbcnsnippet($id, $subid, $type) {
        if ($type == 'listing') {

            $this->Myseller_model->getBase(array($id));
        }
        $this->mysmarty->assign('id', $id);
        $this->Myseller_model->getChannelData($id);
        $this->mysmarty->assign('updatetime', CurrentTimeR());
        echo $this->mysmarty->fetch('myseller/bcnarea.html');
    }

    function Finder($return = false) {
        if ($return) {
            $str = trim($this->input->post('src'));
            $listingid = (int) $this->input->post('listingid');
            $this->mysmarty->assign('listingid', $listingid);
            $this->mysmarty->assign('action', (int) $this->input->post('wid'));
        } else {
            if (isset($this->gotobcn))
                $str = $this->gotobcn;
            else
                $str = trim($this->input->post('find1'));
            $listingid = false;
        }

        if ($return) {
            $this->mysmarty->assign('list', $this->Mywarehouse_model->GetFound($str, $listingid));
            echo $this->mysmarty->fetch('mywarehouse/finder_listing.html');
        } else {
            if ($str != '')
                $list = $this->Mywarehouse_model->GetFound($str, $listingid);
            else {
                $this->session->set_flashdata('error_msg', 'Empty search value');
                if (($_SERVER['HTTP_REFERER'] != $this->config->config['base_url'] . '/Mywarehouse/Finder') && ($_SERVER['HTTP_REFERER'] != '')) {
                    header("Location: " . $_SERVER['HTTP_REFERER']);
                    exit();
                }
            }
            if (count($list) == 1) {
                $warehouse_area = $this->session->userdata('warehouse_area');
                //$warehouse_area2 = $this->session->userdata('warehouse_area2');

                $this->session->set_userdata('warehouse_area2', $this->session->userdata('warehouse_area'));
                $this->session->set_userdata('warehouse_area', '');

                if ($warehouse_area == 'testing'/* || $warehouse_area2 == 'testing' */)
                    Redirect('Mywarehouse/Testing/' . $this->Mywarehouse_model->AuctionNameToId($list[0]['aucid']) . '/' . $list[0]['wid']);
                elseif ($warehouse_area == 'accounting'/* || $warehouse_area2 == 'accounting' */)
                    Redirect('Mywarehouse/Accounting/' . $this->Mywarehouse_model->AuctionNameToId($list[0]['aucid']) . '/' . $list[0]['wid']);
                else
                    Redirect('Mywarehouse/bcndetails/' . $list[0]['wid']);
            } else {
                //$this->session->set_flashdata('error_msg', 'No Results');
            }
            $this->mysmarty->assign('list', $list);
            $this->mysmarty->assign('find1', $str);
            $this->mysmarty->assign('admins', $this->Mywarehouse_model->GetAdminList());
            $this->mysmarty->view('mywarehouse/finder_main.html');
        }
    }

    function AttachBcnToListing() {
        $this->Mywarehouse_model->AttachBcnToListing((int) $this->input->post('wid'), (int) $this->input->post('listingid'));
    }

    function DettachBcnFromListing() {
        $this->Mywarehouse_model->DettachBcnFromListing((int) $this->input->post('wid'), (int) $this->input->post('listingid'));
    }

    function GetAttachedBcns() {
        $listingid = (int) $this->input->post('listingid');
        $this->mysmarty->assign('list', $this->Mywarehouse_model->GetAttachedBcns($listingid));
        $this->mysmarty->assign('attached', TRUE);
        $this->mysmarty->assign('listingid', $listingid);
        $this->mysmarty->assign('action', (int) $this->input->post('wid'));
        echo $this->mysmarty->fetch('mywarehouse/finder_listing.html');
    }

    function ProcessOldEbay() {
        exit();
        $this->db->select('e_id, e_title, e_manuf, e_part, e_model, e_compat, e_package, e_condition');
        $this->db->where('e_part !=', '');
        $this->db->order_by("e_id", "ASC");
        $q = $this->db->get('ebay');


        if ($q->num_rows() > 0) {
            foreach ($q->result_array() as $r) {

                //echo '<tr>';
                //echo '<td>'.$r['e_id'].'</td>';
                //echo '<td>'.$r['e_title'].'</td>';
                //echo '<td>'.$r['e_manuf'].'</td>';
                $r['e_part'] = array_map('trim', explode(',', $r['e_part']));
                //foreach ($r['e_part'] as $p)
                //	{						
                //$r['e_part'][] = trim($p);	
                //	}
                //echo '<td>'.$r['e_model'].'</td>'; echo '<td>'.$r['e_compat'].'</td>';
                //echo '<td>'.$r['e_package'].'</td>';
                //echo '<td>'.$r['e_condition'].'</td>';
                //echo '</tr>';				
                $bcn_p1 = date("m") . substr(date("y"), 1, 1);
                foreach ($r['e_part'] as $k => $v) {
                    if (trim($r['e_compat']) != '')
                        $data['mfgpart'] = $r['e_model'] . ' | ' . $r['e_compat'];
                    else
                        $data['mfgpart'] = $r['e_model'];
                    $data['mfgname'] = $r['e_manuf'];
                    $data['title'] = $r['e_title'];
                    $data['listingid'] = $r['e_id'];
                    $data['aucid'] = 'eBayDB ' . ceil($r['e_id'] / 100);


                    $bcn_p2 = sprintf('%05u', $this->Mywarehouse_model->GetNextBcn((int) $bcn_p1));

                    $data['bcn'] = $bcn_p1 . '-' . $bcn_p2 . '-' . $v;
                    $data['bcn_p1'] = $bcn_p1;
                    $data['bcn_p2'] = $bcn_p2;
                    $data['bcn_p3'] = $v;
                    $data['notes'] = $r['e_package'];
                    $data['problems'] = $r['e_condition'];
                    //printcool ($data);
                    //$this->db->insert('warehouse', $data);
                }
            }
        }

        echo 'COMPLETE';
    }

    function GetWarehouseItems() {
        if (!isset($_POST['aucid']))
            exit('No ID');
        $aucid = trim($this->input->post('aucid'));
        $list = $this->Mywarehouse_model->GetWarehouseItems($aucid);
        $sellingfee = 0;
        $shippingfee = 0;
        $emptysellingfee = 0;
        $emptyshippingfee = 0;
        if ($this->session->userdata['type'] == 'master' || ($this->session->userdata['type'] != 'master' && $this->session->userdata['accounting'] == 1)) {
            foreach ($list as $l) {
                if ((float) $l['sellingfee'] == 0)
                    $emptysellingfee++;
                if ((float) $l['shipped'] == 0)
                    $emptyshippingfee++;
                $sellingfee = $sellingfee + (float) $l['sellingfee'];
                $shippingfee = $shippingfee + (float) $l['shipped'];
            }
        }
        $this->mysmarty->assign('sellingfee', $sellingfee);
        $this->mysmarty->assign('shippingfee', $shippingfee);
        $this->mysmarty->assign('emptysellingfee', $emptysellingfee);
        $this->mysmarty->assign('emptyshippingfee', $emptyshippingfee);
        $this->mysmarty->assign('list', $list);
        $this->mysmarty->assign('aucid', $aucid);
        $this->mysmarty->assign('auction', $this->Mywarehouse_model->GetAuction(trim($aucid)));
        echo $this->mysmarty->fetch('mywarehouse/warehouse_items.html');
    }

    function FillAcutions() {
        exit();
        $this->db->select("distinct aucid", false);
        $this->query = $this->db->get('warehouse');
        //printcool($this->query->result_array());
        foreach ($this->query->result_array() as $r) {
            $this->db->insert('s', array('wtitle' => $r['aucid'], 'wdate' => CurrentTime()));
            $this->db->update('warehouse', array('waid' => $this->db->insert_id()), array('aucid' => $r['aucid']));
        }
    }

    function FillLastAuctionReport() {
        exit();
        $this->db->select("wid, waid");
        $this->query = $this->db->get('warehouse');
        //printcool($this->query->result_array());
        foreach ($this->query->result_array() as $r) {
            $this->db->update('warehouse', array('insid' => $r['waid']), array('wid' => $r['wid']));
        }
    }

    function GetEbayLiveDBData($mod = 0, $type = 0) {
        $ebl = array('active' => false, 'sold' => false, 'unsold' => false);
        $query = $this->db->get('ebay_live');
        foreach ($query->result_array() as $r) {
            if ($r['etype'] == 's')
                $ebl['sold'][] = $r;
            elseif ($r['etype'] == 'u')
                $ebl['unsold'][] = $r;
            else
                $ebl['active'][] = $r;

            if ($r['eid'] > 0) {
                if (($r['lq'] != $r['ebavq']) || $r['lebq'] != $r['ebtq']) {
                    $this->db->select('el_id, eid, ebavq, ebtq, lq, lebq, itemid');
                    $this->db->where('el_id', (int) $r['el_id']);
                    $query = $this->db->get('ebay_live');
                    if ($query->num_rows() > 0) {
                        $rm = $query->row_array();
                        $this->db->update('ebay', array('quantity' => $r['ebavq'], 'ebayquantity' => $r['ebtq']), array('e_id' => (int) $rm['eid']));
                        $this->db->update('ebay_live', array('lq' => $rm['ebavq'], 'lebq' => $rm['ebtq']), array('el_id' => (int) $r['el_id']));

                        $ra['admin'] = $this->session->userdata['ownnames'];
                        $ra['time'] = CurrentTimeR();
                        $ra['ctrl'] = 'UpdateQuantityFromActive';
                        $ra['field'] = 'quantity';
                        $ra['atype'] = 'Q';
                        $ra['e_id'] = (int) $r['eid'];
                        $ra['ebay_id'] = (int) $r['itemid'];
                        $ra['datafrom'] = $r['lq'];
                        $ra['datato'] = (int) $r['ebavq'];
                        if ($ra['datafrom'] != $ra['datato'])
                            $this->db->insert('ebay_actionlog', $ra);
                        $ra['field'] = 'ebayquantity';
                        $ra['datafrom'] = $r['lebq'];
                        $ra['datato'] = (int) $r['ebtq'];
                        if ($ra['datafrom'] != $ra['datato'])
                            $this->db->insert('ebay_actionlog', $ra);
                    }
                }
            }
        }
        $this->mysmarty->assign('ebl', $ebl);
        $this->mysmarty->assign('mod', $mod);
        $this->mysmarty->assign('livetype', $type);
        $this->mysmarty->view('myebay/myebay_ebl.html');
    }

    /*
      function testloadurl()
      {
      echo json_encode(array(array('id' => 1, 'qty' => 10, 'title' => 'title 1'), array('id' => 2, 'qty' => 20, 'title' => 'title 2'), array('id' => 3, 'qty' => 30, 'title' => 'title 3')));
      }
      function testsaveurl()
      {
      Gomail(array('msg_title' => 'testsave', 'msg_body' => printcool($_POST,true)), 'mr.reece@gmail.com');
      $out['result'] = 'OK';
      echo json_encode($out);
      }
      function testautosaveurl()
      {
      Gomail(array('msg_title' => 'testautosave', 'msg_body' => printcool($_POST,true)), 'mr.reece@gmail.com');

      $colMap = array(
      0 => 'qty',
      1 => 'cost',
      2 => 'mfgname',
      3 => 'mfgpart',
      4 => 'title',
      5 => 'lot'
      );


      foreach($_POST as $d)
      {
      //$d[0] ROW
      //$d[1] COL
      //$d[2] FROM VAL
      //$d[3] TO VAL



      }
      $out['result'] = 'OK';
      echo json_encode($out);
      }
      function TestFullEditor()
      {

      $fielset = array('accounting' => array(
      'headers' => "'BCN', 'Where Listed', 'Listed Date', 'Where Sold' , 'Sold Date', 'Paid', 'Paid Date', 'Shipped', 'Shipped Date'",
      'width' => "80, 125, 125, 125, 125, 125, 125, 125, 125",
      'startcols' => 9,
      'startrows' => 100,
      'saveurl' => "/Mywarehouse/testsaveurl/",
      'autosaveurl' => "/Mywarehouse/testautosaveurl/",
      'loadurl' => "/Mywarehouse/testloadurl/",
      'colmap' => "{readOnly: true},{},{},{},{},{},{},{},{}")
      );

      $this->mysmarty->assign('headers', $fielset['accounting']['headers']);
      $this->mysmarty->assign('width', $fielset['accounting']['width']);
      $this->mysmarty->assign('startcols', $fielset['accounting']['startcols']);
      $this->mysmarty->assign('startrows', $fielset['accounting']['startrows']);
      $this->mysmarty->assign('saveurl', $fielset['accounting']['saveurl']);
      $this->mysmarty->assign('autosaveurl', $fielset['accounting']['autosaveurl']);
      $this->mysmarty->assign('loadurl', $fielset['accounting']['loadurl']);
      $this->mysmarty->assign('colmap', $fielset['accounting']['colmap']);

      $this->mysmarty->view('mywarehouse/editorgo.html');

      }

      function TestEditorPre()
      {

      $fielset = array('testing' => array(
      'headers' => "'BCN', 'POST', 'Battery', 'Charger' , 'HDD Status', 'Problems', 'Notes', 'Sell', 'LastUpdt', 'Tech'",
      'width' => "100, 50, 50, 50, 250, 250, 250, 50, 125, 125",
      'startcols' => 9,
      'startrows' => 10,
      'autosaveurl' => "/Mywarehouse/Testing/".(int)$id,
      'colmap' => '{data: 0, readOnly: true},{data: 1, type: "checkbox", checkedTemplate: 1, uncheckedTemplate: 0},{data: 2, type: "checkbox", checkedTemplate: 1, uncheckedTemplate: 0},{data: 3, type: "checkbox", checkedTemplate: 1, uncheckedTemplate: 0},{data: 4},{data: 5},{data: 6},{data: 7, type: "checkbox", checkedTemplate: 1, uncheckedTemplate: 0},{data: 8, readOnly: true},{data: 9, readOnly: true}')
      );

      if ((int)$id == 0) $id = $this->Mywarehouse_model->GetLastAuc();
      $list = $this->Mywarehouse_model->GetTesting((int)$id);
      if ($list)
      {
      $this->session->set_userdata(array('accrel' => $list['headers'], 'acclot' => (int)$id));
      $loaddata = '';
      $adms = $this->Mywarehouse_model->GetAdminList();
      foreach ($list['data'] as $k => $l)
      {
      $loaddata .= "['".$l['bcn']."', '".$l['post']."', '".$l['battery']."', '".$l['charger']."', '".$l['hddstatus']."', '".$l['problems']."', '".$l['notes']."', '".$l['tosell']."', '".$l['techlastupdate']."', '".$adms[$l['tech']]."'],
      ";
      }
      }

      $this->mysmarty->assign('headers', $fielset['testing']['headers']);
      $this->mysmarty->assign('rowheaders', $fielset['testing']['rowheaders']);
      $this->mysmarty->assign('width', $fielset['testing']['width']);
      $this->mysmarty->assign('startcols', $fielset['testing']['startcols']);
      $this->mysmarty->assign('startrows', $fielset['testing']['startrows']);
      $this->mysmarty->assign('autosaveurl', $fielset['testing']['autosaveurl']);
      $this->mysmarty->assign('loaddata', rtrim($loaddata, ','));
      $this->mysmarty->assign('colmap', $fielset['testing']['colmap']);

      $this->mysmarty->assign('id', (int)$id);
      $this->mysmarty->assign('admins', $this->Mywarehouse_model->GetAdminList());
      $this->mysmarty->view('mywarehouse/editorpre.html');






      } */

    function parsebcn() {
        exit();
        $this->db->select('wid, bcn_p1, bcn_p2, bcn_p3');
        $this->db->where("bcnparsed", 0);
        $this->db->order_by("wid", "DESC");
        $this->query = $this->db->get('warehouse');
        if ($this->query->num_rows() > 0) {
            //printcool($this->query->result_array());

            foreach ($this->query->result_array() as $k => $l) {
                $l['bcn_p2'] = (int) $l['bcn_p2'];
                if ($l['bcn_p3'] == '')
                    $l['bcn'] = $l['bcn_p1'] . '-' . $l['bcn_p2'];
                else
                    $l['bcn'] = $l['bcn_p1'] . '-' . $l['bcn_p2'] . '-' . $l['bcn_p3'];

                $this->db->update('warehouse', array('bcn' => $l['bcn'], 'bcn_p2' => $l['bcn_p2'], 'bcnparsed' => 1), array('wid' => (int) $l['wid']));
            }
        }
    }

    function testpost() {
        exit();
        if ($_POST) {
            printcool($_POST);
            exit();
        }

        echo '<form method="post" action="/Mywarehouse/testpost">';

        $min = 1;
        $max = 1000;


        while ($min < $max) {
            echo '<input type="text" name="col1' . $min . '" value="t">';
            echo '<input type="text" name="col2' . $min . '" value="t">';
            echo '<input type="text" name="col3' . $min . '" value="t">';
            echo '<input type="text" name="col4' . $min . '" value="t">';
            echo '<input type="text" name="col5' . $min . '" value="t">';
            echo '<input type="text" name="col6' . $min . '" value="t">';


            $min++;
        }

        echo '<input type="submit" name="go" vaalue="GO">';

        echo '</form>';
    }

    function testmiv() {
        $this->_logallpost();
        phpinfo();
    }

    function _savesession($data = '') {
        if (!is_array($data))
            exit('Unable to write session');
        $arr = json_encode($data);
        $name = mktime() . '.txt';
        $this->load->helper('file');
        if (!write_file($this->config->config['pathtosystem'] . '/application/sess/' . $name, $arr)) {
            exit('Unable to write session');
        }
        return $name;
    }

    function _loadsession($filename = '') {
        if ($filename == '')
            exit('Error reading session');
        $this->load->helper('file');
        $newarr = read_file($this->config->config['pathtosystem'] . '/application/sess/' . $filename);
        //unlink($this->config->config['pathtosystem'].'/application/sess/'.$filename);
        return json_decode($newarr, true);
    }

    function _logallpost() {
        $name = CurrentTime() . '_' . (int) $this->session->userdata['admin_id'] . '_' . str_replace(' Mywarehouse ', '', str_replace('/', ' ', $_SERVER['REQUEST_URI'])) . '.txt';
        if ($_POST)
            file_put_contents($this->config->config['pathtosystem'] . '/application/sess/post/' . $name, urldecode(file_get_contents("php://input")));
    }

    function readpostlog() {
        $this->load->helper('directory');
        $map = array_reverse($this->_dir_map_sort(directory_map('../system_la/application/sess/post/')));
        $this->load->helper('file');
        if (count($map) > 0) {
            $data = array();
            $adms = $this->Mywarehouse_model->GetAdminList();
            $tcolMap = array(
                0 => 'BCN',
                1 => 'Title',
                2 => 'SN',
                3 => 'POST',
                4 => 'Battery',
                5 => 'Charger',
                6 => 'HDD Status',
                7 => 'Problems',
                8 => 'Notes',
                9 => 'Status',
                10 => 'Status Notes',
                11 => 'Parts Needed',
                12 => 'Warranty'
            );

            foreach ($map as $m) {

                $item = explode('_', $m);
                $guess = explode(' ', $item[2]);

                $stuff = read_file($this->config->config['pathtosystem'] . '/application/sess/post/' . $m);
                echo $adms[$item[1]] . ' @ ' . $item[0] . ' from ' . str_replace('.txt', '', $item[2]);
                echo '<br>';


                if (trim($guess[0]) == 'Testing') {
                    $info = explode('changes[0][]', $stuff);
                    $row = str_replace('=', '', str_replace('&', '', $info[1]));
                    $field = str_replace('=', '', str_replace('&', '', $info[2]));
                    $from = str_replace('=', '', str_replace('&', '', $info[3]));
                    $to = str_replace('=', '', str_replace('&', '', $info[4]));
                    echo 'Row: ' . $row . ' - Field: <strong>' . $tcolMap[(int) $field] . '</strong> - From: "' . $from . '" - To: "' . $to . '"';
                } else
                    echo str_replace('changes[0][]', 'VAL', $stuff);
                echo '<br>-<br>';
            }
        }
    }

    function _dir_map_sort($array) {
        $dirs = array();
        $files = array();

        foreach ($array as $key => $val) {
            if (is_array($val)) { // if is dir
                // run dir array through function to sort subdirs and files
                // unless it's empty
                $dirs[$key] = (!empty($array)) ? dir_map_sort($val) : $val;
            } else {
                $files[$key] = $val;
            }
        }

        ksort($dirs); // sort by key (dir name)
        asort($files); // sort by value (file name)
        // put the sorted arrays back together
        // swap $dirs and $files if you'd rather have files listed first
        return array_merge($dirs, $files);
    }

    function forcefind() {
        $this->db->select('wid, channel, sold_id, listingid, sold_date');
        $this->db->where('listingid', 13034);
        $e = $this->db->get('warehouse');
        if ($e->num_rows() > 0) {
            printcool($e->result_array());
        }
    }

    function forceprocess() {

//
        $ids = array(25420, 25419, 25418, 25417, 25416, 25415, 25414, 25413, 25410, 25407, 25406, 25412, 25411, 25408, 25405, 25404, 25403, 25402, 25401, 25400, 25399, 25398, 25397, 25396, 25395, 25394, 25393, 25392, 25391, 25389);

        foreach ($ids as $i) {
            $this->db->where('et_id', $i);
            $e = $this->db->get('ebay_transactions');
            if ($e->num_rows() > 0) {
                $ebt = $e->row_array();
                $this->load->model('Myseller_model');
                $this->Myseller_model->AssignBCN($ebt, 1);
            }
        }
    }

}
