$(function(){
	// 搜索返回按钮
	$(".search-return").on("click",function(){
		$(".search-alert").removeClass("search-alert-show");
	});
	if($(".searchinput").val()!=""){
		$(".form-reset").show();
	}
	//搜索框内有值出现重置按钮，否则隐藏重置按钮
	$(".searchinput").on("input",function(){
		if($(this).val()!=""){
			$(".form-reset").show();
		}else{
			$(".form-reset").hide();
		}
	});
	//搜索重置功能
	$(".form-reset").on("click",function(){
		$(".searchinput").val("");
		$(".form-reset").hide();
	});
});
function formSub(){
	$(".search-form").submit();
}
function searchShow(){
	$(".search-alert").addClass("search-alert-show");
}