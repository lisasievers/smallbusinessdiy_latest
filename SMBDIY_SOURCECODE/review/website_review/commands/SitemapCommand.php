<?php
set_time_limit(0);
class SitemapCommand extends CConsoleCommand {
    protected $urlCount = 50000;
    protected $sitemapDir;
    protected $sitemapFile;

    public function init() {
        $this->sitemapDir = Yii::app()->basePath . '/../sitemap/';
        $this->sitemapFile = Yii::app()->basePath. '/../sitemap.xml';
        if(!is_writable($this->sitemapDir) OR !is_writable($this->sitemapFile)) {
            throw new Exception ("Make sure {$this->sitemapDir} and {$this->sitemapFile} have writable permissions");
        }
    }

    public function actionIndex() {
        $languages = Yii::app()->params['languages'];
        if(!$languages) {
            $lang = Yii::app()->language;
            $languages[$lang] = $lang;
        }

        $date = date('c', time());
        $this->cleanSitemapDirectory();

        Yii::app()->db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
        $dataReader = Yii::app()->db->createCommand()
            ->select('domain, modified')
            ->from('{{website}}')
            ->query();

        $sitemapCounter = $urlCounter = 0;
        $sitemapIndexDOM = $this->createSitemapIndexDOM();

        $sitemapDOM = $this->createSitemapDOM();
        $sitemapDoc = $sitemapDOM["document"];
        $sitemapUrlset = $sitemapDOM["urlset"];

        while(($website=$dataReader->read())!==false) {
            foreach($languages as $id => $language) {
                $link = Yii::app()->getRequest()->getHostInfo() . Yii::app() -> urlManager -> createUrl("websitestat/generateHTML", array(
                        "domain" => CHtml::encode($website['domain']),
                        "language" => $id,
                    ));
                $lastmod = date('c', strtotime($website['modified']));
                $urlDOM = $sitemapDoc->createElement('url');
                $urlDOM->appendChild($sitemapDoc->createElement('loc'))->appendChild($sitemapDoc->createTextNode($link));
                $urlDOM->appendChild($sitemapDoc->createElement('lastmod'))->appendChild($sitemapDoc->createTextNode($lastmod));
                $urlDOM->appendChild($sitemapDoc->createElement('changefreq'))->appendChild($sitemapDoc->createTextNode('daily'));
                $sitemapUrlset->appendChild($urlDOM);
                $urlCounter++;
                if($urlCounter == $this->urlCount) {
					if($this->saveGz($sitemapCounter, $sitemapDoc->saveXML())) {
						$sitemap = $sitemapIndexDOM["document"]->createElement('sitemap');
						$sitemapURL = Yii::app()->getRequest()->getHostInfo().Yii::app()->getRequest()->getBaseUrl()."/sitemap/sitemap$sitemapCounter.xml.gz";
						$sitemap->appendChild($sitemapIndexDOM["document"]->createElement('loc'))->appendChild($sitemapIndexDOM["document"]->createTextNode($sitemapURL));
						$sitemap->appendChild($sitemapIndexDOM["document"]->createElement('lastmod'))->appendChild($sitemapIndexDOM["document"]->createTextNode($date));
						$sitemapIndexDOM["index"] -> appendChild($sitemap);
					}
					
                    $sitemapDOM = $this->createSitemapDOM();
                    $sitemapDoc = $sitemapDOM["document"];
                    $sitemapUrlset = $sitemapDOM["urlset"];

                    $sitemapCounter++;
                    $urlCounter = 0;
                }
            }
        }
        if($sitemapUrlset->hasChildNodes()) {
            if($this->saveGz($sitemapCounter, $sitemapDoc->saveXML())) {
				$sitemap = $sitemapIndexDOM["document"]->createElement('sitemap');
				$sitemapURL = Yii::app()->getRequest()->getHostInfo().Yii::app()->getRequest()->getBaseUrl()."/sitemap/sitemap$sitemapCounter.xml.gz";
				$sitemap->appendChild($sitemapIndexDOM["document"]->createElement('loc'))->appendChild($sitemapIndexDOM["document"]->createTextNode($sitemapURL));
				$sitemap->appendChild($sitemapIndexDOM["document"]->createElement('lastmod'))->appendChild($sitemapIndexDOM["document"]->createTextNode($date));
				$sitemapIndexDOM["index"] -> appendChild($sitemap);
			}
        }
        file_put_contents(Yii::app() -> basePath . "/../sitemap.xml", $sitemapIndexDOM["document"]->saveXML(), LOCK_EX);
        return 0;
    }
	
	protected function saveGz($sitemapNr, $sitemapString) {
		$sitemap = $this->sitemapDir."sitemap$sitemapNr.xml.gz";
		if(!$fp = gzopen($sitemap, 'w9')) {
			return false;
		}
		gzwrite($fp, $sitemapString);
		gzclose($fp);
		return true;
	}
	
    protected function createSitemapIndexDOM() {
        $xml = new DOMDocument('1.0', 'UTF-8');
        $sitemapIndex = $xml->createElement('sitemapindex');
        $attrNode = $xml->createAttribute('xmlns');
        $attrNode->value = 'http://www.sitemaps.org/schemas/sitemap/0.9';
        $xml->appendChild($sitemapIndex);
        $sitemapIndex->appendChild($attrNode);
        return array(
            "document" => $xml,
            "index" => $sitemapIndex,
        );
    }

    protected function createSitemapDOM() {
        $document = new DOMDocument('1.0', 'UTF-8');
        $urlsetNode = $document->createElement('urlset');
        $xmlnsNode = $document->createAttribute('xmlns');
        $xmlnsNode->value = 'http://www.sitemaps.org/schemas/sitemap/0.9';
        $document->appendChild($urlsetNode);
        $urlsetNode->appendChild($xmlnsNode);
        return array(
            "document" => $document,
            "urlset" => $urlsetNode,
        );
    }

    public function actionOld() {
        $command = Yii::app() -> db -> createCommand();
        $total = $command
            -> select('count(*)')
            -> from ('{{website}}')
            -> queryScalar();

        $totalSitemaps = ceil($total / $this->urlCount);
        if(!$totalSitemaps) {
            return 0;
        }
        $this->sitemapIndex($totalSitemaps);
    }

    protected function sitemapIndex($count) {
        $xml = new DOMDocument('1.0', 'UTF-8');

        $sitemapIndex = $xml->createElement('sitemapindex');
        $attrNode = $xml->createAttribute('xmlns');
        $attrNode->value = 'http://www.sitemaps.org/schemas/sitemap/0.9';
        $sitemapIndex->appendChild($attrNode);

        $date = date('c', time());

        $this->cleanSitemapDirectory();

        for($i = 0; $i < $count; $i++) {
            $sitemap = $xml->createElement('sitemap');
            $url = Yii::app()->getRequest()->getHostInfo().Yii::app()->getRequest()->getBaseUrl()."/sitemap/sitemap$i.xml";

            $this->createSitemap($i);

            $sitemap->appendChild($xml->createElement('loc'))->appendChild($xml->createTextNode($url));
            $sitemap->appendChild($xml->createElement('lastmod'))->appendChild($xml->createTextNode($date));

            $sitemapIndex -> appendChild($sitemap);
        }

        $newNode = $xml->appendChild($sitemapIndex);
        file_put_contents(Yii::app() -> basePath . "/../sitemap.xml", $xml->saveXML(), LOCK_EX);
        return 0;
    }

    protected function createSitemap($i) {
        $xml = new DOMDocument('1.0', 'UTF-8');
        $urlset = $xml->createElement('urlset');
        $attrNode = $xml->createAttribute('xmlns');
        $attrNode->value = 'http://www.sitemaps.org/schemas/sitemap/0.9';
        $xml->appendChild($urlset);
        $urlset->appendChild($attrNode);

        $websites = Yii::app()->db->createCommand()
            ->select('domain, modified')
            ->from('{{website}}')
            ->limit($this->urlCount)
            ->offset($i * $this->urlCount)
            ->queryAll();
        foreach($websites as $website) {
            $link = Yii::app()->getRequest()->getHostInfo() . Yii::app() -> urlManager -> createUrl("websitestat/generateHTML", array(
                    "domain" => CHtml::encode($website['domain']),
                ));
            $lastmod = date('c', strtotime($website['modified']));
            $url = $xml->createElement('url');
            $url->appendChild($xml->createElement('loc'))->appendChild($xml->createTextNode($link));
            $url->appendChild($xml->createElement('lastmod'))->appendChild($xml->createTextNode($lastmod));
            $url->appendChild($xml->createElement('changefreq'))->appendChild($xml->createTextNode('daily'));
            $urlset->appendChild($url);
        }
        file_put_contents($this->sitemapDir."sitemap$i.xml", $xml->saveXML(), LOCK_EX);
    }

    protected function cleanSitemapDirectory() {
        if(!$files = glob($this->sitemapDir.'*')) {
            return true;
        }
        foreach($files as $file){
            if(is_file($file))
                unlink($file);
        }
    }
}