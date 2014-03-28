$(function()
{
	$('span:#' + 'audit').addClass('active');
})

//$(".colorbox").colorbox({width:960, height:550, iframe:true, transition:'none'});
/**
	 * 加载小组成员或者组长的周计划
	 */
function loadPlan()
{
//	alert($('#member').val());
		//链接1获取本周周计划
		link1 = createLink('plan', 'ajaxGetPlan', 'account='+$('#member').val()+'&flag=0');
		$('#thisPlanBody').load(link1, function(){
			//链接2获取下周周计划			
			link2 = createLink('plan', 'ajaxGetPlan', 'account='+$('#member').val()+'&flag=1');
			$('#nextPlanBody').load(link2, function(){
				//链接3获取未审核周计划			
				link3 = createLink('plan', 'ajaxGetPlan', 'account='+$('#member').val()+'&flag=2');
				$('#unauditPlanBody').load(link3);
			});	
		});
	
}