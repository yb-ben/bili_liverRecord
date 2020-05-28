<?php

set_time_limit(0);
require './vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
//use Swoole\Process;

//Process::daemon();

$jar = new CookieJar();
$client = new Client(['cookies' => $jar]);

$retry = 10;
$uri = 'https://bbs.saraba1st.com/2b/member.php?mod=logging&action=login&loginsubmit=yes&infloat=yes&lssubmit=yes&inajax=1';

while(true){
    $response = $client->request(
        'POST',
        $uri,
        [
            'headers' => [
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Encoding' => 'gzip, deflate, br',
                'Accept-Language' => 'zh-CN,zh;q=0.9',
                'Referer' => $uri,
                'Connection' => 'keep-alive',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.163 Safari/537.36'
                , 'Reference' => 'https://bbs.saraba1st.com/2b/home.php?mod=spacecp&ac=usergroup'
                , 'Origin' => 'https://bbs.saraba1st.com'
                , 'Host' => 'bbs.saraba1st.com'
                , 'Content-Type' => 'application/x-www-form-urlencoded'
            ],
            'verify' => true,
            'form_params' => [
                'fastloginfield' => 'username',
                'username' => 'nocontent',
                'cookietime' => '2592000',
                'password' => '159753hybHYB!',
                'quickforward' => 'yes',
                'handlekey' => 'ls'
            ]
        ]
    );
    $s = $response->getBody();

    if ($response->getStatusCode() === 200 && false !== mb_strpos($s->getContents(), 'window.location.href')) {
        //登录成功，开始保活
        $retry = 10;
        $reLogin = 120;
        while (true) {
            try{
                $response = $client->request(
                    'GET',
                    'https://bbs.saraba1st.com/2b/forum-151-1.html',
                    [
                        'headers' => [
                            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                            'Accept-Encoding' => 'gzip, deflate, br',
                            'Accept-Language' => 'zh-CN,zh;q=0.9',
                            'Connection' => 'keep-alive',
                            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.163 Safari/537.36'
                            , 'Reference' => 'https://bbs.saraba1st.com/2b/home.php?mod=spacecp&ac=usergroup'
                            , 'Origin' => 'https://bbs.saraba1st.com'
                            , 'Host' => 'bbs.saraba1st.com'
                        ],
                        'verify' => true,
                    ]);
                if ($response->getStatusCode() !== 200 || !($reLogin--)) {
                    break;
                }
                sleep(60);

            }catch (\Throwable $throwable){
                break;
            }
        }
    }
    if(!($retry--)){
        exit;
    }
    sleep(60);
}



