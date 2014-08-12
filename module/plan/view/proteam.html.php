<?php 
/*project group settings*/
error_reporting(E_ALL);
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/form.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<?php js::set('holders', $lang->task->placeholder);?>
<body>
  <div id='topmyplan'>
    <div class='f-left'>
      <?php 
      foreach($mymenu as $period => $label)
      {
          $vars = $period;
//           if($period == 'before') $vars .= "&account={$app->user->account}&status=undone";
          echo "<span id='$period'>" . html::a(inlink($vars), $label) . '</span>';
      }
      ?>
    </div>
  </div>
  
	<table width="100%" id="proteam" style="border: 0">
	  <tr>
	  	<td valign="top">
	  		<div class="week-title"><?php echo $lang->plan->proteam;?></div>
		  		<div class="prot-add">
		  		<form method="post">
		  			<table width="100%" style="border: 0">
		  				<tr>
		  				<td><?php echo $lang->plan->team.'：'.html::input('team','',"class='text-2'");?></td>
		  				<td><?php echo $lang->plan->leader.'：'.html::select('leader',$users,'',"class='select-2'");?></td>
		  				<td>
		  					<?php
					        echo '技术经理：'. html::select('techmanager[]', $users, str_replace(' ', '', ''), 'class="text-1" multiple style="vertical-align:middle"');
					        //if($contactLists) echo html::select('', $contactLists, '', "class='f-right' style='vertical-align:middle' onchange=\"setMailto('mailto', this.value)\"");
					        ?>
		  				</td>
		  				<td><?php echo html::submitButton().html::resetButton().html::backButton();?></td>
		  				</tr>
		  			</table>
				</form>
	  		</div>
	  		<table width="100%" style="text-align: center;">
	  			<tr class='colhead'>
	  				<th></th>
	  				<th><?php echo $lang->plan->team;?></th>
	  				<th><?php echo $lang->plan->leader;?></th>
	  				<th>技术经理</th>
	  				<th><?php echo $lang->actions;?></th>
	  			</tr>
	  			<?php $i = 0;?>
	  			<?php foreach ($proteam as $info):?>
	  			<?php $i++ ;?>
	  			<tr onmouseOver="this.style.backgroundColor='#D0DEE3';" onmouseout="this.style.backgroundColor='white';">
	  				<td><?php echo $i ;?></td>
	  				<td><?php echo $info->team ;?></td>
	  				<td><?php echo $info->leaderName ;?></td>
	  				<td><?php echo $info->managerName;?></td>
	  				<td>
	  				<?php common::printIcon('plan', 'delete', "id=$info->id&module=proteam&date=0", '', 'list', '', "hiddenwin");?>
	  				<!-- <a href='/zentaoZtrack/www/index.php?m=plan&amp;f=editproteaminfo&amp;infoId=<?php echo $info->id;?>&amp;onlybody=yes'
			       	 target="" class="link-icon iframe cboxElement icon-green-common-edit" title="编辑"/>
			       	  -->
	  				</td>
	  			</tr>
	  			<?php endforeach;?>
	  		</table>
		</td>
		</tr>
	</table>
	
	<script type="text/javascript">
		function editproteam(id) {
			alert(id);
			return false;
		}
	</script>
</body>
<?php include '../../common/view/footer.html.php';?>		   	