<?php
error_reporting(E_ALL);
require './vendor/autoload.php';

//
//$roomId = '21224291';
//
//$recorder = new \core\LiverRecorder();
//$recorder->run($roomId);

\core\Process::fork();
