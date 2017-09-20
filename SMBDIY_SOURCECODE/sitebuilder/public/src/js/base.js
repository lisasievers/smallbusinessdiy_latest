function dynamicThumbnail(url) {
    $.each(url, function(key, url) {
        var onReady = function(img, downloadUrl) {
            img.attr("src", downloadUrl);
            //console.log(downloadUrl);
            img.on("error", function() {
                onError(img);
            });
        };

        var onError = function(img) {
            img.attr("src", _global.baseUrl + "/img/not-available.png");
        };

        var image = $('#thumb_'+key);
        if(_global.proxyImage == 1) {
            var pp = new PagePeekerHelper(image, url, onReady, onError);
            pp.poll();
        } else {
            onReady(image, url);
        }
    });
}

$(document).ready(function(){
    $("a.disabled, li.disabled a").click(function(){
        return false;
    })
})

// Constructor
function PagePeekerHelper(image, url, onReady, onError) {
    $.ajaxSetup({ cache: false });
    this.proxy = _global.baseUrl+'/index.php/proxy?url={url}',
        this.url = url;
    this.parsed = this.parseUrl(url);
    this.onReady = onReady;
    this.onError = onError;
    this.image = image;
    this.pollTime = 10; // In seconds
    this.execLimit = 5; // If after x requests PP willn't response with status "Ready", then clear interval to avoid ddos attack.
};

PagePeekerHelper.prototype.parseUrl = function(url) {
    var parser = document.createElement('a'),
        searchObject = {},
        queries, split, i;
    // Let the browser do the work
    parser.href = url;
    // Convert query string to object
    queries = parser.search.replace(/^\?/, '').split('&');
    for(i = 0; i < queries.length; i++ ) {
        split = queries[i].split('=');
        searchObject[split[0]] = split[1];
    }
    return {
        protocol: parser.protocol,
        host: parser.host,
        hostname: parser.hostname,
        port: parser.port,
        pathname: parser.pathname,
        search: parser.search,
        searchObject: searchObject,
        hash: parser.hash
    };
};

PagePeekerHelper.prototype.getEntryPoint = function() {
    var host = this.parsed.hostname || 'free.pagepeeker.com';
    return host.split('.')[0];
};

PagePeekerHelper.prototype.conf = {
    reset: 'http://{entrypoint}.pagepeeker.com/v2/thumbs.php?size={size}&refresh=1&url={url}',
    poll: 'http://{entrypoint}.pagepeeker.com/v2/thumbs_ready.php?size={size}&url={url}',
    download: 'http://{entrypoint}.pagepeeker.com/v2/thumbs.php?size={size}&url={url}'
};

PagePeekerHelper.prototype.prepare = function(str, replacement) {
    for (var i in replacement) {
        str = str.replace(i, replacement[i]);
    }
    return str;
};

PagePeekerHelper.prototype.poll = function() {
    var self = this,
        size = this.parsed.searchObject.size || 'm',
        url = this.parsed.searchObject.url || '',
        params = {
            //'{entrypoint}': this.getEntryPoint(),
            '{entrypoint}': 'free',
            '{size}': size,
            '{url}': url
        },
        resetUrl = this.prepare(this.conf.reset, params),
        pollUrl = this.prepare(this.conf.poll, params),
        downloadUrl = this.prepare(this.conf.download, params),
        proxyReset = this.prepare(this.proxy, {
            '{url}': encodeURIComponent(resetUrl)
        }),
        proxyPoll = this.prepare(this.proxy, {
            '{url}': encodeURIComponent(pollUrl)
        }),
        proxyDownload = this.prepare(this.proxy, {
            '{url}': encodeURIComponent(downloadUrl)
        }),
        limit = this.execLimit,
        i = 0,
        isFirstCall = true;

    // Flush the image
    $.get(proxyReset, function() {
        //console.log("Reseting " + url);

        var pollUntilReady = function(cb) {
            //console.log("Polling " + url + " " + (i + 1) + " times");

            $.get(proxyPoll, function(data) {
                var isReady = data.IsReady || 0;
                if(isReady) {
                    //console.log("The " + url + " is ready: " + isReady);
                    self.onReady.apply(self, [self.image, downloadUrl]);
                    return true;
                }
                cb();
            }).fail(function() {
                //console.log('Failed to request local proxy script. Clearing the timeout');
                self.onError.apply(self, [self.image]);
            });
        };


        (function pollThumbnail() {
            var timeout = isFirstCall ? 0 : self.pollTime * 1000;
            setTimeout(function() {
                pollUntilReady(function() {
                    //console.log("Async " + url + " has done");
                    isFirstCall = false;
                    i++;
                    if(i < limit) {
                        pollThumbnail();
                    } else {
                        //console.log("Reached limit of reuqests for " + url);
                        self.onError.apply(self, [self.image]);
                    }
                });
            }, timeout);
        })();

    }).fail(function() {
        self.onError.apply(self, [self.image]);
    });
};
