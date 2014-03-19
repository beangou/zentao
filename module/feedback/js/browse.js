$(document).ready(function() 
{
    if($('a.reply').size()) $("a.reply").colorbox({width:800, height:500, iframe:true, transition:'elastic', speed:350, scrolling:true});
    $('#' + browseType + 'Tab').addClass('active');
});
