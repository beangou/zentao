<?php 
/*member group settings*/
?>
<?php include '../../common/view/header.html.php';
 	  include '../../common/view/tablesorter.html.php';
 	  include '../../common/view/colorize.html.php';
 	  include '../../common/view/datepicker.html.php';
?>
<style>
.rowcolor{background:#F9F9F9;}
</style>
<body>
	<table width="100%" class="cont-lt1">
		<tr valign="top">
		  <td width="12%" style="padding-right: 10px;" class="side">
			  <div class="box-title"><?php echo $lang->indexcompare->titCompleted;?></div>
			  <div class="box-content">
				  <ul id="report-list">
				  	<li>
				  		<?php echo html::a($this->createLink('indexcompare', 'completed'), $lang->indexcompare->proCompleted);?>
				  	</li>
				  	<li>
				  		<?php echo html::a($this->createLink('indexcompare', 'perCompleted'), $lang->indexcompare->perCompleted);?>
				  	</li>
				  </ul>
			  </div>
		  </td>
		  <td>
				<!-- <div class="week-title"><?php echo $lang->indexcompare->titCompleted;?></div>-->
			<form method="post">
				<table align='center' class='table-1 a-left'>
					<tr>
						<th><?php echo $lang->indexcompare->choose.'：';?><?php echo html::selectAll('defect', 'checkbox', false);?></th>
						<td id='defect' class='f-14px pv-10px'>
						<?php $i = 1;?>
				        <?php foreach($products as $product => $name):?>
				        <div style="width: 180px;" class='f-left'><?php echo '<span>' . html::checkbox("ids", array($product => $name), '') . '</span>';?></div>
				        <?php if(($i %  5) == 0) echo "<div class='c-both'></div>"; $i ++;?>
				        <?php endforeach;?>
					</td>
					</tr>
					<tr><th class='rowhead'></th><td class='a-center'><?php echo html::submitButton($lang->defect->query);?></td></tr>
				</table>
  		   </form>
  		   <table class="table-1 fixed colored datatable border-sep" id="product">
  			  <thead>
  			  	<tr class="colhead">
  			  		<th width='200'><?php echo $lang->indexcompare->productName;?></th>
  			  		<th width='260'><?php echo $lang->indexcompare->projectName;?></th>
  			  		<th><?php echo $lang->indexcompare->proCloseTasks;?></th>
  			  		<th><?php echo $lang->indexcompare->proAllTasks;?></th>
  			  		<th><?php echo $lang->indexcompare->proCompleted;?></th>
  			  	</tr>
  			  </thead>
  			  <tbody>
  			  <?php $color = false;?>
  			  <?php foreach ($tasks as $task):?>
  			  	<tr class="a-center">
  			  		<?php 
  			  			if ($task->rowspanVal > 0) {
  			  				echo '<td rowspan="'. $task->rowspanVal. '">'. $task->productname. '</td>';
  			  			}
  			  		?>
  			  		<td><?php echo $task->projectname;?></td>
  			  		<td><?php echo $task->closedtasks;?></td>
  			  		<td><?php echo $task->alltasks;?></td>
  			  		<td><?php echo 100*$task->completed. '%';?></td>
  			  	</tr>
  			  <?php endforeach;?>
  			  </tbody>
  		   </table>
		</td>
		  
		</tr>
	</table>
</body>
<?php include '../../common/view/footer.html.php';?>	