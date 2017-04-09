function AdminTestHelper(){
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

    
    this.testDb = function(args, success, error) {
        var log = function(tag, use_time, args, data){
            var f = _this._log_func[tag]; typeof args.csrf != "undefined" && delete args.csrf;
            _this.DEBUG && f && f(_this.formatDate(), '['+tag+'] AdminTest.testDb('+use_time+'ms)', 'args:', args, 'data:', data);
        }
        return _this.rfcApi('POST', '/api/AdminTest/testDb' ,args, success, error, log);
    }
    this.exports.testDb = this.testDb;
    this.testDb_args = {"admin_id":"?"};
    this.exports.testDb_args = this.testDb_args;

    
    this.testDb2 = function(args, success, error) {
        var log = function(tag, use_time, args, data){
            var f = _this._log_func[tag]; typeof args.csrf != "undefined" && delete args.csrf;
            _this.DEBUG && f && f(_this.formatDate(), '['+tag+'] AdminTest.testDb2('+use_time+'ms)', 'args:', args, 'data:', data);
        }
        return _this.rfcApi('POST', '/api/AdminTest/testDb2' ,args, success, error, log);
    }
    this.exports.testDb2 = this.testDb2;
    this.testDb2_args = {"room_id":"?"};
    this.exports.testDb2_args = this.testDb2_args;

    /**
     * @param string $name
     * @param int $id
     * @return array
     * @throws ApiParamsError
     */
    this.testApiFirst = function(args, success, error) {
        var log = function(tag, use_time, args, data){
            var f = _this._log_func[tag]; typeof args.csrf != "undefined" && delete args.csrf;
            _this.DEBUG && f && f(_this.formatDate(), '['+tag+'] AdminTest.testApiFirst('+use_time+'ms)', 'args:', args, 'data:', data);
        }
        return _this.rfcApi('POST', '/api/AdminTest/testApiFirst' ,args, success, error, log);
    }
    this.exports.testApiFirst = this.testApiFirst;
    this.testApiFirst_args = {"name":"?","id":123};
    this.exports.testApiFirst_args = this.testApiFirst_args;
}

if( typeof window.AdminTest == "undefined" ){
    window.AdminTest = new AdminTestHelper();
    if(typeof exports == "undefined"){
        var exports = {};
    }
    for(var key in AdminTest.exports){
        exports[key] = AdminTest.exports[key];
    }
}