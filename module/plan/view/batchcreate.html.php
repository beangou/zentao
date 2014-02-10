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
<form method='post'>
  <table class='table-1' > 
    <caption> 
    <?php echo $lang->plan->batchCreate . $lang->colon . html::input('startDate', $date, "class='select-2 date'").
    		html::input('finishedDate', $date, "class='select-2 date'");?>
    </caption>
    <tr>
      <th class='w-80px'><?php echo $lang->plan->type;?></th>
      <th class='w-50px'><?php echo $lang->plan->sort;?></th>
      <th><?php echo $lang->plan->matter;?></th>
      <th class='w-p40 red'><?php echo $lang->plan->plan;?></th>
      <th class='w-80px'><?php echo $lang->plan->appraise;?></th>
      <th class='w-80px'><?php echo $lang->plan->auditor;?></th>
      <th class='w-82px'><?php echo $lang->plan->limit;?></th>
    </tr>

    <?php 
    if (!empty($lastPlan)):
    foreach ($lastPlan as $plans):?>
    <tr class='a-center'>
      <td><?php echo html::hidden("taskID[]", $plans->id, "class=text-1");?>
      <?php echo html::select("types[]", $lang->plan->types, isset($plans->type)?$plans->type:'', "class='select-1'");?></td>
      <td><?php echo html::select("sorts[]", $lang->plan->abcSort, isset($plans->sort)?$plans->sort:'', "class='select-1'");?></td>
      <td>
        <?php echo html::input("matters[]", isset($plans->matter)?$plans->matter:$plans->name, 'class="f-left text-1"');?>
      </td>
      <td><?php echo html::input("plans[]", isset($plans->plan)?$plans->plan:'', "class=text-1");?></td>
      <td><?php echo html::select("appraises[]", $lang->plan->completed, isset($plans->appraise)?$plans->appraise:'', "class='select-1'");?></td>
       <td><?php echo html::select("auditors[]", $users, isset($plans->auditor)?$plans->auditor:'', "class='select-1'");?></td>
      <td><?php echo html::input("limits[]", $date, "class='text-1 date'");?></td>
    </tr>  
    <?php endforeach;?>
    <?php 
    else :
    for ($i = 0; $i < $config->plan->batchCreate; $i++) :?>
    <tr class='a-center'>
      <td><?php echo html::select("types[]", $lang->plan->types, '', "class='select-1'");?></td>
      <td><?php echo html::select("sorts[]", $lang->plan->abcSort, '', "class='select-1'");?></td>
      <td>
        <?php echo html::input("matters[]", '', 'class="f-left text-1"');?>
      </td>
      <td><?php echo html::input("plans[]", '', "class=text-1");?></td>
      <td><?php echo html::select("appraises[]",$lang->plan->completed, '', "class='select-1'");?></td>
       <td><?php echo html::select("auditors[]", $users, '', "class='select-1'");?></td>
      <td><?php echo html::input("limits[]", $date, "class='text-1 date'");?></td>
    </tr><?php endfor;?>
    <?php endif;?>
    <tr><td colspan='7' class='a-center'><?php echo html::submitButton() . html::backButton();?></td></tr>
  </table>
</form>
<?php include '../../common/view/footer.html.php';?>
<script>

    $(function(){
        var obj = document.getElementById('sorts');
        if(obj.options[obj.selectedIndex].innerText.length >2){
			//$('#sorts').attr("readonly");
			obj.onclick=function(){
        	var index = this.selectedIndex;     
            this.onchange = function() {     
                this.selectedIndex = index;     
				};
    		};     
        }
        });
</script>