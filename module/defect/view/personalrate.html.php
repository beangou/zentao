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
		  <?php include 'left.html.php';?>
		  <td>
				<!-- <div class="week-title"><?php echo $lang->defect->defectRate;?></div>-->
			<form method="post">
				<table align='center' class='table-1 a-left'>
					<tr>
						<th><?php echo $lang->defect->choose.'：';?><?php echo html::selectAll('defect', 'checkbox', false);?></th>
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
  			  		<th width='260'><?php echo $lang->defect->project;?></th>
  			  		<th><?php echo $lang->defect->startDate;?></th>
  			  		<th><?php echo $lang->defect->endDate;?></th>
  			  		<th><?php echo $lang->defect->name;?></th>
  			  		<th><?php echo $lang->defect->personalDefect;?></th>
  			  		<th><?php echo $lang->defect->personalRate;?></th>
  			  	</tr>
  			  </thead>
  			  <tbody>
  			  <?php $color = false;?>
  			  <?php foreach ($personalRate as $defect):?>
  			  	<tr class="a-center">
  			  		<?php $count = isset($defect->details) ? count($defect->details) : 1;?>
  			  		<td align='left' rowspan="<?php echo $count;?>"><?php echo "<p>" . $defect->name. "</p>";?></td>
  			  		<td align='left' rowspan="<?php echo $count;?>"><?php echo $defect->begin;?></td>
  			  		<td align='left' rowspan="<?php echo $count;?>"><?php echo $defect->end;?></td>
  			  		<?php if(isset($defect->details)):?>
		            <?php $id = 1;?>
		            <?php foreach($defect->details as $account):?>
		            <?php $class = $color ? 'rowcolor' : '';?>
		            <?php if($id != 1) echo "<tr class='a-center'>"?>
  			  		<td><?php echo $account->realname;?></td>
  			  		<td><?php echo $account->self;?></td>
            		<td><?php echo !empty($account->rate)?round($account->rate*100,2).'%':$account->rate;?></td>
  			  		<?php if($id != 1) echo "</tr>"?>
		            <?php $id ++;?>
		            <?php $color = !$color;?>
		            <?php endforeach;?>
            		<?php else:?>
	              <?php $class = $color ? 'rowcolor' : '';?>
	              <td class="<?php echo $class;?>"></td>
	              <td class="<?php echo $class;?>"></td>
	              <td class="<?php echo $class;?>"></td>
	              <?php $color = !$color;?>
	              <?php endif;?>
  			  	</tr>
  			  <?php endforeach;?>
  			  </tbody>
  		   </table>
		</td>
		  
		</tr>
	</table>
</body>
<?php include '../../common/view/footer.html.php';?>	