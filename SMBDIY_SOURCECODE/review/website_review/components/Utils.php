<?php
class Utils {
	public static function shuffle_assoc($list) {
		$keys = array_keys($list);
		shuffle($keys);
		$random = array();
		foreach ($keys as $key)
			$random[$key] = $list[$key];

		return $random;
	}

	public static function proportion ($big, $small) {
		return $big > 0 ? round($small * 100 / $big, 2) : 0;
	}

	public static function createNestedDir($path) {
		$dir = pathinfo($path , PATHINFO_DIRNAME);
		if(is_dir($dir))
			return true;
		else if(self::createNestedDir($dir) )
			if( mkdir($dir) ) {
				chmod($dir , 0777);
				return true;
			}
		return false;
	}
	public static function createPdfFolder($domain) {
		$pdf=self::getPdfFile($domain);
		if(!file_exists($pdf)) {
			self::createNestedDir($pdf);
		}
		return $pdf;
	}
	public static function deletePdf($domain) {
        foreach(Yii::app()->params['languages'] as $langId=>$language) {
            $pdf=self::getPdfFile($domain, $langId);
            if(file_exists($pdf)) {
                unlink($pdf);
            }
        }
		return true;
	}

	public static function getPdfFile($domain, $lang=null) {
		$root = Yii::getPathofAlias('webroot');
		$lang = $lang ? $lang : Yii::app() -> language;
		$subfolder = mb_substr($domain, 0, 1);
		$file = $root."/pdf/".$lang."/".$subfolder."/".$domain.".pdf";
		return $file;
	}

	public static function loadConfig($config) {
		static $stack=array();
		if(isset($stack[$config])) {
			return $stack[$config];
		}
		$file=Yii::getPathOfAlias("application.config.{$config}").".php";
		$stack[$config]=file_exists($file) ? include_once $file : array();
		return $stack[$config];
	}
	
	/*
	* thelonglongdomain.com -> thelong...ain.com
	*/
	public static function cropDomain($domain, $length=24, $separator='...') {
		if(mb_strlen($domain)<$length) {
			return $domain;
		}
		$sepLength=mb_strlen($separator);
		$backLen=6;
		$availableLen=$length-$sepLength-$backLen; // 20-3-6=11
		$firstPart=mb_substr($domain, 0, $availableLen);
		$lastPart=mb_substr($domain, -$backLen);
		return $firstPart.$separator.$lastPart;
	}

    public static function curl($url) {
        $ch = curl_init($url);
        $html = self::curl_exec($ch);
        curl_close($ch);
        return $html;
    }

    public static function curl_exec($ch, &$maxredirect = null) {
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);

        $user_agent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5)".
            " Gecko/20041107 Firefox/1.0";
        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent );

        $mr = $maxredirect === null ? 5 : intval($maxredirect);
        if (ini_get('open_basedir') == '' && (ini_get('safe_mode') == 'Off' || ini_get('safe_mode')=='')) {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $mr > 0);
            curl_setopt($ch, CURLOPT_MAXREDIRS, $mr);
        } else {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
            if ($mr > 0)
            {
                $original_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
                $newurl = $original_url;

                $rch = curl_copy_handle($ch);

                curl_setopt($rch, CURLOPT_HEADER, true);
                curl_setopt($rch, CURLOPT_NOBODY, true);
                curl_setopt($rch, CURLOPT_FORBID_REUSE, false);
                curl_setopt($rch, CURLOPT_RETURNTRANSFER, true);
                do
                {
                    curl_setopt($rch, CURLOPT_URL, $newurl);
                    $header = curl_exec($rch);
                    if (curl_errno($rch)) {
                        $code = 0;
                    } else {
                        $code = curl_getinfo($rch, CURLINFO_HTTP_CODE);
                        if ($code == 301 || $code == 302) {
                            preg_match('/Location:(.*?)\n/i', $header, $matches);
                            $newurl = trim(array_pop($matches));
                            // if no scheme is present then the new url is a
                            // relative path and thus needs some extra care
                            if(!preg_match("/^https?:/i", $newurl)){
                                $newurl = $original_url . $newurl;
                            }
                        } else {
                            $code = 0;
                        }
                    }
                } while ($code && --$mr);

                curl_close($rch);

                if (!$mr)
                {
                    if ($maxredirect === null)
                        return false;
                    else
                        $maxredirect = 0;

                    return false;
                }
                curl_setopt($ch, CURLOPT_URL, $newurl);
            }
        }
        return curl_exec($ch);
    }

    public static function getResponseWithCurlInfo($url) {
        $ch = curl_init($url);
        $response = self::curl_exec($ch);
        $info = curl_getinfo($ch);
        return array(
            'response'=>$response,
            'info'=>$info
        );
    }

    public static function getHeaders($url, $maxredir=5) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept-Encoding:gzip',
        ));
        $i=0;
        do {
            curl_setopt($ch, CURLOPT_URL, $url);
            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                return [];
            } else {
                $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if ($code == 301 || $code == 302) {
                    preg_match('/Location:(.*?)\n/i', $response, $matches);
                    $newurl = trim(array_pop($matches));
                    if(!preg_match("/^https?:/i", $newurl)){
                        $url = $url . $newurl;
                    } else {
                        $url = $newurl;
                    }
                } else {
                    return self::get_headers_from_curl_response($response);
                }
            }
            $i++;
        } while($i < $maxredir);
        return [];
    }



    private static function get_headers_from_curl_response($response) {
        $headers = array();
        $header_text = substr($response, 0, strpos($response, "\r\n\r\n"));
        foreach (explode("\r\n", $header_text) as $i => $line) {
            if ($i === 0) {
                $headers['status'] = $line;
                $data = explode(' ', $line);
                $headers['http_code'] = isset($data[1]) ? $data[1] : null;
            } else {
                list ($key, $value) = explode(': ', $line);
                $headers[$key] = $value;
            }
        }
        return $headers;
    }
}