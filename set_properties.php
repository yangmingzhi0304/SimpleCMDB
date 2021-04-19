<?php

include_once(dirname(__file__).'/title.php');

function format_properties($properties)
{
    foreach($properties as $index=>$property)
    {
        if(in_array(trim($property),array('资产编号','')))
        {
            unset($properties[$index]);
        } else {
            $properties[$index] = trim($property);
        }
    }
    $properties = array_unique($properties);
    return array_merge(array("资产编号"),$properties);
}

if(isset($_GET['delete']))
{
    if(!in_array($get_t,$asset_types)&&$get_t!='新增')auto_return(1,$PHP_SELF,'类型错误');
    $db->properties->remove(array("type"=>$get_t));
    auto_return(1,$PHP_SELF,"删除完毕");
    die;
}

if(!empty($get_t))
{
    if(!in_array($get_t,$asset_types)&&$get_t!='新增')auto_return(1,$PHP_SELF,'类型错误');
    if($_POST!=array())
    {
        $post_t = @$_POST['type'];
        if(!empty($post_t))$get_t=$post_t;
        $properties = explode("\n",$_POST['properties']);
        $properties = format_properties($properties);
        $old   = $db->properties->find(array("type"=>$get_t))->getNext(); $old = $old['properties'];
        $db->properties->update(array("type"=>$get_t),array('$set'=>array('type'=>$get_t,'properties'=>$properties)),array('upsert'=>true));
        $new   = $db->properties->find(array("type"=>$get_t))->getNext(); $new = $new['properties'];
        $same  = array_intersect($old,$new);
        $old   = implode("<br>",array_diff($old,$same));
        $new   = implode("<br>",array_diff($new,$same));
        oplogs($old,$new,$get_t,"属性设置",'手动修改');
        auto_return(1,"$PHP_SELF?t=$get_t","修改完毕");
    }
    $row = $db->properties->find(array("type"=>$get_t))->getNext();
    $properties = $row['properties'];
    foreach($properties as $index=>$property)
    {
        if(trim($property)=='资产编号')
        {
            unset($properties[$index]);
        }
    }
    $properties     = array_merge(array("资产编号"),$properties);
    $old_values     = implode("\n",$properties);
    $delete_link    = "<a style='color:#FF8800' href='$PHP_SELF?t=$get_t&delete'>点我删除本类型</a>";
    $all_type_link  = "<a href=$PHP_SELF>点我查看所有类型</a>";
    $this_type_link = "<a href=/list_assets.php?t=$get_t style='color:#FF8800'>点我查看本类型数据</a>";
    print "<form action='$PHP_SELF?t=$get_t' method=POST><table border=0>";
    if($get_t=='新增'){
           $delete_link = '';
           $old_values  = '资产编号';
           print_line(array("数据类型：","<input name=type style='width:400px' required>"),'left');
    } else print_line(array("数据类型：","${get_t}&nbsp;$this_type_link&nbsp;&nbsp;$all_type_link"),"left");
    $desc = array(
        "不必填写【资产类型】字段，写了也会被忽略，当前类型为[<font color=#FF8800>$get_t</font>](不可修改)",
        "不必填写【资产编号】字段，因为会自动添加，若填了会去重，不会存在两个的。",
        "填写的字段不会自动排序，你按什么顺序填，使用时就是什么顺序(资产编号会始终置顶)",
        "若删除某字段，该字段的数据不会被删除，只是会隐藏起来，你看不到了而已。",
        "若重新添加某个曾被删除的字段，则其曾经设置的值会重新显示(改字段不影响数据)",
    );
    print_line(array("修改说明：",implode("<br>",$desc)),'left');
    print_line(array("字段设置：","<textarea name=properties cols=50 rows=50 placeholder='一行一个'>$old_values\n</textarea>"),'left');
    print_line(array("修改说明：","请填写中英文和数字的组合，请不要使用空白字符，特殊字符等等。"),"left");
    print_line(array("字段说明：","[资产编号]是必须存在的字段，若不存在、点击提交后工具会自动添加。"),'left');
    print_line(array("提交修改：","<input type=submit style='width:500px'>&nbsp;&nbsp;&nbsp;$delete_link"),'left');
    die("</table></form>");
}

print "为谁设置：<select style='width:200px' name=t onchange=\"location='$PHP_SELF?t='+this.value\">";
print "<option>请选择数据类型</option><option value=新增>".str_pad(0,strlen(count($asset_types)),'0',STR_PAD_LEFT).". 选我可以新增一个资产类型</option>";
$auto_id = 0;
foreach($asset_types as $type)
{
    $selected = $type == $get_t ? 'selected' : '';
    print "<option value='$type' $selected>" . str_pad(++$auto_id,strlen(count($asset_types)),'0',STR_PAD_LEFT) . ". $type</option>";
}
print "</select>&nbsp;&nbsp;<a href=$PHP_SELF?t=新增>点我新增</a><br><br>";

$auto_id = 0;
print "<table border=1>";
print_line(array("序号","资产类型","已设置的属性","按钮"),'left');
foreach($asset_types as $type)
{
    $row = $db->properties->find(array("type"=>$type))->getNext();
    $properties = $row['properties'];
    foreach($properties as $index=>$property)if($property=='资产编号')unset($properties[$index]);
    sort($properties,SORT_NUMERIC); $properties = array_merge(array('资产编号'),$properties);
    print_tr(++$auto_id,2,$style="style='text-align:left'");
    print_tds(array($type,implode('<br>',$properties),"<a href='$PHP_SELF?t=$type'>修改</a>"));
}
print "</table>";








    
