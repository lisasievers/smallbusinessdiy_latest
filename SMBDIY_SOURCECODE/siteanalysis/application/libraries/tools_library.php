<?php  
    require_once('phpwhois-4.2.2/whois.main.php'); // including 
    require_once('simple_html_dom.php');
    require_once( 'IXR_Library.php' );


    class Tools_library
    {

        public  $googlehost='toolbarqueries.google.com';
        public  $googleua='Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.0.6) Gecko/20060728 Firefox/1.5';
        public $same_site_in_ip=array();

        public $ping_link = array(
            'http://ping.blogs.yandex.ru/RPC2',
            'http://blogsearch.google.com/ping/RPC2',
            'http://blogsearch.google.ae/ping/RPC2',
            'http://blogsearch.google.at/ping/RPC2',
            'http://blogsearch.google.be/ping/RPC2',
            'http://blogsearch.google.bg/ping/RPC2',
            'http://blogsearch.google.ch/ping/RPC2',
            'http://blogsearch.google.cl/ping/RPC2',
            'http://blogsearch.google.co.id/ping/RPC2',
            'http://blogsearch.google.co.il/ping/RPC2',
            'http://blogsearch.google.co.in/ping/RPC2',
            'http://blogsearch.google.co.jp/ping/RPC2',
            'http://blogsearch.google.co.ma/ping/RPC2',
            'http://blogsearch.google.co.nz/ping/RPC2',
            'http://blogsearch.google.co.th/ping/RPC2',
            'http://blogsearch.google.co.uk/ping/RPC2',
            'http://blogsearch.google.co.ve/ping/RPC2',
            'http://blogsearch.google.co.za/ping/RPC2',
            'http://blogsearch.google.com.ar/ping/RPC2',
            'http://blogsearch.google.com.au/ping/RPC2',
            'http://blogsearch.google.com.br/ping/RPC2',
            'http://blogsearch.google.com.co/ping/RPC2',
            'http://blogsearch.google.com.mx/ping/RPC2',
            'http://blogsearch.google.com.my/ping/RPC2',
            'http://blogsearch.google.com.pe/ping/RPC2',
            'http://blogsearch.google.com.sa/ping/RPC2',
            'http://blogsearch.google.com.sg/ping/RPC2',
            'http://blogsearch.google.com.tr/ping/RPC2',
            'http://blogsearch.google.com.tw/ping/RPC2',
            'http://blogsearch.google.com.ua/ping/RPC2',
            'http://blogsearch.google.com.uy/ping/RPC2',
            'http://blogsearch.google.com.vn/ping/RPC2',
            'http://blogsearch.google.de/ping/RPC2',
            'http://blogsearch.google.es/ping/RPC2',
            'http://blogsearch.google.fi/ping/RPC2',
            'http://blogsearch.google.fr/ping/RPC2',
            'http://blogsearch.google.gr/ping/RPC2',
            'http://blogsearch.google.hr/ping/RPC2',
            'http://blogsearch.google.ie/ping/RPC2',
            'http://blogsearch.google.it/ping/RPC2',
            'http://blogsearch.google.jp/ping/RPC2',
            'http://blogsearch.google.lt/ping/RPC2',
            'http://blogsearch.google.nl/ping/RPC2',
            'http://blogsearch.google.pl/ping/RPC2',
            'http://blogsearch.google.pt/ping/RPC2',
            'http://blogsearch.google.ro/ping/RPC2',
            'http://blogsearch.google.ru/ping/RPC2',
            'http://blogsearch.google.se/ping/RPC2',
            'http://blogsearch.google.sk/ping/RPC2',
            'http://blogsearch.google.us/ping/RPC2',
            'http://blogsearch.google.ca/ping/RPC2',
            'http://blogsearch.google.co.cr/ping/RPC2',
            'http://blogsearch.google.co.hu/ping/RPC2',
            'http://blogsearch.google.com.do/ping/RPC2',
            'http://blogpingr.de/ping/rpc2',
            'http://ping.pubsub.com/ping',
            'http://pingomatic.com',
            'http://blogsearch.google.lk/ping/RPC2',
            'http://blogsearch.google.ws/ping/RPC2',
            'http://blogsearch.google.vu/ping/RPC2',
            'http://blogsearch.google.vg/ping/RPC2',
            'http://blogsearch.google.tt/ping/RPC2',
            'http://blogsearch.google.to/ping/RPC2',
            'http://blogsearch.google.tm/ping/RPC2',
            'http://blogsearch.google.tl/ping/RPC2',
            'http://blogsearch.google.tk/ping/RPC2',
            'http://blogsearch.google.st/ping/RPC2',
            'http://blogsearch.google.sn/ping/RPC2',
            'http://blogsearch.google.sm/ping/RPC2',
            'http://blogsearch.google.si/ping/RPC2',
            'http://blogsearch.google.sh/ping/RPC2',
            'http://blogsearch.google.sc/ping/RPC2',
            'http://blogsearch.google.rw/ping/RPC2',
            'http://blogsearch.google.pn/ping/RPC2',
            'http://blogsearch.google.nu/ping/RPC2',
            'http://blogsearch.google.nr/ping/RPC2',
            'http://blogsearch.google.no/ping/RPC2',
            'http://blogsearch.google.mw/ping/RPC2',
            'http://blogsearch.google.mv/ping/RPC2',
            'http://blogsearch.google.mu/ping/RPC2',
            'http://blogsearch.google.ms/ping/RPC2',
            'http://blogsearch.google.mn/ping/RPC2',
            'http://blogsearch.google.md/ping/RPC2',
            'http://blogsearch.google.lu/ping/RPC2',
            'http://blogsearch.google.li/ping/RPC2',
            'http://blogsearch.google.la/ping/RPC2',
            'http://blogsearch.google.kz/ping/RPC2',
            'http://blogsearch.google.kg/ping/RPC2',
            'http://blogsearch.google.jo/ping/RPC2',
            'http://blogsearch.google.je/ping/RPC2',
            'http://blogsearch.google.is/ping/RPC2',
            'http://blogsearch.google.im/ping/RPC2',
            'http://blogsearch.google.hu/ping/RPC2',
            'http://blogsearch.google.ht/ping/RPC2',
            'http://rpc.weblogs.com/RPC2',
            'http://services.newsgator.com/ngws/xmlrpcping.aspx',
            'http://www.blogpeople.net/servlet/weblogUpdates',
            'http://blogpeople.net/ping',
            'http://pubsub.com/ping'
            );
            
            
            public $back_link_url=Array(
            
                    "http://similarsites.com/site/[url]",
                    "http://alexa.com/siteinfo/[url]",
                    "http://builtwith.com/[url]",
                    "http://siteadvisor.cn/sites/[url]/summary/",
                    "http://whois.domaintools.com/[url]",
                    "http://whoisx.co.uk/[url]",
                    "http://aboutdomain.org/info/[url]/",
                    "http://aboutus.org/[url]",
                    "http://validator.w3.org/check?uri=[url]",
                    "http://sitepricechecker.com/[url]",
                    "http://script3.prothemes.biz/[url]",
                    "http://websitevaluebot.com/[url]",
                    "http://listenarabic.com/search?q=[url]&sa=Search",
                    "http://keywordspy.com/research/search.aspx?q=[url]&tab=domain-overview",
                    "http://aboutdomain.org/backlinks/[url]/",
                    "http://who.is/whois/[url]/",
                    "http://protect-x.com/info/[url]",
                    "https://siteanalytics.compete.com/[url]/",
                    "http://sitedossier.com/site/[url]",
                    "http://wholinkstome.com/url/[url]",
                    "http://serpanalytics.com/#competitor/[url]/summary/1",
                    "http://hosts-file.net/default.asp?s=[url]",
                    "http://robtex.com/dns/[url].html",
                    "https://quantcast.com/[url]",
                    "http://toolbar.netcraft.com/site_report?url=[url]",
                    "http://aboutthedomain.com/[url]",
                    "http://websiteshadow.com/[url]",
                    "http://surcentro.com/en/info/[url]",
                    "http://onlinewebcheck.com/check.php?url=[url]",
                    "http://socialwebwatch.com/stats.php?url=[url]",
                    "http://statscrop.com/www/[url]",
                    "http://statmyweb.com/site/[url]",
                    "http://tools.quicksprout.com/analyze/[url]",
                    "http://whois.net/whois/[url]",
                    "http://iwebchk.com/reports/view/[url]",
                    "http://siteadvisor.com/sites/[url]",
                    "http://google.com/safebrowsing/diagnostic?site=[url]",
                    "https://safeweb.norton.com/report/show?url=[url]",
                    "https://mywot.com/en/scorecard/[url]",
                    "http://sitecheck.sucuri.net/results/[url]",
                    "http://sitejabber.com/search/[url]",
                    "http://avgthreatlabs.com/website-safety-reports/domain/[url]",
                    "http://siteprice.org/AnalyzeSite.aspx?url=[url]",
                    "http://similarweb.com/website/[url]",
                    "http://dnscheck.pingdom.com/?domain=[url]",
                    "http://www.myip.net/[url]",
                    "http://hqindex.org/[url]",
                    "http://hqindex.org/[url]",
                    "http://statsie.com/[url]",
                    "http://toolbar.netcraft.com/site_report?url=[url]#last_reboot",
                    "http://estibot.com/appraise.php?a=appraise&data=[url]",
                    "http://onthesamehost.com/?q=[url]",
                    
                );



    function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
        $this->CI->load->helper('my_helper');
    }


    /** Get Alexa Ranking, Traffic Rank, Reach Rank, Country Rank ****/


    public function start_scrapping($website)
        {
            $scrapping_url=$website;
            $content=$this->getContents($scrapping_url);

            echo $content;
            exit();            
           
             $found_url=$this->get_url($content);

            return $found_url;
            
        }


         public function get_url($content)
        {
            $urls=$this->extract_html_urls($content);
            return    $urls;
        }

        private function getContents($url, $proxy='')
        {
            $ch = curl_init(); // initialize curl handle
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_VERBOSE, 0);
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
            curl_setopt($ch, CURLOPT_AUTOREFERER, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 7);
            curl_setopt($ch, CURLOPT_REFERER, 'http://'.$url);
            curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
            curl_setopt($ch, CURLOPT_FAILONERROR, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirects
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
            curl_setopt($ch, CURLOPT_TIMEOUT, 50); // times out after 50s
            curl_setopt($ch, CURLOPT_POST, 0); // set POST method

            if ($proxy) {
                curl_setopt($ch, CURLOPT_PROXY, $proxy);
            }               
            
            
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_COOKIEJAR, "my_cookies.txt");
            curl_setopt($ch, CURLOPT_COOKIEFILE, "my_cookies.txt");
             
             
            $buffer = curl_exec($ch); // run the whole process
            curl_close($ch);
            return $buffer;
        }



         public function extract_html_urls($text)
        {
            $match_elements = array(
                        // HTML
                        array('element'=>'a',       'attribute'=>'href'),       // 2.0
                        array('element'=>'a',       'attribute'=>'urn'),        // 2.0
                        array('element'=>'base',    'attribute'=>'href'),       // 2.0
                        array('element'=>'form',    'attribute'=>'action'),     // 2.0
                        array('element'=>'img',     'attribute'=>'src'),        // 2.0
                        array('element'=>'link',    'attribute'=>'href'),       // 2.0

                        array('element'=>'applet',  'attribute'=>'code'),       // 3.2
                        array('element'=>'applet',  'attribute'=>'codebase'),   // 3.2
                        array('element'=>'area',    'attribute'=>'href'),       // 3.2
                        array('element'=>'body',    'attribute'=>'background'), // 3.2
                        array('element'=>'img',     'attribute'=>'usemap'),     // 3.2
                        array('element'=>'input',   'attribute'=>'src'),        // 3.2

                        array('element'=>'applet',  'attribute'=>'archive'),    // 4.01
                        array('element'=>'applet',  'attribute'=>'object'),     // 4.01
                        array('element'=>'blockquote','attribute'=>'cite'),     // 4.01
                        array('element'=>'del',     'attribute'=>'cite'),       // 4.01
                        array('element'=>'frame',   'attribute'=>'longdesc'),   // 4.01
                        array('element'=>'frame',   'attribute'=>'src'),        // 4.01
                        array('element'=>'head',    'attribute'=>'profile'),    // 4.01
                        array('element'=>'iframe',  'attribute'=>'longdesc'),   // 4.01
                        array('element'=>'iframe',  'attribute'=>'src'),        // 4.01
                        array('element'=>'img',     'attribute'=>'longdesc'),   // 4.01
                        array('element'=>'input',   'attribute'=>'usemap'),     // 4.01
                        array('element'=>'ins',     'attribute'=>'cite'),       // 4.01
                        array('element'=>'object',  'attribute'=>'archive'),    // 4.01
                        array('element'=>'object',  'attribute'=>'classid'),    // 4.01
                        array('element'=>'object',  'attribute'=>'codebase'),   // 4.01
                        array('element'=>'object',  'attribute'=>'data'),       // 4.01
                        array('element'=>'object',  'attribute'=>'usemap'),     // 4.01
                        array('element'=>'q',       'attribute'=>'cite'),       // 4.01
                        array('element'=>'script',  'attribute'=>'src'),        // 4.01

                        array('element'=>'audio',   'attribute'=>'src'),        // 5.0
                        array('element'=>'command', 'attribute'=>'icon'),       // 5.0
                        array('element'=>'embed',   'attribute'=>'src'),        // 5.0
                        array('element'=>'event-source','attribute'=>'src'),    // 5.0
                        array('element'=>'html',    'attribute'=>'manifest'),   // 5.0
                        array('element'=>'source',  'attribute'=>'src'),        // 5.0
                        array('element'=>'video',   'attribute'=>'src'),        // 5.0
                        array('element'=>'video',   'attribute'=>'poster'),     // 5.0

                        array('element'=>'bgsound', 'attribute'=>'src'),        // Extension
                        array('element'=>'body',    'attribute'=>'credits'),    // Extension
                        array('element'=>'body',    'attribute'=>'instructions'),//Extension
                        array('element'=>'body',    'attribute'=>'logo'),       // Extension
                        array('element'=>'div',     'attribute'=>'href'),       // Extension
                        array('element'=>'div',     'attribute'=>'src'),        // Extension
                        array('element'=>'embed',   'attribute'=>'code'),       // Extension
                        array('element'=>'embed',   'attribute'=>'pluginspage'),// Extension
                        array('element'=>'html',    'attribute'=>'background'), // Extension
                        array('element'=>'ilayer',  'attribute'=>'src'),        // Extension
                        array('element'=>'img',     'attribute'=>'dynsrc'),     // Extension
                        array('element'=>'img',     'attribute'=>'lowsrc'),     // Extension
                        array('element'=>'input',   'attribute'=>'dynsrc'),     // Extension
                        array('element'=>'input',   'attribute'=>'lowsrc'),     // Extension
                        array('element'=>'table',   'attribute'=>'background'), // Extension
                        array('element'=>'td',      'attribute'=>'background'), // Extension
                        array('element'=>'th',      'attribute'=>'background'), // Extension
                        array('element'=>'layer',   'attribute'=>'src'),        // Extension
                        array('element'=>'xml',     'attribute'=>'src'),        // Extension

                        array('element'=>'button',  'attribute'=>'action'),     // Forms 2.0
                        array('element'=>'datalist','attribute'=>'data'),       // Forms 2.0
                        array('element'=>'form',    'attribute'=>'data'),       // Forms 2.0
                        array('element'=>'input',   'attribute'=>'action'),     // Forms 2.0
                        array('element'=>'select',  'attribute'=>'data'),       // Forms 2.0

                        // XHTML
                        array('element'=>'html',    'attribute'=>'xmlns'),
                 
                        // WML
                        array('element'=>'access',  'attribute'=>'path'),       // 1.3
                        array('element'=>'card',    'attribute'=>'onenterforward'),// 1.3
                        array('element'=>'card',    'attribute'=>'onenterbackward'),// 1.3
                        array('element'=>'card',    'attribute'=>'ontimer'),    // 1.3
                        array('element'=>'go',      'attribute'=>'href'),       // 1.3
                        array('element'=>'option',  'attribute'=>'onpick'),     // 1.3
                        array('element'=>'template','attribute'=>'onenterforward'),// 1.3
                        array('element'=>'template','attribute'=>'onenterbackward'),// 1.3
                        array('element'=>'template','attribute'=>'ontimer'),    // 1.3
                        array('element'=>'wml',     'attribute'=>'xmlns'),      // 2.0
                    );
                 
            $match_metas = array(
                        'content-base',
                        'content-location',
                        'referer',
                        'location',
                        'refresh',
                    );
                 
                    // Extract all elements
                    if (!preg_match_all('/<([a-z][^>]*)>/iu', $text, $matches)) {
                        return array( );
                    }
            $elements = $matches[1];
            $value_pattern = '=(("([^"]*)")|([^\s]*))';
                 
                    // Match elements and attributes
                    foreach ($match_elements as $match_element) {
                        $name = $match_element['element'];
                        $attr = $match_element['attribute'];
                        $pattern = '/^' . $name . '\s.*' . $attr . $value_pattern . '/iu';
                        if ($name == 'object') {
                            $split_pattern = '/\s*/u';
                        }  // Space-separated URL list
                        elseif ($name == 'archive') {
                            $split_pattern = '/,\s*/u';
                        } // Comma-separated URL list
                        else {
                            unset($split_pattern);
                        }    // Single URL
                        foreach ($elements as $element) {
                            if (!preg_match($pattern, $element, $match)) {
                                continue;
                            }
                            $m = empty($match[3]) ? $match[4] : $match[3];
                            if (!isset($split_pattern)) {
                                $urls[$name][$attr][] = $m;
                            } else {
                                $msplit = preg_split($split_pattern, $m);
                                foreach ($msplit as $ms) {
                                    $urls[$name][$attr][] = $ms;
                                }
                            }
                        }
                    }
                 
                    // Match meta http-equiv elements
                    foreach ($match_metas as $match_meta) {
                        $attr_pattern    = '/http-equiv="?' . $match_meta . '"?/iu';
                        $content_pattern = '/content'  . $value_pattern . '/iu';
                        $refresh_pattern = '/\d*;\s*(url=)?(.*)$/iu';
                        foreach ($elements as $element) {
                            if (!preg_match('/^meta/iu', $element) ||
                                !preg_match($attr_pattern, $element) ||
                                !preg_match($content_pattern, $element, $match)) {
                                continue;
                            }
                            $m = empty($match[3]) ? $match[4] : $match[3];
                            if ($match_meta != 'refresh') {
                                $urls['meta']['http-equiv'][] = $m;
                            } elseif (preg_match($refresh_pattern, $m, $match)) {
                                $urls['meta']['http-equiv'][] = $match[2];
                            }
                        }
                    }
                 
                    // Match style attributes
                    $urls['style'] = array( );
            $style_pattern = '/style' . $value_pattern . '/iu';
            foreach ($elements as $element) {
                if (!preg_match($style_pattern, $element, $match)) {
                    continue;
                }
                $m = empty($match[3]) ? $match[4] : $match[3];
                $style_urls =$this->extract_css_urls($m);
                if (!empty($style_urls)) {
                    $urls['style'] = array_merge_recursive(
                                $urls['style'], $style_urls);
                }
            }
                 
                    // Match style bodies
                    if (preg_match_all('/<style[^>]*>(.*?)<\/style>/siu', $text, $style_bodies)) {
                        foreach ($style_bodies[1] as $style_body) {
                            $style_urls =$this->extract_css_urls($style_body);
                            if (!empty($style_urls)) {
                                $urls['style'] = array_merge_recursive(
                                    $urls['style'], $style_urls);
                            }
                        }
                    }
            if (empty($urls['style'])) {
                unset($urls['style']);
            }
                 
            return $urls;
        } 

         public function extract_css_urls($text)
        {
            $urls = array( );
 
            $url_pattern     = '(([^\\\\\'", \(\)]*(\\\\.)?)+)';
            $urlfunc_pattern = 'url\(\s*[\'"]?' . $url_pattern . '[\'"]?\s*\)';
            $pattern         = '/(' .
         '(@import\s*[\'"]' . $url_pattern     . '[\'"])' .
        '|(@import\s*'      . $urlfunc_pattern . ')'      .
        '|('                . $urlfunc_pattern . ')'      .  ')/iu';
            if (!preg_match_all($pattern, $text, $matches)) {
                return $urls;
            }
 
    // @import '...'
    // @import "..."
    foreach ($matches[3] as $match) {
        if (!empty($match)) {
            $urls['import'][] =
                preg_replace('/\\\\(.)/u', '\\1', $match);
        }
    }
 
    // @import url(...)
    // @import url('...')
    // @import url("...")
    foreach ($matches[7] as $match) {
        if (!empty($match)) {
            $urls['import'][] =
                preg_replace('/\\\\(.)/u', '\\1', $match);
        }
    }
 
    // url(...)
    // url('...')
    // url("...")
    foreach ($matches[11] as $match) {
        if (!empty($match)) {
            $urls['property'][] =
                preg_replace('/\\\\(.)/u', '\\1', $match);
        }
    }
 
            return $urls;
        }



    
}







?>