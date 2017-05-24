<?php
/*
页面操作相关
*/

function refresh()
{
    global $_GP, $_CMS;
    $_CMS['refresh'] = $_SERVER['HTTP_REFERER'];
    $_CMS['refresh'] = substr($_CMS['refresh'], - 1) == '?' ? substr($_CMS['refresh'], 0, - 1) : $_CMS['refresh'];
    $_CMS['refresh'] = str_replace('&amp;', '&', $_CMS['refresh']);
    $reurl = parse_url($_CMS['refresh']);
    
    if (! empty($reurl['host']) && ! in_array($reurl['host'], array(
        $_SERVER['HTTP_HOST'],
        'www.' . $_SERVER['HTTP_HOST']
    )) && ! in_array($_SERVER['HTTP_HOST'], array(
        $reurl['host'],
        'www.' . $reurl['host']
    ))) {
        $_CMS['refresh'] = WEBSITE_ROOT;
    } elseif (empty($reurl['host'])) {
        $_CMS['refresh'] = WEBSITE_ROOT . './' . $_CMS['referer'];
    }
    return strip_tags($_CMS['refresh']);
}

function page($filename)
{
    global $_CMS;
    if (SYSTEM_ACT == 'mobile') {
        $source = SYSTEM_ROOT . $_CMS['module'] . "/template/mobile/{$filename}.php";
        
        if (! is_file($source)) {
            $source = SYSTEM_ROOT . "common/template/mobile/{$filename}.php";
        }
    } else {
        
        $source = SYSTEM_ROOT . $_CMS['module'] . "/template/web/{$filename}.php";
        if (! is_file($source)) {
            $source = SYSTEM_ROOT . "common/template/web/{$filename}.php";
        }
    }
    return $source;
}

function themePage($filename)
{
    $theme = '';
    $themeconfig = SYSTEM_WEBROOT . "/themes/theme.bjk";
    if (! file_exists($themeconfig)) {
        $myfile = fopen($themeconfig, "w");
        fwrite($myfile, 'default');
        fclose($myfile);
    }

    if (empty($_SESSION["theme"]) || empty($_SESSION["theme_md5"]) || $_SESSION["theme_md5"] != md5_file($themeconfig)) {
        if (file_exists($themeconfig)) {
            $myfile = fopen($themeconfig, "r");
            $theme = fgets($myfile);
            fclose($myfile);
        }
        
        if (empty($theme)) {
            $theme = 'default';
        }
        $_SESSION["theme"] = $theme;
        $_SESSION["theme_md5"] = md5_file($themeconfig);
    } else {
        $theme = $_SESSION["theme"];
    }

    if (is_mobile_request()||$_GET['wap']==1){
        $theme = 'wap';
    }
    $cachefile = WEB_ROOT . '/cache/' . $theme . '/' . $filename . '.php';
    $template = SYSTEM_WEBROOT . '/themes/' . $theme . '/' . $filename . '.html';
    if (! file_exists($template)) {
        $template = SYSTEM_WEBROOT . '/themes/default/' . $filename . '.html';
        $cachefile = WEB_ROOT . '/cache/default/' . $filename . '.php';
        $theme = 'default';
    }

    if (! file_exists($cachefile) || DEVELOPMENT) {
        $str = file_get_contents($template);
        $path = dirname($cachefile);
        if (! is_dir($path)) {
            mkdirs($path);
        }
        $content = preg_replace('/__RESOURCE__/', WEBSITE_ROOT . 'themes/' . $theme . '/__RESOURCE__', $str);
        $content = preg_replace('/<!--@php\s+(.+?)@-->/', '<?php $1?>', $content);
        file_put_contents($cachefile, $content);
        return $cachefile;
    } else {
        return $cachefile;
    }
}

function pagination($total, $pindex, $psize = 15,$callback='',$style=0)
{
    global $_GP, $_CMS;
    $tpage = ceil($total / $psize);
    if ($tpage <= 1) {
        return '';
    }
    $findex = 1;
    $lindex = $tpage;
    $cindex = $pindex;
    $cindex = min($cindex, $tpage);
    $cindex = max($cindex, 1);
    $cindex = $cindex;
    $pindex = $cindex > 1 ? $cindex - 1 : 1;
    $nindex = $cindex < $tpage ? $cindex + 1 : $tpage;
    $_GP['page'] = $findex;
    $furl = 'href="' . 'index.php?' . http_build_query($_GP) . '"';
    $_GP['page'] = $pindex;
    $purl = 'href="' . 'index.php?' . http_build_query($_GP) . '"';
    $_GP['page'] = $nindex;
    $nurl = 'href="' . 'index.php?' . http_build_query($_GP) . '"';
    $_GP['page'] = $lindex;
    $lurl = 'href="' . 'index.php?' . http_build_query($_GP) . '"';
    $beforesize = 5;
    $aftersize = 4;
    if (is_mobile_request()){
        // 初始化异步加载数据
        $html = '<div class="dataTables_paginate paging_simple_numbers">';
        $html .='<input type="hidden" value="1" id="num"/>';
        $html .='<input type="hidden" value="'.$tpage.'" id="maxnum"/>';
        $html .='<input type="hidden" value="1" id="lock"/>';
        if ($tpage > 1){
            $html .="<a class='loading' href='javascript:void(0)' onclick='calls();' style='height:50px;width:60px;margin:5px auto;line-height:50px;text-align:center;display:block;'>加载更多</a>";
        }
        $html .='
          <script>
              function calls(){       
                if (Number($("#lock").val()) == 1){
                    var url = "index.php"+window.location.search;
					
                    $("#num").val(Number($("#num").val())+1);
                    var index = $("#num").val();
                    var load = "<img src=\"images/load.gif\" height=\"20\" style=\"margin-top:15px;\"/> 正在加载";
                        var bodyparam = {
                              page : index              
                        };
                        $("#lock").val(0);
                        $(".loading").html(load); 
                        $.post(url, bodyparam, function(s,status){
                             $("'.$callback.'").html($("'.$callback.'").html()+s);
                             $(".loading").html("点击加载更多");
                             $("#lock").val(1);
                                if (Number($("#num").val()) >= Number($("#maxnum").val())){
                                    $(".loading").css("display","none");
                                }
                        });
                    }else{
               return false;
    }
              }
          </script>
        ';
        $html .= '</div>';
    }else{
            switch ($style){
               case 1:
                   $html = '<div class="page">';
			       $html .= '<span><i>'.$cindex.'</i> / '.$tpage.'</span>';
				   if ($cindex > 1) {
						$html .= "<a {$purl} class=\"pageLeft\"></a>";
				   }else{
                        $html .= "<a href='javascript:void(0)' class=\"GrayLeft\"></a>";
				   }
				   if ($cindex < $tpage) {
						$html .= "<a {$nurl} class=\"pageRight\"></a>";			
				    }else{
                        $html .= "<a href='javascript:void(0)' class=\"GrayRight\"></a>";
				    }
                   $html .= '</div>';
				   break;
			   default:
				   $html = '<div class="dataTables_paginate paging_simple_numbers"><ul class="pagination">';
					if ($cindex > 1) {
						$html .= "<li><a {$furl} class=\"paginate_button previous\">首页</a></li>";
						$html .= "<li><a {$purl} class=\"paginate_button previous\">&laquo;上一页</a></li>";
					}
					$rastart = max(1, $cindex - $beforesize);
					$raend = min($tpage, $cindex + $aftersize);
					if ($raend - $rastart < $beforesize + $aftersize) {
						$raend = min($tpage, $rastart + $beforesize + $aftersize);
						$rastart = max(1, $raend - $beforesize - $aftersize);
					}
					for ($i = $rastart; $i <= $raend; $i ++) {
						$_GP['page'] = $i;
						$aurl = 'href="index.php?' . http_build_query($_GP) . '"';
						$html .= ($i == $cindex ? '<li class="paginate_button active"><a href="javascript:;">' . $i . '</a></li>' : "<li><a {$aurl}>" . $i . '</a></li>');
				   }
				   if ($cindex < $tpage) {
						$html .= "<li><a {$nurl} class=\"paginate_button next\">下一页&raquo;</a></li>";
						$html .= "<li><a {$lurl} class=\"paginate_button next\">尾页</a></li>";
				   }
				   $html .= '</ul></div>';
				   break;
			}	
    }
    return $html;
}