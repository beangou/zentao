<?php
/**
 * The complete file of task module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2013 青岛易软天创网络科技有限公司 (QingDao Nature Easy Soft Network Technology Co,LTD www.cnezsoft.com)
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      shi.haiyang
 * @package     plan
 * @version     $Id: finish.html.php 935 2013-10-21 15:20:00Z $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<form method='post' target='hiddenwin'>
  <table class='table-1'>
    <caption><?php echo $lang->plan->planCheck;?></caption>
    <tr>
      <td class='rowhead'><?php echo $lang->plan->status;?></td>
      <td><?php echo html::select('status', $lang->plan->handleStatus, $plan->status, "class='select-3'");?></td>
    </tr>
    <tr>
    <?php if (!empty($lead)):?>
    <tr>
      <td class='rowhead'><?php echo $lang->plan->complete;?></td>
      <td><?php echo html::select('complete', $lang->plan->completed, $plan->complete, "class='select-3'");?></td>
    </tr>
    <?php endif;?>
    <tr>
      <td class='rowhead'><?php echo $lang->comment;?></td>
      <td><?php echo html::textarea('remark', $plan->remark, "rows='6' class='area-1'");?></td>
    </tr>
    <tr>
      <td colspan='2' class='a-center'><?php echo html::submitButton();?></td>
    </tr>
  </table>
</form>
<?php include '../../common/view/footer.html.php';?>
