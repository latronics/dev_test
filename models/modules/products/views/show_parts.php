<div id = "Success" hidden align="center"><font style="color: green;">Success!</font></div>
<table id="products_table" class="table table-bordered table-striped">
    <tr>
        <th>&nbsp;</th>
        <th><?php echo lang('product_sku'); ?></th>
        <th><?php echo lang('bcn'); ?></th>
        <th><?php echo lang('status'); ?></th>


        <th><?php echo lang('product_name'); ?></th>
        <th><?php echo lang('product_description'); ?></th>
        <th class="text-right"><?php echo lang('product_price'); ?></th>
    </tr>

    <?php foreach ($warehouse_data as $product) { ?>
        <tr class="product">
            <td class="text-left">
                <input type="checkbox" name="parts_ids[]" class ="parts_ids" onclick ="click_checkbox()"  id  ="mark"
                       value="<?php echo $product['bcn']; ?>">
            </td>
            <td nowrap class="text-left">
                <b><?php echo $product['psku']; ?></b>
            </td>

            <td nowrap class="text-left">
                <b><?php echo $product['bcn']; ?></b>
            </td>
            <td nowrap class="text-left">
                <b><?php echo $product['status']; ?></b>
            </td>

            <td>
                <b><?php echo $product['title']; ?></b>
            </td>
            <td>
                <?php echo nl2br($product['notes']); ?>
            </td>
            <td class="text-right">
                <?php echo "$" . number_format($product['cost'], 2); ?>
            </td>
        </tr>

    <?php } ?></div>
</table>
<script>
/*
 * var stringId = [];
$('.parts_ids').click(function() {

         if(this.checked===true){
             
        stringId.push($(this).val());
            
             
            alert(stringId);
    }else{
         
            var index = stringId.indexOf($(this).val());
         
            stringId.splice(index);

           
            alert(stringId);
            
    }
      });*/
       

        
    
         

        $('.select-items-confirm').click(function () {
  stringId = $('.parts_ids').serialize();

            $.post("<?php echo site_url('parts/insert_part_on_quote'); ?>", {
                id_part: stringId,
                invoice_ticket: window.location.href

            },
                    function (data) {
                        //alert(data);
                        //$("#modal-choose-items").modal('toggle');
                        location.reload();

                    });




        });
    











</script>