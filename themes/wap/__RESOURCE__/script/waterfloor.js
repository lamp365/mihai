/* ������дα���룬�ڸ���α����д���롣 
				�ٲ������ص��ǣ��������Ը��ݴ��ڴ�С����Ӧ��ͼƬ�����Ǵ����Ұ�˳��ŵģ�
				���ǣ��ҵ���һ�и߶���С��һ�ţ����������棬����ʣ�µĸ߶���С���Դ�����
			*/
		
			window.onload = function(){
				waterfall();				
			}
			window.onresize = function(){   //���ڴ�С�ı�ʱ���ٲ�����������Ӧ
				waterfall();
			}
			
			//Ϊ�˿��Ը��ݴ�������Ӧ��������װ�ɺ��������ڸı�ʱ�����¼���
			function waterfall() {
					//��ȡ���е�div
				var divall = document.getElementById("wrap").getElementsByTagName("div");
				//console.log(divall.length);
				
					//var �ܵ����� = ����ȡ������������/ͼƬ��ȣ�;  ������ע��������ȡ��
				//var colAll = Math.floor(document.body.offsetWidth/200);
				var colAll = 2;
					//console.log(colAll);
					//var ˮƽ��� = ���������� - ͼƬ���*�ܵ�������/���ܵ�����+1��;	
				var perwidth =  window.screen.width*0.47;				
				
				$(".wrap div").width(perwidth);
				var spaceX = (window.screen.width - perwidth* colAll) / (colAll+1);			
					//var ��ֱ��� = 10;      ����������Ȼ���Լ��趨��
				var spaceY = 10;
					//var ������һ�п����ڵ�λ�õ�����arr;  
					//��Ϊÿ�ζ�Ҫ�ҵ��߶���С��һ�ţ������뵽�����飬���Ǽǵø���
				var arr = [];
				
				for(var i=0; i<divall.length; i++){
						//��ǰ���� = ����߶���С����һ��;					
					if(i<colAll){	//��һ��
							//divall[i].left = �ڼ���*ˮƽ��� + ���ڼ���-1��*ͼƬ���; //��Ϊi��0��ʼ
						divall[i].style.left = (i + 1) * spaceX + (i + 1 -1) * perwidth + "px";   //������Ҫpx
						divall[i].style.top = 10 + "px";
							//arr[�ڼ���-1] = {left: divall[i].left ,top: top+height+��ֱ���};
						arr[i] = {
							left:divall[i].offsetLeft,
							top: divall[i].offsetHeight + 2 * spaceY  //!!!!arr�������һ�еĸ�Ӧ���ڵ�λ��
							//!!!!!top:divall[i].style.top+ divall[i].offsetHeight + ospace  
						}
						
					} else {   //�ڶ��п�ʼ
							//��ǰ���� = �ҵ�arr topֵ��С����һ��
						var smallTop = 0;    //��д��ѭ�������
						for(var n = 1 ; n < arr.length ; n++){      //���forѭ�������ҵ���ǰ��������С��top
							
							if(arr[n].top < arr[smallTop].top){
								smallTop = n;
							}
							//return smallTop;	//!!!!!!!����return
						}
						
								//divall[i].left = arr[��ǰ����].left;
						divall[i].style.left = arr[smallTop].left + "px";  //!!!!
							//divall[i].top = arr[��ǰ����].top;    
						divall[i].style.top = arr[smallTop].top + "px";    //!!!!!
							//arr[��ǰ����-1] = {left: divall[i].left ,top: top+height+��ֱ���};
						
						arr[smallTop] = {
							//left : divall[i].style.left,
							left:divall[i].offsetLeft,
							//top : divall[i].style.top + 200 + 20 + "px"   !!!!!!û�е�λ��,topҲ����
							
							top: arr[smallTop].top + divall[i].offsetHeight + 20
						}
						
						
					}	
						
				}
				//Ϊ����ҳ��ɹ��ʼǵ�����и߶�
				var boxheight = $(".category-container:last-child").prop("scrollHeight")-400 || $(".mhnews-wap").prop("scrollHeight");			
				window.setTimeout(function(){$("#wrap").css({"height":boxheight+"px"})},500);						 
			}		
			