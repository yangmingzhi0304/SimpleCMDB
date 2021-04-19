<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
    <meta name="robots" content="noindex, nofollow" />
    <link media=all href="/nav.css" type=text/css rel=stylesheet>
    <style type='text/css'> body { color:#0088FF; } a { color:#0088FF; }</style>
    <title>SimpleCMDB/轻量级资产管理系统</title>
    <script type="text/javascript">
        sfHover = function() {
            var sfEls = document.getElementById("navMenu").getElementsByTagName("LI");
            for (var i=0; i<sfEls.length; i++) {
                sfEls[i].onmouseover=function() {
                    this.className+=" sfhover";
                }
                sfEls[i].onmouseout=function() {
                    this.className=this.className.replace(new RegExp(" sfhover\\b"), "");
                }
            }
        }
        if (window.attachEvent) window.attachEvent("onload", sfHover);
    </script>
</head>

<body>

<div id="navMenu">
<UL class="menu1">

<li class="onelink"><a href='/'>首页</a></li>

<?php

    include_once(dirname(__file__).'/functions.php');

    function show_items($items,$title,$default_url=null)
    {
        if($default_url!=null)
        {
            if(preg_match("@^http://@",$default_url))$default_url="http://$default_url";
            $default_url = "href='$default_url'";
        }
        print "<li class='navthis1'><a rel='dropmenu8' $default_url>$title</a><ul>";
        foreach($items as $url=>$desc)
        {
            if(preg_match("/^blank/",$desc)){ $blank="target=_blank"; $desc = preg_replace("/^blank/","",$desc).'(新窗口)'; } else $blank='';
            print "<li><a href='$url' $blank>$desc</a></li>";
        }
        print "</ul></li>\n";
    }

    print "<li class='onelink'><a href='/set_assets.php'>添加数据</a></li>\n";

    $type_titles = array('/set_properties.php?t=新增'=>'新增基础数据类型');
    foreach($asset_types as $type)
    {
        $type_titles["/list_assets.php?t=$type"]="基础数据：$type - " . $db->assets->find(array("资产类型"=>$type))->count() . '条数据';
    }
    show_items($type_titles,"基础数据");

    print "<li class='onelink'><a href='/group_by_cpu_memory.php'>分组统计</a></li>\n";
    print "<li class='onelink'><a href='/api.php'>API接口</a></li>\n";
    print "<li class='onelink'><a href='/modify_logs.php'>操作日志</a></li>\n";


?>

</div></body></html><br>
