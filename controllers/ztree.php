<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

////MITKO
/// TEST CONTROLLER FOR CREATION OF PDF...

class ztree extends Controller {

	function ztreetest()
	{		
		parent::Controller();	
					
	}
	function index() 
	{
	echo '
	
	<!DOCTYPE html>
<HTML>
<HEAD>
	<TITLE> ZTREE DEMO - async & edit</TITLE>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" href="'.Site_url().'css/demoztree.css" type="text/css">
	<link rel="stylesheet" href="'.Site_url().'css/zTreeStyle/zTreeStyle.css" type="text/css">
	<script type="text/javascript" src="'.Site_url().'js/jquery-1.4.4.min.js"></script>
	<script type="text/javascript" src="'.Site_url().'js/jquery.ztree.core.js"></script>
	<script type="text/javascript" src="'.Site_url().'js/jquery.ztree.excheck.js"></script>
	<script type="text/javascript" src="'.Site_url().'js/jquery.ztree.exedit.js"></script>
	<SCRIPT type="text/javascript">
		<!--
		var setting = {
			async: {
				enable: true,
				url:"'.Site_url().'ztree/async",				
				autoParam:["id", "name=n", "level=lv"],
				otherParam:{"otherParam":"zTreeAsyncTest"},
				dataFilter: filter
			},
			view: {expandSpeed:"",
				addHoverDom: addHoverDom,
				removeHoverDom: removeHoverDom,
				selectedMulti: false
			},
			edit: {
				enable: true
			},
			data: {
				simpleData: {
					enable: true
				}
			},
			callback: {
				beforeRemove: beforeRemove,
				beforeRename: beforeRename
			}
		};

		function filter(treeId, parentNode, childNodes) {
			if (!childNodes) return null;
			for (var i=0, l=childNodes.length; i<l; i++) {
				childNodes[i].name = childNodes[i].name.replace(/\.n/g, \'.\');
			}
			return childNodes;
		}
		function beforeRemove(treeId, treeNode) {
			var zTree = $.fn.zTree.getZTreeObj("treeDemo");
			zTree.selectNode(treeNode);
			return confirm("Confirm delete node \'" + treeNode.name + "\' it?");
		}		
		function beforeRename(treeId, treeNode, newName) {
			if (newName.length == 0) {
				setTimeout(function() {
					var zTree = $.fn.zTree.getZTreeObj("treeDemo");
					zTree.cancelEditName();
					alert("Node name can not be empty.");
				}, 0);
				return false;
			}
			return true;
		}

		var newCount = 1;
		function addHoverDom(treeId, treeNode) {
			var sObj = $("#" + treeNode.tId + "_span");
			if (treeNode.editNameFlag || $("#addBtn_"+treeNode.tId).length>0) return;
			var addStr = "<span class=\'button add\' id=\'addBtn_" + treeNode.tId
				+ "\' title=\'add node\' onfocus=\'this.blur();\'></span>";
			sObj.after(addStr);
			var btn = $("#addBtn_"+treeNode.tId);
			if (btn) btn.bind("click", function(){
				var zTree = $.fn.zTree.getZTreeObj("treeDemo");
				zTree.addNodes(treeNode, {id:(100 + newCount), pId:treeNode.id, name:"new node" + (newCount++)});
				return false;
			});
		};
		function removeHoverDom(treeId, treeNode) {
			$("#addBtn_"+treeNode.tId).unbind().remove();
		};

		$(document).ready(function(){
			$.fn.zTree.init($("#treeDemo"), setting);
		});
		//-->
	</SCRIPT>
	<style type="text/css">
.ztree li span.button.add {margin-left:2px; margin-right: -1px; background-position:-144px 0; vertical-align:top; *vertical-align:middle}
	</style>
</HEAD>

<BODY>
<h1>Editing Dynamic Tree</h1>
<h6>[ File Path: exedit/async_edit.html ]</h6>
<div class="content_wrap">
	<div class="zTreeDemoBackground left">
		<ul id="treeDemo" class="ztree"></ul>
	</div>	
</div>
</BODY>
</HTML>
	
	';
	}
	function async()
	{
	echo '[';
$pId = "0";
$pName = "";
$pLevel = "";
$pCheck = "";
if(array_key_exists( 'id',$_REQUEST)) {
	$pId=$_REQUEST['id'];
}
if(array_key_exists( 'lv',$_REQUEST)) {
	$pLevel=$_REQUEST['lv'];
}
if(array_key_exists('n',$_REQUEST)) {
	$pName=$_REQUEST['n'];
}
if(array_key_exists('chk',$_REQUEST)) {
	$pCheck=$_REQUEST['chk'];
}
if ($pId==null || $pId=="") $pId = "0";
if ($pLevel==null || $pLevel=="") $pLevel = "0";
if ($pName==null) $pName = "";
else $pName = $pName.".";

$pId = htmlspecialchars($pId);

$pName = htmlspecialchars($pName);

//for ($i=1; $i<9999; $i++) {
//	for ($j=1; $j<999; $j++) {
//
//	}
//}

for ($i=1; $i<5; $i++) {
	$nId = $pId.$i;
	$nName = $pName."n".$i;
	echo "{ id:'".$nId."',	name:'".$nName."',	isParent:".(( $pLevel < "2" && ($i%2)!=0)?"true":"false").($pCheck==""?"":((($pLevel < "2" && ($i%2)!=0)?", halfCheck:true":"").($i==3?", checked:true":"")))."}";
	if ($i<4) {
		echo ",";
	}
}
	echo ']';
		
	}
	function bigasync()
	{
	?>
[<?php
$pId = "-1";
if(array_key_exists( 'id',$_REQUEST)) {
	$pId=$_REQUEST['id'];
}
$pCount = "10";
if(array_key_exists( 'count',$_REQUEST)) {
	$pCount=$_REQUEST['count'];
}
if ($pId==null || $pId=="") $pId = "0";
if ($pCount==null || $pCount=="") $pCount = "10";

$pId = htmlspecialchars($pId);

$max = (int)$pCount;
for ($i=1; $i<=$max; $i++) {
	$nId = $pId."_".$i;
	$nName = "tree".$nId;
	echo "{ id:'".$nId."',	name:'".$nName."'}";
	if ($i<$max) {
		echo ",";
	}
	
}
?>]<?php
		
	}
}
	