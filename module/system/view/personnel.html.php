<?php
/**
 * 人员管理
 */
?>
<?php include '../../common/view/header.html.php';
 	  include '../../common/view/tablesorter.html.php';
 	  include '../../common/view/colorize.html.php';
?>
 <table class='cont-lt1'>
  <tr valign='top'>
    <td class='side'>
      <div class='box-title'><?php echo $lang->system->personnel;?></div>
      <div class='box-content'>
      	<?php common::printLink('system', 'personnel','', $lang->system->personnelInfo); ?>
      </div>
      <div class='box-content' style="height: 350px;"><?php echo ''?></div>
    </td>
      <?php
      if ($typeID==0 || $typeID==2)
      include 'personnelInfo.html.php';
      else if($typeID==1) include 'personnelEdit.html.php';
      ?>
  </tr>
 
</table>
<?php include '../../common/view/footer.html.php';?>