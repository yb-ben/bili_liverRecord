<?php
error_reporting(E_ALL);
require './vendor/autoload.php';

//
//$roomId = '21224291';
//
//$recorder = new \core\LiverRecorder();
//$recorder->run($roomId);

$log = '/var/log/bili_observer.log';
$master= new \core\Process();
$master->daemon();
while(true){
    @file_put_contents($log,'heartbeat',FILE_APPEND);
}
