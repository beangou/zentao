<?php
/**
 * The create view of plan module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2013 青岛易软天创网络科技有限公司 (QingDao Nature Easy Soft Network Technology Co,LTD www.cnezsoft.com)
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      shihaiyang
 * @package     plan
 * @version     $Id: batchcreate.html.php 4728 2013-10-18 11:00:00Z  $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<form method='post' action="<?php echo $this->inLink('batchAction', "from=handleBatchAction")?>">
  <table class='table-1' > 
    <caption> 
    <?php echo $lang->plan->batchAction . $lang->colon ;?>
    </caption>
    <tr>
      <th class='w-80px'><?php echo $lang->plan->type;?></th>
      <th class='w-50px'><?php echo $lang->plan->sort;?></th>
      <th class='w-150px'><?php echo $lang->plan->matter;?></th>
      <th class='w-230px red'><?php echo $lang->plan->plan;?></th>
      <th class='w-80px'><?php echo $lang->plan->appraise;?></th>
      <th class='w-80px'><?php echo $lang->plan->status;?></th>
      <th class='w-80px'><?php echo $lang->plan->auditor;?></th>
      <th class='w-80px'><?php echo $lang->plan->limit;?></th>
      <th class='w-80px'><?php echo $lang->plan->complete;?></th>
      <th class='w-100px'><?php echo $lang->plan->remark;?></th>
    </tr>
	<?php foreach($actionPlans as $plan):?>
    <tr class='a-center'>
      <td><?php echo html::hidden("planIDList[$plan->id]", $plan->id);?>
      <?php echo html::select("types[$plan->id]", $lang->plan->types, $plan->type, "class='select-1' disabled");?></td>
      <td><?php echo html::select("sorts[$plan->id]", $lang->plan->abcSort, $plan->sort, 'class=select-1 disabled');?></td>
      <td title="<?php echo $plan->matter;?>">
        <?php echo html::input("matters[$plan->id]", $plan->matter, 'class="f-left text-1" readonly');?>
      </td>
      <td title="<?php echo $plan->plan;?>"><?php echo html::input("plans[$plan->id]", $plan->plan, "class='text-1' readonly");?></td>
      <td><?php echo html::select("appraises[$plan->id]", $lang->plan->completed, $plan->appraise, "class='select-1' disabled");?></td>
      <td><?php echo html::select("status[$plan->id]", $lang->plan->handleStatus, $plan->status, "class='select-1'");?></td>
      <td><?php echo html::select("auditors[$plan->id]", $users, $plan->auditor, "class='select-1' disabled");?></td>
      <td><?php echo html::input("limits[$plan->id]", $plan->limit, "class='select-1' readonly");?></td>
      <td><?php echo html::select("completes[$plan->id]", $lang->plan->completed, $plan->complete, !empty($lead)?"class='select-1'":"class='select-1' disabled");?></td>
      <td><?php echo html::input("remarks[$plan->id]", $plan->remark, "class='select-1'");?></td>
    </tr>  
    <?php endforeach;?>
    <tr><td colspan='10' class='a-center'><?php echo html::submitButton() . html::backButton();?></td></tr>
  </table>
</form>
<?php include '../../common/view/footer.html.php';?>