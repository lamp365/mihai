	
	//点击添加修改认证按钮
 $(document).ready(function(){
	function addIdentity(){					
		$("#addIdentityPanel").show();	
		$("#IndentityBtn").hide();
		$(".usuallyQuestion").show();
		//调用上传照片插件
		uploaderfrontImg();
		uploaderbackImg();
	}
	)}


	//上传正面		
	function uploaderfrontImg(){
		// 初始化Web Uploader
		var $ = jQuery,
        $list = $('#fileList1'),
        // 优化retina, 在retina下这个值是2
        ratio = window.devicePixelRatio || 1,

        // 缩略图大小
        thumbnailWidth = 100 * ratio,
        thumbnailHeight = 100 * ratio,

        // Web Uploader实例
        uploader;
		var uploader = WebUploader.create({
			
		    // 选完文件后，是否自动上传。
		    auto: true,
              			
		    swf:'__RESOURCE__/recouse/js/webuploader/Uploader.swf',
		
		    // 文件接收服务端。
		    server: "fileupload.php",
		
		    // 选择文件的按钮。可选。
		    // 内部根据当前运行是创建，可能是input元素，也可能是flash.
		  	
		    pick: '#filePicker1',
			
		    // 只允许选择图片文件。
		    accept: {
		        title: 'Images',
		        extensions: 'gif,jpg,jpeg,bmp,png',
		        mimeTypes: 'image/*'
		    }
		   
		});
		
		
		// 当有文件添加进来的时候
		uploader.on( 'fileQueued', function( file ) {
			//隐藏添加照片的文字
		    var $li = $(
		            '<div id="' + file.id + '" class="indentityImg">' +
		                '<img>' +		                
		            '</div>'
		            ),
		        $img = $li.find('img');	
		    // $list为容器jQuery实例
		    $list.append( $li );
		
		    // 创建缩略图
		    // 如果为非图片文件，可以不用调用此方法。
		    // thumbnailWidth x thumbnailHeight 为 100 x 100
		    uploader.makeThumb( file, function( error, src ) {
		        if ( error ) {
		            $img.replaceWith('<span>不能预览</span>');
		            return;
		        }
		
		        $img.attr( 'src', src );
		    }, thumbnailWidth, thumbnailHeight );
		});

		// 文件上传过程中创建进度条实时显示。
		uploader.on( 'uploadProgress', function( file, percentage ) {
		    var $li = $( '#'+file.id ),
		        $percent = $li.find('.progress span');
			
		    // 避免重复创建
		    if ( !$percent.length ) {
		        $percent = $('<p class="progress"><span></span></p>')
		                .appendTo( $li )
		                .find('span');
		    }
		
		    $percent.css( 'width', percentage * 100 + '%' );
		});
		
		// 文件上传成功，给item添加成功class, 用样式标记上传成功。
		uploader.on( 'uploadSuccess', function( file ) {
//			$("#filePicker1").hide();
			$("#fileList1 h3").show();
		    $( '#'+file.id ).addClass('upload-state-done');
		});
		
		// 文件上传失败，显示上传出错。
		uploader.on( 'uploadError', function( file ) {
		   toast1("short","上传失败");
		});
		
		// 完成上传完了，成功或者失败，先删除进度条。
		uploader.on( 'uploadComplete', function( file ) {
		    $( '#'+file.id ).find('.progress').remove();
		    $("#reloaderFront").show();
		});
		
	}
	
	//上传背面
	function uploaderbackImg(){
		// 初始化Web Uploader
		var $ = jQuery,
        $list2 = $('#fileList2'),
        // 优化retina, 在retina下这个值是2
        ratio = window.devicePixelRatio || 1,

        // 缩略图大小
        thumbnailWidth = 100 * ratio,
        thumbnailHeight = 100 * ratio,

        // Web Uploader实例
        uploader;
		var uploader = WebUploader.create({
			
		    // 选完文件后，是否自动上传。
		    auto: true,
		
		    // swf文件路径
		    swf:'__RESOURCE__/recouse/js/webuploader/Uploader.swf',
		
		    // 文件接收服务端。
		    server: "fileupload.php",
		
		    // 选择文件的按钮。可选。
		    // 内部根据当前运行是创建，可能是input元素，也可能是flash.
		    pick: '#filePicker2',
		
		    // 只允许选择图片文件。
		    accept: {
		        title: 'Images',
		        extensions: 'gif,jpg,jpeg,bmp,png',
		        mimeTypes: 'image/*'
		    }
		});
		
		
		// 当有文件添加进来的时候
		uploader.on( 'fileQueued', function( file ) {			
		    var $li2 = $(
		            '<div id="' + file.id + '" class="indentityImg">' +
		                '<img>' +		                
		            '</div>'
		            ),
		        $img = $li2.find('img');	
		
		    // $list为容器jQuery实例
		    $list2.append( $li2 );
		
		    // 创建缩略图
		    // 如果为非图片文件，可以不用调用此方法。
		    // thumbnailWidth x thumbnailHeight 为 100 x 100
		    uploader.makeThumb( file, function( error, src ) {
		        if ( error ) {
		            $img.replaceWith('<span>不能预览</span>');
		            return;
		        }
		
		        $img.attr( 'src', src );
		    }, thumbnailWidth, thumbnailHeight );
		});

		// 文件上传过程中创建进度条实时显示。
		uploader.on( 'uploadProgress', function( file, percentage ) {
		    var $li = $( '#'+file.id ),
		        $percent = $li.find('.progress span');
		
		    // 避免重复创建
		    if ( !$percent.length ) {
		        $percent = $('<p class="progress"><span></span></p>')
		                .appendTo( $li )
		                .find('span');
		    }
		
		    $percent.css( 'width', percentage * 100 + '%' );
		});
		
		// 文件上传成功，给item添加成功class, 用样式标记上传成功。
		uploader.on( 'uploadSuccess', function( file,response) {
//			$("#filePicker2").hide();
			$("#fileList2 h3").show();
			console.log(response)
		    $( '#'+file.id ).addClass('upload-state-done');
		});
		
		// 文件上传失败，显示上传出错。
		uploader.on( 'uploadError', function( file ) {
		        toast2("short","上传失败");
		});
		
		// 完成上传完了，成功或者失败，先删除进度条。
		uploader.on( 'uploadComplete', function( file ) {
		    $( '#'+file.id ).find('.progress').remove();
		    $("#reloaderBack").show();
		});
		
	}
	