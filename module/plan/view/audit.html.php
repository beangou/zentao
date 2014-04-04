<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/colorize.html.php';?>
<?php js::set('users', json_encode($users))?>

	<style type="text/css">
        body{background:#ececec;}
        #uptop{display:none;position:fixed;font-size:12px;text-align:center;width:20px;height:55px;
               bottom:0;right:0;background: #9eceb7;border: 1px solid #333;margin-bottom:50px}
        #uptop:hover{cursor: pointer;background: #3a6d8c;color: #FFFFFF}
    </style>

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
  
   
   
   <table class='table-1 tablesorter colored datatable newBoxs' style="margin-top:3%"> 
	    <caption><div align="center">待审核列表</div></caption>
	    <tbody>
	    	<?php 
	    	    $i = 0;
	    	    echo '<tr class="colhead"><th style="width:12%">编号</th><th>周计划列表</th></tr>';
	    		foreach ($unAuditPlansAlink as $unAuditPlan) {
// 					echo '<tr><td><a href="/zentao/www/index.php?m=plan&f=audit&account='. $unAuditPlan->account。 
// 					 'firstDayOfWeek='. $unAuditPlan->firstDayOfWeek. '">'. 
// 					$unAuditPlan->title. '</a></td></tr>'; $unAuditPlan->account。 'firstDayOfWeek='. $unAuditPlan->firstDayOfWeek. '">'. $unAuditPlan->title.
	    			$i++;
					echo '<tr><td style="text-align:center">'. $i. '</td><td style="text-align:left"><a href="/zentao/www/index.php?m=plan&f=audit&account='. $unAuditPlan->account. 
						'&firstDayOfWeek='. $unAuditPlan->firstDayOfWeek. '&lastDayOfWeek='. $unAuditPlan->lastDayOfWeek. '&realname='. $unAuditPlan->realname. '#form1">   &nbsp;&nbsp;' . $unAuditPlan->team. ' &nbsp;&nbsp;  '.
					$unAuditPlan->realname. '周计划     ('. $unAuditPlan->firstDayOfWeek. '~'. $unAuditPlan->lastDayOfWeek. 
					')</a></td></tr>';
				}
	    	?>
	    </tbody>
  </table>
   
 
<?php 
//  	echo '<select name="member" id="member" onchange="loadPlan()"><option selected></option>';
// 	foreach ($mymembers as $member) {
// 		echo '<option value="'. $member->account. '">'. $member->realname. '</option>';  	
// 	}
// 	echo '</select>';
 ?>  
  
  <form id="form1" name="form1" method='post'>
  
	  <table class='table-1 tablesorter colored datatable newBoxs' style="margin-top:3%"> 
	    <caption><div align="center">审核周计划     <?php echo $this->view->realname. $this->view->firstDayOfWeek. 
			$this->view->lastDayOfWeek. ' ';?></div></caption>
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
	    	<?php 
	    	    if (!empty($unAuditPlans)) {
	    	    	foreach ($unAuditPlans as $unAuditPlan) {
						echo '<tr><td style="text-align:center"><input type="hidden" name="weekPlanId[]" value="'. $unAuditPlan->id.
						'"><input type="hidden" name="weekAuditId[]" value="'. $unAuditPlan->auditId.
						'">'. $unAuditPlan->type. '</td><td>'.
						$unAuditPlan->matter. '</td><td>'.
						$unAuditPlan->plan.  '</td><td>'.
						$unAuditPlan->deadtime. '</td><td>'.
						$unAuditPlan->submitToName. '</td></tr>';
					}
					echo '<input type="hidden" id="resultId" value="'. $unAuditPlans[0]->result.'">';
					echo '<input type="hidden" id="commentId" value="'. $unAuditPlans[0]->auditComment.'">';
					
					echo '<input type="hidden" name="account" value="'. $unAuditPlans[0]->account.'">';
					echo '<input type="hidden" name="firstDayOfWeek" value="'. $unAuditPlans[0]->firstDayOfWeek.'">';
						
	    	    }
	    	?>
	    </tbody>
	  </table>
	 
	 <div align="center" style="margin-top:2%"> 
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
  <div id="uptop" onclick="scroTop()">回到顶部</div>
   <script type="text/javascript">
		 window.onscroll = function(){
	        var t = document.documentElement.scrollTop || document.body.scrollTop;
	        var div_up = document.getElementById("uptop");
	        t > 50 ? div_up.style.display="block" : div_up.style.display="none";
	    }

	    function scroTop(){
	        document.body.scrollTop = document.documentElement.scrollTop = 0;
	    }
	 </script> 
<?php include '../../common/view/footer.html.php';?>