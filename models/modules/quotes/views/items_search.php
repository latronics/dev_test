<?php
$i = 0;
if (($products != null) && ($products != 1)) {
    ?>
    
        <table >
            <?php
            foreach ($products as $products) {
                ?>


                <tr>
                    <td>

                    </td>
                    <td style="padding-left:10px; font-weight: bold;">
                        Product Name
                    </td>
                    <!--<td style="padding-left:10px; font-weight: bold;">
                        Product Description
                    </td>-->
                    <td style="padding-left:10px; font-weight: bold;">
                        Quantity
                    </td>
                    <td style="padding-left:10px; font-weight: bold;">
                        Product Price
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="checkbox" class="items_checked" value="<?php echo $products->product_id; ?>" name="items_checked[]" onclick="disable_line(this.value)">
                    </td><input type ="text" class="product_id" name="product_id[]" value="<?php echo $products->product_id; ?>" hidden>
                <td style="padding-left:10px;">
                    <input type="text" class="product_name form-control" name="product_name[<?php echo $products->product_id; ?>]" id="product_name<?php echo $products->product_id; ?>" value="<?php echo $products->product_name; ?>" style="width:180px;"  disabled>
                </td>
                <!--<td style="padding-left:10px;">
                    <input type="text" class="product_description form-control" name="product_description[<?php echo $products->product_id; ?>]" id="product_description<?php echo $products->product_id; ?>" value="<?php echo $products->product_description; ?>" style="width:300px;" disabled>
                </td>-->
                <td style="padding-left:10px;">
                    <input type="number" class="product_qtt form-control" name="product_qtt[<?php echo $products->product_id; ?>]" id="product_qtt<?php echo $products->product_id; ?>" value="1" style="width:60px;" disabled>
                </td>
                <td style="padding-left:10px;">
                    <input type="text" class="product_price form-control" name="product_price[<?php echo $products->product_id; ?>]" id="product_price<?php echo $products->product_id; ?>" value="<?php echo $products->product_price; ?>" disabled>
                </td>
                </tr>
                <input type="text" id="validate" value="0" hidden>
                <?php
            }
            ?>
        </table>
    
<?php } else if ($products == 1) { ?>
   
        <table>
            <?php while ($i <= 10) { ?>
                <tr>
                    <td style="font-weight: bold;">
                        Product Name
                    </td>
                    <!--<td style="padding-left:10px; font-weight: bold;">
                        Product Description
                    </td>-->
                    <td style="padding-left:10px; font-weight: bold;">
                        Quantity
                    </td>
                    <td style="padding-left:10px; font-weight: bold;">
                        Product Price
                    </td>
                </tr>
                <tr><input type ="text" class="product_id" name="product_id[]" value="<?php echo $products->product_id; ?>" hidden>
                <td>
                    <input type="text" class="product_name form-control" name="product_name[]" value="" style="width:180px;">
                </td>
                <!--<td style="padding-left:10px;">
                    <input type="text" class="product_description form-control" name="product_description[]" value="" style="width:300px;">
                </td>-->
                <td style="padding-left:10px;">
                    <input type="number" class="product_qtt form-control" name="product_qtt[]" value="1" style="width:60px;">
                </td>
                <td style="padding-left:10px;">
                    <input type="text" class="product_price form-control" name="product_price[]" value="">
                </td>
                <input type="text" name="validate" id="validate" value="1" hidden>
                </tr>
                <?php
                $i++;
            }
            ?>
        </table>
    
<?php } ?>
<script src="../../../../assets/default/jQuery-Mask-Plugin-master/src/jquery.mask.js"></script>
<script type="text/javascript">
                    $(document).ready(function () {
                        $('.product_price').mask("#.##0,00", {reverse: true});

                    });
                    function disable_line(product_id)
                    {
                        if ($("#product_name" + product_id).prop('disabled'))
                        {
                            $("#product_name" + product_id).prop('disabled', false);
                            $("#product_description" + product_id).prop('disabled', false);
                            $("#product_qtt" + product_id).prop('disabled', false);
                            $("#product_price" + product_id).prop('disabled', false);
                        } else
                        {
                            $("#product_name" + product_id).prop('disabled', true);
                            $("#product_description" + product_id).prop('disabled', true);
                            $("#product_qtt" + product_id).prop('disabled', true);
                            $("#product_price" + product_id).prop('disabled', true);
                        }
                    }
</script>