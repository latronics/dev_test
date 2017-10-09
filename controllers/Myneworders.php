<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Myneworders extends Controller {

    public $admin365;

    function Myneworders() {
        parent::__construct();




        //exit('Commiting update, please wait  1-2 mins...');
        parent::Controller();

        $this->load->model('Mywarehouse_model');
        $this->load->model('Auth_model');

        if ($this->router->method != 'Comm') {
            $this->Auth_model->VerifyAdmin();
            $this->Auth_model->CheckWarehouse();

            $showparts = $this->session->userdata('showparts');
            $showparents = $this->session->userdata('showparents');

            if (!$showparts && !$showparents) {
                $this->session->set_userdata('showparents', 1);
                $this->session->set_userdata('showparts', 1);
                $this->mysmarty->assign('session', $this->session->userdata);
            }



            $this->mysmarty->assign('session', $this->session->userdata);
            $this->mysmarty->assign('action', $this->session->flashdata('action'));
            $this->mysmarty->assign('error_msg', $this->session->flashdata('error_msg'));
            $this->mysmarty->assign('success_msg', $this->session->flashdata('success_msg'));
            $this->mysmarty->assign('area', 'Warehouse');
            $this->mysmarty->assign('hot', TRUE);
            $this->mysmarty->assign('newlayout', TRUE);
            $this->mysmarty->assign('jslog', TRUE);
        }
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

    function _savesession($data = '') {
        if (!is_array($data))
            exit('Unable to write session - Data is not Array');
        $arr = json_encode($data);
        $name = mktime() . (int) $this->session->userdata['admin_id'] . '.txt';
        $this->load->helper('file');
        if (!write_file($this->config->config['pathtosystem'] . '/' . $this->config->config['pathtoapplication'] . '/sess/' . $name, $arr)) {
            exit('Unable to write session - File write error');
        }
        return $name;
    }

    function _logallpost() {
        $name = CurrentTime() . '_' . (int) $this->session->userdata['admin_id'] . '_' . str_replace(' Mywarehouse ', '', str_replace('/', ' ', $_SERVER['REQUEST_URI'])) . '.txt';
        if ($_POST)
            file_put_contents($this->config->config['pathtosystem'] . '/' . $this->config->config['pathtoapplication'] . '/sess/post/' . $name, urldecode(file_get_contents("php://input")));
    }

    function CustomerSearch() {

        $admin365 = $this->load->database("365admin", true);

        //QUERY TO SELECT CUSTOMERS TO FILL JQUERY AUTOCOMPLETE
        $admin365->select("client_id, client_name");
        $admin365->like("client_name", $this->input->post("term"), "after");
        $admin365->order_by("client_name", "asc");
        $admin365->limit("10");
        $client_autocomplete = $admin365->get("ip_clients")->result_object();

        foreach ($client_autocomplete as $client_autocomplete) {

            $array[] = array(
                "value" => $client_autocomplete->client_id,
                "label" => $client_autocomplete->client_name
            );
        }
        echo json_encode($array);
    }

    public function RemoveWalking() {
        $data = array(
            "user_id" => $this->session->userdata("user_id"),
            "walking" => 0
        );
        $admin365->where("user_id", $this->session->userdata("user_id"));
        $admin365->update("ip_client_walking_aux", $data);
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
            $this->OrderAccounting($id, false, true);
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
        $o['shipped'] = (float) $this->input->post('shipped', TRUE);
        $o['wholeprice'] = (float) $this->input->post('wholeprice', TRUE);
        $o['notes'] = $this->input->post('notes', TRUE);
        $o['subchannel'] = $this->input->post('subchannel', TRUE);
        $o['sc_id'] = $this->input->post('sc_id', TRUE);

        if ((int) $id > 0 && !$_POST)
            $o = $dbo;
        $this->mysmarty->assign('order', $o);


        $this->load->library('form_validation');
        $this->form_validation->set_rules('buyer', 'Buyer', 'trim|required|xss_clean');

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

        //CALL 365ADMIN DB
        $admin365['admin365'] = $this->load->database('365admin', TRUE);


        //PASSING ITEMS TO VIEW
        $admin365['admin365']->where("invoice_number", "W" . $id);
        $admin365['admin365']->join("ip_invoices", "ip_invoices.invoice_id = ip_invoice_items.invoice_id");
        $items = $admin365['admin365']->get("ip_invoice_items")->result_object();

        //PASSING INVOICE DATA TO VIEW
        $admin365['admin365']->select(" *, ip_invoices.store, ip_invoices.invoice_id, ip_clients.client_id");
        $admin365['admin365']->where("ip_invoices.invoice_number", "W" . $id);
        $admin365['admin365']->join("ip_clients", "ip_clients.client_id = ip_invoices.client_id");
        $admin365['admin365']->join("ip_invoice_amounts", "ip_invoice_amounts.invoice_id = ip_invoices.invoice_id");
        $admin365['admin365']->join("ip_payments", "ip_payments.invoice_id = ip_invoices.invoice_id", "left");
        $admin365['admin365']->join("ip_invoice_items", "ip_invoice_items.invoice_id = ip_invoices.invoice_id", "left");

        $invoice_data = $admin365['admin365']->get("ip_invoices")->result_object();
        $invoice_discount = 0;

        if ($invoice_data != null) {
            foreach ($items as $items_tocalc) {

                $invoice_discount += $items_tocalc->item_discount_amount * $items_tocalc->item_quantity;
            }

            $admin365['items'] = $items;
            $admin365['invoice_id'] = $id;
            $admin365['invoice'] = $invoice_data[0];
            $admin365['payment_methods'] = $admin365['admin365']->get("ip_payment_methods")->result_object();
            $admin365['stores'] = $admin365['admin365']->get("ip_stores")->result_object();
            $admin365['invoice_discount'] = $invoice_discount;
            $this->mysmarty->assign('date', CurrentTime());
            $this->mysmarty->assign('hot', true);
            $this->mysmarty->assign('noenter', $noenter);
            $this->load->view('mywarehouse/new_order/orders', $admin365);
        } else {
            $this->load->database('default', TRUE);
            $this->db->where("woid", $id);
            $client_w = $this->db->get("warehouse_orders")->result_object();
            $this->load->database('365admin', TRUE);
            $admin365['admin365']->where("client_id", $client_w[0]->client_id);
            $client_data = $admin365['admin365']->get("ip_clients")->result_object();
            $admin365['invoice'] = $client_data[0];
            $admin365['invoice_id'] = $id;
            $admin365['payment_methods'] = $admin365['admin365']->get("ip_payment_methods")->result_object();
            $admin365['stores'] = $admin365['admin365']->get("ip_stores")->result_object();
            $this->mysmarty->assign('date', CurrentTime());
            $this->mysmarty->assign('hot', true);
            $this->mysmarty->assign('noenter', $noenter);
            $this->load->view('mywarehouse/new_order/orders', $admin365);
        }
    }

    function UpdateCustomers()
    {
        $this->load->database('365admin', TRUE);
        $client_id =  $this->input->post("client_id");
        $client_data = array(
            "client_name" => $this->input->post("client_name"),
            "client_address_1" => $this->input->post("billing_address"),
            "client_city" => $this->input->post("billing_city"),
            "client_state" => $this->input->post("billing_state"),
            "client_zip" => $this->input->post("billing_zip"),
            "client_country" => $this->input->post("billing_country"),
            "shipping_address" => $this->input->post("shipping_address"),
            "shipping_city" => $this->input->post("shipping_city"),
            "shipping_state" => $this->input->post("shipping_state"),
            "shipping_zip" => $this->input->post("shipping_zip"),
            "shipping_country" => $this->input->post("shipping_country"),
            "client_phone" => $this->input->post("client_phone"),
            "client_email" => $this->input->post("client_email")
        );
        $this->db->where("client_id", $client_id);
        if($this->db->update("ip_clients", $client_data))
        {
            echo 1;
        }
        else
        {
            echo 0;
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
        }
        else {
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
        $exact = array();
        if ($res) {
            $this->mysmarty->assign('selection', $res);

            foreach ($res as $k => $w) {
                if (is_array($from)) {
                    foreach ($from as $f) {
                        if (trim($f) != '' && trim($f) == $w['bcn'] && $sales == 4 && $w['sold_id'] == 0)
                            $exact[] = $w['wid'];
                    }
                }
            }
        }
        $this->Myseller_model->assignchannels();
        //if (!isset($_POST['id'])) echo json_encode($this->mysmarty->fetch('myseller/availbcn.html'));
        //else echo $this->mysmarty->fetch('myseller/availbcn.html');

        echo json_encode(array('html' => $this->mysmarty->fetch('mywarehouse/new_order/availbcn.html'), 'exact' => $exact));
    }

    function BCNListingAttach() {
        if (isset($_POST['wid']) && isset($_POST['listingid']) && isset($_POST['remove'])) {
            $wid = $this->Mywarehouse_model->getbcnattachdata((int) $_POST['wid']);
            $title = $this->Mywarehouse_model->GetListingTitleAndCondition((int) $_POST['listingid'], true);
            if ($wid) {

                if ((int) $_POST['remove'] == 1) {
                    $data['prevlistingid'] = $wid['listingid'];
                    $data['listingid'] = 0;
                    if ($wid['status'] != 'Scrap')
                        $data['status'] = 'Not Listed';
                    $data['listed'] = '';
                    $data['listed_date'] = '';

                    $actionqn = 1;
                }
                else {
                    $data['listingid'] = (int) $_POST['listingid'];
                    $data['status'] = 'Listed';
                    if ($title && $title != '')
                        $data['title'] = $title;
                    $data['listed'] = 'eBay ' . (int) $_POST['listingid'];
                    $data['listed_date'] = CurrentTime();


                    $actionqn = -1;
                }
                foreach ($data as $k => $v) {
                    if ($wid[$k] != $v)
                        $this->Auth_model->wlog($wid['bcn'], $wid['wid'], $k, $wid[$k], $v);
                }
                if ($title != $wid['title']) {
                    $this->Auth_model->wlog($wid['bcn'], $wid['wid'], 'title', $wid['title'], $title);
                    $data['title'] = $title;
                }
                $data['status_notes'] = 'Changed from "' . $wid['status'] . '" - Listing ' . $data['listingid'] . ' by ' . $this->session->userdata['ownnames'];
                //if (trim($wid['status_notes']) == '')  = $statusnotes;
                //else $data['status_notes'] = $statusnotes.' | '.$wid['status_notes'];

                $this->db->update('warehouse', $data, array('wid' => (int) $_POST['wid']));


                $this->Myseller_model->runAssigner((int) $_POST['listingid'], $actionqn);

                echo $this->_getbcnsnippet((int) $_POST['listingid'], false, 'listing');
            } else
                echo 0;
        } else
            echo 0;
    }

    function OrderAccounting($id = 0, $return = false, $display = false) {
        if ((int) $id == 0) {
            echo '';
            exit();
        }
        //$_POST['soldid'] =14;

        $sql = 'SELECT wid, oldbcn, bcn, title, status, generic, waid, channel, sold_id, sold_subid, paid, shipped_actual, sellingfee, sold_date, sold, location, ordernotes FROM warehouse WHERE `deleted` = 0 AND `nr` = 0 AND `vended` != 0 AND `channel` = 4 AND `sold_id` = ' . (int) $id;
        $q = $this->db->query($sql);
        $list['data'] = array();

        if ($q->num_rows() > 0) {
            $list['data'] = $q->result_array();
        }

        foreach ($list['data'] as $k => $l) {
            $h[$k] = array('wid' => $l['wid'], 'bcn' => $l['bcn']);
        }


        $sesfile = $this->_savesession(array('accrel' => $h, 'accord' => (int) $id));
        $this->session->set_userdata(array('sessfile' => $sesfile));



        $loaddata = '';
        $adms = $this->Mywarehouse_model->GetAdminList();
        foreach ($list['data'] as $k => $l) {
            if (trim($l['audit']) != '')
                $audit = 1;
            else
                $audit = 0;

            /* if ($this->session->userdata['type'] == 'master' || ($this->session->userdata['type'] != 'master' && $this->session->userdata['accounting'] == 1))
              {

              $returndata[] = array("<a target=\"_blank\" href=\"/Mywarehouse/gotobcn/".cstr($l['bcn'])."\"><img src=\"/images/admin/table.png\" border=\"0\"></a><a target=\"_blank\" href=\"/Myebay/Search/".cstr($l['listingid'])."\"><img src=\"/images/admin/b_search.png\" border=\"0\"></a><a target=\"_blank\" href=\"/Myebay/ShowOrder/".cstr($l['sold_id'])."/".cstr($l['channel'])."\"><img src=\"/images/admin/shoppingbag.png\" border=\"0\"></a>", cstr($l['bcn']), cstr($l['oldbcn']), cstr($l['title']), cstr($l['location']),cstr($audit),cstr($l['status']),cstr($l['listed']),cstr($l['listed_date']),cstr($l['sold_date']),cstr($l['sold']), cstr($l['sold_id']), cstr($l['soldqn']),cstr($l['paid']),cstr($l['shipped']), cstr($l['shipped_actual']),cstr($l['shipped_inbound']),cstr($l['ordernotes']),cstr($l['sellingfee']), cstr($l['netprofit']),cstr($l['cost']),cstr($l['aupdt']));
              }
              else
              { */

            $returndata[] = array("<a target=\"_blank\" href=\"/Mywarehouse/gotobcn/" . cstr($l['bcn']) . "\"><img src=\"/images/admin/table.png\" border=\"0\"></a><a target=\"_blank\" href=\"/Myebay/Search/" . cstr($l['listingid']) . "\"><img src=\"/images/admin/b_search.png\" border=\"0\"></a><a target=\"_blank\" href=\"/Myebay/ShowOrder/" . cstr($l['sold_id']) . "/" . cstr($l['channel']) . "\"><img src=\"/images/admin/shoppingbag.png\" border=\"0\"></a>", cstr($l['bcn']), cstr($l['oldbcn']), cstr($l['title']), cstr($l['status']), cstr($l['paid']), cstr($l['shipped_actual']), cstr($l['sellingfee']), cstr($l['sold_date']), cstr($l['sold']), cstr($l['location']), cstr($l['ordernotes']));
            //}
        }
        if (count($list['data']) > 0) {
            foreach ($returndata as $kr => $r) {
                $loaddata .= "[";
                foreach ($r as $krr => $rr) {
                    $loaddata .= "'" . $rr . "',";
                    $returndata[$kr][$krr] = stripslashes($rr);
                }
                $loaddata .= "],";
            }
        }
        if ($return) {
            //echo '['.rtrim($loaddata, ',').']';
            echo json_encode($returndata);

            exit();
        }

        //printcool ($list['headers']);
        $this->mysmarty->assign('list', $list['data']);
        $this->mysmarty->assign('id', (int) $_POST['soldid']);

        $fielset = array('accounting' => array(
            'headers' => "'GO', 'BCN', 'Old BCN', 'Title', 'Status', 'Price Sold', 'Actual Sh.', 'Fee', 'Date Sold', 'Where Sold', 'Location', 'Order Notes'",
            'width' => "60, 80, 100, 300, 125, 125, 125, 100, 125, 125, 80, 140",
            'startcols' => 12,
            'startrows' => 10,
            'autosaveurl' => "/Mywarehouse/OrderAccountingSave/" . (int) $id,
            'colmap' => '{readOnly: true, renderer: "html"},{readOnly: true},{},{},{type: "dropdown", source: [' . $this->statuses['accountingstring'] . ']},{},{},{},{readOnly: true},{},{},{}')
        );

        /* if ($this->session->userdata['type'] == 'master' || ($this->session->userdata['type'] != 'master' && $this->session->userdata['accounting'] == 1))
          {

          $fielset = array('accounting' => array(
          'headers' => "'GO', 'BCN', 'Old BCN', 'Title', 'Location', 'Audit', 'Status', 'Where Listed', 'Date Listed', 'Date Sold', 'Where Sold', 'Sold ID', 'Sold QN', 'Price Sold', 'Shipping Cost','Actual Sh.', 'Inbound Sh.','Order Notes', 'Selling Fee', 'Net Profit', 'Cost', 'Last Upd'",

          'width' => "60, 80, 100, 300, 125, 80, 125, 125, 125, 125, 125, 80, 80, 125, 125, 125, 125, 125,125, 125, 125, 125",
          'startcols' => 22,
          'startrows' => 10,
          'autosaveurl' => "/Mywarehouse/OrderAccountingSave/".(int)$id,
          'colmap' => '{readOnly: true, renderer: "html"},{readOnly: true},{},{},{},{type: "checkbox", checkedTemplate: 1, uncheckedTemplate: 0},{type: "dropdown", source: ['.$this->statuses['accountingstring'].']},{},{},{},{},{readOnly: true},{readOnly: true},{},{},{},{},{},{},{readOnly: true},{},{readOnly: true}')
          );
          }
          else
          {
          $fielset = array('accounting' => array(
          'headers' => "'GO', 'BCN', 'Old BCN', 'Title', 'Location', 'Audit', 'Status', 'Where Listed', 'Date Listed', 'Date Sold', 'Where Sold',  'Sold ID', 'Sold QN', 'Price Sold', 'Shipping Cost','Actual Sh.', 'Inbound Sh.', 'Order Notes',  'Last Upd'",

          'width' => "60, 80, 100, 300, 125, 80, 125, 125, 125, 125, 125, 80, 80, 125, 125, 125,125, 125,  125",
          'startcols' => 19,
          'startrows' => 10,
          'autosaveurl' => "/Mywarehouse/OrderAccountingSave/".(int)$id,
          'colmap' => '{readOnly: true, renderer: "html"},{readOnly: true},{},{},{},{type: "checkbox", checkedTemplate: 1, uncheckedTemplate: 0},{type: "dropdown", source: ['.$this->statuses['accountingstring'].']},{},{},{},{},{readOnly: true},{readOnly: true},{},{},{},{},{},{readOnly: true}')
          );
          }
         */


        $this->mysmarty->assign('headers', $fielset['accounting']['headers']);
        $this->mysmarty->assign('rowheaders', $fielset['accounting']['rowheaders']);
        $this->mysmarty->assign('width', $fielset['accounting']['width']);
        $this->mysmarty->assign('startcols', $fielset['accounting']['startcols']);
        $this->mysmarty->assign('startrows', $fielset['accounting']['startrows']);
        $this->mysmarty->assign('autosaveurl', $fielset['accounting']['autosaveurl']);
        $this->mysmarty->assign('colmap', $fielset['accounting']['colmap']);
        $this->mysmarty->assign('loaddata', rtrim($loaddata, ','));
        $this->mysmarty->assign('copyrows', count($list['data']));

        $this->mysmarty->assign('order', TRUE);

        if (!$display)
            echo $this->mysmarty->fetch('mywarehouse/accounting.html');
    }

    public function GetWalking() {
        $admin365 = $this->load->database("365admin", true);
        $data = array(
            "user_id" => $this->session->userdata("user_id"),
            "walking" => 1
        );
        $admin365->where("walking", "1");
        $client_name = $admin365->get("ip_clients")->result_array();

        $admin365->where("user_id", $this->session->userdata("user_id"));
        $walking_customer_rows = $admin365->get("ip_client_walking_aux")->num_rows();

        if ($walking_customer_rows == 0) {
            $admin365->insert("ip_client_walking_aux", $data);
        } else {
            $admin365->where("user_id", $this->session->userdata("user_id"));
            $admin365->update("ip_client_walking_aux", $data);
        }
        echo $client_name[0]['client_id'];
    }

    public function Save() {

        $admin365 = $this->load->database("365admin", true);
        date_default_timezone_set('America/Los_Angeles');
        $this->load->model('invoices/mdl_items');
        $this->load->model('invoices/mdl_invoices');
//        $i = 0;

        $invoice_number = $this->input->post('invoice_number');

        //VALIDATE STORE
        $store = $this->input->post("store");
        $admin365->where("id", $store);
        $store_data = $admin365->get("ip_stores")->result_object();
        $store_name = $store_data[0]->store_name;


        if ($store_name != "Warehouse") {


            $invoice_id = $this->input->post('invoice_id');

            //$this->mdl_invoices->set_id($invoice_id);

            $invoice_status = $this->input->post('invoice_status_id');

//        if ($this->mdl_invoices->run_validation('validation_rules_save_invoice')) {
//        $items = json_decode($this->input->post('items'));
//        foreach ($items as $item) {
//            // Check if an item has either a quantity + price or name or description
//            //if (!empty($item->item_name)) {
//            $item->item_quantity = ($item->item_quantity ? standardize_amount($item->item_quantity) : floatval(0));
//            $item->item_price = ($item->item_quantity ? standardize_amount($item->item_price) : floatval(0));
//            $item->item_discount_amount = ($item->item_discount_amount) ? standardize_amount($item->item_discount_amount) : null;
//            $item->item_product_id = ($item->item_product_id ? $item->item_product_id : null);
//
//
//            $amendment = $item->amendment;
//            $item_id = ($item->item_id) ?: null;
//            unset($item->item_id);
//
//            $admin365->select("amendment");
//            $admin365->where("product_id", $item->item_product_id);
//            $product_data = $admin365->get("ip_products")->result_array();
//
//            $admin365->select("invoice_id, id_status");
//            $admin365->join("ip_invoices_status_history", "ip_invoices_status_history.id_invoice = ip_invoice_items.invoice_id");
//            $admin365->where("invoice_id", $invoice_id);
//            $admin365->order_by("id_status", "desc");
//            @$invoice_items = $admin365->get("ip_invoice_items")->result_array();
//
//            if (($amendment == 1) || ($product_data[0]['amendment'] == 1)) {
//                $i++;
//                if ($i == 1) {
//
//
//
//
//                    $admin365->join("ip_clients", "ip_clients.client_id = ip_invoices.client_id");
//                    $admin365->where("invoice_id", $invoice_id);
//                    $ip_invoices = $admin365->get("ip_invoices")->result_array();
//                    if ($ip_invoices[0]['invoice_url_key'] == null) {
//                        $this->load->helper('string');
//                        $invoice_url_key = random_string('alnum', 15);
//
//                        $admin365->set("invoice_url_key", $invoice_url_key);
//                        $admin365->where("invoice_id", $invoice_id);
//                        $admin365->update("ip_invoices");
//                    }
//                    $orders_array = array(
//                        "oid_ref" => $ip_invoices[0]['ticket_id'],
//                        "buytype" => 5,
//                        "subtype" => "u",
//                        "email" => $ip_invoices[0]['client_email'],
//                        "order" => $item->item_name,
//                        "endprice" => $item->item_price,
//                        "time" => date("Y-m-d H:i:s"),
//                        "staffcomments" => "<strong>(" . $this->session->userdata("user_name") . ")</strong>"
//                    );
//
//
//                    $admin365->where("invoice_id", $invoice_id);
//                    $admin365->where("amendment", 1);
//                    $ip_invoice_items = $admin365->get("ip_invoice_items")->num_rows();
//
//                    if ($ip_invoice_items == 0) {
//                        $admin365->insert("orders", $orders_array);
//
//                        $admin365->select("oid");
//                        $admin365->where($orders_array);
//                        $orders = $admin365->get("orders")->result_array();
//
//                        $admin365->set("amendment_id", $orders[0]['oid']);
//                        $admin365->where("invoice_id", $invoice_id);
//                        $admin365->update("ip_invoices");
//
//                        $this->mdl_items->save($item_id, $item);
//                    }
//                }
//            } else if ($product_data[0]['amendment'] == 0) {
//                $this->mdl_items->save($item_id, $item);
//            }
//        }
//        if ($invoice_status == 4) {
//            $admin365->select("amendment_id");
//            $admin365->where("invoice_id", $invoice_id);
//            $ip_invoices = $admin365->get("ip_invoices")->result_array();
//
//            $admin365->set("complete", 1);
//            $admin365->where("oid", $ip_invoices[0]['amendment_id']);
//            $admin365->update("orders");
//        }

            if ($this->input->post('invoice_discount_amount') === '') {
                $invoice_discount_amount = floatval(0);
            } else {
                $invoice_discount_amount = $this->input->post('invoice_discount_amount');
            }

            if ($this->input->post('invoice_discount_percent') === '') {
                $invoice_discount_percent = floatval(0);
            } else {
                $invoice_discount_percent = $this->input->post('invoice_discount_percent');
            }



            $db_array = array(
                'invoice_number' => $invoice_number,
                'invoice_terms' => $this->input->post('invoice_terms'),
                'invoice_date_created' => date('Y-m-d', strtotime($this->input->post('invoice_date_created'))),
                'invoice_date_due' => date('Y-m-d', strtotime($this->input->post('invoice_date_due'))),
                'invoice_status_id' => $invoice_status,
                'payment_method' => $this->input->post('payment_method'),
                'invoice_discount_amount' => $invoice_discount_amount,
                'invoice_discount_percent' => $invoice_discount_percent,
                'store' => $this->input->post("store_invoice")
            );

//        // check if status changed to sent, the feature is enabled and settings is set to sent
//        if ($this->config->item('disable_read_only') === false) {
//            if ($invoice_status == $this->mdl_settings->setting('read_only_toggle')) {
//                $db_array['is_read_only'] = 1;
//            }
//        }

            $admin365->where("invoice_id", $invoice_id);
            $admin365->update("ip_invoices", $db_array);

//        // Recalculate for discounts
//        $this->load->model('invoices/mdl_invoice_amounts');
//        $this->mdl_invoice_amounts->calculate($invoice_id);

            $response = array(
                'success' => 1,
            );
//        } else {
//            $this->load->helper('json_error');
//            $response = array(
//                'success' => 0,
//                'validation_errors' => json_errors()
//            );
//        }
            //GET CLIENT EMAIL
            $admin365->join("ip_clients", "ip_clients.client_id = ip_invoices.client_id");
            $admin365->where("ip_invoices.invoice_id", $invoice_id);
            $client_email = $admin365->get("ip_invoices")->result_array();

            //INSERT INTO IP_INVOICES_STATUS_HISTORY
            $data_invoice_status_history = array(
                "id_invoice" => $invoice_id,
                "id_status" => $invoice_status,
                "date_changed" => date("Y-m-d- H:i:s"),
                "staff_comments" => $this->input->post('staff_comments'),
                "notes_to_customer" => $this->input->post('notes_to_customer'),
                "user_id" => 9999 //NEED TO MATCH THE USER WITH admin365 SYSTEM USER
            );

            $admin365->insert("ip_invoices_status_history", $data_invoice_status_history);

            if ($invoice_status == 1) {
                $status = "Quote";
            } else if ($invoice_status == 2) {
                $status = "Sent";
            } else if ($invoice_status == 3) {
                $status = "Denied";
            } else if ($invoice_status == 4) {
                $status = "Paid";
            }
            //SEND THE EMAIL TO THE CUSTOMER JUST WHEN THE STATUS IS SENT
            if ($invoice_number == "") {
                $invoice_number_email = $invoice_id;
            } else {
                $invoice_number_email = $invoice_number;
            }
            //CHECK IF ALERT EXISTS
            $admin365->where("invoice_id", $invoice_id);
            $alerts_data = $this->db->get("ip_customeralerts")->result_object();
            if ($alerts_data == null) {
                //INSERT CUSTOMER ALERTS
                $admin365->set("message_qtt", 1);
                $admin365->set("invoice_id", $invoice_id);
                $admin365->insert("ip_customeralerts");
            } else {
                //UPDATE CUSTOMER ALERTS
                $admin365->set("message_qtt", 1);
                $admin365->where("invoice_id", $invoice_id);
                $admin365->update("ip_customeralerts");
            }
            $to = $client_email[0]['client_email'];
            $subject = "The Invoice $invoice_number_email status has been changed @ " . date("m-d-Y H:i:s");
            $message = "The Invoice $invoice_number_email status has been changed to <b>$status</b><br><br>" . $data_invoice_status_history['notes_to_customer'] . "<br>You can access the link below to check your orders/invoices status or make payments using you email and phone number as password,<br><a href='https://365laptoprepair.com/my'>Customer Dashboard</a><br>Thank you.";
            $headers = "Content-Type:text/html; charset=UTF-8\n";
            $headers .= "From:  365laptoprepair.com<support@365laptoprepair.com>\n";
            $headers .= "X-Sender:  <support@365laptoprepair.com>\n"; //email do servidor //que enviou
            $headers .= "X-Mailer: PHP  v" . phpversion() . "\n";
            $headers .= "X-IP:  " . $_SERVER['REMOTE_ADDR'] . "\n";
            $headers .= "Return-Path:  <support@365laptoprepair.com>\n"; //caso a msg //seja respondida vai para  este email.
            $headers .= "MIME-Version: 1.0\n";

            if (mail($to, $subject, $message, $headers)) {
                echo 1;
            } else {

                echo 2;
            }


//        // Save all custom fields
//        if ($this->input->post('custom')) {
//            $db_array = array();
//
//            foreach ($this->input->post('custom') as $custom) {
//                $db_array[str_replace(']', '', str_replace('custom[', '', $custom['name']))] = $custom['value'];
//            }
//
//            $this->load->model('custom_fields/mdl_invoice_custom');
//            $this->mdl_invoice_custom->save_custom($invoice_id, $db_array);
//        }
//        echo json_encode($response);
        }
    }

    public function ShowProducts() {

        $product_get = $this->input->post("term");
        $this->db->select('title, wid');
        $this->db->or_like("bcn", $product_get);
        $this->db->or_like("bcn_p1", $product_get);
        $this->db->or_like("bcn_p2", $product_get);
        $this->db->or_like("bcn_p3", $product_get);
        $this->db->or_like("title", $product_get);
        $this->db->limit(10);
        $produt_data = $this->db->get("warehouse")->result_object();
        foreach ($produt_data as $produt_data) {
            $product_name[] = array(
                "id" => $produt_data->wid,
                "value" => $produt_data->title
            );
        }
        echo json_encode($product_name);
    }

    public function UpdateNrows() {
        if ($this->input->post("erase") == 1) {
            $this->session->unset_userdata("nrows");
        } else {
            $nrows = $this->session->userdata("nrows");
            if ($nrows == null) {
                $this->session->set_userdata('nrows', $this->input->post('qtt_rows'));
            } else {
                $nrows += 1;
                $this->session->set_userdata('nrows', $nrows);
            }
            echo $this->session->userdata("nrows");
        }
    }

    public function SetProduct() {
        $product_id = $this->input->post("prod_id");
        $this->db->where("wid", $product_id);
        $product_data = $this->db->get("warehouse")->result_object();

        echo json_encode($product_data[0]);
    }

    public function create() {

        $admin365 = $this->load->database("365admin", true);

        $this->load->model('neworders/mdl_orders');

        $store = $this->input->post("store");
        $client_id = $this->input->post("client_name");



        $invoice_id = $this->mdl_orders->create($store, $client_id);
        $admin365 = $this->load->database("365admin", true);
        $admin365->where("invoice_id", $invoice_id);
        $row = $admin365->get("ip_invoices")->num_rows();
        if ($row != 0) {
            $response = array(
                'success' => 1,
                'invoice_id' => $invoice_id
            );


            //UPDATE MESSAGE QTT TO ALERT CUSTOMER ON CUSTOMER DASHBOARD(WEBSITE)
            $custom_alert = array(
                "client_id" => $client_id,
                "invoice_id" => $invoice_id,
                "message_qtt" => 1
            );
            $admin365->insert("ip_customeralerts", $custom_alert);
        } else {
            $response = array(
                'success' => 1,
                'invoice_id' => $invoice_id
            );
        }




        echo json_encode($response);
    }

}
