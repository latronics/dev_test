<div class="portlet-header" >
    <div class="col-xs-12">
        <div id="panel-quote-overview" class="panel panel-default" >
            <div class="panel-heading" style="cursor: move;">
                <b><i class="fa fa-history fa-margin"></i> <?php echo lang('recent_quotes'); ?></b> 
            </div>
            <div class="table-responsive" style="height: 330px;">
                <table class="table table-striped table-condensed no-margin">
                    <thead><tbody>
                        <tr>
                            <th><?php echo lang('status'); ?></th>
                            <th style="min-width: 15%;"><?php echo lang('date'); ?></th>
                            <th style="min-width: 15%;"><?php echo lang('quote'); ?></th>
                            <th style="min-width: 20%;"><?php echo lang('client'); ?></th>
                            <th style="min-width: 15%;"><?php echo 'Market'; ?></th>
                            <th><?php echo lang('pdf'); ?></th>
                        </tr>
                        </thead>

                        <?php
                        $aux = 0;
                        $this->db->select("*");
                        if ($display_data['all'] == 1) {
                            $this->db->where("ip_quotes.quote_date_created >= '" . $display_data['date_from'] . "'");
                            $this->db->where("ip_quotes.fraud", 0);
                        } else {
                            if ($display_data['numeric'] == 1) {
                            $this->db->where("ip_quotes.quote_number like ", "" . $display_data['input_data'] . "%");
                        }
                        else
                        {
                            $this->db->where("ip_clients.client_name like '%" . $display_data['input_data'] . "%'");
                            $this->db->where("ip_quotes.quote_date_created between '" . $display_data['date_from'] . "' and '" . $display_data['date_to'] . "'");
                        }
                            
                            $this->db->where("ip_quotes.fraud", 0);
                        }
                        
                        $this->db->join("ip_stores", "ip_stores.id = ip_quotes.store");
                        $this->db->join("ip_clients", "ip_clients.client_id = ip_quotes.client_id");
                        $quotes = $this->db->get("ip_quotes")->result_array();

                        foreach ($quotes as $quote) {
                            //GET CSS LABEL
                            $this->db->select("css_label,status");
                            $this->db->where("id", $quote['quote_status_id']);
                            $status_css_label = $this->db->get("status")->result_array();

                            //GET MARKET(STORE)
                            $this->db->select("*");
                            $this->db->where("id", $quote['store']);
                            $market_data = $this->db->get("ip_stores")->result_array();
                            ?>



                            <tr>
                                <td>
                                    <span class="label 
                                          <?php echo $status_css_label[0]['css_label']; ?>">
                                          <?php echo $status_css_label[0]['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo date("m/d/Y", strtotime($quote['quote_date_created'])); ?>
                                </td>
                                <td>
                                    <?php echo anchor('quotes/view/' . $quote['quote_id'], ($quote['quote_number'] ? $quote['quote_number'] : $quote['quote_id'])); ?>
                                </td>
                                <td>
                                    <?php echo anchor('clients/view/' . $quote['client_id'], $quote['client_name']); ?>
                                </td>
                                <td>
                                    <a href ="<?php echo site_url("stores"); ?>"> <?php echo $market_data[0]['store_name']; ?>
                                    </a>
                                </td>
                                <!-- <td class="amount">
                                <?php //echo format_currency($quote->quote_total);                  ?>
                                </td> -->
                                <td colspan="2" style="text-align: center;">
                                    <a href="<?php echo site_url('quotes/generate_pdf/' . $quote['quote_id']); ?>"
                                       title="<?php echo lang('download_pdf'); ?>" target="_blank">
                                        <i class="fa fa-print"></i>
                                    </a>
                                </td>
                            </tr>

                            <?php
                            $aux++;
                            if ($aux == 8) {
                                break;
                            }
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
        <div class="portlet-content">


        </div>
    </div>  
</div>