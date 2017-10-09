

{if isset($loaddata)}

<h4>Data assigned to Order {$id}</h4><br />

<div class="pad" data-jsfiddle="example1" >
<button name="load" style="display:none;">Load</button> <label style="display:none;"><input type="checkbox" style="display:none;" name="autosave" checked="checked" autocomplete="off"> Autosave</label>

<div id="Console1" class="console" style="font-size:16px; color:#6A8DFD;"></div>

<br clear="all">

<div id="container1"></div>

<br clear="all" >

      <div class="codeLayout">
        <div class="pad">
          <script>{literal}
            var
              $container = $("#container1"),
              $console = $("#Console1"),
              $parent = $container.parent(),
			  autosaveNotification,
              hot;

            hot = new Handsontable($container[0], {
              columnSorting: true,
              startRows: {/literal}{$startrows}{literal},
              startCols: {/literal}{$startcols}{literal},
              rowHeaders: true,
              colHeaders: [{/literal}{$headers}{literal}],
			  colWidths: [{/literal}{$width}{literal}],
              columns: [{/literal}{$colmap}{literal}],
              minSpareCols: 0,
              minSpareRows: 0,
			  manualColumnResize: true,
			  manualRowResize: true,
			  currentRowClassName: 'currentRow',
			  currentColClassName: 'currentCol',
			  data: [{/literal}{$loaddata}{literal}],			  
              contextMenu: false,
			  afterChange: function (change, source) {
                var data;

                if (source === 'loadData' || !$parent.find('input[name=autosave]').is(':checked')) {
                  return;
                }
                data = change[0];

                // transform sorted row to original row
                data[0] = hot.sortIndex[data[0]] ? hot.sortIndex[data[0]][0] : data[0];

                clearTimeout(autosaveNotification);
                $.ajax({
                  url: '{/literal}{$autosaveurl}{literal}',
                  dataType: 'json',
                  type: 'POST',
                  data: {changes: change}, // contains changed cells' data
                  success: function (res) {
                    $console.text('(' + change.length + ' cell' + (change.length > 1 ? 's' : '') + ') - ' + res.msg);

                    /*autosaveNotification = setTimeout(function () {
                      $console.text('Changes will be autosaved');
                    }, 1000);*/
					//alert(res.row);
					//alert(res.col);
					//data[res.row][res.col] = 'newval'; // change "Kia" to "Ford" programmatically
  					//hot.render();
                  }
                });
              }
            });       
			
             {/literal}
          </script>
        </div>
      </div>



{/if}