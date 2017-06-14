/**
 * Created by Luker on 2016/10/31.
 * 使用案例
 * alert(title,message,callback,config);
   confirm(title,message,callback,config);
 config 配置参数：
 width:  宽度,
 height: 最小高度,
 type: 'warning'|'error'|'success'|'info'|'question',
 showConfirmButton: 是否显示确认按钮,
 showCancelButton: 是否显示取消按钮,
 confirmButtonText: '确认',
 cancelButtonText: '取消'

 *  $(function () {
        $("#alert").click(function () {
            alert("Hello world!", "welcome to my world :)", function () {
                //after click the confirm button, will run this callback function
            }, {type: 'success', confirmButtonText: 'OK'});
        });
        $("#confirm").click(function () {
            confirm("Are you sure?", "You will not be able to recover this imaginary file!", function (isConfirm) {
                if (isConfirm) {
                    //after click the confirm
                } else {
                    //after click the cancel
                }
            }, {confirmButtonText: 'Yes, delete it!', cancelButtonText: 'No, cancel plx!', width: 400});
        });
    });
 */
if (typeof $ === 'function') {
    $(function () {
        var BeAlert = {
            defaultConfig: {
                width: 320,
                height: 170,
                timer: 0,
                type: 'warning',
                showConfirmButton: true,
                showCancelButton: false,
                confirmButtonText: '确认',
                cancelButtonText: '取消'
            },
            html: '<div class="BeAlert_box">' +
            '<div class="BeAlert_image"></div>' +
            '<div class="BeAlert_title"></div>' +
            '<div class="BeAlert_message"></div>' +
            '<div class="BeAlert_button">' +
			'<button class="BeAlert_confirm"></button>' +
            '<button class="BeAlert_cancel"></button>' +
            '</div>' +
            '</div>',
            overlay: '<div class="BeAlert_overlay"></div>',
            open: function (title, message, callback, o) {
                var opts = {}, that = this;
                $.extend(opts, that.defaultConfig, o);
                $('body').append(that.html).append(that.overlay);
                var box = $('.BeAlert_box');
                box.css({
                    'width': opts.width + 'px',
                    'min-height': opts.height + 'px',
                    'margin-left': -(opts.width / 2) + 'px'
                });
                $('.BeAlert_image').addClass(opts.type);
                title && $('.BeAlert_title').html(title).show(),
                message && $('.BeAlert_message').html(message).show();
                var confirmBtn = $('.BeAlert_confirm'), cancelBtn = $('.BeAlert_cancel');
                opts.showConfirmButton && confirmBtn.text(opts.confirmButtonText).show(),
                opts.showCancelButton && cancelBtn.text(opts.cancelButtonText).show();
                $('.BeAlert_overlay').unbind('click').bind('click', function () {
                    that.close();
                });
                confirmBtn.unbind('click').bind('click', function () {
                    that.close();
                    typeof callback === 'function' && callback(true);
                });
                cancelBtn.unbind('click').bind('click', function () {
                    that.close();
                    typeof callback === 'function' && callback(false);
                });
                var h = box.height();
                box.css({
                    'margin-top': -(Math.max(h, opts.height) / 2 + 100) + 'px'
                });
            },
            close: function () {
                $(".BeAlert_overlay,.BeAlert_box").remove();
            }
        };
        window.alert = function (title, message, callback, opts) {
            BeAlert.open(title, message, callback, opts);
        };
        var _confirm = window.confirm;
        window.confirm = function (title, message, callback, opts) {
            opts = $.extend({type: 'question', showCancelButton: true}, opts);
            if (typeof callback === 'function') {
                BeAlert.open(title, message, callback, opts);
            } else {
                return _confirm(title);
            }
        }
    });
}



(function($){
    //AJAX加载模板  success 成功时回调方法  error错误时回调方法
    /* 使用方式  当点击的时候 触发以下function
     $.ajaxLoad(url,{},function(){
        $('#alterModal').modal('show');
     });
     远程页面div 事例  宽度可以自己定义
     <div class="alertModal-dialog-bg" style="width:52%">内容随意（可结合bootstrap 的样式会好看）</div>

     //在如：：具体事例  宽度可以自己定义
     <div class="alertModal-dialog-bg" style="width:45%">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">模态框（Modal）标题</h4>
         </div>
         <div class="modal-body">在这里添加一些文本</div>
         <div class="modal-footer">
             <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
             <button type="button" class="btn btn-primary">提交更改</button>
        </div>
     </div>
     */
    $.ajaxLoad = function(url,data,success,error){

        if(data && !$.isEmptyObject(data)){
            method = 'post';
        }else{
            method = 'get';
        }

        $.ajax({
            url:url,
            data:data,
            type:method,
            success:function(result){
                if(result.hasOwnProperty('errno')){
                    if($.isEmptyObject(layer)){
                        alert(result.message);
                    }else{
                        layer.alert(result.message);
                    }
                }else{
                    $("#alterModal").html(result);
                    $.isFunction(success) && success(result);
                }
            },
            error:function(result){
                $.isFunction(error) && error(result);
            }
        });
    };
}($));

//事件绑定-模态框使用多次请求
$("#alterModal").on("hidden.bs.modal", function() {
    $(this).removeData("bs.modal");
});

