<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class Show extends Controller
{

    function Show()
    {
        parent::Controller();
        $this->load->model('Menus_model');
        $this->load->model('Product_model');
        $this->StoreCart = $this->Product_model->GetStoreCart();
        $this->mysmarty->assign('StoreCart', $this->StoreCart);
        $this->Menus_model->GetStructure();
        $this->Product_model->GetStructure();

        $this->mysmarty->assign('session', $this->session->userdata);

        if (isset($this->session->userdata['user_id'])) {
            $this->load->model('Start_model');
            $this->load->model('Auth_model');
            $this->Auth_model->VerifyUser();
            $this->load->model('Myorders_model');
            $this->mysmarty->assign('statuses', $this->Myorders_model->GetStatuses());

            $this->mysmarty->assign('myorders', $this->Product_model->ListMyOrders($this->session->userdata['email']));
        }


        if (isset($this->session->userdata['cart']) && ($this->session->userdata['cart'] != '')) {
            $this->mysmarty->assign('cartsession', $this->session->userdata['cart']);
        }

        $this->mysmarty->assign('session', $this->session->userdata);
    }

    function index()
    {
        if ($this->StoreCart) {
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

        $sc = false;
        $this->db->select('storeCatID');
        $this->db->where('sitesell', 1);
        $this->db->distinct();
        $q = $this->db->get('ebay');
        if ($q->num_rows() > 0) {
            foreach ($q->result_array() as $s) {
                if ((int)$s['storeCatID'] > 0) $f[$s['storeCatID']] = TRUE;
            }

            $this->load->helper('directory');
            $this->load->helper('file');
            $responseXml = read_file($this->config->config['ebaypath'] . '/cats.txt');
            $sxml = simplexml_load_string($responseXml);
            $sc = array();
            if (isset($sxml->Store->CustomCategories->CustomCategory)) {
                foreach ($sxml->Store->CustomCategories->CustomCategory as $s) {
                    $a = (array)$s;
                    if (isset($f[$a['CategoryID']])) $sc[$a['CategoryID']] = $a['Name'];
                }
            }
            asort($sc);

        }

        //printcool ($sc);


        $this->mysmarty->assign('store', $sc);

        //GET ALL THE PRODUCTS TO SHOW ON BANNER SLIDER ON WEBSITE
        $this->db->where("show_banner", 1);
        $this->db->where("price_ch1 <>", '0.00');
        $this->db->where("qn_ch2 <>", 0);
        $ebay_banner = $this->db->get("ebay")->result_array();


        $this->db->where("wsc_title <>", "ACTIONS");
        $categories = $this->db->get("warehouse_sku_categories")->result_array();

        $this->db->where("wsc_parent <>", 0);
        $categories_child = $this->db->get("warehouse_sku_categories")->result_array();

        $this->mysmarty->assign('ebay', $this->Product_model->GetLatestEbayListings());
        $this->mysmarty->assign('ebay_banner', $ebay_banner);
        $this->mysmarty->assign('productview', 'homeebaylisting');
        $this->mysmarty->assign('categories', $categories);
        $this->mysmarty->assign('categories_child', $categories_child);
        $this->mysmarty->assign('imgebaypath', $this->config->config['wwwpath']['imgebay']);

        $this->mysmarty->view('welcome/welcome_main.html');


    }

public function getsubmenu($wsc_parent)
    {
        $this->db->where("wsc_parent", $wsc_parent);
        $categories = $this->db->get("warehouse_sku_categories")->result_object();
        foreach($categories as $categories)
        {
            $submenu[] = array(
                "id" => $categories->wsc_id,
                "value" => $categories->wsc_title,
                "submenu" => $this->getsubmenu($categories->wsc_id),
                "icon" => "cube"
                
            );
            $this->db->where("storeCatID", $categories->wsc_id);
            $products = $this->db->get("ebay")->result_object();
        foreach($products as $products)
        {
            $submenu[] = array(
                "id" => $products->e_id,
                "value" => $products->e_title,
                "icon" => "diamond"
                
            );
        }
        }
        
        return $submenu;
    }
    public function getfirstmenu($wsc_parent) {
        $this->db->where("wsc_mainparent", $wsc_parent);
        $this->db->where("wsc_parent", $wsc_parent);
        $categories = $this->db->get("warehouse_sku_categories")->result_object();
        
        
        foreach ($categories as $categories_totree) {
            
            $data_final[] = array(
                "id" => $categories_totree->wsc_id,
                "value" => $categories_totree->wsc_title,
                "submenu" => $this->getsubmenu($categories_totree->wsc_id),
                "icon" => "cubes"
            ); 
           
            
        }
        
        
        $data = json_encode($data_final);
        $data = str_replace("][", ",", $data_final);
       

            return $data;
    }

    function newnewindex()
    {

        $sc = false;
        $this->db->select('storeCatID');
        $this->db->where('sitesell', 1);
        $this->db->distinct();
        $q = $this->db->get('ebay');
        if ($q->num_rows() > 0) {
            foreach ($q->result_array() as $s) {
                if ((int)$s['storeCatID'] > 0) $f[$s['storeCatID']] = TRUE;
            }

            $this->load->helper('directory');
            $this->load->helper('file');
            $responseXml = read_file($this->config->config['ebaypath'] . '/cats.txt');
            $sxml = simplexml_load_string($responseXml);
            $sc = array();
            if (isset($sxml->Store->CustomCategories->CustomCategory)) {
                foreach ($sxml->Store->CustomCategories->CustomCategory as $s) {
                    $a = (array)$s;
                    if (isset($f[$a['CategoryID']])) $sc[$a['CategoryID']] = $a['Name'];
                }
            }
            asort($sc);

        }

        //printcool ($sc);


        $this->mysmarty->assign('store', $sc);

        $this->mysmarty->assign('ebay', $this->Product_model->GetLatestEbayListings());
        $this->mysmarty->assign('productview', 'homeebaylisting');
        $this->mysmarty->assign('imgebaypath', $this->config->config['wwwpath']['imgebay']);
        $this->mysmarty->view('welcome/welcome_main.html');

    }

    function searchitem()
    {
        $src = '';
        if (isset($_POST['item']) && CleanInput((string)$_POST['item'] != '')) {
            $src = CleanInput(htmlspecialchars((string)$_POST['item']));
            $this->mysmarty->assign('srcitem', $src);
        } else Redirect();
        if ($src == '') Redirect();

        $sc = false;
        $this->db->select('storeCatID');
        $this->db->where('sitesell', 1);
        $this->db->distinct();
        $q = $this->db->get('ebay');
        if ($q->num_rows() > 0) {
            foreach ($q->result_array() as $s) {
                if ((int)$s['storeCatID'] > 0) $f[$s['storeCatID']] = TRUE;
            }

            $this->load->helper('directory');
            $this->load->helper('file');
            $responseXml = read_file($this->config->config['ebaypath'] . '/cats.txt');
            $sxml = simplexml_load_string($responseXml);
            $sc = array();
            if (isset($sxml->Store->CustomCategories->CustomCategory)) {
                foreach ($sxml->Store->CustomCategories->CustomCategory as $s) {
                    $a = (array)$s;
                    if (isset($f[$a['CategoryID']])) $sc[$a['CategoryID']] = $a['Name'];
                }
            }
            asort($sc);

        }

        //printcool ($sc);


        $this->mysmarty->assign('store', $sc);

        $this->mysmarty->assign('ebay', $this->Product_model->GetLatestEbayListings($src));
        $this->mysmarty->assign('productview', 'homeebaylisting');
        $this->mysmarty->assign('imgebaypath', $this->config->config['wwwpath']['imgebay']);
        $this->mysmarty->view('welcome/welcome_main.html');

    }

    function store($ordering = 'listdesc', $id = 0, $page = 1)
    {
        $this->load->library('pagination');

        if ($ordering != 'listdesc' && $ordering != 'listasc' && $ordering != 'priceasc' && $ordering != 'pricedesc') $ordering = 'listdesc';

        $sc = false;
        $this->db->select('storeCatID');

        $this->db->where('sitesell', 1);
        $this->db->distinct();
        $q = $this->db->get('ebay');
        if ($q->num_rows() > 0) {
            foreach ($q->result_array() as $s) {
                if ((int)$s['storeCatID'] > 0) $f[$s['storeCatID']] = TRUE;
            }
            if (isset($_POST['prod-cat'])) $id = (int)$this->input->post('prod-cat', true);
            $this->load->helper('directory');
            $this->load->helper('file');
            $responseXml = read_file($this->config->config['ebaypath'] . '/cats.txt');
            $sxml = simplexml_load_string($responseXml);
            $sc = array();
            if (isset($sxml->Store->CustomCategories->CustomCategory)) {
                foreach ($sxml->Store->CustomCategories->CustomCategory as $s) {
                    $a = (array)$s;
                    if (isset($f[$a['CategoryID']])) {
                        $sc[$a['CategoryID']] = $a['Name'];
                        if ($a['CategoryID'] == (int)$id) {
                            $this->mysmarty->assign('current', $a['Name']);
                            $this->mysmarty->assign('currentid', $a['CategoryID']);
                        }
                    }
                }
            }
            asort($sc);

        }

        $get = $this->Product_model->GetStoreEbayListings($ordering, (int)$id, (int)$page);

        if($get != null) {
            foreach ($get['pages'] as $pages) {
                $pages = $pages;
            }
        }
        //CONFIG PAGINATION

        $config['uri_segment'] = 4;
        $config['display_pages'] = FALSE;
        $config['full_tag_open'] = '<ul class="pagination" style="float:left; display:inline; margin-left:15px; margin-top:-10px;">';
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
        $config['total_rows'] = $pages;
        $config['per_page'] = 1;
        $config['base_url'] = site_url() . 'store/listdesc/'.$id;
        $config['use_page_numbers'] = TRUE;




        $this->pagination->initialize($config);



        $this->mysmarty->assign('store', $sc);

        //GET ALL THE PRODUCTS TO SHOW ON BANNER SLIDER ON WEBSITE
        $this->db->where("show_banner", 1);
        $this->db->where("price_ch1 <>", '0.00');
        $this->db->where("qn_ch2 <>", 0);
        $ebay_banner = $this->db->get("ebay")->result_array();

        $this->db->where("wsc_title <>", "ACTIONS");
        $this->db->where("wsc_title <>", "NOT LISTED");
        $this->db->order_by("wsc_title");
        $categories = $this->db->get("warehouse_sku_categories")->result_array();

        $this->db->where("wsc_parent <>", 0);
        $this->db->order_by("wsc_title");
        $categories_child = $this->db->get("warehouse_sku_categories")->result_array();



        $this->db->where("wsc_id", $id);
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


        $this->mysmarty->assign('ebay_banner', $ebay_banner);
        $this->mysmarty->assign('ebay', $get['result']);
        $this->mysmarty->assign('pagination_links', $this->pagination->create_links());
        $this->mysmarty->assign('pages', $get['pages']);

        $this->mysmarty->assign('page', (int)$page);
        $this->mysmarty->assign('catid', (int)$id);
        $this->mysmarty->assign('ordering', $ordering);

        $this->mysmarty->assign('categories', $categories);
        $this->mysmarty->assign('categories_child', $categories_child);
        $this->mysmarty->assign('categories_top', $cat_tree_top);
        $this->mysmarty->assign('dad_title', $dad_title);
        $this->mysmarty->assign('dad_id', $dad_id);
        $this->mysmarty->assign('productview', 'homeebaylisting');
        $this->mysmarty->assign('imgebaypath', $this->config->config['wwwpath']['imgebay']);
        $this->mysmarty->view('welcome/welcome_main.html');

    }

    function NotFound()
    {
        $this->mysmarty->assign('notfound', '1');
        $this->mysmarty->assign('imgproductpath', $this->config->config['wwwpath']['imgproducts']);
        $this->mysmarty->assign('specials', $this->Product_model->GetTopSpecials());
        $this->mysmarty->assign('innerview', 'home');
        $this->mysmarty->view('welcome/welcome_main.html');
    }


    function Menu($place = '', $level1 = '', $level2 = '')
    {
        if ($level1 == '') {
            Redirect("/");
            break;
            exit();
        }
        if ((int)$place == 0) {
            Redirect("/");
            break;
            exit();
        }

        $this->l1 = CleanInput($level1);
        $this->submenu = $this->Menus_model->GetSubMenu((int)$place, '1', $this->l1);
        $this->mysmarty->assign('l1string', $this->l1);
        $this->mysmarty->assign('submenu', array((int)$place => $this->submenu));

        if ($level2 != '') {
            $this->l2 = CleanInput($level2);
            $this->subsubmenu = $this->Menus_model->GetSubMenu((int)$place, '2', $this->l2);
            $this->mysmarty->assign('l2string', $this->l2);
            $this->mysmarty->assign('subsubmenu', array((int)$place => $this->subsubmenu));
        }

        $this->mysmarty->assign('innerview', 'menu');
        $this->mysmarty->view('welcome/welcome_main.html');

    }

    function Item($s_sefurl)
    {
        $this->sefstring = CleanInput($s_sefurl);

        $this->item = $this->Menus_model->GetItem($this->sefstring);

        if ($this->item['s_link'] != '') {

            $this->urlparts = parse_url($this->item['s_link']);
            $this->urlparts['home'] = $this->urlparts['scheme'] . '://' . $this->urlparts['host'];

            if ($this->urlparts['home'] == $this->config->config['base_url']) {
                header('Location:' . $this->config->config['base_url'] . $this->urlparts['path']);
                //echo ($this->config->config['base_url'].'/'.$this->config->config['language_abbr'].$this->urlparts['path']);
                exit;
            } else {
                header('Location:' . $this->item['s_link']);
            }
            exit;
        }

        $this->mysmarty->assign('item', $this->item);

        switch ($this->item['s_level']) {

            case 2:

                $this->subsubmenu[$this->item['s_menu']] = $this->Menus_model->GetSubMenu($this->item['s_menu'], '2', '', $this->item['s_levelparentid']);
                $this->mysmarty->assign('l3string', $this->sefstring);
                $this->mysmarty->assign('subsubmenu', $this->subsubmenu);

                $this->l1parent = $this->Menus_model->GetParent((int)$this->item['s_levelparentid']);
                $this->submenu[$this->item['s_menu']] = $this->Menus_model->GetSubMenu($this->item['s_menu'], '1', '', $this->l1parent['s_levelparentid']);
                $this->mysmarty->assign('l2string', $this->l1parent['s_seourl']);
                $this->mysmarty->assign('submenu', $this->submenu);

                $this->l0parent = $this->Menus_model->GetParent((int)$this->l1parent['s_levelparentid']);
                $this->mysmarty->assign('l1string', $this->l0parent['s_seourl']);
                break;
            case 1:
                $this->mysmarty->assign('l1string', $this->Menus_model->GetParentSef((int)$this->item['s_levelparentid']));
                $this->mysmarty->assign('l2string', $this->sefstring);

                $this->mysmarty->assign('submenu', array($this->item['s_menu'] => $this->Menus_model->GetSubMenu($this->item['s_menu'], '1', '', $this->item['s_levelparentid'])));

                break;
            default:
                $this->mysmarty->assign('l1string', $this->sefstring);
        }


        $this->mysmarty->assign('innerview', 'viewopen');
        $this->mysmarty->view('welcome/welcome_main.html');


    }


//////////////////////// Add-ons

    function Search($string = '')
    {
        if ($string == '') {
            $this->checklength = strlen($this->input->post('find'));
            if (((int)$this->checklength > 2) && ($this->input->post('find') != 'Search...')) {
                $this->searchtype = (int)$this->input->post('type', TRUE);

                if ($this->searchtype == '2') {
                    $this->mysmarty->assign('search', $this->Menus_model->Search(si(htmlspecialchars($this->input->post('find', TRUE)))));
                    $this->mysmarty->assign('innerview', 'search');
                } else {
                    $this->mysmarty->assign('productlist', $this->Product_model->Search(si(htmlspecialchars($this->input->post('find', TRUE)))));

                    $this->mysmarty->assign('imgproductpath', $this->config->config['wwwpath']['imgproducts']);
                    $this->mysmarty->assign('productview', 'list');
                }

                $this->mysmarty->assign('searchstring', si($this->input->xss_clean(htmlspecialchars($this->input->post('find', TRUE)))));
                $this->mysmarty->assign('searchtype', $this->searchtype);

                $this->mysmarty->view('welcome/welcome_main.html');
            } else   Redirect('');
        } else {
            if (strlen($string) > 2) {
                $string = si($string);
                //$string = str_replace("-", " ", $string);
                $this->mysmarty->assign('search', $this->Menus_model->Search($this->input->xss_clean($string)));
                $this->mysmarty->assign('innerview', 'search');
                $this->mysmarty->assign('searchstring', $string);
                $this->mysmarty->assign('searchtype', 2);
                $this->mysmarty->view('welcome/welcome_main.html');
            } else Redirect('');
        }
    }

    function Faq($action = '', $id = '')
    {

        if ($action == 'View') {
            if ((int)$id == 0) {
                Redirect("Faq/Categories");
                break;
                exit();
            }
            $this->faq = $this->Menus_model->GetFaq((int)$id);

            $this->faqcatopen = $this->Menus_model->GetOpenFaqCat((int)$this->faq['f_cat']);
            $this->mysmarty->assign('faqcatopen', $this->faqcatopen);
            $this->mysmarty->assign('faq', $this->faq);
            $this->mysmarty->assign('innerview', 'faqview');

        } elseif ($action == 'Categories') {
            $this->mysmarty->assign('faqcat', $this->Menus_model->ListFaqCategories());
            $this->mysmarty->assign('innerview', 'faqcat');

        } else {
            if ((int)$id == 0) {
                Redirect("Faq/Categories");
                break;
                exit();
            }
            $this->faqcatopen = $this->Menus_model->GetOpenFaqCat((int)$id);
            $this->faqlist = $this->Menus_model->ListFaq((int)$id);
            $this->mysmarty->assign('faqlist', $this->faqlist);
            $this->mysmarty->assign('faqcatopen', $this->faqcatopen['f_cattitle']);
            $this->mysmarty->assign('innerview', 'faqlist');

        }

        $this->mysmarty->view('welcome/welcome_main.html');

    }

    function News($action = '', $id = '')
    {

        if ($action == 'View') {

            $this->mysmarty->assign('newsview', $this->Menus_model->GetNews((int)$id));
            $this->mysmarty->assign('innerview', 'newsview');

            $this->mysmarty->view('welcome/welcome_main.html');
        } else {

            $this->mysmarty->assign('newslist', $this->Menus_model->ListNews());
            $this->mysmarty->assign('innerview', 'newslist');

            $this->mysmarty->view('welcome/welcome_main.html');
        }
    }


    function Newsletter()
    {
        //$this->lang->load($this->config->config['lang_uri_abbr'][$this->config->config['language_abbr']].'/email');
        $this->load->library('form_validation');
        $this->fieldnames = array(
            'bg' => array(
                'email' => 'Е-мейл адрес'
            ),
            'en' => array(
                'email' => 'Email Address'
            )
        );

        $this->messages = array(
            'bg' => array(
                'suc-unsc' => 'Успешно оптисан',
                'eml-no-reg' => 'Този е-мейл не е регистриран',
                'eml-reg' => 'Този е-мейл вече е регистриран',
                'suc-sucr' => 'Успешно записан'
            ),
            'en' => array(
                'suc-unsc' => 'Succesfully Unsubscribed'
            ,
                'eml-no-reg' => 'E-Mail is not registered',
                'eml-reg' => 'E-Mail is already registered',
                'suc-sucr' => 'Succesfully Subscribed'
            )
        );

        $this->form_validation->set_rules('newsletter', $this->fieldnames[$this->config->config['language_abbr']]['email'], 'trim|required|min_length[7]|valid_email|xss_clean');
        if ($this->form_validation->run() == FALSE) {

            $this->mysmarty->assign('errors', $this->form_validation->_error_array);
            $this->mysmarty->assign('innerview', 'newsletter');
            $this->mysmarty->assign('imgproductpath', $this->config->config['wwwpath']['imgproducts']);
            $this->mysmarty->assign('specials', $this->Product_model->GetTopSpecials());


            $this->mysmarty->view('welcome/welcome_main.html');
            exit();

        } else {

            $this->checkemail = $this->Menus_model->FindNewsletterEmail($this->form_validation->set_value('newsletter'));
            if ($this->input->post('unsubscribe') == 'on') {
                if ($this->checkemail) {
                    $this->Menus_model->UnsubscribeEmail((int)$this->checkemail['n_id']);

                    $this->_DoForm($this->form_validation->set_value('newsletter'), 'newsletterunsubscribe');

                    $this->mysmarty->assign('success', $this->messages[$this->config->config['language_abbr']]['suc-unsc']);
                    $this->mysmarty->assign('innerview', 'newsletter');


                    $this->mysmarty->view('welcome/welcome_main.html');
                    exit();
                } else {

                    $this->mysmarty->assign('errors', array('newsletter' => $this->messages[$this->config->config['language_abbr']]['eml-no-reg']));
                    $this->mysmarty->assign('innerview', 'newsletter');


                    $this->mysmarty->view('welcome/welcome_main.html');
                    exit();
                }

            }

            if ($this->checkemail) {
                $this->mysmarty->assign('errors', array('newsletter' => $this->messages[$this->config->config['language_abbr']]['eml-reg']));
                $this->mysmarty->assign('innerview', 'newsletter');


                $this->mysmarty->view('welcome/welcome_main.html');
                exit();
            }

            $this->Menus_model->SubscribeEmail($this->form_validation->set_value('newsletter'));

            $this->_DoForm($this->form_validation->set_value('newsletter'), 'newslettersubscribe');

            $this->mysmarty->assign('success', $this->messages[$this->config->config['language_abbr']]['suc-sucr']);
            $this->mysmarty->assign('innerview', 'newsletter');


            $this->mysmarty->view('welcome/welcome_main.html');
            exit();
        }
    }

    function Unsubscribe($code = '')
    {
        if ((int)$code == '') {
            Redirect("");
            exit;
        }

        $this->messages = array(
            'bg' => array(
                'suc-unsc' => 'Успешно оптисан',
                'eml-no-reg' => 'Този е-мейл не е регистриран',
                'eml-reg' => 'Този е-мейл вече е регистриран',
                'suc-sucr' => 'Успешно записан'
            ),
            'en' => array(
                'suc-unsc' => 'Succesfully Unsubscribed'
            ,
                'eml-no-reg' => 'E-Mail is not registered',
                'eml-reg' => 'E-Mail is already registered',
                'suc-sucr' => 'Succesfully Subscribed'
            )
        );

        $this->checknumber = $this->Menus_model->FindNewsletterCode($code);

        if ($this->checknumber) {
            $this->Menus_model->UnsubscribeEmail((int)$this->checknumber['n_id']);

            $this->_DoForm($this->checknumber['n_email'], 'newsletterunsubscribe');

            $this->mysmarty->assign('success', $this->messages[$this->config->config['language_abbr']]['suc-unsc']);
            $this->mysmarty->assign('innerview', 'newsletter');


            $this->mysmarty->view('welcome/welcome_main.html');
            exit();
        } else {
            $this->mysmarty->assign('errors', array('newsletter' => $this->messages[$this->config->config['language_abbr']]['eml-no-reg']));
            $this->mysmarty->assign('innerview', 'newsletter');


            $this->mysmarty->view('welcome/welcome_main.html');
            exit();
        }


    }

    function ContactReply($code = '', $id = '')
    {
        if (strlen($code) != 50) {
            $data = array('msg_title' => 'Contact Reply Error @ ' . FlipDateMail(CurrentTime()),
                'msg_body' => 'Contact Reply Code not 50 chars for ' . (int)$id,
                'msg_date' => CurrentTime()
            );
            GoMail($data, $this->config->config['support_email']);
            exit();
        }
        if ((int)$id == 0) {
            $data = array('msg_title' => 'Contact Reply Error @ ' . FlipDateMail(CurrentTime()),
                'msg_body' => 'Contact Reply ID = 0 for ' . (int)$id,
                'msg_date' => CurrentTime()
            );
            GoMail($data, $this->config->config['support_email']);
            exit();
        }

        $this->confirmcode = '';
        $this->cleanedcode = $this->input->xss_clean($code);
        $this->cleanedcode = ereg_replace("[^A-Za-z0-9]", "", $this->cleanedcode);
        $this->codelength = strlen($this->cleanedcode);
        $this->confirmcode = $this->cleanedcode;


        $success = false;
        $msg = '';
        if (isset($_POST['msg']) && $_POST['msg'] != '') {
            $cdata['f_msg'] = si(addslashes($this->input->xss_clean($this->input->post('msg', TRUE))));

            $cdata['f_id'] = (int)$id;
            $cdata['f_owner'] = 'cust';
            $cdata['f_time'] = CurrentTime();

            $this->db->insert('form_contact_comm', $cdata);
            $this->_DoForm($cdata, 'contactreply');

            $success = TRUE;
        }

        $this->load->model('Myforms_model');
        $data = $this->Myforms_model->GetFrontForm($this->confirmcode, (int)$id);
        $matched = false;
        if ($data) $matched = true;

        $this->mysmarty->assign('view', $data);
        $this->mysmarty->assign('matched', $matched);
        $this->mysmarty->assign('msg', $msg);
        $this->mysmarty->assign('success', $success);
        $this->mysmarty->assign('code', $this->confirmcode);
        $this->mysmarty->assign('id', (int)$id);
        $this->mysmarty->assign('comm', $this->Myforms_model->GetFormReplies((int)$id));

        $this->mysmarty->assign('innerview', 'contactreply');
        $this->mysmarty->view('welcome/welcome_main.html');

    }

    function Contact()
    {

        $uri = explode('/', $_SERVER['HTTP_REFERER']);
        if ($uri[2] == 'www.ebay.com' || $uri[2] == 'ebay.com') {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }
        $this->load->library('form_validation');
        $this->load->model('Captcha_model');

        $this->fieldnames = array(
            'bg' => array(
                'name' => 'Имена',
                'email' => 'Е-мейл адрес',
                'body' => 'Съобшение',
                'Code' => 'Код'
            ),
            'en' => array(
                'name' => 'Names',
                'email' => 'Е-mail address',
                'body' => 'Message',
                'Code' => 'code'
            )
        );

        $this->mysmarty->assign('tmactive', 'contact');

        if (!isset($this->session->userdata['user_id'])) {
            $this->form_validation->set_rules('name', $this->fieldnames[$this->config->config['language_abbr']]['name'], 'trim|required|min_length[5]|xss_clean');
            $this->form_validation->set_rules('email', $this->fieldnames[$this->config->config['language_abbr']]['email'], 'trim|required|min_length[7]|valid_email|xss_clean');
        }

        $this->form_validation->set_rules('body', $this->fieldnames[$this->config->config['language_abbr']]['body'], 'trim|required|xss_clean');


        $this->captcha = $this->Captcha_model->CheckCaptcha();

        if (($this->form_validation->run() == FALSE) || !$this->captcha) {
            $this->inputdata = array(
                'name' => $this->input->post('name', TRUE),
                'email' => $this->input->post('email', TRUE),
                'body' => $this->input->post('body', TRUE)
            );

            $this->Captcha_model->DoCaptcha();

            if ((!$this->captcha) && (count($_POST) > 0)) {
                if ($this->config->config['language_abbr'] == 'en') $this->mysmarty->assign('errorcaptcha', 'Please specify if you are human');
                else $this->mysmarty->assign('errorcaptcha', 'Невалиден код');

            }
            $this->mysmarty->assign('pagedata', $this->Menus_model->GetItem('Contact-Us'));
            $this->mysmarty->assign('inputdata', $this->inputdata);
            $this->mysmarty->assign('errors', $this->form_validation->_error_array);
            $this->mysmarty->assign('innerview', 'contact');

            $this->mysmarty->view('welcome/welcome_main.html');
            exit();

        } else {
            $this->load->helper('arithmetic');
            $this->insertdata['code'] = rand_string(50);

            if (isset($this->session->userdata['user_id'])) {

                $this->usercdata = $this->Menus_model->GetUserContactData((int)$this->session->userdata['user_id']);

                $this->formdata = array(
                    'user_id' => (int)$this->usercdata['user_id'],
                    'names' => $this->usercdata['fname'] . ' ' . $this->usercdata['lname'],
                    'email' => $this->usercdata['email'],
                    'date' => CurrentTime(),
                    'contents' => $this->form_validation->set_value('body'),
                    'code' => $this->insertdata['code']

                );
            } else {
                $this->formdata = array(
                    'user_id' => 0,
                    'names' => si($this->form_validation->set_value('name')),
                    'email' => si($this->form_validation->set_value('email')),
                    'date' => CurrentTime(),
                    'contents' => si($this->form_validation->set_value('body')),
                    'code' => $this->insertdata['code']
                );
            }

            $this->_DoForm($this->formdata, 'contact');
            $this->mysmarty->assign('innerview', 'contactok');
            $this->mysmarty->view('welcome/welcome_main.html');
            exit();
        }
    }

    function Sitemap()
    {
        $this->mysmarty->assign('content', $this->Menus_model->GetOtherSitemapData());
        $this->mysmarty->assign('innerview', 'sitemap');
        $this->mysmarty->view('welcome/welcome_main.html');
    }

    function Poll($action = '')
    {

        if ($action == 'Submit') {
            $this->answer = (int)$this->input->post('pollanswer');

            if ($this->answer == '0') {
                $this->session->set_flashdata('pollerror', 'Please select an answer...');
                Redirect("/Poll/Error");
                exit();
            }

            $this->allowed = $this->Menus_model->CountPollAnswers();

            if (count($this->allowed) < 2) {
                Redirect("/");
                break;
                exit();
            }

            if ($this->allowed[$this->answer] == 1) {
                $this->activepoll = $this->Menus_model->GetActivePollID();
                $this->alreadyanswered = $this->Menus_model->CheckIfHasAnswered($this->input->ip_address(), $this->activepoll['p_id']);

                if (!$this->alreadyanswered) {
                    $this->Menus_model->InsertPollAnswer($this->activepoll['p_id'], CurrentTime(), $this->answer, $this->input->ip_address());
                    Redirect("/Poll/ThankYou");
                    exit();

                } else {
                    if ($this->config->config['language_abbr'] == 'bg') {
                        $this->session->set_flashdata('pollerror', 'Вече сте отговорили на тази анкета');
                    } else {
                        $this->session->set_flashdata('pollerror', 'You have already taken this poll');
                    }
                    Redirect("/Poll/Error");
                    exit();

                }
            } else {
                if ($this->config->config['language_abbr'] == 'bg') {
                    $this->session->set_flashdata('pollerror', 'Невалиден отговор');
                } else {
                    $this->session->set_flashdata('pollerror', 'Unavailable answer');
                }

                Redirect("/Poll/Error");
                exit();
            }
        } elseif ($action == 'Error') {
            if ($this->session->flashdata('pollerror') == '') {
                Redirect("");
                break;
                exit();
            }
            $this->mysmarty->assign('innerview', 'pollresult');
            $this->mysmarty->assign('pollerror', $this->session->flashdata('pollerror'));
            $this->mysmarty->assign('polltype', 'error');
            $this->mysmarty->view('welcome/welcome_main.html');
        } else {

            $results = $this->Menus_model->GetPollResults();
            $data['options']['1'] = $results['actpoll']['opt1'];
            $data['options']['2'] = $results['actpoll']['opt2'];
            $data['options']['3'] = $results['actpoll']['opt3'];
            $data['options']['4'] = $results['actpoll']['opt4'];
            $data['options']['5'] = $results['actpoll']['opt5'];
            $data['options']['6'] = $results['actpoll']['opt6'];
            $data['options']['7'] = $results['actpoll']['opt7'];
            $data['options']['8'] = $results['actpoll']['opt8'];
            $data['options']['9'] = $results['actpoll']['opt9'];
            $data['answers'] = $results['answers'];

            $items = '';
            $values = '';
            foreach ($data['answers'] as $key => $value) {
                if ($data['options'][$key] != '') $items .= "|" . $data['options'][$key];
            }

            $values = implode(",", $data['answers']);
            $maxvalues = max($data['answers']);

            $this->mysmarty->assign('pollchart', array('items' => $items, 'values' => $values, 'max' => $maxvalues));

            //$this->mysmarty->assign('chartres', $data);
            $this->mysmarty->assign('innerview', 'pollresult');
            $this->mysmarty->assign('polltype', 'ok');
            $this->mysmarty->view('welcome/welcome_main.html');
        }

    }


//////////////////////////////////////////

    function _DoForm($data, $type = '')
    {
        switch ($type) {
            case 'contactreply':
                $this->msg_data = array('msg_title' => 'Latronics Reply to contact form ' . $data['f_id'] . ' @ ' . FlipDateMail(CurrentTime()),
                    'msg_body' => '<a href="' . Site_url() . 'Myforms/View/' . (int)$data['f_id'] . '" target="_blank">' . Site_url() . 'Myforms/View/' . (int)$data['f_id'] . '</a>',
                    'msg_date' => CurrentTime()
                );
                GoMail($this->msg_data, '', 'noreply@la-tronics.com');

                break;
            case 'contact':
                $this->formcontactid = $this->Menus_model->SaveContactForm($data);

                $this->msg_data = array('msg_title' => 'Latronics Contact form data from ' . $data['names'] . ' @ ' . FlipDateMail($data['date']),
                    'msg_body' => 'From URL: ' . $_SERVER['HTTP_REFERER'] . '<Br>' . $data['contents'],
                    'msg_date' => $data['date']
                );

                $this->mailid = 17;
                GoMail($this->msg_data, '', $data['email']);
                $this->msg_data['msg_body'] = 'Please see <a href="' . Site_url() . 'Myforms/View/' . $this->formcontactid . '">form data</a>';
                break;
            case 'newsletterunsubscribe':
                $this->msg_data = array('msg_title' => 'Newsletter unsubscription @ ' . FlipDateMail(CurrentTime()),
                    'msg_body' => 'User ' . $data . ' has unsubscribed from newsletter',
                    'msg_date' => CurrentTime()
                );

                $this->mailid = 18;
                GoMail($this->msg_data, '', $data);
                break;

            case 'newslettersubscribe';
                $this->msg_data = array('msg_title' => 'Newsletter subscription @ ' . FlipDateMail(CurrentTime()),
                    'msg_body' => 'User ' . $data . ' has subscribed to newsletter',
                    'msg_date' => CurrentTime()
                );
                $this->mailid = 19;
                GoMail($this->msg_data, '', $data);
                break;

            default:
        }


        $this->Menus_model->InsertHistoryData($this->msg_data);

    }

    function _CartTotal()
    {
        $this->data = $this->session->userdata['cart'];
        $total = 0;
        foreach ($this->data as $key => $value) {
            $total = $total + ((int)$value['quantity'] * (float)$value['p_price']);
        }
        return (float)$total;
    }
}
