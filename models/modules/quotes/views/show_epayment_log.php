<style>
    BODY {
        font-family: Tahoma, Verdana;
        font-size: 11px;
        color: #898989;
        margin: 0;
    }
    H3 {
        margin: 0;
        height: 28px;
        line-height: 28px;
        vertical-align: middle;
        color: #2fb3fe;
        margin-top: 5px;
    }



</style>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
            <meta http-equiv="Cache-Control" content="no-Cache" />
            <title>Payment Proccessor Log</title>

    </head>
    <body style="padding:20px;">
        <h3>Payment Proccessor Log</h3>
        <div id="form1">
            <?php
            foreach ($payment_status as $payment_status) {
                $payment_log_array = unserialize($payment_status['payproc_data']);

                if ($payment_status['payproc'] == 2) {

                    if ($payment_log_array['x_invoice_num'] != "") {
                        $i = 0;
                        foreach ($payment_log_array as $payment_log_array) {
                            if ($i <= 68) {
                                echo "<b>$i</b>: ";
                            }


                            if ($i == 69) {
                                echo "<b>x_invoice_num: </b>";
                            } else if ($i == 70) {
                                echo "<b>x_response_code: </b>";
                            } else if ($i == 71) {
                                echo "<b>x_response_desc: </b>";
                            }
                            echo $payment_log_array . "<br>";
                            $i++;
                        }
                    } else {
                        echo "<b>Payment_status: </b>" . $payment_log_array['payment_status'] . "<br>";
                        echo "<b>Item_number: </b>" . $payment_log_array['item_number'];
                    }
                } else if ($payment_status['payproc'] == 1) {

                    $i = 0;
                    foreach ($payment_log_array as $payment_log_array) {
                        if ($i <= 68) {
                            echo "<b>$i</b>: ";
                        }


                        if ($i == 69) {
                            echo "<b>x_invoice_num: </b>";
                        } else if ($i == 70) {
                            echo "<b>x_response_code: </b>";
                        } else if ($i == 71) {
                            echo "<b>x_response_desc: </b>";
                        }
                        echo $payment_log_array . "<br>";
                        $i++;
                    }
                }
            }
            ?>
        </div>
    </body>
</html>
