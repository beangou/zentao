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
<script type="text/javascript">
	function inputData() {
			link1 = createLink('indexcompare', 'ajaxInsertDefectData');
			$('#getResult').load(link1);
	}	
</script>
<body>
	<span id="getResult"></span>
	<input type="button" onclick="inputData()" value="生成数据" class="button-s"/>
	<table width="100%" class="cont-lt1">
		<tr valign="top">
		  <?php include 'left.html.php';?>
		  <td>
				<!-- <div class="week-title"><?php echo $lang->indexcompare->defectRate;?></div>-->
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
					<tr><th class='rowhead'></th><td class='a-center'><?php echo html::submitButton($lang->indexcompare->query);?></td></tr>
				</table>
  		   </form>
  		   <table class="table-1 fixed colored datatable border-sep" id="product">
  			  <thead>
  			  	<tr class="colhead">
  			  		<th><?php echo $lang->indexcompare->project;?></th>
  			  		<th><?php echo $lang->indexcompare->name;?></th>
  			  		<th><?php echo $lang->indexcompare->devBug;?></th>
  			  		<th><?php echo $lang->indexcompare->testBug;?></th>
  			  		<th><?php echo $lang->indexcompare->total;?></th>
  			  		<th><?php echo $lang->indexcompare->personalRate;?></th>
  			  		<th><?php echo $lang->indexcompare->perHisRate;?></th>
  			  	</tr>
  			  </thead>
  			  <tbody>
  			  <?php $color = false;?>
  			  <?php foreach ($personalRate as $defect):?>
  			  	<tr class="a-center">
  			  		<?php 
  			  			if ($defect->rowspanVal > 0) {
  			  				echo '<td rowspan="'. $defect->rowspanVal. '">'. $defect->projectname. '</td>';
  			  			}
  			  		?>
					<td><?php echo $defect->developer;?></td>
					<td><?php echo $defect->devbugs;?></td>
					<td><?php echo $defect->testbugs;?></td>
					<td><?php echo $defect->allbugs;?></td>
					<td><?php echo 100*round($defect->defect, 4). '%';?></td>
					<td>
						<?php
						//common::printIcon('indexcompare', 'perHisRate', "projectID=1&taskID=2", '', 'list', '', '_self', 'iframe', true);
						?>
						<a href="/zentao/www/index.php?m=indexcompare&amp;f=perHisRate&amp;account=<?php echo $defect->account;?>&amp;realname=<?php echo $defect->developer;?>&amp;onlybody=yes" target="" class="link-icon iframe cboxElement" title="查看">查看</a>
					</td>
  			  	</tr>
  			  <?php endforeach;?>
  			  </tbody>
  		   </table>
		</td>
		  
		</tr>
	</table>
</body>
<?php include '../../common/view/footer.html.php';?>	