function OrmHelper(){
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

    
    this.autoHelp = function(args, success, error) {
        var log = function(tag, use_time, args, data){
            var f = _this._log_func[tag]; typeof args.csrf != "undefined" && delete args.csrf;
            _this.DEBUG && f && f(_this.formatDate(), '['+tag+'] Orm.autoHelp('+use_time+'ms)', 'args:', args, 'data:', data);
        }
        return _this.rfcApi('POST', '/api/Orm/autoHelp' ,args, success, error, log);
    }
    this.exports.autoHelp = this.autoHelp;
    this.autoHelp_args = null;
    this.exports.autoHelp_args = this.autoHelp_args;

    
    this.aTest = function(args, success, error) {
        var log = function(tag, use_time, args, data){
            var f = _this._log_func[tag]; typeof args.csrf != "undefined" && delete args.csrf;
            _this.DEBUG && f && f(_this.formatDate(), '['+tag+'] Orm.aTest('+use_time+'ms)', 'args:', args, 'data:', data);
        }
        return _this.rfcApi('POST', '/api/Orm/aTest' ,args, success, error, log);
    }
    this.exports.aTest = this.aTest;
    this.aTest_args = null;
    this.exports.aTest_args = this.aTest_args;

    /**
     * @param string $db_name
     * @param string $table_name
     * @return BuilderHelper
     * @throws ApiParamsError
     */
    this.table = function(args, success, error) {
        var log = function(tag, use_time, args, data){
            var f = _this._log_func[tag]; typeof args.csrf != "undefined" && delete args.csrf;
            _this.DEBUG && f && f(_this.formatDate(), '['+tag+'] Orm.table('+use_time+'ms)', 'args:', args, 'data:', data);
        }
        return _this.rfcApi('POST', '/api/Orm/table' ,args, success, error, log);
    }
    this.exports.table = this.table;
    this.table_args = {"table_name":"?","db_name":null};
    this.exports.table_args = this.table_args;

    /**
     * Pluck a single column's value from the first result of a query.
     * @param string $column
     * @param array $queries
     * @return mixed
     */
    this.pluck = function(args, success, error) {
        var log = function(tag, use_time, args, data){
            var f = _this._log_func[tag]; typeof args.csrf != "undefined" && delete args.csrf;
            _this.DEBUG && f && f(_this.formatDate(), '['+tag+'] Orm.pluck('+use_time+'ms)', 'args:', args, 'data:', data);
        }
        return _this.rfcApi('POST', '/api/Orm/pluck' ,args, success, error, log);
    }
    this.exports.pluck = this.pluck;
    this.pluck_args = {"column":"?","queries":[]};
    this.exports.pluck_args = this.pluck_args;

    /**
     * Get an array with the values of a given column.
     *
     * @param  string $column
     * @param array $queries
     * @return array
     */
    this.lists = function(args, success, error) {
        var log = function(tag, use_time, args, data){
            var f = _this._log_func[tag]; typeof args.csrf != "undefined" && delete args.csrf;
            _this.DEBUG && f && f(_this.formatDate(), '['+tag+'] Orm.lists('+use_time+'ms)', 'args:', args, 'data:', data);
        }
        return _this.rfcApi('POST', '/api/Orm/lists' ,args, success, error, log);
    }
    this.exports.lists = this.lists;
    this.lists_args = {"column":"?","queries":[]};
    this.exports.lists_args = this.lists_args;

    /**
     * Execute the query as a "select" statement.
     *
     * @param int $skip
     * @param int $take
     * @param array $orderBy
     * @param array $columns
     * @param array $queries
     * @return array|static[]
     */
    this.get = function(args, success, error) {
        var log = function(tag, use_time, args, data){
            var f = _this._log_func[tag]; typeof args.csrf != "undefined" && delete args.csrf;
            _this.DEBUG && f && f(_this.formatDate(), '['+tag+'] Orm.get('+use_time+'ms)', 'args:', args, 'data:', data);
        }
        return _this.rfcApi('POST', '/api/Orm/get' ,args, success, error, log);
    }
    this.exports.get = this.get;
    this.get_args = {"skip":0,"take":20,"orderBy":[],"columns":["*"],"queries":[]};
    this.exports.get_args = this.get_args;

    /**
     * Get a record from the database by id.
     *
     * @param int $id
     * @return array
     */
    this.getItem = function(args, success, error) {
        var log = function(tag, use_time, args, data){
            var f = _this._log_func[tag]; typeof args.csrf != "undefined" && delete args.csrf;
            _this.DEBUG && f && f(_this.formatDate(), '['+tag+'] Orm.getItem('+use_time+'ms)', 'args:', args, 'data:', data);
        }
        return _this.rfcApi('POST', '/api/Orm/getItem' ,args, success, error, log);
    }
    this.exports.getItem = this.getItem;
    this.getItem_args = {"id":"?"};
    this.exports.getItem_args = this.getItem_args;

    /**
     * Update a record in the database.
     *
     * @param array $values
     * @param array $queries
     * @return int
     * @throws ApiParamsError
     */
    this.update = function(args, success, error) {
        var log = function(tag, use_time, args, data){
            var f = _this._log_func[tag]; typeof args.csrf != "undefined" && delete args.csrf;
            _this.DEBUG && f && f(_this.formatDate(), '['+tag+'] Orm.update('+use_time+'ms)', 'args:', args, 'data:', data);
        }
        return _this.rfcApi('POST', '/api/Orm/update' ,args, success, error, log);
    }
    this.exports.update = this.update;
    this.update_args = {"values":["?","..."],"queries":[]};
    this.exports.update_args = this.update_args;

    /**
     * update a record from the database by id.
     *
     * @param $id
     * @param array $values
     * @return int
     * @throws OrmStartUpError
     * @throws ApiParamsError
     */
    this.updateItem = function(args, success, error) {
        var log = function(tag, use_time, args, data){
            var f = _this._log_func[tag]; typeof args.csrf != "undefined" && delete args.csrf;
            _this.DEBUG && f && f(_this.formatDate(), '['+tag+'] Orm.updateItem('+use_time+'ms)', 'args:', args, 'data:', data);
        }
        return _this.rfcApi('POST', '/api/Orm/updateItem' ,args, success, error, log);
    }
    this.exports.updateItem = this.updateItem;
    this.updateItem_args = {"id":"?","values":["?","..."]};
    this.exports.updateItem_args = this.updateItem_args;

    /**
     * Increment a column's value by a given amount.
     *
     * @param  string $column
     * @param  int $amount
     * @param  array $extra
     * @param array $queries
     * @return int
     */
    this.increment = function(args, success, error) {
        var log = function(tag, use_time, args, data){
            var f = _this._log_func[tag]; typeof args.csrf != "undefined" && delete args.csrf;
            _this.DEBUG && f && f(_this.formatDate(), '['+tag+'] Orm.increment('+use_time+'ms)', 'args:', args, 'data:', data);
        }
        return _this.rfcApi('POST', '/api/Orm/increment' ,args, success, error, log);
    }
    this.exports.increment = this.increment;
    this.increment_args = {"column":"?","amount":1,"extra":[],"queries":[]};
    this.exports.increment_args = this.increment_args;

    /**
     * Decrement a column's value by a given amount.
     *
     * @param  string $column
     * @param  int $amount
     * @param  array $extra
     * @param array $queries
     * @return int
     */
    this.decrement = function(args, success, error) {
        var log = function(tag, use_time, args, data){
            var f = _this._log_func[tag]; typeof args.csrf != "undefined" && delete args.csrf;
            _this.DEBUG && f && f(_this.formatDate(), '['+tag+'] Orm.decrement('+use_time+'ms)', 'args:', args, 'data:', data);
        }
        return _this.rfcApi('POST', '/api/Orm/decrement' ,args, success, error, log);
    }
    this.exports.decrement = this.decrement;
    this.decrement_args = {"column":"?","amount":1,"extra":[],"queries":[]};
    this.exports.decrement_args = this.decrement_args;

    /**
     * Delete a record from the database.
     *
     * @param array $queries
     * @return array
     */
    this.delete = function(args, success, error) {
        var log = function(tag, use_time, args, data){
            var f = _this._log_func[tag]; typeof args.csrf != "undefined" && delete args.csrf;
            _this.DEBUG && f && f(_this.formatDate(), '['+tag+'] Orm.delete('+use_time+'ms)', 'args:', args, 'data:', data);
        }
        return _this.rfcApi('POST', '/api/Orm/delete' ,args, success, error, log);
    }
    this.exports.delete = this.delete;
    this.delete_args = {"queries":[]};
    this.exports.delete_args = this.delete_args;

    /**
     * Delete a record from the database by id.
     *
     * @param $id
     * @return int
     */
    this.deleteItem = function(args, success, error) {
        var log = function(tag, use_time, args, data){
            var f = _this._log_func[tag]; typeof args.csrf != "undefined" && delete args.csrf;
            _this.DEBUG && f && f(_this.formatDate(), '['+tag+'] Orm.deleteItem('+use_time+'ms)', 'args:', args, 'data:', data);
        }
        return _this.rfcApi('POST', '/api/Orm/deleteItem' ,args, success, error, log);
    }
    this.exports.deleteItem = this.deleteItem;
    this.deleteItem_args = {"id":"?"};
    this.exports.deleteItem_args = this.deleteItem_args;

    /**
     * Insert many records into the database.
     *
     * @param  array $values
     * @return array
     */
    this.insertMany = function(args, success, error) {
        var log = function(tag, use_time, args, data){
            var f = _this._log_func[tag]; typeof args.csrf != "undefined" && delete args.csrf;
            _this.DEBUG && f && f(_this.formatDate(), '['+tag+'] Orm.insertMany('+use_time+'ms)', 'args:', args, 'data:', data);
        }
        return _this.rfcApi('POST', '/api/Orm/insertMany' ,args, success, error, log);
    }
    this.exports.insertMany = this.insertMany;
    this.insertMany_args = {"values":["?","..."]};
    this.exports.insertMany_args = this.insertMany_args;

    /**
     * Insert a new record and get the value of the primary key.
     *
     * @param  array $values
     * @return int
     */
    this.insertItem = function(args, success, error) {
        var log = function(tag, use_time, args, data){
            var f = _this._log_func[tag]; typeof args.csrf != "undefined" && delete args.csrf;
            _this.DEBUG && f && f(_this.formatDate(), '['+tag+'] Orm.insertItem('+use_time+'ms)', 'args:', args, 'data:', data);
        }
        return _this.rfcApi('POST', '/api/Orm/insertItem' ,args, success, error, log);
    }
    this.exports.insertItem = this.insertItem;
    this.insertItem_args = {"values":["?","..."]};
    this.exports.insertItem_args = this.insertItem_args;

    /**
     * Execute the query and get the first result.
     *
     * @param  array $columns
     * @param array $queries
     * @return array
     */
    this.first = function(args, success, error) {
        var log = function(tag, use_time, args, data){
            var f = _this._log_func[tag]; typeof args.csrf != "undefined" && delete args.csrf;
            _this.DEBUG && f && f(_this.formatDate(), '['+tag+'] Orm.first('+use_time+'ms)', 'args:', args, 'data:', data);
        }
        return _this.rfcApi('POST', '/api/Orm/first' ,args, success, error, log);
    }
    this.exports.first = this.first;
    this.first_args = {"columns":["*"],"queries":[]};
    this.exports.first_args = this.first_args;

    /**
     * Retrieve the "count" result of the query.
     *
     * @param array $queries
     * @return int
     */
    this.count = function(args, success, error) {
        var log = function(tag, use_time, args, data){
            var f = _this._log_func[tag]; typeof args.csrf != "undefined" && delete args.csrf;
            _this.DEBUG && f && f(_this.formatDate(), '['+tag+'] Orm.count('+use_time+'ms)', 'args:', args, 'data:', data);
        }
        return _this.rfcApi('POST', '/api/Orm/count' ,args, success, error, log);
    }
    this.exports.count = this.count;
    this.count_args = {"queries":[]};
    this.exports.count_args = this.count_args;

    /**
     * Retrieve the maximum value of a given column.
     *
     * @param  string $column
     * @param array $queries
     * @return float|int
     */
    this.max = function(args, success, error) {
        var log = function(tag, use_time, args, data){
            var f = _this._log_func[tag]; typeof args.csrf != "undefined" && delete args.csrf;
            _this.DEBUG && f && f(_this.formatDate(), '['+tag+'] Orm.max('+use_time+'ms)', 'args:', args, 'data:', data);
        }
        return _this.rfcApi('POST', '/api/Orm/max' ,args, success, error, log);
    }
    this.exports.max = this.max;
    this.max_args = {"column":"?","queries":[]};
    this.exports.max_args = this.max_args;

    /**
     * Retrieve the minimum value of a given column.
     *
     * @param  string $column
     * @param array $queries
     * @return float|int
     */
    this.min = function(args, success, error) {
        var log = function(tag, use_time, args, data){
            var f = _this._log_func[tag]; typeof args.csrf != "undefined" && delete args.csrf;
            _this.DEBUG && f && f(_this.formatDate(), '['+tag+'] Orm.min('+use_time+'ms)', 'args:', args, 'data:', data);
        }
        return _this.rfcApi('POST', '/api/Orm/min' ,args, success, error, log);
    }
    this.exports.min = this.min;
    this.min_args = {"column":"?","queries":[]};
    this.exports.min_args = this.min_args;

    /**
     * Retrieve the average of the values of a given column.
     *
     * @param  string $column
     * @param array $queries
     * @return float|int
     */
    this.avg = function(args, success, error) {
        var log = function(tag, use_time, args, data){
            var f = _this._log_func[tag]; typeof args.csrf != "undefined" && delete args.csrf;
            _this.DEBUG && f && f(_this.formatDate(), '['+tag+'] Orm.avg('+use_time+'ms)', 'args:', args, 'data:', data);
        }
        return _this.rfcApi('POST', '/api/Orm/avg' ,args, success, error, log);
    }
    this.exports.avg = this.avg;
    this.avg_args = {"column":"?","queries":[]};
    this.exports.avg_args = this.avg_args;

    /**
     * Retrieve the sum of the values of a given column.
     *
     * @param  string $column
     * @param array $queries
     * @return float|int
     */
    this.sum = function(args, success, error) {
        var log = function(tag, use_time, args, data){
            var f = _this._log_func[tag]; typeof args.csrf != "undefined" && delete args.csrf;
            _this.DEBUG && f && f(_this.formatDate(), '['+tag+'] Orm.sum('+use_time+'ms)', 'args:', args, 'data:', data);
        }
        return _this.rfcApi('POST', '/api/Orm/sum' ,args, success, error, log);
    }
    this.exports.sum = this.sum;
    this.sum_args = {"column":"?","queries":[]};
    this.exports.sum_args = this.sum_args;
}

if( typeof window.Orm == "undefined" ){
    window.Orm = new OrmHelper();
    if(typeof exports == "undefined"){
        var exports = {};
    }
    for(var key in Orm.exports){
        exports[key] = Orm.exports[key];
    }
}