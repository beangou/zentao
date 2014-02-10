function changeDate(date)
{
    date = date.replace(/\-/g, '');
    link = createLink('plan', 'queryPlan', 'date=' + date);
    location.href=link;
}

$(function()
{
	$('#' + 'queryplan').addClass('active');
});

$(".colorbox").colorbox({width:960, height:550, iframe:true, transition:'none'});