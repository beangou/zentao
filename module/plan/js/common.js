/**
 * @param tableid
 * 加载所选小组组长
 */
function loadLeader(td, proteam){
	link = createLink('plan', 'ajaxGetLeader', 'id='+proteam+'&memb='+td);
	$('#auditors'+td).load(link);
}
/**
 * Change form action.
 * 
 * @param  formName   $formName 
 * @param  actionName $actionName 
 * @param  actionLink $actionLink 
 * @access public
 * @return void
 */
function changeAction(formName, actionName, actionLink)
{
    $('#' + formName).attr('action', actionLink).submit();
}

$(function() 
{ 
    //$("#submenuchangePassword").colorbox({width:600, height:400, iframe:true, transition:'none', scrolling:false});
    $(function(){$('.iframe').colorbox({width:900, height:500, iframe:true, transition:'none', onCleanup:function(){parent.location.href=parent.location.href;}});});
});