<?php

include_once(dirname(__file__).'/title.php');

if(!in_array($get_t,$asset_types))die('[资产类型]有误，未设置该类型资产的属性。');

$row        = $db->properties->find(array("type"=>$get_t))->getNext();
$properties = $row['properties'];
$results    = $db->assets->find(array("资产类型"=>$get_t))->sort(array("$get_o"=>$get_m));
$length     = strlen($results->count(true));

print "<a href=/set_assets.php?t=$get_t style='color:#FF8800'>点我新增一条记录</a>";
print "&nbsp;(拖动标题栏可修改列宽)&nbsp;<a href=/set_properties.php?t=$get_t style='color:#FF8800'>点我修改【${get_t}】分类的字段</a>";
print "&nbsp;&nbsp;<font color=red size=6 id='loading'>数据加载中，请稍候</font><br><br>";

?>

<script type='text/javascript'>
function ctrl_show(column_number)
{
    var table = document.getElementById("myasset");
    var trs = table.rows;
    for(var i = 0, len = trs.length; i < len; i++)
    {
        cell  = trs[i].cells[column_number]
        state = cell.style.display
        if(state=='none')cell.style.display='';
        if(state=='')    cell.style.display='none';
    }
}
</script>


<?php

print "<table border=0>";
$count = 0; $each_line = 11;
foreach($properties as $property)
{
    $count++;
    if($count%$each_line==1)print "<tr>";
    print "<td><input id='checkbox$property' type=checkbox checked onChange='ctrl_show(\"$count\");''>$property;</td>";
    if($count%$each_line==0)print "</tr>";
}
if($count%$each_line!=0)print "</tr>";
print "</table>";


print "<table id='myasset' border=1><tr style='text-align:right;background-color:#7FFFD4;'><td>序号</td>";
foreach($properties as $property)
{
    if($property=='资产类型')continue;
    $color = $property == $get_o ? '#FF8800' : 'blue';
    print "<td><a href='$PHP_SELF?t=$get_t&o=$property&m=".-1*$get_m."' style='color:$color'>$property</a></td>";
}
print "<td>按钮</td></tr>";

foreach($results as $result)
{
    unset($result['资产类型']);
    print_tr(++$auto_id,$length);
    $title = "信息预览：";
    foreach($properties as $property)
    {
        $title .= "\n$property : " . preg_replace("/\<[^>]*>/","",$result[$property]);
    }

    foreach($properties as $property)
    {
        if($property=='资产类型')continue;
        $value = preg_replace("/\n/","<br>",$result[$property]);
        if(preg_match("@^http(s)?://@",$value))$value = "<a href='$value' target=_blank style='color:#0088FF'>$value</a>";
        if($property=='资产编号')$value = "<a href=/set_assets.php?t=$get_t&i=$value>$value</a>";
        print "<td title=\"$title\">$value</td>";
    }
    print "<td><a href=/set_assets.php?t=$get_t&i=".$result['资产编号'].">修改</a></td>";
    print "</tr>\n";
}

print "</table>";

print "<script type=\"text/javascript\">\n";
print "document.getElementById('loading').color='#0088FF';\n";
print "document.getElementById('loading').size='';\n";
print "document.getElementById('loading').innerHTML='加载完毕，共<font color=red>$auto_id</font>条记录。';\n";
print "</script>";

?>

<script type="text/javascript">
    var tTD;
    var table = document.getElementById("myasset");
    for (j = 0; j < table.rows[0].cells.length; j++) {
        table.rows[0].cells[j].onmousedown = function () {
            tTD = this;
            if (event.offsetX > tTD.offsetWidth - 10) {
                tTD.mouseDown = true;
                tTD.oldX = event.x;
                tTD.oldWidth = tTD.offsetWidth;
            }
        };
        table.rows[0].cells[j].onmouseup = function () {
            if (tTD == undefined) tTD = this;
            tTD.mouseDown = false;
            tTD.style.cursor = 'default';
        };
        table.rows[0].cells[j].onmousemove = function () {
            if (event.offsetX > this.offsetWidth - 10)
                this.style.cursor = 'col-resize';
            else
                this.style.cursor = 'default';
            if (tTD == undefined) tTD = this;
            if (tTD.mouseDown != null && tTD.mouseDown == true) {
                tTD.style.cursor = 'default';
                if (tTD.oldWidth + (event.x - tTD.oldX) > 0)
                    tTD.width = tTD.oldWidth + (event.x - tTD.oldX);
                tTD.style.width = tTD.width;
                tTD.style.cursor = 'col-resize';
                table = tTD;
                while (table.tagName != 'TABLE') table = table.parentElement;
                for (j = 0; j < table.rows.length; j++) {
                    table.rows[j].cells[tTD.cellIndex].width = tTD.width;
                }
            }
        };
    }
</script>
