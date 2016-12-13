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
        app_click_to_down(url);
    }
}


function app_wake_to_up(){
    var iPhoneAgreement = "mihaiweb://";
    var AndroidAgreement ="mihai://hinrc.com";
    //IOS
    if(navigator.userAgent.match(/(iPhone|iPod|iPad);?/i)){
        window.location.href = iPhoneAgreement;
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
    $.post(url,{},function(response,xml){
        var obj = response.message;
            iPhoneUrl = obj.iPhoneUrl;
            AndroidUrl = obj.AndroidUrl;
            Apply_iPhoneUrl = obj.Apply_iPhoneUrl;
            Apply_AndroidUrl = obj.Apply_AndroidUrl;
            if( ua.match(/MicroMessenger/i) == 'micromessenger'){
                if(navigator.userAgent.match(/(iPhone|iPod|iPad);?/i)){
                    window.location.href = Apply_iPhoneUrl; 
                }else if(navigator.userAgent.match(/android/i)){
                   window.location.href = Apply_AndroidUrl;  
                }
            }else if(navigator.userAgent.match(/(iPhone|iPod|iPad);?/i)){
                window.location.href = iPhoneUrl;
            }else if(navigator.userAgent.match(/android/i)){
                window.location.href = AndroidUrl;
            }
    },'json');
}

function app_show_tip(){
    var div = document.createElement("div");
    var img = document.createElement("img");
    var wx_nav    = document.getElementsByClassName("wx_nav")[0];
    var foot_menu = document.getElementsByClassName("foot_menu")[0];
    var divlink = document.createElement("div");
    if(wx_nav || foot_menu){
        div.className = "appdownload-hasfooter";
    }else{
        div.className = "appdownload-nofooter";
    }
    div.id = "appdownload";
    divlink.id = "appdownloadlink";
    img.src ="http://192.168.1.85/WEB2/xiaowu/images/down_banner.png";
    div.appendChild(img);
    div.appendChild(divlink);
    document.body.appendChild(div);
}