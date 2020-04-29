<?php

//error_reporting(E_ALL);
//$data = '0000011300100002000000050000000078da4c8fbf4a034118c4cfc2de67987a02dfb77bbbb9bd4e895ac546acce4342bc6020c6c24b933f451ac117d042023e82b1b4f0695cf0316453a83b30ecc0f0fb982cdb3bc90eb2f4f6932d30bcbd4689dee159ffe2aa7f7e0a623c1ddda1ac2aa1d2386a5e042bce515dd1151b423062c36f724a21c2c00f86a3c683925413f169f3f5f110dfb6f17913b76bb032a2dd5c0b623e1fdda4a2268948fa01352bb5c452437c5f2f417c3f7ec697d7e3f1b4018de662bd28d57773afde1048672aeb12489c1a6f1c713913b18d4bd004443b6e274dc7f94e0efe0f358596d3d964c205da7b947fe388618b1247c189ed9902abdda45d756752af7e020000ffff6edc56be';
//$body = substr($data,32);
//$context = inflate_init(ZLIB_ENCODING_DEFLATE);
//$msg = inflate_add($context, hex2bin($body));
//$head = substr($msg,0,16);
//$payload = substr($msg,16);
//var_dump(json_decode($payload,JSON_UNESCAPED_UNICODE));

go(function(){
    $roomId = '17961';
    //$url = "https://api.live.bilibili.com/room/v1/Danmu/getConf?room_id={$roomId}&platform=pc&player=web";
    $client = new Swoole\Coroutine\Http2\Client('api.live.bilibili.com',443,true);
    $client->connect();

    $req = Swoole\Coroutine\Http2\Request();
    $req->method = 'GET';
    $req->path = "room/v1/Danmu/getConf?room_id={$roomId}&platform=pc&player=web";
    $req->headers = [
        'Host' => 'api.live.bilibili.com' ,
        'Origin' => 'https://live.bilibili.com',
        'Reference' => 'https://live.bilibili.com/'.$roomId,
    ];
    $client->send($req);
    $response = $client->recv();

    print_r($response->data);



});
