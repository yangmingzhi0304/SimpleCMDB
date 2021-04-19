<?php

include_once(dirname(__file__).'/title.php');

if($_POST!=array())
{
    set_asset($modify_method='手动修改');
    die;
}

if(empty($get_t))
{
    print "请选择添加哪种类型的资产数据：<select onchange=\"location='$PHP_SELF?t='+this.value\">";
    print "<option selected>请选择</option>";
    print "<option value='新增'>".str_pad('0',strlen(count($asset_types)),'0',STR_PAD_LEFT).". 新增一个数据类型</option>";
    $auto_id = 0;
    foreach($asset_types as $type)
    {
        $selected = $type == $get_t ? 'selected' : '';
        print "<option value='$type' $selected>" . str_pad(++$auto_id,strlen(count($asset_types)),'0',STR_PAD_LEFT) . ". $type</option>";
    }
    print "</select>";

    if($asset_types==[])die("<br><br>暂无数据类型，<a style='color:#FF8800' href=/set_properties.php?t=新增>点我新增</a>");

    print "<br><br><table border=1><tr style='color:#FF8800'>";
    print_tds(array("序号","资产类型","新增数据","修改属性","已有数据量"));
    $auto_id = 0;
    foreach($asset_types as $type)
    {
        print_tr(++$auto_id,strlen(count($asset_types)));
        print "<td><a href=/list_assets.php?t=$type>$type</a></td>";
        print "<td><a href='$PHP_SELF?t=$type'>点我新增一条本类型数据</a></td>";
        print "<td><a href='/set_properties.php?t=$type'>点我修改本类型字段设置</a></td>";
        print "<td align=right><a href=/list_assets.php?t=$type>".$db->assets->find(array("资产类型"=>$type))->count().'条</a>'."</td></tr>";
    }
    die("</table>");
}

if($get_t=='新增')header("location:/set_properties.php?t=新增");

if(!in_array($get_t,$asset_types))die('[资产类型]有误，未设置该类型资产的属性。');

$row        = $db->properties->find(array("type"=>$get_t))->getNext();
$properties = $row['properties']; asort($properties); foreach($properties as $key=>$value)if($value=='资产编号')unset($properties[$key]);
$properties = array_merge(array('资产编号'),$properties);
$row        = $db->assets->find(array("资产类型"=>$get_t,"资产编号"=>$get_i))->getNext();

print "建议使用的登记规范：<br>";
print "&nbsp;&nbsp;数值：数值型的属性请不要添加单位，以方便代码进行自动计算。支持登记浮点数。<br>";
print "&nbsp;&nbsp;日期：请固定使用10位日期，例如：" . date('Y-m-d') . "<br>";
print "&nbsp;&nbsp;价格：单位固定为[元]，例如：1万元请登记为10000<br>";
print "&nbsp;&nbsp;带宽：单位固定为[Mb]，例如：128KB登记成1，1MB登记成8等等(1MB=8Mb，请注意单位)<br>";
print "&nbsp;&nbsp;内存：单位统一使用GB，例如：将1T登记成1024，将512M登记成0.5等等<br>";
print "&nbsp;&nbsp;硬盘：单位统一使用GB，例如：将1T登记成1024，将512M登记成0.5等等<br>";
print "每一条数据都是一项数字资产，资产编号不能为空，【资产类型+资产编号】可唯一定位该资源。";
print "<br><br><form action='$PHP_SELF?t=$get_t' method=POST><table border=1>";
print "<tr><td>资产类型：</td><td>$get_t &nbsp;&nbsp;&nbsp;<a style='color:#FF8800' href=/set_properties.php?t=$get_t>点我修改【${get_t}】的字段设置</a></td></tr>";
foreach($properties as $property)
{
    $property  = preg_replace("/.* /","",$property);
    $old_value = $row[$property];
    if($property=='资产编号'&&!empty($get_i))
    {
        print "<input type=hidden name='资产编号' value='$old_value'>"; # 修改
        print_line(array("${property}：","$old_value &nbsp;<font color=#D2D0F8>资产编号不可变，如需修改请以[删除后重建]的方式实现</font>"),"left");
    } elseif(preg_match("/日期/",$property)) {
        print_line(array("${property}：","<input name='$property' style='width:200px' type=date value='$old_value'>&nbsp;请使用10位日期格式，例如：".date('Y-m-d')),'left');
    } elseif(preg_match("/总额|金额|价格|价钱/",$property)) {
        print_line(array("${property}：","<input name='$property' style='width:200px' type=number step=0.01 value='$old_value'>&nbsp;单位：元(支持小数点后两位)"),'left');
    } elseif(preg_match("/CPU|内存|带宽|流量|使用率/i",$property)) {
        print_line(array("${property}：","<input name='$property' style='width:200px' type=number step=0.01 value='$old_value'>&nbsp;数值型数据，支持小数点后两位"),'left');
    } else {
        print_line(array("${property}：","<textarea name='$property' rows=3 cols=100>$old_value</textarea>"),'left');
    }
}
print "<input type=hidden name='资产类型' value='$get_t'>\n";
print "<tr><td align=left>修改方式：</td><td>";
print "<input type=radio name=modify_way value='modify' checked>修改为新数据；";
print "<input type=radio name=modify_way value='delete'>删除本条数据；";
print "</td></tr>\n";
print "<tr><td colspan=2>&nbsp;</td></tr>\n";
print_line(array("确认操作：","<input type=submit value='点击提交' style='width:100%'>"),'left');
print "</table></form>";

