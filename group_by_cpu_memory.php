<?php

include_once(dirname(__file__).'/title.php');

print "请选择统计哪种类型的资产数据：<select onchange=\"location='$PHP_SELF?t='+this.value\">";
print "<option selected>请选择</option>";
$auto_id = 0; if(empty($get_t))$get_t='虚拟机';
foreach($asset_types as $type)
{
    $selected = $type == $get_t ? 'selected' : '';
    print "<option value='$type' $selected>" . str_pad(++$auto_id,strlen(count($asset_types)),'0',STR_PAD_LEFT) . ". $type</option>";
}
print "</select><br><br>";

$cpus       = array();
$mems       = array();
$results    = $db->assets->find(array('资产类型'=>$get_t),array("CPU"=>true,"内存"=>true));

foreach($results as $result)
{
    $cpu = $result['CPU'];  array_push($cpus,$cpu); $cpus = array_unique($cpus); sort($cpus,SORT_NUMERIC);
    $mem = $result['内存']; array_push($mems,$mem); $mems = array_unique($mems); sort($mems,SORT_NUMERIC);
    if(!empty($cpu)&&!empty($mem))
    {
        $index = "${cpu}核${mem}G";
        $datas[$index]+=1;
    }
}

if($datas==array())die("[$get_t]类型的数据不包含[CPU]或[内存]字段，无法进行统计。");

print "按配置矩阵展示：<table border=1>";
print "<tr><td>CPU\内存</td>"; foreach($mems as $mem)if(!empty($mem))print "<td>${mem}G</td>";print "</tr>";
foreach($cpus as $cpu)
{
    if(empty($cpu))continue;
    print "<tr><td>${cpu}核</td>";
    foreach($mems as $mem)
    {
        if(empty($mem))continue;
        print "<td align=right><a href=/templated_query.php?资产类型=$get_t&CPU=$cpu&内存=$mem>" . $datas["${cpu}核${mem}G"] . "</a></td>";
    }
    print "</tr>";
}
print "</table>";

print "<table border=0><tr><td>";

arsort($datas); $auto_id = 0;
print "<br>按个数排序展示：<table border=1>";
print_line(array("序号","配置","个数"),'right');
foreach($datas as $config=>$number)
{
    print_tr(++$auto_id,2,"style='text-align:right'");
    list($cpu,$mem) = explode(' ',trim(preg_replace('/核|G/',' ',$config)));
    print_tds(array($config,"<a href=/templated_query.php?CPU=$cpu&内存=$mem>" . $datas[$config] . "</a>"));
}
print "</table>";

print "</td><td>";

krsort($datas,SORT_NUMERIC); $auto_id = 0;
print "<br>按配置排序展示：<table border=1>";
print_line(array("序号","配置","个数"),'right');
foreach($datas as $config=>$number)
{
    print_tr(++$auto_id,2,"style='text-align:right'");
    list($cpu,$mem) = explode(' ',trim(preg_replace('/核|G/',' ',$config)));
    print_tds(array($config,"<a href=/templated_query.php?CPU=$cpu&内存=$mem>" . $datas[$config] . "</a>"));
}
print "</table>";

print "</td></tr></table>";


