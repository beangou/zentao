<?php 
/*project group settings*/
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/form.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<?php include '../../common/view/colorize.html.php';?>
<body>
  <div id='topmyplan'>
    <div class='f-left'>
      <?php 
	      foreach($mymenu as $period => $label)
	      {
	          $vars = $period;
	          echo "<span id='$period'>" . html::a(inlink($vars), $label) . '</span>';
	      }
      ?>
    </div>
  </div>
  <form id="form1" name="form1" method='post'>
  		<table align="center" border="1" cellspacing="0" style="width:50%;margin-top:3%;">
  			<thead>
  				<tr><th colspan="4"><div class="week-title">请假登记</div></th></tr>
  			</thead>
  			<tbody>
  				<tr>
  					<td>请假人：</td>
  					<td><?php echo html::select('leaverName', $users, '', "class='select-1' style='width:127px' onchange='findDepatment()'");?></td>
  					<td>所属部门：</td>
  					<td id="department"></td>
  				</tr>
  				<tr><td>请假事由：</td><td colspan="3"><?php echo html::input('reason','',"class='text-2' style='width:84%'")?></td></tr>
  				<tr><td>请假时间：</td><td><?php echo html::input('startTime', '', "class='select-2 date' ")?></td><td>起至</td><td><?php echo html::input('endTime', '', "class='select-2 date' ")?></td></tr>
  				<tr><td colspan="2"></td><td>申请请假日期：</td><td width="35%"><?php echo html::input('askTime', date('Y-m-d', time()), "class='select-2 date' ")?></td></tr>
  				<tr><td colspan="4" align="center"><input type="reset" value="重置" style="margin-right: 4%"><input type="submit" value="提交"></td></tr>
  			</tbody>
  		</table>
  </form>
</body>
<?php include '../../common/view/footer.html.php';
	if ($saveResult == 'success') {
		echo '<script type="text/javascript">alert("保存成功！")</script>';
	}
?>		   	