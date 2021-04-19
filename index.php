<?php

include_once(dirname(__file__).'/title.php');

$auto_id = 0;

print "一些说明：";
print "<p>&nbsp;&nbsp;" . str_pad(++$auto_id,2,'0',STR_PAD_LEFT) . ". 可以添加任意类型的数字资产，如虚拟机列表，供应商管理等等，资产类型无限制。</p>";
print "<p>&nbsp;&nbsp;" . str_pad(++$auto_id,2,'0',STR_PAD_LEFT) . ". 字段可随意自定义，修改字段不影响已设置的数据，只影响显示结果。</p>";
print "<p>&nbsp;&nbsp;" . str_pad(++$auto_id,2,'0',STR_PAD_LEFT) . ". 资产类型总数无上线，只要浏览器不卡死，加1亿种类型都可以。</p>";
print "<p>&nbsp;&nbsp;" . str_pad(++$auto_id,2,'0',STR_PAD_LEFT) . ". 操作日志若无需长期保留，可自行配置定时任务删除旧数据。例如(Python2语法)删除7天以上的日志的方法：</p>";
print "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;tb.remove({'修改时间':{'\$lt':time.strftime(\"%F %T\",time.localtime(time.time()-86400*7))}})</p>";
print "<p>&nbsp;&nbsp;" . str_pad(++$auto_id,2,'0',STR_PAD_LEFT) . ". [资产类型]+[资产编号]可唯一定位某资产，同类型的资产的编号必须唯一，不同类型可以重复。</p>";

