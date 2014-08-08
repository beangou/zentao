$(function()
{
	$('span:#' + 'leave').addClass('active');

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
        .dpSetPosition($.dpConst.POS_TOP, $.dpConst.POS_RIGHT);
})

function findDepatment() {
	link = createLink('plan', 'ajaxGetDepatment', 'account='+$('#leaverName').val());
	$('#department').load(link);
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