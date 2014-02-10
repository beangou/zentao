<?php
/**
 * 参数设定
 */
?>
<?php include '../../common/view/header.html.php';
 	  include '../../common/view/tablesorter.html.php';
 	  include '../../common/view/colorize.html.php';
 	  include '../../common/view/datepicker.html.php';
?>
 <table class='cont-lt1'>
  <tr valign='top'>
    <td class='side'>
      <div class='box-title'><?php echo $lang->system->parameter;?></div>
      <div class='box-content'>
      	<?php common::printLink('system', 'parameter','typeID=1', $lang->system->projectInfo); ?>
      </div>
      <div class='box-content'>
      	<?php common::printLink('system', 'parameter','typeID=2', $lang->system->personnelFormula); ?>
      </div>
      <div class='box-content' style="height: 350px;"><?php echo ''?></div>
    </td>
      <?php
      if ($typeID==0 || $typeID==1)
      include 'parameterProject.html.php';
      else include 'parameterFormula.html.php';
      ?>
  </tr>
 
</table>

<?php include '../../common/view/footer.html.php';?>