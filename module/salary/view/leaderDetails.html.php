      <td>
      <form method="post">
      <?php $finishedDate = isset($finishDate)?$finishDate:date('Y-m-t',time());?>
      <table id="accumulativeT" style="width: 90%;border: 0px">
        <thead>
          <tr>
		      <td><div class="a-leftS"><?php echo $monthQuery.$lang->salary->monthDetail;?></div></td>
	      </tr>
          <tr>
            	<td>
            	  <?php echo html::input('finishedDate',$finishedDate,"class='w-date date'")?>
            	  <?php echo html::submitButton($lang->salary->queryButton);?>
            	</td>
            	<td>
            	</td>
          </tr>
      	</thead>
      	</table>
      	</form>
      	<?php $date = isset($finishDate)?$finishDate:date('Y-m-t',time());?>
      	<table id="accumulativeT" style="width: 90%;">
        <thead>
        <tr><th colspan="11" class="a-left"><?php echo $lang->salary->staffDetail .' ('.$lang->salary->deptProduct;?>
        <?php echo isset($allPerson[0])?$allPerson[0]->deptCoeff:'' ;?>)</th>
        	<td align="right" style="padding-right: 12px;">
        		<?php common::printIcon('salary', 'export',"startDate=$finishedDate&finishedDate=$finishedDate&param=staff");?>
		    </td>
        </tr>
      	<?php if(!isset($orderBy))    $orderBy = '';
      	$vars = "typeID=3&finishedDate=$date&orderBy=%s"; ?>
        <tr class='colhead'>
          <th><?php echo $lang->salary->number;?></th>
          <th><?php echo $lang->salary->realname;?></th>
          <th><?php echo $lang->salary->standsalary;?></th>
          <th><?php echo $lang->salary->measureSalaryBase;?></th>
          <th><?php echo $lang->salary->personnelCoeff;?></th>
          <th><?php common::printOrderLink('count', $orderBy, $vars, $lang->salary->count) ;?></th>
          <th><?php echo $lang->salary->measure;?></th>
          <th><?php echo $lang->salary->bug;?></th>
          <th><?php echo $lang->salary->bonus;?></th>
          <th><?php common::printOrderLink('finalSalary', $orderBy, $vars, $lang->salary->finalSalary);?></th>
          <th><?php echo $lang->salary->deptName;?></th>
          <th><?php common::printOrderLink('allRank', $orderBy, $vars, $lang->salary->allRank);?></th>
        </tr>
        </thead>
        <?php $i = 0;?>
        <?php foreach ($allPerson as $all):?>
        <?php $i++;?>
        <tr class='a-center' style="height: 35px;">
          <td><?php echo $i;?></td>
          <td><?php echo $all->realname;?></td>
          <td><?php echo $all->standsalary;?></td>
          <td><?php echo $all->standsalary*0.6;?></td>
          <td><?php echo $all->personnelCoeff;?></td>
          <td><?php echo $all->count;?></td>
          <td><?php echo $all->measure;?></td>
          <td><?php echo $all->bug;?></td>
          <td><?php echo $all->bonus;?></td>
          <td><?php echo $all->finalSalary;?></td>
          <td><?php echo $all->deptName;?></td>
          <td><?php echo $all->allRank;?></td>
        </tr>
        </tbody>
        <?php endforeach;?>
        </table>
        <table id="accumulativeT" style="width: 90%">
        <thead>
        <tr><th colspan="11" class="a-left">项目经理薪酬明细</th>
        	<td align="right" style="padding-right: 12px;">
        		<?php common::printIcon('salary', 'export',"startDate=$finishedDate&finishedDate=$finishedDate&param=manage");?>
		    </td>
        </tr>
        <tr class='colhead'>
          <th><?php echo $lang->salary->number;?></th>
          <th><?php echo $lang->salary->realname;?></th>
          <th><?php echo $lang->salary->standsalary;?></th>
          <th><?php echo $lang->salary->measureSalaryBase;?></th>
          <th><?php echo $lang->salary->manageCoeff;?></th>
          <th><?php common::printOrderLink('count', $orderBy, $vars, $lang->salary->count);?></th>
          <th><?php echo $lang->salary->otherCoeff;?></th>
          <th><?php echo $lang->salary->measure;?></th>
          <th><?php echo $lang->salary->bug;?></th>
          <th><?php echo $lang->salary->bonus;?></th>
          <th><?php common::printOrderLink('finalSalary', $orderBy, $vars, $lang->salary->finalSalary);?></th>
          <th><?php echo $lang->salary->deptName;?></th>
        </tr>
        </thead>
        <?php $i = 0;?>
        <?php foreach ($allManager as $all):?>
        <?php $i++;?>
        <tr class='a-center' style="height: 35px;">
          <td><?php echo $i;?></td>
          <td><?php echo $all->realname;?></td>
          <td><?php echo $all->standsalary;?></td>
          <td><?php echo $all->standsalary*0.6;?></td>
          <td><?php echo $all->manageCoeff;?></td>
          <td><?php echo $all->count;?></td>
          <td><?php echo $all->otherCoeff;?></td>
          <td><?php echo $all->measure;?></td>
          <td><?php echo $all->bug;?></td>
          <td><?php echo $all->bonus;?></td>
          <td><?php echo $all->finalSalary;?></td>
          <td><?php echo $all->deptName;?></td>
        </tr>
        </tbody>
        <?php endforeach;?>
      </table>
      </td>