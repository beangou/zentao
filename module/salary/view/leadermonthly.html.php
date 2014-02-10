<td>
	<form method="post" action='<?php echo $this->createLink('salary', 'monthly');?>'>
            	<div style="display: none;">
            	  <?php echo html::input('typeID',1);?>
            	</div>
            	<div>
            	<div style="margin-bottom: 12px;float: left;">
            	  <?php echo html::input('finishedDate',isset($finishDate)?$finishDate:date('Y-m-t',time()),"class='w-date date'")?>
            	  <?php echo html::submitButton('查询');?>
            	</div>
            	<div style="float: right;margin-right: 10%;">
            	<?php common::printLink('salary', 'monthly','typeID=3&finishedDate='.$finishDate,$lang->salary->leaderDetail);?></div>
            	</div>
        <div id="increase" style="width: 90%; height: 400px; margin: 0 auto"></div>
        <div id="hours" style="width: 45%; height: 400px; margin: 0 auto"></div>
        <table id="datatable" style="display: none">
       	 	<thead>
			<tr>
				<th></th>
				<th>当月总工时(小时)</th>
				<th>当月平均工时</th>
			</tr>
			</thead>
			
			
			<tbody>
		<tr>
			<th>合作伙伴</th>
			<td><?php echo isset($allStaffHours[0]->sum)?$allStaffHours[0]->sum:0;?></td>
			<td><?php echo isset($allStaffHours[0]->average)?$allStaffHours[0]->average:0;?></td>
		</tr>
		<tr>
			<th>ICT全体人员</th>
			<td><?php echo isset($allStaffHours[1]->sum)?$allStaffHours[1]->sum:0;?></td>
			<td><?php echo isset($allStaffHours[1]->average)?$allStaffHours[1]->average:0;?></td>
		</tr>
		<tr>
			<th>全体人员</th>
			<td><?php echo isset($allStaffHours[2]->sum)?$allStaffHours[2]->sum:0;?></td>
			<td><?php echo isset($allStaffHours[2]->average)?$allStaffHours[2]->average:0;?></td>
		</tr>
	</tbody>
		</table>
	<div id="personNum" style="width: 45%; height: 400px; margin: 0 auto"></div>
	
	<table id="increasetable" style="display: none">
       	 	<thead>
			<tr>
				<th></th>
				<th>月度薪酬增减</th>
			</tr>
			</thead>
			
			
			<?php $i=0;?>
			<?php foreach ($salaryIncrease as $increase):?>
			<?php $i++;?>
			<tbody>
			<tr>
				<th><?php echo $increase->realname;?></th>
				<td><?php echo isset($increase->finalSalary)?$increase->finalSalary-$increase->standsalary:0;?></td>
			</tr>
			</tbody>
		<?php endforeach;?>
		</table>
      	</form>
      </td>