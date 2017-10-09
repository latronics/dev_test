<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

    <head>

        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

        <title>Upload page</title>



    </head>

    <body>

        <div id="container">

            <div id="form" class="control-label">



                <?php
//$deleterecords = "TRUNCATE TABLE ip_products"; //empty the table of its current records
//mysql_query($deleterecords);
//Upload File

                if (isset($_POST['submit'])) {

                    if (is_uploaded_file($_FILES['filename']['tmp_name'])) {

                        //echo "<h1>" . "File ". $_FILES['filename']['name'] ." uploaded successfully." . "</h1>";
                        //echo "<h2>Displaying contents:</h2>";
                        //readfile($_FILES['filename']['tmp_name']);
                    }



                    //Import uploaded file to Database

                    $handle = fopen($_FILES['filename']['tmp_name'], "r");
                    $array = explode('.', $_FILES['filename']['name']);
                    $extension = end($array);


                    if (($extension != "CSV") && ($extension != "csv")) {
                        echo "<script>alert('$extension is not accepted! Only CSV Files!'); window.open('" . base_url('index.php/settings') . "','_self');</script>";
                    }
                    $firstRow = true;

                    if ($handle != null) {
                        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {


                            if ($firstRow) {
                                $firstRow = false;
                            } else {

                                $products_from_qb_data = array(
                                    'active_status' => $data[1],
                                    'type' => $data[2],
                                    'product_name' => $data[3],
                                    'product_description' => $data[4],
                                    'sales_tax_code' => $data[5],
                                    'account' => $data[6],
                                    'cogs_account' => $data[7],
                                    'asset_account' => $data[8],
                                    'accumulated_depreciation' => $data[9],
                                    'purchase_description' => $data[10],
                                    'quantity_on_hand' => $data[11],
                                    'purchase_price' => $data[12],
                                    'preferred_vendor' => $data[13],
                                    'tax_agency' => $data[14],
                                    'product_price' => $data[15],
                                    'reorder_pt' => $data[16],
                                    'mpn' => $data[17]
                                );

                                $this->db->insert('ip_products', $products_from_qb_data);
                                //$import="INSERT into ip_products(product_id,family_id,product_sku,product_name,product_description) values('$data[0]','$data[1]','$data[2]','$data[3]','$data[4]')";
                                //mysql_query($import) or die(mysql_error());
                            }
                        }


                        fclose($handle);


                        echo "<script>window.alert('Import Succefull !'); window.open('" . base_url('index.php/settings') . "','_self'); </script>";
                    } else {

                        echo "<script>window.alert('Please select a file first !'); window.open('" . base_url('index.php/settings') . "','_self'); </script>";
                    }



                    //view upload form
                } else {



                    print "<b>Upload CSV Services/Products file from QuickBooks</b><br />\n";



                    print "<form enctype='multipart/form-data' action='settings/csv_import' method='post'>";
                    ?>
                    <input size='50' type='file' name='filename' accept=".csv" onchange="show_submit()" class="input-sm form-control" style="width: 400px;"><br />


                        <div id ="submit" hidden>
                            <input type='submit' name='submit'  value='Upload' class="btn btn-success btn-sm ajax-loader"></form>
                    <?php
                }
                ?>

                    </div>
            </div>


        </div>

    </body>
    <script>
        function show_submit()
        {
            $("#submit").show();
        }
    </script>
</html>
