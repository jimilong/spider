<?php

require("RollingCurl.php");
require("DB.php");

$funcMap=array('methodOne' , 'methodTwo' ,'methodThree' );
$worker_num =3;//创建的进程数

for($i=0;$i<$worker_num ; $i++){
    $process = new swoole_process($funcMap[$i]);
    //$process->daemon('true', 'true');
    $pid = $process->start();
    //sleep(2);
}

function request_callback($response, $info, $request) {
    // parse the page title out of the returned HTML
    preg_match_all('~(http://www.lagou.com/gongsi/\w.+)"\s~Us', $response, $out);
    $db = new DB();
    $check = $db->select('web_url', ['url'], []);
    $check = array_column($check, 'url');
    $out[1] = array_diff($out[1], $check);
    if ($out[1]) {
        $data = array_unique($out[1]);
        foreach ($data as $k => $v) {
            $db->insert('web_url', ['url' => $v, 'create_time' => time()]);
        }
    }
    echo 'ok!';
}

function methodOne(swoole_process $worker){// 第一个处理
    // single curl request
    $db = new DB();
    $urls = $db->select('web_url', ['url'], []);
    $urls = array_column($urls, 'url');

    $rc = new RollingCurl("request_callback");
    $rc->window_size = 20;
    foreach ($urls as $url) {
        $request = new RollingCurlRequest($url);
        $rc->add($request);
    }
    $rc->execute();

    /*$rc = new RollingCurl("request_callback");
    $rc->request("http://www.lagou.com/gongsi/");
    $rc->execute();*/
}

function methodTwo(swoole_process $worker){// 第二个处理
    $db = new DB();
    $urls = $db->select('web_url', ['url'], []);
    $urls = array_column($urls, 'url');
    $urls = array_reverse($urls);

    $rc = new RollingCurl("request_callback");
    $rc->window_size = 20;
    foreach ($urls as $url) {
        $request = new RollingCurlRequest($url);
        $rc->add($request);
    }
    $rc->execute();
}

function methodThree(swoole_process $worker){// 第三个处理
    $db = new DB();
    $urls = $db->select('web_url', ['url'], []);
    $urls = array_column($urls, 'url');
    $urls = array_slice($urls, 60);

    $rc = new RollingCurl("request_callback");
    $rc->window_size = 20;
    foreach ($urls as $url) {
        $request = new RollingCurlRequest($url);
        $rc->add($request);
    }
    $rc->execute();
}

while(1){
    $ret = swoole_process::wait();
    if ($ret){// $ret 是个数组 code是进程退出状态码，
        $pid = $ret['pid'];
        echo PHP_EOL."Worker Exit, PID=" . $pid . PHP_EOL;
    }else{
        break;
    }
}
