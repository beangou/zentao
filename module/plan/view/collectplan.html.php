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
  
  <div align="center">
	<form  method='post' onsubmit='return checkEmail()'>
		<br><br>
		请输入邮箱地址：<input id="email" type="text" name="email" style="width:18%;height:3%"/>
		<input type="submit" value="提交"><?php echo $info;?>
	</form>
  </div>
  
  <table class='table-1 tablesorter colored datatable newBoxs' style="margin-top: 2%"> 
    <caption><div align="center">汇总周计划</div></caption>
    <thead>
    	<tr class='colhead'>
		  <th width="5%">负责人</th>
    	  <th width="8%">时间</th>
	      <th width="5%"><?php echo $lang->plan->sort;?></th>
	      <th width="15%"><?php echo $lang->plan->matter;?></th>
	      <th width="20%"><?php echo $lang->plan->plan;?></th>
	      <th width="6%">完成时限</th>
	      <th width="6%">完成情况</th>
	      <th width="10%">见证性材料</th>
	      <th width="14%">未完成原因说明及如何补救</th>
	    </tr>  
    </thead>
    <?php 
    if (!empty($passedPlan)):
    foreach ($passedPlan as $plan):
    ?>
    <tr class='a-center'>
      <td><?php echo $plan->accountname;?></td>
      <td><?php echo $plan->firstDayOfWeek. ' ~ '. $plan->lastDayOfWeek;?></td>
      <td><?php echo $plan->type;?></td>
      <td><?php echo $plan->matter;?></td>
      <td><?php echo $plan->plan;?></td>
      <td><?php echo $plan->deadtime;?>
      <td><?php echo $plan->status;?></td>
      <td><?php echo $plan->evidence;?></td>
      <td><?php echo $plan->courseAndSolution;?></td>
    </tr>
    <?php endforeach;?>
     <?php else :
     ?>
    <tr class='a-center'>
      <td class='stepID' colspan="10">无数据</td>
    </tr>
    <?php endif;?>
  </table>
  
<script type="text/javascript">
function checkEmail()
{
	if($('#email').val() == '') {
		alert('邮箱地址不能为空！');
		return false;
	}

	var temp = $('#email').val();
	//对电子邮件的验证
	var myreg = /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
// 	var myreg = /^[^\.@]+@[^\.@]+\.[a-z]+$/;


// 	var myreg = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/; 
// 	return reg.test(str); 

// 	var emailPat=/^(.+)@(.+)$/;
// 	var matchArray=emailPat.match(temp);
// 	if (matchArray==null) {
// 		alert("电子邮件地址必须包括 ( @ 和 . )")
// 		return false;
// 	}
	
	if(!myreg.test(temp))
	{
		alert('提示\n\n请输入有效的E_mail！');
		//myreg.focus();
		return false;
	}
	
// 	alert('邮箱地址不能为空！');
	return true;
}
</script>
<?php include '../../common/view/footer.html.php';?>