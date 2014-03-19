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
<form method='post' target='hiddenwin'>
<table class='w-p60 fixed' align='center'>
  <caption><?php echo $lang->feedback->syncConfig?></caption>
  <tr>
    <th class='w-150px'><?php echo $lang->feedback->apiRoot?></th>
    <?php $apiRoot = $config->feedback->api->root ? $config->feedback->api->root : "http://"?>
    <td><?php echo html::input('apiRoot', $apiRoot, "class='text-3'")?></td>
  </tr>
  <tr>
    <th class='w-100px'><?php echo $lang->feedback->key?></th>
    <td><?php echo html::input('key', $config->feedback->api->key, "class='text-3'") . $lang->feedback->keyNote?></td>
  </tr>
  <tr>
    <td colspan='2' align='center'><?php echo html::submitButton() . html::resetButton()?></td>
  </tr>
</table>
</form>
<?php include '../../common/view/footer.html.php';?>
