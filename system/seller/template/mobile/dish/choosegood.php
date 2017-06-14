<style>
    .select-area{
        margin-bottom: 15px;
    }
    .select-area select{
        padding: 3px;
    }
    .choose-before{
        color: #b0b0b0;
        font-size: 20px;
        cursor: pointer;
    }
    .choose-checked{
        color: #60a75a;
        font-size: 20px;
        cursor: pointer;
    }
    .good-table th,.good-table td{
        text-align: center;
    }
    .spec-name,.spec-detail{
        margin-bottom: 5px;
    }
</style>
<div class="alertModal-dialog" style="width:50%">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">从产品库添加</h4>
    </div>
    <div class="modal-body">
        <div class="select-area">
            <select class="p1" onchange="changeP1(this)">
                <option value="0">--请选择分类一--</option>
                <option value="1">分类1</option>
            </select>
            <select class="p2" onchange="changeP2(this)">
                <option value="0">--分请选择分类二--</option>
            </select>
        </div>
        <table class="good-table table table-bordered">
            <thead>
                <tr>
                    <th>产品名称</th>
                    <th>规格名称</th>
                    <th>规格项</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>手机</td>
                    <td>
                        <div class="spec-name">有效时长</div>
                        <div class="spec-name">剂量</div>
                    </td>
                    <td>
                        <div class="spec-detail">
                           <span class="btn btn-success btn-xs">半个月</span>
                           <span class="btn btn-success btn-xs">半个月</span>
                           <span class="btn btn-success btn-xs">半个月</span>
                        </div>
                        <div class="spec-detail">
                            <span class="btn btn-success btn-xs">1.5升</span>
                            <span class="btn btn-success btn-xs">1.5升</span>
                            <span class="btn btn-success btn-xs">1.5升</span>
                            <span class="btn btn-success btn-xs">1.5升</span>
                            <span class="btn btn-success btn-xs">1.5升</span>
                        </div>
                    </td>
                    <!-- goodurl用来存储跳转的URL ，未选择的class包含choose-before和fa-circle-thin-->
                    <td><i class="choose-i choose-before fa fa-circle-thin" goodurl="1" aria-hidden="true"></i></td>
                </tr>
                <tr>
                    <td>手机</td>
                    <td>
                        <div class="spec-name">有效时长</div>
                        <div class="spec-name">剂量</div>
                    </td>
                    <td>
                        <div class="spec-detail">
                           <span class="btn btn-success btn-xs">半个月</span>
                           <span class="btn btn-success btn-xs">半个月</span>
                           <span class="btn btn-success btn-xs">半个月</span>
                        </div>
                        <div class="spec-detail">
                            <span class="btn btn-success btn-xs">1.5升</span>
                            <span class="btn btn-success btn-xs">1.5升</span>
                            <span class="btn btn-success btn-xs">1.5升</span>
                            <span class="btn btn-success btn-xs">1.5升</span>
                            <span class="btn btn-success btn-xs">1.5升</span>
                        </div>
                    </td>
                    <!-- goodurl用来存储跳转的URL 被选择的class包含choose-checked和fa-check-circle -->
                    <td><i class="choose-i choose-checked fa fa-check-circle" goodurl="2" aria-hidden="true"></i></td>
                </tr>
                <tr>
                    <td>手机</td>
                    <td>有效时长</td>
                    <td><span class="label label-success">半年</span></td>
                    <!-- goodurl用来存储跳转的URL ，未选择的class包含choose-before和fa-circle-thin -->
                    <td><i class="choose-i choose-before fa fa-circle-thin" goodurl="3" aria-hidden="true"></i></td>
                </tr>
                <tr>
                    <td>手机</td>
                    <td>有效时长</td>
                    <td><span class="label label-success">半年</span></td>
                    <!-- goodurl用来存储跳转的URL ，未选择的class包含choose-before和fa-circle-thin -->
                    <td><i class="choose-i choose-before fa fa-circle-thin" goodurl="4" aria-hidden="true"></i></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        <button type="submit" class="btn btn-primary" onclick="save()">确认</button>
    </div>
    <!-- url-hidden隐藏域用来存储跳转的URL -->
    <input type="hidden" class="url-hidden" value="">
</div>
<script>
$(function(){
    $("body").on("click",".choose-i",function(){
        if( $(this).hasClass("fa-check-circle") ){
            //已经选择 直接return
            return false;
        }else{
            $(".choose-i").removeClass("fa-check-circle choose-checked").addClass("fa-circle-thin choose-before");
            $(this).removeClass("fa-circle-thin choose-before").addClass("fa-check-circle choose-checked");
            var url = $(this).attr("goodurl");
            $(".url-hidden").val(url);
        }
    });
    $("body").on("click",".look-spec",function(){
        $(".spec-modal").modal();
    })
})
// 分类一change事件带出分类二的数据
function changeP1(obj){
    var that = $(obj);
    var select_val = that.val();
    var url = "";
    //下拉框联动，请补充URL
    $.post(url,{value:select_val},function(data){
        if( data.errno == 1 ){
            //暂时写个例子，请遍历服务端返回的数据拼接到html中。
            var html = '<option value="0">--分请选择分类二--</option>';
            $(".p2").html(html);
        }else{
            alert(data.message);
        }
    },"json");
}
// 分类二change事件带出分类表格的数据
function changeP2(obj){
    var that = $(obj);
    var select_val = that.val();
    var url = "";
    $.post(url,{value:select_val},function(data){
        if( data.errno == 1 ){
            //暂时写个例子，请遍历服务端返回的数据拼接到html中。
            var html = '<option value="0">--分请选择分类二--</option>';
            $(".good-table").html(html);
        }else{
            alert(data.message);
        }
    },"json")
}
//保存
function save(){
    var url = $(".url-hidden").val(); 
    window.location.href = url;
}
</script>