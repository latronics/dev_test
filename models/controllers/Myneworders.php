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
        $admin365['admin365']->where("invoice_id", $id);
        $items = $admin365['admin365']->get("ip_invoice_items")->result_object();

        //PASSING INVOICE DATA TO VIEW
        $admin365['admin365']->select(" *, ip_invoices.store");
        $admin365['admin365']->where("ip_invoices.invoice_id", $id);
        $admin365['admin365']->join("ip_clients", "ip_clients.client_id = ip_invoices.client_id");
        $admin365['admin365']->join("ip_invoice_amounts", "ip_invoice_amounts.invoice_id = ip_invoices.invoice_id");
        $admin365['admin365']->join("ip_payments", "ip_payments.invoice_id = ip_invoices.invoice_id", "left");
        $admin365['admin365']->join("ip_invoice_items", "ip_invoice_items.invoice_id = ip_invoices.invoice_id", "left");
        $invoice_data = $admin365['admin365']->get("ip_invoices")->result_object();




        $admin365['items'] = $items;
        $admin365['invoice_id'] = $id;
        $admin365['invoice'] = $invoice_data[0];
        $admin365['payment_methods'] = $admin365['admin365']->get("ip_payment_methods")->result_object();
        $admin365['stores'] = $admin365['admin365']->get("ip_stores")->result_object();

        $this->mysmarty->assign('date', CurrentTime());
        $this->mysmarty->assign('hot', true);
        $this->mysmarty->assign('noenter', $noenter);

        $this->load->view('mywarehouse/new_order/orders', $admin365);
        //$this->mysmarty->view('mywarehouse/order.html');	
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
        $data = array(
            "user_id" => $this->session->userdata("user_id"),
            "walking" => 1
        );
        $admin365->select("*");
        $admin365->where("walking", "1");
        $client_name = $admin365->get("ip_clients")->result_array();

        $admin365->select("*");
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

    public function create() {
        $this->load->model('neworders/mdl_orders');

        $store = $this->input->post("store");
        $client_id = $this->input->post("client_name");



        $invoice_id = $this->mdl_orders->create($store, $client_id);

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





        echo json_encode($response);
    }

}
