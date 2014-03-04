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
  		    <caption><div align="center"><?php echo $developer. $lang->defect->perHisRateTitle;?></div></caption>
  			  <thead>
  			  	<tr class="colhead">
  			  		<th><?php echo $lang->defect->startDate;?></th>
  			  		<th><?php echo $lang->defect->endDate;?></th>
  			  		<th><?php echo $lang->defect->project;?></th>
  			  		<th><?php echo $lang->defect->devBug;?></th>
  			  		<th><?php echo $lang->defect->total;?></th>
  			  		<th><?php echo $lang->defect->personalRate;?></th>
  			  	</tr>
  			  </thead>
  			  <tbody>
  			  <?php $color = false;?>
  			  <?php foreach ($hisRateList as $hisRate):?>
  			  	<tr class="a-center">
  			  		<td><?php echo $hisRate->begin;?></td>
  			  		<td><?php echo $hisRate->end;?></td>
  			  		<td><?php echo $hisRate->name;?></td>
  			  		<td><?php echo $hisRate->devBug;?></td>
  			  		<td><?php echo $hisRate->total;?></td>
  			  		<td><?php echo $hisRate->defect;?></td>
  			  	</tr>
  			  <?php endforeach;?>
  			  </tbody>
  		   </table>
</body>
<?php include '../../common/view/footer.html.php';?>	