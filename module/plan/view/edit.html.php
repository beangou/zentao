<?php
/**
 * The create view of plan module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2013 青岛易软天创网络科技有限公司 (QingDao Nature Easy Soft Network Technology Co,LTD www.cnezsoft.com)
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      shihaiyang
 * @package     plan
 * @version     $Id: create.html.php 4728 2013-10-17 17:14:34Z  $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
<form method='post' id='dataform'>
  <table class='table-1'> 
    <caption><?php echo $lang->plan->edit;?></caption>
    <tr>
      <th class='rowhead'><?php echo $lang->plan->type;?></th>
      <td><?php echo html::select('type',$lang->plan->types, $plan->type,'class=select-3 disabled');?>
      </td>
    </tr>  
    <tr>
      <th class='rowhead'><?php echo $lang->plan->sort;?></th>
      <td><?php echo html::input('sort', $plan->sort, 'class=select-3 readonly');?> 
    </tr>  
    <tr>
      <th class='rowhead'><?php echo $lang->plan->matter;?></th>
      <td><?php echo html::input('matter', $plan->matter, "class='text-1' readonly");?></td>
    </tr>  
    <tr>
      <th class='rowhead'><?php echo $lang->plan->plan;?></th>
      <td><?php echo html::textarea('plan', $plan->plan, "rows='8' class='area-1' readonly");?></td>
    </tr>  
    <tr>
      <th class='rowhead'><?php echo $lang->plan->limit;?></th>
      <td><?php echo html::input('limit', $plan->limit, "class='select-3 date' readonly");?></td>
    </tr>  
    <tr>
      <th class='rowhead'><?php echo $lang->plan->appraise;?></th>
      <td><?php echo html::select('appraise', $lang->plan->completed, $plan->appraise, 'class=select-3');?></td>
    </tr>
    <?php if (!empty($from) && $from == 'handle'):?>
    <tr>
      <th class='rowhead'><?php echo $lang->plan->status;?></th>
      <td><?php echo html::select('status', $lang->plan->handleStatus, $plan->status, 'class=select-3 disabled');?></td>
    </tr>
    <tr>
      <th class='rowhead'><?php echo $lang->plan->complete;?></th>
      <td><?php echo html::select('complete', $lang->plan->completed, $plan->complete, 'class=select-3');?></td>
    </tr>
    <?php endif;?>
    <tr>
      <th class='rowhead'><?php echo $lang->plan->auditor;?></th>
      <td><?php echo html::select('auditor', $users, $plan->auditor, "class='select-3' disabled");?></td>
    </tr>
    <tr>
    <tr>
      <th class='rowhead'><?php echo $lang->plan->beginEnd;?></th>
      <td><?php echo html::input('startDate', $plan->startDate, "class='select-3 date' readonly");?>&nbsp;
      <?php echo html::input('finishedDate', $plan->finishedDate, "class='select-3 date' readonly");?></td>
    </tr>
    <tr>
      <th class='rowhead'><?php echo $lang->plan->remark;?></th>
      <td><?php echo html::input('remark', $plan->remark, "class='text-1'");?></td>
    </tr>  
    <tr>
      <td colspan='2' class='a-center'>
        <?php echo html::submitButton() . html::backButton();?>
      </td>
    </tr>
  </table>
</form>
<?php include '../../common/view/footer.html.php';?>