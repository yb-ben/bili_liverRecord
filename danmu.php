<?php

error_reporting(E_ALL);

$data = '000000700010000000000005000000007b22636d64223a22524f4f4d5f5245414c5f54494d455f4d4553534147455f555044415445222c2264617461223a7b22726f6f6d6964223a32313434393038332c2266616e73223a3236313932312c227265645f6e6f74696365223a2d317d7d';
//$header = substr($data,0,32);
//
//$header = str_split($header,2);
//
//$body = substr($data,32);

echo pack('H*',$data);



function parseMsg($str){

     static $temp,$context;
     if(empty($temp)){
         $temp = [];
     }
     if(empty($context)){
         $context = inflate_init(ZLIB_ENCODING_DEFLATE);
     }

    $header = substr($str,0,32);
    $header = str_split($header,2);//头
    $len =  base_convert($header[2].$header[3],16,10);//长度
    $body = substr($str,32);
    $body = hex2bin($body);
    if('08' === $header[11]){
        return json_decode($body);
    }
    if('00' === $header[7]){
        $b = substr($body,0,$len-16);

        $d = json_decode($b,JSON_UNESCAPED_UNICODE);

        if($d['cmd'] === 'DANMU_MSG'){
            $temp[] = [$d['info'][1],$d['info'][2][0],$d['info'][2][1],date('Y-m-d H:i:s',$d['info'][9]['ts'])];
        }
        if($d['cmd'] === 'SUPER_CHAT_MESSAGE'){

        }
        $s = substr($body, $len - 16);
         if($s === ''){

             return $temp;

         }
        return parseMsg(bin2hex($s));

    }
    $body = inflate_add($context,$body);
    return parseMsg(bin2hex($body));
}

print_r(parseMsg($data));
