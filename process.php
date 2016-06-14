<?php
$funcMap=array('methodOne' , 'methodTwo' ,'methodThree' );
$worker_num =3;//创建的进程数

for($i=0;$i<$worker_num ; $i++){
    $process = new swoole_process($funcMap[$i]);
    //$process->daemon('true', 'true');
    $pid = $process->start();
    sleep(2);
}

function methodOne(swoole_process $worker){// 第一个处理
    for($i=0;$i<100000000;$i++){}
    echo $worker->callback .PHP_EOL;
}

function methodTwo(swoole_process $worker){// 第二个处理
    for($i=0;$i<100000000;$i++){}
    echo $worker->callback .PHP_EOL;
}

function methodThree(swoole_process $worker){// 第三个处理
    for($i=0;$i<100000000;$i++){}
    echo $worker->callback .PHP_EOL;
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
