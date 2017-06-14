/**
 * Created by 刘建凡 on 2017/4/21.
 */
(function($){
    //AJAX加载模板  success 成功时回调方法  error错误时回调方法
    /* 使用方式  当点击的时候 触发以下function
     $.ajaxLoad(url,{},function(){
        $('#alterModal').modal('show');
     });
     远程页面div 事例  宽度可以自己定义
     <div class="alertModal-dialog" style="width:52%">内容随意（可结合bootstrap 的样式会好看）</div>

     //在如：：具体事例  宽度可以自己定义
     <div class="alertModal-dialog" style="width:45%">
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
