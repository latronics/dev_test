<?php
ini_set('session.gc_maxlifetime', 30*99999);
date_default_timezone_set('America/Los_Angeles');
$this->session->sess_expiration = '0';

function greetings() {
    $hour = date('H', time());
    if ($hour > 6 && $hour <= 12) {
        echo "<b>Good Morning!</b>";
    } else if ($hour > 12 && $hour <= 16) {
        echo "<b>Good Afternoon!</b>";
    } else if ($hour > 16 && $hour <= 23) {
        echo "<b>Good Evening!</b>";
    }
}
?>
<head>
    <title>Client Terminal</title>
    <link rel="stylesheet" type="text/css" href="../../../../assets/default/css/style.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <script src="../../../../assets/default/jQuery-Mask-Plugin-master/src/jquery.mask.js"></script>










</head>
<body style="background-color:#ddd;">





    <div class="basic-grey" id = "register_form" style="border:1px #ddd solid; border-radius:10px; width:650px; margin-top:10%; background-color:white; box-shadow: 5px 5px 5px #888888;" align="center">
        <form method="post" action="<?php echo site_url('clients/register_client_terminal'); ?>" >
            <table border ="0" style="padding:10px; color:#337ab7;">
                <tr><td align="left" colspan="2" style="padding-left:10px; padding-top:10px;">
                        <img src="../../../../assets/default/img/logo.png" />

                    </td><td colspan="2" align="right" style="padding-right:10px;"><h3><?php greetings(); ?></h3></td></tr>
                <tr><td align="center" colspan="4">
                        <h4>Please make your registration in our system.</h4></td>
                </tr>
                <tr><td colspan="4">&nbsp;</td></tr>
                <tr>

                    <td style="padding-left: 10px; padding-right: 10px; padding-bottom: 10px;" ><input type="text" placeholder="First Name" class="form-control" size="20" name="FirstName" onkeypress="return alpha(event)"/></td>

                    <td style="padding-left: 10px; padding-right: 10px; padding-bottom: 10px;" colspan="2"><input type="text" placeholder="Last Name" class="form-control" size="20" name="LastName" onkeypress="return alpha(event)"/></td>
                </tr>
                <tr>

                    <td style="padding-left: 10px; padding-right: 10px; padding-bottom: 10px;"><input type="text" placeholder="E-mail" class="form-control" size="20" name="Email"  ></td>

                    <td style="padding-left: 10px; padding-right: 10px; padding-bottom: 10px;" colspan="2"><input type="text" placeholder="Phone Number" class="form-control" size="20" name="PhoneNumber" id="PhoneNumber" onkeypress="return alpha(event)"/></td>

                </tr>

                <tr><td colspan="4" align='center'><?php
                        if ($warnings != "") {
                            echo "<font style='color: red; font-size: 18px;'>" . $warnings . "</font>";
                            ?> <META http-equiv="refresh" content="1;URL=<?php echo base_url('index.php/clients/clientTerminal'); ?>">     <?php
                        } else if ($congrats != "") {
                            echo "<p style='color: green; font-size: 18px;'>" . $congrats . "</p>";
                            ?> <META http-equiv="refresh" content="2;URL=<?php echo base_url('index.php/clients/clientTerminal'); ?>">  <?php } ?></td></tr>
                <tr>
                    <td style="text-align: center; padding-bottom:10px;"  colspan="4" >
                        <br><input class="btn btn-info" type="submit" value="Submit" style="width: 100px; height: 50px;" >
                        <!--<input class="greyagreements" type="button" value="<?php echo "See the agreement terms/invoice"; ?>" style="width: 260px; height: 100px;" onclick="refresh_this_page()"></td>-->

                </tr>

            </table></form></div>












</body>

<script>
    
    $(document).ready(function () {
        $('#PhoneNumber').mask('(000) 000-0000');
        setInterval(function () {
            $.post("<?php echo site_url('clients/clients/update_client_terminal'); ?>", {
                ticket_id: 0

            },
                    function (data) {
                        if (data == "true")
                        {
                            location.reload();
                        }
                    });
        }, 1000);



    });

    function refresh_this_page()
    {

        location.reload();

    }

    function alpha(e) {
        var k;
        document.all ? k = e.keyCode : k = e.which;
        return ((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8 || k == 32 || (k >= 48 && k <= 57));
    }
</script>



</html>



