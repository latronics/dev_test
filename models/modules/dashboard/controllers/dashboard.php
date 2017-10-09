<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * InvoicePlane
 * 
 * A free and open source web based invoicing system
 *
 * @package		InvoicePlane
 * @author		Kovah (www.kovah.de)
 * @copyright	Copyright (c) 2012 - 2015 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 * 
 */

class Dashboard extends Admin_Controller {
    /* public function create_ticket(){

      $data = array(
      'id_client_on_turn' => $this->input->post('radio'),

      );
      $this->db->insert('ip_client_to_ticket', $data);




      } */

    public function new_clients() {
        ?>
        <table class="table table-bordered table-condensed no-margin"> 
            <?php
            $i = 0;


            //foreach ($result_object as $total) {   






            $this->db->select("client_name, client_id");
            $this->db->from("ip_clients");
            $this->db->where("new_client = '1'");
            $result_object = $this->db->get();
            $result = $result_object->result_array();
            $rows = $result_object->num_rows();
            if ($rows == '') {
                echo "<tr><td align = 'center'><i><font size = '2'>No new clients</font></i></td></tr>";
            }
            while ($i < $rows) {
                ?>

                <tr ><td>


                        <a href ="#" onclick="getValue(<?php echo $result[$i]["client_id"]; ?>);"> <?php echo $result[$i]["client_name"]; ?> </a>
                        <?php
                        $i++;
                    }
                }

                public function update_tickets() {
                    $this->load->helper('url');
                    ?>
                    <link rel="stylesheet" type="text/css" href=“<?php echo base_url('assets/default/css/style.css'); ?>">

                    <div id="panel-recent-quotes" class="panel panel-default" >

                        <div class="panel-heading">
                            <b><i class="fa fa-history fa-margin"></i> <?php echo lang('recent_quotes'); ?></b>
                        </div>
                        <div class="table-responsive" >
                            <table class="table table-striped table-condensed no-margin">
                                <thead><tbody>
                                    <tr>
                                        <th><?php echo lang('status'); ?></th>
                                        <th style="min-width: 15%;"><?php echo lang('date'); ?></th>
                                        <th style="min-width: 15%;"><?php echo lang('quote'); ?></th>
                                        <th style="min-width: 35%;"><?php echo lang('client'); ?></th>
                                        <th style="text-align: right;"><?php //echo lang('balance');      ?></th>
                                        <th><?php echo lang('pdf'); ?></th>
                                    </tr>
                                    </thead>

                                    <?php
                                    $this->db->select("*");
                                    $this->db->from("ip_quotes");
                                    $this->db->join('status', 'ip_quotes.quote_status_id = status.id');
                                    $this->db->join('ip_clients', 'ip_quotes.client_id = ip_clients.client_id');
                                    $this->db->order_by("quote_id", "desc");
                                    $this->db->limit("10");
                                    $result_quotes_query = $this->db->get();
                                    $result_quotes = $result_quotes_query->result_array();
                                    $x = 0;
                                    ?>
                                    <?php foreach ($result_quotes as $quotes) {
                                        ?>

                                        <tr><td><span class = "<?php echo $result_quotes[$x]['css_label']; ?> label"> 
                                                    <?php echo $result_quotes[$x]['status']; ?>

                                                </span></td>

                                            <td> <?php echo date_from_mysql($result_quotes[$x]['quote_date_created']); ?></td>
                                            <td> <?php echo anchor('quotes/view/' . $result_quotes[$x]['quote_id'], ($quote->quote_number ? $quote->quote_number : $result_quotes[$x]['quote_number'])); ?></td>
                                            <td > <?php echo anchor('clients/view/' . $result_quotes[$x]['client_id'], $result_quotes[$x]['client_name']); ?></td>
                                            <td colspan="2" style="text-align: center;">
                                                <a href="<?php echo site_url('quotes/generate_pdf/' . $result_quotes[$x]['quote_id']); ?>"
                                                   title="<?php echo lang('download_pdf'); ?>" target="_blank">
                                                    <i class="fa fa-print"></i>
                                                </a>
                                            </td>
                                        </tr>

                                        <?php
                                        $x++;
                                    }
                                    ?>
                                    <tr>
                                        <td colspan="6" class="text-right small">
                                            <?php echo anchor('quotes/status/all', lang('view_all')); ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>           


                    <?php
                }

                public function set_attention() {
                    $order_id = $this->input->post("order_id");
                    $this->db->set("attention", 1);
                    $this->db->where("quote_id", $order_id);
                    $this->db->update("ip_quotes");
                }

                public function setback_attention() {
                    $order_id = $this->input->post("order_id");
                    $this->db->set("attention", 0);
                    $this->db->where("quote_id", $order_id);
                    $this->db->update("ip_quotes");
                }

                public function index() {
                    $this->db->where("attention", 1);
                    $this->db->join("ip_clients", "ip_clients.client_id = ip_quotes.client_id");
                    $this->db->join("status", "ip_quotes.quote_status_id = status.id");
                    $attention_orders = $this->db->get("ip_quotes")->result_array();


                    $this->load->model('invoices/mdl_invoice_amounts');
                    $this->load->model('quotes/mdl_quote_amounts');
                    $this->load->model('invoices/mdl_invoices');
                    $this->load->model('quotes/mdl_quotes');

                    //$quote_overview_period = $this->mdl_settings->setting('quote_overview_period');
                    $invoice_overview_period = $this->mdl_settings->setting('invoice_overview_period');
                    //GET UNCOMPLETE ROWS

                   
                    
                    $this->layout->set(
                            array(
                                'invoice_status_totals' => $this->mdl_invoice_amounts->get_status_totals($invoice_overview_period),
                                'quote_status_totals' => $this->mdl_quote_amounts->get_status_totals($quote_overview_period),
                                'invoice_status_period' => str_replace('-', '_', $invoice_overview_period),
                                'quote_status_period' => str_replace('-', '_', $quote_overview_period),
                                'invoices' => $this->mdl_invoices->limit(10)->get()->result(),
                                'quotes' => $this->mdl_quotes->limit(10)->get()->result(),
                                'invoice_statuses' => $this->mdl_invoices->statuses(),
                                'quote_statuses' => $this->mdl_quotes->statuses(),
                                'overdue_invoices' => $this->mdl_invoices->is_overdue()->get()->result(),
                                'attention_orders' => $attention_orders
                                
                            )
                    );

                    $this->layout->buffer('content', 'dashboard/index');
                    $this->layout->render();
                }

            }
            