<?php
/**
 * The syncProduct view file of feedback module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2011 青岛易软天创网络科技有限公司 (QingDao Nature Easy Soft Network Technology Co,LTD www.cnezsoft.com)
 * @license     商业软件，未经授权，请立刻删除!
 * @author      Congzhi Chen <congzhi@cnezsoft.com>
 * @package     feedback
 * @version     $Id$
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/tablesorter.html.php';?>
<form method='post' target='hiddenwin'>
  <table align='center' class='table-1 a-left'> 
    <caption><?php echo $lang->feedback->syncProduct;?></caption>
    <tr>
      <th class='rowhead'><?php echo $lang->feedback->syncedProducts;?><input type='checkbox' checked='checked' onclick='checkall(this, "group");'></th>
      <td id='group' class='f-14px pv-10px'><?php $i = 1;?>
        <?php foreach($syncedProducts as $syncedProduct):?>
        <div class='w-p10 f-left'><?php echo '<span>' . html::checkbox('products', array($syncedProduct->id => $syncedProduct->name), $syncedProduct->id) . '</span>';?></div>
        <?php if(($i %  8) == 0) echo "<div class='c-both'></div>"; $i ++;?>
        <?php endforeach;?>
      </td>
    </tr>
    <tr>
      <th class='rowhead'><?php echo $lang->feedback->unsyncedProducts;?><input type='checkbox' onclick='checkall(this, "other");'></th>
      <td id='other' class='f-14px pv-10px'><?php $i = 1;?>
        <?php foreach($unsyncedProducts as $unsyncedProduct):?>
        <div class='w-p10 f-left'><?php echo '<span>' . html::checkbox('products', array($unsyncedProduct->id => $unsyncedProduct->name), '') . '</span>';?></div>
        <?php if(($i %  8) == 0) echo "<div class='c-both'></div>"; $i ++;?>
        <?php endforeach;?>
      </td>
    </tr>
    <tr>
      <th class='rowhead'></th>
      <td class='a-center'>
        <?php 
        echo html::submitButton($lang->sync);
        echo html::linkButton($lang->goback, $this->createLink('feedback', 'browse'));
        echo html::hidden('foo'); // Just a var, to make sure $_POST is not empty.
        ?>
      </td>
    </tr>
  </table>
</form>

<?php include '../../common/view/footer.html.php';?>
