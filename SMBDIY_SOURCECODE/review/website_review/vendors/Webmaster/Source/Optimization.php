<?php
class Optimization {
    private $domain;

    private $robotsTxt = null;

    public function __construct($domain) {
        $this->domain = $domain;
    }

    public function getSitemap() {
        $robotsTxt = $this->getRobotsTxt();
        $pattern = "/Sitemap: ([^\r\n]*)/is";
        $sitemaps = array();
        preg_match_all($pattern, $robotsTxt, $matches);

        $urlMap = array(
            "http://".$this->domain."/sitemap.xml",
        );
        if(!empty($matches[1])) {
            foreach($matches[1] as $sitemap) {
                $urlMap[] = $sitemap;
            }
        }
        foreach($urlMap as $url) {
            $i = Utils::getHeaders($url);
            if($i['http_code'] == 200) {
                $sitemaps[] = $url;
            }
        }
        return $sitemaps;
    }

    public function getRobotsTxt() {
        if($this->robotsTxt !== null) {
            return $this->robotsTxt;
        }
        $url = "http://".$this->domain."/robots.txt";
        $i = Utils::getResponseWithCurlInfo($url);
        $response = $i['response'];
        $info = $i['info'];
        if($info['http_code'] != 200) {
            $this->robotsTxt = false;
        } else {
            $this->robotsTxt = $response;
        }
        return $this->robotsTxt;
    }

    public function hasRobotsTxt() {
        $r = $this->getRobotsTxt();
        return $r !== false;
    }

    public function hasGzipSupport() {
        $h = Utils::getHeaders("http://".$this->domain);
        return isset($h['Content-Encoding']) AND (mb_stripos($h['Content-Encoding'], 'gzip') !== false);
    }

}