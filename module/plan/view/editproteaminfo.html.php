<?php 
/*member group settings*/
?>
<?php include '../../common/view/header.html.php';
 	  include '../../common/view/tablesorter.html.php';
 	  include '../../common/view/colorize.html.php';
 	  include '../../common/view/datepicker.html.php';
?>
<?php include '../../common/view/form.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<?php js::set('holders', $lang->task->placeholder);?>
<style>
.rowcolor{background:#F9F9F9;}
</style>

<body>
	<form method='post' target='hiddenwin'>
  		   <table class="table-1 colored datatable border-sep" id="product">
  		    <caption><div align="center">编辑项目组信息</div></caption>
  			  <thead>
				  <tr>    
				      <th width="30%">项目组名称</th>
				      <td>
				      	<input type="text" style="width:461px;height:25px" name="team" value="<?php echo $proteamInfo->team;?>"/>
				      	<input type="hidden" name="infoId" value="<?php echo $proteamInfo->id;?>"/>
				      </td>
				  </tr>
				  <tr>    
				      <th>组长</th><td><?php echo html::select('leader', $nowLeaders, 'hujun', "class='select-1'");?></td>
				  </tr>
				  <tr>    
				      <th width="35%">技术经理</th>
				      <td>
				      	<?php 
				      	echo html::select('techmanager[]', $nowManagers, str_replace(' ', '', ''), 'class="text-1" multiple style="vertical-align:middle"');
// 				      	if($contactLists) echo html::select('', $contactLists, '', "class='f-right' style='vertical-align:middle' onchange=\"setMailto('mailto', this.value)\"");
// 				      	echo html::select('', $users, '', "class='f-right' style='vertical-align:middle' onchange=\"setMailto('mailto', this.value)\"");?>
				      </td>
				  </tr>
				  <tr>
				      <td colspan='2' class='a-center'><?php echo html::submitButton();?></td>
				  </tr>
  			  </thead>
  		   </table>
  		   
  	</form>	   
</body>
<?php include '../../common/view/footer.html.php';?>	