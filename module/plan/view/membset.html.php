<?php 
/*member group settings*/
?>
<?php include '../../common/view/header.html.php';
 	  include '../../common/view/tablesorter.html.php';
 	  include '../../common/view/colorize.html.php';
 	  include '../../common/view/datepicker.html.php';
?>
<body>

	   <div id='topmyplan'>
	    <div class='f-left'>
	      <?php 
	      foreach($lang->plan->periods as $period => $label)
	      {
	          $vars = $period;
	//           if($period == 'before') $vars .= "&account={$app->user->account}&status=undone";
	          echo "<span id='$period'>" . html::a(inlink($vars), $label) . '</span>';
	      }
	      ?>
	    </div>
	  </div>
	
	<table width="100%" style="border: 0">
		<tr valign="top">
			<td valign="top">
				<div class="week-title"><?php echo $lang->plan->membset;?></div>
				<form method="post">
					<table align='center' class='table-1 a-left'>
						<tr>
							<th><?php echo $lang->plan->choose.'：';?><?php echo html::selectAll('plan', 'checkbox', false);?></th>
							<td id='plan' class='f-14px pv-10px'>
							<?php $i = 1;?>
					        <?php foreach($users as $account => $realname):?>
					        <div class='w-p10 f-left'><?php echo '<span>' . html::checkbox('members', array($account => $realname), '') . '</span>';?></div>
					        <?php if(($i %  8) == 0) echo "<div class='c-both'></div>"; $i ++;?>
					        <?php endforeach;?>
						</td>
						</tr>
						<tr><th class='rowhead'></th><td class='a-center'><?php echo html::submitButton().html::backButton();?></td></tr>
					</table>
	  			</form>
	  			<form method="post">
					<table width="100%" class="table-memb">
			  			<tr class='colhead'>
			  				<th><?php echo $lang->plan->realname;?></th>
			  				<th><?php echo $lang->plan->dept;?></th>
			  				<th><?php echo $lang->plan->team;?></th>
			  				<th><?php echo $lang->plan->leader;?></th>
			  				<th><?php echo $lang->plan->auditor;?></th>
			  				<th><?php echo $lang->actions;?></th>
			  			</tr>
			  			<?php foreach ($members as $memb):?>
			  			<tr>
			  				<td><?php echo $memb->realname;?></td>
			  				<td><?php echo $memb->name;?></td>
			  				<td><?php 
			  				if ($memb->leader == '1')echo $memb->team;
			  				else echo html::select("proteam[$memb->id]",$teams,$memb->proteam,"onchange=\"loadLeader($memb->id, this.value)\"");?></td>
			  				<td id="leaderId_<?php echo $memb->id;?>"><?php echo $memb->leadname;?></td>
			  				<td align="center">
			  				  <table style="border: none;height: 20px;margin-top: 8px;">
			  				    <tr>
			  				      <td id="auditors<?php echo $memb->id ;?>">
			  				      <?php if (isset($memb->rel1)):?>
			  				      <input value='<?php echo isset($memb->rel1)?$memb->rel1:'' ;?>' style='border:0;width: 60px;' readonly/>
			  				      <?php endif;?>
			  				      <?php if (isset($memb->rel2)):?>
			  				      	<input style='border:0;width: 60px;' value="<?php echo $memb->rel2;?> " readonly/>
			  				      <?php endif;?></td>
			  				    </tr>
			  				  </table>
			  				  
			  				</td>
			  				
			  				<td><?php common::printIcon('plan', 'delete', "id=$memb->id&module=memb&date=0", '', 'list', '', "hiddenwin");?></td>
			  			</tr>
			  			<?php endforeach;?>
			  			<tr>
			  				<td colspan="5" class='a-center'><?php echo html::submitButton().html::backButton();?></td>
			  			</tr>
		  			</table>
	  			</form>
			</td>
		</tr>
	</table>
</body>
<?php include '../../common/view/footer.html.php';?>	