/* 可以先写伪代码，在根据伪代码写代码。 
				瀑布流的特点是：列数可以根据窗口大小自适应，图片并不是从左到右按顺序放的，
				而是，找到上一行高度最小的一张，放在它下面，再找剩下的高度最小，以此类推
			*/
		
			window.onload = function(){
				waterfall();				
			}
			window.onresize = function(){   //窗口大小改变时，瀑布的列数自适应
				waterfall();
			}
			
			//为了可以根据窗口自适应，将它封装成函数，窗口改变时，重新加载
			function waterfall() {
					//获取所有的div
				var divall = document.getElementById("wrap").getElementsByTagName("div");
				//console.log(divall.length);
				
					//var 总的列数 = 向下取整（浏览器宽度/图片宽度）;  ！！！注意是向下取整
				//var colAll = Math.floor(document.body.offsetWidth/200);
				var colAll = 2;
					//console.log(colAll);
					//var 水平间距 = （浏览器宽度 - 图片宽度*总的列数）/（总的列数+1）;	
				var perwidth =  window.screen.width*0.47;				
				
				$(".wrap div").width(perwidth);
				var spaceX = (window.screen.width - perwidth* colAll) / (colAll+1);			
					//var 垂直间距 = 10;      ！！！！居然是自己设定的
				var spaceY = 10;
					//var 保存下一行可以在的位置的数组arr;  
					//因为每次都要找到高度最小的一张，所以想到用数组，但是记得更新
				var arr = [];
				
				for(var i=0; i<divall.length; i++){
						//当前列数 = 计算高度最小的那一列;					
					if(i<colAll){	//第一行
							//divall[i].left = 第几列*水平间距 + （第几列-1）*图片宽度; //因为i从0开始
						divall[i].style.left = (i + 1) * spaceX + (i + 1 -1) * perwidth + "px";   //这里需要px
						divall[i].style.top = 10 + "px";
							//arr[第几列-1] = {left: divall[i].left ,top: top+height+垂直间距};
						arr[i] = {
							left:divall[i].offsetLeft,
							top: divall[i].offsetHeight + 2 * spaceY  //!!!!arr存的是下一行的高应该在的位置
							//!!!!!top:divall[i].style.top+ divall[i].offsetHeight + ospace  
						}
						
					} else {   //第二行开始
							//当前列数 = 找到arr top值最小的那一列
						var smallTop = 0;    //是写在循环外面的
						for(var n = 1 ; n < arr.length ; n++){      //这个for循环用来找到当前数组中最小的top
							
							if(arr[n].top < arr[smallTop].top){
								smallTop = n;
							}
							//return smallTop;	//!!!!!!!不用return
						}
						
								//divall[i].left = arr[当前列数].left;
						divall[i].style.left = arr[smallTop].left + "px";  //!!!!
							//divall[i].top = arr[当前列数].top;    
						divall[i].style.top = arr[smallTop].top + "px";    //!!!!!
							//arr[当前列数-1] = {left: divall[i].left ,top: top+height+垂直间距};
						
						arr[smallTop] = {
							//left : divall[i].style.left,
							left:divall[i].offsetLeft,
							//top : divall[i].style.top + 200 + 20 + "px"   !!!!!!没有单位的,top也不对
							
							top: arr[smallTop].top + divall[i].offsetHeight + 20
						}
						
						
					}	
						
				}
				//为了首页的晒物笔记的外层有高度
				var boxheight = $(".category-container:last-child").prop("scrollHeight")-400 || $(".mhnews-wap").prop("scrollHeight");			
				window.setTimeout(function(){$("#wrap").css({"height":boxheight+"px"})},500);						 
			}		
			