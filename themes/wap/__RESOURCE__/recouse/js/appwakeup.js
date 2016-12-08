function ajax(options) {
    options = options || {};
    options.type = (options.type || "GET").toUpperCase();
    options.dataType = options.dataType || "json";
    var params = formatParams(options.data);

    //创建 - 非IE6 - 第一步
    if (window.XMLHttpRequest) {
        var xhr = new XMLHttpRequest();
    } else { //IE6及其以下版本浏览器
        var xhr = new ActiveXObject('Microsoft.XMLHTTP');
    }

    //接收 - 第三步
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4) {
            var status = xhr.status;
            if (status >= 200 && status < 300) {
                options.success && options.success(xhr.responseText, xhr.responseXML);
            } else {
                options.fail && options.fail(status);
            }
        }
    }

    //连接 和 发送 - 第二步
    if (options.type == "GET") {
        xhr.open("GET", options.url + "?" + params, true);
        xhr.send(null);
    } else if (options.type == "POST") {
        xhr.open("POST", options.url, true);
        //设置表单提交时的内容类型
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.send(params);
    }
}
//格式化参数
function formatParams(data) {
    var arr = [];
    for (var name in data) {
        arr.push(encodeURIComponent(name) + "=" + encodeURIComponent(data[name]));
    }
    arr.push(("v=" + Math.random()).replace(".",""));
    return arr.join("&");
}
//url是获取 app版本下载地址的路劲
//首先唤醒app，唤醒不起来则加入提示条，引导下载。并且点击提示条可以下载app
function appWakeUp(url,isshow_down_tip){
    //先唤醒app
    app_wake_to_up();

    if(isshow_down_tip){   //是否显示提醒下载 提示条
        //显示下载提示条
        app_show_tip();
        document.getElementById("appdownload").onclick = function(){
            //点击则下载app
            app_click_to_down(url);
        };
    }

}

//点击下载app版本
function appDownLoad(url){
    //先唤醒app
    app_wake_to_up();
    //点击则下载app
    app_click_to_down(url);
}


function app_wake_to_up(){
    var iPhoneAgreement = "mihaiweb://";
    var AndroidAgreement ="mihai://hinrc.com";
    //IOS
    if(navigator.userAgent.match(/(iPhone|iPod|iPad);?/i)){
        window.location.href = iPhoneAgreement;
    }
    //Android
    if(navigator.userAgent.match(/android/i)){
        window.location.href = AndroidAgreement;
    }
}

function app_click_to_down(url){
    var ua = navigator.userAgent.toLowerCase();
    var iPhoneUrl = "";
    var AndroidUrl = "";
    ajax({
        url: url,              //请求地址
        type: "POST",                       //请求方式
        data: {},        //请求参数
        dataType: "json",
        success: function (response, xml) {
            console.log(response);
            // 此处放成功后执行的代码
            response = eval("("+response+")");
            var obj = response.message;
            iPhoneUrl = obj.iPhoneUrl;
            AndroidUrl = obj.AndroidUrl;
        },
        fail: function (status) {
            // 此处放失败后执行的代码
        }
    });
    if( ua.match(/MicroMessenger/i) == 'micromessenger'){
        alert("请在浏览器中打开");
    }
    if(navigator.userAgent.match(/(iPhone|iPod|iPad);?/i)){
        window.location.href = iPhoneUrl;
    }
    if(navigator.userAgent.match(/android/i)){
        window.location.href = AndroidUrl;
    }
}


function app_show_tip(){
    var div = document.createElement("div");
    var img = document.createElement("img");
    var wx_nav    = document.getElementsByClassName("wx_nav")[0];
    var foot_menu = document.getElementsByClassName("foot_menu")[0];

    if(wx_nav || foot_menu){
        div.style = "position:fixed;left:0;z-index:999;bottom:45px;width:100%;display:block";
    }else{
        div.style = "position:fixed;left:0;z-index:999;bottom:0;width:100%;display:block";
    }
    div.id = "appdownload";
    img.style = "width:100%";
    img.src ="images/down_banner.png";
    div.appendChild(img);
    document.body.appendChild(div);
}