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
    <caption><?php echo $lang->plan->copy;?></caption>
    <tr>
      <th class='rowhead'><?php echo $lang->plan->type;?></th>
      <td><?php echo $lang->plan->types[$plan->type];?>
      </td>
    </tr>  
    <tr>
      <th class='rowhead'><?php echo $lang->plan->sort;?></th>
      <td><?php echo $plan->sort;?> 
    </tr>  
    <tr>
      <th class='rowhead'><?php echo $lang->plan->matter;?></th>
      <td><?php echo $plan->matter;?></td>
    </tr>  
    <tr>
      <th class='rowhead'><?php echo $lang->plan->plan;?></th>
      <td><?php echo html::textarea('plan', $plan->plan, "rows='8' class='area-1' readonly");?></td>
    </tr>  
    <tr>
      <th class='rowhead'><?php echo $lang->plan->limit;?></th>
      <td><?php echo $plan->limit;?></td>
    </tr>  
    <tr>
      <th class='rowhead'><?php echo $lang->plan->appraise;?></th>
      <td><?php echo $lang->plan->completed[$plan->appraise];?></td>
    </tr>
    <tr>
      <th class='rowhead'><?php echo $lang->plan->complete;?></th>
      <td><?php echo $lang->plan->completed[$plan->complete];?></td>
    </tr>
    <tr>
      <th class='rowhead'><?php echo $lang->plan->auditor;?></th>
      <td><?php echo $users[$plan->auditor];?></td>
    </tr>
    <tr>
    <tr>
      <th class='rowhead'><?php echo $lang->plan->beginEnd;?></th>
      <td><?php echo $plan->startDate;?>&nbsp;
      <?php echo $plan->finishedDate;?></td>
    </tr>
    <tr>
      <th class='rowhead'><?php echo $lang->plan->remark;?></th>
      <td><?php echo $plan->remark;?></td>
    </tr>  
    <tr>
      <td colspan='2' class='a-center'>
        <?php echo html::backButton();?>
      </td>
    </tr>
  </table>
</form>
<?php include '../../common/view/footer.html.php';?>