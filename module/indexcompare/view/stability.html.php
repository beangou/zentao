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
		//alert($('#project_id').val() + ';' + $('#endTime').val());
		if($('#project_id').val() && $('#endTime').val()) {
			link1 = createLink('indexcompare', 'ajaxGetEndTime', 'id='+$('#project_id').val()+'&endTime='+$('#endTime').val());
	
			$('#auditors_time').load(link1, function() {
				link2 = createLink('indexcompare', 'ajaxGetProjAndTime');
				$('#auditors_pro_time').load(link2);
			});
		} else {
			alert('暂时没有需要输入时间的项目或者您未输入！');
		}
	}

	$(function(){  
	    // do something  
// 		getProjects();
	});  

	function inputData() {
			link1 = createLink('indexcompare', 'ajaxInsertStabilityData');
			$('#getResult').load(link1);
	}	
</script>
<body>
	<span id="getResult"></span>
	<input type="button" onclick="inputData()" value="生成数据" class="button-s"/>
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
		    			<span id="auditors_time">
			    			<?php 
			    				echo indexcompare::select('project_id', $ids, '', "class='select-1'");
			    			?>
						</span>	
			    			&nbsp;&nbsp;&nbsp;&nbsp;
		    			输入原始需求结束时间：
		    			<?php echo html::input('endTime',date('Y-m-d'), "class='text-3 date'");?>
		    			<!-- <input id="endTime" type="datetime"/> -->
		    			&nbsp;&nbsp;
		    			<input type="button" onclick="getProjects()" value="确定"/>
		    		</td>
		    	</tr>
		    </table>
		    
		    <table class="table-1 fixed colored datatable border-sep" id="product">
  			  <thead>
  			  	<tr class="colhead">
  			  		<th>产品名</th>
  			  		<th width='260'>项目名</th>
  			  		<th>原始需求结束时间</th>
  			  	</tr>
  			  </thead>
  			  <tbody id='auditors_pro_time'>
  			  		<?php foreach($proAndTimes as $proAndTime):?>
  			  		<tr>
  			  			<td><?php echo $proAndTime->prodName;?></td>
  			  			<td><?php echo $proAndTime->projName;?></td>
  			  			<td><?php echo $proAndTime->initstory_endtime;?></td>
  			  		</tr>
  			  	<?php endforeach;?>
  			  </tbody>
  			  
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
					<tr><th class='rowhead'></th><td class='a-center'><?php echo html::submitButton($lang->indexcompare->query);?></td></tr>
				</table>
  		   </form>
  		   <table class="table-1 fixed colored datatable border-sep" id="product">
  			  <thead>
  			  	<tr class="colhead">
  			  		<th width='200'><?php echo $lang->indexcompare->productName;?></th>
  			  		<th width='80'><?php echo $lang->indexcompare->productStability;?></th>
  			  		<th width='260'><?php echo $lang->indexcompare->projectName;?></th>
  			  		<th><?php echo $lang->indexcompare->addDemandNo;?></th>
  			  		<th><?php echo $lang->indexcompare->changeDemandNo;?></th>
  			  		<th><?php echo $lang->indexcompare->initDemandNo;?></th>
  			  		<th><?php echo $lang->indexcompare->proStability;?></th>
  			  	</tr>
  			  </thead>
  			  <tbody>
  			  <?php $color = false;?>
  			  <?php foreach ($stories as $story):?>
  			  	<tr class="a-center">
  			  		<?php 
  			  			if ($story->rowspanVal > 0) {
  			  				echo '<td rowspan="'. $story->rowspanVal. '">'. $story->productname. '</td>';
  			  				echo '<td rowspan="'. $story->rowspanVal. '">'. $story->productStability. '</td>';
  			  			}
  			  		?>
  			  		<td><?php echo $story->projectname;?></td>
  			  		<td><?php echo $story->addstory;?></td>
  			  		<td><?php echo $story->changestory;?></td>
  			  		
  			  		<td><?php echo $story->initstory;?></td>
  			  		<td><?php echo $story->stability;?></td>
  			  	</tr>
  			  <?php endforeach;?>
  			  </tbody>
  		   </table>
		</td>
		  
		</tr>
	</table>
</body>
<?php include '../../common/view/footer.html.php';?>	