<?php

define('SITE_PATH', dirname(__FILE__).'/');
date_default_timezone_set('Asia/Shanghai');

require_once 'CoolQ.class.php';
require_once 'CoolQ.config.php';

$group = '677269300';

$url = 'http://3g.dxy.cn/newh5/view/pneumonia';
$contents = file_get_contents($url);



/*  最新消息 开始  */
preg_match('/window\.getTimelineService \= (\[.*?\])\}catch\(e\)/',$contents,$match);

$list = json_decode($match[1],1);
array_multisort(array_column($list,'id'),SORT_ASC,$list);
//print_r($list);
$last_id = file_get_contents('last_id.txt');
foreach ($list as $item)
{
    if ($last_id < $item['id'])
    {
        //print_r($item);
        $time = $item['pubDate'] / 1000;
        $timeStr = date("m-d H:i",$time);
        $sendMsg = "【{$item['title']}】\n{$item['summary']}\n[{$item['infoSource']}] {$timeStr}";
        $CQ->sendGroupMsg($group,$sendMsg);
        echo $sendMsg;
        file_put_contents('last_id.txt',$item['id']);
        break;
    }
}
/*  最新消息 结束  */

/*  全国统计信息 开始  */
preg_match('/window\.getStatisticsService \= (\{.*?\})\}catch\(e\)/',$contents,$match);
$info = json_decode($match[1],1);
$last_time = file_get_contents('last_time.txt');
if ($last_time < $info['modifyTime'])
{
	$timeStr = date("Y-m-d H:i",$info['modifyTime']/1000);
    $sendMsg = "截至{$timeStr}\n全国2019-nCoV感染确诊{$info['confirmedCount']}例,疑似{$info['suspectedCount']}例,死亡{$info['deadCount']}例,治愈{$info['curedCount']}例\n详情：https://3g.dxy.cn/newh5/view/pneumonia";
    $CQ->sendGroupMsg($group,$sendMsg);
    echo $sendMsg;
    file_put_contents('last_time.txt',$info['modifyTime']);
}
/*  全国统计信息 结束  */

?>