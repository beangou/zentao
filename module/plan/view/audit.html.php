<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/colorize.html.php';?>
<?php js::set('users', json_encode($users))?>

  <div id='topmyplan'>
    <div class='f-left'>
      <?php 
//       foreach($lang->plan->periods as $period => $label)
      foreach($mymenu as $period => $label)
      {
//           if ($period == 'collectplan') {continue;}
      	  $vars = $period;
//           if($period == 'before') $vars .= "&account={$app->user->account}&status=undone";
          echo "<span id='$period'>" . html::a(inlink($vars), $label) . '</span>';
      }
      ?>
    </div>
  </div>
  
  <form  method='post'>
  
  选择成员：<?php echo html::select('member', $mymembers, '', 'onchange="loadPlan()"');?>
  <br/><br/>
	  <table class='table-1 tablesorter colored datatable newBoxs'> 
	    <caption><div align="center">本周周计划</div></caption>
	    <thead>
	    	<tr class='colhead'>
		      <th width="5%"><?php echo $lang->plan->sort;?></th>
		      <th width="15%"><?php echo $lang->plan->matter;?></th>
		      <th width="20%"><?php echo $lang->plan->plan;?></th>
		      <th width="6%">完成时限</th>
		      <th width="6%">完成情况</th>
		      <th width="10%">见证性材料</th>
		      <th width="14%">未完成原因说明及如何补救</th>
		      <th width="5%">确认人</th>
		      <th width="6%">确认结果</th>
		      <th>备注</th>
		    </tr>  
	    </thead>
	    <tbody id="thisPlanBody">
	    </tbody>
	  </table>
	  
	  <table class='table-1 tablesorter colored datatable newBoxs' style="margin-top:5%"> 
	    <caption><div align="center">下周周计划</div></caption>
	    <thead>
	    	<tr class='colhead'>
		      <th width="10%"><?php echo $lang->plan->sort;?></th>
		      <th width="30%"><?php echo $lang->plan->matter;?></th>
		      <th width="40%"><?php echo $lang->plan->plan;?></th>
		      <th width="10%">完成时限</th>
		      <th width="10%">确认人</th>
		   </tr>   
	    </thead>
	    <tbody id="nextPlanBody">
	    </tbody>
	  </table>
	 
	 <div align="center" style="margin-top:4%"> 
	  评审意见：
	  <div style="margin-top:2%">
	  	<input id="resultYes" type="radio" name="result" value="同意" checked>同意<input id="resultNo" type="radio" name="result" value="不同意">不同意
	  	<br/><br/>
	  	<textarea id="auditComment" name="auditComment" cols="80" rows="5"></textarea>
	  	<br/><br/>
	  	<input type="submit" value="提交"> 
	  </div>	
	 </div> 
	  
	 <?php
// 	  echo	html::submitButton($lang->plan->submit, "onclick='changeSubmit(\"" . $this->createLink('plan', 'myplan', "isSubmit=1") . "\")'");
	  ?> 
	</form>  
  
<?php include '../../common/view/footer.html.php';?>