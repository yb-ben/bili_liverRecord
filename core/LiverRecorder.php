<?php

namespace core;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Utils;
use Monolog\Logger;
use Psr\Http\Message\ResponseInterface;
use function GuzzleHttp\Psr7\stream_for;

class LiverRecorder
{

    protected  $roomId;

    protected  $client;

    protected  $path = './';

    protected  $writeBuffer = 1048576;

    protected $at = '';

    protected $logger;

    public function __construct()
    {
        $this->client = new Client();;
    }

    public function setLogger(Logger $logger){
        $this->logger = $logger;
    }

    public function getLogger():Logger
    {
        return $this->logger;
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

        $ret =  json_decode($response->getBody(), JSON_UNESCAPED_UNICODE);
        return $ret;
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

        $ret =  json_decode($response->getBody(), JSON_UNESCAPED_UNICODE);
        return $ret;
    }



    public function getStreamDataV2($url){
        $host  =  parse_url($url,PHP_URL_HOST);
        $headers = [
            'Accept' => '*/*',
            'Accept-Encoding' => 'gzip, deflate',
            'Host' => $host,
            'Origin' => 'https://live.bilibili.com',
            'Reference' => 'https://live.bilibili.com',
            'User-Agent' => 'BiliRecorder'
        ];
        $header = '';
        foreach ($headers as $k=>$v) {
            $header .= "{$k}:{$v}\r\n";
        }
        $context = stream_context_create([
            'http' =>[
                'method' => 'GET',
                'header' => $header
            ],
            'ssl' => [
                'verify_peer' => false
            ]
        ]);
        $fp = fopen($url, 'rb',false,$context);
        $wfp= fopen($this->getSaveFileName(), 'wb+');

        while (!feof($fp)) {
            fwrite($wfp, fread($fp, $this->writeBuffer));
        }
        fclose($fp);
        fclose($wfp);
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
                'User-Agent' => 'BiliRecorder'
            ],
            'verify' => true,
            'stream' => true,
        ]);

    }


    public function test($url){

        $ret = $this->getStreamData($url);
        $this->record($ret);
    }

    public function getSaveFileName(){
        return $this->path . $this->roomId .'_'.date('YmdHis').'.flv';
    }

    /**
     * @param ResponseInterface $response
     */
    public function record(ResponseInterface $response){
        $f = fopen($this->getSaveFileName(),'wb+');

        //$stream = Utils::streamFor($f);
        $body = $response->getBody();
        while ((!$body->eof())) {
             fwrite($f, $body->read($this->writeBuffer));
        }
        $body->close();
        //$stream->close();
        fclose($f);
        $this->fireLiveFinished();
    }


    public function run($id){

        $ret = $this->setRoomId($id)->getRoomInfo();


        if($ret['code'] !== 0){
             $this->fireErrorResponse($ret);
            return $ret;
        }

        if($ret['data']['live_status'] !== 1){
            //未开播
             $this->fireLiveOffline($ret);
            return $ret;
        }
        $ret = $this->getPlayUrl(2);

        $this->getLogger()->debug('[LiveUrl]',$ret);

        if($ret['code'] !== 0){
            $this->fireErrorResponse($ret);
            return $ret;
        }

        $this->getStreamDataV2($ret['data']['durl'][0]['url']);
        return true;
    }


    protected function fireLiveOffline($response = []){
        $this->logger->debug('[error response][offline]未开播');
    }

    protected function fireErrorResponse($response=[] ){
        $this->logger->debug('[error response]',$response);
    }

    protected function fireLiveFinished($response = []){
        $this->logger->debug('[finished]',$response);
    }


    public function __destruct()
    {
        $this->getLogger()->error('Recorder is exiting');
    }
}
