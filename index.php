<?php

require './vendor/autoload.php';

$url = 'https://api.live.bilibili.com/room/v1/Room/getRoomInfoOld';
$log = 'client_log';
$log_templete = <<<EOF
[__DATE__] __MESSAGE__
EOF;
$method = 'GET';
$headers = [
    'query' => ['mid' => '421267475'],
    'headers' => [
        'Accept'=>'application/json, text/plain, */*',
        'Accept-Encoding' => 'gzip, deflate, br',
        'Accept-Language'=> 'zh-CN,zh;q=0.9',
        'Connection'=>'keep-alive',
        'Host'=> 'api.live.bilibili.com',
        'Origin'=> 'https://space.bilibili.com',
        'Referer'=> 'https://space.bilibili.com/421267475',
        'Sec-Fetch-Dest'=>'empty',
        'Sec-Fetch-Mode'=> 'cors',
        'Sec-Fetch-Site'=> 'same-site',
        'User-Agent'=> 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.163 Safari/537.36'
    ],
    'verify' => true
];

$flag = true;
while($flag){

    try{

        $client = new \GuzzleHttp\Client();

        $response = $client->request($method,$url,$headers);

        if($response->getStatusCode() === 200){
            $body = $response->getBody();
            $data = json_decode($body,JSON_OBJECT_AS_ARRAY);
            if($data['data']['liveStatus'] == 1){
               //  echo '主播开播了';
               $transport = (new Swift_SmtpTransport('smtp.qq.com',465,'ssl'))
                    ->setUsername('804141892@qq.com')
                    ->setPassword('pbltupcmuaubbebj')
               ;
                $mailer = new Swift_Mailer($transport);
                $message = (new Swift_Message('艾因开播了'))
                        ->setFrom(['804141892@qq.com' => 'huyibin'])
                        ->setTo(['804141892hyb@gmail.com' => 'huyibin'])
                        ->setBody('艾因开播了');
                $result = $mailer->send($message);
                file_put_contents($log,'['.date('Y-m-d H:i:s').'] '.'The notification has been send!'."\n",FILE_APPEND);

            }
        }

        sleep(60);

    }catch (\Throwable $e){

        $str = str_replace('__DATE__',date('Y-m-d H:i:s'),$log_templete);
        $str = str_replace('__MESSAGE__',$e->getMessage(),$str);
        $str .= "\n";
        @file_put_contents($log,$str,FILE_APPEND);
    }
}
