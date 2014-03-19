$(function(){
    $('#allNoSync').click(function(){
        if($(this).attr('checked'))
        {
          $('#noSync :checkbox').attr('checked', true);
        }
        else
        {
          $('#noSync :checkbox').attr('checked', false);
        }
      });
    $('#allSynced').click(function(){
        if($(this).attr('checked'))
        {
          $('#synced').find(':checkbox').attr('checked', true);
        }
        else
        {
          $('#synced').find(':checkbox').attr('checked', false);
        }
      });
    });
