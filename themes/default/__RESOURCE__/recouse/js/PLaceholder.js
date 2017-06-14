//解决360浏览器，placeholder不显示的问题
$(function(){
//	var pwdField    = $("input[type=password]");  
//  var pwdVal      = pwdField.attr('placeholder');  
//  pwdField.after('<input id="pwdPlaceholder" type="text" value='+pwdVal+' autocomplete="off" />');  
//  var pwdPlaceholder = $('#pwdPlaceholder');  
//  pwdPlaceholder.show();  
//  pwdField.hide();  
//    
//  pwdPlaceholder.focus(function(){  
//      pwdPlaceholder.hide();  
//      pwdField.show();  
//      pwdField.focus();  
//  });  
//    
//  pwdField.blur(function(){  
//      if(pwdField.val() == '') {  
//          pwdPlaceholder.show();  
//          pwdField.hide();  
//      }  
//  });  
if(!placeholderSupport()){   // 判断浏览器是否支持 placeholder
    $('[placeholder]').focus(function() {
        var input = $(this);
        if (input.val() == input.attr('placeholder')) {
            input.val('');
            input.removeClass('placeholder');
        }
    }).blur(function() {
        var input = $(this);
        if (input.val() == '' || input.val() == input.attr('placeholder')) {
            input.addClass('placeholder');
            input.val(input.attr('placeholder'));
        }
    }).blur();     
};
})
function placeholderSupport() {
    return 'placeholder' in document.createElement('input');
}
      