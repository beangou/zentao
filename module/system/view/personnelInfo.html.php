<td>
      <div class="a-leftS"><?php echo $lang->system->infoTable;?></div>
      <div style="margin-bottom: 10px;">
        <form method="post">
        <span id="searchicon" style="margin-right: 5px;"></span>
        <?php echo $lang->user->account;?>：<?php echo html::input('account',isset($account)?$account:'',"class=text-2");?>
        <?php echo html::submitButton($lang->salary->queryButton);?>
      	</form>
      </div>
<div style="width:100%">
  <div style="float: left;width: 49%">
	<form method="post">
      	<table width="100%">
        <thead>
        <tr><th class="a-left" colspan="7"><?php echo html::submitButton('修改');?></th>      
        </tr>
        <tr class='colhead'>
        <td><?php echo "<input type='hidden' name='typeID' value='1'/>";?></td>
          <th><?php echo $lang->system->number;?></th>
          <th><?php echo $lang->system->name;?></th>
          <th><?php echo $lang->system->standSalary;?></th>
          <th><?php echo $lang->system->loginAccount;?></th>
          <th><?php echo $lang->system->role;?></th>
          <th><?php echo $lang->actions;?></th>
        </tr>
        </thead>
        <tbody>
        <?php $i = 0;?>
        <?php foreach ($userInfo as $info):?>
        <?php $i++;?>
        <tr class='a-center' style="height: 35px;">
          <td><?php echo "<input type='radio' name='account' value='$info->account' id='account'> ";?></td>
          <td><?php echo $i;?></td>
          <td><?php echo $info->realname;?></td>
          <td><?php echo $info->standsalary;?></td>
          <td><?php echo $info->account;?></td>
          <td><?php echo $info->role;?></td>
          <td class='a-left'>
            <?php 
            common::printIcon('system', 'edit', "typeID=1&account=$info->account", '', 'list');
            if(strpos($this->app->company->admins, ",{$info->account},") === false) common::printIcon('system', 'delete', "page=person&account=$info->account", '', 'list', '', "hiddenwin");
            ?>
          </td>
        </tr>
        <?php endforeach;?>
        </tbody>
        </table>
        </form></div>
    <div style="float: right;width: 49%;">
        <form method="post" id="batchCopy">
        <table width="100%">
	        <thead>
	        <tr><th class="a-left" colspan="4"><?php echo html::submitButton('同步','onclick=batchCopy()');?></th>      
        	</tr>
	        <tr class='colhead'>
	          <td><?php echo "<input type='hidden' name='typeID' value='2'/>";?></td>
	          <th><?php echo $lang->system->number;?></th>
	          <th><?php echo $lang->system->name;?></th>
	          <th><?php echo $lang->system->loginAccount;?></th>
	        </tr>
	        </thead>
	          <tbody>
	        <?php $i = 0;?>
	        <?php foreach ($ztUser as $zt):?>
	        <?php $i++;?>
	        <tr class='a-center' style="height: 35px;">
	          <td><?php echo "<input type='checkbox' name='accounts[]' value='$zt->account' id='account'> ";?></td>
	          <td><?php echo $i;?></td>
	          <td><?php echo $zt->realname;?></td>
	          <td><?php echo $zt->account;?></td>
	        </tr>
	        <?php endforeach;?>
	        </tbody>
        </table>
        </form>
    </div></div>
</td>