<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Myebay extends Controller
{
    function Myebay()
    {
        parent::Controller();
        $this->load->model('Myebay_model');
        $this->load->model('Auth_model');
        $this->Auth_model->VerifyAdmin();
        //if ($this->session->userdata['admin_id'] == 7) { echo 'Sorry, you don\'t have clearance for here.';exit();}
        //if ($this->session->userdata['admin_id'] == 10) { echo 'Sorry, you don\'t have clearance for here.'; exit();}
        $this->load->model('Myproducts_model');
        $this->mysmarty->assign('zero', $this->Myproducts_model->CheckWeightZero());
        $this->load->library('pagination');
        $this->session->set_userdata('d1from', date('m/j/Y'));
        $this->session->set_userdata('d1to', date('m/j/Y', strtotime("-30 days")));
        $this->session->set_userdata('d2from', date('m/j/Y'));
        $this->session->set_userdata('d2to', date('m/j/Y', strtotime("-60 days")));
        $this->session->set_userdata('d3from', date('m/j/Y'));
        $this->session->set_userdata('d3to', date('m/j/Y', strtotime("-90 days")));
        $this->mysmarty->assign('cal', TRUE);
        $navfrom = $this->session->userdata('navfrom');
        $navto   = $this->session->userdata('navto');
        if (!$navfrom || !$navto)
        {
            $this->session->set_userdata('navfrom', date('m/j/Y'));
            $this->session->set_userdata('navto', date('m/j/Y', strtotime("-15 days")));
        }
        $this->_DoCounting();
        $this->mysmarty->assign('session', $this->session->userdata);
        $this->mysmarty->assign('action', $this->session->flashdata('action'));
        $this->mysmarty->assign('gotoebay', $this->session->flashdata('gotoebay'));
        $this->mysmarty->assign('error_msg', $this->session->flashdata('error_msg'));
        $this->mysmarty->assign('success_msg', $this->session->flashdata('success_msg'));
        $this->mysmarty->assign('area', 'Ebay');
        $this->mysmarty->assign('ebupd', TRUE);
        $this->actabrv = array('e_img1' => 'Image 1',     'e_img2' => 'Image 2', 'e_img3' => 'Image 3', 'e_img4' => 'Image 4', 'e_img5' => 'Image 5', 'e_img6' => 'Image 6', 'e_img7' => 'Image 7', 'e_img8' => 'Image 8', 'quantity' => 'Local Quantity', 'e_part' => 'BCN', 'e_qpart' => 'BCN Count', 'buyItNowPrice' => 'Price', 'e_title' => 'Title', 'e_sef' => 'SEF URL', 'e_condition' => 'Condition', 'e_model' => 'Model', 'e_compat' => 'Compatibility', 'ebayquantity' => 'Local eBay Quantity', 'Ebay Quantity' => 'Local eBay Quantity', 'idpath' => 'Image Dir.', 'e_desc' => 'Descripion', 'upc' => 'UPC', 'e_manuf' => 'Brand', 'e_package' => 'Package', 'location' => 'Location', 'pCTitle' => 'Pri.Cat. Title', 'ebay_submitted' => 'Submitted', 'ebay_id' => 'eBay ID', 'sn' => 'Transaction BCN', 'asc' => 'ActShipCost','storeCatTitle' => 'Store Category','storeCatID' => 'Store Cat. ID');
    }
    function _DoCounting()
    {
        $this->mysmarty->assign('allsitelistings', $this->db->count_all_results('ebay'));
        //$this->db->where('sitesell', 0);
        //$this->mysmarty->assign('offsite', $this->db->count_all_results('ebay'));
        $this->db->where('sitesell', 1);
        $this->mysmarty->assign('onsite', $this->db->count_all_results('ebay'));
        $query = $this->db->query('SELECT e_id FROM ebay WHERE (ebay_id = 0  OR ebended IS NOT NULL)');
        $this->mysmarty->assign('notlinked', $query->num_rows());
        $sql    = 'SELECT e_id FROM ebay WHERE `ebay_id` != 0 AND `ebended` IS NULL';
        $q      = $this->db->query($sql);
        $sql2   = '';
        $linked = $q->num_rows();
        $this->mysmarty->assign('linked', $linked);
        foreach ($q->result_array() as $e)
        {
            $sql2 .= 'tr.e_id = ' . $e['e_id'] . ' OR ';
            $active[$e['e_id']] = TRUE;
        }
        $this->db->where('ebay_id !=', 0);
        $this->db->where('ebended', NULL);
        //$this->db->where('ebay_submitted', NULL);
        $this->db->where('ebay_id !=', 0);
        $this->db->where('ebended', NULL);
        $this->db->where('qn_ch1 != ebayquantity');
        $this->mysmarty->assign('noquantbcn', $this->db->count_all_results('ebay'));
        $this->db->where('ebay_id !=', 0);
        $this->db->where('ebended', NULL);
        $this->db->where('quantity != ebayquantity');
        $this->mysmarty->assign('noqnquantbcn', $this->db->count_all_results('ebay'));
        $this->db->where('ebay_id !=', 0);
        $this->db->where('ebended', NULL);
        $this->db->where('e_qpart != ebayquantity');
        $this->mysmarty->assign('assignedmismatch', $this->db->count_all_results('ebay'));
        $this->db->where('ebay_id !=', 0);
        $this->db->where('ebended', NULL);
        $this->db->where('ebayquantity', 0);
        $this->db->where('ooskeepalive', 0);
        $this->mysmarty->assign('ostock', $this->db->count_all_results('ebay'));
        $this->db->where('ebay_id !=', 0);
        $this->db->where('ebended', NULL);
        $this->db->where('ebayquantity', 0);
        $this->db->where('ooskeepalive', 1);
        $this->mysmarty->assign('ooskeepalive', $this->db->count_all_results('ebay'));
        $this->db->where('ebay_id !=', 0);
        $this->db->where('ebended', NULL);
        $this->db->where('qn_ch1 = ebayquantity');
        $this->mysmarty->assign('quantbcn', $this->db->count_all_results('ebay'));
        $this->db->where('etype', 'a');
        $this->mysmarty->assign('activeebay', $this->db->count_all_results('ebay_live'));
        //$this->db->where('etype', 'u');
        //$this->mysmarty->assign('inactiveebay', $this->db->count_all_results('ebay_live'));
        //$this->db->where('ebay_id', 0);
        //$this->db->where('quantity != e_qpart');
        //$this->mysmarty->assign('inactnoquantbcn', $this->db->count_all_results('ebay'));
        //$this->db->where('ebay_id', 0);
        //$this->db->where('quantity = e_qpart');
        //$this->mysmarty->assign('inactquantbcn', $this->db->count_all_results('ebay'));
        $this->db->where('ebay_id !=', 0);
        $this->db->where('ebended', NULL);
        $this->db->where('ngen >', 0);
        $this->mysmarty->assign('actghosts', $this->db->count_all_results('ebay'));
        //$query = $this->db->query('SELECT e_id FROM ebay WHERE (ebay_id = 0  OR ebended IS NOT NULL) AND ngen > 0 AND e_qpart > 0');
        //$this->mysmarty->assign('noactghosts', $query->num_rows());
        $msql = 'SELECT distinct e.e_id FROM (ebay e) LEFT JOIN warehouse w ON e.e_id = w.listingid WHERE `e`.`ebay_id` != 0 AND `e`.`ebended` IS NULL  AND `w`.`deleted` = 0  AND `w`.`nr` = 0 AND `w`.`vended` = 0 AND `w`.`generic` = 1 AND `w`.`regen` = 1';
        $q    = $this->db->query($msql);
        $this->mysmarty->assign('actgreens', $q->num_rows());
        /////////////ORDERS////
        $tdf   = 46800;
        $from  = explode('/', $this->session->userdata('navfrom'));
        $ofrom = mktime(23, 59, 59, $from[0], $from[1], $from[2]) + $tdf;
        $to    = explode('/', $this->session->userdata('navto'));
        $oto   = mktime(0, 0, 0, $to[0], $to[1], $to[2]) + $tdf;
        $this->db->where('notpaid', 1);
        $this->db->where('mkdt <= ', $ofrom);
        $this->db->where('mkdt >= ', $oto);
        $this->mysmarty->assign('ebaynotpaid', $this->db->count_all_results('ebay_transactions'));
        $this->db->where("(customcode = 1 OR refunded = 1)", null, false);
        $this->db->where('mkdt <= ', $ofrom);
        $this->db->where('mkdt >= ', $oto);
        $this->db->where('sellingstatus != ', 'PartiallyPaid');
        $this->mysmarty->assign('ebayrefunded', $this->db->count_all_results('ebay_transactions'));
        $this->db->where("(customcode = 1 OR refunded = 1)", null, false);
        $this->db->where('mkdt <= ', $ofrom);
        $this->db->where('mkdt >= ', $oto);
        $this->db->where('sellingstatus', 'PartiallyPaid');
        $this->mysmarty->assign('ebaypartialrefund', $this->db->count_all_results('ebay_transactions'));
        $this->db->where('pendingpay', 1);
        $this->db->where('mkdt <= ', $ofrom);
        $this->db->where('mkdt >= ', $oto);
        $this->mysmarty->assign('ebaypendingpay', $this->db->count_all_results('ebay_transactions'));
        //$this->db->where('paid', '');
        //$this->db->or_where('paid', '0.0');
        //$this->db->where("(paid = '' OR paid = '0.0')",null, false);
        $this->db->where('paidtime', '');
        $this->db->where('notpaid', 0);
        $this->db->where('refunded', 0);
        $this->db->where('mkdt <= ', $ofrom);
        $this->db->where('mkdt >= ', $oto);
        $this->mysmarty->assign('ordersebaynotpaid', $this->db->count_all_results('ebay_transactions'));
        $this->db->where('paidtime !=', '');
        $this->db->where('mark', 0);
        $this->db->where('mkdt <= ', $ofrom);
        $this->db->where('mkdt >= ', $oto);
        $this->db->where('notpaid', 0);
        $this->db->where("refunded", 0);
        $this->db->where('pendingpay', 0);
        $this->mysmarty->assign('ordersebaynotshipped', $this->db->count_all_results('ebay_transactions'));
        $this->db->where('complete !=', 1);
        $this->db->where('complete !=', "-1");
        $this->db->where('submittime <= ', $ofrom);
        $this->db->where('submittime >= ', $oto);
        $this->mysmarty->assign('orderssitenotcomplete', $this->db->count_all_results('orders'));
        $this->db->where('complete', 1);
        $this->db->where('mark !=', 0);
        $this->db->where('submittime <= ', $ofrom);
        $this->db->where('submittime >= ', $oto);
        $this->mysmarty->assign('orderssitecomplete', $this->db->count_all_results('orders'));
        $this->db->where('complete', 1);
        $this->db->where('mark', 0);
        $this->db->where('submittime <= ', $ofrom);
        $this->db->where('submittime >= ', $oto);
        $this->mysmarty->assign('orderssitenotshipped', $this->db->count_all_results('orders'));
        $this->db->where('complete <', 0);
        $this->db->where('submittime <= ', $ofrom);
        $this->db->where('submittime >= ', $oto);
        $this->mysmarty->assign('orderssitefraud', $this->db->count_all_results('orders'));
        $this->db->where('paid !=', '');
        $this->db->where('paid !=', '0.0');
        $this->db->where('mark !=', 0);
        $this->db->where('mkdt <= ', $ofrom);
        $this->db->where('mkdt >= ', $oto);
        $this->db->where('notpaid', 0);
        $this->db->where('refunded', 0);
        $this->mysmarty->assign('ordersebaypaid', $this->db->count_all_results('ebay_transactions'));
        //$timesql = ' AND `mkdt` <= "'.$ofrom.'" AND `mkdt` >= "'.$oto.'"';
        /*$query = $this->db->query('SELECT e_id FROM ebay WHERE (ebay_id = 0  OR ebended IS NOT NULL) AND qn_ch1 != ebayquantity');
        
        $this->mysmarty->assign('inactnoquantbcn', $query->num_rows());*/
        /*$query = $this->db->query('SELECT e_id FROM ebay WHERE (ebay_id = 0  OR ebended IS NOT NULL) AND qn_ch1 = ebayquantity');
        
        $this->mysmarty->assign('inactquantbcn', $query->num_rows());*/
        $query = $this->db->query('SELECT distinct e.e_id FROM (ebay e) LEFT JOIN warehouse w ON e.e_id = w.listingid WHERE `e`.`ebended` IS NOT NULL AND `w`.`deleted` = 0  AND `w`.`nr` = 0 AND `w`.`vended` = 0');
        $this->mysmarty->assign('needrelist', $query->num_rows());
        $query = $this->db->query('SELECT distinct e_id FROM ebay WHERE ebended IS NULL AND ebay_id = 0');
        $this->mysmarty->assign('neverlist', $query->num_rows());
        $query = $this->db->query('SELECT distinct e_id FROM ebay WHERE e_title = ""');
        $this->mysmarty->assign('nlnotitle', $query->num_rows());
        $query = $this->db->query('SELECT distinct e.e_id FROM (ebay e) LEFT JOIN warehouse w ON e.e_id = w.listingid WHERE `e`.`ebended` IS NULL AND `e`.`ebay_id` = 0 AND `w`.`deleted` = 0  AND `w`.`nr` = 0 AND `w`.`vended` = 0');
        $this->mysmarty->assign('nlbcn', $query->num_rows());
        //$query = $this->db->query('SELECT e_id FROM ebay WHERE audit IS NOT NULL AND `ebay_id` != 0  AND `ebended` IS NULL');
        $query    = $this->db->query('SELECT e_id FROM ebay WHERE audit IS NOT NULL AND `ebay_id` != 0  AND `ebended` IS NULL AND `auditmk` <= ' . $ofrom . ' AND `auditmk` >= ' . $oto);
        $eaudited = $query->num_rows();
        $this->mysmarty->assign('audited', $eaudited);
        $query = $this->db->query('SELECT distinct e_id FROM autopilot_rules');
        $this->mysmarty->assign('autopilot', $query->num_rows());
        $query = $this->db->query('SELECT distinct e_id FROM autopilot_rules WHERE e_id > 0 AND runnextmk < ' . mktime());
        $this->mysmarty->assign('inactiveautopilot', $query->num_rows());
        $query = $this->db->query('SELECT distinct e_id FROM competitor_rules');
        $this->mysmarty->assign('competitor', $query->num_rows());
        $query = $this->db->query('SELECT e_id FROM ebay WHERE `ebay_id` != 0  AND `ebended` IS NULL AND `dispose` > 0');
        $this->mysmarty->assign('dispose', $query->num_rows());
        $query = $this->db->query('SELECT e_id FROM ebay WHERE `ebay_id` != 0  AND `ebended` IS NULL AND `eBay_specs` IS NOT NULL');
        $this->mysmarty->assign('ebayspec', $query->num_rows());
        $query = $this->db->query('SELECT e_id FROM ebay WHERE `ebay_id` != 0  AND `ebended` IS NULL AND `eBay_specs` IS NULL');
        $this->mysmarty->assign('noebayspec', $query->num_rows());
        /*$cn = 1;
        
        $this->db->select('audit');
        
        foreach($query->result_array() as $e)
        
        {
        
        if ($cn == 1) $this->db->where('listingid', $e['e_id']);
        
        $this->db->or_where('listingid', $e['e_id']);
        
        $cn++;
        
        }
        
        */
        $query   = $this->db->query('SELECT wid FROM warehouse WHERE `auditmk` <= ' . $ofrom . ' AND `auditmk` >= ' . $oto);
        $bcnsaud = $query->num_rows();
        $this->mysmarty->assign('bcnsaud', $bcnsaud);
        $msql = 'SELECT wid FROM warehouse WHERE `deleted` = 0  AND `nr` = 0 AND `vended` = 0 AND `listingid` > 0';
        $q    = $this->db->query($msql);
        $this->mysmarty->assign('bcnsnoaud', $q->num_rows() - $bcnsaud);
        /*$audited = $this->db->get('warehouse');
        
        $audbcns['yes'] = 0;
        
        $audbcns['no'] = 0;
        
        if ($audited->num_rows() > 0)
        
        {
        
        foreach ($audited->result_array() as $a)
        
        {
        
        if ($a['audit'] == '') $audbcns['no']++;
        
        elseif ($a['audit'] != '' && ($a['auditmk'] >= $ofrom || $a['auditmk'] <= $oto)) $audbcns['no']++;
        
        else $audbcns['yes']++;
        
        }
        
        }
        
        $this->mysmarty->assign('audbcns', $audbcns);
        
        */
        /*$query = $this->db->query('SELECT e_id FROM ebay WHERE audit IS NULL AND `ebay_id` != 0  AND `ebended` IS NULL');*/
        $this->mysmarty->assign('notaudited', $linked - $eaudited);
        /*$cn = 1;
        
        $this->db->select('audit');
        
        foreach($query->result_array() as $e)
        
        {
        
        if ($cn == 1) $this->db->where('listingid', $e['e_id']);
        
        $this->db->or_where('listingid', $e['e_id']);
        
        $cn++;
        
        }
        
        $naudited = $this->db->get('warehouse');
        
        $naudbcns['yes'] = 0;
        
        $naudbcns['no'] = 0;
        
        if ($naudited->num_rows() > 0)
        
        {
        
        foreach ($naudited->result_array() as $a)
        
        {
        
        if ($a['audit'] == '') $naudbcns['no']++;
        
        else $naudbcns['yes']++;
        
        }
        
        }
        
        $this->mysmarty->assign('naudbcns', $naudbcns);*/
        $ebl               = array(
            'active' => false,
            'sold' => false,
            'unsold' => false
        );
        $activenotlinked   = 0;
        $activelinked      = 0;
        $inactivelinked    = 0;
        $inactivenotlinked = 0;
        $query             = $this->db->get('ebay_live');
        foreach ($query->result_array() as $r)
        {
            if ($r['etype'] == 'u')
            {
                if ($r['eid'] == 0)
                    $inactivenotlinked++;
                else
                    $inactivelinked++;
            }
            elseif ($r['etype'] == 'a')
            {
                if ($r['eid'] == 0)
                    $activenotlinked++;
                else
                    $activelinked++;
            }
        }
        $this->mysmarty->assign('activenotlinked', $activenotlinked);
        $this->mysmarty->assign('activelinked', $activelinked);
        $this->mysmarty->assign('inactivelinked', $inactivelinked);
        $this->mysmarty->assign('inactivenotlinked', $inactivenotlinked);
        $or1 = $this->db->count_all_results('ebay_transactions');
        $or2 = $this->db->count_all_results('orders');
        $this->mysmarty->assign('ordercount', ($or1 + $or2));
        $msql = 'SELECT distinct e.e_id FROM (ebay e) LEFT JOIN warehouse w ON e.e_id = w.listingid WHERE `e`.`ebay_id` != 0 AND `e`.`ebended` IS NULL  AND `w`.`deleted` = 0  AND `w`.`nr` = 0 AND `w`.`vended` = 0 AND `w`.`status` = "Mismatch"';
        $q    = $this->db->query($msql);
        $this->mysmarty->assign('mismatched', $q->num_rows());
        /*$this->db->select('e_id');
        
        $this->db->where('ebay_id !=', 0);
        
        $this->db->where('ebended', NULL);
        
        $notr = $this->db->get('ebay');
        
        if ($notr->num_rows() > 0)
        
        {
        
        $c = 1;
        
        foreach ($notr->result_array() as $r)
        
        {
        
        $idarray[] = $r['e_id'];
        
        }
        
        if (isset($idarray))
        
        {
        
        $this->load->model('Myseller_model');
        
        $this->mysmarty->assign('notransbcns', $this->Myseller_model->getEmptySales($idarray, 1, TRUE));
        
        }
        
        }*/
        /*$sql = 'SELECT e_id FROM ebay WHERE `ebay_id` != 0 AND `ebended` IS NULL';
        
        $q = $this->db->query($sql);
        
        $sql2 = '';
        
        foreach ($q->result_array() as $e)
        
        {
        
        $sql2 .= 'tr.e_id = '.$e['e_id'].' OR ';
        
        $active[$e['e_id']] = TRUE;
        
        }*/
        $this->db->where('notpaid', 1);
        $this->db->where('mkdt <= ', $ofrom);
        $this->db->where('mkdt >= ', $oto);
        $this->mysmarty->assign('ebaynotpaid', $this->db->count_all_results('ebay_transactions'));
        $msql = 'SELECT distinct e.et_id FROM (ebay_transactions e) LEFT JOIN warehouse w ON e.et_id = w.sold_id WHERE (`e`.`notpaid` != 0 OR `e`.`refunded` != 0  OR `e`.`returnnotif` IS NOT NULL) AND `e`.`mkdt` <= ' . $ofrom . ' AND `e`.`mkdt` >= ' . $oto . ' AND `w`.`deleted` = 0  AND `w`.`nr` = 0 AND `w`.`vended` > 0 AND `w`.`sold_id` != 0';
        $q    = $this->db->query($msql);
        $this->mysmarty->assign('ebrb', $q->num_rows());
        $msql = 'SELECT erlid FROM ebay_revise_log WHERE `sev` = 1 AND `atmk` <= ' . $ofrom . ' AND `atmk` >= ' . $oto;
        $q    = $this->db->query($msql);
        $this->mysmarty->assign('erevsev', $q->num_rows());
        $q  = $this->db->query('SELECT et_id, e_id FROM ebay_transactions WHERE `e_id` != 0 AND `notpaid` = 0 AND `refunded` = 0 AND `pendingpay` = 0 AND `mkdt` <= ' . $ofrom . ' AND `mkdt` >= ' . $oto);
        $wh = 'SELECT listingid, sold_id FROM warehouse WHERE sold_id != 0  AND listingid != 0 AND (';
        foreach ($q->result_array() as $t)
        {
            $wh .= 'sold_id = ' . $t['et_id'] . ' OR ';
            $lst[$t['e_id']][$t['et_id']] = TRUE;
        }
        if ($q->num_rows() > 0)
        {
            $w = $this->db->query(rtrim($wh, ' OR ') . ')');
            foreach ($w->result_array() as $s)
            {
                if (isset($lst[$s['listingid']][$s['sold_id']]))
                    unset($lst[$s['listingid']][$s['sold_id']]);
            }
            if (isset($lst))
                foreach ($lst as $k => $v)
                {
                    if (count($v) == 0)
                        unset($lst[$k]);
                }
        }
        if (isset($lst) && count($lst) > 0)
        {
            $sql = 'SELECT e_id FROM ebay WHERE `ebay_id` != 0 AND `ebended` IS NULL AND (';
            foreach ($lst as $lk => $lc)
            {
                $sql .= 'e_id = ' . $lk . ' OR ';
            }
            $sql = rtrim($sql, ' OR ') . ')';
            $q   = $this->db->query($sql);
            $this->mysmarty->assign('notransbcns', $q->num_rows());
        }
        else
            $this->mysmarty->assign('notransbcns', 0);
    }
    function CT()
    {
        printcool(CurrentTime());
    }
    function SaveAsc($etid = 0)
    {
        $sasc = $this->input->post('sasc', true);
        $this->db->select('sasc,itemid,rec,asc,qty');
        $this->db->where('et_id', (int) $etid);
        $t = $this->db->get('ebay_transactions');
        if ($t->num_rows() > 0)
        {
            $tt = $t->row_array();
            $this->_logaction('Transactions', 'B', array(
                'SActShipCost' => $tt['sasc']
            ), array(
                'SActShipCost' => floater($sasc)
            ), 0, $tt['itemid'], $tt['rec']);
            $this->db->update('ebay_transactions', array(
                'sasc' => floater($sasc)
            ), array(
                'et_id' => (int) $etid
            ));
            $this->load->model('Myseller_model');
            $this->db->select('wid, bcn, ' . $this->Myseller_model->sellingfields());
            $this->db->where('channel', 1);
            $this->db->where('sold_id', (int) $etid);
            $this->db->where('vended', 1);
            $f = $this->db->get('warehouse');
            if ($f->num_rows() > 0)
            {
                $fr = $f->result_array();
                foreach ($fr as $fl)
                {
                    if ($fl['vended'] == 1)
                    {
                        if (floater($sasc) != 0.00)
                            $this->Myseller_model->HandleBCN(array(
                                'shipped_actual' => floater($sasc) / $tt['qty']
                            ), $fl);
                        else
                            $this->Myseller_model->HandleBCN(array(
                                'shipped_actual' => $tt['asc'] / $tt['qty']
                            ), $fl);
                    }
                }
            }
        }
    }
    function _logaction($location = '', $type = 'M', $datafrom = '', $datato = '', $eid = '', $itemid = '', $transid = '', $key = '')
    {
        foreach ($datato as $k => $v)
        {
            if ($v != $datafrom[$k])
            {
                if (isset($this->session->userdata['ownnames']))
                    $admin = $this->session->userdata['ownnames'];
                else
                    $admin = 'Cron';
                $hmsg = array(
                    'msg_title' => 'Action Log for ' . (int) $eid . ' - Field: ' . $k . ' (' . $datafrom[$k] . '/' . $datafrom[$k] . ') by ' . $admin,
                    'msg_body' => 'Action Log for ' . (int) $eid . ' - Field: ' . $k . ' (' . $datafrom[$k] . '/' . $datafrom[$k] . ') by ' . $admin,
                    'msg_date' => CurrentTime()
                );
                //GoMail($hmsg, $this->config->config['support_email'], $this->config->config['no_reply_email']);
                if ($key == '')
                    $this->db->insert('ebay_actionlog', array(
                        'atype' => $type,
                        'e_id' => (int) $eid,
                        'ebay_id' => (int) $itemid,
                        'time' => CurrentTimeR(),
                        'datafrom' => $datafrom[$k],
                        'datato' => $v,
                        'field' => $k,
                        'admin' => $admin,
                        'trans_id' => (int) $transid,
                        'ctrl' => $location
                    ));
                else
                    $this->db->insert('ebay_actionlog', array(
                        'atype' => $type,
                        'e_id' => (int) $eid,
                        'ebay_id' => (int) $itemid,
                        'time' => CurrentTimeR(),
                        'datafrom' => $datafrom[$k],
                        'datato' => $v,
                        'field' => $k,
                        'admin' => $admin,
                        'oid' => (int) $transid,
                        'okey' => $key,
                        'ctrl' => $location
                    ));
            }
        }
    }
    function StartReturn($orderid, $ordertype, $notfirst = 0)
    {
        $o = $this->Myebay_model->getOrderReturn((int) $orderid, (int) $ordertype);
        if ($o)
        {
            $this->load->model('Myseller_model');
            $this->mysmarty->assign('salebcns', $this->Myseller_model->getSales(array(
                (int) $orderid
            ), (int) $ordertype, true, true));
            //$this->mysmarty->assign('returnbcns'.(int)$ordertype, $this->Myseller_model->getReturns(array((int)$orderid),(int)$ordertype, true, true));
            $this->Myseller_model->getReturns(array(
                $o['return_id']
            ), (int) $ordertype, TRUE);
            $this->mysmarty->assign('o', $o);
            $this->mysmarty->assign('orderid', (int) $orderid);
            $this->mysmarty->assign('ordertype', (int) $ordertype);
            $this->mysmarty->assign('notfirst', (int) $notfirst);
            echo $this->mysmarty->fetch('myebay/return_area.html');
        }
    }
    function SetReturned($orderid, $ordertype, $notfirst = 0)
    {
        $o = $this->Myebay_model->getOrderReturn((int) $orderid, (int) $ordertype);
        if ($o)
        {
            if ($notfirst > 0)
            {
                $this->db->select('vlog');
                $this->db->where('vid', (int) $o['return_id']);
                $q = $this->db->get('returns');
                if ($q->num_rows() > 0)
                {
                    $vlog = $q->row_array();
                    if (strlen($vlog['vlog']) > 15)
                        $v[] = unserialize($vlog['vlog']);
                }
                $v[] = $o;
                unset($v['return_id']);
                $this->db->update('returns', array(
                    'vlog' => serialize($v)
                ), array(
                    'vid' => $o['return_id']
                ));
            }
            $o['returned_notes'] = $insert['returned_notes'] = $this->input->post('reason', TRUE);
            $o['returned']       = $insert['returned'] = (int) $this->input->post('repl', TRUE);
            if ($insert['returned'] > 2)
                $o['returned'] = $insert['returned'] = 1;
            $o['returned_time'] = $insert['returned_time'] = CurrentTime();
            if ($notfirst > 0)
            {
                $insert['return_id'] = $o['return_id'];
            }
            else
            {
                $this->db->insert('returns', array(
                    'orderid' => (int) $orderid,
                    'channel' => (int) $ordertype,
                    'vdate' => CurrentTime(),
                    'adminid' => (int) $this->session->userdata['admin_id']
                ));
                $insert['return_id'] = $this->db->insert_id();
            }
            $this->Myebay_model->SetOrderReturn((int) $orderid, (int) $ordertype, $insert);
            //
            /*if (isset($_POST['wids']))
            
            {
            
            foreach ($_POST['wids'] as $w) $wids[(int)$w] = (int)$w;
            
            $bcns = $this->Myebay_model->getBcnsFromWids($wids);
            
            }
            
            else $bcns = false;
            
            
            
            if ($bcns)
            
            {
            
            foreach ($bcns as $ka => $b)
            
            {
            
            foreach ($insert as $k => $v)
            
            {
            
            if ($b[$k] != '' && $k != 'returned') unset($insert[$k]);
            
            else $bcns[$ka][$k] = $insert[$k];
            
            
            
            }
            
            if (count($insert) > 0)
            
            {
            
            $this->db->update('warehouse', $insert, array('wid' => (int)$b['wid']));
            
            foreach ($insert as $k => $v) $this->Auth_model->wlog($b['bcn'], $b['wid'], $k, $b[$k], $insert[$k], 'SetReturned');
            
            }
            
            }
            
            
            
            }*/
            $displbcns = $this->Myebay_model->PreGetOrderReturnedBCN($orderid, $ordertype);
            $this->mysmarty->assign('salebcns', $displbcns);
            $this->load->model('Myseller_model');
            $this->Myseller_model->getReturns(array(
                $o['return_id']
            ), (int) $ordertype, TRUE);
            $this->Myseller_model->assignstatuses();
            $this->mysmarty->assign('o', $o);
            $this->mysmarty->assign('orderid', (int) $orderid);
            $this->mysmarty->assign('ordertype', (int) $ordertype);
            $this->mysmarty->assign('notfirst', 0);
            echo $this->mysmarty->fetch('myebay/return_area.html');
        }
    }
    function DisplayBCNSforReturn($orderid, $ordertype)
    {
        //if (isset($_POST['wids']))
        //foreach ($_POST['wids'] as $w) $wids[(int)$w] = (int)$w;
        $o         = $this->Myebay_model->getOrderReturn((int) $orderid, (int) $ordertype);
        $displbcns = $this->Myebay_model->getOrderReturnedBCN($this->Myebay_model->getReturnID((int) $orderid, (int) $ordertype));
        $orderbcns = $this->Myebay_model->PreGetOrderReturnedBCN($orderid, $ordertype);
        if ($orderbcns)
            foreach ($orderbcns as $k => $v)
                $displbcns[$k] = $v;
        $this->mysmarty->assign('salebcns', $displbcns);
        $this->load->model('Myseller_model');
        //$this->Myseller_model->getReturns(array($o['return_id']), (int)$ordertype, TRUE);
        $this->Myseller_model->assignstatuses();
        $this->mysmarty->assign('o', $o);
        $this->mysmarty->assign('orderid', (int) $orderid);
        $this->mysmarty->assign('ordertype', (int) $ordertype);
        echo $this->mysmarty->fetch('myebay/return_area.html');
    }
    function SetReceived($orderid, $ordertype, $notfirst = 0, $onlybcn = false)
    {
        $o = $this->Myebay_model->getOrderReturn((int) $orderid, (int) $ordertype);
        if ($o || $onlybcn)
        {
            if (!$onlybcn)
            {
                if ($o['returned_recieved'] == '')
                {
                    $o['returned_recieved'] = $insert['returned_recieved'] = CurrentTime();
                    $this->Myebay_model->SetOrderReturn((int) $orderid, (int) $ordertype, $insert);
                }
            }
            $insert['returned_recieved'] = CurrentTime();
            $insert['returned_time']     = $o['returned_time'];
            $insert['returned_notes']    = $o['returned_notes'];
            $insert['returned']          = $o['returned'];
            $insert['return_id']         = $o['return_id'];
            //$insert['return_order_id'] = (int)$orderid;
            //$insert['return_order_channel'] = (int)$ordertype;
            $insert['sellingfee']        = 0;
            //$bcns = $this->Myebay_model->getOrderReturnedBCN($this->Myebay_model->getReturnID((int)$orderid, (int)$ordertype));
            if ((int) $insert['return_id'] == 0)
            {
                GoMail(array(
                    'msg_title' => 'return_id 0',
                    'msg_body' => printcool($o, true) . printcool((int) $orderid, true) . printcool((int) $ordertype, true),
                    'msg_date' => CurrentTime()
                ), $this->config->config['support_email'], $this->config->config['no_reply_email']);
                $retake              = $this->Myebay_model->getOrderReturn((int) $orderid, (int) $ordertype);
                $insert['return_id'] = $retake['return_id'];
                if ((int) $insert['return_id'] == 0)
                    GoMail(array(
                        'msg_title' => 'return_id 0 II',
                        'msg_body' => printcool($retake, true) . printcool((int) $orderid, true) . printcool((int) $ordertype, true),
                        'msg_date' => CurrentTime()
                    ), $this->config->config['support_email'], $this->config->config['no_reply_email']);
            }
            if (isset($_POST['wids']))
            {
                foreach ($_POST['wids'] as $w)
                    $wids[(int) $w] = (int) $w;
                $bcns = $this->Myebay_model->getBcnsFromWids($wids);
            }
            else
                $bcns = false;
            if ($bcns)
            {
                foreach ($bcns as $ka => $b)
                {
                    if (!$onlybcn)
                    {
                        $status = $this->input->post('status', TRUE);
                        if ($status != '')
                        {
                            $binsert['status']       = $this->input->post('status', TRUE);
                            $binsert['status_notes'] = 'Changed from: "' . $b['status'] . '" By SetReceived';
                            if ($binsert['status'] == 'Scrap')
                                $binsert['listingid'] = 0;
                        }
                        $location = trim($this->input->post('location', TRUE));
                        if ($location != '')
                        {
                            $binsert['location'] = $this->input->post('location', TRUE);
                        }
                    }
                    foreach ($insert as $okey => $ovalue)
                    {
                        /*if ((($okey == 'return_id' || $okey == 'returned') && $b[$okey] == 0) || (($okey != 'return_id' || $okey != 'returned') && $b[$okey] == '')) $binsert[$okey] = $ovalue;
                        
                        elseif (isset($binsert[$okey])) unset($binsert[$okey]);*/
                        if (!isset($binsert[$okey]))
                            $binsert[$okey] = $ovalue;
                    }
                    if (!$onlybcn || ($onlybcn && $onlybcn == $b['wid']) && isset($binsert))
                    {
                        $this->db->update('warehouse', $binsert, array(
                            'wid' => (int) $b['wid']
                        ));
                        foreach ($binsert as $bk => $bv)
                        {
                            $this->Auth_model->wlog($b['bcn'], $b['wid'], $bk, $b[$bk], $binsert[$bv], 'SetReceived');
                            if ($bk == 'location')
                            {
                                $this->load->model('Mywarehouse_model');
                                $this->Mywarehouse_model->DoLocation($bv, (int) $b['wid']);
                            }
                        }
                    }
                }
            }
            $displbcns = $this->Myebay_model->getOrderReturnedBCN($this->Myebay_model->getReturnID((int) $orderid, (int) $ordertype));
            $orderbcns = $this->Myebay_model->PreGetOrderReturnedBCN($orderid, $ordertype);
            if ($orderbcns)
                foreach ($orderbcns as $k => $v)
                    $displbcns[$k] = $v;
            $this->mysmarty->assign('salebcns', $displbcns);
            $this->load->model('Myseller_model');
            //$this->Myseller_model->getReturns(array($o['return_id']), (int)$ordertype, TRUE);
            $this->Myseller_model->assignstatuses();
            $this->mysmarty->assign('o', $o);
            $this->mysmarty->assign('orderid', (int) $orderid);
            $this->mysmarty->assign('ordertype', (int) $ordertype);
            $this->mysmarty->assign('notfirst', (int) $notfirst);
            echo $this->mysmarty->fetch('myebay/return_area.html');
        }
    }
    function SetRefunded($orderid, $ordertype, $notfirst = 0, $onlybcn = false)
    {
        $o = $this->Myebay_model->getOrderReturn((int) $orderid, (int) $ordertype);
        if ($o || $onlybcn)
        {
            if (!$onlybcn)
            {
                if ($o['returned_refunded'] == '')
                {
                    $o['returned_refunded'] = $insert['returned_refunded'] = CurrentTime();
                    $this->Myebay_model->SetOrderReturn((int) $orderid, (int) $ordertype, $insert);
                }
            }
            $insert['returned_time']     = $o['returned_time'];
            $insert['returned_notes']    = $o['returned_notes'];
            $insert['returned']          = $o['returned'];
            $insert['return_id']         = $o['return_id'];
            $insert['returned_refunded'] = CurrentTime();
            if ((int) $insert['return_id'] == 0)
            {
                GoMail(array(
                    'msg_title' => 'refund return_id 0',
                    'msg_body' => printcool($o, true) . printcool((int) $orderid, true) . printcool((int) $ordertype, true),
                    'msg_date' => CurrentTime()
                ), $this->config->config['support_email'], $this->config->config['no_reply_email']);
                $retake              = $this->Myebay_model->getOrderReturn((int) $orderid, (int) $ordertype);
                $insert['return_id'] = $retake['return_id'];
                if ((int) $insert['return_id'] == 0)
                    GoMail(array(
                        'msg_title' => 'refund return_id 0 II',
                        'msg_body' => printcool($retake, true) . printcool((int) $orderid, true) . printcool((int) $ordertype, true),
                        'msg_date' => CurrentTime()
                    ), $this->config->config['support_email'], $this->config->config['no_reply_email']);
            }
            //$bcns = $this->Myebay_model->getOrderReturnedBCN($this->Myebay_model->getReturnID((int)$orderid, (int)$ordertype));
            if (isset($_POST['wids']))
            {
                foreach ($_POST['wids'] as $w)
                    $wids[(int) $w] = (int) $w;
                $bcns = $this->Myebay_model->getBcnsFromWids($wids);
            }
            else
                $bcns = false;
            if ($bcns)
            {
                foreach ($bcns as $ka => $b)
                {
                    foreach ($insert as $okey => $ovalue)
                    {
                        if ((($okey == 'return_id' || $okey == 'return') && $b[$okey] == 0) || (($okey != 'return_id' || $okey != 'return') && $b[$okey] == ''))
                            $binsert[$okey] = $ovalue;
                        elseif (isset($binsert[$okey]))
                            unset($binsert[$okey]);
                    }
                    if (!$onlybcn || ($onlybcn && $onlybcn == $b['wid']) && isset($binsert))
                    {
                        $this->db->update('warehouse', $binsert, array(
                            'wid' => (int) $b['wid']
                        ));
                        foreach ($binsert as $bk => $bv)
                            $this->Auth_model->wlog($b['bcn'], $b['wid'], $bk, $b[$bk], $binsert[$bv], 'SetRefunded');

                    }
                }
            }
            $displbcns = $this->Myebay_model->getOrderReturnedBCN($this->Myebay_model->getReturnID((int) $orderid, (int) $ordertype));
            $orderbcns = $this->Myebay_model->PreGetOrderReturnedBCN($orderid, $ordertype);
            if ($orderbcns)
                foreach ($orderbcns as $k => $v)
                    $displbcns[$k] = $v;
            $this->mysmarty->assign('salebcns', $displbcns);
            $this->load->model('Myseller_model');
            //$this->Myseller_model->getReturns(array($o['return_id']), (int)$ordertype, TRUE);
            $this->Myseller_model->assignstatuses();
            $this->mysmarty->assign('o', $o);
            $this->mysmarty->assign('orderid', (int) $orderid);
            $this->mysmarty->assign('ordertype', (int) $ordertype);
            $this->mysmarty->assign('returnid', (int) $returnid);
            $this->mysmarty->assign('notfirst', (int) $notfirst);
            echo $this->mysmarty->fetch('myebay/return_area.html');
        }
    }
    function SetRefundExtraCost($orderid, $ordertype, $notfirst = 0)
    {
        $o = $this->Myebay_model->getOrderReturn((int) $orderid, (int) $ordertype);
        if ($o)
        {
            if (floater($o['returned_extracost']) == 0)
            {
                $o['returned_extracost'] = $insert['returned_extracost'] = floater($this->input->post('val', TRUE));
                $this->Myebay_model->SetOrderReturn((int) $orderid, (int) $ordertype, $insert);
            }
            else
            {
                $insert['returned_time']      = $o['returned_time'];
                $insert['returned_extracost'] = floater($this->input->post('val', TRUE));
                $insert['returned_notes']     = $o['returned_notes'];
                $insert['returned']           = $o['returned'];
                $insert['return_id']          = $o['return_id'];
            }
            //$bcns = $this->Myebay_model->getOrderReturnedBCN($this->Myebay_model->getReturnID((int)$orderid, (int)$ordertype));
            if (isset($_POST['wids']))
            {
                foreach ($_POST['wids'] as $w)
                    $wids[(int) $w] = (int) $w;
                $bcns = $this->Myebay_model->getBcnsFromWids($wids);
            }
            else
                $bcns = false;
            if ($bcns)
            {
                foreach ($bcns as $ka => $b)
                {
                    foreach ($insert as $okey => $ovalue)
                    {
                        if ((($okey == 'return_id' || $okey == 'return') && $b[$okey] == 0) || (($okey != 'return_id' || $okey != 'return') && $b[$okey] == ''))
                            $binsert[$okey] = $ovalue;
                        elseif (isset($binsert[$okey]))
                            unset($binsert[$okey]);
                    }
                    if (!$onlybcn || ($onlybcn && $onlybcn == $b['wid']) && isset($binsert))
                    {
                        $this->db->update('warehouse', $binsert, array(
                            'wid' => (int) $b['wid']
                        ));
                        foreach ($binsert as $bk => $bv)
                            $this->Auth_model->wlog($b['bcn'], $b['wid'], $bk, $b[$bk], $binsert[$bv], 'SetRefundExtraCost');
                    }
                }
            }
            $displbcns = $this->Myebay_model->getOrderReturnedBCN($this->Myebay_model->getReturnID((int) $orderid, (int) $ordertype));
            $orderbcns = $this->Myebay_model->PreGetOrderReturnedBCN($orderid, $ordertype);
            if ($orderbcns)
                foreach ($orderbcns as $k => $v)
                    $displbcns[$k] = $v;
            $this->mysmarty->assign('salebcn', $displbcns);
            $this->load->model('Myseller_model');
            $this->Myseller_model->assignstatuses();
            $this->mysmarty->assign('o', $o);
            $this->mysmarty->assign('orderid', (int) $orderid);
            $this->mysmarty->assign('ordertype', (int) $ordertype);
            $this->mysmarty->assign('notfirst', (int) $notfirst);
            echo $this->mysmarty->fetch('myebay/return_area.html');
        }
    }
    function SetRefundStatus($orderid, $ordertype)
    {
        exit();
        $o = $this->Myebay_model->getOrderReturn((int) $orderid, (int) $ordertype);
        if ($o)
        {
            $insert['status'] = $this->input->post('status', TRUE);
            //$bcns = $this->Myebay_model->getOrderReturnedBCN($this->Myebay_model->getReturnID((int)$orderid, (int)$ordertype));
            if (isset($_POST['wids']))
            {
                foreach ($_POST['wids'] as $w)
                    $wids[(int) $w] = (int) $w;
                $bcns = $this->Myebay_model->getBcnsFromWids($wids);
            }
            else
                $bcns = false;
            if ($bcns)
            {
                foreach ($bcns as $ka => $b)
                {
                    $binsert['status_notes'] = 'Changed from: "' . $b['status'] . '" By SetRefundStatus';
                    $binsert                 = $insert;
                    foreach ($o as $okey => $ovalue)
                    {
                        if ($b[$okey] == '')
                            $binsert[$okey] = $ovalue;
                        elseif (isset($binsert[$okey]))
                            unset($binsert[$okey]);
                    }
                    unset($binsert['return_id']);
                    unset($binsert['returned']);
                    if (!$onlybcn || ($onlybcn && $onlybcn == $b['wid']) && isset($binsert))
                    {
                        $this->db->update('warehouse', $binsert, array(
                            'wid' => (int) $b['wid']
                        ));
                        foreach ($binsert as $bk => $bv)
                            $this->Auth_model->wlog($b['bcn'], $b['wid'], $bk, $b[$bk], $binsert[$bv], 'SetRefundStatus');
                        if ($insert['status'] == 'Scrap')
                        {
                            $this->load->model('Myseller_model');
                            $this->Myseller_model->HandleBCN('', '', (int) $b['wid']);
                        }
                    }
                }
            }
            $displbcns = $this->Myebay_model->getOrderReturnedBCN($this->Myebay_model->getReturnID((int) $orderid, (int) $ordertype));
            $orderbcns = $this->Myebay_model->PreGetOrderReturnedBCN($orderid, $ordertype);
            if ($orderbcns)
                foreach ($orderbcns as $k => $v)
                    $displbcns[$k] = $v;
            if ($bcns)
            {
                foreach ($bcns as $ka => $b)
                {
                    $displbcns[$ka]['checked'] = TRUE;
                }
            }
            $this->mysmarty->assign('returnbcns', $displbcns);
            $this->load->model('Myseller_model');
            $this->Myseller_model->assignstatuses();
            $this->mysmarty->assign('o', $o);
            $this->mysmarty->assign('orderid', (int) $orderid);
            $this->mysmarty->assign('ordertype', (int) $ordertype);
            echo $this->mysmarty->fetch('myebay/return_area.html');
        }
    }
    function ChangeOOS($eid, $oos)
    {
        if ((int) $eid > 0 && ((int) $oos == 1 || (int) $oos == 0))
        {
            $this->db->update('ebay', array(
                'ooskeepalive' => (int) $oos
            ), array(
                'e_id' => (int) $eid
            ));
            if ((int) $oos == 1)
                echo '<input type="checkbox" value="1" checked id="ooskeepalive_' . $eid . '" onClick="ChangeOOS(' . $eid . ');"> Keep Alive';
            else
                echo '<a href="' . Site_url() . 'Myebay/EndListing/' . $eid . '/" style="color:red;">End Listing</a> <input type="checkbox" value="0" id="ooskeepalive_' . $eid . '" onClick="ChangeOOS(' . $eid . ');"> Keep Alive';
        }
    }

    function SetNav($clean = false)
    {
        if (!$clean)
        {
            $navfrom = $this->input->post('ofrom', TRUE);
            $navto   = $this->input->post('oto', TRUE);
        }
        else
        {
            $navfrom = date('m/j/Y');
            $navto   = date('m/j/Y', strtotime("-30 days"));
        }
        $this->session->unset_userdata('dfrom');
        $this->session->unset_userdata('dto');
        $this->session->set_userdata('navfrom', $navfrom);
        $this->session->set_userdata('navto', $navto);
        header('location: ' . $_SERVER['HTTP_REFERER']);
    }
    function index()
    {
        $this->Auth_model->CheckListings();
        $this->ListItems();
    }
    function ListItems($page = 1, $searched = false)
    {
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
        if ($searched)
        {
            $session_search   = $this->session->userdata('last_string');
            $session_where    = $this->session->userdata('last_where');
            $session_zero     = $this->session->userdata('last_zero');
            $session_ended    = $this->session->userdata('last_ended');
            $session_mm       = $this->session->userdata('last_mm');
            $session_bcnmm    = $this->session->userdata('last_bcnmm');
            $session_sitesell = $this->session->userdata('last_sitesell');
        }
        else
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
        $data = $this->Myebay_model->ListItems($string, $where, $ended, $zero, $mm, $bcnmm, $sitesell, $page);
        if ($string != '' || $where != '' || $ended != '' || $zero != '' || $mm != '' || $bcnmm != '' || $sitesell != '')
            $searched = TRUE;
        $this->mysmarty->assign('counted', $data['count']);
        $this->mysmarty->assign('list', $data['results']);
        $this->mysmarty->assign('pages', $data['pages']);
        $this->mysmarty->assign('page', (int) $page);
        $pages = count($data['pages']);
        for ($counter = 1; $counter <= $pages; $counter++)
        {
            $before = 12;
            $after  = 12;
            $min    = (int) $page - $before;
            if ($min < 0)
                $after = $before - $min;
            $max = (int) $page + $after;
            if ($max > $pages)
                $before = $before + ($max - $pages);
            if (($counter >= ((int) $page - $before)) && ($counter <= ((int) $page + $after)))
            {
                $pagearray[] = $counter;
            }
        }
        $this->mysmarty->assign('pagearray', $pagearray);
        /*
        
        $this->load->helper('directory');
        $this->load->helper('file');
        $responseXml = read_file($this->config->config['ebaypath'].'/cats.txt');
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
        
        asort($sc);
        */
        $this->db->where("notebay", 0);
        $this->db->orderby('listorder', 'ASC');
        $categories = $this->db->get("warehouse_sku_categories")->result_array();
        $this->mysmarty->assign('dbstore', $categories);
        //$this->mysmarty->assign('store', $sc);
        if (!$searched)
        {
            $this->session->unset_userdata('last_string');
            $this->session->unset_userdata('last_where');
        }
        else
            $this->mysmarty->assign('searched', TRUE);
        $this->_getQNPendingRev();
        $this->mysmarty->view('myebay/myebay_show.html');
    }
    function _getQNPendingRev()
    {
        $revisions = false;
        $this->db->where('e_type', 'q');
        $q = $this->db->get('ebay_revise');
        if ($q->num_rows() > 0)
            foreach ($q->result_array() as $rev)
            {
                $revisions[$rev['e_id']] = $rev['e_val'];
            }
        $this->mysmarty->assign('revisions', $revisions);
    }
    function MSGtest()
    {
        $this->session->set_flashdata('success_msg', 'SUCCESS');
        $this->session->set_flashdata('error_msg', 'ERROR');
        Redirect('Myebay');
    }
    function SaveOrderNotes($id = '', $otype = '')
    {
        $value = $this->input->post('val', TRUE);
        if ((int) $otype == 2)
        {
            $place                = 'orders';
            $dbf                  = 'oid';
            $arr['staffcomments'] = $value;
        }
        else
        {
            $place        = 'ebay_transactions';
            $dbf          = 'rec';
            $arr['notes'] = $value;
        }
        $id = (int) $id;
        $this->db->update($place, $arr, array(
            $dbf => $id
        ));
        echo ($value);
    }
    function SaveOrderAccounted($id = '', $otype = '')
    {
        $value = (int) $this->input->post('val', TRUE);
        if ($value != 1)
            $value = 0;
        if ($otype == 2)
        {
            $place = 'orders';
            $dbf   = 'oid';
        }
        else
        {
            $place = 'ebay_transactions';
            $dbf   = 'rec';
        }
        $this->db->update($place, array(
            'accounted' => (int) $value
        ), array(
            $dbf => (int) $id
        ));
        echo ((int) $value);
    }
    function cRev()
    {
        $q = $this->db->count_all_results('ebay_revise');
        if ($q > 0)
            echo '<a href="' . Site_url() . '/Myebay/RevPending" target="_blank"><span style="color:red;">' . $q . '</span> <img src="/images/updtebay.png" />';
        else
            echo '';
    }
    function RevPending($id = 0)
    {
        if ($id > 0)
        {
            $this->db->where('er_id', (int) $id);
            $this->db->delete('ebay_revise');
            Redirect('Myebay/RevPending');
        }
        $this->db->order_by("er_id", "DESC");
        $q = $this->db->get('ebay_revise');
        if ($q->num_rows() > 0)
            $this->mysmarty->assign('erlist', $q->result_array());
        else
            $this->mysmarty->assign('erlist', false);
        $this->mysmarty->assign('noenter', '<script type="text/javascript" src="/js/warehouse.js"></script>');
        $this->mysmarty->view('myebay/myebay_revpending.html');
    }
    function ReviseLog($page = 1, $sev = 0)
    {
        $this->Auth_model->CheckListings();
        if ($sev != 0)
            $this->mysmarty->assign('reviselog', TRUE);
        $this->mysmarty->assign('floatmenu', TRUE);
        $tdf   = 46800;
        $from  = explode('/', $this->session->userdata('navfrom'));
        $ofrom = mktime(23, 59, 59, $from[0], $from[1], $from[2]) + $tdf;
        $to    = explode('/', $this->session->userdata('navto'));
        $oto   = mktime(0, 0, 0, $to[0], $to[1], $to[2]) + $tdf;
        $this->mysmarty->assign('page', (int) $page);
        if ((int) $page > 0)
            $page--;
        $this->db->order_by("erlid", "DESC");
        if ($sev != 0)
            $this->db->where('sev !=', 0);
        $this->db->where('atmk <=', $ofrom);
        $this->db->where('atmk >=', $oto);
        $this->db->limit(500, (int) $page * 500);
        $q = $this->db->get('ebay_revise_log');
        $this->mysmarty->assign('list', $q->result_array());
        if ($sev != 0)
            $this->db->where('sev !=', 0);
        $this->db->where('atmk <=', $ofrom);
        $this->db->where('atmk >=', $oto);
        $countall = $this->db->count_all_results('ebay_revise_log');
        $pages    = ceil($countall / 500);
        for ($counter = 1; $counter <= $pages; $counter++)
            $pagearray[] = $counter;
        $this->mysmarty->assign('pages', $pagearray);
        $this->mysmarty->assign('sev', $sev);
        $this->mysmarty->view('myebay/myebay_reviselog.html');
    }
    function weightset($page = 1)
    {
        $this->Auth_model->CheckListings();
        $this->mysmarty->assign('page', (int) $page);
        if ((int) $page > 0)
            $page--;
        $this->db->select("e_id, e_title, weight_lbs, weight_oz");
        $this->db->order_by("weight_kg", 'DESC');
        $this->db->order_by("e_id", "DESC");
        $this->db->limit(500, (int) $page * 500);
        $q       = $this->db->get('ebay');
        $updated = array();
        foreach ($q->result_array() as $k => $v)
        {
            $list[$k] = $v;
            if (isset($_POST['items'][$v['e_id']]))
            {
                if ($v['weight_lbs'] != (float) $_POST['items'][$v['e_id']]['lbs'] || $v['weight_oz'] != (float) $_POST['items'][$v['e_id']]['oz'])
                {
                    $this->db->update('ebay', array(
                        'weight_lbs' => (float) $_POST['items'][$v['e_id']]['lbs'],
                        'weight_oz' => (float) $_POST['items'][$v['e_id']]['oz'],
                        'weight_kg' => lbsoz2kg((float) $_POST['items'][$v['e_id']]['lbs'], (float) $_POST['items'][$v['e_id']]['oz'])
                    ), array(
                        'e_id' => (int) $_POST['items'][$v['e_id']]['eid']
                    ));
                    $list[$k]['weight_lbs']    = (float) $_POST['items'][$v['e_id']]['lbs'];
                    $list[$k]['weight_oz']     = (float) $_POST['items'][$v['e_id']]['oz'];
                    $updated[(int) $v['e_id']] = TRUE;
                }
            }
        }
        if ($_POST)
            $this->mysmarty->assign('changed', count($updated));
        $this->mysmarty->assign('list', $list);
        $this->mysmarty->assign('updated', $updated);
        $countall = $this->db->count_all_results('ebay');
        $pages    = ceil($countall / 500);
        for ($counter = 1; $counter <= $pages; $counter++)
            $pagearray[] = $counter;
        $this->mysmarty->assign('pages', $pagearray);
        $this->mysmarty->view('myebay/myebay_weight.html');
    }
    function gtset($page = 1)
    {
        $this->Auth_model->CheckListings();
        $this->mysmarty->assign('page', (int) $page);
        if ((int) $page > 0)
            $page--;
        $this->mysmarty->assign('taxonomy', $this->_gTaxonomy());
        $this->db->select("e_id, e_title, gtaxonomy");
        $this->db->order_by("gtaxonomy", "ASC");
        $this->db->order_by("e_id", "DESC");
        $this->db->limit(100, (int) $page * 100);
        $q       = $this->db->get('ebay');
        $updated = array();
        foreach ($q->result_array() as $k => $v)
        {
            $list[$k] = $v;
            if (isset($_POST['items'][$v['e_id']]))
            {
                $this->db->update('ebay', array(
                    'gtaxonomy' => $_POST['items'][$v['e_id']]['gtaxonomy']
                ), array(
                    'e_id' => (int) $_POST['items'][$v['e_id']]['eid']
                ));
                $list[$k]['gtaxonomy']     = $_POST['items'][$v['e_id']]['gtaxonomy'];
                $updated[(int) $v['e_id']] = TRUE;
            }
        }
        if ($_POST)
            $this->mysmarty->assign('changed', count($updated));
        $this->mysmarty->assign('list', $list);
        $this->mysmarty->assign('updated', $updated);
        $countall = $this->db->count_all_results('ebay');
        $pages    = ceil($countall / 100);
        for ($counter = 1; $counter <= $pages; $counter++)
            $pagearray[] = $counter;
        $this->mysmarty->assign('pages', $pagearray);
        $this->mysmarty->assign('filled', $this->Myebay_model->GetTaxonomyValue(TRUE));
        $this->mysmarty->assign('unfilled', $this->Myebay_model->GetTaxonomyValue());
        $this->mysmarty->view('myebay/myebay_gtaxonomy.html');
    }
    function _gTaxonomy()
    {
        $list = taxonomyfill();
        return explode("\n", $list);
    }
    function getrevised()
    {
        $this->db->select("e_id");
        $this->db->order_by("e_id", "DESC");
        $this->db->where("autorev", 1);
        $this->query = $this->db->get('ebay');
        if ($this->query->num_rows() > 0)
        {
            foreach ($this->query->result_array() as $k => $v)
            {
                $this->db->update('ebay', array(
                    'autorev' => 0
                ), array(
                    'e_id' => (int) $v['e_id']
                ));
            }
        }
    }
    function GetModData($id = 0)
    {
        $this->Auth_model->CheckListings();
        if ((int) $id == 0)
            exit('Invalid ID');
        $adm = $this->Myebay_model->GetAdminList();
        $this->db->select("e_id, e_title, ebay_id, mods");
        $this->db->where('e_id', (int) $id);
        $this->query = $this->db->get('ebay');
        $this->mysmarty->assign('newlayout', TRUE);
        echo $this->mysmarty->fetch('header.html');
        echo $this->mysmarty->fetch('messages/error_success_msg.html');
        if ($this->query->num_rows() > 0)
        {
            $r = $this->query->row_array();
            echo 'Cron log for: ID: <strong>' . $r['e_id'] . '</strong> - EbayID: <strong>' . $r['ebay_id'] . '</strong> - <strong>' . $r['e_title'] . '</strong><br><Br>';
            if (strlen($r['mods']) > 15)
                $r['mods'] = unserialize($r['mods']);
            if (is_array($r['mods']))
            {
                foreach ($r['mods'] as $k => $v)
                {
                    echo '(' . ($k + 1) . ') Price from: <strong>' . $v['from'] . '</strong> to <strong>' . $v['to'] . '</strong> @ <strong>' . $v['date'] . '</strong><br>';
                }
            }
            else
                echo 'No Cron updates so far...';
            $data = $this->Myebay_model->GetActionLogItem($id);
            if ($data)
            {
                echo '<table cellpadding="5" cellspacing="3" border="0" class="admintable3">
				  <tr>
					<th>ID</th>
					<th>Local</th>
					<th>Ebay/WH</th>
					<th>Admin</th>
					<th>Action</th>
					<th>Field</th>
					<th>From/Info</th>
					<th>To</th>
					<th>Trans.</th>
					<th>Time</th>
				  </tr>';
                foreach ($data as $k => $l)
                {
                    if ($l['t'] == 'E')
                    {
                        if (isset($this->actabrv[$l['field']]))
                            $l['field'] = $this->actabrv[$l['field']];
                        echo '<tr>
				   <td valign="top">E' . $l['al_id'] . '</td>
					<td valign="top">' . $l['e_id'] . '</td>
					<td valign="top">' . $l['ebay_id'] . '</td>
					<td valign="top" nowrap>' . $l['admin'] . '</td>
					<td valign="top">' . $l['ctrl'] . '</td>
					<td valign="top" nowrap>' . $l['field'] . '</td>
					<td valign="top">' . $l['datafrom'] . '</td>
					<td valign="top">' . $l['datato'] . '</td>
					<td valign="top">' . $l['trans_id'] . '</td>
					<td valign="top" nowrap>' . $l['time'] . '</td>
					</tr>';
                    }
                    elseif ($l['t'] == 'R')
                        echo '<tr>
				   <td valign="top" rowspan="2">R' . $l['erlid'] . '</td>
					<td valign="top">' . $l['eid'] . '</td>
					<td valign="top">N/A</td>
					<td valign="top">N/A</td>
					<td valign="top">DoRevise</td>
					<td valign="top">' . strtoupper($l['type']) . '</td>
					<td valign="top">' . $l['oldvalue'] . '</td>
					<td valign="top">' . $l['value'] . '</td>
					<td valign="top">N/A</td>
					<td valign="top" nowrap>' . $l['attime'] . '</td>
					</tr><tr><td colspan="9">' . $l['response'] . '</td></tr>';
                    elseif ($l['t'] == 'C')
                        echo '<tr>
				   <td valign="top">C' . $l['ec_id'] . '</td>
					<td valign="top">' . $l['e_id'] . '</td>
					<td valign="top">N/A</td>
					<td valign="top">N/A</td>
					<td valign="top">CronLog</td>
					<td valign="top" colspan="3">' . $l['data'] . '</td>
					<td valign="top">N/A</td>
					<td valign="top" nowrap>' . $l['time'] . '</td>
					</tr>';
                    else
                        echo '<tr>
				   <td valign="top">W' . $l['wl_id'] . '</td>
					<td valign="top" nowrap>' . $l['bcn'] . '</td>
					<td valign="top" nowrap>' . $l['wid'] . '</td>
					<td valign="top" nowrap>' . $adm[$l['admin']] . '</td>
					<td valign="top">' . $l['ctrl'] . '</td>
					<td valign="top" nowrap>' . $l['field'] . '</td>
					<td valign="top">' . $l['datafrom'] . '</td>
					<td valign="top">' . $l['datato'] . '</td>
					<td valign="top">N/A</td>
					<td valign="top" nowrap>' . $l['time'] . '</td>
					</tr>';
                }
                echo '</table>';
            }
        }
        echo $this->mysmarty->fetch('footer.html');
    }
    function ModLog()
    {
        $this->Auth_model->CheckListings();
        $this->db->order_by("ec_id", "DESC");
        $q = $this->db->get('ebay_cron');
        $r = false;
        if ($q->num_rows() > 0)
        {
            $r = $q->result_array();
            foreach ($r as $k => $v)
            {
                $r[$k]['data'] = unserialize($v['data']);
            }
        }
        $this->mysmarty->assign('modlog', $r);
        $this->mysmarty->view('myebay/myebay_modlog.html');
    }
    function Search($id = 0)
    {
        $this->Auth_model->CheckListings();
        if ((int) $id == 0)
            Redirect('ListItems');
        $this->session->set_userdata('last_string', (int) $id);
        $this->session->set_userdata('last_where', 3);
        $this->ListItems(1, TRUE);
    }
    function nav($row = 0)
    {
        switch ($row)
        {
            case 2:
            case 3:
            case 4:
            case 9:
            case 10:
            case 11:
                Redirect('Mywarehouse/GetEbayLiveDBData/0/' . $row);
                break;
        }
        //$this->ListItems();
    }
    function switchmenu($func = '')
    {
        if ($func == 'open')
            $this->session->set_userdata('flnav', 0);
        else
            $this->session->set_userdata('flnav', 1);
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }
    function showitem($eid = 0)
    {
        $_POST['search'] = (int) $eid;
        $_POST['where']  = 3;
        $this->ListItems();
    }
    function showmatch($fromitemid = 0)
    {
        $this->mysmarty->assign('fromitemid', (int) $fromitemid);
        //$_POST['search'] = htmlspecialchars(stripslashes($_POST['search']));
        $_POST['where'] = 0;
        $this->ListItems();
    }
    function viewport($view = 'clear', $page = 1)
    {
        $this->load->model('Myautopilot_model');
        $this->mysmarty->assign('prules', $this->Myautopilot_model->GetPredefined());
        $this->mysmarty->assign('floatmenu', TRUE);
        $this->Auth_model->CheckListings();
        $this->mysmarty->assign('hot', TRUE);
        $this->load->model('Myseller_model');
        $this->statuses = $this->Myseller_model->assignstatuses();
        $this->session->unset_userdata('gotcats');
        switch ($view)
        {
            case "ActiveWebsite":
            case "ActiveEbayLocal":
            case "ActiveEbayLocalBCNQTYMatch":
            case "ActiveEbayLocalBCNQTYMisMatch":
            case "ActiveEbayActiveBCNQTYMisMatch":
            case "ActiveEbayAssignedMisMatch":
            case "ActiveEbayLocalGhost":
            case "ActiveEbayLocalGreens":
            case "NoTransBCNs":
            case "InActiveWebsite":
            case "InActiveEbayLocal":
            case "InActiveEbayLocalBCNQTYMatch":
            case "InActiveEbayLocalBCNQTYMisMatch":
            case "InActiveEbayLocalGhost":
            case "NeedsRelisting":
            case "NeverList":
            case "nlnotitle":
            case "nlbcn":
            case "Audited":
            case "NotAudited":
            case "BcnsAudited":
            case "BcnsNotAudited":
            case "MisMatched":
            case "OutOfStock":
            case "OosKeepAlive":
            case "Autopilot":
            case "Debugpilot":
            case "Expiredpilot":
            case "Competitor":
            case "Dispose":
            case "Ebayspec":
            case "NoEbayspec":
            case "ChannelUnmatch":
                $data  = $this->Myebay_model->Viewport($view, (int) $page);
                $pages = count($data['pages']);
                for ($counter = 1; $counter <= $pages; $counter++)
                {
                    $before = 12;
                    $after  = 12;
                    $min    = (int) $page - $before;
                    if ($min < 0)
                        $after = $before - $min;
                    $max = (int) $page + $after;
                    if ($max > $pages)
                        $before = $before + ($max - $pages);
                    if (($counter >= ((int) $page - $before)) && ($counter <= ((int) $page + $after)))
                    {
                        $pagearray[] = $counter;
                    }
                }
                $this->mysmarty->assign('pagearray', $pagearray);
                $this->mysmarty->assign('viewport', $view);
                break;
            default:
                Redirect('Myebay');
                break;
        }
        $this->mysmarty->assign('adm', $this->Myebay_model->GetAdminList());
        $this->mysmarty->assign('list', $data['results']);
        $this->mysmarty->assign('pages', $data['pages']);
        $this->mysmarty->assign('page', (int) $page);
        $this->mysmarty->assign('total', (int) $data['total']);
        /*
        
        $this->load->helper('directory');
        $this->load->helper('file');
        $responseXml = read_file($this->config->config['ebaypath'].'/cats.txt');
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
        asort($sc);
        $this->mysmarty->assign('store', $sc);
        */
        $this->db->where("notebay", 0);
        $this->db->orderby('listorder', 'ASC');
        $categories = $this->db->get("warehouse_sku_categories")->result_array();
        $this->mysmarty->assign('dbstore', $categories);
        $this->_getQNPendingRev();
        $this->mysmarty->view('myebay/myebay_show.html');
    }
    function ListSearch($page = 1)
    {
        $this->ListItems((int) $page, TRUE);
    }
    function tablize($eid = 0)
    {
        $this->load->model('Myseller_model');
        $this->Myseller_model->getBase(array(
            (int) $eid
        ));
        $this->mysmarty->assign('listingid', (int) $eid);
        $this->mysmarty->view('myebay/myebay_tablize.html');
    }
    function CronEventsLog()
    {
        $this->Auth_model->CheckListings();
        $this->db->order_by("se_id", "DESC");
        $q = $this->db->get('ebay_sellerevents');
        if ($q->num_rows() > 0)
        {
            foreach ($q->result_array() as $k => $r)
            {
                $res[$k]               = $r;
                $d                     = explode('-', $res[$k]['run']);
                $res[$k]['daterun']    = trim($d[1]);
                $res[$k]['notfndlist'] = unserialize($res[$k]['notfndlist']);
                $res[$k]['duplist']    = unserialize($res[$k]['duplist']);
            }
            $this->mysmarty->assign('list', $res);
        }
        $this->mysmarty->view('myebay/myebay_cronevlog.html');
    }
    function LikeItem($ebayid = '')
    {
        $this->Auth_model->CheckListings();
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
        $requestXmlBody .= '<ItemID>' . (int) $ebayid . '</ItemID>

		</GetItemRequest>';
        $verb        = 'GetItem';
        $session     = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
        $responseXml = $session->sendHttpRequest($requestXmlBody);
        if (stristr($responseXml, 'HTTP 404') || $responseXml == '')
            die('<P>Error sending request');
        $xml    = simplexml_load_string($responseXml);
        //printcool ((string)$xml->Item->Title);
        $string = str_replace("'", "", (string) $xml->Item->Title);
        $string = str_replace('"', '', $string);
        $this->db->like('e_title', $string);
        $this->db->where('ebay_id !=', (int) $ebayid);
        $qs = $this->db->get('ebay');
        if ($qs->num_rows() > 0)
        {
            $this->mysmarty->assign('list', $qs->result_array());
        }
        else
        {
            $this->mysmarty->assign('list', false);
        }
        $this->mysmarty->assign('parent', (int) $ebayid);
        $this->mysmarty->view('myebay/myebay_likeitem.html');
    }
    function ReplaceEbayId($eid = '', $parent = '')
    {
        $this->Auth_model->CheckListings();
        $this->ReWaterMark((int) $id);
        $item = $this->Myebay_model->GetItem((int) $eid);
        if (!$item)
            $item['ebay_id'] = 0;
        if ((int) $eid != 0 && (int) $parent != 0)
            $this->db->update('ebay', array(
                'ebay_id' => (int) $parent,
                'ebended' => NULL
            ), array(
                'e_id' => $eid
            ));
        $ra['admin']    = $this->session->userdata['ownnames'];
        $ra['time']     = CurrentTimeR();
        $ra['ctrl']     = 'ReplaceLike';
        $ra['field']    = 'ebay_id';
        $ra['atype']    = 'M';
        $ra['e_id']     = (int) $eid;
        $ra['field']    = 'ebay_id';
        $ra['ebay']     = (int) $parent;
        $ra['time']     = CurrentTime();
        $ra['datafrom'] = $item['ebay_id'];
        $ra['datato']   = (int) $parent;
        $this->db->insert('ebay_actionlog', $ra);
        Redirect('Myebay/LikeItem/' . (int) $parent);
    }
    function ReWaterMark($id = '')
    {
        //////////////////////////////////////////////
        $this->db->select("e_img1, e_img2, e_img3, e_img4, nwm");
        $this->db->where('e_id', (int) $id);
        $r = $this->db->get('ebay');
        if ($r->num_rows() > 0)
        {
            $r = $r->row_array();
            if ($r['nwm'] == 0)
            {
                if ($r['e_img1'] != '')
                    $imgs[] = $r['e_img1'];
                if ($r['e_img2'] != '')
                    $imgs[] = $r['e_img2'];
                if ($r['e_img3'] != '')
                    $imgs[] = $r['e_img3'];
                if ($r['e_img4'] != '')
                    $imgs[] = $r['e_img4'];
                if ($r['e_img5'] != '')
                    $imgs[] = $r['e_img5'];
                if ($r['e_img6'] != '')
                    $imgs[] = $r['e_img6'];
                if ($r['e_img7'] != '')
                    $imgs[] = $r['e_img7'];
                if ($r['e_img8'] != '')
                    $imgs[] = $r['e_img8'];
                $change = 0;
                foreach ($imgs as $i)
                {
                    if (file_exists($this->config->config['paths']['imgebay'] . '/' . idpath((int) $id) . 'Original_' . $i))
                    {
                        //echo 'File Exists '.$i;
                        if (file_exists($this->config->config['paths']['imgebay'] . '/' . idpath((int) $id) . 'thumb_main_' . $i))
                            unlink($this->config->config['paths']['imgebay'] . '/' . idpath((int) $id) . 'thumb_main_' . $i);
                        if (file_exists($this->config->config['paths']['imgebay'] . '/' . idpath((int) $id) . 'thumb_' . $i))
                            unlink($this->config->config['paths']['imgebay'] . '/' . idpath((int) $id) . 'thumb_' . $i);
                        if (file_exists($this->config->config['paths']['imgebay'] . '/' . idpath((int) $id) . 'Ebay_' . $i))
                            unlink($this->config->config['paths']['imgebay'] . '/' . idpath((int) $id) . 'Ebay_' . $i);
                        if (file_exists($this->config->config['paths']['imgebay'] . '/' . idpath((int) $id) . $i))
                            unlink($this->config->config['paths']['imgebay'] . '/' . idpath((int) $id) . $i);
                    }
                    else
                        echo 'File not found ' . $i;
                    $this->_ReApplyWaterMark((int) $id, $i);
                    $change++;
                }
                if ($change > 0)
                    $this->db->update('ebay', array(
                        'nwm' => 1
                    ), array(
                        'e_id' => (int) $id
                    ));
            }
        }
        else
            exit('ERROR WARKING. ACTION IS CANCELLED. CONTACT ADMINISTRATOR');
        //echo 'go';
        //$this->db->update('ebay', array('nwm' => 0));
        //SubmitEbay
        //UpdateFromEbay
        //ReSubmitEbay
        //////////////////////////////////////////
    }
    function _ReApplyWaterMark($id, $filename)
    {
        $sourcefilename = $this->config->config['paths']['imgebay'] . '/' . idpath($id) . 'Original_' . $filename;
        if (!copy($sourcefilename, $this->config->config['paths']['imgebay'] . '/' . idpath((int) $id) . $filename))
        {
            //$filename = str_replace('.jpg', '.JPG', $filename);
            $sourcefilename = str_replace('.jpg', '.JPG', $sourcefilename);
            if (!copy($sourcefilename, $this->config->config['paths']['imgebay'] . '/' . idpath((int) $id) . $filename))
            {
                echo "failed to copy Ebay_file...\n";
                break;
            }
        }
        $this->load->library('image_lib');
        $econfig['image_library']  = 'gd2';
        $econfig['source_image']   = $sourcefilename;
        $econfig['create_thumb']   = FALSE;
        $econfig['maintain_ratio'] = TRUE;
        $econfig['width']          = '600';
        $econfig['new_image']      = 'Ebay_' . $filename;
        $this->image_lib->initialize($econfig);
        $this->image_lib->resize();
        $this->image_lib->clear();
        //printcool ($econfig);
        $iconfig['image_library']  = 'gd2';
        $iconfig['source_image']   = $sourcefilename;
        $iconfig['create_thumb']   = TRUE;
        $iconfig['maintain_ratio'] = TRUE;
        $iconfig['width']          = $this->config->config['sizes']['ebayimg']['width'];
        $iconfig['height']         = $this->config->config['sizes']['ebayimg']['height'];
        $iconfig['new_image']      = 'thumb_' . $filename;
        $this->image_lib->initialize($iconfig);
        $this->image_lib->resize();
        $this->image_lib->clear();
        //printcool ($iconfig);
        $nconfig['image_library']  = 'gd2';
        $nconfig['source_image']   = $sourcefilename;
        $nconfig['create_thumb']   = TRUE;
        $nconfig['maintain_ratio'] = TRUE;
        $nconfig['new_image']      = 'thumb_main_' . $filename;
        $nconfig['width']          = '200';
        $nconfig['height']         = '200';
        $this->image_lib->initialize($nconfig);
        $this->image_lib->resize();
        $this->image_lib->clear();
        //printcool ($nconfig);
        $this->_WaterMark('bottom', 'right', 'wm_original_ebay.png', $this->config->config['paths']['imgebay'] . '/' . idpath((int) $id), 'Ebay_' . $filename);
        $this->_WaterMark('bottom', 'right', 'wm_original_bottom.png', $this->config->config['paths']['imgebay'] . '/' . idpath((int) $id), $filename);
        $this->_WaterMark('middle', 'center', 'wm_original_center.png', $this->config->config['paths']['imgebay'] . '/' . idpath((int) $id), $filename);
        $this->_WaterMark('bottom', 'right', 'wm_bigtn_bottom.png', $this->config->config['paths']['imgebay'] . '/' . idpath((int) $id), 'thumb_main_' . $filename);
        $this->_WaterMark('middle', 'center', 'wm_bigtn_center.png', $this->config->config['paths']['imgebay'] . '/' . idpath((int) $id), 'thumb_main_' . $filename);
        $this->_WaterMark('bottom', 'right', 'wm_smalltn_bottom.png', $this->config->config['paths']['imgebay'] . '/' . idpath((int) $id), 'thumb_' . $filename);
    }
    function _WaterMark($val, $hal, $wm, $path = '', $file = '')
    {
        $this->load->library('image_lib');
        $config['source_image']     = $path . '/' . $file;
        $config['wm_type']          = 'overlay';
        $config['wm_overlay_path']  = $this->config->config['pathtopublic'] . '/images/' . $wm;
        $config['wm_vrt_alignment'] = $val;
        $config['wm_hor_alignment'] = $hal;
        $config['create_thumb']     = FALSE;
        $config['wm_padding']       = '0';
        //printcool ($config);
        $this->image_lib->initialize($config);
        $this->image_lib->watermark();
        //printcool ($this->image_lib->display_errors());
        $this->image_lib->clear();
    }
    function ChangeItemId($eid = '', $page = 1)
    {
        $this->Auth_model->CheckListings();
        if ((int) $_POST['itemid'] > 0 && (int) $eid > 0)
        {
            $this->ReWaterMark((int) $id);
            $item = $this->Myebay_model->GetItem((int) $eid);
            if (!$item)
                $item['ebay_id'] = 0;
            $this->db->update('ebay', array(
                'ebay_id' => (int) $_POST['itemid'],
                'ebended' => NULL
            ), array(
                'e_id' => (int) $eid
            ));
            $ra['admin']    = $this->session->userdata['ownnames'];
            $ra['time']     = CurrentTimeR();
            $ra['ctrl']     = 'ChangeItemID';
            $ra['field']    = 'ebay_id';
            $ra['atype']    = 'M';
            $ra['local']    = (int) $eid;
            $ra['field']    = 'ebay_id';
            $ra['ebay']     = (int) $_POST['itemid'];
            $ra['time']     = CurrentTime();
            $ra['datafrom'] = $item['ebay_id'];
            $ra['datato']   = (int) $_POST['itemid'];
            $this->db->insert('ebay_actionlog', $ra);
        }
        Redirect('Myebay/ListItems/' . (int) $page . '#' . (int) $eid);
    }
    function RevertAction($alid = 0, $page = 1)
    {
        $this->Auth_model->CheckListings();
        $this->db->where('al_id', (int) $alid);
        $q = $this->db->get('ebay_actionlog');
        if ($q->num_rows() > 0)
        {
            $ra = $q->row_array();
            unset($this->actabrv['Ebay Quantity']);
            $revactabrv = array_flip($this->actabrv);
            if (isset($revactabrv[$ra['field']]))
                $ra['field'] = $revactabrv[$ra['field']];
            //printcool ($ra);
            if ($ra['field'] == 'sn')
                $this->db->update('ebay_transactions', array(
                    $ra['field'] => $ra['datafrom']
                ), array(
                    'rec' => (int) $ra['trans_id']
                ));
            elseif ($ra['field'] == 'asc')
                $this->db->update('ebay_transactions', array(
                    $ra['field'] => $ra['datafrom']
                ), array(
                    'rec' => (int) $ra['trans_id']
                ));
             else
                $this->db->update('ebay', array(
                    $ra['field'] => $ra['datafrom']
                ), array(
                    'e_id' => (int) $ra['e_id']
                ));
            $ra['admin'] = $this->session->userdata['ownnames'];
            $ra['time']  = CurrentTimeR();
            unset($ra['al_id']);
            $ra['ctrl']     = 'Revert';
            $from           = $ra['datafrom'];
            $ra['datafrom'] = $ra['datato'];
            $ra['datato']   = $from;
            $this->db->insert('ebay_actionlog', $ra);
            //printcool ($ra);
            //printcool ($revactabrv);
            if ($ra['field'] == 'e_part')
            {
                $this->ReviseEbayDescription($ra['e_id']);
                echo '<span style="font-size:14px; font-weight:bold; color:#0066C;"><br><br>Redirecting back in 2 seconds...</span><script type="text/JavaScript">

<!--

setTimeout("location.href = \'' . Site_url() . 'Myebay/ActionLog/' . $page . '#' . (int) $alid . '\';",4000);

-->

</script>';
                exit();
            }
            if ($ra['field'] == 'e_qpart')
            {
                $this->EbayInventoryUpdate((int) $ra['e_id'], false);
                echo '<span style="font-size:14px; font-weight:bold; color:#0066C;"><br><br>Redirecting back in 2 seconds...</span><script type="text/JavaScript">

<!--

setTimeout("location.href = \'' . Site_url() . 'Myebay/ActionLog/' . $page . '#' . (int) $alid . '\';",4000);

-->

</script>';
                exit();
            }
        }
        Redirect('Myebay/ActionLog/' . $page . '#' . (int) $alid);
    }
    function ReviseEbayDescription($id = 0, $page = false, $save = false)
    {
        $this->Auth_model->CheckListings();
        if (isset($_POST['eid']))
            $id = (int) $_POST['eid'];
        if ((int) $id > 0)
        {
            $this->session->set_flashdata('action', (int) $id);
            //redirect("Myebay");
            set_time_limit(90);
            $item = $this->Myebay_model->GetItem((int) $id);
            if (!$item)
            {
                echo 'Item not found!';
                exit();
            }
            require_once($this->config->config['ebaypath'] . 'get-common/keys.php');
            require_once($this->config->config['ebaypath'] . 'get-common/eBaySession.php');
            ini_set('magic_quotes_gpc', false);
            $this->mysmarty->assign('displays', $item);
            $listDescHtml        = htmlspecialchars($this->mysmarty->fetch('myebay/myebay_sendsource.html'));
            $requestXmlBodySTART = '<?xml version="1.0" encoding="utf-8"?>

<ReviseItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
            $requestXmlBody      = "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
            $requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
            //<VerifyOnly>".TRUE."</VerifyOnly>
            $requestXmlBody .= "<Version>$compatabilityLevel</Version>";
            $requestXmlBody .= "<Item>

    <Description>" . $listDescHtml . "</Description>

    <DescriptionReviseMode>Replace</DescriptionReviseMode>

    <ItemID>" . $item['ebay_id'] . "</ItemID>";
            $requestXmlBody .= "</Item>";
            $requestXmlBodyEND = '</ReviseItemRequest>';
            $session           = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], 'ReviseItem');
            $responseXml       = $session->sendHttpRequest($requestXmlBodySTART . $requestXmlBody . $requestXmlBodyEND);
            if (stristr($responseXml, 'HTTP 404') || $responseXml == '')
                die('<P>Error sending request');
            $responseDoc = new DomDocument();
            $responseDoc->loadXML($responseXml);
            $errors = $responseDoc->getElementsByTagName('Errors');
            if ($errors->length > 0)
            {
                echo '<P><B>eBay returned the following error(s):</B>';
                $code     = $errors->item(0)->getElementsByTagName('ErrorCode');
                $shortMsg = $errors->item(0)->getElementsByTagName('ShortMessage');
                $longMsg  = $errors->item(0)->getElementsByTagName('LongMessage');
                echo '<P>', $code->item(0)->nodeValue, ' : ', str_replace(">", "&gt;", str_replace("<", "&lt;", $shortMsg->item(0)->nodeValue));
                if (count($longMsg) > 0)
                    echo '<BR>', str_replace(">", "&gt;", str_replace("<", "&lt;", $longMsg->item(0)->nodeValue));
                $this->_recordsubmiterror(array(
                    'msg_title' => 'REVISE DESCRIPTION ERRORS ' . (int) $id . ' @' . CurrentTime(),
                    'msg_body' => printcool(str_replace(">", "&gt;", str_replace("<", "&lt;", $longMsg->item(0)->nodeValue)), TRUE),
                    'msg_date' => CurrentTime()
                ));
                //if ($save) $this->db->update('ebay', array('autorev' => -1, 'autorevtxt' => str_replace(">", "&gt;", str_replace("<", "&lt;", $longMsg->item(0)->nodeValue)).' @ '.CurrentTime()), array('e_id' => (int)$id));
            }
            else //no errors
            {
                //get results nodes
                $responses = $responseDoc->getElementsByTagName("ReviseItemResponse");
                $txtresp   = '';
                foreach ($responses as $response)
                {
                    $acks = $response->getElementsByTagName("Ack");
                    $ack  = $acks->item(0)->nodeValue;
                    $txtresp .= 'Result: ' . $ack . '<br>';
                } // foreach response
                if (isset($_POST['eid']))
                    exit($txtresp);
                //GoMail(array ('msg_title' => 'REVISED DESCRIPTION '.(int)$id.' @'.CurrentTime(), 'msg_body' => $txtresp, 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
                //if ($save) $this->db->update('ebay', array('autorev' => 1, 'autorevtxt' => '@ '.CurrentTime()), array('e_id' => (int)$id));
            }
            /*
            
            
            
            $this->db->select("e_id");
            
            $this->db->where("autorev", 0);
            
            $this->db->where("ebay_id > ", 0);
            
            $this->db->order_by("e_id", "DESC");
            
            $this->query = $this->db->get('ebay');
            
            if ($this->query->num_rows() > 0) { $goto = $this->query->row_array();
            
            echo $goto['e_id'];
            
            echo '<script type="text/JavaScript">
            
            <!--
            
            setTimeout("location.href = \'http://www.la-tronics.com/Myebay/ReviseEbayDescription/'.$goto['e_id'].'\';",30000);
            
            -->
            
            </script>'
            
            
            
            
            
            
            
            }
            
            ;*/
        }
        if ($page)
        {
            //Redirect('Myebay/ListItems/'.(int)$page.'#'.(int)$id);
            echo '<span style="font-size:14px; font-weight:bold; color:#0066C;"><br><br>Redirecting back in 2 seconds...</span><script type="text/JavaScript">

<!--

setTimeout("location.href = \'' . Site_url() . 'Myebay/ListItems/' . (int) $page . '#' . (int) $id . '\';",4000);

-->

</script>';
        }
    }
    function _recordsubmiterror($err = array())
    {
        //GoMail($err, $this->config->config['support_email'], $this->config->config['no_reply_email']);
        $err['admin'] = $this->session->userdata['ownnames'];
        $this->db->insert('ebay_submitlog', $err);
    }
    function EbayInventoryUpdate($id = 0, $page = false)
    {
        $this->Auth_model->CheckListings();
        if ((int) $id > 0)
        {
            $this->session->set_flashdata('action', (int) $id);
            //redirect("Myebay");
            set_time_limit(90);
            $item = $this->Myebay_model->GetItem((int) $id);
            if (!$item)
            {
                echo 'Item not found!';
                exit();
            }
            require($this->config->config['ebaypath'] . 'get-common/keys.php');
            require_once($this->config->config['ebaypath'] . 'get-common/eBaySession.php');
            $requestXmlBody = '<?xml version="1.0" encoding="utf-8" ?>';
            $requestXmlBody .= '<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
            $requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
            $requestXmlBody .= '<DetailLevel>ItemReturnAttributes</DetailLevel>';
            $requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
            $requestXmlBody .= "<Version>$compatabilityLevel</Version>";
            $requestXmlBody .= '<ItemID>' . (int) $item['ebay_id'] . '</ItemID></GetItemRequest>';
            $verb        = 'GetItem';
            $session     = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
            $responseXml = $session->sendHttpRequest($requestXmlBody);
            if (stristr($responseXml, 'HTTP 404') || $responseXml == '')
                die('<P>Error sending request');
            $xml = simplexml_load_string($responseXml);
            if ((string) $xml->Item->ItemID == '')
            {
                echo 'ERROR: Invalid Item ID...<a href="javascript:history.back()">Back</a>';
                exit();
            }
            $oldebayvalue   = (string) $xml->Item->Quantity;
            $requestXmlBody = '<?xml version="1.0" encoding="utf-8"?>

<ReviseInventoryStatusRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
            $requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
            $requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
            $requestXmlBody .= "<Version>$compatabilityLevel</Version>

						";
            //<Quantity>".$item['quantity']."</Quantity>
            $requestXmlBody .= "<InventoryStatus>

			    <ItemID>" . $item['ebay_id'] . "</ItemID>

			    <Quantity>" . $item['qn_ch1'] . "</Quantity>

				</InventoryStatus>

				</ReviseInventoryStatusRequest>";
            //GoMail(array ('msg_title' => 'INVENTORY UPDATED '.(int)$id.' @'.CurrentTime(), 'msg_body' => $requestXmlBody, 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
            $session     = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], 'ReviseInventoryStatus');
            $responseXml = $session->sendHttpRequest($requestXmlBody);
            if (stristr($responseXml, 'HTTP 404') || $responseXml == '')
                die('<P>Error sending request');
            $responseDoc = new DomDocument();
            $responseDoc->loadXML($responseXml);
            $errors = $responseDoc->getElementsByTagName('Errors');
            if ($errors->length > 0)
            {
                echo '<P><B>eBay returned the following error(s):</B>';
                $code     = $errors->item(0)->getElementsByTagName('ErrorCode');
                $shortMsg = $errors->item(0)->getElementsByTagName('ShortMessage');
                $longMsg  = $errors->item(0)->getElementsByTagName('LongMessage');
                echo '<P>', $code->item(0)->nodeValue, ' : ', str_replace(">", "&gt;", str_replace("<", "&lt;", $shortMsg->item(0)->nodeValue));
                if (count($longMsg) > 0)
                    echo '<BR>', str_replace(">", "&gt;", str_replace("<", "&lt;", $longMsg->item(0)->nodeValue));
            }
            else //no errors
            {
                //get results nodes
                $responses = $responseDoc->getElementsByTagName("ReviseInventoryStatusResponse");
                foreach ($responses as $response)
                {
                    $acks = $response->getElementsByTagName("Ack");
                    /*				*/
                    $ack  = $acks->item(0)->nodeValue;
                    $this->session->set_flashdata('success_msg', 'Result: ' . $ack);
                } // foreach response
                $this->db->update('ebay', array(
                    'ebayquantity' => $item['qn_ch1']
                ), array(
                    'e_id' => (int) $id
                ));
                $linkBase = "http://cgi.ebay.com/ws/eBayISAPI.dll?ViewItem&item=";
                $this->session->set_flashdata('action', (int) $id);
                $this->session->set_flashdata('gotoebay', $linkBase . $item['ebay_id']);
                $this->_logaction('EbayInventoryUpdate', 'Q', array(
                    'Quantity @ eBay' => $oldebayvalue
                ), array(
                    'Quantity @ eBay' => $item['qn_ch1']
                ), $id, $item['ebay_id'], 0);
                $this->_logaction('EbayInventoryUpdate', 'Q', array(
                    'Local eBay Quantity' => $item['ebayquantity']
                ), array(
                    'Local eBay Quantity' => $item['qn_ch1']
                ), $id, $item['ebay_id'], 0);
                //Redirect('Myebay/ListItems/'.(int)$page.'#'.(int)$id);
                if ($page)
                    echo '<span style="font-size:14px; font-weight:bold; color:#0066C;"><br><br>Redirecting back in 2 seconds...</span><script type="text/JavaScript">

<!--

setTimeout("location.href = \'' . Site_url() . 'Myebay/ListItems/' . (int) $page . '#' . (int) $id . '\';",4000);

-->

</script>';
            }
        }
    }
    function ActionLog($page = 1)
    {
        $this->Auth_model->CheckListings();
        $last_search = $this->session->userdata('last_search');
        if (isset($_POST['field']))
        {
            if (isset($_POST['append']))
                $last_search = $this->input->post('field', TRUE);
            else
            {
                $ls = $this->input->post('field', TRUE);
                foreach ($ls as $k => $v)
                {
                    if (trim($v) != '')
                        $last_search[$k] = trim($v);
                }
            }
        }
        $this->mysmarty->assign('lastsearch', $last_search);
        $this->session->set_userdata('last_search', $last_search);
        if (!$_POST && !$last_search)
            $this->session->set_userdata('page', $page);
        $this->mysmarty->assign('adm', $this->Myebay_model->GetAdminList());
        $data = $this->Myebay_model->GetActionLogS($last_search, $page, $this->actabrv);
        if ($data['results'])
        {
            foreach ($data['results'] as $k => $v)
            {
                $time                        = explode('-', $v['time']);
                $data['results'][$k]['date'] = trim($time[1]);
            }
        }
        $this->mysmarty->assign('list', $data['results']);
        $this->mysmarty->assign('pages', $data['pages']);
        $this->mysmarty->assign('page', (int) $page);
        $this->mysmarty->assign('abbr', $this->actabrv);
        $this->mysmarty->view('myebay/myebay_actionlog.html');
    }
    function AddFromStore($storeid = 0)
    {
        $this->Auth_model->CheckListings();
        if ((int) $storeid == 0)
            Redirect('Myebay');
        $itemid = $this->Myebay_model->GetStoreFirstProduct($storeid);
        if ($itemid)
            Redirect('Myebay/Add/0/' . $itemid);
        else
            Redirect('Myebay');
    }
    function CleanSearch()
    {
        $this->Auth_model->CheckListings();
        $this->session->unset_userdata('last_string');
        $this->session->unset_userdata('last_where');
        $this->session->unset_userdata('last_zero');
        $this->session->unset_userdata('last_mm');
        $this->session->unset_userdata('last_bcnmm');
        $this->session->unset_userdata('last_sitesell');
        Redirect('Myebay');
    }
    function CleanActionLogSearch()
    {
        $this->Auth_model->CheckListings();
        //$this->session->unset_userdata('last_string');
        //$this->session->unset_userdata('last_where');
        $this->session->unset_userdata('last_search');
        $page = $this->session->userdata['page'];
        if ((int) $page != 0)
            Redirect('Myebay/ActionLog/' . $page);
        else
            Redirect('Myebay/ActionLog');
    }
    function GetSource($itemid = '')
    {
        $this->Auth_model->CheckListings();
        $this->id = (int) $itemid;
        if ($this->id == 0)
            Redirect('Myebay');
        $this->displays = $this->Myebay_model->GetItem($this->id);
        $this->_GetSpecialAndTree();
        $this->mysmarty->assign('displays', $this->displays);
        $this->load->model('Settings_model');
        $this->Settings_model->GetEbayListingAddress();
        $this->mysmarty->view('myebay/myebay_source.html');
    }
    function _GetSpecialAndTree()
    {
        $this->load->model('Myproducts_model');
        $this->mysmarty->assign('catlist', $this->Myproducts_model->GetAllCategories());
        $this->mysmarty->assign('specials', $this->Myebay_model->GetTopSpecialAds());
    }
    function ViewTemplate($itemid = '')
    {
        $this->Auth_model->CheckListings();
        $this->id = (int) $itemid;
        if ($this->id == 0)
            Redirect('Myebay');
        $this->displays = $this->Myebay_model->GetItem($this->id);
        $this->_GetSpecialAndTree();
        $this->mysmarty->assign('displays', $this->displays);
        $this->load->model('Settings_model');
        $this->Settings_model->GetEbayListingAddress();
        $this->db->where("notebay", 0);
        $this->db->orderby('listorder', 'ASC');
        $categories = $this->db->get("warehouse_sku_categories")->result_array();
        $this->mysmarty->assign('dbstore', $categories);
        $this->mysmarty->view('myebay/myebay_template.html');
    }
    function Delete($id = '')
    {
        $this->Auth_model->CheckListings();
        $this->id = (int) $id;
        if ($this->id > 0)
        {
            $this->DeleteImageInEbay($this->id, '1', TRUE);
            $this->DeleteImageInEbay($this->id, '2', TRUE);
            $this->DeleteImageInEbay($this->id, '3', TRUE);
            $this->DeleteImageInEbay($this->id, '4', TRUE);
            $this->DeleteImageInEbay($this->id, '5', TRUE);
            $this->DeleteImageInEbay($this->id, '6', TRUE);
            $this->DeleteImageInEbay($this->id, '7', TRUE);
            $this->DeleteImageInEbay($this->id, '8', TRUE);
            $this->Myebay_model->Delete($this->id);
        }
        $this->session->set_flashdata('success_msg', 'Item ' . $this->id . ' Deleted');
        Redirect("Myebay");
    }
    function DeleteImageInEbay($id = '', $place = '', $nogo = FALSE)
    {
        $this->id    = (int) $id;
        $this->place = (int) $place;
        if (($this->id > 0) && ($this->place > 0))
        {
            $this->img = $this->Myebay_model->DeleteEbayImage($this->id, $this->place);
            if ($this->img != '')
            {
                $this->load->helper('directory');
                $this->load->helper('file');
                if (read_file($this->config->config['paths']['imgebay'] . '/' . idpath($this->id) . 'Ebay_' . $this->img))
                    unlink($this->config->config['paths']['imgebay'] . '/' . idpath($this->id) . 'Ebay_' . $this->img);
                if (read_file($this->config->config['paths']['imgebay'] . '/' . idpath($this->id) . $this->img))
                    unlink($this->config->config['paths']['imgebay'] . '/' . idpath($this->id) . $this->img);
                if (read_file($this->config->config['paths']['imgebay'] . '/' . idpath($this->id) . 'Original_' . $this->img))
                    unlink($this->config->config['paths']['imgebay'] . '/' . idpath($this->id) . 'Original_' . $this->img);
                if (read_file($this->config->config['paths']['imgebay'] . '/' . idpath($this->id) . 'thumb_' . $this->img))
                    unlink($this->config->config['paths']['imgebay'] . '/' . idpath($this->id) . 'thumb_' . $this->img);
                if (read_file($this->config->config['paths']['imgebay'] . '/' . idpath($this->id) . 'thumb_main_' . $this->img))
                    unlink($this->config->config['paths']['imgebay'] . '/' . idpath($this->id) . 'thumb_main_' . $this->img);
            }
        }
        if (!$nogo)
        {
            Redirect("Myebay/Edit/" . $this->id);
        }
    }
    function frontsell($id = '', $page = '')
    {
        if (isset($_POST['eid']))
            $id = (int) $_POST['eid'];
        $this->Auth_model->CheckListings();
        if ((int) $id > 0)
            $do = $this->Myebay_model->SwapSiteSellVal((int) $id);
        if (!isset($_POST['eid']))
        {
            $this->session->set_flashdata('action', (int) $id);
            Redirect('Myebay/ListItems/' . (int) $page . '#' . (int) $id);
        }
        else
            $this->_refreshlisting($id);
    }
    function _refreshlisting($eid)
    {
        if (isset($_POST['skuedit']))
            $this->mysmarty->assign('skuedit', (int) $_POST['skuedit']);
        $this->db->where('e_id', (int) $eid);
        $q = $this->db->get('ebay');
        if ($q->num_rows() > 0)
        {
            $this->load->model('Myautopilot_model');
            $this->mysmarty->assign('prules', $this->Myautopilot_model->GetPredefined());
            $this->mysmarty->assign('l', $q->row_array());
            $idarray[] = (int) $eid;
            $this->mysmarty->assign('hot', TRUE);
            //$this->mysmarty->assign('skuedit',TRUE);
            $this->Auth_model->CheckListings();
            $this->load->model('Myseller_model');
            $this->statuses = $this->Myseller_model->assignstatuses();
            $this->mysmarty->assign('adm', $this->Myebay_model->GetAdminList());
            $this->Myseller_model->getBase($idarray);
            $this->Myseller_model->getOnHold($idarray);
            echo json_encode(array(
                'a' => $this->mysmarty->fetch('myebay/myebay_show_loop1.html'),
                'b' => $this->mysmarty->fetch('myebay/myebay_show_loop2.html'),
                'c' => $this->mysmarty->fetch('myebay/myebay_show_loop3.html'),
                'd' => $this->mysmarty->fetch('myebay/myebay_show_loop4.html')
            ));
        }
    }
    function FrontOffice()
    {
        exit();
        $this->Auth_model->CheckListings();
        $this->load->helper('directory');
        $this->load->helper('file');
        $responseXml = read_file($this->config->config['ebaypath'] . '/cats.txt');
        $store       = simplexml_load_string($responseXml);
        $sc          = array();
        if (isset($store->Store->CustomCategories->CustomCategory))
        {
            foreach ($store->Store->CustomCategories->CustomCategory as $s)
            {
                $a                    = (array) $s;
                $sc[$a['CategoryID']] = trim($a['Name']);
            }
        }
        natcasesort($sc);
        $this->mysmarty->assign('store', $sc);
        $this->db->select('storeCatID, sitesell');
        $this->db->where('storeCatID !=', 0);
        $sq      = $this->db->get('ebay');
        $sell    = 0;
        $nosell  = 0;
        $sccount = array();
        if ($sq->num_rows() > 0)
        {
            foreach ($sq->result_array() as $k => $v)
            {
                if (isset($sc[$v['storeCatID']]))
                    $key = $sc[$v['storeCatID']];
                else
                    $key = $v['storeCatID'];
                if (isset($sccount[$key][$v['sitesell']]))
                    $sccount[$key][$v['sitesell']]++;
                else
                    $sccount[$key][$v['sitesell']] = 1;
                $sccount[$key]['id'] = $v['storeCatID'];
            }
        }
        ksort($sccount);
        foreach ($sccount as $s)
        {
            $sell   = $sell + $s[1];
            $nosell = $nosell + $s[0];
        }
        $this->mysmarty->assign('sell', $sell);
        $this->mysmarty->assign('nosell', $nosell);
        //printcool ($sccount);
        $this->mysmarty->assign('storecount', $sccount);
        $this->mysmarty->view('myebay/myebay_frontoffice.html');
    }
    function FrontStoreOn($id)
    {
        $this->Auth_model->CheckListings();
        $this->db->update('ebay', array(
            'sitesell' => 1
        ), array(
            'storeCatID' => (int) $id
        ));
        $this->session->set_flashdata('action', (int) $id);
        Redirect("Myebay/FrontOffice");
    }
    function FrontStoreOff($id)
    {
        $this->Auth_model->CheckListings();
        $this->db->update('ebay', array(
            'sitesell' => 0
        ), array(
            'storeCatID' => (int) $id
        ));
        $this->session->set_flashdata('action', (int) $id);
        Redirect("Myebay/FrontOffice");
    }
    function _GetCategorySpecifics($catID = '')
    {
        $this->Auth_model->CheckListings();
        if ($catID != '')
        {
            require_once($this->config->config['ebaypath'] . 'get-common/keys.php');
            require_once($this->config->config['ebaypath'] . 'get-common/eBaySession.php');
            $requestXmlBody = '<?xml version="1.0" encoding="utf-8" ?>';
            $requestXmlBody .= '<GetCategorySpecificsRequest xmlns="urn:ebay:apis:eBLBaseComponents">

';
            $requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
            $requestXmlBody .= '<WarningLevel>High</WarningLevel>';
            $requestXmlBody .= '<CategorySpecific><CategoryID>' . $catID . '</CategoryID></CategorySpecific>

						</GetCategorySpecificsRequest>';
            $verb        = 'GetCategorySpecifics';
            $session     = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
            $responseXml = $session->sendHttpRequest($requestXmlBody);
            if (stristr($responseXml, 'HTTP 404') || $responseXml == '')
                die('<P>Error sending request');
            $xml = simplexml_load_string($responseXml);
            printcool($xml);
        }
    }
    function Download($id = 0, $place = 1)
    {
        $this->Auth_model->CheckListings();
        if ((int) $id > 0 && (int) $place < 5 && (int) $place > 0)
        {
            $img = $this->Myebay_model->GetImage((int) $id, (int) $place);
            $this->load->helper('download');
            if (file_exists($this->config->config['paths']['imgebay'] . '/' . idpath($id) . 'Original_' . $img))
            {
                $data = file_get_contents($this->config->config['paths']['imgebay'] . '/' . idpath($id) . 'Original_' . $img);
                force_download('Original_' . $img, $data);
            }
            else
            {
                echo 'File Does Not Exist';
            }
        }
    }
    function Edit($itemid = '', $catID = 0, $merge = false, $called = false)
    {
        $this->session->unset_userdata('submitredir');
        if ($merge == 'false')
            $merge = false;
        $this->Auth_model->CheckListings();
        $this->mysmarty->assign('hot', TRUE);
        set_time_limit(180);
        ini_set('mysql.connect_timeout', 180);
        ini_set('max_execution_time', 180);
        ini_set('default_socket_timeout', 180);
        if (isset($_POST['catsearch']) && (trim($_POST['catsearch']) != ''))
            $this->mysmarty->assign('searchcat', trim($_POST['catsearch']));
        else
            $this->mysmarty->assign('searchcat', '');
        $this->id = (int) $itemid;
        if ($this->id > 0)
        {
            if (!$called)
            {
                $idarray[] = $this->id;
                if (isset($idarray))
                {
                    $this->load->model('Myseller_model');
                    $this->Myseller_model->getBase($idarray);
                }
            }
            $this->mysmarty->assign('shipcount', array(1,2,3,4));
            $this->load->helper('directory');
            $this->load->helper('file');
            $sresponseXml = read_file($this->config->config['ebaypath'] . '/shipping.txt');
            $shxml        = simplexml_load_string($sresponseXml);
            $this->mysmarty->assign('ShippingOptions', $shxml->ShippingServiceDetails);
            /*
            $responseXml = read_file($this->config->config['ebaypath'].'/cats.txt');
            $store = simplexml_load_string($responseXml);
            $this->cs = array();
            if (isset($store->Store->CustomCategories->CustomCategory)) $this->_storecatting($store->Store->CustomCategories->CustomCategory);
            $this->mysmarty->assign('store', $this->cs);
            */
            $this->db->where("notebay", 0);
            $this->db->orderby('listorder', 'ASC');
            $categories = $this->db->get("warehouse_sku_categories")->result_array();
            $this->mysmarty->assign('dbstore', $categories);
            $this->displays = $this->Myebay_model->GetItem($this->id);
            if ($this->displays['e_img1'] != '' /* && !$called*/ )
                $imgexists[1] = file_exists($this->config->config['paths']['imgebay'] . '/' . idpath($this->id) . 'Original_' . $this->displays['e_img1']);
            else
                $imgexists[2] = false;
            if ($this->displays['e_img2'] != '' /* && !$called*/ )
                $imgexists[2] = file_exists($this->config->config['paths']['imgebay'] . '/' . idpath($this->id) . 'Original_' . $this->displays['e_img2']);
            else
                $imgexists[2] = false;
            if ($this->displays['e_img3'] != '' /* && !$called*/ )
                $imgexists[3] = file_exists($this->config->config['paths']['imgebay'] . '/' . idpath($this->id) . 'Original_' . $this->displays['e_img3']);
            else
                $imgexists[3] = false;
            if ($this->displays['e_img4'] != '' /* && !$called*/ )
                $imgexists[4] = file_exists($this->config->config['paths']['imgebay'] . '/' . idpath($this->id) . 'Original_' . $this->displays['e_img4']);
            else
                $imgexists[4] = false;
            if ($this->displays['e_img5'] != '' /* && !$called*/ )
                $imgexists[5] = file_exists($this->config->config['paths']['imgebay'] . '/' . idpath($this->id) . 'Original_' . $this->displays['e_img5']);
            else
                $imgexists[5] = false;
            if ($this->displays['e_img6'] != '' /* && !$called*/ )
                $imgexists[6] = file_exists($this->config->config['paths']['imgebay'] . '/' . idpath($this->id) . 'Original_' . $this->displays['e_img6']);
            else
                $imgexists[6] = false;
            if ($this->displays['e_img7'] != '' /* && !$called*/ )
                $imgexists[7] = file_exists($this->config->config['paths']['imgebay'] . '/' . idpath($this->id) . 'Original_' . $this->displays['e_img7']);
            else
                $imgexists[7] = false;
            if ($this->displays['e_img8'] != '' /* && !$called*/ )
                $imgexists[8] = file_exists($this->config->config['paths']['imgebay'] . '/' . idpath($this->id) . 'Original_' . $this->displays['e_img8']);
            else
                $imgexists[8] = false;
            /*if ($called)
            
            {
            
            unset($this->displays['e_img1']);
            
            unset($this->displays['e_img2']);
            
            unset($this->displays['e_img3']);
            
            unset($this->displays['e_img4']);
            
            }*/
            $this->mysmarty->assign('imgexists', $imgexists);
            //$this->_GetCategorySpecifics($this->displays['primaryCategory']);
            $this->_GetSpecialAndTree();
            $this->load->library('form_validation');
            if (!$merge)
            {
                $this->form_validation->set_rules('e_title', 'Title', 'trim|required|min_length[3]|xss_clean');
                if (isset($_POST['Condition']) && ((int) $_POST['Condition'] == 1000 || (int) $_POST['Condition'] == 1500 || (int) $_POST['Condition'] == 1750 || (int) $_POST['Condition'] == 2000 || (int) $_POST['Condition'] == 2500))
                    $this->form_validation->set_rules('e_manuf', 'Brand', 'trim|required|xss_clean');
                else
                    $this->form_validation->set_rules('e_manuf', 'Brand', 'trim|xss_clean');
                $this->form_validation->set_rules('e_model', 'Model', 'trim|xss_clean');
                $this->form_validation->set_rules('e_part', 'Part Number', 'trim|xss_clean');
                $this->form_validation->set_rules('e_title', 'Title', 'trim|required|min_length[3]|xss_clean');
                if (isset($_POST['Condition']) && ((int) $_POST['Condition'] == 1000 || (int) $_POST['Condition'] == 1500 || (int) $_POST['Condition'] == 1750 || (int) $_POST['Condition'] == 2000 || (int) $_POST['Condition'] == 2500))
                    $this->form_validation->set_rules('e_compat', 'Compatibility', 'trim|required|xss_clean');
                else
                    $this->form_validation->set_rules('e_compat', 'Compatibility', 'trim|xss_clean');
                $this->form_validation->set_rules('e_package', 'Package', 'trim|xss_clean');
                $this->form_validation->set_rules('e_condition', 'Condition', 'trim|xss_clean');
                $this->form_validation->set_rules('e_shipping', 'Shipping', 'trim|xss_clean');
                $this->form_validation->set_rules('e_desc', 'Description', 'trim|xss_clean');
                $this->form_validation->set_rules('listingType', 'Listing Type', 'trim|xss_clean');
                $this->form_validation->set_rules('primaryCategory', 'Primary Category', 'trim|xss_clean');
                //$this->form_validation->set_rules('pCTitle', 'Description', 'trim|xss_clean');
                $this->form_validation->set_rules('listingDuration', 'Duration', 'trim|required|xss_clean');
                $this->form_validation->set_rules('startPrice', 'Start Price', 'trim|xss_clean');
                $this->form_validation->set_rules('buyItNowPrice', 'Price', 'trim|xss_clean');
                //$this->form_validation->set_rules('quantity', 'Quantity', 'trim|required|xss_clean');
                $this->form_validation->set_rules('PaymentMethods', 'Payment method', 'required');
                $this->form_validation->set_rules('Subtitle', 'Subtitle', 'trim|xss_clean');
                $this->form_validation->set_rules('Condition', 'Condition', 'trim|required|xss_clean');
                $this->form_validation->set_rules('e_title', 'Title', 'trim|required|min_length[3]|xss_clean');
                if (isset($_POST['Condition']) && ((int) $_POST['Condition'] == 1000 || (int) $_POST['Condition'] == 1500 || (int) $_POST['Condition'] == 1750 || (int) $_POST['Condition'] == 2000 || (int) $_POST['Condition'] == 2500))
                    $this->form_validation->set_rules('upc', 'UPC No.', 'trim|required|xss_clean');
                else
                    $this->form_validation->set_rules('upc', 'UPC No.', 'trim|xss_clean');
                $this->form_validation->set_rules('location', 'Location', 'trim|xss_clean');
                $this->form_validation->set_rules('storecat', 'Store Category', 'trim|required|xss_clean');
            }
            if ($this->form_validation->run() == FALSE || $merge)
            {
                if (!$merge)
                    $this->inputdata = array(
                        'e_title' => $this->input->post('e_title', TRUE),
                        'e_manuf' => $this->input->post('e_manuf', TRUE),
                        'e_model' => $this->input->post('e_model', TRUE),
                        //'e_part' => $this->_SerialSave($this->input->post('e_part', TRUE)),
                        'e_compat' => $this->input->post('e_compat', TRUE),
                        'e_package' => $this->input->post('e_package', TRUE),
                        'e_condition' => $this->input->post('e_condition', TRUE),
                        'e_shipping' => $this->input->post('e_shipping', TRUE),
                        'e_notice_header' => (int) $this->input->post('e_notice_header', TRUE),
                        'e_notice_shipping' => (int) $this->input->post('e_notice_shipping', TRUE),
                        'e_shipping' => $this->input->post('e_shipping', TRUE),
                        'e_desc' => $this->input->post('e_desc', TRUE),
                        'listingType' => $this->input->post('listingType', TRUE),
                        'primaryCategory' => (int) $this->input->post('primaryCategory', TRUE),
                        'listingDuration' => $this->input->post('listingDuration', TRUE),
                        'buyItNowPrice' => $this->input->post('buyItNowPrice', TRUE),
                        //'quantity' => (int)$this->input->post('quantity', TRUE),
                        'PaymentMethods' => $this->input->post('PaymentMethods', TRUE),
                        'Subtitle' => $this->input->post('Subtitle', TRUE),
                        'Condition' => $this->input->post('Condition', TRUE),
                        'upc' => $this->input->post('upc', TRUE),
                        'location' => $this->input->post('location', TRUE),
                        'storecat' => $this->input->post('storecat', TRUE),
                        'gtaxonomy' => addslashes($this->input->post('gtaxonomy')),
                        'weight_lbs' => $this->input->post('weight_lbs'),
                        'weight_oz' => $this->input->post('weight_oz')
                    );
                if (isset($_POST['shipping']))
                    $this->inputdata['shipping'] = $_POST['shipping'];
                else
                    $this->inputdata['shipping'] = array();
                if (!$_POST || $merge)
                {
                    $catID = $this->displays['primaryCategory'];
                    $this->mysmarty->assign('catname', $this->displays['pCTitle']);
                    $this->mysmarty->assign('storecat', $this->displays['storeCatID']);
                    $this->displays['storecat'] = $this->displays['storeCatID'];
                    $this->inputdata            = $this->displays;
                }
                if (isset($_POST['storecat']))
                {
                    if ($merge == 2)
                        $siv = $this->Myebay_model->GetItemItemValues((int) $_POST['storecat']);
                    else
                        $siv = $this->Myebay_model->GetStoreItemValues((int) $_POST['storecat']);
                    if ($siv)
                    {
                        $this->mysmarty->assign('takenfrom', $siv['e_id']);
                        $this->displays['PaymentMethod'] = $this->inputdata['PaymentMethod'] = $siv['PaymentMethod'];
                        $this->displays['shipping']      = $this->inputdata['shipping'] = $siv['shipping'];
                        if (isset($_POST['catsearch']) && (trim($_POST['catsearch']) != ''))
                        {
                            $this->displays['pCTitle']         = $this->inputdata['pCTitle'];
                            $this->displays['primaryCategory'] = $this->inputdata['primaryCategory'];
                        }
                        else
                        {
                            $this->displays['pCTitle']         = $this->inputdata['pCTitle'] = $siv['pCTitle'];
                            $this->displays['primaryCategory'] = $this->inputdata['primaryCategory'] = $siv['primaryCategory'];
                        }
                        $this->displays['storeCatID']    = $this->inputdata['storeCatID'] = $siv['storeCatID'];
                        $this->displays['storeCatTitle'] = $this->inputdata['storeCatTitle'] = $siv['storeCatTitle'];
                        $this->displays['storecat']      = $this->inputdata['storecat'] = $siv['storeCatID'];
                        $this->mysmarty->assign('catname', $this->inputdata['pCTitle']);
                        $this->mysmarty->assign('storecat', $this->inputdata['storeCatID']);
                    }
                    else
                    {
                        $this->displays['storeCatID'] = $this->input->post('storecat', true);
                    }
                }
                /*
                if (!isset($this->cs[$this->displays['storeCatID']]))
                
                {
                
                $this->mysmarty->assign('storecatnotfound', TRUE);
                
                $this->mysmarty->assign('storeCatTitle', $this->displays['storeCatTitle']);
                
                $this->mysmarty->assign('storeCatID', $this->displays['storeCatID']);
                
                }
                */
                require_once($this->config->config['pathtopublic'] . '/fckeditor/fckeditor.php');
                $this->editor             = new FCKeditor('e_desc');
                $this->editor->Width      = "350";
                $this->editor->Height     = "250";
                $this->editor->Value      = $this->displays['e_desc'];
                $this->displays['e_desc'] = $this->editor->CreateHtml();
                require_once($this->config->config['pathtopublic'] . '/fckeditor/fckeditor.php');
                $this->ieditor             = new FCKeditor('e_desc');
                $this->ieditor->Width      = "350";
                $this->ieditor->Height     = "250";
                $this->ieditor->Value      = $this->inputdata['e_desc'];
                $this->inputdata['e_desc'] = $this->ieditor->CreateHtml();
                $this->mysmarty->assign('ebupd', TRUE);
                if (strlen($this->displays['eBay_specs']) > 10)
                    $this->displays['eBay_specs'] = unserialize($this->displays['eBay_specs']);
                $this->mysmarty->assign('displays', $this->displays);
                //$this->mysmarty->assign('categories', $this->Myebay_model->GetEbayDataCategories((int)$catID));
                $distinctcats = $this->Myebay_model->GetDistinctUsedEbayCategories();
                //printcool ($this->session->userdata['gotcats']);
                //printcool ($distinctcats);
                if (isset($this->session->userdata['gotcats']))
                {
                    $distinctcats[0] = CleanCatDups(array_merge($this->session->userdata['gotcats'], $distinctcats[0]));
                }
                //printcool ($distinctcats);
                $this->mysmarty->assign('categories', $distinctcats);
                //$this->mysmarty->assign('categories', $this->Myebay_model->GetDistinctUsedEbayCategories());
                $this->mysmarty->assign('inputdata', $this->inputdata);
                $this->mysmarty->assign('errors', $this->form_validation->_error_array);
                $this->mysmarty->assign('taxonomy', $this->_gTaxonomy());
                $this->mysmarty->assign('called', $this->session->userdata['called']);
                $this->mysmarty->view('myebay/myebay_editnew.html');
                exit();
            }
            else
            {
                if (isset($_POST['closesubmit']))
                    $this->session->set_userdata('submitredir', 'Myebay');
                elseif (isset($_POST['closeload']))
                    $this->session->set_userdata('submitredir', 'Myebay/Search/' . (int) $this->id);
                if (isset($_POST['shipping']) && $_POST['shipping']['domestic'][1]['ShippingService'] == '' && $_POST['shipping']['domestic'][2]['ShippingService'] == '' && $_POST['shipping']['domestic'][3]['ShippingService'] == '' && $_POST['shipping']['domestic'][4]['ShippingService'] == '' && $_POST['shipping']['international'][1]['ShippingService'] == '' && $_POST['shipping']['international'][2]['ShippingService'] == '' && $_POST['shipping']['international'][3]['ShippingService'] == '' && $_POST['shipping']['international'][4]['ShippingService'] == '')
                {
                    echo "You must specify atleast one shipping method. <a href=\"javascript:history.back()\">Back</a>";
                    exit();
                }
                $this->db_data = array(
                    'e_title' => $this->form_validation->set_value('e_title'),
                    'e_sef' => $this->_CleanSef($this->form_validation->set_value('e_title')),
                    'e_manuf' => $this->form_validation->set_value('e_manuf'),
                    'e_model' => $this->form_validation->set_value('e_model'),
                    //'e_part' => $this->_SerialSave($this->form_validation->set_value('e_part')),
                    //'e_qpart' => $this->_RealCount($this->_SerialSave($this->form_validation->set_value('e_part'))),
                    'e_compat' => $this->form_validation->set_value('e_compat'),
                    'e_package' => $this->form_validation->set_value('e_package'),
                    'e_condition' => $this->form_validation->set_value('e_condition'),
                    'e_shipping' => $this->form_validation->set_value('e_shipping'),
                    'e_notice_header' => (int) $this->input->post('e_notice_header', TRUE),
                    'e_notice_shipping' => (int) $this->input->post('e_notice_shipping', TRUE),
                    'e_desc' => $this->form_validation->set_value('e_desc'),
                    'listingType' => $this->form_validation->set_value('listingType'),
                    'primaryCategory' => (int) $this->form_validation->set_value('primaryCategory'),
                    'pCTitle' => $this->Myebay_model->GetEbayCategoryTitle((int) $this->form_validation->set_value('primaryCategory')),
                    'listingDuration' => $this->form_validation->set_value('listingDuration'),
                    'startPrice' => floater($this->form_validation->set_value('buyItNowPrice')),
                    'buyItNowPrice' => floater($this->form_validation->set_value('buyItNowPrice')),
                    //'quantity' => (int)$this->form_validation->set_value('quantity'),
                    'PaymentMethod' => serialize($this->input->post('PaymentMethods', TRUE)),
                    'Subtitle' => $this->form_validation->set_value('Subtitle'),
                    'Condition' => $this->form_validation->set_value('Condition'),
                    'upc' => $this->form_validation->set_value('upc'),
                    'location' => $this->form_validation->set_value('location'),
                    'shipping' => serialize($_POST['shipping']),
                    'gtaxonomy' => addslashes($this->input->post('gtaxonomy')),
                    'weight_lbs' => $this->input->post('weight_lbs'),
                    'weight_oz' => $this->input->post('weight_oz'),
                    'weight_kg' => lbsoz2kg($this->input->post('weight_lbs'), $this->input->post('weight_oz'))
                );
                //$this->db_data['e_qpart'] = $this->_RealCount((string)$this->db_data['e_part']);
                if (isset($categories[$this->form_validation->set_value('storecat')]))
                {
                    $this->db_data['storeCatID']    = $this->form_validation->set_value('storecat');
                    $this->db_data['storeCatTitle'] = $categories[(int) $this->form_validation->set_value('storecat')]['wsc_title'];
                }
                if ($this->db_data['PaymentMethod'] == 'b:0;')
                    $this->db_data['PaymentMethod'] = '';
                /*if ($called) $this->checkexists =  $this->Myebay_model->CheckSefExists($this->db_data['e_sef']);
                
                else */
                $this->checkexists = $this->Myebay_model->CheckSefExists($this->db_data['e_sef'], $this->id);
                if ($this->checkexists)
                    $this->pref = rand(1, 9) . rand(1, 9) . rand(1, 9);
                else
                    $this->pref = '';
                $this->db_data['e_sef'] = $this->db_data['e_sef'] . $this->pref;
                $this->productimages    = array(1, 2, 3, 4, 5, 6, 7, 8);
                $this->load->library('upload');
                $watermark = FALSE;
                /*if ($called)
                
                {
                
                $this->id = $this->Myebay_model->Insert($this->db_data);
                
                $title = $this->db_data['e_title'];
                
                unset($this->db_data);
                
                $this->db_data['e_title'] = $title;
                
                }*/
                foreach ($this->productimages as $value)
                {
                    if ($_FILES['e_img' . $value]['name'] != '')
                    {
                        $this->_CheckImageDirExist(idpath((int) $this->id));
                        $newname[$value] = (int) $this->id . '_' . substr($this->_CleanSef($this->db_data['e_title']), 0, 210) . '_' . $value;
                        $image[$value]   = $this->_UploadImage('e_img' . $value, $this->config->config['paths']['imgebay'] . '/' . idpath((int) $this->id), TRUE, $this->config->config['sizes']['ebayimg']['width'], $this->config->config['sizes']['ebayimg']['height'], FALSE, TRUE, $newname[$value]);
                        if ($image[$value])
                        {
                            $oldimage[$value] = $this->Myebay_model->GetOldEbayImage($this->id, $value);
                            if ($oldimage[$value] != '' && $image[$value] != $oldimage[$value])
                            {
                                //if ($value == 1 && file_exists($this->config->config['paths']['imgebay'].'/'.idpath((int)$this->id).'Ebay_'.$oldimage[$value])) unlink($this->config->config['paths']['imgebay'].'/'.idpath((int)$this->id).'Ebay_'.$oldimage[$value]);
                                //if (file_exists($this->config->config['paths']['imgebay'].'/'.idpath((int)$this->id).$oldimage[$value])) unlink($this->config->config['paths']['imgebay'].'/'.idpath((int)$this->id).$oldimage[$value]);
                                //if (file_exists($this->config->config['paths']['imgebay'].'/'.idpath((int)$this->id).'thumb_'.$oldimage[$value])) unlink($this->config->config['paths']['imgebay'].'/'.idpath((int)$this->id).'thumb_'.$oldimage[$value]);
                                //if (file_exists($this->config->config['paths']['imgebay'].'/'.idpath((int)$this->id).'thumb_main_'.$oldimage[$value])) unlink($this->config->config['paths']['imgebay'].'/'.idpath((int)$this->id).'thumb_main_'.$oldimage[$value]);
                            }
                            $this->db_data['e_img' . $value] = $image[$value];
                            $this->db_data['idpath']         = str_replace('/', '', idpath((int) $this->id));
                            $watermark                       = TRUE;
                        }
                    }
                }
                $this->displays = $this->Myebay_model->GetItem($this->id);
                $this->Myebay_model->Update((int) $this->id, $this->db_data);
                if ($called)
                {
                    $this->db->where('listingid', (int) $this->id);
                    $f = $this->db->get('warehouse');
                    if ($f->num_rows() > 0)
                    {
                        $fr = $f->result_array();
                        foreach ($fr as $fl)
                        {
                            if ($fl['title'] != $this->db_data['e_title'])
                            {
                                $this->Auth_model->wlog($fl['bcn'], $fl['wid'], 'title', $fl['title'], $this->db_data['e_title']);
                                $this->db->update('warehouse', array(
                                    'title' => $this->db_data['e_title']
                                ), array(
                                    'wid' => $fl['wid']
                                ));
                            }
                        }
                    }
                }
                /*foreach ($this->db_data as $k => $v)
                
                {
                
                if (isset($this->displays[$k]) && $this->displays[$k]) $olddata = (string)$this->displays[$k];
                
                else $olddata = '';
                
                if ($k != 'PaymentMethod' && $k != 'shipping' && $k != 'startPrice')
                
                {
                
                if ($k == 'e_part') $latp = 'B';
                
                elseif ($k = 'e_qpart') $latp = 'B';
                
                elseif ($k = 'quantity') $latp = 'Q';
                
                else $latp = 'M';
                
                $this->_logaction('Edit', $latp ,array($k => $olddata), array($k => $v), (int)$this->id, $this->displays['ebay_id'], 0);
                
                
                
                }
                
                }
                
                */
                //$this->_GhostPopulate((int)$this->id);
                //// gDRV
                /*$search_term = commasep(commadesep($this->db_data['e_part']));
                
                $workdata = array('newvals' => array(
                
                array('name' => 'title',
                
                'value' =>  $this->db_data['e_title']
                
                )
                
                ),
                
                'origin' => (int)$this->id,
                
                'origin_type' => 'EditLocalListing',
                
                'admin' => $this->session->userdata['ownnames'],
                
                'gdrv' =>$this->Auth_model->gDrv()
                
                );
                
                */
                /*if (trim($search_term) != '')
                
                {
                
                $this->load->library('Googledrive');
                
                $this->load->library('Googlesheets');
                
                $res = $this->googlesheets->ProcessUpdate($this->googlesheets->PopulateWorksheetInfo($this->googledrive->FindInDrive(explode(',', $search_term))), $workdata);
                
                if ($res) $this->session->set_flashdata('success_msg', $res);
                
                }*/
                $this->session->unset_userdata('gotcats');
                if (!$called)
                {
                    $this->session->set_flashdata('success_msg', '"' . $this->db_data['e_title'] . '" Updated');
                    $this->session->set_flashdata('action', (int) $this->id);
                }
                if ($watermark)
                    Redirect('Myebay/DoWaterMark/' . (int) $this->id);
                else
                {
                    if ($this->session->userdata('called'))
                    {
                        echo "<html><head>

<script type=\"text/javascript\" src=\"/js/jquery.js\"></script>

<script type=\"text/javascript\" src=\"/js/warehouse.js\"></script>

<script type=\"text/javascript\" src=\"/js/jquery-min.js\"></script>

</head><body onload=\"addlistings(" . (int) $this->id . ", " . $this->session->userdata('called') . ", '', '',1);\"'></body></html>";
                        $this->session->unset_userdata('called');
                    } //else Redirect ('Myebay/Edit/'.(int)$this->id);
                    else
                    {
                        if (isset($_POST['closesubmit']))
                            Redirect('Myebay');
                        elseif (isset($_POST['closeload']))
                            Redirect('Myebay/Search/' . (int) $this->id);
                        else
                            Redirect('Myebay/Edit/' . (int) $this->id);
                    }
                }
            }
        }
        else
        {
            redirect("Myebay");
        }
    }
    function LoadProductsInCategory($catId) {
        $this->db->select('e_id, e_title, price_ch1')
            ->from('ebay')
            ->where('storeCatID', $catId);

        $query = $this->db->get();

        $title_array = array();
        foreach ($query->result_array() as $row) {

            $title_array[] = array('id' => $row['e_id'],
                //'value' => '<a href="'.$site_url.'/Mysku/Listing/'.$row['e_id'].'">'.$row['e_model'].'</a>',
                'value' => '<a id="mylin1k" onclick="showListing(' . $row['e_id'] . ')">' . substr($row['e_title'], 0, 25) . '</a>',
                'open' => false,
                'product' => substr($row['e_title'], 0, 25),
                'listing_title' => $row['e_title']
            );
        }

        //printcool($title_array);
        return $title_array;
    }

    function EditCategory($e_id) {
        if (isset($_POST['eid_cat'])) {
            echo '<p>We have POST eid=' . $_POST['eid_cat'];

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
                $storeCategories[$row['id_store']] = $row['store_cat_title'];
            }
            $storeCategories[0] = '';

            foreach ($queryeGoogle->result_array() as $row) {
                $googleCategories[$row['id_google']] = $row['google_cat_title'];
            }
            $googleCategories[0] = '';

            foreach ($queryeAmazon->result_array() as $row) {
                $amazonCategories[$row['id_amazon']] = $row['amazon_cat_title'];
            }
            $amazonCategories[0] = '';

            foreach ($queryeBay1->result_array() as $row) {
                $ebayCategories1[$row['primaryCategory']] = $row['pCTitle'];
            }
            $ebayCategories1[0] = '';

            foreach ($queryeBay2->result_array() as $row) {
                $ebayCategories2[$row['categoryEbaySecondaryId']] = $row['categoryEbaySecondaryTitle'];
            }
            $ebayCategories2[0] = '';

            //printcool($ebayCategories);


            $query = $this->db->query('select storeCatID, primaryCategory, categoryEbaySecondaryId,categoryEbaySecondaryTitle, categoryAmazonId, categoryGoogleId from ebay where e_id=' . $e_id);

            $row = $query->row(0);
            $this->mysmarty->assign('mySelectStore', ($row->storeCatID == '') ? 0 : $row->storeCatID);
            $this->mysmarty->assign('mySelectEbayFirst', ($row->primaryCategory == '') ? 0 : $row->primaryCategory);
            $this->mysmarty->assign('mySelectEbaySecond', ($row->categoryEbaySecondaryId == '') ? 0 : $row->categoryEbaySecondaryId);
            $this->mysmarty->assign('mySelectAmazon', ($row->categoryAmazonId == '') ? 0 : $row->categoryAmazonId);
            $this->mysmarty->assign('mySelectGoogle', ($row->categoryGoogleId == '') ? 0 : $row->categoryGoogleId);
            // echo '<p>'.$row->storeCatID;
            $this->mysmarty->assign('myCatsStore', $storeCategories);
            $this->mysmarty->assign('myCatsEbay1', $ebayCategories1);
            $this->mysmarty->assign('myCatsEbay2', $ebayCategories2);
            $this->mysmarty->assign('myCatsAmazon', $amazonCategories);
            $this->mysmarty->assign('myCatsGoogle', $googleCategories);
            $this->mysmarty->assign('searchcat', 'Computers');
            //$this->mysmarty->view('mycategories/mycategories_mapping.html');
        }
    }

    function _CleanSef($string)
    {
        $string     = str_replace(" ", "-", $string);
        $string     = str_replace("_", "-", $string);
        $cyrchars   = array(
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��',
            '��'
        );
        $latinchars = array(
            'A',
            'B',
            'V',
            'G',
            'D',
            'E',
            'J',
            'Z',
            'I',
            'I',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'R',
            'S',
            'T',
            'U',
            'F',
            'H',
            'CH',
            'TS',
            'SH',
            'SHT',
            'U',
            'U',
            'JU',
            'YA',
            'a',
            'b',
            'v',
            'g',
            'd',
            'e',
            'j',
            'z',
            'i',
            'i',
            'k',
            'l',
            'm',
            'n',
            'o',
            'p',
            'r',
            's',
            't',
            'u',
            'f',
            'h',
            'ch',
            'ts',
            'sh',
            'sht',
            'u',
            'u',
            'ju',
            'ya'
        );
        $string     = str_replace($cyrchars, $latinchars, $string);
        $string     = str_replace('---', '-', $string);
        $string     = str_replace('--', '-', $string);
        $string     = preg_replace('/[^A-Za-z0-9\-]/', '', $string);
        return $string;
    }
    function _CheckImageDirExist($path)
    {
        $this->load->helper('directory');
        $this->load->helper('file');
        $dir = directory_map($this->config->config['paths']['imgebay'] . '/' . $path);
        if (!$dir && !is_array($dir))
        {
            if (!mkdir($this->config->config['paths']['imgebay'] . '/' . $path))
                die('Failed to create folder...');
        }
        if (!read_file($this->config->config['paths']['imgebay'] . '/' . $path . 'index.html'))
        {
            if (!write_file($this->config->config['paths']['imgebay'] . '/' . $path . 'index.html', $this->_indexhtml($path)))
                echo 'Unable to write Directory Index for ' . $path;
        }
        if (!read_file($this->config->config['paths']['imgebay'] . '/' . $path . '.htaccess'))
        {
            if (!write_file($this->config->config['paths']['imgebay'] . '/' . $path . '.htaccess', $this->_htaccess($path)))
                echo 'Unable to write .htaccess for ' . $path;
        }
    }
    function _indexhtml($path = '')
    {
        $msg_data = array(
            'msg_title' => 'LATRONICS: GENERATED INDEX for Path: ' . $path,
            'msg_body' => '@ ' . CurrentTimeR(),
            'msg_date' => CurrentTime()
        );
        GoMail($msg_data);
        return '<html><head><title>403 Forbidden</title></head><body>403 forbidden.</body></html>	';
    }
    function _htaccess($path = '')
    {
        $msg_data = array(
            'msg_title' => 'LATRONICS: GENERATED .htaccess for Path: ' . $path,
            'msg_body' => '@ ' . CurrentTimeR(),
            'msg_date' => CurrentTime()
        );
        GoMail($msg_data);
        /*return 'RemoveHandler .php .phtml .php3
        
        RemoveType .php .phtml .php3
        
        php_flag engine off
        
        <IfModule mod_php5.c>
        
        php_value engine off
        
        </IfModule>
        
        <IfModule mod_php4.c>
        
        php_value engine off
        
        </IfModule>';*/
        return '<IfModule mod_php5.c>

  php_value engine off

</IfModule>

<IfModule mod_php4.c>

  php_value engine off

</IfModule>

';
    }
    function _UploadImage($fieldname = '', $configpath = '', $thumb = FALSE, $width = '', $height = '', $justupload = FALSE, $wm = FALSE, $filename = FALSE)
    {
        if (($fieldname != '') || ($configpath != '') || ((int) $width != 0) || ((int) $height != 0))
        {
            $uconfig['upload_path']   = $configpath;
            $uconfig['allowed_types'] = 'gif|jpg|png|bmp';
            $uconfig['remove_spaces'] = TRUE;
            $uconfig['max_size']      = '1900';
            $uconfig['max_filename']  = '240';
            if ($filename)
                $uconfig['file_name'] = $filename;
            //printcool ($filename);
            //printcool( $uconfig);
            $this->upload->initialize($uconfig);
            $this->uploadresult = $this->upload->do_upload($fieldname);
            $processimgdata     = $this->upload->data();
            //printcool($processimgdata['file_name']);
            if (!$this->uploadresult)
            {
                printcool($this->upload->display_errors());
                exit;
            }
            if (!$justupload)
            {
                if (($processimgdata['image_width'] > $width) || ($processimgdata['image_height'] > $height))
                {
                    $this->iconfig['image_library'] = 'gd2';
                    $this->iconfig['source_image']  = $configpath . '/' . $processimgdata['file_name'];
                    if (!$thumb)
                        $this->iconfig['create_thumb'] = FALSE;
                    else
                        $this->iconfig['create_thumb'] = TRUE;
                    $this->iconfig['maintain_ratio'] = TRUE;
                    $this->iconfig['width']          = $width;
                    $this->iconfig['height']         = $height;
                    $this->load->library('image_lib');
                    $this->image_lib->initialize($this->iconfig);
                    $this->imagesresult = $this->image_lib->resize();
                    if ($this->imagesresult != '1')
                    {
                        printcool($this->image_lib->display_errors());
                        exit;
                    }
                    $this->image_lib->clear();
                    $this->nconfig['image_library']  = 'gd2';
                    $this->nconfig['source_image']   = $configpath . '/' . $processimgdata['file_name'];
                    $this->nconfig['maintain_ratio'] = TRUE;
                    $this->nconfig['new_image']      = 'main_' . $processimgdata['file_name'];
                    $this->nconfig['width']          = '200';
                    $this->nconfig['height']         = '200';
                    $this->image_lib->initialize($this->nconfig);
                    $this->imagesresult = $this->image_lib->resize();
                    if ($this->imagesresult != '1')
                    {
                        printcool($this->image_lib->display_errors());
                        exit;
                    }
                    $this->image_lib->clear();
                }
            }
            //sleep(0.5);
            return ($processimgdata['file_name']);
        }
    }
    function UpdateCategories($catID = 0)
    {
        $this->Auth_model->CheckListings();
        $loop = $this->Myebay_model->GetEbayDataCategories((int) $catID);
        $main = $this->Myebay_model->GetEbayCategoryTitle((int) $catID);
        if ((int) $catID != 0)
            $html = $main . '&nbsp;&nbsp;&nbsp;<a style="font-size:10px;" id="aprimaryCategory" onClick="catupdt(0);"><img src="' . Site_url() . 'images/admin/delete.png" /> CLEAR</a><br><br>';
        else
            $html = 'Select Main Category:<br><br>';
        if (!$loop)
            $html .= '<span style="color:red; font-size:10px;">No more sub categories</span><br><br><select name="primaryCategory" >';
        else
            $html .= "<select id=\"primaryCategory\" name=\"primaryCategory\" onchange=\"var catid = document.getElementById('primaryCategory').value; catupdt(catid);\">";
        if ($loop[$catID])
            foreach ($loop[$catID] as $k => $v)
                $html .= '<option value="' . $v['catID'] . '">' . $v['catName'] . '</option>';
        else
            $html .= '<option value="' . $catID . '">' . $main . '</option>';
        $html .= '</select>';
        echo $html;
    }
    function UpdateQuantityFromActive($elid = 0)
    {
        exit('Turned off on purpose. MSG  me if we will still be using this.');
        $this->db->select('el_id, eid, ebavq, ebtq, lq, lebq, itemid');
        $this->db->where('el_id', (int) $elid);
        $query = $this->db->get('ebay_live');
        if ($query->num_rows() > 0)
        {
            $r = $query->row_array();
            $this->db->update('ebay', array(
                'qn_1' => $r['ebavq'],
                'ebayquantity' => $r['ebtq']
            ), array(
                'e_id' => (int) $r['eid']
            ));
            $this->db->update('ebay_live', array(
                'lq' => $r['ebavq'],
                'lebq' => $r['ebtq']
            ), array(
                'el_id' => (int) $elid
            ));
            $ra['admin']    = $this->session->userdata['ownnames'];
            $ra['time']     = CurrentTimeR();
            $ra['ctrl']     = 'UpdateQuantityFromActive';
            $ra['field']    = 'qn_1';
            $ra['atype']    = 'Q';
            $ra['e_id']     = (int) $r['eid'];
            $ra['ebay_id']  = (int) $r['itemid'];
            $ra['datafrom'] = $r['lq'];
            $ra['datato']   = (int) $r['ebavq'];
            if ($ra['datafrom'] != $ra['datato'])
                $this->db->insert('ebay_actionlog', $ra);
            $ra['field']    = 'ebayquantity';
            $ra['datafrom'] = $r['lebq'];
            $ra['datato']   = (int) $r['ebtq'];
            if ($ra['datafrom'] != $ra['datato'])
                $this->db->insert('ebay_actionlog', $ra);
        }
        Redirect('Mywarehouse/GetEbayLiveDBData/' . $elid . '#' . $elid);
    }
    function DoWaterMark($id, $place = 1, $attempt = 1, $backtoedit = false)
    {
        $img = $this->Myebay_model->GetOldEbayImage((int) $id, $place);
        if ($img)
        {
            if (!copy($this->config->config['paths']['imgebay'] . '/' . idpath((int) $id) . $img, $this->config->config['paths']['imgebay'] . '/' . idpath((int) $id) . 'Original_' . $img))
            {
                echo "failed to copy Original_file...\n";
                log_message('error', 'failed to copy Original_file (attempt ' . $attempt . ') ' . (int) $id . ', ' . $place . ', ' . $img . ' @' . CurrentTime());
                GoMail(array(
                    'msg_title' => 'failed to copy Original_file (attempt ' . $attempt . ')',
                    'msg_body' => (int) $id . ', ' . $place . ', ' . $img . ' @' . CurrentTime(),
                    'msg_date' => CurrentTime()
                ), $this->config->config['support_email'], $this->config->config['no_reply_email']);
                $attempt++;
                if ($attempt <= 7)
                    Redirect('Myebay/DoWaterMark/' . (int) $id . '/' . $place, $attempt, $backtoedit);
            }
            if (!copy($this->config->config['paths']['imgebay'] . '/' . idpath((int) $id) . $img, $this->config->config['paths']['imgebay'] . '/' . idpath((int) $id) . 'Ebay_' . $img))
            {
                echo "failed to copy Ebay_file...\n";
                log_message('error', 'failed to copy Ebay_file (attempt ' . $attempt . ') ' . (int) $id . ', ' . $place . ', ' . $img . ' @' . CurrentTime());
                GoMail(array(
                    'msg_title' => 'failed to copy Ebay_file (attempt ' . $attempt . ')',
                    'msg_body' => (int) $id . ', ' . $place . ', ' . $img . ' @' . CurrentTime(),
                    'msg_date' => CurrentTime()
                ), $this->config->config['support_email'], $this->config->config['no_reply_email']);
                $attempt++;
                if ($attempt <= 7)
                    Redirect('Myebay/DoWaterMark/' . (int) $id . '/' . $place, $attempt, $backtoedit);
            }
            $this->iconfig['image_library']  = 'gd2';
            $this->iconfig['source_image']   = $this->config->config['paths']['imgebay'] . '/' . idpath((int) $id) . 'Ebay_' . $img;
            $this->iconfig['create_thumb']   = FALSE;
            $this->iconfig['maintain_ratio'] = TRUE;
            $this->iconfig['width']          = '600';
            $this->load->library('image_lib');
            $this->image_lib->initialize($this->iconfig);
            $this->imagesresult = $this->image_lib->resize();
            if ($this->imagesresult != '1')
            {
                printcool($this->image_lib->display_errors());
                exit;
            }
            $this->image_lib->clear();
            $this->_WaterMark('bottom', 'right', 'wm_original_ebay.png', $this->config->config['paths']['imgebay'] . '/' . idpath((int) $id), 'Ebay_' . $img);
            $this->_WaterMark('bottom', 'right', 'wm_original_bottom.png', $this->config->config['paths']['imgebay'] . '/' . idpath((int) $id), $img);
            $this->_WaterMark('middle', 'center', 'wm_original_center.png', $this->config->config['paths']['imgebay'] . '/' . idpath((int) $id), $img);
            $this->_WaterMark('bottom', 'right', 'wm_bigtn_bottom.png', $this->config->config['paths']['imgebay'] . '/' . idpath((int) $id), 'thumb_main_' . $img);
            $this->_WaterMark('middle', 'center', 'wm_bigtn_center.png', $this->config->config['paths']['imgebay'] . '/' . idpath((int) $id), 'thumb_main_' . $img);
            $this->_WaterMark('bottom', 'right', 'wm_smalltn_bottom.png', $this->config->config['paths']['imgebay'] . '/' . idpath((int) $id), 'thumb_' . $img);
        }
        $place++;
        if ($place > 8)
        {
            if ($this->session->userdata('called'))
            {
                echo "<html><head>

<script type=\"text/javascript\" src=\"/js/jquery.js\"></script>

<script type=\"text/javascript\" src=\"/js/warehouse.js\"></script>

<script type=\"text/javascript\" src=\"/js/jquery-min.js\"></script>

</head><body onload=\"addlistings(" . (int) $id . ", " . $this->session->userdata('called') . ", '', '',1);\"'></body></html>";
                $this->session->unset_userdata('called');
            }
            else
            {
                if ($backtoedit)
                    redirect("/Myebay/Edit/" . (int) $id);
                else
                {
                    if ($this->session->userdata('submitredir'))
                        redirect($this->session->userdata('submitredir'));
                    else
                        redirect("/Myebay#" . (int) $id);
                }
            }
        }
        else
            Redirect('Myebay/DoWaterMark/' . (int) $id . '/' . $place . '/1/' . $backtoedit);
    }
    function CallAdd($fieldid = '', $skudetails = '')
    {
        $this->session->set_userdata('called', (int) $fieldid);
        $this->session->set_userdata('skudetails', $skudetails);
        $this->Add();
    }
    /*
    
    function RefreshLocalEbayValue($itemid = 0, $id = 0, $page = '')
    
    {
    
    $this->Auth_model->CheckListings();
    
    
    
    
    
    if ((int)$id > 0 && $itemid > 0)
    
    {
    
    require_once($this->config->config['ebaypath'].'get-common/keys.php');
    
    require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
    
    $requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
    
    $requestXmlBody .= '<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
    
    $requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
    
    $requestXmlBody .= '<DetailLevel>ItemReturnAttributes</DetailLevel>';
    
    $requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
    
    $requestXmlBody .= "<Version>$compatabilityLevel</Version>";
    
    $requestXmlBody .= '<ItemID>'.(int)$itemid.'</ItemID></GetItemRequest>';
    
    $verb = 'GetItem';
    
    $session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
    
    $responseXml = $session->sendHttpRequest($requestXmlBody);
    
    if(stristr($responseXml, 'HTTP 404') || $responseXml == '') die('<P>Error sending request');
    
    $xml = simplexml_load_string($responseXml);
    
    if ((string)$xml->Item->ItemID == '') { echo 'ERROR: Invalid Item ID...<a href="javascript:history.back()">Back</a>'; exit();}
    
    
    
    $this->db->select('e_id, ebayquantity');
    
    $this->db->where('e_id', (int)$id);
    
    $this->db->where('ebay_id', (string)$xml->Item->ItemID);
    
    $query = $this->db->get('ebay');
    
    if ($query->num_rows() > 0)
    
    {
    
    
    
    $ebr = $query->row_array();
    
    
    
    $qfromebay = (int)$xml->Item->Quantity;
    
    $qsfromebay = (int)$xml->Item->SellingStatus->QuantitySold;
    
    $ql = $qfromebay-$qsfromebay;
    
    $this->db->update('ebay', array('ebayquantity' => $ql), array('e_id' => $ebr['e_id']));
    
    
    
    
    
    
    
    array(
    
    //'ebayquantity' => ((int)$xml->Item->Quantity-(int)$xml->Item->SellingStatus->QuantitySold),
    
    //'quantity' => ((int)$xml->Item->Quantity-(int)$xml->Item->SellingStatus->QuantitySold)
    
    //),
    
    //array('e_id' => $ebr['e_id'])
    
    
    
    
    
    $this->_logaction('RefreshLocalEbayValue', 'Q',array('Local eBay Quantity' => $ebr['ebayquantity']), array('Local eBay Quantity' => $ql), $ebr['e_id'], (int)$itemid, 0);
    
    
    
    //$this->_logaction('RefreshLocalEbayValue', 'Q',array('Local Quantity' => $ebr['quantity']), array('Local Quantity' => $ql), $ebr['e_id'], (int)$itemid, 0);
    
    //$this->_GhostPopulate((int)$ebr['e_id']);
    
    
    
    $hmsg = array ('msg_title' => 'Local Listing Ebay Quantity Refreshed to '.((int)$xml->Item->Quantity-(int)$xml->Item->SellingStatus->QuantitySold), 'msg_body' => '', 'msg_date' => CurrentTime(),
    
    'e_id' => (int)$id,
    
    'itemid' => (int)$itemid,
    
    'trec' => 0,
    
    'admin' => $this->session->userdata['ownnames'],
    
    'sev' => '');
    
    
    
    $this->db->insert('admin_history', $hmsg);
    
    
    
    //GoMail($hmsg, $this->config->config['support_email'], $this->config->config['no_reply_email']);
    
    
    
    $this->session->set_flashdata('success_msg', 'Item '.(int)$id.' Local Ebay & Quantity Refreshed to '.((int)$xml->Item->Quantity-(int)$xml->Item->SellingStatus->QuantitySold));
    
    $this->session->set_flashdata('action', (int)$id);
    
    Redirect('Myebay/ListItems/'.(int)$page.'#'.(int)$id);
    
    }
    
    else echo 'ERROR. LOCAL ID DOES NOT MATCH eBAY ITEMID';
    
    
    
    }
    
    }
    
    */
    function Add($catID = 0, $itemID = 0, $called = false, $merge = false)
    {
        $this->Auth_model->CheckListings();
        //printcool ($_POST);
        set_time_limit(180);
        ini_set('mysql.connect_timeout', 180);
        ini_set('max_execution_time', 180);
        ini_set('default_socket_timeout', 180);
        if (isset($_POST['catsearch']) && (trim($_POST['catsearch']) != ''))
            $this->mysmarty->assign('searchcat', trim($_POST['catsearch']));
        else
            $this->mysmarty->assign('searchcat', '');
        $this->shiponly = false;
        /*if ($itemID == 0)
        
        {
        
        $itemID = $this->Myebay_model->GetFirstProduct();
        
        if ($itemID) $this->shiponly = true;
        
        
        
        }*/
        $this->mysmarty->assign('shipcount', array(
            1,
            2,
            3,
            4
        ));
        $this->load->helper('directory');
        $this->load->helper('file');
        $sresponseXml = read_file($this->config->config['ebaypath'] . '/shipping.txt');
        $shxml        = simplexml_load_string($sresponseXml);
        $this->mysmarty->assign('ShippingOptions', $shxml->ShippingServiceDetails);
        /*
        $responseXml = read_file($this->config->config['ebaypath'].'/cats.txt');
        
        $store = simplexml_load_string($responseXml);
        
        
        
        $this->cs = array();
        
        if (isset($store->Store->CustomCategories->CustomCategory))
        
        {
        
        
        
        $lvl = 1;
        
        foreach ($store->Store->CustomCategories->CustomCategory as $s0)
        
        {
        
        $this->_catstruct($s0,$lvl);
        
        }
        
        //printcool($this->cs);
        
        }
        
        $this->mysmarty->assign('store', $this->cs);
        */
        $this->db->where("notebay", 0);
        $this->db->orderby('listorder', 'ASC');
        $categories = $this->db->get("warehouse_sku_categories")->result_array();
        $this->mysmarty->assign('dbstore', $categories);
        $this->_GetSpecialAndTree();
        $this->load->library('form_validation');
        $this->form_validation->set_rules('e_title', 'Title', 'trim|required|min_length[3]|xss_clean');
        if (isset($_POST['Condition']) && ((int) $_POST['Condition'] == 1000 || (int) $_POST['Condition'] == 1500 || (int) $_POST['Condition'] == 1750 || (int) $_POST['Condition'] == 2000 || (int) $_POST['Condition'] == 2500))
            $this->form_validation->set_rules('e_manuf', 'Brand', 'trim|required|xss_clean');
        else
            $this->form_validation->set_rules('e_manuf', 'Brand', 'trim|xss_clean');
        $this->form_validation->set_rules('e_model', 'Model', 'trim|xss_clean');
        $this->form_validation->set_rules('e_part', 'Part Number', 'trim|xss_clean');
        if (isset($_POST['Condition']) && ((int) $_POST['Condition'] == 1000 || (int) $_POST['Condition'] == 1500 || (int) $_POST['Condition'] == 1750 || (int) $_POST['Condition'] == 2000 || (int) $_POST['Condition'] == 2500))
            $this->form_validation->set_rules('e_compat', 'Compatibility', 'trim|required|xss_clean');
        else
            $this->form_validation->set_rules('e_compat', 'Compatibility', 'trim|xss_clean');
        $this->form_validation->set_rules('e_package', 'Package', 'trim|xss_clean');
        $this->form_validation->set_rules('e_condition', 'Condition', 'trim|xss_clean');
        $this->form_validation->set_rules('e_shipping', 'Shipping', 'trim|xss_clean');
        $this->form_validation->set_rules('e_desc', 'Description', 'trim|xss_clean');
        $this->form_validation->set_rules('listingType', 'Listing Type', 'trim|xss_clean');
        $this->form_validation->set_rules('primaryCategory', 'Primary Category', 'trim|xss_clean');
        //$this->form_validation->set_rules('pCTitle', 'Description', 'trim|xss_clean');
        $this->form_validation->set_rules('listingDuration', 'Duration', 'trim|required|xss_clean');
        $this->form_validation->set_rules('startPrice', 'Start Price', 'trim|xss_clean');
        $this->form_validation->set_rules('buyItNowPrice', 'Price', 'trim|required|xss_clean');
        //$this->form_validation->set_rules('quantity', 'Quantity', 'trim|required|xss_clean');
        $this->form_validation->set_rules('PaymentMethods', 'Payment method', 'required');
        $this->form_validation->set_rules('Subtitle', 'Subtitle', 'trim|xss_clean');
        $this->form_validation->set_rules('Condition', 'Condition', 'trim|required|xss_clean');
        if (isset($_POST['Condition']) && ((int) $_POST['Condition'] == 1000 || (int) $_POST['Condition'] == 1500 || (int) $_POST['Condition'] == 1750 || (int) $_POST['Condition'] == 2000 || (int) $_POST['Condition'] == 2500))
            $this->form_validation->set_rules('upc', 'UPC No.', 'trim|required|xss_clean');
        else
            $this->form_validation->set_rules('upc', 'UPC No.', 'trim|xss_clean');
        $this->form_validation->set_rules('location', 'Location', 'trim|xss_clean');
        $this->form_validation->set_rules('storecat', 'Store Category', 'trim|required|xss_clean');
        if ($this->form_validation->run() == FALSE)
        {
            if ((int) $itemID > 0)
            {
                set_time_limit(90);
                ini_set('mysql.connect_timeout', 90);
                ini_set('max_execution_time', 90);
                ini_set('default_socket_timeout', 90);
                require_once($this->config->config['ebaypath'] . 'get-common/keys.php');
                require_once($this->config->config['ebaypath'] . 'get-common/eBaySession.php');
                $requestXmlBody = '<?xml version="1.0" encoding="utf-8" ?>';
                $requestXmlBody .= '<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">

';
                $requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
                $requestXmlBody .= '<DetailLevel>ItemReturnAttributes</DetailLevel>';
                $requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
                $requestXmlBody .= "<Version>$compatabilityLevel</Version>";
                $requestXmlBody .= '<ItemID>' . (int) $itemID . '</ItemID>

						</GetItemRequest>';
                $verb        = 'GetItem';
                $session     = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
                $responseXml = $session->sendHttpRequest($requestXmlBody);
                if (stristr($responseXml, 'HTTP 404') || $responseXml == '')
                    die('<P>Error sending request');
                $xml = simplexml_load_string($responseXml);
                if ((string) $xml->Ack == 'Failure')
                    echo '<br><br>' . (string) $xml->Errors->LongMessage . '<br><br>';
            }
            $this->displays['primaryCategory'] = $this->displays['pCTitle'] = $this->displays['storeCatID'] = $this->displays['storecat'] = $this->displays['storeCatID'] = false;
            if ((int) $itemID > 0 && isset($xml->Item->PrimaryCategory->CategoryID))
            {
                $this->displays               = $this->inputdata = array(
                    'e_title' => $this->input->post('e_title', TRUE),
                    'e_manuf' => $this->input->post('e_manuf', TRUE),
                    'e_model' => $this->input->post('e_model', TRUE),
                    //'e_part' => $this->_SerialSave($this->input->post('e_part', TRUE)),
                    'e_compat' => $this->input->post('e_compat', TRUE),
                    'e_package' => $this->input->post('e_package', TRUE),
                    'e_condition' => $this->input->post('e_condition', TRUE),
                    'e_shipping' => $this->input->post('e_shipping', TRUE),
                    'e_notice_header' => (int) $this->input->post('e_notice_header', TRUE),
                    'e_notice_shipping' => (int) $this->input->post('e_notice_shipping', TRUE),
                    'e_desc' => $this->input->post('e_desc', TRUE),
                    'upc' => $this->input->post('upc', TRUE),
                    'listingType' => $xml->Item->ListingType,
                    'primaryCategory' => (int) $xml->Item->PrimaryCategory->CategoryID,
                    'listingDuration' => $xml->Item->ListingDuration
                );
                $this->displays['pCTitle']    = $xml->Item->PrimaryCategory->CategoryName;
                $this->displays['storeCatID'] = $xml->Item->Storefront->StoreCategoryID;
                if (is_array($xml->Item->PaymentMethods))
                {
                    foreach ($xml->Item->PaymentMethods as $p)
                        $this->inputdata['PaymentMethods'][$p] = 'on';
                }
                else
                {
                    $p                                     = str_replace(' ', '', trim((string) $xml->Item->PaymentMethods));
                    $this->inputdata['PaymentMethods'][$p] = 'on';
                }
                if (!$this->shiponly)
                {
                    $this->mysmarty->assign('catname', $xml->Item->PrimaryCategory->CategoryName);
                    $this->mysmarty->assign('catID', $xml->Item->PrimaryCategory->CategoryID);
                    $this->mysmarty->assign('storecat', $xml->Item->Storefront->StoreCategoryID);
                    $this->mysmarty->assign('itemID', (int) $itemID);
                }
                else
                {
                    unset($this->inputdata['primaryCategory']);
                }
                //printcool ($xml->Item);
                $sd = array();
                if (isset($xml->Item->ShippingDetails->ShippingServiceOptions))
                {
                    foreach ($xml->Item->ShippingDetails->ShippingServiceOptions as $s)
                    {
                        $sd[(int) $s->ShippingServicePriority] = array(
                            'ShippingService' => (string) $s->ShippingService,
                            'ShippingServiceCost' => (float) $s->ShippingServiceCost,
                            'ShippingServiceAdditionalCost' => (float) $s->ShippingServiceAdditionalCost,
                            'FreeShipping' => (string) $s->FreeShipping
                        );
                    }
                }
                $is = array();
                if (isset($xml->Item->ShippingDetails->InternationalShippingServiceOption))
                {
                    foreach ($xml->Item->ShippingDetails->InternationalShippingServiceOption as $s)
                    {
                        $is[(int) $s->ShippingServicePriority] = array(
                            'ShippingService' => (string) $s->ShippingService,
                            'ShippingServiceCost' => (float) $s->ShippingServiceCost,
                            'ShippingServiceAdditionalCost' => (float) $s->ShippingServiceAdditionalCost,
                            'ShipToLocation' => (string) $s->ShipToLocation
                        );
                    }
                }
                $this->mysmarty->assign('ShippingServices', $sd);
                $this->mysmarty->assign('IntlShippingServices', $is);
                $this->mysmarty->assign('SellerExcludeShipToLocationsPreference', (string) $xml->Item->ShippingDetails->SellerExcludeShipToLocationsPreference);
                $this->mysmarty->assign('ExcludeShipToLocation', (array) $xml->Item->ShippingDetails->ExcludeShipToLocation);
                //printcool ($shxml->ShippingServiceDetails);
                //$this->mysmarty->assign('ShippingDetails', printcool ($xml->Item->ShippingDetails, TRUE));
                //$this->mysmarty->assign('ReturnPolicy',	printcool ($xml->Item->ReturnPolicy, TRUE));
            }
            else
            {
                $this->inputdata = array(
                    'e_title' => $this->input->post('e_title', TRUE),
                    'e_manuf' => $this->input->post('e_manuf', TRUE),
                    'e_model' => $this->input->post('e_model', TRUE),
                    //'e_part' => $this->_SerialSave($this->input->post('e_part', TRUE)),
                    'e_compat' => $this->input->post('e_compat', TRUE),
                    'e_package' => $this->input->post('e_package', TRUE),
                    'e_condition' => $this->input->post('e_condition', TRUE),
                    'e_shipping' => $this->input->post('e_shipping', TRUE),
                    'e_notice_header' => (int) $this->input->post('e_notice_header', TRUE),
                    'e_notice_shipping' => (int) $this->input->post('e_notice_shipping', TRUE),
                    'e_desc' => $this->input->post('e_desc', TRUE),
                    'listingType' => $this->input->post('listingType', TRUE),
                    'primaryCategory' => (int) $this->input->post('primaryCategory', TRUE),
                    'listingDuration' => $this->input->post('listingDuration', TRUE),
                    //'buyItNowPrice' => $this->input->post('buyItNowPrice', TRUE),
                    //'quantity' => (int)$this->input->post('quantity', TRUE),
                    'PaymentMethods' => $this->input->post('PaymentMethods', TRUE),
                    'Subtitle' => $this->input->post('Subtitle', TRUE),
                    'Condition' => $this->input->post('Condition', TRUE),
                    'upc' => $this->input->post('upc', TRUE),
                    'location' => $this->input->post('location', TRUE),
                    'storecat' => $this->input->post('storecat', TRUE),
                    'gtaxonomy' => addslashes($this->input->post('gtaxonomy')),
                    'weight_lbs' => $this->input->post('weight_lbs'),
                    'weight_oz' => $this->input->post('weight_oz')
                );
                //printcool ($this->inputdata);
                if (isset($_POST['shipping']))
                    $this->inputdata['shipping'] = $_POST['shipping'];
                else
                    $this->inputdata['shipping'] = array();
                //printcool ($this->inputdata['shipping']);
            }
            if (!$_POST || $merge)
            {
                $catID = $this->displays['primaryCategory'];
                $this->mysmarty->assign('catname', $this->displays['pCTitle']);
                $this->mysmarty->assign('storecat', $this->displays['storeCatID']);
                $this->displays['storecat'] = $this->displays['storeCatID'];
                $this->inputdata            = $this->displays;
            }
            if (isset($_POST['storecat']))
            {
                if ($merge == 2)
                    $siv = $this->Myebay_model->GetItemItemValues((int) $_POST['storecat']);
                else
                    $siv = $this->Myebay_model->GetStoreItemValues((int) $_POST['storecat']);
                if ($siv)
                {
                    $this->mysmarty->assign('takenfrom', $siv['e_id']);
                    $this->displays['PaymentMethod'] = $this->inputdata['PaymentMethod'] = $siv['PaymentMethod'];
                    $this->displays['shipping']      = $this->inputdata['shipping'] = $siv['shipping'];
                    if (isset($_POST['catsearch']) && (trim($_POST['catsearch']) != ''))
                    {
                        $this->displays['pCTitle']         = $this->inputdata['pCTitle'];
                        $this->displays['primaryCategory'] = $this->inputdata['primaryCategory'];
                    }
                    else
                    {
                        $this->displays['pCTitle']         = $this->inputdata['pCTitle'] = $siv['pCTitle'];
                        $this->displays['primaryCategory'] = $this->inputdata['primaryCategory'] = $siv['primaryCategory'];
                    }
                    $this->displays['storeCatID']    = $this->inputdata['storeCatID'] = $siv['storeCatID'];
                    $this->displays['storeCatTitle'] = $this->inputdata['storeCatTitle'] = $siv['storeCatTitle'];
                    $this->displays['storecat']      = $this->inputdata['storecat'] = $siv['storeCatID'];
                    $this->mysmarty->assign('catname', $this->inputdata['pCTitle']);
                    $this->mysmarty->assign('storecat', $this->inputdata['storeCatID']);
                }
                else
                {
                    $this->displays['storeCatID'] = $this->input->post('storecat', true);
                }
            }
            if (count($_POST) == 0)
                $this->inputdata['e_shipping'] = 'United States Postal Service.

We ship Internationally.

We use primarily USPS and FedEx';
            require_once($this->config->config['pathtopublic'] . '/fckeditor/fckeditor.php');
            $this->editor         = new FCKeditor('e_desc');
            $this->editor->Width  = "355";
            $this->editor->Height = "250";
            if (!isset($this->displays['e_desc']))
                $this->displays['e_desc'] = '';
            $this->editor->Value      = $this->displays['e_desc'];
            $this->displays['e_desc'] = $this->editor->CreateHtml();
            require_once($this->config->config['pathtopublic'] . '/fckeditor/fckeditor.php');
            $this->ieditor             = new FCKeditor('e_desc');
            $this->ieditor->Width      = "350";
            $this->ieditor->Height     = "250";
            $this->ieditor->Value      = $this->inputdata['e_desc'];
            $this->inputdata['e_desc'] = $this->ieditor->CreateHtml();
            $this->mysmarty->assign('inputdata', $this->inputdata);
            $this->mysmarty->assign('ebupd', TRUE);
            //$this->mysmarty->assign('categories', FALSE);
            //printcool ($this->Myebay_model->GetEbayDataCategories((int)$catID));
            //$this->mysmarty->assign('categories', $this->Myebay_model->GetEbayDataCategories((int)$catID));
            $distinctcats = $this->Myebay_model->GetDistinctUsedEbayCategories();
            //printcool ($this->session->userdata['gotcats']);
            //printcool ($distinctcats);
            if (!is_array($distinctcats[0]))
                $distinctcats[0] = array();
            if (is_array($distinctcats[0]) && count($distinctcats[0]) == 0)
                $distinctcats[0] = array();
            if (isset($this->session->userdata['gotcats']))
            {
                $distinctcats[0] = CleanCatDups(array_merge($this->session->userdata['gotcats'], $distinctcats[0]));
            }
            //printcool ($distinctcats);
            $this->mysmarty->assign('categories', $distinctcats);
            $errors = $this->form_validation->_error_array;
            //printcool ($_POST['PaymentMethods']);
            //if (!isset($_POST['PaymentMethod'])) $errors['PaymentMethod'] = 'Please select payment method';
            $this->mysmarty->assign('errors', $errors);
            $this->mysmarty->assign('taxonomy', $this->_gTaxonomy());
            if (isset($this->session->userdata['called']))
                $this->mysmarty->assign('called', $this->session->userdata['called']);
            $this->mysmarty->view('myebay/myebay_add.html');
            exit();
        }
        else
        {
            if (isset($_POST['shipping']) && $_POST['shipping']['domestic'][1]['ShippingService'] == '' && $_POST['shipping']['domestic'][2]['ShippingService'] == '' && $_POST['shipping']['domestic'][3]['ShippingService'] == '' && $_POST['shipping']['domestic'][4]['ShippingService'] == '' && $_POST['shipping']['international'][1]['ShippingService'] == '' && $_POST['shipping']['international'][2]['ShippingService'] == '' && $_POST['shipping']['international'][3]['ShippingService'] == '' && $_POST['shipping']['international'][4]['ShippingService'] == '')
            {
                echo "You must specify atleast one shipping method. <a href=\"javascript:history.back()\">Back</a>";
                exit();
            }
            $this->db_data = array(
                'e_title' => $this->form_validation->set_value('e_title'),
                'e_sef' => $this->_CleanSef($this->form_validation->set_value('e_title')),
                'e_manuf' => $this->form_validation->set_value('e_manuf'),
                'e_model' => $this->form_validation->set_value('e_model'),
                //'e_part' => $this->_SerialSave($this->form_validation->set_value('e_part')),
                //'e_qpart' => $this->_RealCount($this->_SerialSave($this->form_validation->set_value('e_part'))),
                'e_compat' => $this->form_validation->set_value('e_compat'),
                'e_package' => $this->form_validation->set_value('e_package'),
                'e_condition' => $this->form_validation->set_value('e_condition'),
                'e_shipping' => $this->form_validation->set_value('e_shipping'),
                'e_notice_header' => (int) $this->input->post('e_notice_header', TRUE),
                'e_notice_shipping' => (int) $this->input->post('e_notice_shipping', TRUE),
                'e_desc' => $this->form_validation->set_value('e_desc'),
                'admin_id' => (int) $this->session->userdata['admin_id'],
                'created' => CurrentTimeR(),
                'listingType' => $this->form_validation->set_value('listingType'),
                'primaryCategory' => (int) $this->form_validation->set_value('primaryCategory'),
                'pCTitle' => $this->Myebay_model->GetEbayCategoryTitle((int) $this->form_validation->set_value('primaryCategory')),
                'listingDuration' => $this->form_validation->set_value('listingDuration'),
                //'startPrice' => $this->form_validation->set_value('buyItNowPrice'),
                //'buyItNowPrice' => $this->form_validation->set_value('buyItNowPrice'),
                //'quantity' => (int)$this->form_validation->set_value('quantity'),
                'PaymentMethod' => serialize($this->input->post('PaymentMethods', TRUE)),
                'Subtitle' => $this->form_validation->set_value('Subtitle'),
                'Condition' => $this->form_validation->set_value('Condition'),
                'upc' => $this->form_validation->set_value('upc'),
                'location' => $this->form_validation->set_value('location'),
                'shipping' => serialize($_POST['shipping']),
                'storeCatID' => $this->form_validation->set_value('storecat'),
                'storeCatTitle' => $categories[(int) $this->form_validation->set_value('storecat')]['wsc_title'],
                'gtaxonomy' => addslashes($this->input->post('gtaxonomy')),
                'weight_lbs' => $this->input->post('weight_lbs'),
                'weight_oz' => $this->input->post('weight_oz'),
                'weight_kg' => lbsoz2kg($this->input->post('weight_lbs'), $this->input->post('weight_oz')),
                'price_ch1' => floater($this->form_validation->set_value('buyItNowPrice')),
                'price_ch2' => floater($this->form_validation->set_value('buyItNowPrice')),
                'price_ch3' => floater($this->form_validation->set_value('buyItNowPrice'))
            );
            //$this->db_data['e_qpart'] = $this->_RealCount((string)$this->db_data['e_part']);
            if ($this->db_data['PaymentMethod'] == 'b:0;')
                $this->db_data['PaymentMethod'] = '';
            $this->checkexists = $this->Myebay_model->CheckSefExists($this->db_data['e_sef']);
            if ($this->checkexists)
                $this->pref = rand(1, 9) . rand(1, 9) . rand(1, 9);
            else
                $this->pref = '';
            $this->db_data['e_sef'] = $this->db_data['e_sef'] . $this->pref;
            $this->load->library('upload');
            $this->newid         = $this->Myebay_model->Insert($this->db_data);
            //$this->_GhostPopulate((int)$this->newid);
            ///Update Images
            $this->productimages = array(1, 2, 3, 4, 5, 6, 7, 8);
            $watermark           = FALSE;
            foreach ($this->productimages as $value)
            {
                if ($_FILES['e_img' . $value]['name'] != '')
                {
                    $this->_CheckImageDirExist(idpath($this->newid));
                    $newname[$value] = (int) $this->newid . '_' . substr($this->_CleanSef($this->db_data['e_title']), 0, 210) . '_' . $value;
                    $image[$value]   = $this->_UploadImage('e_img' . $value, $this->config->config['paths']['imgebay'] . '/' . idpath($this->newid), TRUE, $this->config->config['sizes']['ebayimg']['width'], $this->config->config['sizes']['ebayimg']['height'], FALSE, TRUE, $newname[$value]);
                    if ($image[$value])
                    {
                        $this->newdb_data['e_img' . $value] = $image[$value];
                        $watermark                          = TRUE;
                    }
                    $this->newdb_data['idpath'] = str_replace('/', '', idpath($this->newid));
                }
            }
            if (isset($this->newdb_data))
                $this->Myebay_model->Update((int) $this->newid, $this->newdb_data);
            //// gDRV
            $search_term = commasep(commadesep($this->db_data['e_part']));
            $workdata    = array(
                'newvals' => array(
                    array(
                        'name' => 'title',
                        'value' => $this->db_data['e_title']
                    )
                ),
                'origin' => (int) $this->newid,
                'origin_type' => 'AddLocalListing',
                'admin' => $this->session->userdata['ownnames'],
                'gdrv' => $this->Auth_model->gDrv()
            );
            /*if (trim($search_term) != '')
            
            {
            
            $this->load->library('Googledrive');
            
            $this->load->library('Googlesheets');
            
            $res = $this->googlesheets->ProcessUpdate($this->googlesheets->PopulateWorksheetInfo($this->googledrive->FindInDrive(explode(',', $search_term))), $workdata);
            
            if ($res) $this->session->set_flashdata('success_msg', $res);
            
            }*/
            $this->session->unset_userdata('gotcats');
            $this->session->set_flashdata('success_msg', '"' . $this->db_data['e_title'] . '" Created');
            $this->session->set_flashdata('action', (int) $this->newid);
            if (isset($_POST['nobcn']))
                $backtoedit = '';
            else
                $backtoedit = 1;
            if ($watermark)
                Redirect('Myebay/DoWaterMark/' . (int) $this->newid . '/1/1/' . $backtoedit);
            else
            {
                if ($this->session->userdata('called'))
                {
                    echo "<html><head>

<script type=\"text/javascript\" src=\"/js/jquery.js\"></script>



<script type=\"text/javascript\" src=\"/js/warehouse.js\"></script>

<script type=\"text/javascript\" src=\"/js/jquery-min.js\"></script>

</head><body onload=\"addlistings(" . (int) $this->newid . ", " . $this->session->userdata('called') . ", '', '','1','" . $this->session->userdata('skudetails') . "');\"></body></html>";
                    $this->session->unset_userdata('called');
                    $this->session->unset_userdata('skudetails');
                }
                else
                {
                    if (isset($_POST['nobcn']))
                        Redirect('Myebay#' . (int) $this->newid);
                    else
                        Redirect('Myebay/Edit/' . (int) $this->newid);
                }
            }
        }
    }
    function CallAddSimilar($id = 0, $fieldid = '')
    {
        $this->session->set_userdata('called', (int) $fieldid);
        //$this->Edit((int)$id, 0, false, 1);
        $this->SellSimilar((int) $id);
    }
    function SellSimilar($id)
    {
        $this->db->where('e_id', (int) $id);
        $q = $this->db->get('ebay');
        if ($q->num_rows() > 0)
        {
            $e                          = $q->row_array();
            $similar['e_shipping']      = $e['e_shipping'];
            $similar['idpath']          = $e['idpath'];
            $similar['e_img1']          = $e['e_img1'];
            $similar['e_img2']          = $e['e_img2'];
            $similar['e_img3']          = $e['e_img3'];
            $similar['e_img4']          = $e['e_img4'];
            $similar['primaryCategory'] = $e['primaryCategory'];
            $similar['pCTitle']         = $e['pCTitle'];
            $similar['storeCatID']      = $e['storeCatID'];
            $similar['storeCatTitle']   = $e['storeCatTitle'];
            $similar['listingDuration'] = $e['listingDuration'];
            $similar['PaymentMethod']   = $e['PaymentMethod'];
            $similar['shipping']        = $e['shipping'];
            $similar['sitesell']        = 0;
            $similar['created']         = CurrentTime();
            $similar['admin_id']        = (int) $this->session->userdata['admin_id'];
            $this->db->insert('ebay', $similar);
            $id     = $this->db->insert_id();
            $idpath = idpath($id);
            $this->_CheckImageDirExist($idpath);
            $this->productimages = array(1, 2, 3, 4, 5, 6, 7, 8);
            foreach ($this->productimages as $value)
            {
                if ($similar['e_img' . $value] != '')
                {
                    $sourcefilename = $this->config->config['paths']['imgebay'] . '/' . $similar['idpath'] . '/Original_' . $similar['e_img' . $value];
                    copy($sourcefilename, $this->config->config['paths']['imgebay'] . '/' . idpath((int) $id) . 'Original_' . str_replace($e['e_id'], $id, $similar['e_img' . $value]));
                    $sourcefilename = $this->config->config['paths']['imgebay'] . '/' . $similar['idpath'] . '/thumb_' . $similar['e_img' . $value];
                    copy($sourcefilename, $this->config->config['paths']['imgebay'] . '/' . idpath((int) $id) . 'thumb_' . str_replace($e['e_id'], $id, $similar['e_img' . $value]));
                    $sourcefilename = $this->config->config['paths']['imgebay'] . '/' . $similar['idpath'] . '/thumb_main_' . $similar['e_img' . $value];
                    copy($sourcefilename, $this->config->config['paths']['imgebay'] . '/' . idpath((int) $id) . 'thumb_main_' . str_replace($e['e_id'], $id, $similar['e_img' . $value]));
                    $sourcefilename = $this->config->config['paths']['imgebay'] . '/' . $similar['idpath'] . '/Ebay_' . $similar['e_img' . $value];
                    copy($sourcefilename, $this->config->config['paths']['imgebay'] . '/' . idpath((int) $id) . 'Ebay_' . str_replace($e['e_id'], $id, $similar['e_img' . $value]));
                    $sourcefilename = $this->config->config['paths']['imgebay'] . '/' . $similar['idpath'] . '/' . $similar['e_img' . $value];
                    copy($sourcefilename, $this->config->config['paths']['imgebay'] . '/' . idpath((int) $id) . str_replace($e['e_id'], $id, $similar['e_img' . $value]));
                    $this->db->update('ebay', array(
                        'idpath' => str_replace('/', '', $idpath),
                        'e_img' . $value => str_replace($e['e_id'], $id, $similar['e_img' . $value])
                    ), array(
                        'e_id' => $id
                    ));
                }
            }
            Redirect('Myebay/Edit/' . $id);
        }
    }
    // KHIM change query to return different result set
    function Addfrom($merge = false)
    {
        $this->Add(0, 0, false, $merge);
    }
    function ReSubmitEbay($id = 0)
    {
        $this->Auth_model->CheckListings();
        $this->resubmit = TRUE;
        $this->SubmitEbay($id);
    }
    function SubmitEbay($id = 0)
    {
        $this->Auth_model->CheckListings();
        if (isset($_POST['eid']))
            $id = (int) $_POST['eid'];
        if ((int) $id > 0)
        {
            log_message('error', 'SUBMIT START ' . (int) $id . ' @ ' . CurrentTime());
            $this->session->set_flashdata('action', (int) $id);
            set_time_limit(90);
            $this->ReWaterMark((int) $id);
            $item   = $this->Myebay_model->GetItem((int) $id);
            $zip    = $this->Myebay_model->GetSetting('EbayLocationZIP');
            $ppmail = $this->Myebay_model->GetSetting('EbayPayPalMAIL');
            if (!$item)
            {
                echo 'Item not found!';
                exit();
            }
            require_once($this->config->config['ebaypath'] . 'get-common/keys.php');
            require_once($this->config->config['ebaypath'] . 'get-common/eBaySession.php');
            ini_set('magic_quotes_gpc', false);
            $this->mysmarty->assign('displays', $item);
            $listDescHtml    = htmlspecialchars($this->mysmarty->fetch('myebay/myebay_sendsource.html'));
            $listingType     = $item['listingType'];
            $primaryCategory = str_replace('&', '&amp;', $item['primaryCategory']);
            $itemTitle       = str_replace('&', '&amp;', $item['e_title']);
            if (get_magic_quotes_gpc())
                $itemDescription = stripslashes(str_replace('&', '&amp;', $item['e_desc']));
            else
                $itemDescription = str_replace('&', '&amp;', $item['e_desc']);
            $listingDuration = $item['listingDuration'];
            $startPrice      = $item['price_ch1']; //$item['buyItNowPrice'];//$item['startPrice'];
            $buyItNowPrice   = $item['price_ch1'];
            //if ($item['ebayquantity'] > 0) $quantity  = $item['ebayquantity'];
            //else $quantity = $item['quantity'];
            $quantity        = $item['qn_ch1'];
            $PaymentMethods  = $item['PaymentMethod'];
            $upc             = str_replace('&', '&amp;', $item['upc']);
            $partno          = str_replace('&', '&amp;', $item['e_compat']);
            $shipping        = $item['shipping'];
            $storecat        = $item['storeCatID'];
            if ($listingType == 'StoresFixedPrice')
            {
                $buyItNowPrice   = 0.0; // don't have BuyItNow for SIF
                $listingDuration = 'GTC';
            }
            if ($listingType == 'Dutch')
                $buyItNowPrice = $buyItNowPrice; // don't have BuyItNow for Dutch
            $conditiondescription = '';
            if ($item['Condition'] != 1000)
                $conditiondescription = "<ConditionDescription>" . str_replace('&', '&amp;', $item['e_condition']) . "</ConditionDescription>";
            if (isset($PaymentMethods))
            {
                $paymentsnippet = '';
                if ($PaymentMethods != '')
                    foreach ($PaymentMethods as $k => $v)
                    {
                        $paymentsnippet .= '<PaymentMethods>' . $k . '</PaymentMethods>';
                    }
                if (isset($PaymentMethods['PayPal']))
                    $paymentsnippet .= '<PayPalEmailAddress>' . $ppmail . '</PayPalEmailAddress>';
            }
            $verb       = 'AddItem';
            $upcsnippet = '';
            $true       = TRUE;
            /*
            
            if ($upc != '' || ($item['e_compat'] != '' && $item['e_manuf'] != '')) $upcsnippet .= '<ProductListingDetails><IncludePrefilledItemInformation>'.$true.'</IncludePrefilledItemInformation>';
            
            if ($upc != '')	$upcsnippet .= '<ProductListingDetails><IncludePrefilledItemInformation>'.$true.'</IncludePrefilledItemInformation><UPC>'.$upc.'</UPC></ProductListingDetails>';
            
            if ($item['e_compat'] =! '' && $item['e_manuf'] != '') $upcsnippet .= '<BrandMPN><Brand>'.$item['e_manuf'].'</Brand><MPN>'.$item['e_compat'].'</MPN></BrandMPN>';
            
            if ($upc != '' || ($item['e_compat'] != '' && $item['e_manuf'] != '')) $upcsnippet .= '</ProductListingDetails>';*/
            /*if ($upc != '' && $item['e_compat'] != '' && $item['e_manuf'] != '') $upcsnippet .= '
            
            <ProductListingDetails>
            
            <IncludePrefilledItemInformation>'.$true.'</IncludePrefilledItemInformation>
            
            <UPC>'.$upc.'</UPC>
            
            <BrandMPN>
            
            <Brand>'.$item['e_manuf'].'</Brand>
            
            <MPN>'.$item['e_compat'].'</MPN>
            
            </BrandMPN>
            
            </ProductListingDetails>';*/
            if ($upc != '' && $item['e_compat'] != '' && $item['e_manuf'] != '')
                $upcsnippet .= '

				<ProductListingDetails>

				<IncludePrefilledItemInformation>' . $true . '</IncludePrefilledItemInformation><UPC>' . $upc . '</UPC></ProductListingDetails>

				 <ItemSpecifics>

     <NameValueList>

         <Name>Brand</Name>

         <Value>' . str_replace('&', '&amp;', $item['e_manuf']) . '</Value>

     </NameValueList>

     <NameValueList>

         <Name>MPN</Name>

         <Value>' . str_replace('&', '&amp;', $item['e_compat']) . '</Value>

     </NameValueList>

 </ItemSpecifics>

				';
            $imgsnippet = '<PictureDetails>';
            if ($item['e_img1'] != '')
                $imgsnippet .= '<PictureURL>' . Site_url() . 'ebay_images/' . $item['idpath'] . '/Ebay_' . $item['e_img1'] . '</PictureURL>';
            if ($item['e_img2'] != '')
                $imgsnippet .= '<PictureURL>' . Site_url() . 'ebay_images/' . $item['idpath'] . '/Ebay_' . $item['e_img2'] . '</PictureURL>';
            if ($item['e_img3'] != '')
                $imgsnippet .= '<PictureURL>' . Site_url() . 'ebay_images/' . $item['idpath'] . '/Ebay_' . $item['e_img3'] . '</PictureURL>';
            if ($item['e_img4'] != '')
                $imgsnippet .= '<PictureURL>' . Site_url() . 'ebay_images/' . $item['idpath'] . '/Ebay_' . $item['e_img4'] . '</PictureURL>';
            if ($item['e_img5'] != '')
                $imgsnippet .= '<PictureURL>' . Site_url() . 'ebay_images/' . $item['idpath'] . '/Ebay_' . $item['e_img5'] . '</PictureURL>';
            if ($item['e_img6'] != '')
                $imgsnippet .= '<PictureURL>' . Site_url() . 'ebay_images/' . $item['idpath'] . '/Ebay_' . $item['e_img6'] . '</PictureURL>';
            if ($item['e_img7'] != '')
                $imgsnippet .= '<PictureURL>' . Site_url() . 'ebay_images/' . $item['idpath'] . '/Ebay_' . $item['e_img7'] . '</PictureURL>';
            if ($item['e_img8'] != '')
                $imgsnippet .= '<PictureURL>' . Site_url() . 'ebay_images/' . $item['idpath'] . '/Ebay_' . $item['e_img8'] . '</PictureURL>';
            $imgsnippet .= '</PictureDetails>';
            $requestXmlBodySTART = '<?xml version="1.0" encoding="utf-8" ?><AddItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
            $requestXmlBody      = "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
            $requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';
            $requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
            $requestXmlBody .= "<Version>$compatabilityLevel</Version>";
            // <BuyItNowPrice>".$buyItNowPrice."</BuyItNowPrice>
            $requestXmlBody .= "<Item>

			<Title>" . $itemTitle . "</Title>

			<Description>" . $listDescHtml . "</Description>

			<PrimaryCategory>

			  <CategoryID>" . $primaryCategory . "</CategoryID>

			</PrimaryCategory>

			<ConditionID>" . $item['Condition'] . "</ConditionID>

			" . $conditiondescription . "		    

			<StartPrice>" . $startPrice . "</StartPrice>

			" . $upcsnippet . "			

			<Country>US</Country>

			<Currency>USD</Currency>

			<DispatchTimeMax>1</DispatchTimeMax>

			<ListingDuration>" . $item['listingDuration'] . "</ListingDuration>

			<ListingType>StoresFixedPrice</ListingType>

		    " . $paymentsnippet . "

			" . $imgsnippet . "

			<PostalCode>" . $zip . "</PostalCode>

			<Quantity>" . $quantity . "</Quantity>

			<Storefront><StoreCategoryID>" . $storecat . "</StoreCategoryID></Storefront>

			 <ListingDesigner>

			  <LayoutID>10000</LayoutID>

			  <ThemeID>10</ThemeID>

			</ListingDesigner>

	 <ReturnPolicy>

      <ReturnsAcceptedOption>ReturnsAccepted</ReturnsAcceptedOption>

      <RefundOption>MoneyBack</RefundOption>

      <ReturnsWithinOption>Days_30</ReturnsWithinOption>

      <Description>If you are not satisfied, return for refund.</Description>

      <ShippingCostPaidByOption>Buyer</ShippingCostPaidByOption>

    </ReturnPolicy>";
            if (is_array($shipping))
            {
                $requestXmlBody .= "<ShippingDetails>";
                if (isset($shipping['domestic']))
                    foreach ($shipping['domestic'] as $k => $s)
                    {
                        if ($s['ShippingService'] != '')
                        {
                            if (isset($s['FreeShipping']) && $s['FreeShipping'] == 'on')
                                $fssnip = "<FreeShipping>" . true . "</FreeShipping>";
                            else
                                $fssnip = "";
                            $requestXmlBody .= "<ShippingServiceOptions>

       				 <ShippingServicePriority>" . $k . "</ShippingServicePriority>

        			 <ShippingService>" . $s['ShippingService'] . "</ShippingService>

      				 <ShippingServiceCost>" . (float) $s['ShippingServiceCost'] . "</ShippingServiceCost>

					 <ShippingServiceAdditionalCost currencyID=\"USD\">" . (float) $s['ShippingServiceAdditionalCost'] . "</ShippingServiceAdditionalCost>

					" . $fssnip . "					

      				</ShippingServiceOptions>";
                        }
                    }
                if (isset($shipping['international']))
                    foreach ($shipping['international'] as $k => $s)
                    {
                        if ($s['ShippingService'] != '')
                        {
                            $requestXmlBody .= "<InternationalShippingServiceOption>

		 <ShippingService>" . $s['ShippingService'] . "</ShippingService>

        <ShippingServiceAdditionalCost currencyID=\"USD\">" . (float) $s['ShippingServiceAdditionalCost'] . "</ShippingServiceAdditionalCost>

        <ShippingServiceCost currencyID=\"USD\">" . (float) $s['ShippingServiceCost'] . "</ShippingServiceCost>

        <ShippingServicePriority>" . $k . "</ShippingServicePriority>

        <ShipToLocation>" . $s['ShipToLocation'] . "</ShipToLocation>

  	    </InternationalShippingServiceOption>";
                        }
                    }
                $requestXmlBody .= "</ShippingDetails>";
            }
            $requestXmlBody .= "<Site>US</Site>

		  </Item>";
            $requestXmlBodyEND = '</AddItemRequest>';
            log_message('error', 'SUBMITTED ' . (int) $id . ' @' . CurrentTime());
            $vrequestXmlBodySTART = '<?xml version="1.0" encoding="utf-8" ?><VerifyAddItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
            $vrequestXmlBodyEND   = '</VerifyAddItemRequest>';
            //printcool (simplexml_load_string($vrequestXmlBodySTART.$requestXmlBody.$vrequestXmlBodyEND));
            $vsession             = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], 'VerifyAddItem');
            //send the request and get response
            $vresponseXml         = $vsession->sendHttpRequest($vrequestXmlBodySTART . $requestXmlBody . $vrequestXmlBodyEND);
            if (stristr($vresponseXml, 'HTTP 404') || $vresponseXml == '')
                die('<P>Error sending request');
            //Xml string is parsed and creates a DOM Document object
            //$vresponseDoc = new DomDocument();
            //$vresponseDoc->loadXML($vresponseXml);
            $xmlvresp = simplexml_load_string($vresponseXml);
            log_message('error', 'SUBMIT STEP 1 ' . (int) $id . ' @' . CurrentTime());
            if (isset($xmlvresp->Errors))
            {
                log_message('error', 'SUBMIT STEP 2 ' . (int) $id . ' @' . CurrentTime());
                $estr = '';
                foreach ($xmlvresp->Errors as $e)
                {
                    $estr .= '<div style="color:red;">ERROR:<BR>' . $e->ShortMessage . ' | ' . $e->LongMessage . ' | ' . $e->ErrorCode . ' | ' . $e->SeverityCode . ' | ' . $e->ErrorClassification . '</div><br />';
                    log_message('error', 'SUBMIT STEP 3 ' . (int) $id . ' @' . CurrentTime());
                }
                $this->_recordsubmiterror(array(
                    'msg_title' => 'SUBMITTED ERRORS ' . (int) $id . ' @' . CurrentTime(),
                    'msg_body' => $estr,
                    'msg_date' => CurrentTime()
                ));
                foreach ($xmlvresp->Errors as $e)
                {
                    log_message('error', 'SUBMIT STEP 4 ' . (int) $id . ' @' . CurrentTime());
                    if ((string) $e->SeverityCode !== 'Warning')
                    {
                        log_message('error', 'SUBMIT STEP 5 ' . (int) $id . ' @' . CurrentTime());
                        $this->_recordsubmiterror(array(
                            'msg_title' => 'SUBMITTED ERRORS ECHO\'d ' . (int) $id . ' @' . CurrentTime(),
                            'msg_body' => 'SUBMITTED ERRORS ECHO\'d ' . (int) $id,
                            'msg_date' => CurrentTime()
                        ));
                        echo $estr;
                        echo '<a href="javascript:history.back()">Back</a>';
                        log_message('error', 'SUBMIT STEP 6 ' . (int) $id . ' @' . CurrentTime());
                        exit();
                        log_message('error', 'SUBMIT STEP 7 ' . (int) $id . ' @' . CurrentTime());
                    }
                }
            }
            log_message('error', 'SUBMIT STEP 8 ' . (int) $id . ' @' . CurrentTime());
            //	printcool ($xmlvresp);
            //exit('TESTING ERROR FIXES. FEW MINS. PLEASE WAIT');
            //printcool($xmlvresp);
            //printcool($vrequestXmlBodySTART.$requestXmlBody.$vrequestXmlBodyEND);
            //exit();
            ///if found error, echo with back
            /////////////////////
            //Create a new eBay session with all details pulled in from included keys.php
            $session     = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
            //send the request and get response
            $responseXml = $session->sendHttpRequest($requestXmlBodySTART . $requestXmlBody . $requestXmlBodyEND);
            if (stristr($responseXml, 'HTTP 404') || $responseXml == '')
                die('<P>Error sending request');
            //Xml string is parsed and creates a DOM Document object
            $responseDoc = new DomDocument();
            $responseDoc->loadXML($responseXml);
            $sxml      = simplexml_load_string($responseXml);
            $aresponse = $this->_XML2Array($sxml);
            //get any error nodes
            $errors    = $responseDoc->getElementsByTagName('Errors');
            log_message('error', 'SUBMIT STEP 9 ' . (int) $id . ' @' . CurrentTime());
            //if there are error nodes
            if ($errors->length > 0)
            {
                log_message('error', 'SUBMIT STEP 10 ' . (int) $id . ' @' . CurrentTime());
                //echo '<P><B>eBay returned the following error(s):</B>';
                //display each error
                //Get error code, ShortMesaage and LongMessage
                $code     = $errors->item(0)->getElementsByTagName('ErrorCode');
                $shortMsg = $errors->item(0)->getElementsByTagName('ShortMessage');
                $longMsg  = $errors->item(0)->getElementsByTagName('LongMessage');
                $severity = $errors->item(0)->getElementsByTagName('SeverityCode');
                //Display code and shortmessage
                log_message('error', 'SUBMIT STEP 11 ' . (int) $id . ' @' . CurrentTime());
                //echo '<P>', $code->item(0)->nodeValue, ' : ', str_replace(">", "&gt;", str_replace("<", "&lt;", $shortMsg->item(0)->nodeValue));
                //if there is a long message (ie ErrorLevel=1), display it
                $severrors = 0;
                if (count($longMsg) > 0)
                {
                    if ((string) $severity->item(0)->nodeValue !== 'Warning')
                    {
                        $severrors++;
                        log_message('error', 'SUBMIT STEP 12 ' . (int) $id . ' @' . CurrentTime());
                        echo '<BR>', str_replace(">", "&gt;", str_replace("<", "&lt;", $longMsg->item(0)->nodeValue));
                        log_message('error', 'SUBMIT STEP 13 ' . (int) $id . ' @' . CurrentTime());
                        $this->_recordsubmiterror(array(
                            'msg_title' => 'EBAY API ECHOed ERRORS ' . (int) $id . ' @' . CurrentTime(),
                            'msg_body' => 'EBAY API ECHOed ERRORS ' . (int) $id . ' - ' . str_replace(">", "&gt;", str_replace("<", "&lt;", $longMsg->item(0)->nodeValue)),
                            'msg_date' => CurrentTime()
                        ));
                        exit();
                    }
                    else
                    {
                        log_message('error', 'SUBMIT STEP 14 ' . (int) $id . ' @' . CurrentTime());
                        $this->_recordsubmiterror(array(
                            'msg_title' => 'EBAY API SUBMITTED ERRORS ' . (int) $id . ' @' . CurrentTime(),
                            'msg_body' => $longMsg->item(0)->nodeValue,
                            'msg_date' => CurrentTime()
                        ));
                    }
                }
                log_message('error', 'SUBMIT STEP 15 ' . (int) $id . ' @' . CurrentTime());
            }
            //else { //no errors
            log_message('error', 'SUBMIT STEP 16 ' . (int) $id . ' @' . CurrentTime());
            //get results nodes
            $responses = $responseDoc->getElementsByTagName("AddItemResponse");
            foreach ($responses as $response)
            {
                $acks = $response->getElementsByTagName("Ack");
                /*				*/
                $ack  = $acks->item(0)->nodeValue;
                $this->session->set_flashdata('success_msg', 'Result: ' . $ack);
                /*$endTimes  = $response->getElementsByTagName("EndTime");
                
                $endTime   = $endTimes->item(0)->nodeValue;
                
                echo "endTime = $endTime <BR />\n";
                
                */
                $itemIDs = $response->getElementsByTagName("ItemID");
                $itemID  = 0;
                /*				*/
                if ($itemIDs->length > 0)
                    $itemID = $itemIDs->item(0)->nodeValue;
                //if ($id == 11382)
                //{
                if (isset($this->resubmit) && (isset($longMsg) && count($longMsg) > 0))
                {
                    if (!isset($this->remote))
                        echo '<BR>' . str_replace(">", "&gt;", str_replace("<", "&lt;", $longMsg->item(0)->nodeValue));
                    log_message('error', 'RESUBMIT STEP 14.1 ' . (int) $id . ' @' . CurrentTime());
                    $this->_recordsubmiterror(array(
                        'msg_title' => 'EBAY API RESUBMITTED ERRORS ' . (int) $id . ' @' . CurrentTime(),
                        'msg_body' => $longMsg->item(0)->nodeValue,
                        'msg_date' => CurrentTime()
                    ));
                    if ($severrors > 0)
                    {
                        if (isset($this->remote))
                            return '<BR>' . str_replace(">", "&gt;", str_replace("<", "&lt;", $longMsg->item(0)->nodeValue));
                        exit();
                    }
                }
                //}
                /*				*/
                $linkBase = "http://cgi.ebay.com/ws/eBayISAPI.dll?ViewItem&item=";
                $lfee     = '';
                $ifee     = '';
                $feeNodes = $responseDoc->getElementsByTagName('Fee');
                foreach ($feeNodes as $feeNode)
                {
                    $feeNames = $feeNode->getElementsByTagName("Name");
                    if ($feeNames->item(0))
                    {
                        $feeName = $feeNames->item(0)->nodeValue;
                        $fees    = $feeNode->getElementsByTagName('Fee'); // get Fee amount nested in Fee
                        $fee     = $fees->item(0)->nodeValue;
                        if ($fee > 0.0)
                        {
                            if ($feeName == 'ListingFee')
                            {
                                $lfee = $fee;
                            }
                            else
                            {
                                $ifee = $fee;
                            }
                        } // if $fee > 0
                    } // if feeName
                } // foreach $feeNode
            } // foreach response
            if ($itemID > 0)
            {
                $repref = '';
                if (isset($this->resubmit))
                    $repref = 'Re';
                $this->db->update('ebay', array(
                    'ebay_submitted' => CurrentTime(),
                    'ebay_msubm' => mktime(),
                    'ebay_id' => $itemID,
                    'Ack' => $ack,
                    'link' => $linkBase . $itemID,
                    'InsertionF' => $ifee,
                    'ListingF' => $lfee,
                    'ebayquantity' => $quantity,
                    'unsubmited' => 1,
                    'ebended' => NULL
                ), array(
                    'e_id' => (int) $id
                ));
                $search_term = commasep(commadesep($item['e_part']));
                $workdata    = array(
                    'newvals' => array(
                        array(
                            'name' => 'ebaytitle',
                            'value' => $item['e_title']
                        ),
                        array(
                            'name' => 'wherelisted',
                            'value' => 'eBay (' . $itemID . ')'
                        ),
                        array(
                            'name' => 'datelisted',
                            'value' => CurrentTime()
                        )
                    ),
                    'origin' => (int) $id,
                    'origin_type' => $repref . 'Submit-eBay',
                    'admin' => $this->session->userdata['ownnames'],
                    'gdrv' => $this->Auth_model->gDrv()
                );
                /* if (trim($search_term) != '')
                
                {
                
                $this->load->library('Googledrive');
                
                $this->load->library('Googlesheets');
                
                $res = $this->googlesheets->ProcessUpdate($this->googlesheets->PopulateWorksheetInfo($this->googledrive->FindInDrive(explode(',', $search_term))), $workdata);
                
                if ($res) $this->session->set_flashdata('success_msg', $res);
                
                }*/
            }
            if (isset($this->resubmit) && $itemID > 0)
                $this->db->update('ebay', array(
                    'ebended' => NULL,
                    'submitlog' => 'Resubmited @ ' . CurrentTime() . ' by ' . $this->session->userdata['ownnames'] . ' - Previous submit: ' . $item['ebay_submitted'] . ' ID ' . $item['ebay_id'] . '<br>' . $item['submitlog']
                ), array(
                    'e_id' => (int) $id
                ));
            log_message('error', 'SUBMIT SUCCESS RESPONSE ' . (int) $id . ' [' . $ack . '] @ ' . CurrentTime());
            //$this->_recordsubmiterror(array ('msg_title' => 'SUBMIT SUCCESS RESPONSE '.(int)$id.' @'.CurrentTime(), 'msg_body' => $ack, 'msg_date' => CurrentTime()));
            if (!isset($_POST['eid']))
            {
                $this->session->set_flashdata('action', (int) $id);
                $this->session->set_flashdata('gotoebay', $linkBase . $itemID);
            }
            if ($errors->length == 0)
            {
                //Redirect ('Myebay#'.(int)$id);
                //$this->PopulateItemSpecifics((int)$id);
                $this->db->insert('ebay_itemspec_que', array(
                    'e_id' => (int) $id,
                    'ts' => mktime()
                ));
                if (isset($this->remote))
                    return $itemID;
                else
                    echo '<span style="font-size:14px; font-weight:bold; color:#0066C;"><br><br>Redirecting back in 2 seconds...</span><script type="text/JavaScript">

<!--

setTimeout("location.href = \'' . Site_url() . 'Myebay#' . (int) $id . '\';",4000);

-->

</script>';
            }
            else
            {
                if (isset($this->remote))
                {
                    $retmsg = '';
                    if (isset($aresponse['Errors']['SeverityCode']))
                    {
                        $retmsg .= '<strong>' . $aresponse['Errors']['SeverityCode'] . ':</strong>';
                        unset($aresponse['Errors']['SeverityCode']);
                    }
                    if (isset($aresponse['Errors']['LongMessage']))
                    {
                        $retmsg .= $aresponse['Errors']['LongMessage'] . '<br><br>';
                        unset($aresponse['Errors']['LongMessage']);
                    }
                    if (isset($aresponse['Errors']['ShortMessage']))
                        unset($aresponse['Errors']['ShortMessage']);
                    if (isset($aresponse['Errors']['ErrorCode']))
                        unset($aresponse['Errors']['ErrorCode']);
                    if (isset($aresponse['Errors']['ErrorParameters']))
                        unset($aresponse['Errors']['ErrorParameters']);
                    if (isset($aresponse['Errors']['ErrorClassification']))
                        unset($aresponse['Errors']['ErrorClassification']);
                    if (count($aresponse['Errors']) > 0)
                    {
                        foreach ($aresponse['Errors'] as $d)
                        {
                            log_message('error', 'SUBMITION ERRORS ' . (int) $id . ' [' . $ack . '] @ ' . CurrentTime() . ' - ' . $d['LongMessage']);
                            $retmsg .= '<span style="color:red; font-weight:strong;">' . $d['LongMessage'] . '</span><br>';
                            if (isset($d['ErrorParameters']['Value']))
                            {
                                $retmsg .= $d['ErrorParameters']['Value'] . '<Br><br>';
                                GoMail(array(
                                    'msg_title' => 'Submit Error for ' . (int) $id . ' @ ' . CurrentTime(),
                                    'msg_body' => $d['LongMessage'] . '<br><br>' . $d['ErrorParameters']['Value'],
                                    'msg_date' => CurrentTime()
                                ), $this->config->config['support_email'], $this->config->config['no_reply_email']);
                                GoMail(array(
                                    'msg_title' => 'Submit Error for ' . (int) $id . ' @ ' . CurrentTime(),
                                    'msg_body' => $d['LongMessage'] . '<br><br>' . $d['ErrorParameters']['Value'],
                                    'msg_date' => CurrentTime()
                                ), 'mitko@rusev.me', $this->config->config['no_reply_email']);
                            }
                            else
                            {
                                GoMail(array(
                                    'msg_title' => 'Submit Error for ' . (int) $id . ' @ ' . CurrentTime(),
                                    'msg_body' => $d['LongMessage'],
                                    'msg_date' => CurrentTime()
                                ), $this->config->config['support_email'], $this->config->config['no_reply_email']);
                                GoMail(array(
                                    'msg_title' => 'Submit Error for ' . (int) $id . ' @ ' . CurrentTime(),
                                    'msg_body' => $d['LongMessage'],
                                    'msg_date' => CurrentTime()
                                ), 'mitko@rusev.me', $this->config->config['no_reply_email']);
                            }
                        }
                    }
                    $retmsg .= '<br><br>YOU MAY CONTINUE THROUGH <a href="' . site_url() . 'Myebay#' . (int) $id . '">THIS LINK</a>.<Br><br> SOME ERROR MESSAGES ARE NOTICES AND THE LOCAL PROCESSING MAY HAVE BEEN COMPLETED.';
                    return $retmsg;
                }
                else
                {
                    if (isset($aresponse['Errors']['SeverityCode']))
                    {
                        echo '<strong>' . $aresponse['Errors']['SeverityCode'] . ':</strong>';
                        unset($aresponse['Errors']['SeverityCode']);
                    }
                    if (isset($aresponse['Errors']['LongMessage']))
                    {
                        echo $aresponse['Errors']['LongMessage'] . '<br><br>';
                        unset($aresponse['Errors']['LongMessage']);
                    }
                    if (isset($aresponse['Errors']['ShortMessage']))
                        unset($aresponse['Errors']['ShortMessage']);
                    if (isset($aresponse['Errors']['ErrorCode']))
                        unset($aresponse['Errors']['ErrorCode']);
                    if (isset($aresponse['Errors']['ErrorParameters']))
                        unset($aresponse['Errors']['ErrorParameters']);
                    if (isset($aresponse['Errors']['ErrorClassification']))
                        unset($aresponse['Errors']['ErrorClassification']);
                    if (count($aresponse['Errors']) > 0)
                    {
                        foreach ($aresponse['Errors'] as $d)
                        {
                            log_message('error', 'SUBMITION ERRORS ' . (int) $id . ' [' . $ack . '] @ ' . CurrentTime() . ' - ' . $d['LongMessage']);
                            echo '<span style="color:red; font-weight:strong;">' . $d['LongMessage'] . '</span><br>';
                            if (isset($d['ErrorParameters']['Value']))
                            {
                                echo $d['ErrorParameters']['Value'] . '<Br><br>';
                                GoMail(array(
                                    'msg_title' => 'Submit Error for ' . (int) $id . ' @ ' . CurrentTime(),
                                    'msg_body' => $d['LongMessage'] . '<br><br>' . $d['ErrorParameters']['Value'],
                                    'msg_date' => CurrentTime()
                                ), $this->config->config['support_email'], $this->config->config['no_reply_email']);
                                GoMail(array(
                                    'msg_title' => 'Submit Error for ' . (int) $id . ' @ ' . CurrentTime(),
                                    'msg_body' => $d['LongMessage'] . '<br><br>' . $d['ErrorParameters']['Value'],
                                    'msg_date' => CurrentTime()
                                ), 'mitko@rusev.me', $this->config->config['no_reply_email']);
                            }
                            else
                            {
                                GoMail(array(
                                    'msg_title' => 'Submit Error for ' . (int) $id . ' @ ' . CurrentTime(),
                                    'msg_body' => $d['LongMessage'],
                                    'msg_date' => CurrentTime()
                                ), $this->config->config['support_email'], $this->config->config['no_reply_email']);
                                GoMail(array(
                                    'msg_title' => 'Submit Error for ' . (int) $id . ' @ ' . CurrentTime(),
                                    'msg_body' => $d['LongMessage'],
                                    'msg_date' => CurrentTime()
                                ), 'mitko@rusev.me', $this->config->config['no_reply_email']);
                            }
                        }
                    }
                    echo '<br><br>YOU MAY CONTINUE THROUGH <a href="' . site_url() . 'Myebay#' . (int) $id . '">THIS LINK</a>.<Br><br> SOME ERROR MESSAGES ARE NOTICES AND THE LOCAL PROCESSING MAY HAVE BEEN COMPLETED.';
                }
            }
            //Redirect ('Myebay#'.(int)$id);
            //} // if $errors->length > 0
        }
    }
    function _XML2Array($parent)
    {
        $array = array();
        foreach ($parent as $name => $element)
        {
            ($node =& $array[$name]) && (1 === count($node) ? $node = array(
                $node
            ) : 1) && $node =& $node[];
            $node = $element->count() ? $this->_XML2Array($element) : trim($element);
        }
        return $array;
    }
    function Remoteresubmit($id = 0)
    {
        $this->Auth_model->CheckListings();
        $this->resubmit = TRUE;
        $this->remove   = TRUE;
        $this->SubmitEbay($id);
    }
    function EndListing($id = 0)
    {
        $this->Auth_model->CheckListings();
        if (isset($_POST['eid']))
            $id = (int) $_POST['eid'];
        if ((int) $id > 0)
        {
            $reason = 'NotAvailable'; //$this->input->post('endreason', TRUE);
            $this->session->set_flashdata('action', (int) $id);
            set_time_limit(90);
            $item = $this->Myebay_model->GetItem((int) $id);
            if (!$item)
            {
                echo 'Item not found!';
                exit();
            }
            require_once($this->config->config['ebaypath'] . 'get-common/keys.php');
            require_once($this->config->config['ebaypath'] . 'get-common/eBaySession.php');
            ini_set('magic_quotes_gpc', false);
            $requestXmlBodySTART = '<?xml version="1.0" encoding="utf-8" ?><EndItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
            $requestXmlBody      = "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
            $requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';
            $requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
            $requestXmlBody .= "<Version>$compatabilityLevel</Version>";
            $requestXmlBody .= "<EndingReason>" . $reason . "</EndingReason>

  <ItemID>" . $item['ebay_id'] . "</ItemID>";
            $requestXmlBodyEND = '</EndItemRequest>';
            $session           = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], 'EndItem');
            GoMail(array(
                'msg_title' => 'End listing step 1 - ' . (int) $id . ' @' . CurrentTime(),
                'msg_body' => $requestXmlBodySTART . $requestXmlBody . $requestXmlBodyEND,
                'msg_date' => CurrentTime()
            ), $this->config->config['support_email'], $this->config->config['no_reply_email']);
            $responseXml = $session->sendHttpRequest($requestXmlBodySTART . $requestXmlBody . $requestXmlBodyEND);
            if (stristr($responseXml, 'HTTP 404') || $responseXml == '')
                die('<P>Error sending request');
            GoMail(array(
                'msg_title' => 'End listing step 2 - ' . (int) $id . ' @' . CurrentTime(),
                'msg_body' => $responseXml,
                'msg_date' => CurrentTime()
            ), $this->config->config['support_email'], $this->config->config['no_reply_email']);
            //Xml string is parsed and creates a DOM Document object
            $responseDoc = new DomDocument();
            $responseDoc->loadXML($responseXml);
            $sxml      = simplexml_load_string($responseXml);
            $aresponse = $this->_XML2Array($sxml);
            GoMail(array(
                'msg_title' => 'End listing step 3 - ' . (int) $id . ' @' . CurrentTime(),
                'msg_body' => printcool($aresponse, true),
                'msg_date' => CurrentTime()
            ), $this->config->config['support_email'], $this->config->config['no_reply_email']);
            $errors = $responseDoc->getElementsByTagName('Errors');
            if ($errors->length > 0)
            {
                $code     = $errors->item(0)->getElementsByTagName('ErrorCode');
                $shortMsg = $errors->item(0)->getElementsByTagName('ShortMessage');
                $longMsg  = $errors->item(0)->getElementsByTagName('LongMessage');
                $severity = $errors->item(0)->getElementsByTagName('SeverityCode');
                GoMail(array(
                    'msg_title' => 'End listing step 4 - ' . (int) $id . ' @' . CurrentTime(),
                    'msg_body' => $longMsg,
                    'msg_date' => CurrentTime()
                ), $this->config->config['support_email'], $this->config->config['no_reply_email']);
                log_message('error', 'SUBMIT STEP 11 ' . (int) $id . ' @' . CurrentTime());
                $severrors = 0;
                if (count($longMsg) > 0)
                {
                    echo '<BR>', str_replace(">", "&gt;", str_replace("<", "&lt;", $longMsg->item(0)->nodeValue));
                    if ((string) $severity->item(0)->nodeValue !== 'Warning')
                    {
                        echo '<BR>', str_replace(">", "&gt;", str_replace("<", "&lt;", $longMsg->item(0)->nodeValue));
                        exit();
                    }
                    else
                        echo '<BR>', str_replace(">", "&gt;", str_replace("<", "&lt;", $longMsg->item(0)->nodeValue));
                }
            }
            $acks = $responseDoc->getElementsByTagName("Ack");
            $ack  = $acks->item(0)->nodeValue;
            GoMail(array(
                'msg_title' => 'End listing step 5 - ' . (int) $id . ' @' . CurrentTime(),
                'msg_body' => $ack,
                'msg_date' => CurrentTime()
            ), $this->config->config['support_email'], $this->config->config['no_reply_email']);
            if ($ack == 'Success')
                $this->db->update('ebay', array(
                    'ebended' => CurrentTime(),
                    'endedreason' => $reason . ' by ' . $this->session->userdata['ownnames']
                ), array(
                    'e_id' => (int) $id
                ));
            if (!isset($_POST['eid']))
            {
                $this->session->set_flashdata('action', (int) $id);
                if ($ack == 'Success')
                    $this->session->set_flashdata('success_msg', 'Result: ' . $ack);
                else
                    $this->session->set_flashdata('error_msg', 'Result: ' . $ack);
            }
            else
            {
                if ($ack == 'Success')
                    echo 1;
                else
                    echo $ack;
                exit();
            }
            if ($errors->length == 0)
            {
                echo '<span style="font-size:14px; font-weight:bold; color:#0066C;"><br><br>Redirecting back in 2 seconds...</span><script type="text/JavaScript">

<!--

setTimeout("location.href = \'' . Site_url() . 'Myebay/Search/' . (int) $id . '\';",4000);

-->

</script>';
            }
        }
    }
    function ReviseEbay($id = 0)
    {
        $this->Auth_model->CheckListings();
        if (isset($_POST['eid']))
            $id = (int) $_POST['eid'];
        //echo $id;
        if ((int) $id > 0)
        {
            $this->session->set_flashdata('action', (int) $id);
            //redirect("Myebay");
            set_time_limit(90);
            $item   = $this->Myebay_model->GetItem((int) $id);
            $zip    = $this->Myebay_model->GetSetting('EbayLocationZIP');
            $ppmail = $this->Myebay_model->GetSetting('EbayPayPalMAIL');
            if (!$item)
                exit('Item not found!');
            require_once($this->config->config['ebaypath'] . 'get-common/keys.php');
            require_once($this->config->config['ebaypath'] . 'get-common/eBaySession.php');
            ini_set('magic_quotes_gpc', false);
            $this->mysmarty->assign('displays', $item);
            $listDescHtml    = htmlspecialchars($this->mysmarty->fetch('myebay/myebay_sendsource.html'));
            $listingType     = $item['listingType'];
            $primaryCategory = $item['primaryCategory'];
            $itemTitle       = str_replace('&', '&amp;', $item['e_title']);
            if (get_magic_quotes_gpc())
                $itemDescription = stripslashes($item['e_desc']);
            else
                $itemDescription = $item['e_desc'];
            $listingDuration = $item['listingDuration'];
            $startPrice      = $item['price_ch1']; //$item['buyItNowPrice'];//$item['startPrice'];
            $buyItNowPrice   = $item['price_ch1']; //$item['buyItNowPrice'];
            $quantity        = $item['qn_ch1']; //$item['quantity'];
            $PaymentMethods  = $item['PaymentMethod'];
            $upc             = $item['upc'];
            $shipping        = $item['shipping'];
            $storecat        = $item['storeCatID'];
            if ($listingType == 'StoresFixedPrice')
            {
                $buyItNowPrice   = 0.0; // don't have BuyItNow for SIF
                $listingDuration = 'GTC';
            }
            if ($listingType == 'Dutch')
                $buyItNowPrice = $buyItNowPrice; // don't have BuyItNow for Dutch
            $conditiondescription = '';
            if ($item['Condition'] != 1000)
                $conditiondescription = "<ConditionDescription>" . $item['e_condition'] . "</ConditionDescription>";
            if (isset($PaymentMethods))
            {
                $paymentsnippet = '';
                if ($PaymentMethods != '')
                    foreach ($PaymentMethods as $k => $v)
                    {
                        $paymentsnippet .= '<PaymentMethods>' . $k . '</PaymentMethods>';
                    }
                if (isset($PaymentMethods['PayPal']))
                    $paymentsnippet .= '<PayPalEmailAddress>' . $ppmail . '</PayPalEmailAddress>';
            }
            $verb       = 'ReviseItem';
            $upcsnippet = '';
            $true       = TRUE;
            /*if ($upc != '' || ($item['e_compat'] != '' && $item['e_manuf'] != '')) $upcsnippet .= '<ProductListingDetails><IncludePrefilledItemInformation>'.$true.'</IncludePrefilledItemInformation>';
            
            if ($upc != '')	$upcsnippet .= '<ProductListingDetails><IncludePrefilledItemInformation>'.$true.'</IncludePrefilledItemInformation><UPC>'.$upc.'</UPC></ProductListingDetails>';
            
            if ($item['e_compat'] =! '' && $item['e_manuf'] != '') $upcsnippet .= '<BrandMPN><Brand>'.$item['e_manuf'].'</Brand><MPN>'.$item['e_compat'].'</MPN></BrandMPN>';
            
            if ($upc != '' || ($item['e_compat'] != '' && $item['e_manuf'] != '')) $upcsnippet .= '</ProductListingDetails>';*/
            if ($upc != '' && $item['e_compat'] != '' && $item['e_manuf'] != '')
                $upcsnippet .= '<ProductListingDetails><IncludePrefilledItemInformation>' . $true . '</IncludePrefilledItemInformation><ProductListingDetails><IncludePrefilledItemInformation>' . $true . '</IncludePrefilledItemInformation><UPC>' . $upc . '</UPC></ProductListingDetails><BrandMPN><Brand>' . $item['e_manuf'] . '</Brand><MPN>' . $item['e_compat'] . '</MPN></BrandMPN></ProductListingDetails>';
            $imgsnippet = '<PictureDetails>';
            if ($item['e_img1'] != '')
                $imgsnippet .= '<PictureURL>' . Site_url() . 'ebay_images/' . $item['idpath'] . '/Ebay_' . $item['e_img1'] . '</PictureURL>';
            if ($item['e_img2'] != '')
                $imgsnippet .= '<PictureURL>' . Site_url() . 'ebay_images/' . $item['idpath'] . '/Ebay_' . $item['e_img2'] . '</PictureURL>';
            if ($item['e_img3'] != '')
                $imgsnippet .= '<PictureURL>' . Site_url() . 'ebay_images/' . $item['idpath'] . '/Ebay_' . $item['e_img3'] . '</PictureURL>';
            if ($item['e_img4'] != '')
                $imgsnippet .= '<PictureURL>' . Site_url() . 'ebay_images/' . $item['idpath'] . '/Ebay_' . $item['e_img4'] . '</PictureURL>';
            if ($item['e_img5'] != '')
                $imgsnippet .= '<PictureURL>' . Site_url() . 'ebay_images/' . $item['idpath'] . '/Ebay_' . $item['e_img5'] . '</PictureURL>';
            if ($item['e_img6'] != '')
                $imgsnippet .= '<PictureURL>' . Site_url() . 'ebay_images/' . $item['idpath'] . '/Ebay_' . $item['e_img6'] . '</PictureURL>';
            if ($item['e_img7'] != '')
                $imgsnippet .= '<PictureURL>' . Site_url() . 'ebay_images/' . $item['idpath'] . '/Ebay_' . $item['e_img7'] . '</PictureURL>';
            if ($item['e_img8'] != '')
                $imgsnippet .= '<PictureURL>' . Site_url() . 'ebay_images/' . $item['idpath'] . '/Ebay_' . $item['e_img8'] . '</PictureURL>';
            $imgsnippet .= '</PictureDetails>';
            $requestXmlBodySTART = '<?xml version="1.0" encoding="utf-8"?>

<ReviseItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
            $requestXmlBody      = "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
            $requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
            //<VerifyOnly>".TRUE."</VerifyOnly>
            $requestXmlBody .= "<Version>$compatabilityLevel</Version>";
            $requestXmlBody .= "<Item>

    <Description>" . $listDescHtml . "</Description>

    <DescriptionReviseMode>Replace</DescriptionReviseMode>

    <ItemID>" . $item['ebay_id'] . "</ItemID>

    <ListingDuration>" . $item['listingDuration'] . "</ListingDuration>   

    

    <ConditionID>" . $item['Condition'] . "</ConditionID>

	" . $conditiondescription . "		    

	<StartPrice>" . $startPrice . "</StartPrice>

	" . $upcsnippet . "	

	<Quantity>" . $quantity . "</Quantity>

    <StartPrice>" . $startPrice . "</StartPrice>

    <Storefront><StoreCategoryID>" . $storecat . "</StoreCategoryID></Storefront>

    <Title>" . $itemTitle . "</Title>

	" . $paymentsnippet . "

	" . $imgsnippet;
            if (is_array($shipping))
            {
                $requestXmlBody .= "<ShippingDetails>";
                if (isset($shipping['domestic']))
                    foreach ($shipping['domestic'] as $k => $s)
                    {
                        if ($s['ShippingService'] != '')
                        {
                            if (isset($s['FreeShipping']) && $s['FreeShipping'] == 'on')
                                $fssnip = "<FreeShipping>" . true . "</FreeShipping>";
                            else
                                $fssnip = "";
                            $requestXmlBody .= "<ShippingServiceOptions>

       				 <ShippingServicePriority>" . $k . "</ShippingServicePriority>

        			 <ShippingService>" . $s['ShippingService'] . "</ShippingService>

      				 <ShippingServiceCost>" . (float) $s['ShippingServiceCost'] . "</ShippingServiceCost>

					 <ShippingServiceAdditionalCost currencyID=\"USD\">" . (float) $s['ShippingServiceAdditionalCost'] . "</ShippingServiceAdditionalCost>

					" . $fssnip . "					

      				</ShippingServiceOptions>";
                        }
                    }
                if (isset($shipping['international']))
                    foreach ($shipping['international'] as $k => $s)
                    {
                        if ($s['ShippingService'] != '')
                        {
                            $requestXmlBody .= "<InternationalShippingServiceOption>

		 <ShippingService>" . $s['ShippingService'] . "</ShippingService>

        <ShippingServiceAdditionalCost currencyID=\"USD\">" . (float) $s['ShippingServiceAdditionalCost'] . "</ShippingServiceAdditionalCost>

        <ShippingServiceCost currencyID=\"USD\">" . (float) $s['ShippingServiceCost'] . "</ShippingServiceCost>

        <ShippingServicePriority>" . $k . "</ShippingServicePriority>

        <ShipToLocation>" . $s['ShipToLocation'] . "</ShipToLocation>

  	    </InternationalShippingServiceOption>";
                        }
                    }
                $requestXmlBody .= "</ShippingDetails>";
            }
            $requestXmlBody .= "</Item>";
            $requestXmlBodyEND = '</ReviseItemRequest>';
            //GoMail(array ('msg_title' => 'REVISED '.(int)$id.' @'.CurrentTime(), 'msg_body' => $requestXmlBody, 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
            $session           = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
            $responseXml       = $session->sendHttpRequest($requestXmlBodySTART . $requestXmlBody . $requestXmlBodyEND);
            if (stristr($responseXml, 'HTTP 404') || $responseXml == '')
                die('<P>Error sending request');
            $responseDoc = new DomDocument();
            $responseDoc->loadXML($responseXml);
            $errors = $responseDoc->getElementsByTagName('Errors');
            if ($errors->length > 0)
            {
                echo '<P><B>eBay returned the following error(s):</B>';
                $code     = $errors->item(0)->getElementsByTagName('ErrorCode');
                $shortMsg = $errors->item(0)->getElementsByTagName('ShortMessage');
                $longMsg  = $errors->item(0)->getElementsByTagName('LongMessage');
                echo '<P>', $code->item(0)->nodeValue, ' : ', str_replace(">", "&gt;", str_replace("<", "&lt;", $shortMsg->item(0)->nodeValue));
                if (count($longMsg) > 0)
                    echo '<BR>', str_replace(">", "&gt;", str_replace("<", "&lt;", $longMsg->item(0)->nodeValue));
                $this->_recordsubmiterror(array(
                    'msg_title' => 'REVISE ERRORS ' . (int) $id . ' @' . CurrentTime(),
                    'msg_body' => printcool(str_replace(">", "&gt;", str_replace("<", "&lt;", $longMsg->item(0)->nodeValue)), TRUE),
                    'msg_date' => CurrentTime()
                ));
                if (!isset($_POST['eid']))
                    echo '<a href="javascript:history.back()">Back</a>';
            }
            // else { //no errors
            //get results nodes
            $responses = $responseDoc->getElementsByTagName("ReviseItemResponse");
            if ($responses)
                foreach ($responses as $response)
                {
                    $acks = $response->getElementsByTagName("Ack");
                    $ack  = $acks->item(0)->nodeValue;
                    if (!isset($_POST['eid']))
                        $this->session->set_flashdata('success_msg', 'Result: ' . $ack);
                } // foreach response
            $this->db->update('ebay', array(
                'ebayquantity' => $quantity,
                'submitlog' => 'Revised @ ' . CurrentTime() . ' by ' . $this->session->userdata['ownnames'] . '<br>' . $item['submitlog']
            ), array(
                'e_id' => (int) $id
            ));
            if (!isset($_POST['eid']))
            {
                $this->session->set_flashdata('action', (int) $id);
                $this->session->set_flashdata('gotoebay', "http://cgi.ebay.com/ws/eBayISAPI.dll?ViewItem&item=" . $item['ebay_id']);
                if ($errors->length == 0)
                    Redirect('Myebay#' . (int) $id);
                else
                    echo '<br><br>YOU MAY CONTINUE THROUGH <a href="' . site_url() . 'Myebay#' . (int) $id . '">THIS LINK</a>.<Br><br> SOME ERROR MESSAGES ARE NOTICES AND THE LOCAL PROCESSING MAY HAVE BEEN COMPLETED.';
            }
            else
                echo '<br><br>YOU MAY CONTINUE THROUGH <a href="' . site_url() . 'Myebay#' . (int) $id . '">THIS LINK</a>.<Br><br> SOME ERROR MESSAGES ARE NOTICES AND THE LOCAL PROCESSING MAY HAVE BEEN COMPLETED.';
            //}
        }
    }
    function testgtc()
    {
        set_time_limit(600);
        ini_set('mysql.connect_timeout', 600);
        ini_set('max_execution_time', 600);
        ini_set('default_socket_timeout', 600);
        require($this->config->config['ebaypath'] . 'get-common/keys.php');
        require_once($this->config->config['ebaypath'] . 'get-common/eBaySession.php');
        $requestXmlBody = '<?xml version="1.0" encoding="utf-8" ?>';
        $requestXmlBody .= '<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        $requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
        $requestXmlBody .= '<DetailLevel>ItemReturnAttributes</DetailLevel>';
        $requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
        $requestXmlBody .= "<Version>$compatabilityLevel</Version>";
        $requestXmlBody .= '<ItemID>201562342174</ItemID></GetItemRequest>';
        $verb        = 'GetItem';
        $session     = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
        $responseXml = $session->sendHttpRequest($requestXmlBody);
        if (stristr($responseXml, 'HTTP 404') || $responseXml == '')
            die('<P>Error sending request');
        $xml            = simplexml_load_string($responseXml);
        //printcool ($xml->Item->OutOfStockControl);
        $requestXmlBody = '<?xml version="1.0" encoding="utf-8" ?>';
        $requestXmlBody .= '<GetMyeBaySellingRequest xmlns="urn:ebay:apis:eBLBaseComponents">

';
        $requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
        //$requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';
        $requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
        $requestXmlBody .= "<Version>$compatabilityLevel</Version>";
        $days = 60; //maxtime
        $requestXmlBody .= " <ActiveList>
    <Include>TRUE</Include>
	<Pagination>
<EntriesPerPage>200</EntriesPerPage>
<PageNumber>1</PageNumber>
</Pagination>
  </ActiveList> 
  <HideVariations>FALSE</HideVariations> 
  <SellingSummary>
    <Include>TRUE</Include>
  </SellingSummary> 
  </GetMyeBaySellingRequest>";
        $verb        = 'GetMyeBaySelling';
        $session     = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
        $responseXml = $session->sendHttpRequest($requestXmlBody);
        if (stristr($responseXml, 'HTTP 404') || $responseXml == '')
            die('<P>Error sending request');
        $xml               = simplexml_load_string($responseXml);
        $pages             = (int) $xml->ActiveList->PaginationResult->TotalNumberOfPages;
        $entries           = (int) $xml->ActiveList->PaginationResult->TotalNumberOfEntries;
        $list['active'][1] = $xml->ActiveList->ItemArray;
        if ($pages > 1)
        {
            $page = 2;
            while ($page <= $pages)
            {
                $requestXmlBody = '<?xml version="1.0" encoding="utf-8" ?>';
                $requestXmlBody .= '<GetMyeBaySellingRequest xmlns="urn:ebay:apis:eBLBaseComponents">

';
                $requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
                $requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';
                $requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
                $requestXmlBody .= "<Version>$compatabilityLevel</Version>";
                $requestXmlBody .= "<ActiveList><Include>TRUE</Include><Pagination><EntriesPerPage>200</EntriesPerPage><PageNumber>" . $page . "</PageNumber></Pagination></ActiveList></GetMyeBaySellingRequest>";
                $verb        = 'GetMyeBaySelling';
                $session     = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
                $responseXml = $session->sendHttpRequest($requestXmlBody);
                if (stristr($responseXml, 'HTTP 404') || $responseXml == '')
                    die('<P>Error sending request');
                $xml                   = simplexml_load_string($responseXml);
                $list['active'][$page] = $xml->ActiveList->ItemArray;
                $page++;
            }
        }
        $this->load->helper('explore');
        printcool($list);
    }
    function AutoSendDescRevise()
    {
        $this->db->select('e_id');
        $this->db->where('ebended', NULL);
        $this->db->where('ebay_id !=', '');
        $this->db->where('housekeeping', 0);
        $q = $this->db->get('ebay');
        if ($q->num_rows() > 0)
        {
            printcool($q->num_rows());
            set_time_limit(600);
            require_once($this->config->config['ebaypath'] . 'get-common/keys.php');
            require_once($this->config->config['ebaypath'] . 'get-common/eBaySession.php');
            foreach ($q->result_array() as $r)
            {
                $item = $this->Myebay_model->GetItem((int) $r['e_id']);
                ini_set('magic_quotes_gpc', false);
                $this->mysmarty->assign('displays', $item);
                $listDescHtml        = htmlspecialchars($this->mysmarty->fetch('myebay/myebay_sendsource.html'));
                $requestXmlBodySTART = '<?xml version="1.0" encoding="utf-8"?>

<ReviseItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
                $requestXmlBody      = "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
                $requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
                //<VerifyOnly>".TRUE."</VerifyOnly>
                $requestXmlBody .= "<Version>$compatabilityLevel</Version>";
                $requestXmlBody .= "<Item>

    <Description>" . $listDescHtml . "</Description>

    <DescriptionReviseMode>Replace</DescriptionReviseMode>

    <ItemID>" . $item['ebay_id'] . "</ItemID>";
                $requestXmlBody .= "</Item>";
                $requestXmlBodyEND = '</ReviseItemRequest>';
                $session           = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], 'ReviseItem');
                $responseXml       = $session->sendHttpRequest($requestXmlBodySTART . $requestXmlBody . $requestXmlBodyEND);
                if (stristr($responseXml, 'HTTP 404') || $responseXml == '')
                    die('<P>Error sending request');
                $responseDoc = new DomDocument();
                $responseDoc->loadXML($responseXml);
                $errors = $responseDoc->getElementsByTagName('Errors');
                if ($errors->length > 0)
                {
                    echo '<P><B>eBay returned the following error(s):</B>';
                    $code     = $errors->item(0)->getElementsByTagName('ErrorCode');
                    $shortMsg = $errors->item(0)->getElementsByTagName('ShortMessage');
                    $longMsg  = $errors->item(0)->getElementsByTagName('LongMessage');
                    echo '<P>', $code->item(0)->nodeValue, ' : ', str_replace(">", "&gt;", str_replace("<", "&lt;", $shortMsg->item(0)->nodeValue));
                    if (count($longMsg) > 0)
                        echo '<BR>', str_replace(">", "&gt;", str_replace("<", "&lt;", $longMsg->item(0)->nodeValue));
                    $this->_recordsubmiterror(array(
                        'msg_title' => 'REVISE DESCRIPTION ERRORS ' . (int) $r['e_id'] . ' @' . CurrentTime(),
                        'msg_body' => printcool(str_replace(">", "&gt;", str_replace("<", "&lt;", $longMsg->item(0)->nodeValue)), TRUE),
                        'msg_date' => CurrentTime()
                    ));
                    //if ($save) $this->db->update('ebay', array('autorev' => -1, 'autorevtxt' => str_replace(">", "&gt;", str_replace("<", "&lt;", $longMsg->item(0)->nodeValue)).' @ '.CurrentTime()), array('e_id' => (int)$id));
                }
                else //no errors
                {
                    //get results nodes
                    $responses = $responseDoc->getElementsByTagName("ReviseItemResponse");
                    $txtresp   = '';
                    foreach ($responses as $response)
                    {
                        $acks = $response->getElementsByTagName("Ack");
                        $ack  = $acks->item(0)->nodeValue;
                        $txtresp .= 'Result: ' . $ack . '<br>';
                    } // foreach response
                    if (isset($_POST['eid']))
                        exit($txtresp);
                    //GoMail(array ('msg_title' => 'REVISED DESCRIPTION '.(int)$id.' @'.CurrentTime(), 'msg_body' => $txtresp, 'msg_date' => CurrentTime()), $this->config->config['support_email'], $this->config->config['no_reply_email']);
                    //if ($save) $this->db->update('ebay', array('autorev' => 1, 'autorevtxt' => '@ '.CurrentTime()), array('e_id' => (int)$id));
                }
                $this->db->update('ebay', array(
                    'housekeeping' => 1
                ), array(
                    'e_id' => $r['e_id']
                ));
                printcool($r);
            }
        }
    }
    function NewEbayQuantityRevise($id = 0, $page = '')
    {
        $this->Auth_model->CheckListings();
        $this->EbayInventoryUpdate((int) $id, false);
        echo '<span style="font-size:14px; font-weight:bold; color:#0066C;"><br><br>Redirecting back in 2 seconds...</span><script type="text/JavaScript">

<!--

setTimeout("location.href = \'' . Site_url() . 'Myebay/ListItems/' . (int) $page . '#' . (int) $id . '\';",4000);

-->

</script>';
    }
    function NoBCNReq($rec = 0)
    {
        $this->Auth_model->CheckOrders();
        if ((int) $rec != 0)
        {
            $this->db->select('e.e_id, e.ebay_id, t.admin, t.revs');
            $this->db->where('t.itemid = e.ebay_id');
            $this->db->where('t.rec', (int) $rec);
            $q = $this->db->get('ebay as e, ebay_transactions as t');
            if ($q->num_rows() > 0)
                $res = $q->row_array();
            else
            {
                echo 'Transaction data not found. Contact administrator';
                exit();
            }
            $res['revs']++;
            if ($res['admin'] == '')
                $res['admin'] = '(' . $res['revs'] . ') ' . $this->session->userdata['ownnames'];
            else
                $res['admin'] = '(' . $res['revs'] . ') ' . $this->session->userdata['ownnames'] . ', ' . $res['admin'];
            //DIMITRI - We must keep what's been submited, even if it's not matched in the listing. There will be cases this will be needed.
            $this->db->update('ebay_transactions', array(
                'mark' => 1,
                'admin' => $res['admin'],
                'revs' => $res['revs']
            ), array(
                'rec' => (int) $rec
            ));
            $this->db->insert('admin_history', array(
                'msg_type' => 1,
                'msg_title' => '<span style="color:blue;">Transaction BCN Updated</span> to <span style="color:#FF9900;">"Does not require BCN"</span>',
                'msg_body' => '',
                'msg_date' => CurrentTime(),
                'e_id' => $res['e_id'],
                'itemid' => $res['ebay_id'],
                'trec' => $rec,
                'admin' => $this->session->userdata['ownnames'],
                'sev' => ''
            ));
        }
        $this->session->set_flashdata('action', (int) $rec);
        $sortstring = $this->session->userdata['sortstring'];
        if ($sortstring != '')
            echo '<span style="font-size:14px; font-weight:bold; color:#0066C;"><br><br>Redirecting back in 2 seconds...</span><script type="text/JavaScript">

<!--

setTimeout("location.href = \'' . Site_url() . 'Myebay/SortOrders/' . $sortstring . '#' . (int) $rec . '\';",4000);

-->

</script>';
        else
            echo '<span style="font-size:14px; font-weight:bold; color:#0066C;"><br><br>Redirecting back in 2 seconds...</span><script type="text/JavaScript">

<!--

setTimeout("location.href = \'' . Site_url() . 'Myebay/GetOrders/#' . (int) $rec . '\';",4000);

-->

</script>';
    }
    function CheckEbayValue($itemid = 0, $id = 0, $actionlog = 0)
    {
        $this->Auth_model->CheckListings();
        if ((int) $id > 0 && $itemid > 0 && $actionlog > 0)
        {
            require_once($this->config->config['ebaypath'] . 'get-common/keys.php');
            require_once($this->config->config['ebaypath'] . 'get-common/eBaySession.php');
            $requestXmlBody = '<?xml version="1.0" encoding="utf-8" ?>';
            $requestXmlBody .= '<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
            $requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
            $requestXmlBody .= '<DetailLevel>ItemReturnAttributes</DetailLevel>';
            $requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
            $requestXmlBody .= "<Version>$compatabilityLevel</Version>";
            $requestXmlBody .= '<ItemID>' . (int) $itemid . '</ItemID></GetItemRequest>';
            $verb        = 'GetItem';
            $session     = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
            $responseXml = $session->sendHttpRequest($requestXmlBody);
            if (stristr($responseXml, 'HTTP 404') || $responseXml == '')
                die('<P>Error sending request');
            $xml = simplexml_load_string($responseXml);
            if ((string) $xml->Item->ItemID == '')
            {
                echo 'ERROR: Invalid Item ID...<a href="javascript:history.back()">Back</a>';
                exit();
            }
            $this->db->select('ebayquantity, ebay_id');
            $this->db->where('e_id', (int) $id);
            $this->db->where('ebay_id', (string) $xml->Item->ItemID);
            $query = $this->db->get('ebay');
            if ($query->num_rows() > 0)
            {
                $ebr = $query->row_array();
                if ($ebr['ebayquantity'] == (string) $xml->Item->Quantity)
                    echo '<span style="color: green;">LOCAL VALUE IS CORRECT! - Local eBay Value: ' . $ebr['ebayquantity'] . ' - @ eBay Value: ' . (string) $xml->Item->Quantity;
                else
                {
                    echo '<span style="color: red;">LOCAL VALUE IS INCORRECT! - Local eBay Value: ' . $ebr['ebayquantity'] . ' - @ eBay Value: ' . (string) $xml->Item->Quantity . '<br><br>Record now updated';
                    $this->db->update('ebay', array(
                        'ebayquantity' => (string) $xml->Item->Quantity
                    ), array(
                        'e_id' => (int) $id
                    ));
                    $this->db->update('ebay_actionlog', array(
                        'datato' => (string) $xml->Item->Quantity
                    ), array(
                        'al_id' => (int) $actionlog,
                        'e_id' => (int) $id,
                        'ebay_id' => (int) $itemid
                    ));
                }
            }
        }
    }
    function GetOnlineListings($page = 1, $cat = false)
    {
        exit();
        $this->Auth_model->CheckListings();
        set_time_limit(1500);
        require_once($this->config->config['ebaypath'] . 'get-common/keys.php');
        require_once($this->config->config['ebaypath'] . 'get-common/eBaySession.php');
        $requestXmlBody = '<?xml version="1.0" encoding="utf-8" ?>';
        $requestXmlBody .= '<GetSellerListRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        $requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
        $requestXmlBody .= '<DetailLevel>ItemReturnAttributes</DetailLevel>';
        $requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
        $requestXmlBody .= "<Version>$compatabilityLevel</Version>";
        //http://developer.ebay.com/DevZone/XML/docs/Reference/eBay/types/GranularityLevelCodeType.html
        $dates = array(
            'from' => date('Y-m-d H:i:s', strtotime("-30 days")),
            'to' => date("Y-m-d H:i:s")
        );
        if ($cat)
            $requestXmlBody .= '<CategoryID>' . (int) $cat . '</CategoryID>';
        $requestXmlBody .= '

		<GranularityLevel>Coarse</GranularityLevel>

		<StartTimeFrom>' . $dates['from'] . '</StartTimeFrom>

		<StartTimeTo>' . $dates['to'] . '</StartTimeTo>

		<Pagination>

		<EntriesPerPage>200</EntriesPerPage>

		<PageNumber>' . (int) $page . '</PageNumber>

		</Pagination>

		</GetSellerListRequest>';
        $verb        = 'GetSellerList';
        //Create a new eBay session with all details pulled in from included keys.php
        $session     = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
        //send the request and get response
        $responseXml = $session->sendHttpRequest($requestXmlBody);
        if (stristr($responseXml, 'HTTP 404') || $responseXml == '')
            die('<P>Error sending request');
        $xml       = simplexml_load_string($responseXml);
        $pagearray = false;
        for ($counter = 1; $counter <= $xml->PaginationResult->TotalNumberOfPages; $counter++)
            $pagearray[] = $counter;
        $this->mysmarty->assign('page', (int) $page);
        $this->mysmarty->assign('pages', $pagearray);
        $this->mysmarty->assign('total', $xml->PaginationResult->TotalNumberOfEntries);
        //printcool($xml->ItemArray->Item);
        $this->mysmarty->assign('cat', $cat);
        $this->mysmarty->assign('dates', $dates);
        //printcool ($xml->ItemArray->Item);
        $requestXmlBody = '<?xml version="1.0" encoding="utf-8" ?>';
        $requestXmlBody .= '<GetStoreRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        $requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
        $requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
        $requestXmlBody .= "<Version>$compatabilityLevel</Version>";
        $requestXmlBody .= '<CategoryStructureOnly>TRUE</CategoryStructureOnly>

		<UserID>' . $ebayuserid . '</UserID></GetStoreRequest>';
        $verb        = 'GetStore';
        //Create a new eBay session with all details pulled in from included keys.php
        $session     = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
        //send the request and get response
        $responseXml = $session->sendHttpRequest($requestXmlBody);
        if (stristr($responseXml, 'HTTP 404') || $responseXml == '')
            die('<P>Error sending request');
        $sxml = simplexml_load_string($responseXml);
        $sc   = array();
        if (isset($sxml->Store->CustomCategories->CustomCategory))
        {
            foreach ($sxml->Store->CustomCategories->CustomCategory as $s)
            {
                $a                    = (array) $s;
                $sc[$a['CategoryID']] = $a['Name'];
            }
        }
        $this->mysmarty->assign('store', $sc);
        $k = 1;
        foreach ($xml->ItemArray->Item as $i)
        {
            $l[$k]           = $i;
            $l[$k]->storecat = $sc[(int) $i->Storefront->StoreCategoryID];
            $k++;
        }
        $this->mysmarty->assign('list', $l);
        $this->mysmarty->view('myebay/myebay_onlinelistings.html');
    }
    function SortOrders($type = '', $spec = '')
    {
        $this->Auth_model->CheckOrders();
        $this->sortstring = $type;
        $this->spectype   = false;
        $this->sorttype   = false;
        switch ($type)
        {
            case 'Ebay':
                $this->sorttype = 2;
                break;
            case 'EbayNotPaid':
                $this->sorttype = 21;
                break;
            case 'EbayRefunded':
                $this->sorttype = 22;
                break;
            case 'EbayPartialRefund':
                $this->sorttype = 25;
                break;
            case 'EbayPendingPay':
                $this->sorttype = 23;
                break;
            case 'NeedAttention':
                $this->sorttype = 24;
                break;
            case 'Site':
                $this->sorttype = 3;
                break;
            case 'Paid':
                $this->sorttype = 4;
                break;
            case 'Processed':
                $this->sorttype = 5;
                break;
            case 'NoProcessed':
                $this->sorttype = 6;
                break;
            case 'Asc':
                $this->sorttype = 7;
                break;
            case 'NoAsc':
                $this->sorttype = 8;
                break;
            case 'NoPaid':
                $this->sorttype = 9;
                break;
            case 'Ware':
                $this->sorttype = 10;
                break;
            default:
                $this->sorttype   = 1;
                $this->sortstring = 'All';
        }
        switch ($spec)
        {
            case 'NotPaid':
                $this->spectype = 1;
                break;
            case 'NotShipped':
                $this->spectype = 2;
                break;
            case 'Sold':
                $this->spectype = 3;
                break;
            case 'Fraud':
                $this->spectype = 4;
                break;
        }
        $this->mysmarty->assign('sorttype', $this->sorttype);
        if (isset($this->spectype))
            $this->mysmarty->assign('spectype', $this->spectype);
        $this->mysmarty->assign('sortstring', $this->sortstring);
        $this->session->set_userdata('sorttype', $this->sorttype);
        $this->session->set_userdata('sortstring', $this->sortstring);
        $this->GetOrders();
    }
    function GetOrders($highlight = '')
    {
        $this->mysmarty->assign('noenter', '

<script type="text/javascript"> 



function stopRKey(evt) { 

  var evt = (evt) ? evt : ((event) ? event : null); 

  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null); 

  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;} 

} 



document.onkeypress = stopRKey; 

</script> <script type="text/javascript" src="/js/warehouse.js"></script>

');
        $this->Auth_model->CheckOrders();
        $this->mysmarty->assign('floatmenu', TRUE);
        $this->load->model('Myseller_model');
        $this->Myseller_model->assignstatuses();
        $this->mysmarty->assign('cal', TRUE);
        $tdf   = 46800;
        $ofrom = mktime() + $tdf;
        $oto   = (mktime() + $tdf) - 864000;
        $dfrom = date('m/j/Y');
        $dto   = date('m/j/Y', strtotime("-15 days"));
        $this->mysmarty->assign('d1from', date('m/j/Y'));
        $this->mysmarty->assign('d1to', date('m/j/Y', strtotime("-30 days")));
        $this->mysmarty->assign('d2from', date('m/j/Y'));
        $this->mysmarty->assign('d2to', date('m/j/Y', strtotime("-60 days")));
        $this->mysmarty->assign('d3from', date('m/j/Y'));
        $this->mysmarty->assign('d3to', date('m/j/Y', strtotime("-90 days")));
        //$sesfrom = $this->session->userdata('dfrom');
        //$sesto = $this->session->userdata('dto');
        $sesfrom = false;
        $sesto   = false;
        if (!$sesfrom && !$sesto)
        {
            $sesfrom = $this->session->userdata('navfrom');
            $this->session->set_userdata('dfrom', $sesfrom);
            $sesto = $this->session->userdata('navto');
            $this->session->set_userdata('dto', $sesto);
            $nav = true;
        }
        if (($sesfrom || $sesto) && !isset($nav))
            $this->mysmarty->assign('dateclean', TRUE);
        if (isset($_POST['ofrom']) || $sesfrom)
        {
            if (isset($_POST['ofrom']))
            {
                $dfrom = trim($_POST['ofrom']);
                $this->session->set_userdata('dfrom', $dfrom);
            }
            else
                $dfrom = $sesfrom;
            $postfrom = explode('/', $dfrom);
            $ofrom    = mktime(23, 59, 59, $postfrom[0], $postfrom[1], $postfrom[2]) + $tdf;
            $this->mysmarty->assign('dateclean', TRUE);
        }
        if (isset($_POST['oto']) || $sesto)
        {
            if (isset($_POST['oto']))
            {
                $dto = trim($_POST['oto']);
                $this->session->set_userdata('dto', $dto);
            }
            else
                $dto = $sesto;
            $postto = explode('/', $dto);
            $oto    = mktime(0, 0, 0, $postto[0], $postto[1], $postto[2]) + $tdf;
            $this->mysmarty->assign('dateclean', TRUE);
        }
        $this->mysmarty->assign('dfrom', $dfrom);
        $this->mysmarty->assign('dto', $dto);
        if (!isset($this->sorttype))
        {
            $this->session->set_userdata('sortstring', FALSE);
            $this->sorttype = 0;
        }
        if (!isset($this->spectype))
        {
            $this->spectype = 0;
        }
        $oldtrestentry = mktime() + $tdf;
        $oldorestentry = mktime() + $tdf;
        $list          = array();
        $orders        = array();
        $this->load->model('Myorders_model');
        if ($this->sorttype != 3 && $this->sorttype != 10)
        {
            $this->mysmarty->assign('area', 'Transactions');
            $list = array();
            if (isset($this->sorttype) && $this->sorttype == 24)
            {
                $msql = 'SELECT distinct e.et_id FROM (ebay_transactions e) LEFT JOIN warehouse w ON e.et_id = w.sold_id WHERE (`e`.`notpaid` != 0 OR `e`.`refunded` != 0 OR `e`.`returnnotif` IS NOT NULL) AND `e`.`mkdt` <= ' . $ofrom . ' AND `e`.`mkdt` >= ' . $oto . ' AND `w`.`deleted` = 0  AND `w`.`nr` = 0 AND `w`.`vended` > 0 AND `w`.`sold_id` != 0';
                $q    = $this->db->query($msql);
                if ($q->num_rows() > 0)
                {
                    foreach ($q->result_array() as $r)
                    {
                        $etids[] = $r['et_id'];

                    }
                }
            }
            ///
            $s1 = "distinct t.*, e_part, e_title, idpath, e_img1";
            $this->db->select($s1, false);
            ////
            //$this->db->where('t.mkdt <= ', $ofrom);
            //$this->db->where('t.mkdt >= ', $oto);
            if (isset($_POST['osrc']))
            {
                //$msql .= 'AND (`e`.`buyerid` = '.$this->input->post('osrc', TRUE).' OR `e`.`buyeremail` = '.$this->input->post('osrc', TRUE).' OR `e`.`buyerid` = '.$this->input->post('notes', TRUE).' )';
                $this->db->where("(`t`.`buyerid` LIKE '%" . $this->input->post('osrc', TRUE) . "%' OR `t`.`buyeremail` LIKE '%" . $this->input->post('osrc', TRUE) . "%' OR `t`.`notes` LIKE '%" . $this->input->post('osrc', TRUE) . "%' OR `t`.`et_id` = '" . trim($this->input->post('osrc', TRUE)) . "' )", null, false);
                $this->mysmarty->assign('osrc', $this->input->post('osrc', TRUE));
            }
            else
            {
                $this->db->where('t.mkdt <= ', $ofrom);
                $this->db->where('t.mkdt >= ', $oto);
            }
            if (isset($this->sorttype))
            {
                switch ($this->sorttype)
                {
                    case 4:
                        $this->db->where('t.paidtime !=', '');
                        break;
                    case 9:
                        $this->db->where('t.paidtime', '');
                        $this->db->where('t.notpaid', 0);
                        $this->db->where('t.refunded', 0);
                        break;
                    case 5:
                        $this->db->where('t.mark !=', 0);
                        break;
                    case 6:
                        $this->db->where('t.mark', 0);
                        break;
                    case 7:
                        $this->db->where('t.cascupd !=', 0);
                        break;
                    case 8:
                        $this->db->where('t.cascupd', 0);
                        break;
                    case 21:
                        $this->db->where('t.notpaid', 1);
                        break;
                    case 22:
                        $this->db->where("(customcode = 1 OR refunded = 1)", null, false);
                        $this->db->where('sellingstatus != ', 'PartiallyPaid');
                        //$this->db->where("(paid = '' OR paid = '0.0')",null, false);
                        break;
                    case 25:
                        $this->db->where("(customcode = 1 OR refunded = 1)", null, false);
                        $this->db->where('sellingstatus', 'PartiallyPaid');
                        //$this->db->where("(paid != '' OR paid != '0.0')",null, false);
                        //$this->db->where("(t.customcode = 1 OR t.refunded = 1)",null, false);
                        break;
                    case 23:
                        $this->db->where('t.paidtime', '');
                        $this->db->where('t.notpaid', 0);
                        $this->db->where('t.refunded', 0);
                        break;
                    case 24:
                        if (isset($etids))
                        {
                            $where = 't`.`e_id` >= 0 AND (';
                            foreach ($etids as $e)
                                $where .= "`t`.`et_id` = " . $e . ' OR ';
                            $where = rtrim($where, ' OR ');
                            $where .= ')';
                            $this->db->where($where);
                        }
                        break;
                }
            }
            if (isset($this->spectype))
            {
                switch ($this->spectype)
                {
                    case 1:
                        $this->db->where('t.paidtime', '');
                        break;
                    case 2:
                        $this->db->where('t.paidtime !=', '');
                        $this->db->where('t.mark', 0);
                        $this->db->where('t.notpaid', 0);
                        $this->db->where("t.refunded", 0);
                        break;
                    case 3:
                        $this->db->where('t.paidtime !=', '');
                        $this->db->where('t.mark !=', 0);
                        break;
                }
            }
            //$this->db->limit(500);
            if (isset($this->orderid) && isset($this->orderchannel) && $this->orderchannel == 1)
            {
                $this->db->_reset_select();
                $this->db->select($s1, false);
                $this->db->where('et_id', $this->orderid);
            }
            elseif (isset($this->listingid))
            {
                $this->db->_reset_select();
                $this->db->select($s1, false);
                $this->db->where('t.e_id', $this->listingid);
            }
            $this->db->order_by("rec", "DESC");
            $this->db->join('ebay e', 't.e_id = e.e_id', 'LEFT');
            $q            = $this->db->get('ebay_transactions t');
            $mkdtdupcheck = 0;
            if ($q->num_rows() > 0 && ((isset($this->orderchannel) && $this->orderchannel == 1) || !isset($this->orderchannel)))
            {
                foreach ($q->result_array() as $k => $v)
                {
                    if ((int) $v['mkdt'] == (int) $mkdtdupcheck)
                        $v['mkdt'] = $v['mkdt'] - 1;
                    $mkdtdupcheck = $v['mkdt'];
                    if ($v['mkdt'] < $oldtrestentry)
                        $oldtrestentry = $v['mkdt'];
                    if (strlen($v['paydata']) > 10)
                    {
                        $v['paydata'] = unserialize($v['paydata']);
                        if (isset($v['paydata']))
                            unset($v['paydata']['PaidTime']);
                    }
                    else
                        $v['paydata'] = false;
                    $list[$v['mkdt'] . 'E']    = $v;
                    $idarray[]                 = $v['et_id'];
                    $ridarray[$v['return_id']] = $v['return_id'];
                    $listings[$v['e_id']]      = TRUE;
                }
                if (isset($idarray))
                {
                    $this->load->model('Myseller_model');
                    $this->Myseller_model->getSales($idarray, 1);
                    $this->Myseller_model->getReturns($ridarray, 1);
                    unset($ridarray);
                    unset($idarray);
                }
            }
        }
        if ($this->sorttype != 2 && $this->sorttype != 7 && $this->sorttype != 8 && $this->sorttype != 10 && $this->sorttype != 21 && $this->sorttype != 22 && $this->sorttype != 23 && $this->sorttype != 24 && $this->sorttype != 25)
        {
            if (isset($this->sorttype))
            {
                switch ($this->sorttype)
                {
                    case 4:
                        $this->db->where('complete', 1);
                        break;
                    case 9:
                        $this->db->where('complete !=', 1);
                        $this->db->where('complete !=', "-1");
                        break;
                    case 5:
                        $this->db->where('mark !=', 0);
                        break;
                    case 6:
                        $this->db->where('mark', 0);
                        break;
                }
            }
            if (isset($this->spectype))
            {
                switch ($this->spectype)
                {
                    case 1:
                        $this->db->where('complete !=', 1);
                        $this->db->where('complete !=', "-1");
                        break;
                    case 2:
                        $this->db->where('complete', 1);
                        $this->db->where('mark', 0);
                        break;
                    case 3:
                        $this->db->where('complete', 1);
                        $this->db->where('mark !=', 0);
                        break;
                    case 4:
                        $this->db->where('complete <', 0);
                        break;
                }
            }
            if (isset($_POST['osrc']))
            {
                //$msql .= 'AND (`e`.`buyerid` = '.$this->input->post('osrc', TRUE).' OR `e`.`buyeremail` = '.$this->input->post('osrc', TRUE).' OR `e`.`buyerid` = '.$this->input->post('notes', TRUE).' )';
                $this->db->where("(`fname` LIKE '%" . $this->input->post('osrc', TRUE) . "%' OR `lname` LIKE '%" . $this->input->post('osrc', TRUE) . "%' OR `email` LIKE '%" . $this->input->post('osrc', TRUE) . "%' OR `comments` LIKE '%" . $this->input->post('osrc', TRUE) . "%' OR `staffcomments` LIKE '%" . $this->input->post('osrc', TRUE) . "%'  OR `oid` = '" . trim($this->input->post('osrc', TRUE)) . "' )", null, false);
                $this->mysmarty->assign('osrc', $this->input->post('osrc', TRUE));
            }
            else
            {
                $this->db->where('submittime <= ', $ofrom);
                $this->db->where('submittime >= ', $oto);
            }
            if (isset($this->orderid) && isset($this->orderchannel) && $this->orderchannel == 2)
            {
                $this->db->_reset_select();
                $this->db->where('oid', $this->orderid);
            }
            elseif (isset($this->listingid))
            {
                $this->db->_reset_select();
                $this->db->like('eids', '|' . (int) $this->listingid . '|');
            }
            $this->db->order_by("submittime", "DESC");
            $this->query = $this->db->get('orders');
            $orders      = array();
            if ($this->query->num_rows() > 0 && ((isset($this->orderchannel) && $this->orderchannel == 2) || !isset($this->orderchannel)))
            {
                $nowmk = (int) mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                $os    = array();
                foreach ($this->query->result_array() as $k => $v)
                {
                    if ($v['status'] != '' && $v['status'] != ' ')
                    {
                        $v['status'] = unserialize($v['status']);
                        //$v['origstatus'] = $v['status'][0];
                        $v['status'] = end($v['status']);
                    }
                    if (strlen($v['order']) > 9)
                    {
                        $v['order'] = unserialize($v['order']);
                        if (is_array($v['order']))
                            foreach ($v['order'] as $k => $ov)
                            {
                                $os[$ov['e_id']] = $ov['quantity'];
                                if (!isset($ov['sn']))
                                    $v['order'][$k]['sn'] = '';
                                if (!isset($ov['admin']))
                                    $v['order'][$k]['admin'] = '';
                                $listings[$ov['e_id']] = TRUE;
                            }
                    }
                    if (strlen($v['CheckoutStatus']) > 9)
                        $v['CheckoutStatus'] = unserialize($v['CheckoutStatus']);
                    $v['mktime'] = explode(' ', $v['time']);
                    $v['mktime'] = explode('-', $v['mktime'][0]);
                    if (isset($v['mktime'][1]) && isset($v['mktime'][2]) && isset($v['mktime'][0]))
                        $v['mktime'] = (int) mktime(0, 0, 0, $v['mktime'][1], $v['mktime'][2], $v['mktime'][0]);
                    else
                        $v['mktime'] = false;
                    if ($v['submittime'] < $oldorestentry)
                        $oldorestentry = $v['submittime'];
                    $orders[$v['submittime'] . 'O'] = $v;
                    $idarray[]                      = $v['oid'];
                    $ridarray[$v['return_id']]      = $v['return_id'];
                }
                if (isset($idarray))
                {
                    $this->load->model('Myseller_model');
                    $this->Myseller_model->getSales($idarray, 2);
                    $this->Myseller_model->getReturns($ridarray, 2);
                    unset($idarray);
                    unset($ridarray);
                }
            }
        }
        if ($this->sorttype != 2 && $this->sorttype != 3 && $this->sorttype != 6 && $this->sorttype != 7 && $this->sorttype != 8 && $this->sorttype != 9 && $this->sorttype != 21 && $this->sorttype != 22 && $this->sorttype != 23 && $this->sorttype != 24 && $this->sorttype != 25)
        {
            if (isset($this->listingid))
            {
                $oq            = $this->db->query("SELECT `sold_id`, `listingid` FROM warehouse WHERE `channel` = 4 AND `listingid` = '" . (int) $this->listingid . "' AND `vended` != 0");
                $this->orderid = array();
                if ($oq->num_rows() > 0)
                {
                    foreach ($oq->result_array() as $orq)
                    {
                        $this->orderid[$orq['sold_id']] = $orq['sold_id'];
                    }
                }
            }
            else
            {
                if (!isset($_POST['osrc']) && !isset($this->orderid))
                {
                    $this->db->where('timemk <= ', $ofrom);
                    $this->db->where('timemk >= ', $oto);
                }
            }
            if (isset($_POST['osrc']))
            {
                //$msql .= 'AND (`e`.`buyerid` = '.$this->input->post('osrc', TRUE).' OR `e`.`buyeremail` = '.$this->input->post('osrc', TRUE).' OR `e`.`buyerid` = '.$this->input->post('notes', TRUE).' )';
                $this->db->where("(`buyer` LIKE '%" . $this->input->post('osrc', TRUE) . "%' OR `notes` LIKE '%" . $this->input->post('osrc', TRUE) . "%' OR `woid` = '" . trim($this->input->post('osrc', TRUE)) . "' )", null, false);
                $this->mysmarty->assign('osrc', $this->input->post('osrc', TRUE));
            }
            if (isset($this->orderid) && isset($this->orderchannel) && $this->orderchannel == 4)
            {
                $this->db->where('woid', $this->orderid);
            }
            elseif (isset($this->listingid) && is_array($this->orderid) && count($this->orderid) > 0)
            {
                $this->db->_reset_select();
                $oidcnt = 1;
                foreach ($this->orderid as $oid)
                {
                    if ($oidcnt == 1)
                        $this->db->where('woid', $oid);
                    else
                        $this->db->or_where('woid', $oid);
                    $oidcnt++;
                }
            }
            elseif (isset($this->listingid) && is_array($this->orderid) && count($this->orderid) == 0)
                $nogo = true;
            $this->db->order_by("timemk", "DESC");
            $this->wquery = $this->db->get('warehouse_orders');
            unset($idarray);
            if ($this->wquery->num_rows() > 0 && !isset($nogo) && ((isset($this->orderchannel) && $this->orderchannel == 4) || !isset($this->orderchannel)))
            {
                foreach ($this->wquery->result_array() as $k => $v)
                {
                    $worders[$v['woid']]       = $v;
                    $idarray[]                 = $v['woid'];
                    $ridarray[$v['return_id']] = $v['return_id'];
                }
                $this->load->model('Myseller_model');
                $this->Myseller_model->getSales($idarray, 4);
                $this->Myseller_model->getReturns($ridarray, 4);
                unset($idarray);
                unset($ridarray);
                foreach ($worders as $w)
                {
                    $orders[$w['timemk'] . 'W'] = $w;
                }
                unset($worders);
                $this->mysmarty->assign('hot', TRUE);
            }
        }
        $olist = array_merge($list, $orders);
        krsort($olist);
        if (isset($os) && count($os) > 0)
        {
            $this->db->select("e_part, e_qpart, e_id, quantity, ebayquantity, ebay_id");
            $st = 0;
            foreach ($os as $k => $v)
            {
                if ($st == 0)
                {
                    $this->db->where('e_id', $k);
                    $st++;
                }
                else
                    $this->db->or_where('e_id', $k);
            }
            $q   = $this->db->get('ebay');
            $ebl = false;
            if ($q->num_rows() > 0)
            {
                foreach ($q->result_array() as $k => $v)
                {
                    $ebl[$v['e_id']] = $v;
                }
            }
        }
        $this->mysmarty->assign('ebl', $ebl);
        //printcool ($olist);
        //break;
        if (isset($this->sortstring))
            $this->mysmarty->assign('sortstring', $this->sortstring);
        if (isset($this->specstring))
            $this->mysmarty->assign('specstring', $this->specstring);
        if (isset($this->sortpage))
            $this->mysmarty->assign('sortpage', $this->sortpage);
        $this->mysmarty->assign('list', $olist);
        $this->mysmarty->assign('highlight', $highlight);
        if ($oldtrestentry > $oldorestentry)
            $this->session->set_userdata('next', $oldtrestentry);
        else
            $this->session->set_userdata('next', $oldorestentry);
        $this->mysmarty->assign('statuses', $this->Myorders_model->GetStatuses());
        if (isset($listings))
        {
            $this->load->model('Myseller_model');
            $this->Myseller_model->getSalesListings($listings);
            unset($listings);
        }
        $this->mysmarty->view('myebay/myebay_orders.html');
    }
    function OrderDatesClean()
    {
        $this->session->unset_userdata('dfrom');
        $this->session->unset_userdata('dto');
        header('location: ' . $_SERVER['HTTP_REFERER']);
    }
    function dumptransaction($trid)
    {
        require($this->config->config['ebaypath'] . 'get-common/keys.php');
        require_once($this->config->config['ebaypath'] . 'get-common/eBaySession.php');
        /*
        
        $requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
        
        $requestXmlBody .= '<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        
        $requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
        
        $requestXmlBody .= '<DetailLevel>ItemReturnAttributes</DetailLevel>';
        
        $requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
        
        $requestXmlBody .= "<Version>$compatabilityLevel</Version>";
        
        $requestXmlBody .= '<ItemID>'.(int)$trid.'</ItemID></GetItemRequest>';
        
        $verb = 'GetItem';
        
        $session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
        
        $responseXml = $session->sendHttpRequest($requestXmlBody);
        
        if(stristr($responseXml, 'HTTP 404') || $responseXml == '') die('<P>Error sending request');
        
        $xml = simplexml_load_string($responseXml);
        
        printcool($xml->Item);
        
        */
    }
    function OrdersListing($listing = '')
    {
        $this->listingid = (int) $listing;
        $this->Orders();
    }
    function Orders($page = 1, $perpage = 100, $filtertype = false, $filtersubtype = false)
    {
        $this->load->model('Myorders_model');
        if (isset($this->listingid))
            $lid = $this->listingid;
        else
            $lid = false;
        $this->Myorders_model->GetOrders((int) $page, (int) $perpage, $filtertype, $filtersubtype, false, $lid);
        exit();
        //printcool($_POST);
        /*
        
        if (trim($filtertype) == 'false') $filtertype = false;
        
        $channel[1] = TRUE;
        
        $channel[2] = TRUE;
        
        $channel[4] = TRUE;
        
        $this->sortstring = $type;
        
        $this->spectype = false;
        
        $this->sorttype = false;
        
        $psql[1] = '';
        
        $psql[2] = '';
        
        $psql[4] = '';
        
        $timeframe = TRUE;
        
        
        
        
        
        if ($filtertype && !isset($_POST['osrc']) && !isset($this->listingid)) switch ($filtertype)
        
        {
        
        //CHANNEL1 = EBAY
        
        //CHANNEL2 = WEB
        
        //CHANNEL4 = WARE
        
        case 'Ebay':
        
        $this->sorttype = 2;
        
        $channel[2] = FALSE;
        
        $channel[4] = FALSE;
        
        break;
        
        case 'EbayNotPaid':
        
        $this->sorttype = 21;
        
        $psql[1] .=  "AND notpaid = 1 ";
        
        $channel[2] = FALSE;
        
        $channel[4] = FALSE;
        
        break;
        
        case 'EbayRefunded':
        
        $this->sorttype = 22;
        
        $psql[1] .=  "AND (customcode = 1 OR refunded = 1) AND sellingstatus != 'PartiallyPaid' ";
        
        $channel[2] = FALSE;
        
        $channel[4] = FALSE;
        
        break;
        
        case 'EbayPartialRefund':
        
        $this->sorttype = 25;
        
        $psql[1] .=  "AND (customcode = 1 OR refunded = 1)AND sellingstatus = 'PartiallyPaid' ";
        
        $channel[2] = FALSE;
        
        $channel[4] = FALSE;
        
        break;
        
        case 'EbayPendingPay':
        
        $this->sorttype = 23;
        
        $psql[1] .=  "AND paidtime = '' AND notpaid = 0 AND refunded = 0 ";
        
        $channel[2] = FALSE;
        
        $channel[4] = FALSE;
        
        break;
        
        case 'NeedAttention':
        
        $this->sorttype = 24;
        
        $channel[2] = FALSE;
        
        $channel[4] = FALSE;
        
        break;
        
        case 'Site':
        
        $this->sorttype = 3;
        
        $channel[1] = FALSE;
        
        $channel[4] = FALSE;
        
        break;
        
        case 'Paid':
        
        $this->sorttype = 4;
        
        $psql[1] .=  "AND paidtime != '' ";
        
        $psql[2] .=  "AND complete = 1 ";
        
        $channel[4] = FALSE;
        
        break;
        
        case 'Processed':
        
        $this->sorttype = 5;
        
        $psql[1] .=  "AND mark != 0 ";
        
        $psql[2] .=  "AND mark != 0 ";
        
        $channel[4] = FALSE;
        
        break;
        
        case 'NoProcessed':
        
        $this->sorttype = 6;
        
        $psql[1] .=  "AND mark = 0 ";
        
        $psql[2] .=  "AND mark = 0 ";
        
        $channel[4] = FALSE;
        
        break;
        
        case 'Asc':
        
        $this->sorttype = 7;
        
        $psql[1] .=  "AND cascupd != 0 ";
        
        $channel[2] = FALSE;
        
        $channel[4] = FALSE;
        
        break;
        
        case 'NoAsc':
        
        $this->sorttype = 8;
        
        $psql[1] .=  "AND cascupd = 0 ";
        
        $channel[2] = FALSE;
        
        $channel[4] = FALSE;
        
        break;
        
        case 'NoPaid':
        
        $this->sorttype = 9;
        
        $psql[1] .=  "AND paidtime = '' AND notpaid = 0 AND refunded = 0 ";
        
        $psql[2] .=  "AND ( complete != 1 AND complete != '-1') ";
        
        $channel[4] = FALSE;
        
        break;
        
        case 'Ware':
        
        $this->sorttype = 10;
        
        $channel[1] = FALSE;
        
        $channel[2] = FALSE;
        
        break;
        
        default:
        
        $this->sorttype = 1;
        
        $this->sortstring = 'All';
        
        }
        
        if ($filtersubtype && !isset($_POST['osrc']) && !isset($this->listingid)) switch ($filtersubtype)
        
        {
        
        case 'NotPaid':
        
        $this->spectype = 1;
        
        $psql[1] .=  "AND paidtime = '' ";
        
        $psql[2] .=  "AND ( complete != 1 AND complete != '-1' ) ";
        
        $psql[4] .=  "";
        
        break;
        
        case 'NotShipped':
        
        $this->spectype = 2;
        
        $psql[1] .=  "AND paidtime != '' AND mark = 0 AND notpaid = 0 AND refunded = 0 ";
        
        $psql[2] .=  "AND complete = 1 AND mark = 0";
        
        $psql[4] .=  "";
        
        break;
        
        case 'Sold':
        
        $this->spectype = 3;
        
        $psql[1] .=  "AND paidtime != '' AND mark != 0 ";
        
        $psql[2] .=  "AND complete = 1 AND mark != 0 ";
        
        $psql[4] .=  "";
        
        break;
        
        case 'Fraud':
        
        $this->spectype = 4;
        
        $psql[1] .=  "";
        
        $psql[2] .=  "AND complete < 0 ";
        
        $psql[4] .=  "";
        
        $channel[1] = FALSE;
        
        $channel[4] = FALSE;
        
        break;
        
        }
        
        
        
        
        
        $this->mysmarty->assign('sorttype', $this->sorttyrlpe);
        
        if (isset($this->spectype)) $this->mysmarty->assign('spectype', $this->spectype);
        
        $this->mysmarty->assign('sortstring', $this->sortstring);
        
        
        
        if( !isset($_POST['osrc']) && !isset($this->listingid))
        
        {
        
        if (isset($this->sorttype) && $this->sorttype == 2 && isset($this->spectype) && $this->spectype == 2)$this->mysmarty->assign("gotourl", 'Orders/'.(int)$page.'/'.(int)$perpage.'/NotShipped');
        
        elseif (isset($this->sorttype) && $this->sorttype == 2)$this->mysmarty->assign("gotourl", 'Orders/'.(int)$page.'/'.(int)$perpage.'/Ebay');
        
        elseif (isset($this->sorttype) && $this->sorttype == 23)$this->mysmarty->assign("gotourl", 'Orders/'.(int)$page.'/'.(int)$perpage.'EbayPendingPay');
        
        elseif (isset($this->sorttype) && $this->sorttype == 21)$this->mysmarty->assign("gotourl", 'Orders/'.(int)$page.'/'.(int)$perpage.'/EbayNotPaid');
        
        elseif (isset($this->sorttype) && $this->sorttype == 25)$this->mysmarty->assign("gotourl", 'Orders/'.(int)$page.'/'.(int)$perpage.'/EbayPartialRefund');
        
        elseif (isset($this->sorttype) && $this->sorttype == 24)$this->mysmarty->assign("gotourl", 'Orders/'.(int)$page.'/'.(int)$perpage.'/NeedAttention');
        
        elseif (isset($this->sorttype) && $this->sorttype == 3)$this->mysmarty->assign("gotourl", 'Orders/'.(int)$page.'/'.(int)$perpage.'/Site');
        
        elseif (isset($this->sorttype) && $this->sorttype == 10)$this->mysmarty->assign("gotourl", 'Orders/'.(int)$page.'/'.(int)$perpage.'/Ware');
        
        elseif (isset($this->sorttype) && $this->sorttype == 4)$this->mysmarty->assign("gotourl", 'Orders/'.(int)$page.'/'.(int)$perpage.'/Paid');
        
        elseif (isset($this->sorttype) && $this->sorttype == 9)$this->mysmarty->assign("gotourl", 'Orders/'.(int)$page.'/'.(int)$perpage.'/NoPaid');
        
        elseif (isset($this->sorttype) && $this->sorttype == 5)$this->mysmarty->assign("gotourl", 'Orders/'.(int)$page.'/'.(int)$perpage.'/Processed');
        
        elseif (isset($this->sorttype) && $this->sorttype == 6)$this->mysmarty->assign("gotourl", 'Orders/'.(int)$page.'/'.(int)$perpage.'/NoProcessed');
        
        elseif (isset($this->sorttype) && $this->sorttype == 7)$this->mysmarty->assign("gotourl", 'Orders/'.(int)$page.'/'.(int)$perpage.'/Asc');
        
        elseif (isset($this->sorttype) && $this->sorttype == 8)$this->mysmarty->assign("gotourl", 'Orders/'.(int)$page.'/'.(int)$perpage.'/NoAsc');
        
        else $this->mysmarty->assign("gotourl", 'Orders');
        
        }
        
        $this->session->set_userdata('sorttype', $this->sorttype);
        
        $this->session->set_userdata('sortstring', $this->sortstring);
        
        
        
        
        
        
        
        
        
        $this->mysmarty->assign('noenter', '
        
        <script type="text/javascript">
        
        
        
        function stopRKey(evt) {
        
        var evt = (evt) ? evt : ((event) ? event : null);
        
        var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
        
        if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
        
        }
        
        
        
        document.onkeypress = stopRKey;
        
        </script> <script type="text/javascript" src="/js/warehouse.js"></script>
        
        ');
        
        $this->Auth_model->CheckOrders();
        
        $this->mysmarty->assign('floatmenu', TRUE);
        
        $this->load->model('Myseller_model');
        
        $this->Myseller_model->assignstatuses();
        
        
        
        $this->mysmarty->assign('cal', TRUE);
        
        $tdf =46800;
        
        $ofrom = mktime()+$tdf;
        
        $oto = (mktime()+$tdf)-864000;
        
        $dfrom = date('m/j/Y');
        
        $dto = date('m/j/Y', strtotime("15 days"));
        
        
        
        
        
        $this->mysmarty->assign('d1from', date('m/j/Y'));
        
        $this->mysmarty->assign('d1to', date('m/j/Y', strtotime("-30 days")));
        
        
        
        $this->mysmarty->assign('d2from', date('m/j/Y'));
        
        $this->mysmarty->assign('d2to', date('m/j/Y', strtotime("-60 days")));
        
        
        
        $this->mysmarty->assign('d3from', date('m/j/Y'));
        
        $this->mysmarty->assign('d3to', date('m/j/Y', strtotime("-90 days")));
        
        
        
        //$sesfrom = $this->session->userdata('dfrom');
        
        //$sesto = $this->session->userdata('dto');
        
        $sesfrom = false;
        
        $sesto = false;
        
        
        
        if (!$sesfrom && !$sesto)
        
        {
        
        $sesfrom = $this->session->userdata('dfrom');
        
        $this->session->set_userdata('dfrom', $sesfrom);
        
        $sesto = $this->session->userdata('dto');
        
        $this->session->set_userdata('dto', $sesto);
        
        $nav = true;
        
        }
        
        
        
        if (($sesfrom || $sesto) && !isset($nav)) $this->mysmarty->assign('dateclean', TRUE);
        
        
        
        if (isset($_POST['ofrom']) || $sesfrom)
        
        {
        
        if (isset($_POST['ofrom']))
        
        {
        
        $dfrom = trim($_POST['ofrom']);
        
        $this->session->set_userdata('dfrom', $dfrom);
        
        }
        
        else $dfrom = $sesfrom;
        
        $postfrom = explode('/', $dfrom);
        
        $ofrom = mktime(23, 59, 59, $postfrom[0], $postfrom[1], $postfrom[2])+$tdf;
        
        $this->mysmarty->assign('dateclean', TRUE);
        
        }
        
        if (isset($_POST['oto']) || $sesto)
        
        {
        
        if (isset($_POST['oto']))
        
        {
        
        $dto = trim($_POST['oto']);
        
        $this->session->set_userdata('dto', $dto);
        
        }
        
        else $dto = $sesto;
        
        
        
        $postto = explode('/', $dto);
        
        $oto = mktime(0, 0, 0, $postto[0], $postto[1], $postto[2])+$tdf;
        
        $this->mysmarty->assign('dateclean', TRUE);
        
        }
        
        
        
        $this->mysmarty->assign('dfrom', $dfrom);
        
        $this->mysmarty->assign('dto', $dto);
        
        
        
        $this->mysmarty->assign('area', 'Transactions');
        
        
        
        
        
        ini_set('memory_limit','2048M');
        
        
        
        $this->mysmarty->assign('hot', TRUE);
        
        $csq = '';
        
        if ($channel[1])
        
        {
        
        
        
        $csql .= "SELECT DISTINCT e.et_id AS orderkey ";
        
        if (isset($this->listingid) ) $csql .= ' FROM (ebay_transactions e) WHERE e_id = "'.(int)$this->listingid.'" ';
        
        elseif(isset($_POST['osrc'])) $csql .= " FROM (ebay_transactions e) WHERE et_id = '".$this->input->post('osrc', TRUE)."' OR  buyerid LIKE '%".$this->input->post('osrc', TRUE)."%' OR buyeremail LIKE '%".$this->input->post('osrc', TRUE)."%' OR notes LIKE '%".$this->input->post('osrc', TRUE)."%' ";
        
        elseif (isset($this->sorttype) && $this->sorttype == 24)  $csql .= " FROM (ebay_transactions e) LEFT JOIN warehouse w ON e.et_id = w.sold_id WHERE (e.notpaid != 0 OR e.refunded != 0 OR e.returnnotif IS NOT NULL) AND w.vended > 0 AND w.deleted = 0 AND w.nr = 0 AND w.channel = 1 AND w.sold_id != 0";
        
        elseif ($timeframe) $csql .=  " FROM (ebay_transactions e) WHERE mkdt <= ".$ofrom." AND mkdt >= ".$oto." ";
        
        else $csql .=  " FROM (ebay_transactions e) ";
        
        
        
        $csql .=  $psql[1];
        
        //printcool($csql);
        
        if ($channel[4] ||$channel[2]) $csql .= " UNION ALL ";
        
        }
        
        if ($channel[4])
        
        {
        
        $csql .= " SELECT DISTINCT o.woid AS orderkey ";
        
        //$csql .= " ";
        
        if (isset($this->listingid))  $csql .= ' FROM (warehouse_orders o) LEFT JOIN warehouse w ON o.woid = w.sold_id WHERE w.vended > 0 AND w.deleted = 0 AND w.nr = 0 AND w.channel = 4 AND w.listingid = "'.(int)$this->listingid.'" ';
        
        elseif(isset($_POST['osrc'])) $csql .= " FROM (warehouse_orders o) WHERE woid = '".$this->input->post('osrc', TRUE)."'  OR buyer LIKE '%".$this->input->post('osrc', TRUE)."%' OR notes LIKE '%".$this->input->post('osrc', TRUE)."%' ";
        
        elseif ($timeframe) $csql .=  " FROM (warehouse_orders o) WHERE timemk <= ".$ofrom." AND timemk >= ".$oto." ";
        
        $csql .=  $psql[4];
        
        
        
        if ($channel[2] ||$channel[1] ) $csql .= " UNION ALL ";
        
        }
        
        if ($channel[2])
        
        {
        
        $csql .= "SELECT distinct oid  as orderkey ";
        
        // $csql .=  "";
        
        if (isset($this->listingid))  $csql .= ' FROM orders WHERE eids LIKE "%|'.(int)$this->listingid.'|%"';
        
        elseif(isset($_POST['osrc'])) $csql .= " FROM orders WHERE oid = '".$this->input->post('osrc', TRUE)."' OR fname LIKE '%".$this->input->post('osrc', TRUE)."%' OR lname LIKE '%".$this->input->post('osrc', TRUE)."%' OR email LIKE '%".$this->input->post('osrc', TRUE)."%' ";
        
        elseif ($timeframe) $csql .=  " FROM orders  WHERE submittime <= ".$ofrom." AND submittime >= ".$oto." ";
        
        $csql .=  $psql[2];
        
        }
        
        
        
        if(isset($_POST['osrc'])) $this->mysmarty->assign('osrc',$this->input->post('osrc', TRUE));
        
        else  $this->mysmarty->assign('osrc',false);
        
        
        
        //printcool($csql);
        
        $this->mysmarty->assign('sqldebug',$csql);
        
        $cn = $this->db->query($csql);
        
        $ordercount = $cn->num_rows();
        
        $this->mysmarty->assign('ordercount', $ordercount);
        
        
        
        if ((int)$perpage <= 0) $perpage = 100;
        
        
        
        
        
        $this->mysmarty->assign('page', $page);
        
        
        
        
        
        //if ((int)$page > 0) $page = $page - 1;
        
        $tolimit = (int)$page*(int)$perpage;
        
        $pages = ceil($ordercount/(int)$perpage);
        
        
        
        for ( $counter = 1; $counter <= $pages ; $counter++)
        
        {
        
        $before = 5;
        
        $after= 5;
        
        $min = (int)$page -$before;
        
        if ($min < 0) $after = $before - $min;
        
        $max = (int)$page +$after;
        
        if ($max > $pages) $before = $before + ($max-$pages);
        
        
        
        if ( ($counter >= ((int)$page -$before)) && ($counter <= ((int)$page +$after)) )
        
        {
        
        $pagearray[] = $counter;
        
        }
        
        
        
        }
        
        $this->mysmarty->assign('perpage', (int)$perpage);
        
        $this->mysmarty->assign('page', $page);
        
        $this->mysmarty->assign('pages', $pages);
        
        $this->mysmarty->assign('pagearray', $pagearray);
        
        $sql = '';
        
        if ($channel[1])
        
        {
        
        $sql .= "SELECT distinct e.et_id AS orderkey,e.mkdt as timekey, 'ebay' as typekey, '1' as channel,
        
        
        
        e.paidtime as field_timepaid,
        
        e.notpaid as field_notpaid,
        
        e.mark as field_mark,
        
        e.customcode as field_customcode,
        
        e.returned as field_returned,
        
        e.returned_refunded as field_refunded,
        
        e.sellingstatus as field_sellingstatus ,
        
        
        
        e.datetime	as	created,
        
        e.mkdt	as	createdmk,
        
        e.rec	as	outerkey,
        
        e.paid	as	paid,
        
        e.admin	as	admin,
        
        e.revs	as	revs,
        
        e.notes	as	notes,
        
        e.return_id	as	return_id,
        
        e.returned_notes	as	returned_notes,
        
        e.returned_time	as	returned_time,
        
        e.returned_recieved	as	returned_recieved,
        
        e.returned_amount	as	returned_amount,
        
        e.returned_extracost	as	returned_extracost,
        
        e.buyeremail	as	buyeremail,
        
        e.buyeraddress	as	buyeraddress,
        
        e.buyerid	as	buyerid,
        
        e.returntype	as	returntype,
        
        e.returnQuantity	as	returnQuantity,
        
        e.cascupd	as	cascupd,
        
        e.market	as	market,
        
        e.e_id	as	e_id,
        
        e.autoid	as	autoid,
        
        e.autotitle	as	autotitle,
        
        e.contorderid	as	contorderid,
        
        e.eachpaid	as	eachpaid,
        
        e.fee	as	fee,
        
        e.shipping	as	shipping,
        
        e.tracking	as	tracking,
        
        e.paydata	as	paydata,
        
        e.pptransid	as	pptransid,
        
        e.itemid	as	itemid,
        
        e.qtyof	as	qtyof,
        
        e.qty	as	qty,
        
        e.sn	as          sn,
        
        `e`.`asc`	as	`asc`,
        
        e.ssc	as	ssc,
        
        e.ebsold	as	ebsold,
        
        e.updated	as	updated,
        
        e.transid	as	transid,
        
        e.accounted	as	accounted,
        
        e.mverif	as	mverif,
        
        e.refunded	as	refunded,
        
        e.pendingpay	as	pendingpay,
        
        e.attention	as	attention,
        
        e.gmt	as	gmt,
        
        
        
        ''	as	subchannel,
        
        ''	as	sc_id,
        
        ''	as	otype,
        
        ''	as	buyer,
        
        ''	as	wholeprice,
        
        ''	as	rem,
        
        ''	as	bcns,
        
        ''	as	rbcns,
        
        
        
        ''	as	accounted,
        
        ''	as	time,
        
        ''	as	staffcomments,
        
        ''	as	totalweight,
        
        ''	as	fid,
        
        ''	as	payproc,
        
        ''	as	payproc_data,
        
        ''	as	courier_log,
        
        ''	as	pendquant_action,
        
        ''	as	sysdata,
        
        ''	as	CheckoutStatus,
        
        ''	as	OrderStatus,
        
        ''	as	oid_ref,
        
        ''	as	buytype,
        
        ''	as	subtype,
        
        ''	as	is_special,
        
        ''	as	status,
        
        ''	as	returnedresponse,
        
        ''	as	sameadr,
        
        ''	as	tel,
        
        ''	as	comments,
        
        ''	as	`order`,
        
        ''	as	eids,
        
        ''	as	delivery
        
        ";
        
        
        
        if (isset($this->sorttype) && $this->sorttype == 24)
        
        {
        
        $sql .= " FROM (ebay_transactions e) LEFT JOIN warehouse w ON e.et_id = w.sold_id WHERE (e.notpaid != 0 OR e.refunded != 0 OR e.returnnotif IS NOT NULL) AND w.vended > 0 AND w.deleted = 0 AND w.nr = 0 AND w.channel = 1 AND w.sold_id != 0";
        
        }
        
        else
        
        {
        
        $sql .= " FROM (ebay_transactions e)";
        
        if (isset($this->listingid)) $sql .= ' WHERE e_id = "'.(int)$this->listingid.'" ';
        
        elseif(isset($_POST['osrc'])) $sql .= " WHERE et_id = '".$this->input->post('osrc', TRUE)."' OR  buyerid LIKE '%".$this->input->post('osrc', TRUE)."%' OR buyeremail LIKE '%".$this->input->post('osrc', TRUE)."%' OR notes LIKE '%".$this->input->post('osrc', TRUE)."%' ";
        
        elseif ($timeframe) $sql .=  "WHERE mkdt <= ".$ofrom." AND mkdt >= ".$oto." ";
        
        }
        
        // if ($q->num_rows() > 0  && ((isset($this->orderchannel) && $this->orderchannel == 1) || !isset($this->orderchannel)))
        
        $sql .=  $psql[1];
        
        
        
        if ($channel[4]) $sql .= " UNION ALL ";
        
        }
        
        if ($channel[4])
        
        {
        
        $sql .= " SELECT distinct o.woid AS orderkey, timemk as timekey,  'warehouse' as typekey, '4' as channel,
        
        
        
        o.time as  field_timepaid,
        
        '' as field_notpaid,
        
        o.mark as field_mark,
        
        '' as field_customcode,
        
        o.returned as field_returned,
        
        o.returned_refunded as field_refunded,
        
        '' as field_sellingstatus,
        
        
        
        
        
        o.paid	as	paid,
        
        o.time	as	created,
        
        o.timemk	as	createdmk,
        
        o.notes	as	notes,
        
        o.admin	as	admin,
        
        revs	as	revs,
        
        o.return_id	as	return_id,
        
        o.returned_notes	as	returned_notes,
        
        o.returned_time	as	returned_time,
        
        o.returned_recieved	as	returned_recieved,
        
        o.returned_amount	as	returned_amount,
        
        o.returned_extracost	as	returned_extracost,
        
        o.shipped	as	shipping,
        
        ''	as	buyeremail,
        
        ''	as	buyeraddress,
        
        ''	as	buyerid,
        
        ''	as	returntype,
        
        ''	as	returnQuantity,
        
        ''	as	cascupd,
        
        ''	as	market,
        
        ''	as	e_id,
        
        ''	as	autoid,
        
        ''	as	autotitle,
        
        ''	as	contorderid,
        
        ''	as	eachpaid,
        
        ''	as	fee,
        
        ''	as	shipping,
        
        ''	as	tracking,
        
        ''	as	paydata,
        
        ''	as	pptransid,
        
        ''	as	itemid,
        
        ''	as	qtyof,
        
        ''	as	qty,
        
        ''	as	sn,
        
        ''	as	`asc`,
        
        ''	as	ssc,
        
        ''	as	ebsold,
        
        ''	as	updated,
        
        ''	as	transid,
        
        ''	as	accounted,
        
        ''	as	mverif,
        
        ''	as	refunded,
        
        ''	as	pendingpay,
        
        ''	as	attention,
        
        ''	as	gmt,
        
        
        
        subchannel	as	subchannel,
        
        sc_id	as	sc_id,
        
        otype	as	otype,
        
        buyer	as	buyer,
        
        wholeprice	as	wholeprice,
        
        rem	as	rem,
        
        bcns	as	bcns,
        
        rbcns	as	rbcns,
        
        
        
        ''	as	accounted,
        
        ''	as	time,
        
        ''	as	staffcomments,
        
        ''	as	totalweight,
        
        ''	as	fid,
        
        ''	as	payproc,
        
        ''	as	payproc_data,
        
        ''	as	courier_log,
        
        ''	as	pendquant_action,
        
        ''	as	sysdata,
        
        ''	as	CheckoutStatus,
        
        ''	as	OrderStatus,
        
        ''	as	oid_ref,
        
        ''	as	buytype,
        
        ''	as	subtype,
        
        ''	as	is_special,
        
        ''	as	status,
        
        ''	as	returnedresponse,
        
        ''	as	sameadr,
        
        ''	as	tel,
        
        ''	as	comments,
        
        ''	as	`order`,
        
        ''	as	eids,
        
        ''	as	delivery
        
        
        
        ";
        
        if (isset($this->listingid)) $sql .= ' FROM (warehouse_orders o) LEFT JOIN warehouse w ON o.woid = w.sold_id WHERE w.vended > 0 AND w.deleted = 0 AND w.nr = 0 AND w.channel = 4 AND w.listingid = "'.(int)$this->listingid.'" ';
        
        elseif(isset($_POST['osrc'])) $sql .= " FROM (warehouse_orders o) WHERE woid = '".$this->input->post('osrc', TRUE)."' OR buyer LIKE '%".$this->input->post('osrc', TRUE)."%' OR notes LIKE '%".$this->input->post('osrc', TRUE)."%' ";
        
        elseif ($timeframe) $sql .=  " FROM (warehouse_orders o)  WHERE timemk <= ".$ofrom." AND timemk >= ".$oto." ";
        
        else $sql .= ' FROM  (warehouse_orders o) ';
        
        //if ($q->num_rows() > 0  && ((isset($this->orderchannel) && $this->orderchannel == 1) || !isset($this->orderchannel)))
        
        $sql .=  $psql[4];
        
        }
        
        if ($channel[2] && ($channel[1] || $channel[4])) $sql .= " UNION ALL ";
        
        if ($channel[2])
        
        {
        
        $sql .= " SELECT oid AS orderkey,submittime as timekey,'website' as typekey, '2' as channel,
        
        complete as  field_timepaid,
        
        '' as field_notpaid,
        
        mark as field_mark,
        
        '' as field_customcode,
        
        returned as field_returned,
        
        returned_refunded as field_refunded,
        
        '' as field_sellingstatus,
        
        
        
        submittime	as	createdmk,
        
        complete_time	as	created,
        
        endprice	as	paid,
        
        endprice_delivery	as	shipping,
        
        admin	as	admin,
        
        revs	as	revs,
        
        return_id	as	return_id,
        
        returned_notes	as	returned_notes,
        
        returned_time	as	returned_time,
        
        returned_recieved	as	returned_recieved,
        
        returned_amount	as	returned_amount,
        
        returned_extracost	as	returned_extracost,
        
        ''	as	notes,
        
        email	as	buyeremail,
        
        concat(
        
        address,', ',
        
        city,', ',
        
        state,', ',
        
        postcode,', ',
        
        country,', ',
        
        residential,', ',
        
        daddress,' - ',
        
        dcity,', ',
        
        dstate,', ',
        
        dpostcode,', ',
        
        dcountry)	as	buyeraddress,
        
        concat(`fname`,' ',`lname`)	as  buyerid,
        
        ''	as	returntype,
        
        ''	as	returnQuantity,
        
        ''	as	cascupd,
        
        ''	as	market,
        
        ''	as	e_id,
        
        ''	as	autoid,
        
        ''	as	autotitle,
        
        ''	as	contorderid,
        
        ''	as	eachpaid,
        
        ''	as	fee,
        
        ''	as	shipping,
        
        ''	as	tracking,
        
        ''	as	paydata,
        
        ''	as	pptransid,
        
        ''	as	itemid,
        
        ''	as	qtyof,
        
        ''	as	qty,
        
        ''	as	sn,
        
        ''	as	`asc`,
        
        ''	as	ssc,
        
        ''	as	ebsold,
        
        ''	as	updated,
        
        ''	as	transid,
        
        ''	as	accounted,
        
        ''	as	mverif,
        
        ''	as	refunded,
        
        ''	as	pendingpay,
        
        ''	as	attention,
        
        ''	as	gmt,
        
        
        
        ''	as	subchannel,
        
        ''	as	sc_id,
        
        ''	as	otype,
        
        ''	as	buyer,
        
        ''	as	wholeprice,
        
        ''	as	rem,
        
        ''	as	bcns,
        
        ''	as	rbcns,
        
        
        
        accounted	as	accounted,
        
        time	as	time,
        
        staffcomments	as	staffcomments,
        
        totalweight	as	totalweight,
        
        fid	as	fid,
        
        payproc	as	payproc,
        
        payproc_data	as	payproc_data,
        
        courier_log	as	courier_log,
        
        pendquant_action	as	pendquant_action,
        
        sysdata	as	sysdata,
        
        CheckoutStatus	as	CheckoutStatus,
        
        OrderStatus	as	OrderStatus,
        
        oid_ref	as	oid_ref,
        
        buytype	as	buytype,
        
        subtype	as	subtype,
        
        is_special	as	is_special,
        
        status	as	status,
        
        returnedresponse	as	returnedresponse,
        
        sameadr	as	sameadr,
        
        tel	as	tel,
        
        comments	as	comments,
        
        `order`	as	`order`,
        
        eids	as	eids,
        
        delivery	as	delivery
        
        
        
        FROM orders ";
        
        if (isset($this->listingid)) $sql .= ' WHERE eids LIKE "%|'.(int)$this->listingid.'|%" ';
        
        elseif(isset($_POST['osrc'])) $sql .= " WHERE oid = '".$this->input->post('osrc', TRUE)."' OR fname LIKE '%".$this->input->post('osrc', TRUE)."%' OR lname LIKE '%".$this->input->post('osrc', TRUE)."%' OR email LIKE '%".$this->input->post('osrc', TRUE)."%' ";
        
        elseif ($timeframe) $sql .=  "WHERE submittime <= ".$ofrom." AND submittime >= ".$oto." ";
        
        $csql .=  $psql[2];
        
        }
        
        $sql .= " ORDER BY `timekey` DESC";
        
        
        
        //printcool ($sql);
        
        
        
        $sql .= " limit ".(($perpage*$page)-$perpage).", ".$perpage;
        
        
        
        $po = $this->db->query($sql);
        
        $idarray = array();
        
        $ridarray = array();
        
        $list = array();
        
        if ($po->num_rows() >0)
        
        {
        
        //printcool($po->num_rows());
        
        // printcool($po->result_array());
        
        //$mkdtdupcheck = 0;
        
        foreach ($po->result_array() as $v)
        
        {
        
        // if ((int)$v['createdmk'] == (int)$mkdtdupcheck) $v['createdmk'] = $v['createdmk']-1;
        
        // $mkdtdupcheck = $v['createdmk'];
        
        // if ($v['createdmk'] < $oldtrestentry) $oldtrestentry = $v['createdmk'];
        
        
        
        switch ($v['typekey'])
        
        {
        
        case 'ebay':
        
        if (strlen($v['paydata']) > 10)
        
        {
        
        $v['paydata'] = unserialize($v['paydata']);
        
        if (isset($v['paydata'])) unset($v['paydata']['PaidTime']);
        
        }
        
        else $v['paydata'] = false;
        
        //$list[$v['createdmk'].'E'] = $v;
        
        //$listings[$v['e_id']] = TRUE;
        
        break;
        
        case 'website':
        
        if ($v['status'] != '' && $v['status'] != ' ')
        
        {
        
        $v['status'] = unserialize($v['status']);
        
        //$v['origstatus'] = $v['status'][0];
        
        $v['status'] = end($v['status']);
        
        }
        
        $v['created'] = $v['time'];
        
        $v['createdmk'] = explode(' ', $v['time']);
        
        $v['createdmk'] = explode('-', $v['createdmk'][0]);
        
        if (isset($v['createdmk'][1]) && isset($v['createdmk'][2]) && isset($v['createdmk'][0])) $v['createdmk'] = (int)mktime(0, 0, 0, $v['createdmk'][1], $v['createdmk'][2], $v['createdmk'][0]);
        
        else $v['createdmk'] = false;
        
        if (strlen($v['order']) > 9)
        
        {
        
        $v['order'] = unserialize($v['order']);
        
        if (is_array($v['order']))
        
        foreach ($v['order'] as $k => $ov)
        
        {
        
        $os[$ov['e_id']] = $ov['quantity'];
        
        if (!isset($ov['sn'])) $v['order'][$k]['sn'] = '';
        
        if (!isset($ov['admin'])) $v['order'][$k]['admin'] = '';
        
        $listings[$ov['e_id']] = TRUE;
        
        }
        
        }
        
        
        
        if (strlen($v['CheckoutStatus']) > 9) $v['CheckoutStatus'] = unserialize($v['CheckoutStatus']);
        
        
        
        //if ($v['createdmk'] < $oldorestentry) $oldorestentry = $v['createdmk'];
        
        
        
        //$list[$v['createdmk'].'O'] = $v;
        
        break;
        
        case 'warehouse':
        
        //$list[$w['createdmk'].'W'] = $v;
        
        break;
        
        }
        
        
        
        $list[] = $v;
        
        if ((int)$v['e_id'] > 0) $listings[$v['e_id']] = TRUE;
        
        $idarray[$v['channel']][] = $v['orderkey'];
        
        if ((int)$v['return_id'] > 0) $ridarray[$v['channel']][$v['return_id']] = $v['return_id'];
        
        
        
        
        
        }
        
        }
        
        
        
        //printcool ($idarray);
        
        //printcool ($ridarray);
        
        //printcool ($listings);
        
        //printcool ($list);
        
        
        
        if (count($idarray)>0)
        
        {
        
        $this->load->model('Myseller_model');
        
        foreach ($idarray as $k => $v)
        
        {
        
        $this->Myseller_model->getSales($v, $k);
        
        }
        
        }
        
        if (count($ridarray)>0)
        
        {
        
        $this->load->model('Myseller_model');
        
        foreach ($ridarray as $k => $v)
        
        {
        
        $this->Myseller_model->getReturns($v, $k);
        
        }
        
        }
        
        if (count($listings)>0)
        
        {
        
        $this->db->select("e_title, e_part, e_qpart, e_id, quantity, ebayquantity, ebay_id");
        
        $st = 0;
        
        foreach ($listings as $k => $v)
        
        {
        
        if ($st == 0) { $this->db->where('e_id', $k); $st++; }
        
        else $this->db->or_where('e_id', $k);
        
        }
        
        
        
        $q = $this->db->get('ebay');
        
        $ebl = false;
        
        if ($q->num_rows() > 0)
        
        {
        
        foreach ($q->result_array() as $k=>$v)
        
        {
        
        $ebl[$v['e_id']] = $v;
        
        }
        
        
        
        }
        
        $this->mysmarty->assign('ebl', $ebl);
        
        }
        
        
        
        
        
        $this->mysmarty->assign('list', $list);
        
        
        
        $this->mysmarty->assign('statuses', $this->Myorders_model->GetStatuses());
        
        $this->Myseller_model->getSalesListings($listings);
        
        $this->mysmarty->assign('gotothisurl', 'Orders/'.$page.'/'.$perpage.'/');
        
        $this->mysmarty->view('myebay/myebay_neworders.html');
        
        exit();
        
        */
    }
    function ShowOrder($id, $channel)
    {
        $this->orderid      = (int) $id;
        $this->orderchannel = (int) $channel;
        $this->GetOrders();
    }
    /*function GetEbayStore($display = TRUE)
    
    {
    
    if ($display)
    
    {
    
    set_time_limit(1500);
    
    require_once($this->config->config['ebaypath'].'get-common/keys.php');
    
    require_once($this->config->config['ebaypath'].'get-common/eBaySession.php');
    
    }
    
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
    
    
    
    $xml = simplexml_load_string($responseXml);
    
    if ($display)
    
    {
    
    $this->mysmarty->assign('list', $xml->Store->CustomCategories->CustomCategory);
    
    $this->mysmarty->view('myebay/myebay_store.html');
    
    }
    
    else return $xml->Store->CustomCategories->CustomCategory;
    
    }*/
    function ShowListingOrders($listing = '')
    {
        $this->listingid = (int) $listing;
        $this->GetOrders();
    }
    function FraudStatus($id = 0)
    {
        if ((int) $id == 0)
            Redirect("Myebay/SortOrders/Site#" . (int) $id);
        $this->load->model('Myorders_model');
        $this->order = $this->Myorders_model->GetItem((int) $id);
        if (!$this->order)
            Redirect("Myebay/SortOrders/Site#" . (int) $id);
        //if ($this->order['complete'] != 1)
        //	{
        $this->Myorders_model->FraudStatus((int) $this->order['oid']);
        //}
        if (strlen($this->order['order']) > 9)
        {
            $this->order['order'] = unserialize($this->order['order']);
            //printcool ($this->order['order']);
            if (is_array($this->order['order']))
            {
                //$this->db->select("e_id, quantity, ebayquantity, ebay_id");
                $st = 0;
                foreach ($this->order['order'] as $k => $ov)
                {
                    $channel    = 2;
                    $sold_id    = (int) $id;
                    $sold_subid = $ov['e_id'];
                    $this->db->where('channel', $channel);
                    $this->db->where('sold_id', $sold_id);
                    $this->db->where('sold_subid', $sold_subid);
                    $f = $this->db->get('warehouse');
                    if ($f->num_rows() > 0)
                    {
                        $fr = $f->result_array();
                        foreach ($fr as $fl)
                        {
                            $data['sold_date']  = '';
                            $data['paid']       = 0;
                            $data['shipped']    = 0;
                            $data['ordernotes'] = '';
                            $data['sellingfee'] = 0;
                            $data['sold_id']    = 0;
                            $data['sold_subid'] = 0;
                            $data['status']     = 'Listed';
                            $data['sold']       = '';
                            $data['vended']     = 0;
                            foreach ($data as $k => $v) //printcool ($v); printcool ($wid[$k]);
                            {
                                if ($v != $fl[$k])
                                    $this->Auth_model->wlog($fl['bcn'], $fl['wid'], $k, $fl[$k], $v);
                            }
                            $data['status_notes'] = 'Changed from "' . $fl['status'] . '" - Fraud by ' . $this->session->userdata['ownnames'];
                            //if (trim($v['status_notes']) == '') $data['status_notes'] = $statusnotes;
                            //else $data['status_notes'] = $statusnotes.' | '.$fl['status_notes'];
                            //printcool ($data);
                            //printcool ($fl);
                            $this->db->update('warehouse', $data, array(
                                'wid' => $fl['wid']
                            ));
                        }
                    }
                    /*if (isset($ov['sn']))
                    
                    {
                    
                    if ($st == 0) { $this->db->where('e_id', $k); $st++; }
                    
                    else $this->db->or_where('e_id', $k);
                    
                    }*/
                }
                /*
                
                $q = $this->db->get('ebay');
                
                $ebl = false;
                
                if ($q->num_rows() > 0)
                
                {
                
                foreach ($q->result_array() as $k=>$v)
                
                {
                
                $ebl[$v['e_id']] = $v;
                
                }
                
                }
                
                
                
                foreach ($this->order['order'] as $k => $ov)
                
                {
                
                if (isset($ov['sn']) && isset($ebl[$k]))
                
                {
                
                if (trim($ebl[$k]['e_part']) != '') $ebl[$k]['e_part'] = $ebl[$k]['e_part'].',';
                
                //printcool ($ebl[$k]['e_part'].$ov['sn']);
                
                $this->db->update('ebay', array('e_part' => $ebl[$k]['e_part'].$ov['sn']), array('e_id' => $k));
                
                $this->_GhostPopulate((int)$k);
                
                }
                
                }*/
            }
        }
        $this->admindata['msg_date']  = CurrentTime();
        $this->admindata['msg_title'] = 'Order Frauded';
        $this->admindata['msg_body']  = 'Order ' . $id . ' Frauded by Admin ' . $this->session->userdata['name'] . ' @ ' . FlipDateMail($this->admindata['msg_date']);
        $this->load->model('Login_model');
        $this->Login_model->InsertHistoryData($this->admindata);
        //$this->mailid = 9;
        GoMail($this->admindata);
        Redirect("Myebay/SortOrders/Site#" . (int) $id);
    }
    function CompleteStatus($id = 0)
    {
        $this->Auth_model->CheckOrders();
        if ((int) $id == 0)
            Redirect("Myebay/SortOrders/Site#" . (int) $id);
        $this->load->model('Myorders_model');
        $this->order = $this->Myorders_model->GetItem((int) $id);
        if (!$this->order)
            Redirect("Myebay/SortOrders/Site#" . (int) $id);
        if ($this->order['payproc'] == 1)
        {
            if ($this->order['complete'] == 0 || $this->order['complete'] > 4)
            {
                $this->Myorders_model->CompleteStatus((int) $this->order['oid']);
            }
        }
        elseif ($this->order['payproc'] == 2)
        {
            if ($this->order['complete'] == 0 || $this->order['complete'] == 5 || $this->order['complete'] == 6 || $this->order['complete'] == 0 || $this->order['complete'] > 12)
            {
                $this->Myorders_model->CompleteStatus((int) $this->order['oid']);
            }
        }
        $this->admindata['msg_date']  = CurrentTime();
        $this->admindata['msg_title'] = 'Order Manual Complete';
        $this->admindata['msg_body']  = 'Order ' . $id . ' Completed Manualy by Admin ' . $this->session->userdata['name'] . ' @ ' . FlipDateMail($this->admindata['msg_date']);
        $this->load->model('Login_model');
        $this->Login_model->InsertHistoryData($this->admindata);
        //$this->mailid = 9;
        GoMail($this->admindata);
        Redirect("Myebay/SortOrders/Site#" . (int) $id);
    }
    ///////////////////////////
    function OrderComm($id, $channel = 2)
    {
        $this->Auth_model->CheckOrders();
        switch ($channel)
        {
            case 1:
                $this->db->select('et_id, buyeremail, comm');
                $this->db->where('et_id', (int) $id);
                $query = $this->db->get('ebay_transactions');
                if ($query->num_rows() > 0)
                {
                    $o = $query->row_array();
                    if (strlen($o['comm']) > 15)
                        $o['comm'] = unserialize($o['comm']);
                    else
                        $o['comm'] = FALSE;
                    $this->mysmarty->assign('id', $o['et_id']);
                    $this->mysmarty->assign('comm', $o['comm']);
                    $this->mysmarty->assign('channel', (int) $channel);
                }
                else
                    exit('Invalid Order');
                break;
            case 2:
            default:
                $this->db->select('oid, email, comm');
                $this->db->where('oid', (int) $id);
                $query = $this->db->get('orders');
                if ($query->num_rows() > 0)
                {
                    $o = $query->row_array();
                    if (strlen($o['comm']) > 15)
                        $o['comm'] = unserialize($o['comm']);
                    else
                        $o['comm'] = FALSE;
                    $this->mysmarty->assign('id', $o['oid']);
                    $this->mysmarty->assign('comm', $o['comm']);
                    $this->mysmarty->assign('channel', (int) $channel);
                }
                else
                    exit('Invalid Order');
        }
        require_once($this->config->config['pathtopublic'] . '/fckeditor/fckeditor.php');
        $this->editor         = new FCKeditor('msg');
        $this->editor->Width  = "650";
        $this->editor->Height = "400";
        $this->editor->Value  = '';
        $this->mysmarty->assign('editormsg', $this->editor->CreateHtml());
        if (isset($_POST['msg']))
        {
            $title = $this->input->post('titlemsg', TRUE);
            $body  = $this->input->post('msg', TRUE);
            switch ($channel)
            {
                case 1:
                    if (strlen($title) < 10)
                        $title = 'Regarding your eBay Transaction No.' . $id;
                    break;
                case 2:
                default:
                    if (strlen($title) < 10)
                        $title = 'Regarding your Order No.' . $id . ' at ' . $this->config->config['sitename'];
            }
            if (isset($_FILES['comm']['name']) && $_FILES['comm']['name'] != '')
                $file = trim($_FILES['comm']['name']);
            else
                $file = '';
            $o['comm'][] = array(
                'titlemsg' => $title,
                'msg' => $body,
                'time' => CurrentTime(),
                'file' => $file,
                'admin' => $this->session->userdata['name']
            );
            if (count($o['comm'] > 0))
                $o['comm'] = serialize($o['comm']);
            else
                $o['comm'] = NULL;
            switch ($channel)
            {
                case 1:
                    $this->db->update('ebay_transactions', array(
                        'comm' => $o['comm']
                    ), array(
                        'et_id' => (int) $o['et_id']
                    ));
                    $this->session->set_flashdata('success_msg', 'Message sent and saved');
                    GoMail(array(
                        'msg_title' => $title,
                        'msg_body' => $body
                    ), $o['buyeremail']);
                    //GoMail (array('msg_title' => $title, 'msg_body' => $body), 'mr.reece@gmail.com');
                    break;
                case 2:
                default:
                    $this->db->update('orders', array(
                        'comm' => $o['comm']
                    ), array(
                        'oid' => (int) $o['oid']
                    ));
                    $this->session->set_flashdata('success_msg', 'Message sent and saved');
                    GoMail(array(
                        'msg_title' => $title,
                        'msg_body' => $body
                    ), $o['email']);
                    //GoMail (array('msg_title' => $title, 'msg_body' => $body), 'mr.reece@gmail.com');
            }
            //printcool ($_FILES);exit();
            Redirect('Myebay/OrderComm/' . $id . '/' . (int) $channel);
        }
        //printcool ($o);
        $this->mysmarty->view('myebay/myebay_comm.html');
    }
    function _EndeBayListing($itemid = '', $eid = '', $oid = '')
    {
        //http://developer.ebay.com/Devzone/xml/docs/Reference/ebay/EndFixedPriceItem.html
        if ((int) $itemid > 0 && (int) $eid > 0 && (int) $oid > 0)
        {
            set_time_limit(120);
            ini_set('mysql.connect_timeout', 120);
            ini_set('max_execution_time', 120);
            ini_set('default_socket_timeout', 120);
            require_once($this->config->config['ebaypath'] . 'get-common/keys.php');
            require_once($this->config->config['ebaypath'] . 'get-common/eBaySession.php');
            $verb           = 'EndFixedPriceItem';
            //Create a new eBay session with all details pulled in from included keys.php
            $session        = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
            $requestXmlBody = '<?xml version="1.0" encoding="utf-8" ?>

<EndFixedPriceItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
            $requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
            $requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
            $requestXmlBody .= '<ErrorHandling>BestEffort</ErrorHandling>';
            $requestXmlBody .= "<Version>$compatabilityLevel</Version>";
            $requestXmlBody .= '<EndingReason>NotAvailable</EndingReason>';
            $requestXmlBody .= '<ItemID>' . (int) $itemid . '</ItemID>';
            $requestXmlBody .= '</EndFixedPriceItemRequest>';
            //send the request and get response
            $responseXml = $session->sendHttpRequest($requestXmlBody);
            printcool($responseXml);
            if (stristr($responseXml, 'HTTP 404') || $responseXml == '')
                die('<P>Error sending request');
            $xml = $this->_XML2Array(simplexml_load_string($responseXml));
            //printcool ($xml);
            if (isset($xml['EndTime']))
            {
                $ended = CleanBadDate((string) $xml['EndTime']);
                $this->db->update('ebay', array(
                    'ebended' => $ended,
                    'endedreason' => 'Ended from order ' . (int) $oid,
                    'sitesell' => 0
                ), array(
                    'ebay_id' => (int) $itemid,
                    'e_id' => (int) $eid
                ));
                $updatestring = 'Ebay Listing <a href="' . Site_url() . 'Myebay/Search/' . (int) $eid . '" target="_blank" style="color: #419aff; font-size:10px;"><img src="' . Site_url() . 'images/admin/b_search.png" class="linkicon" />' . (int) $eid . '</a> - ItemID: <a href="http://www.ebay.com/itm/' . $itemid . '" target="_blank" style="color: #419aff; font-size:10px;"><img src="' . Site_url() . 'images/admin/b_search.png" class="linkicon"/>' . $itemid . '</a> ended from order ' . (int) $oid . ' at ' . $ended . '<br>';
                $this->db->insert('admin_history', array(
                    'msg_type' => 1,
                    'msg_title' => 'Order Ended Listing',
                    'msg_body' => $updatestring,
                    'msg_date' => CurrentTime(),
                    'e_id' => (int) $eid,
                    'itemid' => (int) $itemid,
                    'trec' => 0,
                    'admin' => $this->session->userdata['ownnames'],
                    'sev' => 0
                ));
            }
        }
    }
    function GetEbayTransactions($highlight = '')
    {
        $this->Auth_model->CheckOrders();
        if ($highlight == '')
            Redirect('Myebay/GetOrders/');
        else
            Redirect('Myebay/GetOrders/#' . (int) $highlight);
        $this->mysmarty->assign('area', 'Transactions');
        //DIMITRI - 16.7.2014, added more fields to select (e_id, e_title, idpath, e_img1):
        $this->db->select("distinct t.*, e_part, e_id, e_title, idpath, e_img1", false);
        $this->db->where('t.mkdt >= ', mktime() - 2592000);
        //$this->db->limit(500);
        $this->db->order_by("rec", "DESC");
        $this->db->join('ebay e', 't.itemid = e.ebay_id', 'LEFT');
        $q    = $this->db->get('ebay_transactions t');
        $list = false;
        if ($q->num_rows() > 0)
        {
            foreach ($q->result_array() as $k => $v)
            {
                if (strlen($v['paydata']) > 30)
                {
                    $v['paydata'] = unserialize($v['paydata']);
                    if (isset($v['paydata']))
                        unset($v['paydata']['PaidTime']);
                }
                else
                    $v['paydata'] = false;
                $list[$k] = $v;
            }
        }
        $this->mysmarty->assign('list', $list);
        $this->mysmarty->assign('highlight', $highlight);
        $this->mysmarty->view('myebay/myebay_transactions.html');
    }
    function UpdateCurrentTransaction($rec = 0)
    {
        $this->Auth_model->CheckOrders();
        if ((int) $rec > 0)
        {
            set_time_limit(120);
            ini_set('mysql.connect_timeout', 120);
            ini_set('max_execution_time', 120);
            ini_set('default_socket_timeout', 120);
            require_once($this->config->config['ebaypath'] . 'get-common/keys.php');
            require_once($this->config->config['ebaypath'] . 'get-common/eBaySession.php');
            $verb               = 'GetSellingManagerSaleRecord';
            $compatabilityLevel = 959;
            //Create a new eBay session with all details pulled in from included keys.php
            $session            = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
            $this->db->select('rec, et_id, e_id, itemid, transid, fee, shipping, tracking, asc, ssc, qty, paydata, paid, paidtime,notpaid,refunded,pendingpay,customcode');
            $this->db->where('rec', (int) $rec);
            $q = $this->db->get('ebay_transactions');
            if ($q->num_rows() > 0)
            {
                $t              = $q->row_array();
                $requestXmlBody = '<?xml version="1.0" encoding="utf-8" ?>';
                $requestXmlBody .= '<GetSellingManagerSaleRecordRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
                $requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
                $requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';
                $requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
                $requestXmlBody .= '<ErrorHandling>BestEffort</ErrorHandling>';
                $requestXmlBody .= "<ItemID>$t[itemid]</ItemID>";
                $requestXmlBody .= "<TransactionID>$t[transid]</TransactionID>";
                $requestXmlBody .= "<Version>$compatabilityLevel</Version>";
                $requestXmlBody .= '</GetSellingManagerSaleRecordRequest>';
                //send the request and get response
                $responseXml = $session->sendHttpRequest($requestXmlBody);
                if (stristr($responseXml, 'HTTP 404') || $responseXml == '')
                    die('<P>Error sending request');
                $xml  = simplexml_load_string($responseXml); //printcool($xml);
                $item = $xml->SellingManagerSoldOrder;
                if ($item)
                {
                    $asc = floater((string) $item->ActualShippingCost);
                    echo 'Actual Shipping Cost: ' . $asc;
                    if ((float) $asc != (float) $t['asc'] && $asc != '' && floater($asc) > 0)
                    {
                        echo ' - UPDATED';
                        $this->db->update('ebay_transactions', array(
                            'asc' => (float) $asc,
                            'cascupd' => 2
                        ), array(
                            'et_id' => $t['et_id']
                        ));
                        $this->_logaction('Transactions', 'B', array(
                            'ActShipCost' => $t['asc']
                        ), array(
                            'ActShipCost' => (float) $asc
                        ), 0, $t['itemid'], $t['rec']);
                    }
                    $update['shipped_actual'] = floater((float) $asc / (int) $item->SellingManagerSoldTransaction->QuantitySold);
                    $paid                     = floater(((int) $item->SellingManagerSoldTransaction->QuantitySold * (float) $item->SellingManagerSoldTransaction->ItemPrice));
                    echo '<br>Paid: ' . $paid;
                    if ((float) $paid != (float) $t['paid'])
                    {
                        echo ' - UPDATED';
                        $this->db->update('ebay_transactions', array(
                            'paid' => (float) $paid,
                            'cascupd' => 2
                        ), array(
                            'et_id' => $t['et_id']
                        ));
                    }
                    $update['paid'] = floater((float) $item->SellingManagerSoldTransaction->ItemPrice);
                    if (isset($item->ShippingDetails->ShippingServiceOptions->ShippingServiceCost))
                    {
                        $update['shipped'] = floater($item->ShippingDetails->ShippingServiceOptions->ShippingServiceCost / (int) $item->SellingManagerSoldTransaction->QuantitySold);
                        if ($t['ssc'] != (string) $item->ShippingDetails->ShippingServiceOptions->ShippingServiceCost && floater($item->ShippingDetails->ShippingServiceOptions->ShippingServiceCost) > 0)
                        {
                            echo '<br>shipping cost: ' . floater($item->ShippingDetails->ShippingServiceOptions->ShippingServiceCost);
                            $this->db->update('ebay_transactions', array(
                                'ssc' => floater($item->ShippingDetails->ShippingServiceOptions->ShippingServiceCost),
                                'cascupd' => 2
                            ), array(
                                'et_id' => $t['et_id']
                            ));
                        }
                        if ($t['shipping'] != (string) $item->ShippingDetails->ShippingServiceOptions->ShippingService && (string) $item->ShippingDetails->ShippingServiceOptions->ShippingService != '')
                        {
                            echo '<br>shipping: ' . (string) $item->ShippingDetails->ShippingServiceOptions->ShippingService;

                            $this->db->update('ebay_transactions', array(
                                'shipping' => (string) $item->ShippingDetails->ShippingServiceOptions->ShippingService,
                                'cascupd' => 2
                            ), array(
                                'et_id' => $t['et_id']
                            ));
                        }
                    }
                    else
                        $update['shipped'] = floater((float) $t['ssc'] / (int) $item->SellingManagerSoldTransaction->QuantitySold);
                    $update['sellingfee'] = floater($t['fee'] / (int) $item->SellingManagerSoldTransaction->QuantitySold);
                    $ar                   = $this->_XML2Array($item->OrderStatus);
                    $ar                   = $ar['OrderStatus'];
                    if (isset($ar['PaidTime']))
                        echo '<br>Paid Time: ' . CleanBadDate((string) $ar['PaidTime']);
                    if (isset($ar['PaidTime']) && (CleanBadDate((string) $ar['PaidTime']) != $t['paidtime']) && (CleanBadDate((string) $ar['PaidTime']) != ''))
                    {
                        echo ' - UPDATED';
                        $this->db->update('ebay_transactions', array(
                            'paidtime' => CleanBadDate((string) $ar['PaidTime'])
                        ), array(
                            'et_id' => $t['et_id']
                        ));
                        $this->_logaction('Transactions', 'B', array(
                            'PaidTime' => $t['paidtime']
                        ), array(
                            'PaidTime' => CleanBadDate((string) $ar['PaidTime'])
                        ), 0, $t['itemid'], $t['rec']);
                        $update['paid_date'] = (float) $t['paidtime'];
                    }
                    if (isset($ar['CheckoutStatus']))
                        echo '<br>Checkout Status: ' . CleanBadDate((string) $ar['CheckoutStatus']);
                    if (isset($ar['CheckoutStatus']))
                    {
                        if ($ar['CheckoutStatus'] == 'CustomCode')
                        {
                            if ($t['customcode'] == 0)
                            {
                                echo ' - UPDATED to CustomCode';
                                $this->db->update('ebay_transactions', array(
                                    'customcode' => 1
                                ), array(
                                    'et_id' => $t['et_id']
                                ));
                                $this->_logaction('Transactions', 'B', array(
                                    'customcode' => 0
                                ), array(
                                    'customcode' => 1
                                ), 0, $t['itemid'], $t['rec']);
                            }
                        }
                        elseif ($ar['CheckoutStatus'] == 'Incomplete')
                        {
                            if ($t['notpaid'] == 0)
                            {
                                echo ' - UPDATED to Not Paid';
                                $this->db->update('ebay_transactions', array(
                                    'notpaid' => 1
                                ), array(
                                    'et_id' => $t['et_id']
                                ));
                                $this->_logaction('Transactions', 'B', array(
                                    'notpaid' => 0
                                ), array(
                                    'notpaid' => 1
                                ), 0, $t['itemid'], $t['rec']);
                            }
                        }
                        elseif ($ar['CheckoutStatus'] == 'Pending')
                        {
                            if ($t['pendingpay'] == 0)
                            {
                                echo ' - UPDATED to Pending';
                                $this->db->update('ebay_transactions', array(
                                    'pendingpay' => 1
                                ), array(
                                    'et_id' => $t['et_id']
                                ));
                                $this->_logaction('Transactions', 'B', array(
                                    'pendingpay' => 0
                                ), array(
                                    'pendingpay' => 1
                                ), 0, $t['itemid'], $t['rec']);
                            }
                        }
                        elseif ($ar['CheckoutStatus'] == 'CheckoutComplete')
                        {
                            if ($t['pendingpay'] == 1)
                            {
                                echo ' - UPDATED to PendingPay 0';
                                $this->db->update('ebay_transactions', array(
                                    'pendingpay' => 0
                                ), array(
                                    'et_id' => $t['et_id']
                                ));
                                $this->_logaction('Transactions', 'B', array(
                                    'pendingpay' => $t['pendingpay']
                                ), array(
                                    'pendingpay' => 0
                                ), 0, $t['itemid'], $t['rec']);
                            }
                            if ($t['notpaid'] == 1)
                            {
                                echo ' - UPDATED to NotPaid 0';
                                $this->db->update('ebay_transactions', array(
                                    'notpaid' => 0
                                ), array(
                                    'et_id' => $t['et_id']
                                ));
                                $this->_logaction('Transactions', 'B', array(
                                    'notpaid' => $t['notpaid']
                                ), array(
                                    'notpaid' => 0
                                ), 0, $t['itemid'], $t['rec']);
                            }
                            if ($t['customcode'] == 1)
                            {
                                echo ' - UPDATED to CustomCode 0';
                                $this->db->update('ebay_transactions', array(
                                    'customcode' => 0
                                ), array(
                                    'et_id' => $t['et_id']
                                ));
                                $this->_logaction('Transactions', 'B', array(
                                    'customcode' => $t['customcode']
                                ), array(
                                    'customcode' => 0
                                ), 0, $t['itemid'], $t['rec']);
                            }
                        }
                    }
                    unset($ar['paidtime']);
                    $pd = serialize($ar);
                    if ($item && ($pd != $t['paydata']))
                    {
                        $this->db->update('ebay_transactions', array(
                            'paydata' => $pd
                        ), array(
                            'et_id' => $t['et_id']
                        ));
                    }
                    printcool($update);
                    if (isset($update))
                    {
                        $this->load->model('Myseller_model');
                        $this->db->select('wid, bcn, ' . $this->Myseller_model->sellingfields());
                        $this->db->where('channel', 1);
                        $this->db->where('sold_id', $t['et_id']);
                        $this->db->where('vended', 1);
                        $f = $this->db->get('warehouse');
                        if ($f->num_rows() > 0)
                        {
                            $fr = $f->result_array();
                            foreach ($fr as $fl)
                            {
                                printcool($fl);
                                if ($fl['vended'] == 1)
                                    $this->Myseller_model->HandleBCN($update, $fl);
                            }
                        }
                    }
                }
                /*
                
                $requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
                
                $requestXmlBody .= '<GetOrderTransactionsRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
                
                $requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
                
                $requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';
                
                $requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
                
                $requestXmlBody .= "<Version>$compatabilityLevel</Version>";
                
                $requestXmlBody .= ' <ItemTransactionIDArray>
                
                <ItemTransactionID>
                
                <ItemID>'.$t['itemid'].'</ItemID>
                
                <TransactionID>'.$t['transid'].'</TransactionID>
                
                </ItemTransactionID>
                
                </ItemTransactionIDArray>
                
                </GetOrderTransactionsRequest>';
                
                $verb = 'GetOrderTransactions';
                
                $session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
                
                $responseXml = $session->sendHttpRequest($requestXmlBody);
                
                if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
                
                die('<P>Error sending request');
                
                
                
                $xml = simplexml_load_string($responseXml);
                
                $this->load->model('Mywarehouse_model');
                
                $ssc = $newdata['ManualVerSelShippingCost'] = (string)$xml->OrderArray->Order->ShippingServiceSelected->ShippingServiceCost;
                
                $data = $this->Mywarehouse_model->getsaleattachdata(1, $t['et_id'], $t['e_id'],1);
                
                
                
                if(isset($data['qty']) && $data['qty'] > 1) $warehouse['shipped'] = sprintf("%01.2f", (float)$ssc/$data['qty']);
                
                else $warehouse['shipped'] = $ssc;
                
                echo '<br>DB data SSC: '.$t['ssc'];
                
                if ($t['ssc'] != $ssc)
                
                {
                
                echo '<br>Transaction data SSC: '.$ssc; echo ' - UPDATED';
                
                $this->db->update('ebay_transactions', array('ssc_old' => $t['ssc']), array('et_id' => $t['et_id']));
                
                
                
                }
                
                $this->load->model('Myseller_model');
                
                
                
                $bcns = $this->Myseller_model->getSales(array((int)$e['et_id']),1, TRUE, TRUE);
                
                if ($bcns) foreach($bcns as $wid)
                
                {
                
                $warehouse['netprofit'] = ((float)$wid['paid']+(float)$warehouse['shipped'])-((float)$wid['cost']+(float)$wid['sellingfee']+(float)$warehouse['shipped_actual']);
                
                
                
                foreach($warehouse as $k => $v)
                
                {
                
                if ($v != $wid[$k]) $this->Auth_model->wlog($wid['bcn'], $wid['wid'], $k, $wid[$k], $v);
                
                else unset($warehouse[$k]);
                
                }
                
                if (count($warehouse) > 0) $this->db->update('warehouse', $warehouse, array('wid' => (int)$wid['wid']));
                
                }
                
                
                
                
                
                
                
                */
            }
        }
        //$sortstring = $this->session->userdata['sortstring'];
        //if ($sortstring != '') Redirect('Myebay/SortOrders/'.$sortstring.'#'.(int)$rec);
        //else Redirect('Myebay/GetOrders/#'.(int)$rec);
        //Redirect('Myebay/GetEbayTransactions/#'.(int)$rec);
    }
    function GetEbayTransactionsLive()
    {
        $this->Auth_model->CheckOrders();
        $this->mysmarty->assign('area', 'Transactions');
        set_time_limit(1500);
        require_once($this->config->config['ebaypath'] . 'get-common/keys.php');
        require_once($this->config->config['ebaypath'] . 'get-common/eBaySession.php');
        $requestXmlBody = '<?xml version="1.0" encoding="utf-8" ?>';
        $requestXmlBody .= '<GetSellerTransactionsRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        $requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
        $requestXmlBody .= '<DetailLevel>ItemReturnAttributes</DetailLevel>';
        $requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
        $requestXmlBody .= "<Version>$compatabilityLevel</Version>";
        //http://developer.ebay.com/DevZone/XML/docs/Reference/eBay/types/GranularityLevelCodeType.html
        $dates = array(
            'from' => date('Y-m-d H:i:s', strtotime("4 Days")),
            'to' => date('Y-m-d H:i:s', strtotime("6	Days"))
        );
        $requestXmlBody .= '

		 <IncludeCodiceFiscale>' . TRUE . '</IncludeCodiceFiscale>

		 <IncludeContainingOrder>' . TRUE . '</IncludeContainingOrder>

		 <IncludeFinalValueFee>' . TRUE . '</IncludeFinalValueFee>

		<ModTimeFrom>' . $dates['from'] . '</ModTimeFrom>

 		<ModTimeTo>' . $dates['to'] . '</ModTimeTo>

  		<NumberOfDays>8</NumberOfDays>

		<Pagination>

		<EntriesPerPage>100</EntriesPerPage>

		<PageNumber>2</PageNumber>

		</Pagination>

		</GetSellerTransactionsRequest>';
        $verb        = 'GetSellerTransactions';
        //Create a new eBay session with all details pulled in from included keys.php
        $session     = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
        //send the request and get response
        $responseXml = $session->sendHttpRequest($requestXmlBody);
        if (stristr($responseXml, 'HTTP 404') || $responseXml == '')
            die('<P>Error sending request');
        $xml = simplexml_load_string($responseXml);
        printcool($xml);
        break;
        $this->mysmarty->assign('list', $xml->TransactionArray->Transaction);
        $this->mysmarty->assign('dates', $dates);
        $this->mysmarty->view('myebay/myebay_transactions.html');
        //printcool ($xml->TransactionArray);
    }
    function GetEbayTransactionsLiveNew()
    {
        $this->Auth_model->CheckOrders();
        $this->mysmarty->assign('area', 'Transactions');
        set_time_limit(1500);
        require_once($this->config->config['ebaypath'] . 'get-common/keys.php');
        require_once($this->config->config['ebaypath'] . 'get-common/eBaySession.php');
        $requestXmlBody = '<?xml version="1.0" encoding="utf-8" ?>';
        $requestXmlBody .= '<GetSellerTransactionsRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        $requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
        $requestXmlBody .= '<DetailLevel>ItemReturnAttributes</DetailLevel>';
        $requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
        $requestXmlBody .= "<Version>$compatabilityLevel</Version>";
        //http://developer.ebay.com/DevZone/XML/docs/Reference/eBay/types/GranularityLevelCodeType.html
        $dates = array(
            'from' => date('Y-m-d H:i:s', strtotime("-24 Hours")),
            'to' => date("Y-m-d H:i:s")
        );
        $requestXmlBody .= '

		 <IncludeCodiceFiscale>' . TRUE . '</IncludeCodiceFiscale>

		 <IncludeContainingOrder>' . TRUE . '</IncludeContainingOrder>

		 <IncludeFinalValueFee>' . TRUE . '</IncludeFinalValueFee>

		<ModTimeFrom>' . $dates['from'] . '</ModTimeFrom>

 		<ModTimeTo>' . $dates['to'] . '</ModTimeTo>

  		<NumberOfDays>2</NumberOfDays>

		<Pagination>

		<EntriesPerPage>100</EntriesPerPage>

		</Pagination>

		</GetSellerTransactionsRequest>';
        $verb        = 'GetSellerTransactions';
        //Create a new eBay session with all details pulled in from included keys.php
        $session     = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
        //send the request and get response
        $responseXml = $session->sendHttpRequest($requestXmlBody);
        if (stristr($responseXml, 'HTTP 404') || $responseXml == '')
            die('<P>Error sending request');
        $xml = simplexml_load_string($responseXml);
        foreach ($xml->TransactionArray->Transaction as $t)
        {
            $requestXmlBody = '<?xml version="1.0" encoding="utf-8" ?>';
            $requestXmlBody .= '<GetSellingManagerSaleRecordRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
            $requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
            $requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
            $requestXmlBody .= "<Version>$compatabilityLevel</Version>";
            $requestXmlBody .= '<ItemID>' . (int) $t->Item->ItemID . '</ItemID>

  <TransactionID>' . (int) $t->TransactionID . '</TransactionID>

  </GetSellingManagerSaleRecordRequest>';
            $session     = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], 'GetSellingManagerSaleRecord');
            $responseXml = $session->sendHttpRequest($requestXmlBody);
            if (stristr($responseXml, 'HTTP 404') || $responseXml == '')
                die('<P>Error sending request');
            $xmlt = simplexml_load_string($responseXml);
            printcool($xmlt);
            break;
        }
        printcool($xml);
        break;
        $this->mysmarty->assign('list', $xml->TransactionArray->Transaction);
        $this->mysmarty->assign('dates', $dates);
        $this->mysmarty->view('myebay/myebay_transactions.html');
        //printcool ($xml->TransactionArray);
    }
    function GetEbayOrdersLive()
    {
        $this->Auth_model->CheckOrders();
        $this->mysmarty->assign('area', 'Transactions');
        set_time_limit(1500);
        require_once($this->config->config['ebaypath'] . 'get-common/keys.php');
        require_once($this->config->config['ebaypath'] . 'get-common/eBaySession.php');
        $requestXmlBody = '<?xml version="1.0" encoding="utf-8" ?>';
        $requestXmlBody .= '<GetOrdersRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        $requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
        $requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';
        $requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
        $requestXmlBody .= "<Version>$compatabilityLevel</Version>";
        //http://developer.ebay.com/DevZone/XML/docs/Reference/eBay/types/GranularityLevelCodeType.html
        $dates = array(
            'from' => date('Y-m-d H:i:s', strtotime("-10 Days")),
            'to' => date("Y-m-d H:i:s")
        );
        printcool($dates);
        $requestXmlBody .= '

		<CreateTimeFrom>' . $dates['from'] . '</CreateTimeFrom>

		<CreateTimeTo>' . $dates['to'] . '</CreateTimeTo>

		<OrderRole>Seller</OrderRole>

		<OrderStatus>Completed</OrderStatus>

		<NumberOfDays>2</NumberOfDays>

		</GetOrdersRequest>';
        $verb        = 'GetOrders';
        //Create a new eBay session with all details pulled in from included keys.php
        $session     = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
        //send the request and get response
        $responseXml = $session->sendHttpRequest($requestXmlBody);
        if (stristr($responseXml, 'HTTP 404') || $responseXml == '')
            die('<P>Error sending request');
        $xml = simplexml_load_string($responseXml);
        printcool($xml);
        break;
        $this->mysmarty->assign('list', $xml->TransactionArray->Transaction);
        $this->mysmarty->assign('dates', $dates);
        $this->mysmarty->view('myebay/myebay_transactions.html');
        //printcool ($xml->TransactionArray);
    }
    function GetMyeBaySelling()
    {
        $this->Auth_model->CheckOrders();
        $this->mysmarty->assign('area', 'Transactions');
        set_time_limit(1500);
        require_once($this->config->config['ebaypath'] . 'get-common/keys.php');
        require_once($this->config->config['ebaypath'] . 'get-common/eBaySession.php');
        $requestXmlBody = '<?xml version="1.0" encoding="utf-8" ?>';
        $requestXmlBody .= '<GetMyeBaySellingRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        $requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
        $requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';
        $requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
        $requestXmlBody .= "<Version>$compatabilityLevel</Version>";
        //http://developer.ebay.com/DevZone/XML/docs/Reference/eBay/types/GranularityLevelCodeType.html
        $dates = array(
            'from' => date('Y-m-d H:i:s', strtotime("-10 Days")),
            'to' => date("Y-m-d H:i:s")
        );
        printcool($dates);
        $requestXmlBody .= '

		</GetMyeBaySellingRequest>';
        $verb        = 'GetMyeBaySelling';
        //Create a new eBay session with all details pulled in from included keys.php
        $session     = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
        //send the request and get response
        $responseXml = $session->sendHttpRequest($requestXmlBody);
        if (stristr($responseXml, 'HTTP 404') || $responseXml == '')
            die('<P>Error sending request');
        $xml = simplexml_load_string($responseXml);
        printcool($xml);
        break;
        $this->mysmarty->assign('list', $xml->TransactionArray->Transaction);
        $this->mysmarty->assign('dates', $dates);
        $this->mysmarty->view('myebay/myebay_transactions.html');
        //printcool ($xml->TransactionArray);
    }
    function ListQuantities()
    {
        $this->mysmarty->assign('qns', $this->Myebay_model->ListQuantities());
        $this->mysmarty->view('myebay/myebay_qns.html');
    }
    function _clean_file_name($filename)
    {
        $bad      = array(
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
            "%3c", // <
            "%253c", // <
            "%3e", // >
            "%0e", // >
            "%28", // (
            "%29", // )
            "%2528", // (
            "%26", // &
            "%24", // $
            "%3f", // ?
            "%3b", // ;
            "%3d" // =
        );
        $filename = str_replace($bad, '', $filename);
        return stripslashes($filename);
    }
    function GetCats()
    {
        $this->Auth_model->CheckListings();
        set_time_limit(120);
        ini_set('mysql.connect_timeout', 120);
        ini_set('max_execution_time', 120);
        ini_set('default_socket_timeout', 120);
        require_once($this->config->config['ebaypath'] . 'get-common/keys.php');
        require_once($this->config->config['ebaypath'] . 'get-common/eBaySession.php');
        $requestXmlBody = '<?xml version="1.0" encoding="utf-8" ?>';
        $requestXmlBody .= '<GetStoreRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        $requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
        $requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
        $requestXmlBody .= "<Version>$compatabilityLevel</Version>";
        $requestXmlBody .= '<CategoryStructureOnly>TRUE</CategoryStructureOnly>

		<UserID>la.tronics</UserID></GetStoreRequest>';
        $verb        = 'GetStore';
        //Create a new eBay session with all details pulled in from included keys.php
        $session     = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
        //send the request and get response
        $responseXml = $session->sendHttpRequest($requestXmlBody);
        if (stristr($responseXml, 'HTTP 404') || $responseXml == '')
            die('<P>Error sending request');
        //printcool ($responseXml);
        $this->load->helper('directory');
        $this->load->helper('file');
        if ($responseXml)
        {
            if (!write_file($this->config->config['ebaypath'] . '/cats.txt', $responseXml))
            {
                GoMail(array(
                    'msg_title' => 'Unable to write Cats.txt @ ' . CurrentTime(),
                    'msg_body' => '',
                    'msg_date' => CurrentTime()
                ), $this->config->config['support_email'], $this->config->config['no_reply_email']);
                echo 'Unable to update Cats.';
            }
            else
            {
                GoMail(array(
                    'msg_title' => 'Cats written @ ' . CurrentTime(),
                    'msg_body' => $responseXml,
                    'msg_date' => CurrentTime()
                ), $this->config->config['support_email'], $this->config->config['no_reply_email']);
                echo 'Cats updated. Refresh the admin view for the product now and close this window.';
            }
        }
    }
    function GetShipping()
    {
        $this->Auth_model->CheckListings();
        set_time_limit(1500);
        require_once($this->config->config['ebaypath'] . 'get-common/keys.php');
        require_once($this->config->config['ebaypath'] . 'get-common/eBaySession.php');
        $requestXmlBody = '<?xml version="1.0" encoding="utf-8" ?>';
        $requestXmlBody .= '<GeteBayDetailsRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        $requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
        $requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
        $requestXmlBody .= "<Version>$compatabilityLevel</Version>";
        $requestXmlBody .= '<DetailName>ShippingServiceDetails</DetailName>';
        $requestXmlBody .= '</GeteBayDetailsRequest>';
        $verb        = 'GeteBayDetails';
        $session     = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
        $responseXml = $session->sendHttpRequest($requestXmlBody);
        if (stristr($responseXml, 'HTTP 404') || $responseXml == '')
            die('<P>Error sending request');
        $this->load->helper('directory');
        $this->load->helper('file');
        if ($responseXml)
        {
            if (!write_file($this->config->config['ebaypath'] . '/shipping.txt', $responseXml))
                GoMail(array(
                    'msg_title' => 'Unable to write Shippinh.txt @' . CurrentTime(),
                    'msg_body' => '',
                    'msg_date' => CurrentTime()
                ), $this->config->config['support_email'], $this->config->config['no_reply_email']);
            else
                GoMail(array(
                    'msg_title' => 'Shipping written @' . CurrentTime(),
                    'msg_body' => '',
                    'msg_date' => CurrentTime()
                ), $this->config->config['support_email'], $this->config->config['no_reply_email']);
        }
        Redirect('Myebay');
    }
    function UpdateFromEbay($id, $page = 1, $save = false)
    {
        $this->Auth_model->CheckListings();
        if (isset($_POST['eid']))
        {
            $id   = (int) $_POST['eid'];
            $save = TRUE;
        }
        if ($_POST['itemid'] == '')
        {
            echo 'ERROR: Empty Item ID...<a href="javascript:history.back()">Back</a>';
            exit();
        }
        set_time_limit(90);
        ini_set('mysql.connect_timeout', 90);
        ini_set('max_execution_time', 90);
        ini_set('default_socket_timeout', 90);
        require_once($this->config->config['ebaypath'] . 'get-common/keys.php');
        require_once($this->config->config['ebaypath'] . 'get-common/eBaySession.php');
        $requestXmlBody = '<?xml version="1.0" encoding="utf-8" ?>';
        $requestXmlBody .= '<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">

';
        $requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
        $requestXmlBody .= '<DetailLevel>ItemReturnAttributes</DetailLevel>';
        $requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
        $requestXmlBody .= "<Version>$compatabilityLevel</Version>";
        $requestXmlBody .= '<ItemID>' . (int) $_POST['itemid'] . '</ItemID>

						</GetItemRequest>';
        $verb        = 'GetItem';
        $session     = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
        $responseXml = $session->sendHttpRequest($requestXmlBody);
        if (stristr($responseXml, 'HTTP 404') || $responseXml == '')
            die('<P>Error sending request');
        $xml = simplexml_load_string($responseXml);
        if ((string) $xml->Item->ItemID == '')
        {
            echo 'ERROR: Invalid Item ID...<a href="javascript:history.back()">Back</a>';
            exit();
        }
        if (!$save)
        {
            $this->db->select('e_title');
            $this->db->where('e_id', (int) $id);
            $query = $this->db->get('ebay');
            if ($query->num_rows() > 0)
                $etitle = $query->row_array();
            else
            {
                echo 'ERROR: Invalid Site ID...<a href="javascript:history.back()">Back</a>';
                exit();
            }
            if (!isset($_POST['eid']))
                echo '

						<table cellpadding="2" cellspacing="2" border="0">

						<tr><td><strong>LaTronics Title:</strong></td><td>' . $etitle['e_title'] . '</td></tr>

						<tr><td><strong>eBay Title:</strong></td><td>' . (string) $xml->Item->Title . '</td></tr>

						<tr><td colspan="2"><br><strong>These values will be updated:</strong><Br></td></tr>

						<tr><td>eBay Title:</td><td>' . (string) $xml->Item->Title . '</td></tr>

						<tr><td>eBay Item ID:</td><td>' . (string) $xml->Item->ItemID . '</td></tr>						

						<tr><td>eBay Price:</td><td>' . (string) $xml->Item->StartPrice . '</td></tr>

						<tr><td>eBay Primary Category:</td><td>' . (string) $xml->Item->PrimaryCategory->CategoryName . '</td></tr>

						

						';
            //<tr><td>eBay Quantity (Quantity - Sold):</td><td>'.((int)$xml->Item->Quantity-(int)$xml->Item->SellingStatus->QuantitySold).' ( Quantity: '.(int)$xml->Item->Quantity.' / Sold: '.(int)$xml->Item->SellingStatus->QuantitySold.' )</td></tr>
        }
        $this->load->helper('directory');
        $this->load->helper('file');
        /*$responseXml = read_file($this->config->config['ebaypath'].'/cats.txt');
        $store = simplexml_load_string($responseXml);
        $StoreCategoryID = (string)$xml->Item->Storefront->StoreCategoryID;
        */
        $this->db->where("notebay", 0);
        $this->db->orderby('listorder', 'ASC');
        $categories = $this->db->get("warehouse_sku_categories")->result_array();
        $this->mysmarty->assign('dbstore', $categories);
        //$this->cs = array();
        //$this->_storecatting($store->Store->CustomCategories->CustomCategory);
        //!@#///
        if (!$save && isset($categories[(string) $xml->Item->Storefront->StoreCategoryID]))
            echo '<tr><td>eBay Store Category:</td><td>' . $categories[(string) $xml->Item->Storefront->StoreCategoryID]['wsc_title'] . '</td></tr>';
        elseif (!$save && !isset($categories[(string) $xml->Item->Storefront->StoreCategoryID]))
            echo '<tr><td>eBay Store Category:</td><td><span style="color:red;">NOT FOUND IN LOCAL STORE CATS (ID MISMATCH)</span> - ' . (string) $xml->Item->Storefront->StoreCategoryID . ' (StoreCat ID will NOT be updated. Please edit manually.)</td></tr>';
        if (!$save)
            echo '</table>';
        if (!$save)
            echo '<br><br><span style="color:red;">IS THIS CORRECT ?</span><br><form method="post" action="' . Site_url() . 'Myebay/UpdateFromEbay/' . (int) $id . '/' . (int) $page . '/TRUE"><input type="hidden" name="itemid" value="' . (string) $xml->Item->ItemID . '" /><input type="submit" value="YES" />&nbsp;&nbsp;<a href="' . Site_url() . 'Myebay/ListItems/' . (int) $page . '/#' . (int) $id . '">NO</a></form>';
        else
        {
            $data = array(
                'ebay_id' => (string) $xml->Item->ItemID,
                'e_title' => (string) $xml->Item->Title,
                'ebay_submitted' => 'Manual @ ' . CurrentTime() . ' by ' . $this->session->userdata['ownnames'],
                'pCTitle' => (string) $xml->Item->PrimaryCategory->CategoryName,
                'PrimaryCategory' => (string) $xml->Item->PrimaryCategory->CategoryID,
                'quantity' => ((int) $xml->Item->Quantity - (int) $xml->Item->SellingStatus->QuantitySold),
                'ebayquantity' => ((int) $xml->Item->Quantity - (int) $xml->Item->SellingStatus->QuantitySold),
                //'startPrice' => (string)$xml->Item->StartPrice,
                'price_ch2' => floater((string) $xml->Item->StartPrice),
                'ebended' => NULL
            );
            if ((string) $xml->Item->SellingStatus->ListingStatus != 'Active')
            {
                $data['ebended']  = 'Linked Listing Ended';
                $data['sitesell'] = 0;
            }
            if ($save && isset($categories[(string) $xml->Item->Storefront->StoreCategoryID]))
            {
                $data['storeCatID']    = (string) $xml->Item->Storefront->StoreCategoryID;
                $data['storeCatTitle'] = $categories[(string) $xml->Item->Storefront->StoreCategoryID]['wsc_title'];
            }
            if ($save)
            {
                $this->ReWaterMark((int) $id);
                $this->db->select('e_id, price_ch2');
                $this->db->where('e_id', (int) $id);
                //$this->db->where('ebay_submitted', NULL);
                //$this->db->where('ebay_id', 0);
                $query = $this->db->get('ebay');
                if ($query->num_rows() > 0)
                {
                    $ebr = $query->row_array();
                    $this->db->update('ebay', $data, array(
                        'e_id' => $ebr['e_id']
                    ));
                    $hmsg = array(
                        'msg_title' => 'Item ' . (int) $id . ' Manualy Linked (ItemID/Quantity/Price/Categories/StoreCat) with eBay ItemID',
                        'msg_body' => '',
                        'msg_date' => CurrentTime(),
                        'e_id' => $ebr['e_id'],
                        'itemid' => $data['ebay_id'],
                        'trec' => 0,
                        'admin' => $this->session->userdata['ownnames'],
                        'sev' => ''
                    );
                    //$this->_logaction('UpdateFromEbay', 'Q',array('quantity' => $ebr['quantity']), array('quantity' => (string)$xml->Item->Quantity), $ebr['e_id'], $data['ebay_id'], 0);
                    //$this->_logaction('UpdateFromEbay', 'Q',array('ebayquantity' => $ebr['quantity']), array('ebayquantity' => (string)$xml->Item->Quantity), $ebr['e_id'], $data['ebay_id'], 0);
                    $this->_logaction('UpdateFromEbay', 'M', array(
                        'price' => $ebr['price_ch2']
                    ), array(
                        'price' => (string) $xml->Item->StartPrice
                    ), $ebr['e_id'], $data['ebay_id'], 0);
                    foreach ($data as $k => $v)
                    {
                        if (isset($ebr[$k]) && $ebr[$k])
                            $olddata = (string) $ebr[$k];
                        else
                            $olddata = '';
                        if ($k == 'e_part')
                            $latp = 'B';
                        elseif ($k == 'e_qpart')
                            $latp = 'B';
                        elseif ($k == 'quantity')
                            $latp = 'Q';
                        else
                            $latp = 'M';
                        if ($k != 'PaymentMethod' && $k != 'shipping' && $k != 'price_ch2' && $k != 'Submitted')
                            $this->_logaction('RelinkFromEbay', $latp, array(
                                $k => $olddata
                            ), array(
                                $k => $v
                            ), (int) $ebr['e_id'], $data['ebay_id'], 0);
                    }
                    $this->db->insert('admin_history', $hmsg);
                    GoMail($hmsg, $this->config->config['support_email'], $this->config->config['no_reply_email']);
                    if (!isset($_POST['eid']))
                    {
                        $this->session->set_flashdata('success_msg', 'Item ' . (int) $id . ' Manualy Linked with eBay ItemID ' . $data['ebay_id']);
                        $this->session->set_flashdata('action', (int) $id);
                    }
                    if ((int) $page = 1)
                    {
                        $this->session->set_userdata('last_string', (int) $id);
                        $this->session->set_userdata('last_where', 3);
                    }
                    Redirect('Myebay#' . (int) $id);
                }
                else
                {
                    if (isset($_POST['eid']))
                        echo 1;
                    else
                        echo 'ERROR. Not found';
                }
            }
        }
    }
    function RefreshFromEbay($id, $itemid, $save = false)
    {
        $this->Auth_model->CheckListings();
        if ($itemid == '')
        {
            echo 'ERROR: Empty Item ID...<a href="javascript:history.back()">Back</a>';
            exit();
        }
        set_time_limit(90);
        ini_set('mysql.connect_timeout', 90);
        ini_set('max_execution_time', 90);
        ini_set('default_socket_timeout', 90);
        require_once($this->config->config['ebaypath'] . 'get-common/keys.php');
        require_once($this->config->config['ebaypath'] . 'get-common/eBaySession.php');
        $requestXmlBody = '<?xml version="1.0" encoding="utf-8" ?>';
        $requestXmlBody .= '<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">

';
        $requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
        $requestXmlBody .= '<DetailLevel>ItemReturnAttributes</DetailLevel>';
        $requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
        $requestXmlBody .= "<Version>$compatabilityLevel</Version>";
        $requestXmlBody .= '<ItemID>' . (int) $itemid . '</ItemID>

						</GetItemRequest>';
        $verb        = 'GetItem';
        $session     = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
        $responseXml = $session->sendHttpRequest($requestXmlBody);
        if (stristr($responseXml, 'HTTP 404') || $responseXml == '')
            die('<P>Error sending request');
        $xml = simplexml_load_string($responseXml);
        if ((string) $xml->Item->ItemID == '')
        {
            echo 'ERROR: Invalid Item ID...<a href="javascript:history.back()">Back</a>';
            exit();
        }
        if (!$save)
        {
            $this->db->where('e_id', (int) $id);
            $query = $this->db->get('ebay');
            if ($query->num_rows() > 0)
                $e = $query->row_array();
            else
            {
                echo 'ERROR: Invalid Site ID...<a href="javascript:history.back()">Back</a>';
                exit();
            }
            //$this->load->helper('directory');
            //$this->load->helper('file');
            /*
            $responseXml = read_file($this->config->config['ebaypath'].'/cats.txt');
            
            $store = simplexml_load_string($responseXml);
            */
            $this->db->where("noebay", 0);
            $this->db->orderby('listorder', 'ASC');
            $categories = $this->db->get("warehouse_sku_categories")->result_array();
            $this->mysmarty->assign('dbstore', $categories);
            echo 'Compare and confirm:

						<table cellpadding="2" cellspacing="2" border="1">

						<tr><th>Field</th><th>La-tronics Database</th><th>eBay Database</th></tr>

						

						<tr>

						<td>Title</td>

						<td>' . $e['e_title'] . '</td>

						<td>' . (string) $xml->Item->Title . '</td>

						</tr>

						<tr>

						<td>Quantity</td>

						<td>' . $e['quantity'] . '</td>

						<td>' . (string) $xml->Item->Quantity . '</td>

						</tr>

						<tr>

						<td>Price:</td>

						<td>' . $e['buyItNowPrice'] . '</td>

						<td>' . (string) $xml->Item->StartPrice . '</td>

						</tr>

						<tr>

						<td>Primary Category</td>

						<td>' . $e['pCTitle'] . ' (' . $e['primaryCategory'] . ')</td>

						<td>' . (string) $xml->Item->PrimaryCategory->CategoryName . ' (' . (string) $xml->Item->PrimaryCategory->CategoryID . ')</td>

						</tr>

						<tr>

						<td>Store Category</td>

						<td>' . $e['storeCatID'] . '</td>

						<td>' . (string) $xml->Item->Storefront->StoreCategoryID . '</td>

						</tr>

						<tr>

						<td>ListingDuration</td>

						<td>' . $e['listingDuration'] . '</td>

						<td>' . (string) $xml->Item->ListingDuration . '</td>

						</tr>

						<!--<tr>

						<td>PaymentMethods</td>

						<td>' . $e['PaymentMethod'] . '</td>

						<td>' . $xml->Item->PaymentMethods . '</td>

						</tr>

						<tr>

						<td>Shipping</td>

						<td>' . $e['shipping'] . '</td>

						<td>' . $xml->Item->ShippingDetails . '</td>

						</tr>-->

				</table>

						';
            /*
            
            //ShippingDetails
            
            [ShippingServiceOptions] => Array
            
            (
            
            [0] => SimpleXMLElement Object
            
            (
            
            [ShippingService] => USPSPriorityFlatRateEnvelope
            
            [ShippingServiceCost] => 0.0
            
            [ShippingServicePriority] => 1
            
            [ExpeditedService] => false
            
            [ShippingTimeMin] => 2
            
            [ShippingTimeMax] => 3
            
            [FreeShipping] => true
            
            )
            
            
            
            [1] => SimpleXMLElement Object
            
            (
            
            [ShippingService] => USPSExpressFlatRateEnvelope
            
            [ShippingServiceCost] => 35.0
            
            [ShippingServicePriority] => 2
            
            [ExpeditedService] => true
            
            [ShippingTimeMin] => 1
            
            [ShippingTimeMax] => 1
            
            )
            
            
            
            )
            
            
            
            [InternationalShippingServiceOption] => Array
            
            (
            
            [0] => SimpleXMLElement Object
            
            (
            
            [ShippingService] => USPSPriorityMailInternationalFlatRateEnvelope
            
            [ShippingServiceCost] => 35.0
            
            [ShippingServicePriority] => 1
            
            [ShipToLocation] => Worldwide
            
            )
            
            
            
            [1] => SimpleXMLElement Object
            
            (
            
            [ShippingService] => USPSPriorityMailInternationalFlatRateEnvelope
            
            [ShippingServiceCost] => 30.0
            
            [ShippingServicePriority] => 2
            
            [ShipToLocation] => CA
            
            )
            
            
            
            [2] => SimpleXMLElement Object
            
            (
            
            [ShippingService] => USPSExpressMailInternationalFlatRateEnvelope
            
            [ShippingServiceCost] => 47.0
            
            [ShippingServicePriority] => 3
            
            [ShipToLocation] => Worldwide
            
            )
            
            
            
            [3] => SimpleXMLElement Object
            
            (
            
            [ShippingService] => USPSExpressMailInternationalFlatRateEnvelope
            
            [ShippingServiceCost] => 35.0
            
            [ShippingServicePriority] => 4
            
            [ShipToLocation] => CA
            
            )
            
            
            
            )*/
            echo '<br><br><span style="color:red;">IS THIS CORRECT ?</span><br><form method="post" action="' . Site_url() . 'Myebay/UpdateFromEbay/' . (int) $id . '/' . (int) $itemid . '/TRUE"><input type="submit" value="YES" />&nbsp;&nbsp;<a href="' . Site_url() . 'Myebay#' . (int) $id . '">NO</a></form>';
        }
        else
        {
            $data = array(
                'pCTitle' => (string) $xml->Item->PrimaryCategory->CategoryName,
                'PrimaryCategory' => (string) $xml->Item->PrimaryCategory->CategoryID,
                'quantity' => (string) $xml->Item->Quantity,
                'ebayquantity' => (string) $xml->Item->Quantity,
                //'startPrice' => (string)$xml->Item->StartPrice,
                'price_ch2' => floater((string) $xml->Item->StartPrice),
                'e_title' => (string) $xml->Item->Title,
                //'quantity' => (string)$xml->Item->Quantity,
                'ebayquantity' => (string) $xml->Item->Quantity,
                'primaryCategory' => (string) $xml->Item->PrimaryCategory->CategoryID,
                'pCTitle' => (string) $xml->Item->PrimaryCategory->CategoryName,
                'storeCatID' => (string) $xml->Item->Storefront->StoreCategoryID,
                'listingDuration' => (int) $xml->Item->ListingDuration,
                'PaymentMethod' => $xml->Item->PaymentMethods,
                'shipping' => $xml->Item->ShippingDetails
            );
            // GET STORE CATEGORIES
            if (isset($categories[(string) $xml->Item->Storefront->StoreCategoryID]))
            {
                $data['storeCatID']    = (string) $xml->Item->Storefront->StoreCategoryID;
                $data['storeCatTitle'] = $categories[(string) $xml->Item->Storefront->StoreCategoryID]['wsc_title'];
            }
            if ($save)
            {
                $this->db->where('e_id', (int) $id);
                $query = $this->db->get('ebay');
                if ($query->num_rows() > 0)
                {
                    $ebr = $query->row_array();
                    $this->db->update('ebay', $data, array(
                        'e_id' => $ebr['e_id'],
                        'ebay_id' => (string) $xml->Item->ItemID
                    ));
                    foreach ($data as $k => $v)
                    {
                        if (isset($ebr[$k]) && $ebr[$k])
                            $olddata = (string) $ebr[$k];
                        else
                            $olddata = '';
                        if ($k == 'e_part')
                            $latp = 'B';
                        elseif ($k == 'e_qpart')
                            $latp = 'B';
                        elseif ($k == 'quantity')
                            $latp = 'Q';
                        else
                            $latp = 'M';
                        if ($k != 'PaymentMethod' && $k != 'shipping' && $k != 'price_ch2')
                            $this->_logaction('RefreshFromEbay', $latp, array(
                                $k => $olddata
                            ), array(
                                $k => $v
                            ), (int) $ebr['e_id'], $ebr['ebay_id'], 0);
                    }
                    //UPDATE WHERE ITEM ID = ITEM ID
                    $hmsg = array(
                        'msg_title' => 'Listing Refreshed from eBay',
                        'msg_body' => '',
                        'msg_date' => CurrentTime(),
                        'e_id' => $ebr['e_id'],
                        'itemid' => (string) $xml->Item->ItemID,
                        'trec' => 0,
                        'admin' => $this->session->userdata['ownnames'],
                        'sev' => ''
                    );
                    $this->db->insert('admin_history', $hmsg);
                    GoMail($hmsg, $this->config->config['support_email'], $this->config->config['no_reply_email']);
                    $this->session->set_flashdata('success_msg', 'Item ' . (int) $id . ' Refreshed from eBay ItemID ' . $data['ebay_id']);
                    $this->session->set_flashdata('action', (int) $id);
                }
                else
                {
                    $this->session->set_flashdata('error_msg', 'Item ' . (int) $id . ' NOT Refreshed');
                }
                Redirect('Myebay#' . (int) $id);
            }
        }
    }
    function UpdateASC($rec = '', $itemid = '')
    {
        $this->Auth_model->CheckOrders();
        set_time_limit(180);
        ini_set('mysql.connect_timeout', 180);
        ini_set('max_execution_time', 180);
        ini_set('default_socket_timeout', 180);
        require_once($this->config->config['ebaypath'] . 'get-common/keys.php');
        require_once($this->config->config['ebaypath'] . 'get-common/eBaySession.php');
        $verb           = 'GetSellingManagerSaleRecord';
        //Create a new eBay session with all details pulled in from included keys.php
        $session        = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
        $requestXmlBody = '<?xml version="1.0" encoding="utf-8" ?>';
        $requestXmlBody .= '<GetSellingManagerSaleRecordRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        $requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
        $requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';
        $requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
        $requestXmlBody .= '<ErrorHandling>BestEffort</ErrorHandling>';
        $requestXmlBody .= "<ItemID>" . (int) $itemid . "</ItemID>";
        $requestXmlBody .= "<TransactionID>" . (int) $rec . "</TransactionID>";
        $requestXmlBody .= "<Version>$compatabilityLevel</Version>";
        $requestXmlBody .= '</GetSellingManagerSaleRecordRequest>';
        //send the request and get response
        $responseXml = $session->sendHttpRequest($requestXmlBody);
        if (stristr($responseXml, 'HTTP 404') || $responseXml == '')
            die('<P>Error sending request');
        $xml  = simplexml_load_string($responseXml);
        $item = $xml->SellingManagerSoldOrder;
        printcool($xml);
        break;
        //printcool ($t);
        $cascupd = 0;
        if ($item)
        {
            if ((string) $item->ActualShippingCost != $t['asc'])
            {
                //$echo .= "Updating   $t[itemid]   $t[et_id]   ";
                //$echo .= (string)$item->ActualShippingCost . ' - '.$t['asc'].' <br/>';
                $this->db->update('ebay_transactions', array(
                    'asc' => floater((string) $item->ActualShippingCost)
                ), array(
                    'et_id' => $t['et_id']
                ));
                $this->db->select('wid, bcn, ' . $this->Myseller_model->sellingfields());
                $this->db->where('channel', 1);
                $this->db->where('sold_id', $t['et_id']);
                $this->db->where('vended', 1);
                $f = $this->db->get('warehouse');
                if ($f->num_rows() > 0)
                {
                    $fr = $f->result_array();
                    foreach ($fr as $fl)
                    {
                        if ($fl['vended'] == 1)
                            $this->Myseller_model->HandleBCN(array(
                                'shipped_actual' => floater((string) $item->ActualShippingCost)
                            ), $fl);
                    }
                }
                $this->_logaction('Transactions', 'B', array(
                    'ActShipCost' => $t['asc']
                ), array(
                    'ActShipCost' => floater((string) $item->ActualShippingCost)
                ), 0, $t['itemid'], $t['rec']);
                $updatedIds .= $t['rec'] . ', ';
                $change++;
                $cascupd = 1;
            }
            $ar = $this->_XML2Array($item->OrderStatus);
            if (isset($ar['OrderStatus']))
                $ar = $ar['OrderStatus'];
            if (isset($ar['PaidTime']) && (CleanBadDate((string) $ar['PaidTime']) != $t['paidtime']) && (CleanBadDate((string) $ar['PaidTime']) != ''))
            {
                $this->db->update('ebay_transactions', array(
                    'paidtime' => CleanBadDate((string) $ar['PaidTime'])
                ), array(
                    'et_id' => $t['et_id']
                ));
                $this->_logaction('Transactions', 'B', array(
                    'PaidTime' => $t['paidtime']
                ), array(
                    'PaidTime' => CleanBadDate((string) $ar['PaidTime'])
                ), 0, $t['itemid'], $t['rec']);
            }
            if (isset($ar['paidtime']))
                unset($ar['paidtime']);
            $pd = serialize($ar);
            if ($pd != $t['paydata'])
                $this->db->update('ebay_transactions', array(
                    'paydata' => $pd
                ), array(
                    'et_id' => $t['et_id']
                ));
            if ($cascupd == 1)
                $this->db->update('ebay_transactions', array(
                    'cascupd' => 1
                ), array(
                    'et_id' => $t['et_id']
                ));
            unset($item);
        }
        if ($change > 0)
            $this->db->insert('admin_history', array(
                'msg_type' => 1,
                'msg_title' => 'Actual Shipping Cost Updated',
                'msg_body' => 'Updated: ' . rtrim($updatedIds, ', '),
                'msg_date' => CurrentTime(),
                'admin' => 'Auto',
                'sev' => ''
            ));
        //$echo .= "</body></html>";
        // write_file($file, $echo);
        // $this->mysmarty->assign("info", $echo);
        // $this->mysmarty->assign("ids", $updatedIds);
        //$this->mysmarty->view('myebay/khim.html');
    }
    function Resetcascupd($id = '')
    {
        if ((int) $id > 0)
        {
            $this->db->update('ebay_transactions', array(
                'cascupd' => 0,
                'asc' => '0.00'
            ), array(
                'et_id' => $id
            ));
            echo 1;
        }
    }
    function CleanUpActionLog()
    {
        $this->db->select('al_id, field, datafrom, datato');
        //$this->db->like('field', 'part');
        $this->db->where('field', 'sold');
        $query = $this->db->get('ebay_actionlog');
        if ($query->num_rows() > 0)
        {
            $e = $query->result_array();
            foreach ($e as $m)
            {
                //$this->db->update('ebay_actionlog', array('atype' => 'B'), array('al_id' => (int)$m['al_id']));
            }
            printcool($e);
        }
    }
    function GetSuggestedCategories($searchstring = '')
    {
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
        $verb           = 'GetSuggestedCategories';
        //Create a new eBay session with all details pulled in from included keys.php
        $session        = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
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
        $xml  = simplexml_load_string($responseXml);
        $cats = $xml->SuggestedCategoryArray->SuggestedCategory;
        echo '<select id="primaryCategory" name="primaryCategory">';
        foreach ($cats as $c)
        {
            $c      = $this->_XML2Array($c);
            $c      = $c['Category'];
            $cgcstr = '';
            echo '<option value="' . $c['CategoryID'] . '">';
            if (isset($c['CategoryParentID']) && (count($c['CategoryParentID']) > 0))
            {
                /*foreach ($c['CategoryParentID'] as $k => $v)
                
                {
                
                echo $c['CategoryParentName'][$k].' <strong>&rArr;</strong> ';		//('.$v.')
                
                }*/
                if (is_array($c['CategoryParentID']))
                    foreach ($c['CategoryParentName'] as $v)
                    {
                        $cgcstr .= $v . ' <strong>&rArr;</strong> ';
                        echo $v . ' <strong>&rArr;</strong> ';
                    } //('.$v.')
                else
                {
                    $cgcstr .= $c['CategoryParentName'] . ' <strong>&rArr;</strong> ';
                    echo $c['CategoryParentName'] . ' <strong>&rArr;</strong> ';
                }
            }
            //echo '<strong><input onlick="javascript:void(0)" onClick="SaveShipping('.(int)$c['CategoryID'].', '.$c['CategoryName'].', '.$searchstring.')" type="radio" id="'.(int)$c['CategoryID'].'" value="'.$c['CategoryName'].'" name="primaryCategory" /> <label for="'.(int)$c['CategoryID'].'"></label></strong>
            $cgcstr .= $c['CategoryName'];
            echo '<strong>' . $c['CategoryName'] . '</strong>';
            echo '</option>';
            $gotcats[] = array(
                'catID' => $c['CategoryID'],
                'catName' => $cgcstr
            );
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
        if (isset($gotcats) && (count($gotcats) > 0))
            $this->session->set_userdata(array(
                'gotcats' => $gotcats
            ));
    }
    function SaveSuggestedCategories($catid, $catname, $searchstring)
    {
        echo '<input id="catsearch" name="catsearch" value="' . $searchstring . '" style="width:250px;">&nbsp;&nbsp;<a href="javascript:void(0)" onClick="var csrc = document.getElementById(\'catsearch\').value; SelectShipping(csrc)"><img src="' . base_url() . 'images/admin/b_search.png" /> Get eBay Suggested</a><br><br>';
        echo '<select id="primaryCategory" name="primaryCategory">

      <option value="' . $catid . '">' . $catname . '</option></select>';
    }
    function GetSiteXML($display = false)
    {
        $this->load->library('htmltotext');
        $this->productlist = $this->Myebay_model->ListXMLItems();
        $feed              = '<?xml version="1.0"?>

<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">

<channel>

<title>' . $this->config->config['sitename'] . '</title>

<link>' . $this->config->config['base_url'] . '</link>

<description>La-Tronics</description>';
        if ($display)
            $this->mysmarty->assign('total', '<strong>' . count($this->productlist) . ' total products in feed</strong><br><br>');
        //$countnodesc = 0;
        //$countdeschtml = '';
        $this->err = false;
        foreach ($this->productlist as $key => $value)
        {
            /*Remove “Original, Tested, New, Grade etc” from the google merchant feed titles
            
            
            
            If title has display and screen, remove the word display
            
            Same for adapter and charger, remove adapter
            
            */
            $value['e_title'] = str_replace('Original', '', str_replace('Tested', '', str_replace('Grade', '', str_replace('etc', '', $value['e_title']))));
            if (strpos($value['e_title'], 'display') !== false)
            {
                if (strpos($value['e_title'], 'screen') !== false)
                    $value['e_title'] = str_replace('display', '', $value['e_title']);
            }
            if (strpos($value['e_title'], 'adapter') !== false)
            {
                if (strpos($value['e_title'], 'charger') !== false)
                    $value['e_title'] = str_replace('adapter', '', $value['e_title']);
            }
            $value['e_title']                = str_replace(' ', ' ', $value['e_title']);
            $feedarray[$key]                 = "

<item>

<g:id>" . $value['e_id'] . "</g:id>

<g:title>" . substr(CleanXML(htmlspecialchars($value['e_title'])), 0, 150) . "</g:title>

";
            $dsc                             = htmlspecialchars(substr($this->htmltotext->go($value['e_desc']), 0, 5000));
            $this->productlist[$key]['dscl'] = $value['dscl'] = strlen($dsc);
            $feedarray[$key] .= "<g:description>" . $dsc . "</g:description>

";
            $value['e_part'] = bcndelim($value['e_part']);
            $feedarray[$key] .= "<g:link>" . Site_url() . 'storeitem/' . $value['e_id'] . "</g:link>

<g:image_link>" . Site_url() . $this->config->config['wwwpath']['imgebay'] . "/" . $value['idpath'] . "/Original_" . $value['e_img1'] . "</g:image_link>

<g:condition>" . googlecondition($value['Condition']) . "</g:condition>

<g:availability>" . googleavailability($value['qn_ch2']) . "</g:availability>

<g:price>" . $value['price_ch2'] . " USD</g:price>

<g:brand>" . htmlspecialchars(trim($value['e_manuf'])) . "</g:brand>

<g:mpn>" . htmlspecialchars(trim($value['e_part'])) . "</g:mpn>

<g:gtin>" . htmlspecialchars(trim($value['upc'])) . "</g:gtin>

";
            $snm = 0;
            if (trim($value['e_manuf']) == '')
                $snm++;
            if (trim($value['e_part']) == '')
                $snm++;
            if (trim($value['upc']) == '')
                $snm++;
            if ($snm >= 2)
                $feedarray[$key] .= "<g:identifier_exists>FALSE</g:identifier_exists>";
            $feedarray[$key] .= "<g:google_product_category>" . str_replace('& ', '&amp; ', $value['gtaxonomy']) . "</g:google_product_category>	

<g:product_type>" . str_replace('& ', '&amp;', $value['gtaxonomy']) . "</g:product_type>	

<g:shipping_weight>" . $value['weight_kg'] . " kg</g:shipping_weight>

</item>";
            $move = 0;
            if ($snm >= 2)
            {
                $this->err['brandmpcupc'][] = $value;
                $move                       = 1;
            }
            if ($dsc == '')
            {
                $this->err['desc'][] = $value;
                $move                = 1;
            }
            if ($move > 0)
            {
                unset($this->productlist[$key]);
                unset($feedarray[$key]);
            }
        }
        foreach ($feedarray as $f)
        {
            $feed .= $f;
        }
        $feed .= '

		</channel>

				</rss>';
        //$feed = str_replace('{br}', '<br>', $feed);
        //$feed = $this->htmltotext->go($feed);
        //$feed = str_replace('&lt;', '<', $feed);
        //$feed = str_replace('&lt;', '<', $feed);
        //$feed = str_replace('&lt;br&gt;', '<br>', $feed);
        //$feed = str_replace('&quot;', '"', $feed);
        $this->load->helper('file');
        //delete_files($this->config->config['paths']['xml']);
        if (file_exists($this->config->config['paths']['feeds'] . '/feed.xml'))
            unlink($this->config->config['paths']['feeds'] . '/feed.xml');
        write_file($this->config->config['paths']['feeds'] . '/feed.xml', $feed);
        if (!file_exists($this->config->config['paths']['feeds'] . '/index.html'))
            write_file($this->config->config['paths']['feeds'] . '/index.html', ' :) ');
        if ($display)
        {
            //echo '<strong>Products without description: '.$countnodesc.'</strong><br><br>';
            //echo $countdeschtml;
            $this->mysmarty->assign('list', $this->productlist);
            $this->mysmarty->assign('err', $this->err);
            $this->mysmarty->view('myebay/myebay_xmldebug.html');
        }
        //$this->load->helper('download');
        //$name = 'products_'.(int)$catid.'.xml';
        //force_download($name, $feed);
    }
    function unzipebay()
    {
        $zip = new ZipArchive;
        $res = $zip->open('ebay_images/ebi1.zip');
        var_dump($res);
        if ($res === TRUE)
        {
            $zip->extractTo('ebay_images/');
            $zip->close();
            echo 'DONE!';
        }
        else
            echo 'Error';
    }
    function getnullidpath()
    {
        $this->db->select("e_id, e_img1, e_img2, e_img3, e_img1, idpath");
        $this->db->where('idpath', NULL);
        $this->query = $this->db->get('ebay');
        printcool($this->query->result_array());
    }
    function ApplyItemSpecToActive()
    {
        set_time_limit(900);
        ini_set('mysql.connect_timeout', 900);
        ini_set('max_execution_time', 900);
        ini_set('default_socket_timeout', 900);
        $this->db->select('e_id');
        $this->db->where("ebended", NULL);
        $this->db->where('ebay_id !=', 0);
        $this->db->where('housekeeping', 0);
        $this->db->where('eBay_specs', null);
        $this->db->limit(500);
        $res = $this->db->get('ebay');
        printcool('ROWS ' . $res->num_rows());
        if ($res->num_rows() > 0)
        {
            foreach ($res->result_array() as $revs)
            {
                $this->PopulateItemSpecifics((int) $revs['e_id']);
                $this->db->update('ebay', array(
                    'housekeeping' => 1
                ), array(
                    'e_id' => (int) $revs['e_id']
                ));
                printcool((int) $revs['e_id']);
            }
        }
    }
    function PopulateItemSpecifics($eid = 0)
    {
        $item = $this->Myebay_model->GetItem((int) $eid);
        require($this->config->config['ebaypath'] . 'get-common/keys.php');
        require_once($this->config->config['ebaypath'] . 'get-common/eBaySession.php');
        $this->load->model('Myebay_model');
        $requestXmlBody = '<?xml version="1.0" encoding="utf-8" ?>';
        $requestXmlBody .= '<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        $requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
        $requestXmlBody .= '<DetailLevel>ItemReturnAttributes</DetailLevel>';
        $requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
        $requestXmlBody .= "<Version>$compatabilityLevel</Version>";
        $requestXmlBody .= "<IncludeItemSpecifics>true</IncludeItemSpecifics>";
        $requestXmlBody .= '<ItemID>' . (int) $item['ebay_id'] . '</ItemID></GetItemRequest>';
        //$requestXmlBody .= '<ItemID>'.'172186226202'.'</ItemID></GetItemRequest>';
        $verb        = 'GetItem';
        $session     = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $this->config->config['ebaysiteid'], $verb);
        $responseXml = $session->sendHttpRequest($requestXmlBody);
        if (stristr($responseXml, 'HTTP 404') || $responseXml == '')
        {
            log_message('error', 'function PopulateItemSpecifics - GetItem doesn\'t return eBay item to update upc e_id=  ' . (int) $id . ' @ ' . CurrentTime());
            GoMail(array(
                'msg_title' => 'function PopulateItemSpecifics - GetItem doesnt return eBay item to update upc e_id=  ' . (int) $id . ' @ ' . CurrentTime(),
                'msg_body' => '',
                'msg_date' => CurrentTime()
            ), $this->config->config['support_email'], $this->config->config['no_reply_email']);
        }
        $xml      = simplexml_load_string($responseXml);
        //$xml->Item->ItemSpecifics->NameValueList;
        // printcool($xml->Item->ItemSpecifics);
        $itemspec = array();
        if (isset($xml->Item->ItemSpecifics->NameValueList))
            foreach ($xml->Item->ItemSpecifics->NameValueList as $v)
            {
                $itemspec[(string) $v->Name] = (string) $v->Value;
            }
        if (count($itemspec) > 0 && strlen(serialize($itemspec)) > 10)
        {
            GoMail(array(
                'msg_title' => 'YES Item Specifics for Listing ' . (int) $eid,
                'msg_body' => printcool($itemspec, true),
                'msg_date' => CurrentTime()
            ), $this->config->config['support_email'], $this->config->config['no_reply_email']);
            $this->db->update('ebay', array(
                'eBay_specs' => serialize($itemspec)
            ), array(
                'e_id' => (int) $eid
            ));
            echo 'Returned Item Specifics for listing ' . $eid . '<br><br>';
            foreach ($itemspec as $k => $v)
                echo '<strong>' . $k . ':</strong> ' . $v . '<br><br>';
        }
        else
        {
            echo 'Returned Item Specifics for listing ' . $eid . '<br><br>None.';
            GoMail(array(
                'msg_title' => 'No Item Specifics for Listing ' . (int) $eid,
                'msg_body' => printcool($itemspec, true),
                'msg_date' => CurrentTime()
            ), $this->config->config['support_email'], $this->config->config['no_reply_email']);
        }
    }
}