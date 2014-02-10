<td>
	<form method="post" id="projectSet">
      <table id="accumulativeT" style="width: 60%;border: 0px;border-left: 10%;">
        <thead>
          <tr>
            <th colspan="4" class="a-leftS"><?php echo $lang->system->projectInfo;?></th>
          </tr>
      	</thead>
      	</table>
      	<table class="accumulativeT" style="width:60%;border:0px;margin-left:20px;" >
        <tbody>
        	<tr>
        	  <td style="width:90px;text-align:right;padding-right:3px"><?php echo $lang->system->productName;?></td>
        	  <td style="width:300px"><?php echo html::select('productId',$products,'',"onchange=loadAll(this.value) class='select-3'");?></td>
        	  <td style="width:120px;text-align:right;padding-right:3px"><?php echo $lang->system->deptCoeff;?></td>
        	  <td><?php echo html::input('deptCoeff','',"class=text-1");?></td>
        	</tr>
        	<tr>
        	  <td style="text-align:right;padding-right:3px"><?php echo $lang->system->projectName;?></td>
        	  <td id="projectIdBox"><?php echo html::select('project', isset($projects)?$projects:'', '', 'class=select-3');?></td>
        	  <td style="text-align:right;padding-right:3px"><?php echo $lang->system->coefficient;?></td>
        	  <td><?php echo html::input('coefficient','',"class=text-1");?></td>
        	</tr>
        	<tr>
        	  <td style="text-align:right;padding-right:3px"><?php echo $lang->system->DM;?></td>
        	  <td><?php echo html::select('DM',$DMList,'',"class='select-3'");?></td>
        	  <td style="text-align:right;padding-right:3px"><?php echo $lang->system->developNum;?></td>
        	  <td><?php echo html::input('partNum','',"class=text-1");?></td>
        	</tr>
        	<tr>
        	  <td style="text-align:right;padding-right:3px"><?php echo $lang->system->month;?></td>
        	  <td><?php echo html::input('createDate',date('Y-m-d',time()),"class='text-2 date'");?></td>
        	  <td style="text-align:right;padding-right:3px"><?php echo $lang->system->completrate;?></td>
        	  <td><?php echo html::input('completrate','',"class=text-1");?></td>
        	  </tr>
        </tbody>
        	<tr>
        	<td></td>
		    <td align="center"><?php echo html::submitButton('保存','onsubmit="return checkForm()"');?></td>
		    <td align="left"><?php echo html::commonButton('取消');?></td>
		  </tr>
        </table>
        </form>
        <form method="post" >
           <div style="margin-left: 8px"><?php echo html::input('month',isset($month)?$month:date('Y-m-d',time()),"class='text-2 date'");?>
           		<?php echo html::submitButton($lang->system->query);?>
           </div>
        </form>
        <form action="" method="post" id='projectTaskForm'>
           <table style="width: 90%;margin-left:8px;" class="tabbox">
               <thead>
			        <tr class='colhead'>
			          <th><?php echo '';?></th>
			          <th><?php echo $lang->system->productName;?></th>
			          <th><?php echo $lang->system->projectName;?></th>
			          <th><?php echo $lang->system->DM;?></th>
			          <th><?php echo $lang->system->deptCoeff;?></th>
			          <th><?php echo $lang->system->coefficient;?></th>
			          <th><?php echo $lang->system->developNum;?></th>
			          <th><?php echo $lang->system->completrate;?></th>
			          
			        </tr>
			   </thead>
			   <tbody>
			        <?php foreach ($productInfo as $product):?>
			        <div style="display: none;">
			        <?php echo "<input type='hidden' value='$product->DM' name='DM[]'/>"?>
			        <?php echo "<input type='hidden' value='$product->id' name='ids[]'/>"?>
			        <?php echo "<input type='hidden' value='$month' name='month'/>"?>
			        </div>
			        <tr class='a-center' style="height: 35px;">
			          <td><?php echo "<input type='checkbox' name='productID[]' value='$product->id'> ";?></td>
			          <td><?php echo $product->productName;?></td>
			          <td><?php echo $product->projectName;?></td>
			          <td><?php echo $product->realname;?></td>
			          <td><?php echo "<input type='text' value='$product->deptCoeff' name='deptCoeff[]'/>";?></td>
			          <td ><?php echo "<input type='text' value='$product->coefficient' name='coefficient[]'/>";?></td>
			          <td><?php echo "<input type='text' value='$product->partnum' name='partnum[]'/>";?></td>
			          <td><?php echo "<input type='text' value='$product->completrate' name='completrate[]'/>";?></td>
			          
			        </tr>
			        <?php endforeach;?>
			        <tr>
			        
			        <td colspan="4"></td>
                        <td colspan="4"><?php echo html::submitButton($lang->save)?>&nbsp;
	                    <?php $actionLink = $this->createLink('system', 'batchDelete');
                        echo html::submitButton($lang->delete, "onclick=\"changeAction('projectTaskForm', 'batchDelete', '$actionLink')\"");?>
                        </td>
                        </tr>
	        	</tbody>
           </table> 
        </form>
      </td>