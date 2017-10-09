<!doctype html>

<!--[if lt IE 7]>
<html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>
<html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>
<html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="en"> <!--<![endif]-->
    <style>
        .ui-autocomplete {

            position: absolute;
            top: 0;
            left: 0;

            cursor: default;
            z-index:9050!important;
        }



        .loader {
            position: fixed;
            left: 0px;
            top: 0px;
            width: 100%;
            height: 100%;
            z-index: 9999;
            background: url('../../../../assets/default/img/page-loader.gif') 50% 50% no-repeat rgb(249,249,249);
        }

    </style>
    <div class="loader"></div>
    <head>

        <title>
            <?php
            if ($this->mdl_settings->setting('custom_title') != '') {
                echo $this->mdl_settings->setting('custom_title');
            } else if ($this->uri->segment(1) == "quotes") {
                if ($this->uri->segment(2) != null) {
                    if (($this->uri->segment(3) != null) && ($this->uri->segment(3) != "all")) {
                        $this->db->select("quote_number");
                        $this->db->where("quote_id", $this->uri->segment(3));
                        $number_ticket = $this->db->get("ip_quotes")->result_array();
                        echo 'Tickets - ' . $number_ticket[0]['quote_number'];
                    } else {
                        echo '365admin - Tickets';
                    }
                } else {
                    echo '365admin - Tickets';
                }
            } else if ($this->uri->segment(1) == "payments") {
                echo "365admin - Payments";
            } else if ($this->uri->segment(1) == "invoices") {
                if ($this->uri->segment(2) != null) {
                    if (($this->uri->segment(3) != null) && ($this->uri->segment(3) != "index") && ($this->uri->segment(3) != "all")) {
                        $this->db->select("invoice_number");
                        $this->db->where("invoice_id", $this->uri->segment(3));
                        $invoice_info = $this->db->get("ip_invoices")->result_array();
                        echo 'Invoices - ' . $invoice_info[0]['invoice_number'];
                    } else {
                        echo '365admin - Invoices';
                    }
                } else {
                    echo '365admin - Invoices';
                }
            } else if (($this->uri->segment(1) == "clients")) {
                if ($this->uri->segment(2) == "view") {
                    if ($this->uri->segment(3) != null) {
                        $this->db->select("client_name");
                        $this->db->where("client_id", $this->uri->segment(3));
                        $clients_data = $this->db->get("ip_clients")->result_array();
                        echo 'Clients - ' . $clients_data[0]['client_name'];
                    } else {
                        echo '365admin - Clients';
                    }
                } else {
                    echo '365admin - Clients';
                }
            } else if (($this->uri->segment(1) == "products")) {
                if ($this->uri->segment(2) == "form") {
                    if ($this->uri->segment(3) != null) {
                        $this->db->select("product_name");
                        $this->db->where("product_id", $this->uri->segment(3));
                        $products_data = $this->db->get("ip_products")->result_array();
                        echo 'Products - ' . $products_data[0]['product_name'];
                    } else {
                        echo '365admin - Products';
                    }
                } else {
                    echo '365admin - Products';
                }
            } else if ($this->uri->segment(1) == "parts") {
                echo "365admin - Parts";
            } else if ($this->uri->segment(1) == "families") {
                echo "365admin - Product Families";
            } else if (($this->uri->segment(1) == "reports")) {
                if ($this->uri->segment(2) == "payment_history") {
                    echo "365admin - Payment History";
                } else {
                    echo "Sales by Client";
                }
            } else {
                echo "365admin - Dashboard";
            }
            ?>
        </title>






        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <meta name="robots" content="NOINDEX,NOFOLLOW">

        <link rel="icon" type="image/png" href="<?php echo base_url(); ?>assets/default/img/favicon.png">

        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/default/css/style.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/default/css/custom.css">

        <?php if ($this->mdl_settings->setting('monospace_amounts') == 1) { ?>
            <link rel="stylesheet" href="<?php echo base_url(); ?>assets/default/css/monospace.css">
        <?php } ?>

        <!--[if lt IE 9]>
        <script src="<?php echo base_url(); ?>assets/default/js/libs/html5shiv-3.7.2.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/default/js/libs/respond-1.4.2.min.js"></script>
        <![endif]-->

        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <!--<script src="<?php echo base_url(); ?>assets/default/js/libs/jquery-1.12.3.min.js"></script>-->
        <script src="<?php echo base_url(); ?>assets/default/js/libs/bootstrap-3.3.6.min.js"></script>
        <!--<script src="<?php echo base_url(); ?>assets/default/js/libs/jquery-ui-1.11.4.min.js"></script>-->
        <script src="<?php echo base_url(); ?>assets/default/js/libs/select2-4.0.2.full.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/default/js/libs/dropzone-4.3.0.min.js"></script>


        <script type="text/javascript">
            Dropzone.autoDiscover = false;

            function open_client_terminal(page)
            {

                window.open(page, 'principal', 'status=no, toolbar=no, menubar=no, location=yes, fullscreen=1, scrolling=auto');

            }
            function update_user_store(store_id)
            {
                $.post("<?php echo site_url('stores/update_store_user'); ?>", {
                    store_id: store_id

                },
                        function (data) {
                            //alert(data);
                            location.reload();
                        });
            }
            $(function () {
                $("#clear_terminal").click(function () {
                    $.post("<?php echo site_url('invoices/ajax/erase_client_terminal'); ?>", {
                        erase_data: 1


                    },
                            function (data) {
                                alert(data);
                            });
                });

                if (window.location.href.indexOf("quotes/status/") > -1) {
                    $("#filter_input").hide();
                }
                $(".loader").fadeOut("slow");
                $('.nav-tabs').tab();
                $('.tip').tooltip();

                $('body').on('focus', ".datepicker", function () {
                    $(this).datepicker({
                        autoclose: true,
                        format: '<?php echo date_format_datepicker(); ?>',
                        language: '<?php echo lang('cldr'); ?>',
                        weekStart: '<?php echo $this->mdl_settings->setting('first_day_of_week'); ?>',
                        todayBtn: true
                    });
                });
                $('.open-terminal').click(function () {
                    $('#modal-placeholder').load("<?php echo site_url('clients/clients/clientTerminal'); ?>");
                });

                $('.create-invoice').click(function () {
                    $('#modal-placeholder').load("<?php echo site_url('invoices/ajax/modal_create_invoice'); ?>");
                });

                $('.create-quote').click(function () {
                    $('#modal-placeholder').load("<?php echo site_url('quotes/ajax/modal_create_quote'); ?>");
                });

                $('.create-client').click(function () {
                    $('#modal-placeholder').load("<?php echo site_url('quotes/ajax/modal_create_quote'); ?>");
                });

                $('#btn_quote_to_invoice').click(function () {
                    quote_id = $(this).data('quote-id');
                    $('#modal-placeholder').load("<?php echo site_url('quotes/ajax/modal_quote_to_invoice'); ?>/" + quote_id);
                });

                $('#btn_copy_invoice').click(function () {
                    invoice_id = $(this).data('invoice-id');
                    $('#modal-placeholder').load("<?php echo site_url('invoices/ajax/modal_copy_invoice'); ?>", {invoice_id: invoice_id});
                });

                $('#btn_create_credit').click(function () {
                    invoice_id = $(this).data('invoice-id');
                    $('#modal-placeholder').load("<?php echo site_url('invoices/ajax/modal_create_credit'); ?>", {invoice_id: invoice_id});
                });

                $('#btn_copy_quote').click(function () {
                    quote_id = $(this).data('quote-id');
                    $('#modal-placeholder').load("<?php echo site_url('quotes/ajax/modal_copy_quote'); ?>", {quote_id: quote_id});
                });

                $('.client-create-invoice').click(function () {
                    $('#modal-placeholder').load("<?php echo site_url('invoices/ajax/modal_create_invoice'); ?>", {
                        client_name: $(this).data('client-name')
                    });
                });
                $('.client-create-quote').click(function () {
                    $('#modal-placeholder').load("<?php echo site_url('quotes/ajax/modal_create_quote'); ?>", {
                        client_name: $(this).data('client-name')
                    });
                });



                $(document).on('click', '.invoice-add-payment', function () {
                    invoice_id = $(this).data('invoice-id');
                    invoice_balance = $(this).data('invoice-balance');
                    invoice_payment_method = $(this).data('invoice-payment-method');
                    $('#modal-placeholder').load("<?php echo site_url('payments/ajax/modal_add_payment'); ?>", {
                        invoice_id: invoice_id,
                        invoice_balance: invoice_balance,
                        invoice_payment_method: invoice_payment_method
                    });
                });

            });

        </script>

    </head>

    <body class="<?php
    if ($this->mdl_settings->setting('disable_sidebar') == 1) {
        echo 'hidden-sidebar';
    }
    ?>">


        <noscript>
        <div class="alert alert-danger no-margin"><?php echo lang('please_enable_js'); ?></div>
        </noscript>

        <nav class="navbar navbar-inverse" role="navigation" style="text-align: center; padding-left:100px; font-weight: bold; border: 0px;">
            <div class="container-fluid" >
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#ip-navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <?php echo lang('menu') ?> &nbsp; <i class="fa fa-bars"></i>
                    </button>
                </div>

                <table border = "1">
                    <div class="collapse navbar-collapse " id="ip-navbar-collapse" >

                        <ul class="nav navbar-nav " >

                            <li><a href="<?php echo site_url(); ?>"  class="dashboard"><div style="padding-top:70px;" ><?php echo lang('dashboard'); ?></div></a><?php // echo anchor('dashboard', lang('dashboard'), 'class="hidden-sm"')                    ?>
                                <?php // echo anchor('dashboard', '<i class="fa fa-dashboard"></i>', 'class="visible-sm-inline-block"')   ?>
                            </li>
                            <?php if ($this->session->userdata('is_tech') == 0) { ?>
                                <li class="dropdown ">
                                    <a href="#" class="dropdown-toggle dashboard_clients" data-toggle="dropdown">
                                        <div style="padding-top:70px;" ><?php echo lang('clients'); ?></span><i class="fa fa-caret-down"></div></i> &nbsp;<span
                                            class="hidden-sm"><i
                                                class="visible-sm-inline fa fa-users"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><?php echo anchor('clients/form', lang('add_client')); ?></li>
                                        <li><?php echo anchor('clients/index', lang('view_clients')); ?></li>
                                    </ul>
                                </li>
                            <?php } ?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle dashboard_tickets" data-toggle="dropdown">
                                    <div style="padding-top:70px;" align="center"><?php echo lang('quotes'); ?></span><i class="fa fa-caret-down"></div></i> &nbsp;<span
                                        class="hidden-sm"><i
                                            class="visible-sm-inline fa fa-file"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <?php if ($this->session->userdata("is_tech") == 0) { ?>
                                        <li><a href="#" class="create-quote"><?php echo lang('create_quote'); ?></a></li>
                                    <?php } ?>
                                    <li><?php echo anchor('quotes/index', lang('view_quotes')); ?></li>
                                </ul>
                            </li>
                            <?php if ($this->session->userdata('is_tech') == 0) { ?>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle dashboard_invoices" data-toggle="dropdown">
                                        <div style="padding-top:70px;" align="center"><?php echo lang('invoices'); ?></span>
                                            <i class="fa fa-caret-down"></div></i> &nbsp;<span
                                            class="hidden-sm"><i
                                                class="visible-sm-inline fa fa-file-text"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a href="#" class="create-invoice"><?php echo lang('create_invoice'); ?></a></li>
                                        <li><?php echo anchor('invoices/index', lang('view_invoices')); ?></li>
                                        <li><?php echo anchor('invoices/recurring/index', lang('view_recurring_invoices')); ?></li>
                                    </ul>
                                </li>

                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle dashboard_products" data-toggle="dropdown">
                                        <div style="padding-top:70px;"><?php echo lang('services_products'); ?></span>
                                            <i class="fa fa-caret-down"></i></div> &nbsp;<span
                                            class="hidden-sm"></span><i
                                            class="visible-sm-inline fa fa-database"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><?php echo anchor('products/form', lang('create_product')); ?></li>
                                        <li><?php echo anchor('products/index', lang('view_services')); ?></li>
                                        <li><?php echo anchor('parts/index', lang('view_parts') . "(warehouse control)"); ?></li>
                                        <li><?php echo anchor('families/index', lang('product_families')); ?></li>
                                    </ul>
                                </li>

                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle dashboard_payments" data-toggle="dropdown">
                                        <div style="padding-top:70px;"><?php echo lang('payments'); ?><i class="fa fa-caret-down"></div></i> &nbsp;<span
                                            class="hidden-sm"></span><i
                                            class="visible-sm-inline fa fa-credit-card"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><?php echo anchor('payments/form', lang('enter_payment')); ?></li>
                                        <li><?php echo anchor('payments/index', lang('view_payments')); ?></li>
                                        <li><?php echo anchor('payments/approved_payments', lang('approved_payments')); ?></li>
                                    </ul>
                                </li>

                                <li class="dropdown hidden">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        <i class="fa fa-caret-down"></i> &nbsp;<span
                                            class="hidden-sm"><?php echo lang('tasks'); ?></span><i
                                            class="visible-sm-inline fa fa-check-square-o"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><?php echo anchor('tasks/form', lang('create_task')); ?></li>
                                        <li><?php echo anchor('tasks/index', lang('show_tasks')); ?></li>
                                        <li><?php echo anchor('projects/index', lang('projects')); ?></li>
                                    </ul>
                                </li>

                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle dashboard_reports" data-toggle="dropdown">
                                        <div style="padding-top:70px;"><?php echo lang('reports'); ?><i class="fa fa-caret-down"></div></i> &nbsp;<span
                                            class="hidden-sm"></span><i
                                            class="visible-sm-inline fa fa-bar-chart"></i>
                                    </a>
                                    <ul class="dropdown-menu">

                                        <li><?php echo anchor('reports/payment_history', lang('payment_history')); ?></li>
                                        <li><?php echo anchor('reports/sales_by_client', lang('sales_by_client')); ?></li>

                                    </ul>
                                </li>
                                <li class="hidden-sm">
                                    <a href = 'javascript:window.open("<?php echo base_url('index.php/clients/clientTerminal') ?>", "popup", "menubar=no, resizable=no, scrollbars=no, fullscreen=yes, status=no, titlebar=no, toolbar=no");' class ="dashboard_client_terminal">
                                        <div style="padding-top:70px;"><?php echo lang('clients_terminal'); ?>
                                        </div>
                                    </a>
                                </li>
                                <li class="hidden-sm">
                                    <a href = 'https://dev.365laptoprepair.com/Myforms'  target='_blank' class ="contact_forms">
                                        <div style="padding-top:70px; margin-left:-25px;"><?php echo 'Contact Forms'; ?>
                                        </div>
                                    </a>
                                </li>
                               <li class="hidden-sm">
                                    <a href = 'https://dev.365laptoprepair.com/Myestimates'  target='_blank' class ="estimates_link">
                                        <div style="padding-top:70px; margin-left:-30px;"><?php echo 'Estimates/Inquiries'; ?>
                                        </div>
                                    </a>
                                </li>
                               

                                <li><?php if (isset($filter_display) and $filter_display == true) { ?>
                                        <?php $this->layout->load_view('filter/jquery_filter'); ?>
                                        <form class="navbar-form navbar-left" role="search" onsubmit="return false;">
                                            <div id = "filter_input" class="form-group" style="width:300px;">
                                                <input id="filter" type="text" class="search-query form-control input-sm"
                                                       placeholder="<?php echo $filter_placeholder; ?>">
                                            </div>
                                        </form>
                                    <?php } ?></li>


                            <?php } ?>
                        </ul>



                        <ul class="nav navbar-nav navbar-right">

                            <?php if ($this->session->userdata('is_tech') == 0) { ?>


                                <li class="tip icon" style="padding-top:10px; padding-right:10px;"><button class="btn btn-success" id="clear_terminal">Clear client terminal</button></li>

                                <li class="tip icon" style="padding-top:10px;">

                                    <select name="user_store_general" id="user_store_general" class="form-control" onchange="update_user_store(this.value)">

                                        <?php
                                        $this->db->select("*");
                                        $store_data = $this->db->get("ip_stores")->result_array();
                                        foreach ($store_data as $key => $type) {
                                            ?>
                                            <option value="<?php echo $type['id']; ?>"

                                                    <?php
                                                    $this->db->select("*");
                                                    $this->db->where("user_id", $this->session->userdata('user_id'));
                                                    $get_info_store = $this->db->get("ip_users")->result_array();
                                                    if ($get_info_store[0]['user_store'] == $type['id']) {
                                                        echo "selected";
                                                    }
                                                    ?> ><?php echo $type['store_name']; ?></option>
                                                <?php } ?>
                                    </select>

                                    <span class="visible-xs">&nbsp;<?php echo lang('documentation'); ?></span>
                                    </a>
                                </li>
                            <?php } ?>
                            <li class="dropdown">
                                <?php if ($this->session->userdata('is_tech') == 0) { ?>
                                    <a href="#" class="tip icon dropdown-toggle" data-toggle="dropdown"
                                       data-original-title="<?php echo lang('settings'); ?>"
                                       data-placement="bottom">
                                        <i class="fa fa-cogs"></i>
                                        <span class="visible-xs">&nbsp;<?php echo lang('settings'); ?></span>
                                    </a>
                                <?php } ?>
                                <ul class="dropdown-menu">
                                    <li><?php echo anchor('custom_fields/index', lang('custom_fields')); ?></li>
                                    <li><?php echo anchor('email_templates/index', lang('email_templates')); ?></li>
                                    <li><?php echo anchor('invoice_groups/index', lang('invoice_groups')); ?></li>
                                    <li><?php echo anchor('invoices/archive', lang('invoice_archive')); ?></li>

                                    <!-- // temporarily disabled
                                    <li><?php echo anchor('item_lookups/index', lang('item_lookups')); ?></li>
                                    -->
                                    <li><?php echo anchor('payment_methods/index', lang('payment_methods')); ?></li>
                                    <li><?php echo anchor('tax_rates/index', lang('tax_rates')); ?></li>
                                    <li><?php echo anchor('users/index', lang('user_accounts')); ?></li>
                                    <li><?php echo anchor('stores/index', lang('stores')); ?></li>
                                    <li class="divider hidden-xs hidden-sm"></li>
                                    <li><?php echo anchor('settings', lang('system_settings')); ?></li>
                                    <li><?php echo anchor('import', lang('import_data')); ?></li>
                                </ul>
                            </li>
                            <li>
                                <?php if ($this->session->userdata('is_tech') == 0) { ?>
                                    <a href="<?php
                                    echo site_url('users/form/' .
                                            $this->session->userdata('user_id'));
                                    ?>">
                                           <?php
                                           print($this->session->userdata('user_name'));
                                           if ($this->session->userdata('user_company')) {
                                               print(" (" . $this->session->userdata('user_company') . ")");
                                           }
                                           ?>
                                    </a>
                                <?php } else { ?>
                                    <a href="#">
                                        <?php
                                        print($this->session->userdata('user_name'));
                                        if ($this->session->userdata('user_company')) {
                                            print(" (" . $this->session->userdata('user_company') . ")");
                                        }
                                        ?>
                                    </a>
                                <?php } ?>
                            </li>
                            <li>
                                <a href="<?php echo site_url('sessions/logout'); ?>"
                                   class="tip icon logout" data-placement="bottom"
                                   data-original-title="<?php echo lang('logout'); ?>">
                                    <i class="fa fa-power-off"></i>
                                    <span class="visible-xs">&nbsp;<?php echo lang('logout'); ?></span>
                                </a>
                            </li>
                        </ul>
                    </div></table>
            </div>
        </nav>
        <?php if ($this->session->userdata('is_tech') == 0) { ?>
            <div class="sidebar hidden-xs <?php
            if ($this->mdl_settings->setting('disable_sidebar') == 1) {
                echo 'hidden';
            }
            ?>">
                <ul>
                    <li>
                        <a href="<?php echo site_url('dashboard'); ?>" title="<?php echo lang('dashboard'); ?>" class="tip"
                           data-placement="right">
                            <i class="fa fa-dashboard"></i>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo site_url('clients/index'); ?>" title="<?php echo lang('clients'); ?>" class="tip"
                           data-placement="right">
                            <i class="fa fa-users"></i>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo site_url('quotes/index'); ?>" title="<?php echo lang('quotes'); ?>" class="tip"
                           data-placement="right">
                            <i class="fa fa-file"></i>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo site_url('invoices/index'); ?>" title="<?php echo lang('invoices'); ?>" class="tip"
                           data-placement="right">
                            <i class="fa fa-file-text"></i>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo site_url('payments/index'); ?>" title="<?php echo lang('payments'); ?>" class="tip"
                           data-placement="right">
                            <i class="fa fa-money"></i>
                        </a>
                    </li>
                </ul>
            </div>
        <?php } else { ?>
            <div class="sidebar hidden-xs">
            </div>
        <?php } ?>
        <div id="main-area">

            <div id="modal-placeholder"></div>

            <?php echo $content; ?>

        </div>

        <div id="fullpage-loader" style="display: none">
            <div class="loader-content">
                <i class="fa fa-cog fa-spin"></i>
                <div id="loader-error" style="display: none">
                    <?php echo lang('loading_error'); ?><br/>
                    <a href="https://wiki.invoiceplane.com/<?php echo lang('cldr'); ?>/1.0/general/faq"
                       class="btn btn-primary btn-sm" target="_blank">
                        <i class="fa fa-support"></i> <?php echo lang('loading_error_help'); ?>
                    </a>
                </div>
            </div>
        </div>

        <script defer src="<?php echo base_url(); ?>assets/default/js/plugins.js"></script>
        <script defer src="<?php echo base_url(); ?>assets/default/js/scripts.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/default/js/libs/bootstrap-datepicker.min.js"></script>
        <?php if (lang('cldr') != 'en') { ?>
            <script
            src="<?php echo base_url(); ?>assets/default/js/locales/bootstrap-datepicker.<?php echo lang('cldr'); ?>.js"></script>
        <?php } ?>

    </body>
</html>
