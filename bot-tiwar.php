<?php
error_reporting(E_ALL & ~E_NOTICE);
date_default_timezone_set("Asia/Jakarta");
require "simple_html_dom.php";

$baseURL = "http://tiwar-id.net";
$debug = 0;

halaman_utama:
echo "Clear cookie\n";
@unlink("$username.txt");

echo "Buka halaman awal\n";
$out = getUrl($baseURL);

$html = str_get_html($out);
// echo $html;
foreach($html->find('a.btn') as $row) {
        $sessionID = str_replace("/startpage/?","",$row->href);
        break;
}

halaman_login:
echo "Buka halaman login\n";
$url = $baseURL."?sign_in=1&".$sessionID;
getUrl($url);

proses_login:
echo "Proses login\n";
postUrl($baseURL,"login=$username&pass=$password",$baseURL."/?sign_in=1&$sessionID");

halaman_beranda:

halaman_league:
echo "Buka halaman league\n";
$out = getUrl($baseURL .  "/league/");

$html = str_get_html($out);

$i = 1;$j = 1;$musuh=array();unset($musuh);
foreach($html->find('div.block_zero') as $row) {
        if($i == 1 || $i == 3 || $i == 4 || $i == 5 || $i > 6) {
                $i++;
                continue;
        }
        if($i == 2) {
                $url = "";
                // cek jika nemu take reward then take reward then league
                foreach($row->find('a') as $row2) {
                                $url = $row2->href;
                                if(strpos($url,"league/takeReward")) break;
                }

                if(strpos($url,"league/takeReward")) {
                        echo "Take reward\n";

                        $url = $baseURL . $url;
                        echo $url . "\n";
                        $out = getUrl($url,$baseURL."/league");
                        goto end_league;
                }

                // cek sisa pertarungan
                foreach($row->find('b') as $row2)
                        $num = intval($row2->plaintext);

                echo "Sisa pertarungan : $num\n";

                if($num == 0) goto end_league;

                $i++;
                continue;
        }

        $raw = explode("\n",$row->plaintext);
        $musuh[$j][] = filter_var($raw[2], FILTER_SANITIZE_NUMBER_INT);
        $musuh[$j][] = filter_var($raw[3], FILTER_SANITIZE_NUMBER_INT);
        $musuh[$j][] = filter_var($raw[4], FILTER_SANITIZE_NUMBER_INT);
        $musuh[$j][] = filter_var($raw[5], FILTER_SANITIZE_NUMBER_INT);

        foreach($row->find('a.btn') as $row2) {
                        $url = $row2->href;
                        if(strpos($url,"league/fight")) break;
        }
        if(strpos($url,"league/fight")) $musuh[$j][] = $url;
        unset($url);

        $i++;$j++;
}

echo "attack league\n";
$url = $baseURL . $musuh[1][4];
$out = getUrl($url,$baseURL . "/league");

goto halaman_league;
end_league:

halaman_cave:
echo "Buka halaman cave\n";
$out = getUrl($baseURL. "/cave/");

if($debug) echo $out;

$html = str_get_html($out);
foreach($html->find('a.btn') as $row) {
        $url = $row->href;
        if(strpos($url,"cave/runaway")) break;
}

if(strpos($url,"cave/runaway")) {
        echo "Ada monster, kabur\n";
        $url = $baseURL . $url;
        echo $url . "\n";
        $out = getUrl($url,$baseURL . "/cave");

        goto halaman_cave;
}

$html = str_get_html($out);
foreach($html->find('a.btn') as $row) {
        $url = $row->href;
        if(strpos($url,"cave/down")) break;
}

if(strpos($url,"cave/down")) {
        echo "Proses cave down\n";
        $url = $baseURL . $url;
        echo $url . "\n";
        $out = getUrl($url,$baseURL . "/cave");
}

$html = str_get_html($out);
foreach($html->find('a.btn') as $row) {
        $url = $row->href;
        if(strpos($url,"cave/gather")) break;
}

if(strpos($url,"cave/gather")) {
        echo "Proses cave gather\n";
        $url = $baseURL . $url;
        echo $url . "\n";
        $out = getUrl($url,$baseURL . "/cave");
}

halaman_quest:
echo "Buka halaman quest\n";
getUrl($baseURL . "/sage/");
$out = getUrl($baseURL . "/quest/",$baseURL."/sage/");
if($debug) echo $out;

$html = str_get_html($out);
foreach($html->find('a.btn') as $row) {
        $url = $row->href;
        if(strpos($url,"quest/end")) break;
}

if(strpos($url,"quest/end")) {
        echo "Ada quest\n";
        $url = $baseURL . $url;
        echo $url . "\n";
        $out = getUrl($url,$baseURL . "/quest");

        goto halaman_quest;
}
else
        echo "Tidak ada quest\n";

halaman_chest:
echo "Buka halaman chest\n";
getUrl($baseURL . "/sage/");
$out = getUrl($baseURL . "/quest/",$baseURL."/sage/");

$html = str_get_html($out);
foreach($html->find('a.b_green') as $row) {
        $url = $row->href;
        if(strpos($url,"quest/openChest")) break;
}

if(strpos($url,"quest/openChest")) {
        echo "Ada chest\n";
        $url = $baseURL . $url;
        echo $url . "\n";
        $out = getUrl($url,$baseURL . "/quest");
}
else
        echo "Tidak ada chest\n";
end_chest:

halaman_relic:
echo "Buka halaman relic\n";
getUrl($baseURL . "/sage/");
$out = getUrl($baseURL . "/relic/",$baseURL."/sage/");
if($debug) echo $out;

$html = str_get_html($out);
foreach($html->find('a.btn') as $row) {
        $url = $row->href;
        if(strpos($url,"reward")) break;
}

if(@strpos($url,"reward")) {
        echo "Ada reward\n";
        $url = $baseURL . $url;
        echo $url . "\n";
        $out = getUrl($url,$baseURL . "/relic");
        unset($url);

        goto halaman_relic;
}
else
        echo "Tidak ada relic\n";

halaman_gold:
echo "Buka halaman trade\n";
$out = getUrl($baseURL . "/trade/");
if($debug) echo $out;

$html = str_get_html($out);
foreach($html->find('a') as $row) {
        $url = $row->href;
        if(strpos($url,"trade/exchange")) break;
}

if(strpos($url,"trade/exchange")) {
        echo "Buka halaman exchange\n";
        $url = $baseURL . $url;
        $temp = $url;
        echo $url . "\n";
        $out = getUrl($url,$baseURL . "/trade");

        $gold = array();
        $html = str_get_html($out);
        foreach($html->find('a') as $row) {
                        $url = $row->href;
                        if(strpos($url,"trade/exchange/silver")) break;
        }

        if(strpos($url,"trade/exchange/silver/")) {
                $url = $baseURL . $url;
                echo $url . "\n";
                $out = getUrl($url,$temp);

                goto halaman_gold;
        }
}

halaman_campaign:
echo "Buka halaman campaign\n";
$out = getUrl($baseURL . "/campaign/");
if($debug) echo $out;

$html = str_get_html($out);
foreach($html->find('a.btn') as $row) {
        $url = $row->href;
        if(strpos($url,"campaign/go")) break;
}

if(strpos($url,"campaign/go")) {
        $url = $baseURL . $url;
        echo $url . "\n";
        $out = getUrl($url,$baseURL . "/campaign");
        goto halaman_campaign;
}

$html = str_get_html($out);
foreach($html->find('a.btn') as $row) {
        $url = $row->href;
        if(strpos($url,"campaign/fight")) break;
}

if(strpos($url,"campaign/fight")) {
        $url = $baseURL . $url;
        echo $url . "\n";
        $out = getUrl($url,$baseURL . "/campaign");
}

$html = str_get_html($out);
foreach($html->find('a.btn') as $row) {
        $url = $row->href;
        if(strpos($url,"campaign/attack")) break;
}

if(strpos($url,"campaign/attack")) {
        $url = $baseURL . $url;
        echo $url . "\n";
        $out = getUrl($url,$baseURL . "/campaign");
        goto halaman_campaign;
}

$html = str_get_html($out);
foreach($html->find('a.btn') as $row) {
        $url = $row->href;
        if(strpos($url,"campaign/end")) break;
}

if(strpos($url,"campaign/end")) {
        $url = $baseURL . $url;
        echo $url . "\n";
        $out = getUrl($url,$baseURL . "/campaign");
        goto halaman_campaign;
}

halaman_bag:
echo "Buka halaman user\n";
$out = getUrl($baseURL . "/user/");

echo "Buka halaman bag\n";
$out = getUrl($baseURL . "/inv/bag",$baseURL . "/user");

$html = str_get_html($out);
foreach($html->find('a') as $row) {
        $url = $row->href;
        if(strpos($url,"inv/bag/sellAll/1")) break;
}

if(strpos($url,"inv/bag/sellAll/1")) {
        $url = $baseURL . $url;
        echo $url . "\n";
        $out = getUrl($url,$baseURL . "/inv/bag");
}

halaman_money:
echo "Buka halaman clan\n";
$out = getUrl($baseURL . "/clan");

$html = str_get_html($out);
foreach($html->find('a') as $row) {
        $url = $row->href;
        if(strpos($url,"money")) break;
}

if(strpos($url,"money")) {
        echo "Buka halaman clan money\n";
        $url = $baseURL . $url;
        $ref = $url;
        echo $url . "\n";
        $out = getUrl($url,$baseURL . "/clan");

        $html = str_get_html($out);
        foreach($html->find('span.medium') as $row) {
                $silver = $row->plaintext;
                break;
        }

        $html = str_get_html($out);
        foreach($html->find('form') as $row) {
                $post = $row->action;
                break;
        }

        $silver = explode("\n",$silver);
        $silver = filter_var($silver[1], FILTER_SANITIZE_NUMBER_INT);
        if($silver == 0) goto end_money;

        echo "Donasi : $silver silver\n";

        postUrl($baseURL.$post,"silver=$silver&gold=0&type=normal",$baseURL.$ref);
}

end_money:

halaman_coliseum:
//if($username=='digitec') goto end_coliseum;
//if($username=='kucinggarong') goto end_coliseum;

$heal   = 0;
$health = 0;
$loop   = 0;
echo "Buka halaman coliseum\n";
$out = getUrl($baseURL."/coliseum/");
$html = str_get_html($out);
foreach($html->find('a') as $row) {
        $url = $row->href;
        if(strpos($url,"coliseum/enterFight")) {
                echo "enter fight\n";
                $url = $baseURL.$url;
                echo $url . "\n";
                $out = getUrl($url,$baseURL."/coliseum");
                $fight = false;
                sleep(5);
        }
}

halaman_coliseum2:
echo "Buka halaman coliseum lagi\n";
$out = getUrl($baseURL."/coliseum/");
$html = str_get_html($out);
foreach($html->find('a') as $row) {
        $url = $row->href;
        if(strpos($url,"coliseum/?end_fight=true")) break;
}

if(strpos($url,"coliseum/?end_fight=true")) {
        echo "Akhir pertarungan\n";
        $url = $baseURL.$url;
        echo $url . "\n";
        $out = getUrl($url,$baseURL."/coliseum/");
        $out = getUrl($baseURL."/coliseum/quit/?end_fight=true",$baseURL."/coliseum/");
        //echo $out;
        goto end_coliseum;
}

$html = str_get_html($out);
foreach($html->find('a') as $row) {
        $url = $row->href;
        if(strpos($url,"coliseum/atk")) break;
}

$html = str_get_html($out);
foreach($html->find('span.white') as $row)
        $health = intval($row->plaintext);

echo "health = $health\n";
echo "loop   = ".$loop++."\n";
echo "heal   = $heal\n";

if($loop > 100) exit;

if($health < 1500 && $heal == 0) {
        //echo "debug1\n";
        $html = str_get_html($out);
        unset($url);
        foreach($html->find('a') as $row) {
                //echo "debug2\n";
                $url = $row->href;
                if(strpos($url,"coliseum/heal")) break;
        }

        if(strpos($url,"coliseum/heal")) {
                //echo "debug3\n";
                echo "healing\n";
                $heal = 1;
                sleep(2);
                $url = $baseURL.$url;
                echo $url . "\n";
                $out = getUrl($url,$baseURL."/coliseum");
                $out = getUrl($baseURL."/coliseum/");
                foreach($html->find('a') as $row) {
                        $url = $row->href;
                        if(strpos($url,"coliseum/atk")) break;
                }
        }
}

$div = array();
$html = str_get_html($out);
foreach($html->find('div.block_zero') as $row)
        $div[] =  trim($row->plaintext);

echo $div[1] . "\n" . $div[2] . "\n";

if(strpos($url,"coliseum/atk")) {
        $fight = true;
        $url = $baseURL.$url;
        echo $url . "\n";
        $out = getUrl($url,$baseURL."/coliseum");
        $fight = true;
        sleep(2);
        goto halaman_coliseum2;
}
elseif($fight == true) {
        $html = str_get_html($out);
        foreach($html->find('h1') as $row)
                echo $row->plaintext ."\n";

        goto end_coliseum;
}
else {
        sleep(5);
        goto halaman_coliseum2;
}

end_coliseum:

halaman_dungeon:
echo "Buka halaman dungeon\n";
getUrl($baseURL . "/clan/");
$out = getUrl($baseURL . "/clandungeon/",$baseURL."/clan/");

$html = str_get_html($out);
foreach($html->find('a.btn') as $row) {
        $url = $row->href;
        if(strpos($url,"close")) break;
}

if(@strpos($url,"close")) {
        echo "Ada hadiah\n";
        $url = $baseURL . "/clandungeon/?close";
        echo $url . "\n";
        $out = getUrl($url,$baseURL . "/clandungeon");
        unset($url);

        goto halaman_dungeon;
}

$html = str_get_html($out);
foreach($html->find('a.btn') as $row) {
        $url = $row->href;
        if(strpos($url,"clandungeon/attack")) break;
}

if(@strpos($url,"clandungeon/attack")) {
        echo "Ada monster, lawan !\n";
        $url = $baseURL . $url;
        echo $url . "\n";
        $out = getUrl($url,$baseURL . "/clandungeon");
        unset($url);

        goto halaman_dungeon;
}
else
        echo "Tidak ada monster\n";

end_dungeon:

halaman_koleksi:
echo "Buka halaman koleksi\n";
getUrl($baseURL . "/sage/");
$out = getUrl($baseURL . "/collector/",$baseURL."/sage/");

$html = str_get_html($out);
foreach($html->find('a.btn') as $row) {
        $url = $row->href;
        if(strpos($url,"reward/element")) break;
        if(strpos($url,"reward/collection")) break;
}

if(@strpos($url,"reward/element")) {
        echo "Ada koleksi\n";
        $url = $baseURL . $url;
        echo $url . "\n";
        $out = getUrl($url,$baseURL . "/collector");
        unset($url);

        goto halaman_koleksi;
}
else
        echo "Tidak ada koleksi\n";

if(@strpos($url,"reward/collection")) {
        echo "Ada hadiah\n";
        $url = $baseURL . $url;
        echo $url . "\n";
        $out = getUrl($url,$baseURL . "/collector");
        unset($url);

        goto halaman_koleksi;
}
else
        echo "Tidak ada hadiah\n";

end_koleksi:



halaman_arena:
echo "Buka halaman arena\n";
$out = getUrl($baseURL . "/arena/");
if($debug) echo $out;

$html = str_get_html($out);
foreach($html->find('title') as $row) {
        $url = $row->plintext;
        if(strpos($url,"404")) exit;
}

$html = str_get_html($out);
foreach($html->find('a.btn') as $row) {
        $url = $row->href;
        if(strpos($url,"arena/attack/1")) break;
}

if(strpos($url,"arena/attack")) {
        $url = $baseURL . $url;
        echo $url . "\n";
        $out = getUrl($url,$baseURL . "/arena");

        unset($status);
        $html = str_get_html($out);
        foreach($html->find('h2') as $row) {
                $status = trim($row->plaintext);
                echo "Attack status : $status\n";
                break;
        }
        if(@strlen($status) == 0) {
                $html = str_get_html($out);
                foreach($html->find('div.foot') as $row) {
                        echo "stat : ".$row->plaintext . "\n";
                        file_put_contents("stat.txt",$row->plaintext."\n",FILE_APPEND);
                }

                echo date("H:i:s") . " Istirahat\n";
                file_put_contents("stat.txt",date("H:i:s") . " Istirahat\n",FILE_APPEND);
                exit;sleep(900);
                goto halaman_beranda;
        }
}

goto halaman_arena;

function getUrl($url, $ref="http://tiwar-id.net") {
        //sleep(2);
        global $username;
        $ch = curl_init($url);
        //curl_setopt($ch, CURLOPT_PROXY, "192.168.200.2:1080");
        //curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS4);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_USERAGENT,"Mozilla/5.0 (Windows NT 6.3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.116 Safari/537.36");
        curl_setopt($ch, CURLOPT_COOKIEFILE, "$username.txt");
        curl_setopt($ch, CURLOPT_COOKIEJAR, "$username.txt");
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,60);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        if(strlen($ref) > 0) curl_setopt($ch, CURLOPT_REFERER, $ref);
        return curl_exec($ch);
}

function postUrl($url,$post, $ref) {
        //sleep(2);
        global $username;
        $ch = curl_init($url);
        //curl_setopt($ch, CURLOPT_PROXY, "192.168.200.2:1080");
        //curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS4);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_USERAGENT,"Mozilla/5.0 (Windows NT 6.3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.116 Safari/537.36");
        curl_setopt($ch, CURLOPT_COOKIEFILE, "$username.txt");
        curl_setopt($ch, CURLOPT_COOKIEJAR, "$username.txt");
        curl_setopt($ch, CURLOPT_REFERER, $ref);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,60);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        return curl_exec($ch);
}
