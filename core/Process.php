<?php


namespace core;


class Process
{

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
}
