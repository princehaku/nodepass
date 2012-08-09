var http = require('http');
var https = require('https');
var fs = require('fs');
        
var nodeproxy = {
    httpsOptions : {
        key: 'test/fixtures/keys/agent2-key.pem',
        cert: 'test/fixtures/keys/agent2-cert.pem'
    },
    init : function (){
        this.initHttpProxy();
    //this.initHttpsProxy();
    },
    initHttpProxy : function (){
        var self = this;
        http.createServer(function (req, res) {
            self.handleRequest(req, res);
        }).listen(7070);
        console.log('Http Proxy running at port 7070');
    },
    initHttpsProxy : function (){
        https.createServer(this.httpsOptions, function (req, res) {
            self.handleRequest(req, res);
        }).listen(8000);
        console.log('Https Proxy running at port 7070');
    },
    handleRequest : function (req, res){
        console.log("Request For " + req.url);
        var reqData = null;
        req.on('data', function(chunk) {
            reqData = chunk.toString();
        });
        req.on('error', function(e) {
            console.log('problem with request: ' + e.message);
        });
        req.on('end', function(chunk) {
            //console.log(req.headers);
            var options = {
                host: '127.0.0.1',
                port: 80,
                path: '/nodepass/remotenode/proxy.php',
                method: 'POST'
            };
            req.headers.url = req.url;
            req.headers.reqData = reqData;
            postData = "p=" + encodeURIComponent(JSON.stringify(req.headers));
            // console.log(postData);
            options.headers = {
                "Content-Type" : "application/x-www-form-urlencoded",
                "Content-length" : postData.length
            };
            var req2 = http.request(options, function(res2) {
                console.log('STATUS: ' + res2.statusCode);
                res.writeHead(res2.statusCode, res2.headers);
                res2.on('data', function (chunk) {
                    res.write(chunk);
                });
                res2.on('end', function (chunk) {
                    res.end();
                });
            });
            req2.on('error', function(e) {
                console.log('problem with request: ' + e.message);
            });
            
            if(options.method == 'POST' && postData !=null) {
                req2.write(postData);
            }
            req2.end();
           
        });

    }
}

nodeproxy.init();