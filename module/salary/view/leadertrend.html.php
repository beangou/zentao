<td>
	<form method="post" action='<?php echo $this->createLink('salary', 'monthly');?>'>
		<div style="margin-bottom: 12px;">
            	  <?php echo html::input('startDate',isset($startDate)?$startDate:date('Y-m-01',time()),"class='w-date date'");?>至
            	  <?php echo html::input('finishedDate',isset($finishDate)?$finishDate:date('Y-m-t',time()),"class='w-date date'");?>
            	  <input type="hidden" name='typeID' value='2'/>
            	  <?php echo html::submitButton('查询');?>
        </div>
      </form>
		<div id="eachMonth" style="width: 80%; height: 400px; margin: 0 auto"></div>
		<table id="eachMonthtable" style="display: none;">
	<thead>
		<tr>
			<th></th>
			<th>科大国创(单位:小时)</th>
			<th>ICT系统开发部(单位:小时)</th>
			<th>汇总</th>
		</tr>
	</thead>
	<?php foreach ($eachHours as $each):?>
	<tbody>
		<tr>
			<th><?php echo $each->date;?></th>
			<td><?php echo $each->ustSum;?></td>
			<td><?php echo $each->sum;?></td>
			<td><?php echo $each->ustSum+$each->sum;?></td>
		</tr>
	</tbody>
	<?php endforeach;?>
</table>

<div id="personNum" style="display:none"></div>
<div id="eachAverage" style="width: 80%; height: 400px; margin: 0 auto"></div>
		<table id="eachAveragetable" style="display: none;">
	<thead>
		<tr>
			<th></th>
			<th>所有人员</th>
			<th>ICT系统开发部</th>
		</tr>
	</thead>
	<?php foreach ($eachAverage as $average):?>
	<tbody>
		<tr>
			<th><?php echo $average->date;?></th>
			<td><?php echo $average->allSum;?></td>
			<td><?php echo $average->sum;?></td>
		</tr>
	</tbody>
	<?php endforeach;?>
</table>

<div id="salaryPayAnalysis" style="min-width: 40%; height: 400px; margin: 0 auto;display: inline-block;"></div>
<div id="salaryContrast" style="width: 40%; height: 400px; margin: 0 auto"></div>
		<table id="salaryContrasttable" style="display: none;">
	<thead>
		<tr>
			<th></th>
			<th>超过月度标准薪酬人数</th>
			<th>低于月度标准薪酬人数</th>
		</tr>
	</thead>
	<?php foreach ($salaryContrast as $contrast):?>
	<tbody>
		<tr>
			<th><?php echo $contrast['date'];?></th>
			<td><?php echo isset($contrast['upNum'])?$contrast['upNum']:0;?></td>
			<td><?php echo isset($contrast['lowNum'])?$contrast['lowNum']:0;?></td>
		</tr>
	</tbody>
	<?php endforeach;?>
</table>
      </td>