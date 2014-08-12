function changeDate(date)
{
    date = date.replace(/\-/g, '');
    link = createLink('plan', 'myplan', 'finish=' + date);
    location.href=link;
}
$(function()
{
	$('#' + 'myplan').addClass('active');
//	window.location.href='index.php?m=plan&f=myplan';
});

$(".colorbox").colorbox({width:960, height:550, iframe:true, transition:'none'});

function changeSubmit(url, isSubmit)
{
	if (isSubmit == '0')
	{
		$('#planform').attr('action', url);
	} else if (isSubmit == '1') {
		$('#addPlanform').attr('action', url);
	}
}


var newRowID = 0;


/**
 * Delete a step row.
 * flag = 0表示新增下周计划
 * flag = 1表示修改下周计划
 * @param  int    $rowID 
 * @access public
 * @return void
 */
function deleteRow(rowID, flag)
{
	if (!confirm('您确认删除该行计划吗？')) {
		return;
	}
    if(flag == 0 && $('.stepAddID').size() == 1) return;
    if(flag == 1 && $('.stepChangeID').size() == 1) return;
    $('#row' + rowID).remove();
//    updateStepID();
    updateStepAddID();
}

//取当前时间，格式为,yyyy-mm-dd hh:mm:ss
function GetDateT()
 {
  var d,s;
  d = new Date();
  s = d.getYear() + "-";             //取年份
  s = s + (d.getMonth() + 1) + "-";//取月份
  s += d.getDate();         //取日期
  return(s);  
 } 

/**
 * Insert after the step.
 * 
 * @param  int    $rowID 
 * @access public
 * @return void
 */
function postInsert(rowID, flag)
{
//    $('#row' + rowID).after(createRow());
	$('#row' + rowID).after(mycreateRow(rowID, flag));
//    updateStepID();
	updateStepAddID();
//	ajaxGetDate(rowID);
	
	$(function() {
	    $('.date').each(function(){
	        time = $(this).val();
	        if(!isNaN(time) && time != ''){
	            var Y = time.substring(0, 4);
	            var m = time.substring(4, 6);
	            var d = time.substring(6, 8);
	            time = Y + '-' + m + '-' + d;
	            $('.date').val(time);
	        }
	    });

	    startDate = new Date(1970, 1, 1);
	    $(".date").datePicker({createButton:true, startDate:startDate})
	        .dpSetPosition($.dpConst.POS_TOP, $.dpConst.POS_RIGHT)
	});
	
}

function ajaxGetDate(rowID)
{
	link1 = createLink('plan', 'ajaxGetDate');
	$('#addRowDate_'+rowID).load(link1);
}

/**
 * Create a step row.
 * 
 * @access public
 * @return void
 */
function createRow()
{
	var obj = eval("("+users+")");
    if(newRowID == 0) newRowID = $('.stepID').size();
    newRowID ++;
    var newRow    = "<tr class='a-center' id='row" + newRowID + "'>";
    newRow += "<td class='stepID'></td>";
    newRow += "<td><input type='hidden' name='taskID[]' value=''/><select name='types[]' class='select-1'><option value='1'>正常工作</option><option value='2'>能力培养</option></select></td>";
    newRow += "<td><input name='sorts[]' class='select-1' onkeyup='this.value=this.value.toUpperCase()' onkeydown='this.value=this.value.replace(/[^a-z0-9_]/,'')'/></td>";
    newRow += "<td><input name='matters[]' class='f-left text-1'/></td>";
    newRow += "<td><input name='plans[]' class='text-1'/></td>";
    newRow += "<td><select name='auditors[]' class='select-1'>";
    for(var key in obj){
		newRow += "<option value="+key+">"+obj[key]+"</option>";
	}
    newRow += "</td></select>";
    //newRow += "<td class='a-left w-100px'><nobr>";
    newRow += "<td><input type='button' tabindex='-1' class='button-s' value='删除 ' onclick='deleteRow("  + newRowID + ")' />";
    newRow += "<input type='button' tabindex='-1' class='button-s' value='新增' onclick='postInsert(" + newRowID + ")' /></td>";
    //newRow += "</nobr></td>";
    newRow += "</tr>";
    return newRow;
}


/**
 * Create a step row.
 * 
 * @flag = 0 即增加下周计划
 *  flag = 1 即修改本周计划
 * @access public
 * @return void
 */
function mycreateRow(paramRowId , flag)
{
	var obj = eval("("+users+")");
//    if(newRowID == 0) newRowID = $('.stepAddID').size();
	if (flag == '0') {
		newRowID = $('.stepAddID').size();
		newRowID ++;
	} else {
		newRowID = $('.stepChangeID').size();
		newRowID ++;
		newRowID = '_this'+newRowID;
	}
	
    
    var newRow    = "<tr class='a-center' id='row" + newRowID + "'>";
    if (flag == '0') {
    	newRow += "<td class='stepAddID' valign='middlle'></td>";
	} else {
		newRow += "<td class='stepChangeID' valign='middlle'></td>";
	}
//    newRow += "<td class='stepAddID' valign='middlle'></td>";
//    newRow += "<td><input name='type[]' class='text-1' onkeyup='this.value=this.value.toUpperCase()') valign='middlle'></td>";
//    newRow += "<td><input name='matter[]' class='text-1')></td>";
    newRow += "<td valign='middlle'>"+$("#copyId").html()+$("#copyType").html()+"</td>";
    newRow += "<td style='text-align: left'>"+$("#copyMatter").html()+"</td>";
    newRow += "<td style='text-align: left'>"+$("#copyPlan").html()+"</td>";
//    newRow += "<td><input name='plan[]' class='text-1'></td>";
//    newRow += "<td id='addRowDate_"+paramRowId+"'></td>";
//    newRow += "<td>"+$("#copyDateTd").html()+"</td>";
    newRow += "<td valign='middlle'><input type='text' name='deadtime[]' class='select-2 date'></td>";//<input type='text' name='$name' id='$name' value='$value' $attrib />\n
    newRow += "<td valign='middlle'>"+$("#selectName").html()+"</td>"; 
//    alert($("#selectName").html());	
//    newRow += "<td><input name='submitTo[]' class='text-1'></td>";
//    newRow += "<td><select name='submitTo[]' class='select-1'>";
//    for(var key in obj){
//		newRow += "<option value="+key+">"+obj[key]+"</option>";
//	}
//    newRow += "</td></select>";
    //newRow += "<td class='a-left w-100px'><nobr>";
    newRow += "<td valign='middlle'><input type='button' tabindex='-1' class='button-s' value='删除 ' onclick=\"deleteRow('"  + newRowID + "', " + flag + ")\" />";
    newRow += "<input type='button' tabindex='-1' class='button-s' value='新增' onclick=\"postInsert('" + newRowID + "', " + flag + ")\"></td>";
    //newRow += "</nobr></td>";
    newRow += "</tr>";
    return newRow;
}





/**
 * Update the step id.
 * 
 * @access public
 * @return void
 */
function updateStepID()
{
    var i = 1;
    $('.stepID').each(function(){$(this).html(i ++)});
    
    var k = 1;
    $('.stepChangeID').each(function(){$(this).html(k ++)});
}


function updateStepAddID()
{
    var i = 1;
    $('.stepAddID').each(function(){$(this).html(i ++)});
    
    var k = 1;
    $('.stepChangeID').each(function(){$(this).html(k ++)});
}

function makesure()
{
	if (confirm('您确定提交吗?')) {
		return true;
	} else {
		return false;
	}
}