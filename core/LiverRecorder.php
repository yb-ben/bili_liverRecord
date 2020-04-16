<?php

namespace core;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class LiverRecorder
{

    protected  $roomId;

    protected  $client;

    protected  $path = './';

    protected  $writeBuffer = 1048576;

    protected $at = '';

    public function __construct()
    {
        $this->client = new \GuzzleHttp\Client();;
    }


    public function cron($time){

        while($this->at !== date('H')){
            sleep(60);
        }
        $this->run($this->roomId);
    }

    public function setRoomId(string $roomId){
        $this->roomId = $roomId;
        return $this;
    }

    public function setDir(string $path){
        $this->path = $path;
        return $this;
    }


    /**
     * 获取房间信息
     * @param $id
     * @return mixed
     */
    public  function getRoomInfo()
    {

        $response = $this->client->request(
            'GET',
            'https://api.live.bilibili.com/room/v1/Room/get_info',
            [
                'query' => ['id' => $this->roomId],
                'headers' => [
                    'Accept' => 'application/json, text/plain, */*',
                    'Accept-Encoding' => 'gzip, deflate, br',
                    'Accept-Language' => 'zh-CN,zh;q=0.9',
                    'Host' => 'api.live.bilibili.com',
                    'Origin' => 'https://live.bilibili.com',
                    'Referer' => 'https://live.bilibili.com/' . $this->roomId,
                    'Connection' => 'keep-alive',
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.163 Safari/537.36'
                ],
                'verify' => true
            ]
        );

        return json_decode($response->getBody(), JSON_UNESCAPED_UNICODE);
    }


    /**
     * 获取直播地址
     * @param $cid
     * @param int $quality
     * @param string $platform
     * @return mixed
     */
    public function getPlayUrl($quality = 2 ,$platform = 'web'){
        $response = $this->client->request(
            'GET',
            'https://api.live.bilibili.com/room/v1/Room/playUrl',
            [
                'query' => ['cid' => $this->roomId,'quality' => $quality , 'platform' => $platform],
                'headers' => [
                    'Accept' => 'application/json, text/plain, */*',
                    'Accept-Encoding' => 'gzip, deflate, br',
                    'Accept-Language' => 'zh-CN,zh;q=0.9',
                    'Host' => 'api.live.bilibili.com',
                    'Origin' => 'https://live.bilibili.com',
                    'Referer' => 'https://live.bilibili.com/' . $this->roomId,
                    'Connection' => 'keep-alive',
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.163 Safari/537.36'
                ],
                'verify' => true
            ]
        );

        return json_decode($response->getBody(), JSON_UNESCAPED_UNICODE);
    }




    public function getStreamData($url){

        $host  =  parse_url($url,PHP_URL_HOST);

        return $response = $this->client->request('get',$url,[

            'headers' => [
                'Accept' => '*/*',
                'Accept-Encoding' => 'gzip, deflate',
                'Host' => $host,
                'Origin' => 'https://live.bilibili.com',
                'Reference' => 'https://live.bilibili.com',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.163 Safari/537.36'
            ],
            'verify' => true,
            'stream' => true,
        ]);

    }


    public function getSaveFileName(){
        return $this->path . $this->roomId .'_'.time().'.flv';
    }

    /**
     * @param ResponseInterface $response
     */
    public function record(ResponseInterface $response){
        $f = fopen($this->getSaveFileName(),'w+');
        $stream = \GuzzleHttp\Psr7\stream_for($f);
        $body = $response->getBody();
        while (!$body->eof()) {
            $stream->write($body->read($this->writeBuffer));
        }
        fclose($f);
    }


    public function run($id){

        $ret = $this->setRoomId($id)->getRoomInfo();
        print_r($ret);
        if($ret['code'] !== 0){
            $this->fireErrorResponse($ret);
            return;
        }
        if($ret['data']['live_status'] !== 1){
            //未开播
            $this->fireLiveOffline($ret);
            return;
        }
        $ret = $this->getPlayUrl(4);
        print_r($ret);
        if($ret['code'] !== 0){
            $this->fireErrorResponse($ret);
        }
        $ret = $this->getStreamData($ret['data']['durl'][0]['url']);
        $this->record($ret);
    }


    protected function fireLiveOffline($response){
        echo '主播未开播';
    }

    protected function fireErrorResponse($response){
        echo 'error response';
    }
}
