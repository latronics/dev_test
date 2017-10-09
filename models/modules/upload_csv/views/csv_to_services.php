<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title>Upload page</title>



</head>

<body>

<div id="container">

<div id="form">

 

<?php

 




$deleterecords = "TRUNCATE TABLE ip_products"; //empty the table of its current records

mysql_query($deleterecords);

 

//Upload File

if (isset($_POST['submit'])) {

    if (is_uploaded_file($_FILES['filename']['tmp_name'])) {

       
       

    }

 


//exclude the first row

$handle = fopen($_FILES['filename']['tmp_name'], "r");

$firstRow = true;

 

while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

 

    if($firstRow) { $firstRow = false; }

    else {

        $products_from_qb_data = array (
    
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

    }

}

fclose($handle);

 
 echo "<script>alert('Import Done!'); </script>";
 echo "<script>window.open('".base_url('indexx')."', '_self'); </script>";
   
   

 

    //view upload form

}else {

 

    print "Upload new csv by browsing to file and clicking on Upload<br />\n";

 

    print "<form enctype='multipart/form-data' action='settings/csv_import' method='post'>";

 

    print "File name to import:<br />\n";

 

    print "<input size='50' type='file' name='filename'><br />\n";



    print "<input type='submit' name='submit' value='Upload'></form>";

 

}

 

?>

 

</div>

</div>

</body>

</html>
