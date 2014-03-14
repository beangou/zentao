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
				  <tr>    
				      <th width="20%">时间</th><td><?php echo $detailPlan->firstDayOfWeek. '~'. $detailPlan->lastDayOfWeek;?></td>
				  </tr>
				  <tr>    
				      <th><?php echo $lang->plan->sort;?></th><td><?php $myplan->type;?></td>
				  </tr>
				  <tr>    
				      <th width="35%"><?php echo $lang->plan->matter;?></th><td><?php echo $detailPlan->matter;?></td>
				  </tr>
				  <tr>    
       			      <th><?php echo $lang->plan->plan;?></th><td><?php echo $detailPlan->plan;?></td>
       			  </tr>
       			  <tr>    
				      <th>完成时限</th><td><?php echo $detailPlan->deadtime;?></td>
				  </tr>
				  <tr>    
				      <th>完成情况</th><td><?php echo $detailPlan->status;?></td>
				  </tr>
				  <tr>    
				      <th>见证性材料</th><td><?php echo $detailPlan->evidence;?></td>
				  </tr>
				  <tr>    
				      <th>未完成原因说明及如何补救</th><td><?php echo $detailPlan->courseAndSolution;?></td>
				  </tr>
				  <tr>    
				      <th>确认人</th><td><?php echo $detailPlan->submitToName;?></td>
				  </tr>
				  <tr>
				      <th>确认结果</th><td><?php echo $detailPlan->confirmed;?></td>
				  </tr>
				  <tr>    
				      <th>备注</th><td><?php echo $detailPlan->remark;?></td>
				  </tr>				        			  
  			  </thead>
  		   </table>
</body>
<?php include '../../common/view/footer.html.php';?>	