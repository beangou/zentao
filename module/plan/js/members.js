function changeDate(date)
{
    date = date.replace(/\-/g, '');
    link = createLink('plan', 'members', 'finish=' + date);
    location.href=link;
}
$(function()
{
	$('#' + 'members').addClass('active');
});