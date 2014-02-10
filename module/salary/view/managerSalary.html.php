<td>
            	<div style="padding: 8px;">
            	<?php $openedDate = isset($startDate)?$startDate:date('Y-m-01',time());
            		  $finishedDate = isset($finishDate)?$finishDate:date('Y-m-t',time());?>
            	  <?php echo html::input('startDate',$openedDate,"class='w-date date'");?>至
            	  <?php echo html::input('finishedDate',$finishedDate,"class='w-date date'");?>
            	  <?php echo html::submitButton($lang->salary->queryButton);?>
            	</div>
	<table>
        <thead>
          <tr>
            <th colspan="14" style="font-size: 16px;"><?php echo $lang->salary->manageDetail;?></th>
            <td><?php common::printLink('salary', 'monthly','typeID=5',$lang->salary->countHelp);?>&nbsp;&nbsp;
            <?php common::printIcon('salary', 'export',"startDate=$openedDate&finishedDate=$finishedDate");?></td>
          </tr>
          </thead>
        <thead>
        <tr class='colhead'>
          <th><?php echo $lang->salary->number;?></th>
          <th><?php echo $lang->salary->realname;?></th>
          <th><?php echo $lang->salary->standsalary;?></th>
          <th><?php echo $lang->salary->measureSalaryBase;?></th>
          <th><?php echo $lang->salary->manageCoeff;?></th>
          <th><?php echo $lang->salary->count;?></th>
          <th><?php echo $lang->salary->otherCoeff;?></th>
          <th><?php echo $lang->salary->measure;?></th>
          <th><?php echo $lang->salary->bug;?></th>
          <th><?php echo $lang->salary->bonus;?></th>
          <th><?php echo $lang->salary->finalSalary;?></th>
          <th><?php echo $lang->salary->year;?></th>
          <th><?php echo $lang->salary->deptRank;?></th>
          <th><?php echo $lang->salary->officeRank;?></th>
          <th><?php echo $lang->salary->allRank;?></th>
        </tr>
        </thead>
        <?php $i=0;?>
        <?php foreach ($managerCount as $hours):?>
        <?php $i++;?>
        <tbody>
        <tr class='a-center' style="height: 35px;">
          <td><?php echo $i;?></td>
          <td><?php echo common::printLink('salary', 'monthly', 'typeID=2&finishedDate='.$hours->year,$hours->realname);?></td>
          <td><?php echo $hours->standsalary;?></td>
          <td><?php echo $hours->standsalary*0.6;?></td>
          <td><?php echo $hours->manageCoeff;?></td>
          <td><?php echo $hours->count;?></td>
          <td><?php echo $hours->otherCoeff;?></td>
          <td><?php echo $hours->measure;?></td>
          <td><?php echo $hours->bug;?></td>
          <td><?php echo $hours->bonus;?></td>
          <td><?php echo $hours->finalSalary;?></td>
          <td><?php echo $hours->date;?></td>
          <td><?php echo $hours->deptRank;?></td>
          <td><?php echo $hours->officeRank;?></td>
          <td><?php echo $hours->allRank;?></td>
        </tr>
        </tbody>
        <?php endforeach;?>
     </table>
      </td>