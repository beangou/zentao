<?php
/**
 * The view view of feedback module
 *
 * @copyright   Copyright 2009-2011 青岛易软天创网络科技有限公司 (QingDao Nature Easy Soft Network Technology Co,LTD www.cnezsoft.com)
 * @license     商业软件，未经授权，请立刻删除!
 * @author      Congzhi Chen<congzhi@cnezsoft.com>
 * @package     feedback
 * @version     $Id: buildform.html.php 1914 2011-06-24 10:11:25Z yidong@cnezsoft.com $
 * @link        http://www.zentao.net
 */
?>
<?php $config->feedback->editor->view = array('id' => 'reply', 'tools' => 'simpleTools');?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/kindeditor.html.php';?>
  <table class='table-1 fixed' align='center'>
    <caption class='a-left blue'>
    <?php 
    if($request->status == 'viewed')
    {
      echo $lang->request->statusList['wait'] . $lang->arrow . $request->title;
    }
    else
    {
      echo $lang->request->statusList[$request->status] . $lang->arrow . $request->title;
    }
    ?>
    </caption>
    <tr>
      <td><?php echo $request->desc;?><br />
      <?php foreach($request->files as $file):?>
      <?php echo html::a($file->downloadPath, $file->title, '_blank')?><br />
      <?php endforeach;?>
        <br/>
        <strong><?php echo $lang->request->customer;?></strong>:<?php echo $request->customerAccount;?>
        <strong><?php echo $lang->request->addedDate;?></strong>:<?php echo $request->addedDate;?>
        <strong><?php echo $lang->request->product;?></strong>:<?php echo $request->productName;?>
        <strong><?php echo $lang->request->category;?></strong>:<?php echo $request->categoryName;?>
        <div class='a-right'>
        <?php if($request->status != 'closed') echo html::commonButton($lang->request->reply, "onclick='showReply()'");?> 
        <?php echo html::linkButton($lang->goback, $this->inLink('browse', "type=" . $this->session->type));?> 
        </div>  
      </td>
    </tr>
  </table>
  <?php if($comment == 1):?>
  <div>
  <form method='post' target='hiddenwin' action='<?php echo inlink('comment', "requestID=$request->id&paramString=$paramString")?>'>
    <table class='table-1 fixed' align='center'>
      <caption><?php echo $lang->request->commentReply;?></caption>
      <tr>
        <th class='w-100px'><?php echo $lang->request->comment;?></th>
        <td><?php echo html::textarea('comment', '', 'style="width:90%" rows=10');?></td>
      </tr>
      <tr><td colspan='2' class='a-center'><?php echo html::submitButton();?></td></tr>
    </table>
  </form>
  </div>
  <?php endif;?>
  <?php if($request->status != 'closed'):?>
  <div id='replyDiv'>
    <form method='post' target='hiddenwin' action='<?php echo inlink('reply', "requestID=$request->id")?>'>
    <table class='table-1 fixed' align='center'>
      <caption><?php echo $lang->request->reply;?></caption>
      <tr>
        <th width='100'><?php echo $lang->request->reply;?></th>
        <td><?php echo html::textarea('reply', $faq ? $faq->answer : '', 'style="width:90%" rows=10');?></td>
      </tr>
      <tr><td colspan='2' class='a-center'><?php echo html::submitButton();?></td></tr>
    </table>
    </form>
  </div>
  <?php endif;?>
<?php if(!empty($actions)):?>
<?php foreach($actions as $id=>$reply):?>
  <table class='table-1 fixed' align='center'>
    <?php if($reply->action == 'replied'):?>
        <caption class='a-left blue'><?php echo $lang->request->reply;?></caption>
    <?php elseif($reply->action == 'doubted'):?>
        <caption class='a-left blue'><?php echo $lang->request->doubt;?></caption>
    <?php elseif($reply->action == 'commented'):?>
        <caption class='a-left blue'><?php echo $lang->request->commentReply;?></caption>
     <?php elseif($reply->action == 'processed'):?>
        <caption class='a-left blue'><?php echo $lang->request->productReply;?></caption>
    <?php endif;?>
    <form method='post' action='<?php echo inlink('editReply', "requestID=$request->id&replyID=$reply->id");?>'>
    <tr>
      <td>
      <form method='post' action='<?php echo inlink('editReply', "requestID=$request->id&replyID=$reply->id");?>'>
        <?php
        if($editReplyID != $reply->id) echo $reply->comment;
        else if($editReplyID == $reply->id)
        {
            echo html::textarea('comment', $reply->comment, "rows=10 class='text-1'");
            echo html::submitButton();
        }
        ?>
        </form>
        <br />
        <br />
        <?php
        if(empty($reply->realname)) $reply->realname = $reply->actor;
        if(isset($lang->action->desc->{$reply->action}))
        {
            printf($lang->action->desc->{$reply->action}, $reply->date, $reply->realname);
        }
        else
        {
            echo "<strong>$reply->realname</strong> at $reply->date";
        }
        ?>
        <div class='a-right'>
        <?php //if($reply->actor == $app->user->account) echo html::a($this->inLink('view', "requestID=$request->id&editReplyID=$reply->id"), $lang->edit);?>
        </div>
      </td>
    </tr>
  </table>
<?php endforeach;?>
<?php endif;?>
<?php if($rated):?>
  <table class='table-1 fixed'>
    <caption class='a-left blue'><?php echo $lang->request->valuate;?></caption>
    <tr><td><?php echo $lang->request->valuateResult. $rate;?></td></tr>
    <tr><td><?php echo $lang->request->valuateContent. $valuation;?></td></tr>
  </table>
<?php endif;?>
</div>
<script type='text/javascript'>
var viewType    = '<?php echo $viewType?>';
function showReply()
{
  $('#replyDiv').show();
}
</script>
<?php include '../../common/view/footer.html.php';?>
