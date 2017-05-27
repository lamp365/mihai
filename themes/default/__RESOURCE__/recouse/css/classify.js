var offset = 0;
var offset_search = 0;
function cleargoods(){
	$("#good").empty();
	 offset_search = 0;
}
window.onscroll = function(){ 
	var t = document.documentElement.scrollTop || document.body.scrollTop;
	if(t>=44){
	}
}
//下拉加载商品
window.onscroll = function(){ 
	var t = document.documentElement.scrollTop || document.body.scrollTop;
	if( ( $(window).height() + $(window).scrollTop() ) >= ( $("body").height()*0.9 )){
		if(status == 0)
		//  ++offset;
		  if($("#key").val()=="输入搜索内容"||$("#key").val()==""){
			  ++offset;
			  //alert(offset);
			  loadMore();
		  }else{
			  ++offset_search;
			  search();
		  }
		}
	}
//跳转JS
function turnto(url){
	window.location=url;
}
function nofind(){
	var img=event.srcElement;
	img.src="images/logo/error.jpg";
	img.style.height="70px";
	img.style.width="70px";
	img.onerror=null; 
	}