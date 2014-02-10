      <td>
      <table id="accumulativeT">
        <thead>
          <tr >
            <td colspan="15"><?php echo '<h1>项目经理月度薪酬明细表</h1>';?></td>
          </tr>
          <tr>
            	<td colspan="5">
            	  <?php echo html::input('finishedDate',isset($finishDate)?$finishDate:date('Y-m-t',time()),"class='w-date date'")?>
            	  <span><?php echo html::submitButton($lang->salary->queryButton)?></span>
            	</td>
            	<td colspan="5">当前月度薪酬：<?php echo isset($mSingleCount[0]->finalSalary)?$mSingleCount[0]->finalSalary:0 . ' 元';?></td>
            	<td colspan="5">当前月度排名：<?php echo isset($mSingleCount[0]->allRank)?$mSingleCount[0]->allRank:'无当月数据';?></td>
          </tr>
      	</thead>
        <thead>
        <tr class='colhead'>
          <th><?php echo $lang->salary->number;?></th>
          <th><?php echo $lang->salary->realname;?></th>
          <th><?php echo $lang->salary->standsalary;?></th>
          <th><?php echo $lang->salary->measureSalaryBase;?></th>
          <th><?php echo $lang->salary->manageCoeff;?></th>
          <th><?php echo $lang->salary->dayHours;?></th>
          <th><?php echo $lang->salary->otherCoeff;?></th>
          <th><?php echo $lang->salary->measure;?></th>
          <th><?php echo $lang->salary->bug;?></th>
          <th><?php echo $lang->salary->bonus;?></th>
          <th><?php echo $lang->salary->daySalary;?></th>
          <th><?php echo $lang->salary->date;?></th>
          <th><?php echo $lang->salary->deptRank;?></th>
          <th><?php echo $lang->salary->officeRank;?></th>
          <th><?php echo $lang->salary->allRank;?></th>
        </tr>
        </thead>
        <?php $i = 0;?>
        <?php foreach ($accumulative as $accu):?>
        <?php $i++;?>
        <tr class='a-center' style="height: 35px;">
          <td><?php echo $i;?></td>
          <td><?php echo $mSingleCount[0]->realname;?></td>
          <td><?php echo $mSingleCount[0]->standsalary;?></td>
          <td><?php echo $mSingleCount[0]->standsalary*0.6;?></td>
          <td><?php echo $mSingleCount[0]->manageCoeff;?></td>
          <td><?php echo $accu->estimate;?></td>
          <td><?php echo $mSingleCount[0]->otherCoeff;?></td>
          <td><?php echo round($mSingleCount[0]->measure/22,2);?></td>
          <td><?php echo $mSingleCount[0]->bug;?></td>
          <td><?php echo $mSingleCount[0]->bonus;?></td>
          <td><?php echo round($mSingleCount[0]->standsalary*0.6/22/8*$accu->estimate,2);?></td>
          <td><?php echo $accu->finishedDate;?></td>
          <td><?php echo $mSingleCount[0]->deptRank;?></td>
          <td><?php echo $mSingleCount[0]->officeRank;?></td>
          <td><?php echo $mSingleCount[0]->allRank;?></td>
        </tr>
        </tbody>
        <?php endforeach;?>
      </table>
      </td>