<?php
function http_get($url = '') {
	if ($url == '') {
		return false;
	}
	if (! strstr ( $url, 'http://' ) and ! strstr ( $url, 'https://' )) {
		$url = 'http://' . $url;
	}

	$curl = curl_init ();

	curl_setopt ( $curl, CURLOPT_URL, $url );
	curl_setopt ( $curl, CURLOPT_HEADER, 0 );
	curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt ( $curl, CURLOPT_TIMEOUT, 30 );
	$data = curl_exec ( $curl );
	$http_code = curl_getinfo ( $curl, CURLINFO_HTTP_CODE );

	if ($http_code == 200) {
		return $data;
	}

	if (curl_errno ( $curl )) {
		echo 'Curl error: ' . curl_error ( $curl );
		return false;
	}

	curl_close ( $curl );

	return false;
}
?>