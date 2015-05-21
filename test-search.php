<?php
error_reporting(E_ALL);

function getPage($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_USERAGENT, 'php-unsafe-eval-robot'); 
    curl_setopt($ch, CURLOPT_REFERER, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

function gitHubSearch($query, $page) {
	$url = sprintf("https://api.github.com/search/repositories?q=%s&page=%d", urlencode($query), $page);
	$data = getPage($url);
	$json = json_decode($data, true);

	// TODO: We might want some error-checking, GitHub API has error responses on the form:
	//	array(3) {
	//	  ["message"]=>
	//	  string(17) "Validation Failed"
	//	  ["errors"]=>
	//	  array(1) {
	//	    [0]=>
	//	    array(3) {
	//	      ["resource"]=>
	//	      string(6) "Search"
	//	      ["field"]=>
	//	      string(1) "q"
	//	      ["code"]=>
	//	      string(7) "missing"
	//	    }
	//	  }
	//	  ["documentation_url"]=>
	//	  string(38) "https://developer.github.com/v3/search"
	//	}
	//
	
	$items = $json['items'];
	$urls = array();
	foreach ($items as $item) {
		$url = $item['url'];
		$urls[] = $url;
	}

	return $urls;
}

$query = 'eval($_POST';
$page = 1;
while(true) {
	$urls = gitHubSearch($query, $page);
	if(count($urls) == 0) break;
	
	foreach ($urls as $url) {
		print("$url\n");
	}
	
	$page += 1;
} 

