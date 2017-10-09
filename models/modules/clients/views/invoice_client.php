<?php
setlocale(LC_MONETARY, 'en_US');
$this->db->select("*");
$this->db->join("ip_clients", "ip_clients.client_id = ip_invoices.client_id");
$this->db->join("ip_invoice_amounts", "ip_invoice_amounts.invoice_id = ip_invoices.invoice_id");
$this->db->join("ip_invoice_items", "ip_invoice_items.invoice_id = ip_invoices.invoice_id");
$this->db->where("ip_invoices.invoice_id", $invoice_data[0]['id_invoice']);
$invoice_client_data = $this->db->get("ip_invoices")->result_array();

$date_created = strtotime($invoice_client_data[0]['invoice_date_created']);
$due_date = strtotime($invoice_client_data[0]['invoice_date_due']);

//print_r($invoice_client_data);
?>



<!doctype html>
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <title>Invoice  <?php echo $invoice_data[0]['id_invoice']; ?></title>

    <meta name="viewport" content="width=device-width,initial-scale=1">

    <link rel="stylesheet" href="../../../../assets/default/css/style.css">
    <link rel="stylesheet" href="../../../../assets/default/css/custom.css">

</head>

<body onload="focus()">
    <table  cellpadding="2" cellspacing="2" border="0" style="font-size:13px;  font-family: Tahoma, Geneva, sans-serif; margin-top:10px; " align="center" >
        <tr><td><button id="reload" onclick="reload_page()" class="btn btn-success" style="background-color:#00CC00; width: 200px; height:80px;">RELOAD</button></td></tr>
    </table>
    <div class="container">

        <div id="content">

            <div class="webpreview-header">
                <h3 style="text-align:center;" class="alert alert-success">Please Verify the Informations and Swipe your Card.</h3>
                <h2>Invoice <?php echo $invoice_data[0]['id_invoice']; ?></h2>

                <div class="btn-group">

                    <button class="btn btn-success" id="button_pay"><i class="fa fa-credit-card"></i> Pay Now</button>            </div>

            </div>

            <hr>

            <br>
            <div class="invoice">


                <div class="row">
                    <div class="col-xs-12 col-md-6 col-lg-5">

                        <h4><?php echo $invoice_client_data[0]['client_name']; ?></h4>
                        <p>                                                                                                                                                                                                                    </p>

                    </div>
                    <div class="col-lg-2"></div>
                    <div class="col-xs-12 col-md-6 col-lg-5 text-right">

                        <h4><?php echo $invoice_client_data[0]['client_name']; ?></h4>
                        <p><?php echo $invoice_client_data[0]['client_address_1']; ?><br><?php echo $invoice_client_data[0]['client_address_2']; ?><br><?php echo $invoice_client_data[0]['client_city'] . " " . $invoice_client_data[0]['client_state'] . " " . $invoice_client_data[0]['client_zip']; ?><br>                        P:<?php echo $invoice_client_data[0]['client_phone'] ?>                           <br>
                        </p>

                        <br>

                        <table class="table table-condensed">
                            <tbody>
                                <tr>
                                    <td>Invoice Date</td>
                                    <td style="text-align:right;"><?php echo date("m/d/Y", $date_created); ?></td>
                                </tr>
                                <tr class="">
                                    <td>Due Date</td>
                                    <td class="text-right">
                                        <?php echo date("m/d/Y", $due_date); ?>                        </td>
                                </tr>
                                <tr class="">
                                    <td>Amount Due</td>
                                    <td style="text-align:right;"><?php echo money_format('%(#10n', $invoice_client_data[0]['invoice_total']); ?></td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                </div>

                <br>

                <div class="invoice-items">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Description</th>
                                    <th class="text-right">Qty</th>
                                    <th class="text-right">Price</th>
                                    <th class="text-right">Discount</th>
                                    <th class="text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <?php foreach ($invoice_client_data as $invoice_client_data) { ?>
                                        <td><?php echo $invoice_client_data['item_name']; ?></td>
                                        <td><?php echo $invoice_client_data['item_description']; ?></td>
                                        <td class="amount"><?php echo $invoice_client_data['item_quantity']; ?></td>
                                        <td class="amount"><?php echo money_format('%(#10n', $invoice_client_data['item_price']); ?></td>

                                        <td class="amount"><?php
                                            if ($invoice_client_data['item_discount_amount'] == NULL) {
                                                $invoice_client_data['item_discount_amount'] = 0.00;
                                                echo "$ 0.00";
                                            } else {
                                                echo money_format('%(#10n', $invoice_client_data['item_discount_amount']);
                                            }
                                            ?></td>
                                        <td class="amount"><?php echo money_format('%(#10n', $invoice_client_data['item_price'] -= $invoice_client_data['item_discount_amount']); ?></td>
                                    </tr>
                                <?php } ?>
                                <tr>
                                    <td colspan="4"></td>
                                    <td class="text-right">Subtotal:</td>
                                    <td class="amount"><?php echo money_format('%(#10n', $invoice_client_data['invoice_item_subtotal']); ?></td>
                                </tr>



                                <tr>
                                    <td class="no-bottom-border" colspan="4"></td>
                                    <td class="text-right">Discount:</td>
                                    <td class="amount">
                                        <?php
                                        if ($invoice_client_data[0]['invoice_discount_amount'] == NULL) {
                                            echo "$ 0.00";
                                        } else {
                                            echo money_format('%(#10n', $invoice_client_data[0]['invoice_discount_amount']);
                                        }
                                        ?></td>
                                </tr>

                                <tr>
                                    <td class="no-bottom-border" colspan="4"></td>
                                    <td class="text-right">Total:</td>
                                    <td class="amount"><?php echo money_format('%(#10n', $invoice_client_data['invoice_total']); ?></td>
                                </tr>

                                <tr>
                                    <td class="no-bottom-border" colspan="4"></td>
                                    <td class="text-right">Paid</td>
                                    <td class="amount"><?php echo money_format('%(#10n', $invoice_client_data['invoice_paid']); ?></td>
                                </tr>
                                <tr class="overdue">
                                    <td class="no-bottom-border" colspan="4"></td>
                                    <td class="text-right">Balance</td>
                                    <td class="amount">
                                        <b><?php echo money_format('%(#10n', $invoice_client_data['invoice_balance']); ?></b>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>


                    <hr>

                    <div class="row">

                        Card number: <input type ="text" class="input-sm form-control" id="card_number" style="width:200px;" ><br>
                        Card Expire Date: <input type ="text" class="input-sm form-control" id="card_date" style="width:200px;"><br>
                        CCV: <input type ="text" class="input-sm form-control" id="ccv_code" style="width:200px;"><br>


                    </div>

                </div>

            </div>

        </div>
        <div id = "show_result_card"</div>
    </div>

</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script type="text/javascript">
       $("#reload").click(function(){
           
           location.reload();
        
       });
            
        function focus()
        {
            document.getElementById('card_number').focus();
        }
        $("#button_pay").click(function () {


            $.post("<?php echo site_url('payments/process_bluepay/' . $invoice_data[0]['id_invoice'] . "/" . $invoice_data[0]['id_client']); ?>", {
                card_number: $("#card_number").val(),
                expire_date: $("#card_date").val(),
                ccv_code: $("#ccv_code").val(),
                amount: <?php if($invoice_client_data['invoice_total'] != "") {echo $invoice_client_data['invoice_total'];} else { echo 0;} ?>



            },
                    function (data) {
                        $("#content").hide();
                        $("#show_result_card").html(data);

                    });
        });
</script>
</html>
