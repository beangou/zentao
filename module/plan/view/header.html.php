<?php include '../../common/view/header.html.php';?>
<style>
#story {width:90%}
.delbutton{font-size:12px; color:red; width:80px; padding:0}
.addbutton{font-size:12px; color:darkgreen; width:80px; padding:0}
</style>
<script language='Javascript'>
var newRowID = 0;
/**
 * Insert after the step.
 * 
 * @param  int    $rowID 
 * @access public
 * @return void
 */
function postInsert(rowID)
{
	alert('aa');
    $('#row' + rowID).after(createRow());
    updateStepID();
}

/**
 * Create a step row.
 * 
 * @access public
 * @return void
 */
function createRow()
{
    if(newRowID == 0) newRowID = $('.stepID').size();
    newRowID ++;
    var newRow    = "<tr class='a-center' id='row" + newRowID + "'>";
    newRow += "<td class='stepID'></td>";
    newRow += "<td>'<?php echo html::select('types[]', $lang->plan->types, '', 'class=select-1');?>'</td>";
    newRow += "<td>'<?php echo html::input("sorts[]", '', "class='select-1'");?>'</td>";
    newRow += "<td>'<?php echo html::input("matters[]", '', 'class="f-left text-1"');?>'</td>";
    newRow += "<td>'<?php echo html::input("plans[]", '', "class=text-1");?>'</td>";
    newRow += "<td>'<?php echo html::select("auditors[]", $users, '', "class='select-1'");?>'</td>";
    newRow += "<td>'<?php echo html::input("limits[]", $date, "class='text-1 date'");?>'</td>";
    //newRow += "<td class='a-left w-100px'><nobr>";
    //newRow += "<input type='button' tabindex='-1' class='addbutton' value='" + lblBefore + "' onclick='preInsert("  + newRowID + ")' /><br />";
    //newRow += "<input type='button' tabindex='-1' class='addbutton' value='" + lblAfter  + "' onclick='postInsert(" + newRowID + ")' /><br />";
   //newRow += "<input type='button' tabindex='-1' class='delbutton' value='" + lblDelete + "' onclick='deleteRow("  + newRowID + ")' /><br />";
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
}
</script>