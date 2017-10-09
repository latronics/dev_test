
<div id="headerbar">

    <h1><?php echo lang('quotes'); ?></h1>

    <div class="pull-right">
        <button type="button" class="btn btn-default btn-sm submenu-toggle hidden-lg"
                data-toggle="collapse" data-target="#ip-submenu-collapse">
            <i class="fa fa-bars"></i> <?php echo lang('submenu'); ?>
        </button>
        <a class="create-quote btn btn-sm btn-primary" href="#">
            <i class="fa fa-plus"></i> <?php echo lang('new'); ?>
        </a>
    </div>



    <div class="pull-right visible-lg">
        <ul class="nav nav-pills index-options">
            <?php /* <li <?php if ($status == 'draft') { ?>class="active"<?php } ?>><a
              href="<?php echo site_url('quotes/status/draft'); ?>"><?php echo lang('draft'); ?></a></li>
              <li <?php if ($status == 'sent') { ?>class="active"<?php } ?>><a
              href="<?php echo site_url('quotes/status/sent'); ?>"><?php echo lang('sent'); ?></a></li>
              <li <?php if ($status == 'viewed') { ?>class="active"<?php } ?>><a
              href="<?php echo site_url('quotes/status/viewed'); ?>"><?php echo lang('viewed'); ?></a></li>
              <li <?php if ($status == 'approved') { ?>class="active"<?php } ?>><a
              href="<?php echo site_url('quotes/status/approved'); ?>"><?php echo lang('approved'); ?></a>
              </li>
              <li <?php if ($status == 'rejected') { ?>class="active"<?php } ?>><a
              href="<?php echo site_url('quotes/status/rejected'); ?>"><?php echo lang('rejected'); ?></a>
              </li>
              <li <?php if ($status == 'canceled') { ?>class="active"<?php } ?>><a
              href="<?php echo site_url('quotes/status/canceled'); ?>"><?php echo lang('canceled'); ?></a>
              </li> */ ?> 
           
                <li <?php if ($status == 'website') { ?>class="active"<?php } ?>><a
                        href="<?php echo site_url('quotes/status/website'); ?>"><?php echo "Website Orders"; ?></a>
                </li>
            
            <li <?php if ($status == 'all') { ?>class="active"<?php } ?>><a
                    href="<?php echo site_url('quotes/status/all'); ?>"><?php echo "All status"; ?></a>
            </li>
            <li <?php if ($status == 'diagnosing') { ?>class="active"<?php } ?>><a
                    href="<?php echo site_url('quotes/status/diagnosing'); ?>"><?php echo lang('diagnosing'); ?></a>
            </li> 

            <li <?php if ($status == 'waiting_on_approval') { ?>class="active"<?php } ?>><a
                    href="<?php echo site_url('quotes/status/waiting_on_approval'); ?>"><?php echo lang('waiting_on_approval'); ?></a>
            </li>
            <li <?php if ($status == 'ordered_parts') { ?>class="active"<?php } ?>><a
                    href="<?php echo site_url('quotes/status/ordered_parts'); ?>"><?php echo lang('ordered_parts'); ?></a>
            </li>
            <li <?php if ($status == 'repair_completed') { ?>class="active"<?php } ?>><a
                    href="<?php echo site_url('quotes/status/repair_completed'); ?>"><?php echo lang('repair_completed') . "(Sent)"; ?></a>
            </li>
            <li <?php if ($status == 'returned_to_shop') { ?>class="active"<?php } ?>><a
                    href="<?php echo site_url('quotes/status/returned_to_shop'); ?>"><?php echo lang('returned_to_shop'); ?></a>
            </li>
            </li>
            <li <?php if ($status == 'repairing') { ?>class="active"<?php } ?>><a
                    href="<?php echo site_url('quotes/status/repairing'); ?>"><?php echo lang('repairing'); ?></a>
            </li>
            <li <?php if ($status == 'repair_denied') { ?>class="active"<?php } ?>><a
                    href="<?php echo site_url('quotes/status/repair_denied'); ?>"><?php echo lang('repair_denied'); ?></a>
            </li>
            <li <?php if ($status == 'accepted_by_client') { ?>class="active"<?php } ?>><a
                    href="<?php echo site_url('quotes/status/accepted_by_client'); ?>"><?php echo lang('accepted_by_client') . "(Paid)"; ?></a>
            </li>
            <li <?php if ($status == 'new_order') { ?>class="active"<?php } ?>><a
                    href="<?php echo site_url('quotes/status/new_order'); ?>"><?php echo lang('new_order') . "(Draft)"; ?></a>
            </li>
            <li <?php if ($status == 'waiting_for_package') { ?>class="active"<?php } ?>><a
                    href="<?php echo site_url('quotes/status/waiting_for_package'); ?>"><?php echo lang('waiting_for_package'); ?></a>
            </li>
            <?php if ($this->session->userdata("is_tech") == 0) { ?><li <?php if ($status == 'uncomplete') { ?>class="active"<?php } ?>><a
                        href="<?php echo site_url('quotes/status/uncomplete'); ?>"><?php
                            $URL = $_SERVER['REQUEST_URI'];
                            $check_url = stripos($URL, 'quotes/status/uncomplete');
                            $this->db->select("*");
                            $this->db->join("orders", "orders.oid = ip_quotes.quote_number");
                            $this->db->where("ip_quotes.complete <>", 1);
                            $this->db->where("ip_quotes.payment_status <>", 0);
                            $this->db->where("ip_quotes.active", 0);
                            $this->db->where("orders.oid_ref", 0);
                            $this->db->where("ip_quotes.fraud", 0);

                            $ip_quotes_rows = $this->db->get("ip_quotes")->num_rows();
                            if ($check_url == true) {
                                echo "Uncomplete( <font color='white' style='text-shadow: -1px 0px 0px red, -1px 0px 0px red, 0px 1px 0px red, 0px -1px 0px red;'>$ip_quotes_rows</font> ) or denied orders";
                            } else {
                                echo "Uncomplete( <font color='red'>$ip_quotes_rows</font> ) or denied orders";
                            }
                            ?></a>
                </li>
                <li <?php if ($status == 'fraud') { ?>class="active"<?php } ?>><a
                        href="<?php echo site_url('quotes/status/fraud'); ?>"><?php
                    $URL = $_SERVER['REQUEST_URI'];
                    $check_url = stripos($URL, 'quotes/status/fraud');
                    $this->db->select("*");
                    $this->db->join("orders", "orders.oid = ip_quotes.quote_number");
                    $this->db->where("ip_quotes.fraud", 1);
                    $this->db->where("ip_quotes.complete <>", 1);
                    $this->db->where("ip_quotes.payment_status <>", 0);
                    $this->db->where("ip_quotes.active", 0);
                    $this->db->where("orders.oid_ref", 0);
                    $ip_fraud_row = $this->db->get("ip_quotes")->num_rows();
                    if ($check_url == true) {
                        echo "Fraud( <font color='white' style='text-shadow: -1px 0px 0px red, -1px 0px 0px red, 0px 1px 0px red, 0px -1px 0px red;'>$ip_fraud_row</font> )";
                    } else {
                        echo "Fraud( <font color='red'>$ip_fraud_row</font> )s";
                    }
                            ?></a>
                </li><?php } ?>
        </ul>
    </div>

</div>

<div id="submenu">
    <div class="collapse clearfix" id="ip-submenu-collapse">
        <div class="submenu-row">
<?php echo pager(site_url('quotes/status/' . $this->uri->segment(3)), 'mdl_quotes'); ?>
        </div>
        <div class="submenu-row">
            <ul class="nav nav-pills index-options">
            
                    <li <?php if ($status == 'website') { ?>class="active"<?php } ?>><a
                            href="<?php echo site_url('quotes/status/website'); ?>"><?php echo "Website Orders"; ?></a>
                    </li>
                
                <li <?php if ($status == 'all') { ?>class="active"<?php } ?>><a
                        href="<?php echo site_url('quotes/status/all'); ?>"><?php echo lang('all'); ?></a></li>
                <?php /* <li <?php if ($status == 'draft') { ?>class="active"<?php } ?>><a
                  href="<?php echo site_url('quotes/status/draft'); ?>"><?php echo lang('draft'); ?></a></li>
                  <li <?php if ($status == 'sent') { ?>class="active"<?php } ?>><a
                  href="<?php echo site_url('quotes/status/sent'); ?>"><?php echo lang('sent'); ?></a></li>
                  <li <?php if ($status == 'viewed') { ?>class="active"<?php } ?>><a
                  href="<?php echo site_url('quotes/status/viewed'); ?>"><?php echo lang('viewed'); ?></a></li>
                  <li <?php if ($status == 'approved') { ?>class="active"<?php } ?>><a
                  href="<?php echo site_url('quotes/status/approved'); ?>"><?php echo lang('approved'); ?></a>
                  </li>
                  <li <?php if ($status == 'rejected') { ?>class="active"<?php } ?>><a
                  href="<?php echo site_url('quotes/status/rejected'); ?>"><?php echo lang('rejected'); ?></a>
                  </li>
                  <li <?php if ($status == 'canceled') { ?>class="active"<?php } ?>><a
                  href="<?php echo site_url('quotes/status/canceled'); ?>"><?php echo lang('canceled'); ?></a>
                  </li> */ ?>
                <li <?php if ($status == 'diagnosing') { ?>class="active"<?php } ?>><a
                        href="<?php echo site_url('quotes/status/diagnosing'); ?>"><?php echo lang('diagnosing'); ?></a>
                </li>
                </li>
                <li <?php if ($status == 'waiting_on_approval') { ?>class="active"<?php } ?>><a
                        href="<?php echo site_url('quotes/status/waiting_on_approval'); ?>"><?php echo lang('waiting_on_approval'); ?></a>
                </li>
                </li>
                <li <?php if ($status == 'ordered_parts') { ?>class="active"<?php } ?>><a
                        href="<?php echo site_url('quotes/status/ordered_parts'); ?>"><?php echo lang('ordered_parts'); ?></a>
                </li>
                <li <?php if ($status == 'repair_completed') { ?>class="active"<?php } ?>><a
                        href="<?php echo site_url('quotes/status/repair_completed'); ?>"><?php echo lang('repair_completed'); ?></a>
                </li>
                <li <?php if ($status == 'returned_to_shop') { ?>class="active"<?php } ?>><a
                        href="<?php echo site_url('quotes/status/returned_to_shop'); ?>"><?php echo lang('returned_to_shop'); ?></a>
                </li>
                </li>
                <li <?php if ($status == 'repairing') { ?>class="active"<?php } ?>><a
                        href="<?php echo site_url('quotes/status/repairing'); ?>"><?php echo lang('repairing'); ?></a>
                </li>
                </li>
                <li <?php if ($status == 'repair_denied') { ?>class="active"<?php } ?>><a
                        href="<?php echo site_url('quotes/status/repair_denied'); ?>"><?php echo lang('repair_denied'); ?></a>
                </li>
                <li <?php if ($status == 'accepted_by_client') { ?>class="active"<?php } ?>><a
                        href="<?php echo site_url('quotes/status/accepted_by_client'); ?>"><?php echo lang('accepted_by_client'); ?></a>
                </li>
                <li <?php if ($status == 'new_order') { ?>class="active"<?php } ?>><a
                        href="<?php echo site_url('quotes/status/new_order'); ?>"><?php echo lang('new_order'); ?></a>
                </li>
                <li <?php if ($status == 'waiting_for_package') { ?>class="active"<?php } ?>><a
                        href="<?php echo site_url('quotes/status/waiting_for_package'); ?>"><?php echo lang('waiting_for_package'); ?></a>
                </li>
            </ul>
        </div>

    </div>
</div>

<div id="content" class="table-content">

    <div id="filter_results">
<?php $this->layout->load_view('quotes/partial_quote_table', array('quotes' => $quotes)); ?>
    </div>

</div>