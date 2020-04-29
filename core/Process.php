<?php


namespace core;


class Process
{

    protected $children = [];

    public function daemon()
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
        chdir('/');
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
    }


    public function fork(){
        $pid = pcntl_fork();
        if($pid < 0){
            //失败
        }elseif($pid>0){
            //父进程
            $this->children[$pid] = $pid;
        }else{
            $this->dispatch(new Task());
        }
    }

    protected function dispatch(Task $task){
        $task->run();
    }
}
