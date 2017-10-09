<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
      integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css"
      integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
        integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
        crossorigin="anonymous"></script>

<label style="margin-left:10px; margin-top:10px; font-weight:normal;">Thanks for your order, we've have received your order successfully.<br>You can preview the details below.<br>This isn't a payment confirmation, once we receive your payment, we'll send an aditional notification.</label>
<br>
<?php
if($password != null)
{
    ?>
    <label style="margin-left:10px; font-weight:normal;">You now have an account with us.<br>
        Your username is: <?php echo $this->insertdata['email']; ?><br>
        Your password is: <?php echo $password; ?><br>
        It can be changed any time from your account section, which is accessible after logging in.
        Best Regards,<br>
        LA-Tronics team.<br>
    </label><br>
<?php }
?>


<div class="panel panel-default" style="width:600px; margin-left:30px; margin-top:30px;">
    <div class="panel-heading"><label><?php echo '#' . $this->insertdata['oid'] . " - "; ?>La-Tronics Order
            Review</label></div>

    <table class="table table-condensed table-bordered">
        <thead style="font-weight:bold;">
        <td>
            Order Number
        </td>
        <td>
            Date Created
        </td>
        <td>
            Payment Method
        </td>
        <td>
            Shipping Total
        </td>
        <td>
            Items Subtotal
        </td>
        <td>
            Order Total
        </td>
        </thead>
        <tr>
            <td>
                <?php echo $this->insertdata['oid']; ?>
            </td>
            <td>
                <?php echo date("m/d/Y"); ?>
            </td>
            <td>
                <?php
                if ($this->insertdata['payproc'] == '2') {
                    echo "Paypal";
                } else {
                    echo "Credit Card";
                }
                ?>
            </td>
            <td>
                <?php echo "U$".$this->insertdata['endprice_delivery']; ?>
            </td>
            <td>
                <?php echo "U$".$this->insertdata['endprice']; ?>
            </td>
            <td>
                <?php echo money_format("U$%i",$sum); ?>
            </td>

        </tr>
        <tr>
            <td colspan="6">
                <b>Order Items</b>
            </td>
        </tr>
        <tr><td colspan="4">
                <b>Item Name</b>
            </td>
            <td>
                <b>Item Quantity</b>
            </td>
            <td>
                <b>Item Price</b>
            </td>
        </tr>
        <?php
        //FOREACH GOES HERE
        foreach($rcart['order'] as $items) {

            ?>
            <tr>
                <td colspan="4">
                    <?php echo $items['e_title']; ?>
                </td>
                <td>
                    <?php echo  $items['quantity']; ?>
                </td>
                <td>
                    <?php echo "U$".$items['buyItNowPrice']; ?>
                </td>
            </tr>
            <?php
            //FOREACH ENDS HERE

        }
        ?>


        <tr><td colspan="6"><label style="margin-left:10px;">La-Tronics INC, <a href="https://www.la-tronics.com">www.la-tronics.com</a>
                    <br>(424) 269-2902
                </label></td></tr>
    </table>




</div>
