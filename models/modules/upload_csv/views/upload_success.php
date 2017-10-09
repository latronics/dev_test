<html>
 
   <head> 
      <title>Upload Form</title> 
   </head>
	
   <body>  
      <h3>Your file was successfully uploaded!</h3>  
		<?php print_r($data); ?>
      <ul> 
         <?php foreach ($upload_data as $item => $value) {?> 
         <li><?php echo $item;?>: <?php echo $value;?></li> 
         <?php } ?>
      </ul>  
		
      <p><?php echo anchor('upload_csv/upload/index', 'Upload Another File!'); ?></p>  
   </body>
	
</html>