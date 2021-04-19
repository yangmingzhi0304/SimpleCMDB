<?php

include_once(dirname(__file__).'/title.php');

function split_page($total,$number_each_page=100)
{
    $max_pages  =  ceil($total/$number_each_page);
    $pageNo     =  min($max_pages,max(1,isset($_GET['pageNo'])?$_GET['pageNo']:1));
    $pre_no     =  max($pageNo-1,1);
    $post_no    =  min($pageNo+1,$max_pages);
    $pre_color  =  $pageNo ==          1 ? '#D2D0F8' : '#0088FF';
    $post_color =  $pageNo == $max_pages ? '#D2D0F8' : '#0088FF';
    $buttons    =  strpos($_SERVER["HTTP_USER_AGENT"],"Firefox") ? array("|<< ","<-","->"," >>|") : array("9","7","8",":");
    $uri_array  =  parse_url($_SERVER['REQUEST_URI']);
    $uri_array  =  explode('&',$uri_array['query']);
    foreach($uri_array as $index=>$value)
    {
        if(preg_match("/^pageNo=/",$value))unset($uri_array[$index]);
    }
    $uri = $_SERVER['PHP_SELF'] . "?" . implode('&',$uri_array);
    $page_link  = "<font color=#0088FF>翻页:";
    $page_link .= "&nbsp;<a href=$uri&pageNo=1          style='color:#0088FF'    ><font title='首页'   face=webdings>$buttons[0]</font></a>";
    $page_link .= "&nbsp;<a href=$uri&pageNo=$pre_no    style='color:$pre_color' ><font title='上一页' face=webdings>$buttons[1]</font></a>";
    $page_link .= "&nbsp;<a href=$uri&pageNo=$post_no   style='color:$post_color'><font title='下一页' face=webdings>$buttons[2]</font></a>";
    $page_link .= "&nbsp;<a href=$uri&pageNo=$max_pages style='color:#0088FF'    ><font title='尾页'   face=webdings>$buttons[3]</font></a>";

        if($max_pages<10)        {  $start_loop_page = 1;            $stop_loop_page  = $max_pages; }
    elseif($pageNo<5)            {  $start_loop_page = 1;            $stop_loop_page  = 10;         }
    elseif($pageNo>$max_pages-5) {  $start_loop_page = $max_pages-9; $stop_loop_page  = $max_pages; }
    else                         {  $start_loop_page = $pageNo-4;    $stop_loop_page  = $pageNo+5;  }

    $page_link .= "&nbsp;页面跳转:";
    for($i=$start_loop_page;$i<=$stop_loop_page;$i++)
    {
        $page_link .= "&nbsp;<a style='color:" . ($i == $pageNo ? 'red' : '#0088FF') . "' href=$uri&pageNo=$i>" . str_pad($i,2,"0",STR_PAD_LEFT) . "</a>";
    }

    $page_link .= "&nbsp;第<select name='pageNo' style='height=14px' onchange=\"location='$uri&pageNo='+this.value;\">";
    for($i=1;$i<=$max_pages;$i++)
    { 
        $page_link .= "<option value='$i' " . ($i == $pageNo ? "selected" : "") . ">" . str_pad($i,2,"0",STR_PAD_LEFT) . "</option>"; 
    } 
    $page_link .= "</select>页/共${max_pages}页(${total}条记录)(每页显示${number_each_page}条)";
    $start_id   = $number_each_page*($pageNo-1);
    print $page_link;    return $start_id;
}


$total      = $db->oplogs->count();
$auto_id    = split_page($total);
if($auto_id<0)die("<br><br><font color=red>暂无日志</font>");
$results    = $db->oplogs->find()->sort(array('修改时间'=>-1))->skip($auto_id)->limit(100);
$titles     = array("执行人","资产类型","资产编号","修改前","修改后","修改方式","修改时间");

print "<table border=1 width=100%>";
print_line(array_merge(array("序号"),$titles),'left');
foreach($results as $result)
{
    $asset_id   = $result['资产编号'];
    $asset_type = $result['资产类型'];
    $color      = explode(' ',$result['修改时间'])[0] == date('Y-m-d') ? '#FF8800' : '';
    if($asset_id=='属性设置')
    {
        $result['资产编号'] = "<a href=/set_properties.php?t=$asset_type>属性设置</a>";
    } else {
        $result['资产编号'] = "<a href='/set_assets.php?t=$asset_type&i=$asset_id' style='color:$color'>$asset_id</a>";
    }
    $tds = array();
    foreach($titles as $title)
    {
        array_push($tds,$result[$title]);
    }
    print_tr(++$auto_id,'3',"style='text-align:left;color:$color'");
    print_tds($tds);
    #print "<td>" . $tds[0]   . "</td>";
    #print "<td>" . $tds[1] . "</td>";
    #print "<td>" . $tds[2] . "</td>";
    #print "<td>" . htmlspecialchars($tds[3]) . "</td>";
    #print "<td>" . htmlspecialchars($tds[4]) . "</td>";
    #print "<td>" . $tds[5] . "</td>";
    #print "<td>" . $tds[6] . "</td>";
    #print "</tr>";
}
print "</table>";
