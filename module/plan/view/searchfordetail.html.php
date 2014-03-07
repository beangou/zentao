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
  		   <table class="table-1 fixed colored datatable border-sep" id="product">
  		    <caption><div align="center">查看周计划详细信息</div></caption>
  			  <thead>
				      <th width="20%">时间</th>
				      <th><?php echo $lang->plan->sort;?></th>
				      <th width="35%"><?php echo $lang->plan->matter;?></th>
       			      <th><?php echo $lang->plan->plan;?></th>
				      <th>完成时限</th>
				      
				      <th>完成情况</th>
				      <th>见证性材料</th>
				      <th>未完成原因说明及如何补救</th>
				      <th>审核人</th>
				      <th>是否审核</th>
				      
				      <th>审核结果</th>
				      <th>备注</th>  			  
  			  </thead>
  			  
  			  <tbody>
  			  		<td><?php echo $detailPlan->firstDayOfWeek. '~'. $detailPlan->lastDayOfWeek;?></td>
  			  		<td><?php $detailPlan->type;?></td>
  			  		<td><?php $detailPlan->matter;?></td>
  			  		<td><?php $detailPlan->plan;?></td>
  			  		<td><?php $detailPlan->deadtime;?></td>
  			  		
  			  		<td><?php $detailPlan->status;?></td>
  			  		<td><?php $detailPlan->evidence;?></td>
  			  		<td><?php $detailPlan->courseAndSolution;?></td>
  			  		<td><?php $detailPlan->submitTo;?></td>
  			  		<td><?php $detailPlan->confirmedOrNo;?></td>
  			  		
  			  		<td><?php $detailPlan->confirmed;?></td>
  			  		<td><?php $detailPlan->remark;?></td>
  			  </tbody>
  		   </table>
</body>
<?php include '../../common/view/footer.html.php';?>	