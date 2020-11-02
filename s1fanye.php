<?php
set_time_limit(0);
require './vendor/autoload.php';

$client = new \GuzzleHttp\Client();
for($page= 140;$page<238;){
    $uri = "https://bbs.saraba1st.com/2b/thread-1931645-$page-1.html";
    $response = $client->request(
        'GET',
        $uri,
        [

            'headers' => [
                'Accept' => '*/*',
                'Accept-Encoding' => 'gzip, deflate, br',
                'Accept-Language' => 'zh-CN,zh;q=0.9',
                'Referer' => $uri,
                'Connection' => 'keep-alive',
                'Cookie'=>'_uab_collina=158857333391959537265431; B7Y9_2132_saltkey=dztT2t5T; B7Y9_2132_lastvisit=1588569730; __cfduid=d65ba28acbe3f494167595f6a3dbe7bbb1588573332; UM_distinctid=171de596bcca34-0dd72483836e38-c373667-13c680-171de596bcdc32; _ga=GA1.2.2130522746.1588573335; B7Y9_2132_visitedfid=151; B7Y9_2132_nofavfid=1; B7Y9_2132_smile=1465D1; _gid=GA1.2.1276994654.1589246798; B7Y9_2132_sid=rB6t88; B7Y9_2132_pc_size_c=0; B7Y9_2132_ulastactivity=b5ebuUzNzJTmNrqWpwH5Gns84al%2FQ9g1AqxXy5kcI3qgEuHV2QfC; B7Y9_2132_auth=590cBhMjtWGACc6Eyua7%2FwlJwwK%2Fe3%2Fu6Np%2Fgf6IwY%2Fl0irXI%2F53zjdMKz0gY6ack3upEjb2FMeYWzfEagMFLm3RfAQ; B7Y9_2132_lastcheckfeed=530212%7C1589246815; B7Y9_2132_lip=223.73.146.152%2C1589246815; B7Y9_2132_yfe_in=1; B7Y9_2132_myrepeat_rr=R0; B7Y9_2132_noticeTitle=1; B7Y9_2132_ignore_notice=1; B7Y9_2132_home_diymode=1; CNZZDATA1260281688=1115209960-1588571638-https%253A%252F%252Fwww.baidu.com%252F%7C1589247218; B7Y9_2132_st_t=530212%7C1589247329%7C60ea88378840dcc61a0c22df11eaf0ea; B7Y9_2132_forum_lastvisit=D_151_1589247329; B7Y9_2132_viewid=tid_1931645; B7Y9_2132_sendmail=1; B7Y9_2132_st_p=530212%7C1589247420%7Ca14e439a0c2c322a57c32b3733db2f82; B7Y9_2132_lastact=1589247546%09forum.php%09ajax',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.163 Safari/537.36'
            ],
            'verify' => true
        ]
    );

    $s = $response->getBody();
    $str = $s->getContents();
    if(mb_strpos($str,'住持')){
        echo $page,',';
    }

    if(mb_strpos($str,'主持')){
        echo $page,',';
    }
    $page++;
   // sleep(0.1);
}
