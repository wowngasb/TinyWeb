function GraphQLHelper(){
    var _this = this;
    this.DEBUG = true;
    this._log_func = (typeof console != "undefined" && typeof console.info == "function" && typeof console.warn == "function") ? {INFO: console.info.bind(console), ERROR: console.warn.bind(console)} : {};
    this.exports = {};
    
    this.formatDate = function(){
        var now = new Date(new Date().getTime());
        var year = now.getFullYear();
        var month = now.getMonth()+1;
        var date = now.getDate();
        var hour = now.getHours();
        var minute = now.getMinutes();
        if(minute < 10){
            minute = '0' + minute.toString();
        } 
        var seconds = now.getSeconds()
        if(seconds < 10){
            seconds = '0' + seconds.toString();
        }
        return year+"-"+month+"-"+date+" "+hour+":"+minute+":"+seconds;
    }
    
    this.rfcApi = function(type, url, args, success, error, log){
        var start_time = new Date().getTime();
        if( typeof CSRF_TOKEN != "undefined" && CSRF_TOKEN ){
            args.csrf = CSRF_TOKEN;
        }
        $.ajax({
            type: type,
            url: url,
            data: args,
            dataType: 'json',
            success:
                function(data) {
                    var use_time = Math.round( (new Date().getTime() - start_time) );
                    if(data.errno == 0 || typeof data.error == "undefined" ){
                        log('INFO', use_time, args, data);
                        typeof(success) == 'function' && success(data);
                    } else {
                        log('ERROR', use_time, args, data);
                        typeof(error) == 'function' && error(data);
                    }
                }
        });
    }

    
    this.exec = function(args, success, error) {
        var log = function(tag, use_time, args, data){
            var f = _this._log_func[tag]; typeof args.csrf != "undefined" && delete args.csrf;
            _this.DEBUG && f && f(_this.formatDate(), '['+tag+'] GraphQL.exec('+use_time+'ms)', 'args:', args, 'data:', data);
        }
        return _this.rfcApi('POST', '/api/GraphQL/exec' ,args, success, error, log);
    }
    this.exports.exec = this.exec;
    this.exec_args = {"query":"{hello}","variables":null};
    this.exports.exec_args = this.exec_args;

    /**
     * 过滤常见的 API参数  子类按照顺序依次调用父类此方法
     * @return array  处理后的 API 执行参数 将用于调用方法
     */
    this.beforeApi = function(args, success, error) {
        var log = function(tag, use_time, args, data){
            var f = _this._log_func[tag]; typeof args.csrf != "undefined" && delete args.csrf;
            _this.DEBUG && f && f(_this.formatDate(), '['+tag+'] GraphQL.beforeApi('+use_time+'ms)', 'args:', args, 'data:', data);
        }
        return _this.rfcApi('POST', '/api/GraphQL/beforeApi' ,args, success, error, log);
    }
    this.exports.beforeApi = this.beforeApi;
    this.beforeApi_args = null;
    this.exports.beforeApi_args = this.beforeApi_args;

    /**
     * @return null|Request
     */
    this.getRequest = function(args, success, error) {
        var log = function(tag, use_time, args, data){
            var f = _this._log_func[tag]; typeof args.csrf != "undefined" && delete args.csrf;
            _this.DEBUG && f && f(_this.formatDate(), '['+tag+'] GraphQL.getRequest('+use_time+'ms)', 'args:', args, 'data:', data);
        }
        return _this.rfcApi('POST', '/api/GraphQL/getRequest' ,args, success, error, log);
    }
    this.exports.getRequest = this.getRequest;
    this.getRequest_args = null;
    this.exports.getRequest_args = this.getRequest_args;

    /**
     * @return null|Response
     */
    this.getResponse = function(args, success, error) {
        var log = function(tag, use_time, args, data){
            var f = _this._log_func[tag]; typeof args.csrf != "undefined" && delete args.csrf;
            _this.DEBUG && f && f(_this.formatDate(), '['+tag+'] GraphQL.getResponse('+use_time+'ms)', 'args:', args, 'data:', data);
        }
        return _this.rfcApi('POST', '/api/GraphQL/getResponse' ,args, success, error, log);
    }
    this.exports.getResponse = this.getResponse;
    this.getResponse_args = null;
    this.exports.getResponse_args = this.getResponse_args;
}

if( typeof window.GraphQL == "undefined" ){
    window.GraphQL = new GraphQLHelper();
    if(typeof exports == "undefined"){
        var exports = {};
    }
    for(var key in GraphQL.exports){
        exports[key] = GraphQL.exports[key];
    }
}