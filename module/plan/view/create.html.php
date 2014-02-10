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
<form method='post' target='hiddenwin' id='dataform'>
  <table class='table-1'> 
    <caption><?php echo $lang->plan->create;?></caption>
    <tr>
      <th class='rowhead'><?php echo $lang->plan->type;?></th>
      <td><?php echo html::select('type',$lang->plan->types,'','class=select-3');?>
      </td>
    </tr>  
    <tr>
      <th class='rowhead'><?php echo $lang->plan->sort;?></th>
      <td><?php echo html::input('sort', '', 'class=select-3');?> 
    </tr>  
    <tr>
      <th class='rowhead'><?php echo $lang->plan->matter;?></th>
      <td><?php echo html::input('matter', '', "class='text-1'");?></td>
    </tr>  
    <tr>
      <th class='rowhead'><?php echo $lang->plan->plan;?></th>
      <td><?php echo html::textarea('plan', '', "rows='8' class='area-1'");?></td>
    </tr>  
    <tr>
      <th class='rowhead'><?php echo $lang->plan->limit;?></th>
      <td><?php echo html::input('limit', $date, "class='select-3 date'");?></td>
    </tr>  
    <tr>
      <th class='rowhead'><?php echo $lang->plan->auditor;?></th>
      <td><?php echo html::select('auditor', $users, '', "class='select-3'");?></td>
    </tr>
    <tr>
    <tr>
      <th class='rowhead'><?php echo $lang->plan->beginEnd;?></th>
      <td><?php echo html::input('startDate', $date, "class='select-3 date'");?>&nbsp;
      <?php echo html::input('finishedDate', $date, "class='select-3 date'");?></td>
    </tr>
    <tr>
      <th class='rowhead'><?php echo $lang->plan->remark;?></th>
      <td><?php echo html::input('remark', '', "class='text-1'");?></td>
    </tr>  
    <tr>
      <td colspan='2' class='a-center'>
        <?php echo html::submitButton() . html::backButton();?>
      </td>
    </tr>
  </table>
</form>
<?php include '../../common/view/footer.html.php';?>