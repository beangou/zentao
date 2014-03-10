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
  		    <caption><div align="center"><?php echo $developer. $lang->indexcompare->perHisRateTitle;?></div></caption>
  			  <thead>
  			  	<tr class="colhead">
  			  		<th><?php echo $lang->indexcompare->startDate;?></th>
  			  		<th><?php echo $lang->indexcompare->endDate;?></th>
  			  		<th><?php echo $lang->indexcompare->project;?></th>
  			  		<th><?php echo $lang->indexcompare->devBug;?></th>
  			  		<th><?php echo $lang->indexcompare->total;?></th>
  			  		<th><?php echo $lang->indexcompare->personalRate;?></th>
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
  			  		<td><?php echo 100*round($hisRate->defect, 4). '%'?></td>
  			  	</tr>
  			  <?php endforeach;?>
  			  </tbody>
  		   </table>
</body>
<?php include '../../common/view/footer.html.php';?>	