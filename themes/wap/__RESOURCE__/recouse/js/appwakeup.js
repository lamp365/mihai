//url是获取 app版本下载地址的路劲
//首先唤醒app，唤醒不起来则加入提示条，引导下载。并且点击提示条可以下载app
function appWakeUp(url,isshow_down_tip){
    //先唤醒app
    app_wake_to_up();

    if(isshow_down_tip){   //是否显示提醒下载 提示条
        //显示下载提示条
        app_show_tip();
        document.getElementById("appdownloadlink").onclick = function(){
            //点击则下载app
            app_click_to_down(url);
        };
    }

}

//点击下载app版本
function appDownLoad(url){
    //先唤醒app

    var ua2 = navigator.userAgent.toLowerCase();
    if( ua2.match(/MicroMessenger/i) == 'micromessenger'){
        app_click_to_down(url);
    }else{
        app_wake_to_up();
        //点击则下载app
        setTimeout(function(){
            app_click_to_down(url)
        },300);     
    }
}


function app_wake_to_up(){
    var iPhoneAgreement = "mihaiweb://";
    var AndroidAgreement ="mihai://hinrc.com";
    //IOS
    if(navigator.userAgent.match(/(iPhone|iPod|iPad);?/i)){
        if(navigator.userAgent.indexOf("Safari") ==-1){
            window.location.href = iPhoneAgreement;
        }
    }else{
       // window.location.href = AndroidAgreement;
    }
}
function app_click_to_down(url){
    var ua = navigator.userAgent.toLowerCase();
    var iPhoneUrl = "";
    var AndroidUrl = "";
    var Apply_AndroidUrl = "";
    var Apply_iPhoneUrl = "";

    var last_url = "";
    $.ajax({
        url:url,
        type: "POST",
        async: false,
        dataType:'json',
        success:function(response,xml){
            if(response.errno==200){
                var obj = response.message;
                iPhoneUrl = obj.iPhoneUrl;
                AndroidUrl = obj.AndroidUrl;
                Apply_iPhoneUrl = obj.Apply_iPhoneUrl;
                Apply_AndroidUrl = obj.Apply_AndroidUrl;
                if( ua.match(/MicroMessenger/i) == 'micromessenger'){
                    if(navigator.userAgent.match(/(iPhone|iPod|iPad);?/i)){
                       last_url = Apply_iPhoneUrl; 
                    }else if(navigator.userAgent.match(/android/i)){
                       last_url = Apply_AndroidUrl;  
                    }
                }else if(navigator.userAgent.match(/(iPhone|iPod|iPad);?/i)){
                    last_url = iPhoneUrl;
                }else if(navigator.userAgent.match(/android/i)){
                    last_url = AndroidUrl;
                }
            }
            
        }
    });
    window.location.href = last_url;
}

function app_show_tip(){
    var div = document.createElement("div");
    var img = document.createElement("img");
    var wx_nav    = document.getElementsByClassName("wx_nav")[0];
    var foot_menu = document.getElementsByClassName("foot_menu")[0];
    var divlink = document.createElement("div");
    var close_div = document.createElement("div");
    if(wx_nav || foot_menu){
        div.className = "appdownload-hasfooter";
    }else{
        div.className = "appdownload-nofooter";
    }
    div.id = "appdownload";
    divlink.id = "appdownloadlink";
    close_div.id = "closeLoad";
    img.src ="/images/down_banner.png";
    div.appendChild(img);
    div.appendChild(divlink);
    div.appendChild(close_div);
    document.body.appendChild(div);
    var appdownload_div = document.getElementById("appdownload");
    document.getElementById("closeLoad").onclick = function(){
        document.body.removeChild(appdownload_div);    
    }
}