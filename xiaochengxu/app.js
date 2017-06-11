var config = require('./config');
// 授权登录
App({
  onLaunch: function () {
    var that = this;
    wx.checkSession({
        success: function(success_res){
            //session 未过期，并且在本生命周期一直有效
            var session3rd  = '';
            wx.getStorage({
                key : 'session3rd',
                success:function (session_res) {
                  session3rd = session_res.data;
                }
            });
            console.log(session3rd,'3rd');
            if(session3rd == '' || session3rd == undefined){
                //登录态过期 重新登录
                that.letUserLogin();
            }else{
                //验证用户信息
                that.checkUserInfo(session3rd);
            }
        },
        fail: function(){
            //登录态过期 重新登录
            that.letUserLogin();

        }
    });



    // 设备信息
    wx.getSystemInfo({
      success: function (res) {
        that.screenWidth = res.windowWidth;
        that.screenHeight = res.windowHeight;
        that.pixelRatio = res.pixelRatio;
      }
    });
  },

  //设置用户登录的公共方法
  letUserLogin:function () {
    wx.login({
      success: function (res) {
        if (res.code) {
          // 获取openId并缓存
          wx.request({
            url: config.service.host+'/xcx/login/index.html',
            data: {
              code: res.code,
            },
            method: 'POST',
            header: {
              'content-type': 'application/x-www-form-urlencoded'
            },
            success: function (response) {
              if(response.errno == 1){
                  //登录成功
                  var info    = response.data;
                  that.openid = info.openid;
                  wx.setStorage({
                      key  : "session3rd",
                      data : info.session3rd
                  });
              }else{
                wx.showModal({
                  title: res.message
                });
              }
            }
          });
        } else {
          console.log('获取用户登录态失败！' + res.errMsg)
        }//if code end
      }//success end
    });
  },//letUserLogin  end

  //检验用户信息 合法  以及是否过期
  checkUserInfo : function (session3rd) {
    wx.login({
        success: function (res) {
           var code  = res.code;
            wx.getUserInfo({
              success: function (info) {
                var rawData        = info['rawData'];
                var signature      = info['signature'];
                var encryptedData  = info['encryptedData'];
                var iv             = info['iv'];

                //小程序调用server 传入code, rawData, signature, encryptData.
                wx.request({
                  url: config.service.host+'/xcx/login/checkUser.html',
                  data: {
                    "code"       : code,
                    "session3rd" : session3rd,
                    "rawData"    : rawData,
                    "signature"    : signature,
                    'encryptedData': encryptedData,
                    'iv' : iv
                  },
                  method: 'POST',
                  header: {
                    'content-type': 'application/x-www-form-urlencoded'
                  },
                  success: function(res) {
                    if(res.errno == 2) {
                      //重新设置用户缓存 已经重新登录了
                      var info    = response.data;
                      that.openid = info.openid;
                      wx.setStorage({
                          key  : "session3rd",
                          data : info.session3rd
                      });
                    }else if(res.errno == 0){
                      wx.showModal({
                        title: res.message
                      });
                    }
                  }
                });// request end
              }
          });//userinfo end
        }
    }); // login end
  }
});