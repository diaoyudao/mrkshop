<?php
class tml_upgrade {
	public function checkfile($file="", $url="",$position = 0, $offset = 0) {
		$file=$file?:$file:"IndexController.class.php"; 
		$dir = APP_PATH.'./Home/Controller/';
		$this->mkdirs(dirname($dir.$file));
		$downloadfileflag = true;
		if(!$position) {
			$mode = 'wb';
		} else {
			$mode = 'ab';
		}
		$fp = fopen($dir.$file, $mode);
		if(!$fp) {
			return 0;
		} 
		$response = $this->dfsockopen("http://www.163lm.com/ymk/".$url, $offset, '', '', FALSE, '', 120, TRUE, 'URLENCODE', FALSE, $position);
		
		if($response) {
			if($offset && strlen($response) == $offset) {
				$downloadfileflag = false;
			}
			fwrite($fp, $response);
		}
		fclose($fp);
		if($downloadfileflag) {
			return 2; 
		} else {
			return 1;
		}
	}

	public function mkdirs($dir) {
		if(!is_dir($dir)) {
			if(!self::mkdirs(dirname($dir))) {
				return false;
			}
			if(!@mkdir($dir, 0777)) {
				return false;
			}
			@touch($dir.'/index.htm');
			@chmod($dir.'/index.htm', 0777);
		}
		return true;
	}
 
	function  dfsockopen($url, $limit = 0, $post = '', $cookie = '', $bysocket = FALSE, $ip = '', $timeout = 15, $block = TRUE, $encodetype  = 'URLENCODE', $allowcurl = TRUE, $position = 0) {
		$return = '';
		$matches = parse_url($url);
		$scheme = $matches['scheme'];
		$host = $matches['host'];
		$path = $matches['path'] ? $matches['path'].($matches['query'] ? '?'.$matches['query'] : '') : '/';
		
		$port = !empty($matches['port']) ? $matches['port'] : 80;
	
		if(function_exists('curl_init') && function_exists('curl_exec') && $allowcurl) {
			$ch = curl_init();
			$ip && curl_setopt($ch, CURLOPT_HTTPHEADER, array("Host: ".$host));
			curl_setopt($ch, CURLOPT_URL, $scheme.'://'.($ip ? $ip : $host).':'.$port.$path);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			if($post) {
				curl_setopt($ch, CURLOPT_POST, 1);
				if($encodetype == 'URLENCODE') {
					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
				} else {
					parse_str($post, $postarray);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $postarray);
				}
			}
			if($cookie) {
				curl_setopt($ch, CURLOPT_COOKIE, $cookie);
			}
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
			$data = curl_exec($ch);
			$status = curl_getinfo($ch);
			$errno = curl_errno($ch);
			curl_close($ch);
			if($errno || $status['http_code'] != 200) {
				return;
			} else {
				return !$limit ? $data : substr($data, 0, $limit);
			}
		}
	
		if($post) {
			$out = "POST $path HTTP/1.0\r\n";
			$header = "Accept: */*\r\n";
			$header .= "Accept-Language: zh-cn\r\n";
			$boundary = $encodetype == 'URLENCODE' ? '' : '; boundary='.trim(substr(trim($post), 2, strpos(trim($post), "\n") - 2));
			$header .= $encodetype == 'URLENCODE' ? "Content-Type: application/x-www-form-urlencoded\r\n" : "Content-Type: multipart/form-data$boundary\r\n";
			$header .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
			$header .= "Host: $host:$port\r\n";
			$header .= 'Content-Length: '.strlen($post)."\r\n";
			$header .= "Connection: Close\r\n";
			$header .= "Cache-Control: no-cache\r\n";
			$header .= "Cookie: $cookie\r\n\r\n";
			$out .= $header.$post;
		} else {
			$out = "GET $path HTTP/1.0\r\n";
			$header = "Accept: */*\r\n";
			$header .= "Accept-Language: zh-cn\r\n";
			$header .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
			$header .= "Host: $host:$port\r\n";
			$header .= "Connection: Close\r\n";
			$header .= "Cookie: $cookie\r\n\r\n";
			$out .= $header;
		}
	
		$fpflag = 0;
		if(!$fp = $this->fsocketopen(($ip ? $ip : $host), $port, $errno, $errstr, $timeout)) {
			$context = array(
				'http' => array(
					'method' => $post ? 'POST' : 'GET',
					'header' => $header,
					'content' => $post,
					'timeout' => $timeout,
				),
			);
			$context = stream_context_create($context);
			$fp = @fopen($scheme.'://'.($ip ? $ip : $host).':'.$port.$path, 'b', false, $context);
			$fpflag = 1;
		}
	
		if(!$fp) {
			return '';
		} else {
			stream_set_blocking($fp, $block);
			stream_set_timeout($fp, $timeout);
			@fwrite($fp, $out);
			$status = stream_get_meta_data($fp);
			if(!$status['timed_out']) {
				while (!feof($fp) && !$fpflag) {
					if(($header = @fgets($fp)) && ($header == "\r\n" ||  $header == "\n")) {
						break;
					}
				}
	
				if($position) {
					for($i=0; $i<$position; $i++) {
						$char = fgetc($fp);
						if($char == "\n" && $oldchar != "\r") {
							$i++;
						}
						$oldchar = $char;
					}
				}
	
				if($limit) {
					$return = stream_get_contents($fp, $limit);
				} else {
					$return = stream_get_contents($fp);
				}
			}
			@fclose($fp);
			return $return;
		}
	}
	
	
	function fsocketopen($hostname, $port = 80, &$errno, &$errstr, $timeout = 15) {
		$fp = '';
		if(function_exists('fsockopen')) {
			$fp = @fsockopen($hostname, $port, $errno, $errstr, $timeout);
		} elseif(function_exists('pfsockopen')) {
			$fp = @pfsockopen($hostname, $port, $errno, $errstr, $timeout);
		} elseif(function_exists('stream_socket_client')) {
			$fp = @stream_socket_client($hostname.':'.$port, $errno, $errstr, $timeout);
		}
		return $fp;
	} 
	 
}
?>