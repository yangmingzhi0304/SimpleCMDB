<?php

$_POST=@json_decode($HTTP_RAW_POST_DATA,true);

if($_POST!=array())
{
    include_once(dirname(__file__).'/functions.php');
    set_asset($modify_method='API自动修改');
    die;
}

if($_GET!=array())
{
    include_once(dirname(__file__).'/functions.php');
    $results = array();
    foreach($db->assets->find($_GET) as $result)
    {
        unset($result['_id']);
        array_push($results,$result);
    }
    print json_encode($results,JSON_UNESCAPED_UNICODE);
    die;
}

include_once(dirname(__file__).'/title.php');

$auto_id   = 0;
$post_addr = "http://" . $_SERVER['HTTP_HOST'] . $PHP_SELF;

print "怎样上传数据：<br>";
print "<br>&nbsp;&nbsp;" . str_pad(++$auto_id,2,'0',STR_PAD_LEFT) . ". 请求地址：$post_addr";
print "<br>&nbsp;&nbsp;" . str_pad(++$auto_id,2,'0',STR_PAD_LEFT) . ". 请求方法：POST";
print "<br>&nbsp;&nbsp;" . str_pad(++$auto_id,2,'0',STR_PAD_LEFT) . ". 请求参数：【资产类型】和【资产编号】必须有，其他字段传什么就存什么，不限制字段。";
print "<br>&nbsp;&nbsp;" . str_pad(++$auto_id,2,'0',STR_PAD_LEFT) . ". 参数说明：【资产类型】和【资产编号】用于唯一定位资产数据，其他字段都原样保存。";
print "<br>&nbsp;&nbsp;" . str_pad(++$auto_id,2,'0',STR_PAD_LEFT) . ". 字段说明：只有在【属性设置】中设置过的字段才会在页面内显示，其他字段只保存不显示。";
print "<br>&nbsp;&nbsp;" . str_pad(++$auto_id,2,'0',STR_PAD_LEFT) . ". 其他限制：没有限制，你可以同步任何你想保存的数据。";

print "<br><br>语法示例：<br>";
print "<br>&nbsp;&nbsp;&nbsp;Bash版:<br>";
print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font color=#000000>Shell]# curl ";
print preg_replace("@//@","//用户名:密码@",$post_addr) . " -X POST -d 资产类型=服务器 -d 资产编号=ID000001 -d hello=123 -d world=456</font>";

print "<br>&nbsp;&nbsp;&nbsp;Python版:<font color=#000000>";
print "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;>>> import requests";
print "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;>>> url = '$post_addr'";
print "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;>>> params = {";
print "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;>>> &nbsp;&nbsp;&nbsp;&nbsp;'资产类型'&nbsp;&nbsp;:&nbsp;&nbsp;'服务器',";
print "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;>>> &nbsp;&nbsp;&nbsp;&nbsp;'资产编号'&nbsp;&nbsp;:&nbsp;&nbsp;'ID000001',";
print "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;>>> &nbsp;&nbsp;&nbsp;&nbsp;'hello'&nbsp;&nbsp;:&nbsp;&nbsp;'123',";
print "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;>>> &nbsp;&nbsp;&nbsp;&nbsp;'world'&nbsp;&nbsp;:&nbsp;&nbsp;'456',";
print "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;>>> &nbsp;}";
print "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;>>> print requests.post(url,json=params,auth=('用户名','你的密码')).json()";
print "</font>";

print "<br><br>以上示例最终会新增这样一条数据：";
print "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{'资产类型':'服务器','资产编号':'ID000001','hello':'123','world':'456'}";

print "<br><br>字段覆盖说明：";
print "<br>&nbsp;&nbsp;&nbsp;你未填写但已存在的字段：保持现状，不会被删除，只有你填写的字段才会被修改。";
print "<br>&nbsp;&nbsp;&nbsp;你已填写的字段：存在则覆盖，不存在会新增。";

$auto_id = 0;
print "<br><br><br><br>怎样读取数据：";
print "<br>&nbsp;&nbsp;" . str_pad(++$auto_id,2,'0',STR_PAD_LEFT) . ". 请求地址：$post_addr";
print "<br>&nbsp;&nbsp;" . str_pad(++$auto_id,2,'0',STR_PAD_LEFT) . ". 请求方法：GET";
print "<br>&nbsp;&nbsp;你可以指定任何查询条件，只要有符合的，就会被查询出来(返回json格式的数据)";

$get_url = preg_replace("@//@","//用户名:密码@",$post_addr) . "?资产类型=服务器&资产编号=ID000001&hello=123&world=456";

print "<br><br>语法示例：<br>";
print "<br>&nbsp;&nbsp;&nbsp;Bash版:<br>";
print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font color=#000000>Shell]# curl $get_url</font>";

print "<br>&nbsp;&nbsp;&nbsp;Python版:<font color=#000000>";
print "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;>>> import requests";
print "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;>>> url = '$get_url'";
print "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;>>> print requests.get(url,auth=('用户名','你的密码')).json()";
print "</font>";




















