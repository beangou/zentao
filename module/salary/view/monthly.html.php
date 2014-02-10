<?php
/**
 * 薪酬报表主调页面
 */
?>
<?php include '../../common/view/header.html.php';
 	  include '../../common/view/tablesorter.html.php';
 	  include '../../common/view/colorize.html.php';
 	  include '../../common/view/datepicker.html.php';
 	  include './highchart.html.php';
?>
<?php 
	js::set('role', $standSalary->role);
	if ($standSalary->role==1 || $standSalary->role==4){
	  	if ($type==1){
			js::set('personNum', $personNum);
// 			js::set('salaryIncrease', json_encode($salaryIncrease));
	  	}else if ($type==2){
			js::set('salaryPayAnalysis', json_encode($salaryPayAnalysis));
	  	}
	}
			js::set('type', $type);
			js::set('month', $monthQuery);
 ?>
<?php 
  if ($standSalary->role==1 || $standSalary->role==4)
  	include 'leaderCommon.html.php';
  else
  	include 'commontable.html.php';
//   if($standSalary->role !=1 && $standSalary->role !=4)
//   else if($standSalary->role !=1 && $type==5)include 'personnelHelp.html.php';
?> 

<?php include '../../common/view/footer.html.php';?>