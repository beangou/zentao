function showReply()
{
  $('#replyDiv').show();
}
$(function(){
  if(viewType == 'reply')
  {
    $('#replyDiv').show();
  }
  else
  {
    $('#replyDiv').hide();
  }
});
