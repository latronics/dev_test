
<div id="headerbar">
    <h1>Payment Status</h1>
    <div class="pull-right btn-group">
        <button id="btn-receipt" name="btn-receipt" class="btn btn-success btn-sm" value="1" >
            Print Receipt
        </button>
        <button id="btn-return" name="btn-return" class="btn-danger btn  btn-sm" value="1">
            Return
        </button>
    </div>
</div>





<table border="0" align="center" style="margin-top:10px;"> 

    <tr><td style="padding-left:18px;">
            <label class="control-label">Status</label> </td>
        <td style="padding-bottom:5px;">
            <div class="col-xs-12 col-sm-6">


                <input type="text" class="form-control" id ="status" value="<?php echo $payment['transaction_status'];
echo $declined; ?>" disabled style="width:auto;"/>

            </div></td>


        <td style="padding-left:18px;">
            <label class="control-label">Message</label> </td>
        <td style="padding-bottom:5px;">
            <div class="col-xs-12 col-sm-6">


                <input type="text" class="form-control" id="message" value="<?php echo $payment['transaction_message'];
if ($declined) {
    echo "DECLINED";
} ?>" disabled style="width:auto;"/>

            </div></td>
    </tr>
    <tr><td style="padding-left:18px;">
            <label class="control-label">Transaction ID</label> </td>
        <td style="padding-bottom:5px;">
            <div class="col-xs-12 col-sm-6">


                <input type="text" class="form-control" id="trans_id" value="<?php echo $payment['transaction_id']; ?>" disabled style="width:auto;"/>

            </div></td>


        <td style="padding-left:18px;">
            <label class="control-label"><?php if ($payment['card_type'] == "ACH") {
    echo "Check Number";
} else {
    echo "Card Last Digits";
} ?></label> </td>
        <td style="padding-bottom:5px;">
            <div class="col-xs-12 col-sm-6">


                <input type="text" class="form-control" id="card_digits" value="<?php echo $payment['masked_account']; ?>" disabled style="width:auto;"/>

            </div></td>
    </tr>
    <tr><td style="padding-left:18px;">
            <label class="control-label"><?php if ($payment['card_type'] == "ACH") {
    echo "Check";
} else {
    echo "Card Type";
} ?></label> </td>
        <td style="padding-bottom:5px;">
            <div class="col-xs-12 col-sm-6">


                <input type="text" class="form-control" id="card_type" value="<?php echo $payment['card_type']; ?>" disabled style="width:auto;"/>

            </div></td>

        <td style="padding-left:18px;">
            <label class="control-label">Amount</label> </td>
        <td style="padding-bottom:5px;">
            <div class="col-xs-12 col-sm-6">


                <input type="text" class="form-control" id ="amount" value="<?php echo "$" . $payment['amount']; ?>" disabled style="width:auto;"/>

            </div></td>



    </tr>
</table>

<script src="../../../../assets/default/js/jquery.maskMoney.js" type="text/javascript"></script>
<script>

    $(function () {
        var get_url = window.location.href;
        var url_to_validate = "<?php echo site_url('clients/clientTerminal'); ?>";
       
        if (get_url == url_to_validate)
        {
           $("#headerbar").hide();
        }


        if ($("#message").val() === "DECLINED") {

        } else
        {
            window.open('card_receipt/' + $("#trans_id").val());
        }
        $("#btn-receipt").click(function () {
            window.open('card_receipt/' + $("#trans_id").val());

        });

        $("#amount").maskMoney();

        $("#btn-return").click(function () {

            window.open('form', '_self');

        });
    });

</script>