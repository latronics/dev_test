<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>jQuery UI Draggable - Default functionality</title>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="/resources/demos/style.css">
  <style>
  #draggable { width: 150px; height: 150px; padding: 0.5em; }
  #draggable2 { width: 150px; height: 150px; padding: 0.5em; }
  </style>
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
  <script>
  $( function() {
    $( "#draggable" ).draggable();
    $( "#draggable2" ).draggable();
  } );
  </script>
</head>
<body>
 
<div id="draggable" class="ui-widget-content">
  <p>Drag me around</p>
</div>
    <div id="draggable2" class="ui-widget-content">
  <p>Drag me around</p>
</div>
 
 
</body>
</html>