<?php


namespace core;


class Process
{

    public static function fork(){

        $a = 0;
        $pid = pcntl_fork();
        if($pid === -1){
            //fail
        }else if($pid === 0) {
            //parent
            $a += 2;
            pcntl_wait($status);
        }else{
            //子进程
            $a +=3;
        }

        echo $a;
    }
}
Process::fork();
