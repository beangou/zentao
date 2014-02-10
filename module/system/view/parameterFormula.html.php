<td>
		<form method="post">
		<div class="a-leftS"><?php echo $lang->system->personnelFormula;?></div>
		<div>
		  <div id="name" class="a-title"><?php echo $lang->system->month;?>&nbsp;
		  <?php echo html::input('month',isset($_POST['month'])?$_POST['month']:date('Y-m-t',time()),"class='q-date date'")?></div>
		  <div id="name"><?php echo html::submitButton('查询');?></div>
		</div>
		</form>
		<form method="post">
      	<div class="a-title"><?php echo $lang->system->qualityProject;?></div>
      	<table style="width: 80%;" class="a-rewards">
        <thead>
        <tr class='colhead'>
          <th><?php echo '';?></th>
          <th><?php echo $lang->system->name;?></th>
          <th><?php echo $lang->system->integratedBug;?></th>
          <th><?php echo $lang->system->deliverBug;?></th>
          <th><?php echo $lang->system->onlineBug;?></th>
          <th><?php echo $lang->system->documentsPunish;?></th>
          <th><?php echo $lang->system->qualityRewards;?></th>
          <th><?php echo $lang->system->milestoneDelay;?></th>
          <th><?php echo $lang->system->projectBonus;?></th>
          <th><?php echo $lang->system->total;?></th>
          
        </tr>
        </thead>
        <tbody>
        <?php $i=0;?>
        <?php foreach ($rewards as $reward):?>
        <?php $i++;?>
          <tr>
          <td><?php echo "<input type='hidden' value='$reward->account' name='name[]'/>"?></td>
          <td><?php echo "<input type='hidden' value='$month' name='month'/>"?></td>
          </tr>
        <tr class='a-center' style="height: 35px;">
          <td><?php echo $i;?></td>
          <td width="8%"><?php echo html::input('',$reward->realname,"readonly style='border:0px'");?></td>
          <td class="edit" onkeyup="setRewards(<?php echo $i;?>)"><?php echo "<input type='text' value='$reward->integratedBug' name='integratedBug[]' id='integratedBug$i'/>";?></td>
          <td class="edit" onkeyup="setRewards(<?php echo $i;?>)"><?php echo "<input type='text' value='$reward->deliverBug' name='deliverBug[]' id='deliverBug$i'/>";?></td>
	      <td class="edit" onkeyup="setRewards(<?php echo $i;?>)"><?php echo "<input type='text' value='$reward->onlineBug' name='onlineBug[]' id='onlineBug$i'/>";?></td>
	      <td class="edit" onkeyup="setRewards(<?php echo $i;?>)"><?php echo "<input type='text' value='$reward->documentPunish' name='documentPunish[]' id='documentPunish$i'/>";?></td>
	      <td class="edit" onkeyup="setRewards(<?php echo $i;?>)"><?php echo "<input type='text' value='$reward->rewards' name='rewards[]' id='rewards$i'/>";?></td>
	      <td class="edit" onkeyup="setRewards(<?php echo $i;?>)"><?php echo "<input type='text' value='$reward->delay' name='delay[]' id='delay$i'/>";?></td>
	      <td class="edit" onkeyup="setRewards(<?php echo $i;?>)"><?php echo "<input type='text' value='$reward->bonus' name='bonus[]' id='bonus$i'/>";?></td>
	      <td onkeyup="setRewards(<?php echo $i;?>)"><?php echo "<input type='text' value='$reward->total' name='total[]' id='rTotal$i'/>";?></td>
        </tr>
        <?php endforeach;?>
        <tr><td></td><td class='a-center'><?php echo html::submitButton('确定')?></td><td colspan="8"></tr>
        </tbody>
        </table></form>
        <br/>
        <form method="post">
	        <div class="a-title"><?php echo $lang->system->hoursIncrease;?></div>
	      	<table style="width: 80%;" class="a-hours" id="a-hours">
	        <thead>
	        <tr class='colhead'>
	          <th><?php echo '';?></th>
	          <th><?php echo $lang->system->name;?></th>
	          <th><?php echo $lang->system->masterIncrease;?></th>
	          <th><?php echo $lang->system->creativeHours;?></th>
	          <th><?php echo $lang->system->patentHours;?></th>
	          <th><?php echo $lang->system->reportHours;?></th>
	          <th><?php echo $lang->system->codeQuality;?></th>
	          <th><?php echo $lang->system->total;?></th>
	        </tr>
	        </thead>
	        <tbody>
	        <?php $i=0;?>
	        <?php foreach ($increaseS as $increase):?>
	        <?php $i++;?>
	        <tr>
	        <td><?php echo "<input type='hidden' value='$increase->account' name='increaseName[]'/>"?></td>
	        <td><?php echo "<input type='hidden' value='$month' name='month'/>"?></td>
	        </tr>
	        <tr class='a-center' style="height: 35px;">
	          <td><?php echo $i;?></td>
	          <td class="edit" width="8%"><?php echo html::input('',$increase->realname,"readonly style='border:0px;'");?></td>
	          <td class="edit" onkeyup="changeValue(<?php echo $i;?>)"><?php echo "<input type='text' value='$increase->master' name='master[]' id='master$i'/>";?></td>
	          <td class="edit" onkeyup="changeValue(<?php echo $i;?>)"><?php echo "<input type='text' value='$increase->creative' name='creative[]' id='creative$i'/>";?></td>
	          <td class="edit" onkeyup="changeValue(<?php echo $i;?>)"><?php echo "<input type='text' value='$increase->patent' name='patent[]' id='patent$i'/>";?></td>
	          <td class="edit" onkeyup="changeValue(<?php echo $i;?>)"><?php echo "<input type='text' value='$increase->report' name='report[]' id='report$i'/>";?></td>
	          <td class="edit" onkeyup="changeValue(<?php echo $i;?>)"><?php echo "<input type='text' value='$increase->codeQuality' name='codeQuality[]' id='codeQuality$i'/>";?></td>
	          <td><?php echo "<input type='text' value='$increase->total' name='total[]' id='total$i' onkeydown=changeValue($i)/>";?></td>
	        </tr>
	        <?php endforeach;?>
	        <tr><td></td><td class='a-center'><?php echo html::submitButton('确定')?></td><td colspan="6"></tr>
	        </tbody>
	        </table>
        </form>
      </td>