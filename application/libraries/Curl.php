<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class CUrl {
	private $curl;

	function __construct() {
		$this->CI = & get_instance();
		$this->curl = curl_init();
		curl_setopt($this->curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.1) Gecko/2008070208 Firefox/3.0.1");
		curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, false);
		curl_setopt($this->curl, CURLOPT_HEADER, false);
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($this->curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
		if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN')
			curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 5 / 2);
		else
			curl_setopt($this->curl, CURLOPT_TIMEOUT, 5);
		curl_setopt($this->curl, CURLOPT_AUTOREFERER, TRUE);
	}

	function get($url, $follow=false, $header=false, $quiet=true, $referer=false, $headers=array()) {
		curl_setopt($this->curl, CURLOPT_URL, $url);
		curl_setopt($this->curl, CURLOPT_POST, false);
		curl_setopt($this->curl, CURLOPT_HTTPGET, true);
		if ($headers) {
			$curl_headers = array();
			foreach ($headers as $header_name => $value)
				$curl_headers[] = "{$header_name}: {$value}";
			curl_setopt($this->curl, CURLOPT_HTTPHEADER, $curl_headers);
		}
		if ($header OR $follow)
			curl_setopt($this->curl, CURLOPT_HEADER, true);
		else
			curl_setopt($this->curl, CURLOPT_HEADER, false);
		if ($referer)
			curl_setopt($this->curl, CURLOPT_REFERER, $referer);
		else
			curl_setopt($this->curl, CURLOPT_REFERER, '');
		$result = curl_exec($this->curl);
		if ($follow) {
			$new_url = $this->followLocation($result, $url);
			if (!empty($new_url))
				$result = $this->get($new_url, $follow, $header, $quiet, $url, $headers);
		}

		return $result;
	}

	protected function post($url, $post_elements, $follow=false, $header=false, $referer=false, $headers=array()) {
		$flag = false;

		$elements = '';
		foreach ($post_elements as $name => $value) {
			if ($flag)
				$elements.='&';
			$elements.="{$name}=" . urlencode($value);
			$flag = true;
		}

		curl_setopt($this->curl, CURLOPT_URL, $url);
		curl_setopt($this->curl, CURLOPT_POST, true);
		if ($headers) {
			$curl_headers = array();
			foreach ($headers as $header_name => $value)
				$curl_headers[] = "{$header_name}: {$value}";
			curl_setopt($this->curl, CURLOPT_HTTPHEADER, $curl_headers);
		}
		if ($referer)
			curl_setopt($this->curl, CURLOPT_REFERER, $referer);
		else
			curl_setopt($this->curl, CURLOPT_REFERER, '');
		if ($header OR $follow)
			curl_setopt($this->curl, CURLOPT_HEADER, true);
		else
			curl_setopt($this->curl, CURLOPT_HEADER, false);
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, $elements);
		$result = curl_exec($this->curl);
		if ($follow) {
			$new_url = $this->followLocation($result, $url);
			if ($new_url)
				$result = $this->get($new_url, $post_elements, $follow, $header, $url, $headers, $raw_data);
		}

		return $result;
	}

	function followLocation($result, $old_url) {
		if ((strpos($result, "HTTP/1.1 3") === false) AND (strpos($result, "HTTP/1.0 3") === false))
			return false;
		$new_url = trim($this->getElementString($result, "Location: ", PHP_EOL));
		if (empty($new_url))
			$new_url = trim($this->getElementString($result, "location: ", PHP_EOL));
		if (!empty($new_url))
			if (strpos($new_url, 'http') === false) {
				$temp = parse_url($old_url);
				$new_url = $temp['scheme'] . '://' . $temp['host'] . ($new_url[0] == '/' ? '' : '/') . $new_url;
			}
		return $new_url;
	}

	function getElementString($string_to_search, $string_start, $string_end) {
		if (strpos($string_to_search, $string_start) === false)
			return false;
		if (strpos($string_to_search, $string_end) === false)
			return false;
		$start = strpos($string_to_search, $string_start) + strlen($string_start);
		$end = strpos($string_to_search, $string_end, $start);
		$return = substr($string_to_search, $start, $end - $start);
		return $return;
	}

	function __destruct() {
		curl_close($this->curl);
	}

}

?>
