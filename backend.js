$(function(){
    'use strict';

     

    // HIDE PLACEHOLDER ON FORM FOCUS
    $('[placeholder]').focus(function(){
        $(this).attr('data-text', $(this).attr('placeholder'));
        $(this).attr('placeholder', '');
    }).blur(function(){
        $(this).attr('placeholder', $(this).attr('data-text'));
    });
    
    // Confirmation message on button delete
    $('.confirm').click(function(){
        return confirm("Are you sure you want to delete this user?");
    });
});