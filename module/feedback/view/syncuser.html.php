<?php
/**
 * The feedback view file of feedback module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2011 青岛易软天创网络科技有限公司 (QingDao Nature Easy Soft Network Technology Co,LTD www.cnezsoft.com)
 * @license     商业软件，未经授权，请立刻删除!
 * @author      Yidong Wang <yidong@cnezsoft.com>
 * @package     feedback
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<table class='table-1'>
  <tr class='strong'>
    <td width='50%'><?php echo $lang->feedback->account?></td>
    <td><?php echo $lang->feedback->role?></td>
  </tr>
</table>
<form method='post' target='hiddenwin'>
<?php $i = 0?>
<?php if($noSyncUsers):?>
<table class='table-1'>
  <caption><?php echo $lang->feedback->noSync?></caption>
  <tbody id='noSync'>
  <?php foreach($noSyncUsers as $user):?>
  <tr>
    <td width='50%'> <input type='checkbox' name='noSyncUser[<?php echo $i?>]' value='<?php echo $user->id?>' /> <?php echo $user->account?></td>
    <td> <?php echo html::select("role[]", $lang->zentaoasm->roleList)?> </td>
  </tr>
  <?php $i++;?>
  <?php endforeach;?>
  </tbody>
  <tr>
    <td colspan='2'> <input type='checkbox' id='allNoSync' /><?php echo $lang->feedback->selectAll?> </td>
  </tr>
</table>
<?php endif;?>
<?php if($syncedUsers):?>
<table class='table-1'>
  <caption><?php echo $lang->feedback->synced?></caption>
  <tbody id='synced'>
  <?php foreach($syncedUsers as $user):?>
  <tr>
    <td width='50%'> <input type='checkbox' name='syncedUser[<?php echo $i?>]' value='<?php echo $user->id?>' /> <?php echo $user->account?></td>
    <td> <?php echo html::select("role[]", $lang->zentaoasm->roleList, $user->role)?> </td>
  </tr>
  <?php $i++;?>
  <?php endforeach;?>
  </tbody>
  <tr>
    <td colspan='2'>
      <input type='checkbox' id='allSynced' /><?php echo $lang->feedback->selectAll?>
      <input type='checkbox' id='overrideSync' name='overrideSync' /><?php echo $lang->feedback->overrideSync?>
    </td>
  </tr>
</table>
<?php endif?>
<p align='center'> <?php echo html::submitButton($lang->sync)?> <?php echo html::resetButton()?> </p>
</form>
<?php include '../../common/view/footer.html.php';?>
