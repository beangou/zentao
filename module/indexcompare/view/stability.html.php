<?php 
/*member group settings*/
?>
<?php include '../../common/view/header.html.php';
 	  include '../../common/view/tablesorter.html.php';
 	  include '../../common/view/colorize.html.php';
 	  include '../../common/view/datepicker.html.php';
?>
<style>
.rowcolor{background:#F9F9F9;}
</style>

<script type="text/javascript">
	//等页面加载完成后，、
	//通过ajax请求，填充选择项目中后的下拉框
	function getProjects() {
		link = createLink('indexcompare', 'ajaxGetEndTime', 'id=44&memb=66');
		$('#auditors_bean').load(link);
	}
</script>
<body>
	<table width="100%" class="cont-lt1">
		<tr valign="top">
		  <td width="12%" style="padding-right: 10px;" class="side">
				  <div class="box-title"><?php echo $lang->indexcompare->titStability;?></div>
				  <div class="box-content">
				  <ul id="report-list">
				  	<li>
				  		<?php echo html::a($this->createLink('indexcompare', 'stability'), $lang->indexcompare->proStability);?>
				  	</li>
				  	<li>
				  		<?php echo html::a($this->createLink('indexcompare', 'perStability'), $lang->indexcompare->perStability);?>
				  	</li>
				  </ul>
				  </div>
		  </td>
		  <td>
		  
		    <table class='table-1 a-left' >
		    	<tr>
		    		<td>
		    			输入项目：
		    			<span id="auditors_bean">
			    			<?php 
			    				echo html::select('id', $ids, '', "class='select-1'");
			    			?>
						</span>	
			    			&nbsp;&nbsp;&nbsp;&nbsp;
		    			输入原始需求结束时间：<input type="datetime"/>
		    			&nbsp;&nbsp;
		    			<input type="button" onclick="getProjects()" value="确定"/>
		    		</td>
		    	</tr>
		    </table>
				<!-- <div class="week-title"><?php echo $lang->indexcompare->titStability;?></div>-->
			<form method="post">
				<table align='center' class='table-1 a-left'>
					<tr>
						<th><?php echo $lang->indexcompare->choose.'：';?><?php echo html::selectAll('defect', 'checkbox', false);?></th>
						<td id='defect' class='f-14px pv-10px'>
						<?php $i = 1;?>
				        <?php foreach($products as $product => $name):?>
				        <div style="width: 180px;" class='f-left'><?php echo '<span>' . html::checkbox("ids", array($product => $name), '') . '</span>';?></div>
				        <?php if(($i %  5) == 0) echo "<div class='c-both'></div>"; $i ++;?>
				        <?php endforeach;?>
					</td>
					</tr>
					<tr><th class='rowhead'></th><td class='a-center'><?php echo html::submitButton($lang->defect->query);?></td></tr>
				</table>
  		   </form>
  		   <table class="table-1 fixed colored datatable border-sep" id="product">
  			  <thead>
  			  	<tr class="colhead">
  			  		<th width='200'><?php echo $lang->indexcompare->productName;?></th>
  			  		<th width='260'><?php echo $lang->indexcompare->projectName;?></th>
  			  		<th><?php echo $lang->indexcompare->addDemandNo;?></th>
  			  		<th><?php echo $lang->indexcompare->changeDemandNo;?></th>
  			  		<th><?php echo $lang->indexcompare->initDemandNo;?></th>
  			  		<th><?php echo $lang->indexcompare->proStability;?></th>
  			  		<th><?php echo $lang->indexcompare->proStartDate;?></th>
  			  		<th><?php echo $lang->indexcompare->proEndDate;?></th>
  			  	</tr>
  			  </thead>
  			  <tbody>
  			  <?php $color = false;?>
  			  <?php foreach ($defectRate as $defect):?>
  			  	<tr class="a-center">
  			  		<?php $count = isset($defect->details) ? count($defect->details) : 1;?>
  			  		<td align='left' rowspan="<?php echo $count;?>"><?php echo "<p>" . html::a($this->createLink('product', 'view', "product=$defect->id"), $defect->name) . "</p>";?></td>
  			  		<?php if(isset($defect->details)):?>
		            <?php $id = 1;?>
		            <?php foreach($defect->details as $project):?>
		            <?php $class = $color ? 'rowcolor' : '';?>
		            <?php if($id != 1) echo "<tr class='a-center'>"?>
  			  		<td><?php echo $project->projectName;?></td>
  			  		<td><?php echo $project->selfBug;?></td>
  			  		<td><?php echo $project->total;?></td>
  			  		<td><?php echo $project->defect==0?$project->defect:round($project->defect*100,2).'%';?></td>
  			  		<td><?php echo $project->begin;?></td>
  			  		<td><?php echo $project->end;?></td>
  			  		<?php if($id != 1) echo "</tr>"?>
		            <?php $id ++;?>
		            <?php $color = !$color;?>
		            <?php endforeach;?>
            		<?php else:?>
	              <?php $class = $color ? 'rowcolor' : '';?>
	              <td class="<?php echo $class;?>"></td>
	              <td class="<?php echo $class;?>"></td>
	              <td class="<?php echo $class;?>"></td>
	              <td class="<?php echo $class;?>"></td>
	              <td class="<?php echo $class;?>"></td>
	              <td class="<?php echo $class;?>"></td>
	              <td class="<?php echo $class;?>"></td>
	              <?php $color = !$color;?>
	              <?php endif;?>
  			  	</tr>
  			  <?php endforeach;?>
  			  </tbody>
  		   </table>
		</td>
		  
		</tr>
	</table>
</body>
<?php include '../../common/view/footer.html.php';?>	