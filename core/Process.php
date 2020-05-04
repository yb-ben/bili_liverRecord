<?php


namespace core;


class Process
{

    protected $children = [];

    public static function daemon()
    {
        $pid = pcntl_fork();
        if($pid < 0 ){
            die("fork(1) fail!\n");
        }elseif ($pid>0){
            exit;
        }
        $sid = posix_setsid();
        if (!$sid) {
            die("setsid failed!\n");
        }
        $pid = pcntl_fork();
        if($pid < 0 ){
            die("fork(2) fail!\n");
        }elseif ($pid>0){
            exit;
        }
        chdir('/usr/local/bili_liverRecord');
        umask(0);
        if(defined('STDIN')){
            fclose(STDIN);
        }
        if(defined('STDOUT')){
            fclose(STDOUT);
        }
        if(defined('STDERR')){
            fclose(STDERR);
        }
        return $pid;

    }


    public function fork(){
        $pid = pcntl_fork();
        if($pid < 0){
            //失败
            throw new \Exception('error:'. pcntl_get_last_error().PHP_EOL);
        }elseif($pid>0){
            //父进程
            $this->children[$pid] = $pid;
        }else{
            //子进程
        }
        return $pid;
    }

    public function dispatch($pid,Task $task){
        if(!isset($this->children[$pid])){
            throw new \Exception('pid not found');
        }
        $this->children[$pid] = $task;
    }
}
