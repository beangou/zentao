<?php
/**
 * The browse view file of feedback module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2011 青岛易软天创网络科技有限公司 (QingDao Nature Easy Soft Network Technology Co,LTD www.cnezsoft.com)
 * @license     商业软件，未经授权，请立刻删除!
 * @author      Jinyong Zhu <yidong@cnezsoft.com>
 * @package     feedback
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<script language='Javascript'>
var browseType = '<?php echo $browseType;?>';
$(function(){
$('#by<?php echo $this->session->type?>Tab').addClass('active');
})
</script>
<div id='featurebar'>
  <div class='f-left'>
  <?php
  foreach($lang->request->statusList as $statusName => $statusLabel)
  {
    echo "<span id='by$statusName" . 'Tab' . "'".'>'.html::a(inLink('browse', "type=$statusName"), $statusLabel, ''). "</span>";
  }
  echo "<span id='byassignedToMeTab'>" .html::a(inLink('browse', "type=assignedToMe"), $lang->request->assignedToMe, ''). "</span>";
  echo "<span id='byallowedClosedTab'>". html::a(inLink('browse', "type=allowedClosed"), $lang->request->allowedClosed, ''). "</span>";
  echo "<span id='byrepliedByMeTab'>". html::a(inLink('browse', "type=repliedByMe"), $lang->request->repliedByMe, ''). "</span>";
  echo "<span id='byallTab'>" .html::a(inLink('browse', "type=all"), $lang->request->all, ''). "</span>";
  ?>
  </div>
</div>
<table class='table-1 tablesorter'>
  <?php if($feedbacks):?>
  <?php $vars = "type=$browseType&param=$param&orderBy=%s&recTotal=$recTotal&recPerPage=$recPerPage"; ?>
  <thead>
    <tr class='colhead'>
      <th class='w-id'>   <?php common::printOrderLink('id', $orderBy, $vars, $lang->feedback->id);?></th>
      <th class='w-100px'><?php common::printOrderLink('title', $orderBy, $vars, $lang->feedback->title);?></th>
      <th class='w-50px'> <?php common::printOrderLink('product', $orderBy, $vars, $lang->feedback->product);?></th>
      <th class='w-50px'> <?php common::printOrderLink('category', $orderBy, $vars, $lang->feedback->category);?></th>
      <th class='w-50px'> <?php common::printOrderLink('assignedTo', $orderBy, $vars, $lang->feedback->assignedTo);?></th>
      <th class='w-50px'> <?php common::printOrderLink('status', $orderBy, $vars, $lang->feedback->status);?></th>
      <th class='w-100px'><?php common::printOrderLink('addedDate', $orderBy, $vars, $lang->feedback->addedDate);?></th>
      <th class='w-100px'><?php echo $lang->feedback->actions;?></th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($feedbacks as $feedback):?>
    <tr class='a-center'>
    <td class='strong'><?php echo $feedback->id;?></td>
    <td class='a-left'><?php echo html::a(inlink('view', 'requestID=' . $feedback->id), $feedback->title, '' );?></td>
    <td class='a-center'><?php echo $feedback->productName;?></td>
    <td class='a-center'><?php echo $feedback->category;?></td>
    <td class='a-center'><?php echo $feedback->assignedTo;?></td>
    <td class='a-center'><?php echo $lang->request->statusList[$feedback->status];?></td>
    <td class='a-center'><?php echo $feedback->addedDate;?></td>
    <td class='a-center'>
    <?php 
        if($feedback->status == 'transfered')
        {
            echo html::a(inlink('view', "id={$feedback->id}&viewType=reply"), $lang->feedback->reply ); 
            echo html::a(inlink('toBug', "productID={$feedback->product}&feedbackID={$feedback->id}"), $lang->feedback->toBug); 
            echo html::a(inlink('toStory', "productID={$feedback->product}&feedbackID={$feedback->id}"), $lang->feedback->toStory); 
        }
    ?> 
    </td>
    </tr>
  <?php endforeach;?>
  </tbody>
  <tfoot>
    <tr><td class='a-right' colspan='8'><?php if($pager) $pager->show();?></td></tr>
  </tfoot>
  <?php else:?>
  <tr><td><?php echo $lang->feedback->nothing?></td></tr>
  <?php endif;?>
</table>
<?php include '../../common/view/footer.html.php';?>
