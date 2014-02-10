function changeDate(date)
{
    date = date.replace(/\-/g, '');
    link = createLink('plan', 'deal', 'finish=' + date);
    location.href=link;
}
$(function()
{
	$('#' + 'deal').addClass('active');
});