$(function()
{
	$("#techmanager").chosen({no_results_text: noResultsMatch});
	//$("#techmanager").attr('data-placeholder', "选择技术经理...");
	//$('.default').val("选择技术经理...");
	$('span:#' + 'proteam').addClass('active');
	if(onlybody != 'yes') $('.iframe').colorbox({width:900, height:500, iframe:true, transition:'none'});
	$('#techmanager_chzn').css("width", "300px");
	$('#techmanager_chzn').css("vertical-align", "middle");
})

//$(".colorbox").colorbox({width:960, height:550, iframe:true, transition:'none'});
