<?php

include_once(dirname(__file__).'/configs.php');

$mongo  = new MongoClient("mongodb://$dbuser:$dbpass@$dbhost:$dbport/$dbname");
$db     = $mongo->$dbname;

$get_t  = @trim($_GET['t']);                                     # 资产类型 type
$get_i  = @trim($_GET['i']);                                     # 资产编号 id
$get_o  = @trim($_GET['o']); if(empty($get_o))$get_o=$cmdb_time; # order by [whom]
$get_m  = @trim($_GET['m']);                                     # order method
$get_m  = @in_array($get_m,[1,-1]) ? (int)$get_m : -1; 

$PHP_SELF    = @$_SERVER['PHP_SELF'];
$asset_types = array(); foreach($db->properties->find() as $row)array_push($asset_types,$row['type']); asort($asset_types);


function auto_return($second,$return,$message=null)
{
    print "<script>
    function count(number)
    {
        if(number<10)show_number='0'+number;else show_number=number;
        document.getElementById('second').innerHTML=show_number+\"秒后自动返回，或者，点击&nbsp;<a href='$return' style='color:red'><b>这里</b></a>&nbsp;立即返回。\";
        if(number>0)setTimeout('count('+(number-1)+')',1000);
    }
    </script>";
    if($message!=null)
    {
        print "<br>$message<br><br>";
        print "<body onload='count($second);'><div id='second'>${second}</div>";
    }
    print "<meta http-equiv=\"refresh\" content=\"$second;url=$return\">";
    exit(0);
}

function sprint_tr($auto_id,$length=1,$properties="style='text-align:left'",$auto_id_pre='')
{
    $id  = $auto_id_pre . $auto_id;
    $tr  = "<tr id='$id' $properties";
    $tr .= " onmouseover='document.getElementById(\"$id\").style.backgroundColor=\"#D2D0F8\";'";
    $tr .= " onmouseout ='document.getElementById(\"$id\").style.backgroundColor=\"\";'>";
    $tr .= "<td>" . str_pad($auto_id,$length,'0',STR_PAD_LEFT) . ".</td>\n";
    return $tr;
}

function print_tr($auto_id,$length=1,$properties="style='text-align:left'",$auto_id_pre='')
{
    print sprint_tr($auto_id,$length,$properties,$auto_id_pre);
}

function sprint_td($td,$align=null)
{
    return "<td" . ( $align == null ? '' : " align=$align" ) . ">$td</td>";
}

function print_td($td,$align=null)
{
    print sprint_td($td,$align);
}

function sprint_tds($tds,$close_tr=true)
{
    return "<td>" . implode("</td><td>",$tds) . "</td>" . ( $close_tr ? '</tr>' : '' );
}

function print_tds($tds,$close_tr=true)
{
    print sprint_tds($tds,$close_tr);
}

function sprint_line($tds,$align="center",$color=null)
{
    $color = ($color == null || $color == '') ? '' : "color:$color;";
    return "<tr style='text-align:$align;$color'>" . sprint_tds($tds);
}

function print_line($tds,$align="center",$color=null)
{
    print sprint_line($tds,$align,$color);
}

function set_asset($modify_method='API自动修改')
{
    global $db,$username,$cmdb_time,$PHP_SELF;

    $error_msg  = null;
    $modify_way = @$_POST['modify_way'];
    $asset_type = @$_POST['资产类型'];
    $asset_id   = @$_POST['资产编号'];

    if($username=='read')       $error_msg = "只读帐号，无修改权限。";
    if(empty(trim($asset_type)))$error_msg = "资产类型不能为空。";
    if(empty(trim($asset_id)))  $error_msg = "资产编号不能为空。";

    if($error_msg!=null)
    {
        if($modify_method=='API自动修改')die(json_encode(array("RetCode"=>-1,"Response"=>$error_msg))."\n");
        else auto_return(10,$PHP_SELF,"操作<font color=red>失败</font>，$error_msg");
        die;
    }

    unset($_POST['modify_way']);

    $condition  = array("资产类型"=>$asset_type,"资产编号"=>$asset_id);

    $old = $db->assets->find($condition)->getNext();

    if($modify_way=='delete')
    {
        $db->assets->remove($condition);
    } else {
        $_POST[$cmdb_time] = date('Y-m-d H:i:s');
        $db->assets->update($condition,array('$set'=>$_POST),array("upsert"=>true));
    }

    $new = $db->assets->find($condition)->getNext();

    unset($old[$cmdb_time]);
    unset($new[$cmdb_time]);

    if($old==null)$old=array(); foreach(array("_id","资产类型","资产编号") as $index)unset($old[$index]);
    if($new==null)$new=array(); foreach(array("_id","资产类型","资产编号") as $index)unset($new[$index]);

    $same  = array_intersect($new,$old);

    $old_d = array_diff($old,$same); $old_array = array(); foreach($old_d as $key=>$value)array_push($old_array,"$key : $value"); sort($old_array);
    $new_d = array_diff($new,$same); $new_array = array(); foreach($new_d as $key=>$value)array_push($new_array,"$key : $value"); sort($new_array);

    $old_value = $old == null ? '纯新增，无旧值' : implode("<br>",$old_array);
    $new_value = $new == null ? '纯删除，无新值' : implode("<br>",$new_array);

    if(($new!=null||$old!=null))oplogs($old_value,$new_value,$asset_type,$asset_id,$modify_method);

    if($modify_method=='API自动修改')print json_encode(array("RetCode"=>0,"Response"=>$new),JSON_UNESCAPED_UNICODE) . "\n";
    else auto_return(1,"/list_assets.php?t=$asset_type","修改完毕");
}

function oplogs($old,$new,$ResourceType='未填写',$ResourceID='未填写',$modify_method='API自动修改')
{
    global $username,$db;
    if($new!=$old)
    {
        $db->oplogs->insert(array(
            '执行人'    =>  $username,
            '资产类型'  =>  $ResourceType,
            '资产编号'  =>  $ResourceID,
            '修改前'    =>  $old,
            '修改后'    =>  $new,
            '修改方式'  =>  $modify_method,
            '修改时间'  =>  date('Y-m-d H:i:s'),
        ));
    }
}

