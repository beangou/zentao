<td>
	<form method="post" id="userEdit">
	<div><input type="hidden" value="2" name="typeID"/></div>
      	<table class="accumulativeT" style="width: 40%;margin: auto;" >
        <tbody align="center">
         <tr bordercolor="#666666">
		    <th colspan="2"><?php echo $singleInfo->realname;?></th>
		  </tr>
		  <tr>
		    <td><?php echo $lang->system->loginAccount;?></td>
		    <td align="left"><?php echo "<input type='text' name='loginAccount' value=$singleInfo->account readonly style='width:69.5%;height:20px'/>";?></td>
		  </tr>
		  <tr>
		    <td><?php echo $lang->system->role;?></td>
		    <td align="left"><?php echo html::select('role',$lang->system->roleOptions,isset($singleInfo->role)?$singleInfo->role:'',"class='select-3'");?>
		    </td>
		  </tr>
		  <tr>
		    <td><?php echo $lang->system->standSalary;?></td>
		    <td align="left"><?php echo html::input('standSalary',$singleInfo->standsalary,"class='text-3'");?>
		    </td>
		  </tr>
		  <tr>
		    <td><?php echo html::submitButton($lang->save,'onclick=saveUserInfo()');?></td>
		    <td align="left"><?php echo html::backButton();?></td>
		  </tr>
        </tbody>
        </table>
        </form>
      </td>