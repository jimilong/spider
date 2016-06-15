<?php
/*
authored by Josh Fraser (www.joshfraser.com)
released under Apache License 2.0

Maintained by Alexander Makarov, http://rmcreative.ru/

$Id$
*/

require("RollingCurl.php");
require("DB.php");

$urls = [1,2,3,4,5];
$urls = shuffle($urls);
print_r($urls);exit;

// a little example that fetches a bunch of sites in parallel and echos the page title and response info for each request
function request_callback($response, $info, $request) {
	// parse the page title out of the returned HTML
	preg_match_all('~(http://www.lagou.com/gongsi/\w.+)"\s~Us', $response, $out);
	$db = new DB();
	$data = $db->select('web_url', ['url'], []);
	$data = array_column($data, 'url');

	if ($out[1]) {
		$data = array_unique($out[1]);
		foreach ($data as $k => $v) {
			$db->insert('web_url', ['url' => $v, 'create_time' => time()]);
		}
	}
	echo 'ok!';
	//print_r($out[1]);
	/*print_r($info);
    print_r($request);
	echo "<hr>";*/
}

// single curl request
$rc = new RollingCurl("request_callback");
$rc->request("http://www.lagou.com/gongsi/");
$rc->execute();
exit;
/*// another single curl request
$rc = new RollingCurl("request_callback");
$rc->request("http://www.google.com");
$rc->execute();

echo "<hr>";exit;*/

// top 20 sites according to alexa (11/5/09)
$urls = [
	"http://www.chbot.cn",
	"http://www.lagou.com/zhaopin/PHP/"
];

$rc = new RollingCurl("request_callback");
$rc->window_size = 20;
foreach ($urls as $url) {
    $request = new RollingCurlRequest($url);
    $rc->add($request);
}
$rc->execute();
