<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/7 0007
 * Time: 14:16
 */

namespace model\sys;

use \Exception;

/**
 * @__version__ = '0.6.5'
 * __all__ = ['Schema', 'And', 'Or', 'Regex', 'Optional', 'Use', 'SchemaError', 'SchemaWrongKeyError', 'SchemaMissingKeyError', 'SchemaUnexpectedTypeError']
 */

function class_map()
{
    /**
     * Error during Schema validation.
     */
    class SchemaError extends \Exception
    {
        public $autos = [];
        public $errors = [];

        /**
         * SchemaError constructor.
         * @param string|array $autos
         * @param string|array $errors
         */
        public function __construct($autos, $errors)
        {
            $this->autos = is_array($autos) ? $autos : [$autos];
            $this->errors = is_array($errors) ? $errors : [$errors];
            parent::__construct($this->_message(), 101);
        }

        /**
         * Removes duplicates values in auto and error list.
         */
        private function _message()
        {
            /**
             * Utility function that removes duplicate.
             * @param $seq
             * @return array
             */
            function uniq(array $seq)
            {
                $seen = [];
                foreach ($seq as $item) {  # This way removes duplicates while preserving the order.
                    if (!is_null($item) && !in_array($item, $seen)) {
                        $seen[] = $item;
                    }
                }
                return $seen;
            }

            $data_set = uniq($this->autos);
            $error_list = uniq($this->errors);
            return join("\n", !empty($error_list) ? $error_list : $data_set);
        }
    }

    /**
     * Error Should be raised when an unexpected key is detected within the data set being.
     */
    class SchemaWrongKeyError extends SchemaError
    {
    }

    /**
     * Error should be raised when a mandatory key is not found within the data set being vaidated.
     */
    class SchemaMissingKeyError extends SchemaError
    {
    }

    /**
     * Error should be raised when a type mismatch is detected within the data set being validated.
     */
    class SchemaUnexpectedTypeError extends SchemaError
    {
    }

    /**
     * Utility function to combine validation directives in AND Boolean fashion.
     */
    class _And
    {
        protected $_args = [];
        protected $_error = null;
        protected $_schema_func = null;

        /**
         * _And constructor.
         * @param mixed $args
         * @param mixed $error
         * @param \Closure|null $schema_func
         */
        public function __construct($args, $error = null, \Closure $schema_func = null)
        {
            $this->_args = $args;
            $this->_error = $error;
            # You can pass your inherited Schema class.
            $this->_schema_func = !empty($schema_func) ? $schema_func : function ($conf, $error = '') {
                return new Schema($conf, $error);
            };
        }

        public function __toString()
        {
            return __CLASS__ . "(" . strval($this->_args) . ")";
        }

        /**
         * Validate data using defined sub schema/expressions ensuring all values are valid.
         * @param mixed $data to be validated with sub defined schemas.
         * @return mixed returns validated data
         */
        public function validate($data)
        {
            $_schema_func = $this->_schema_func;
            foreach ($this->_args as $arg) {
                /** @var Schema $_schema_obj */
                $_schema_obj = $_schema_func($arg, $this->_error);
                $data = $_schema_obj->validate($data);
            }
            return $data;
        }

    }

    /**
     * Utility function to combine validation directives in a OR Boolean fashion.
     */
    class _Or extends _And
    {

        /**
         * Validate data using sub defined schema/expressions ensuring at least one value is valid.
         * @param mixed $data data to be validated by provided schema.
         * @return mixed return validated data if not validation
         * @throws SchemaError
         */
        public function validate($data)
        {
            $x = new SchemaError([], []);
            $_schema_func = $this->_schema_func;
            foreach ($this->_args as $arg) {
                try {
                    /** @var Schema $_schema_obj */
                    $_schema_obj = $_schema_func($arg, $this->_error);
                    return $_schema_obj->validate($data);
                } catch (SchemaError $_x) {
                    $x = $_x;
                }

            }
            array_unshift($x->autos, strval($this) . ' did not validate ' . strval($data));
            array_unshift($x->errors, Valid::_generate_error($this->_error, $data));
            throw new SchemaError($x->autos, $x->errors);
        }
    }

    /**
     * Enables schema.py to validate string using regular expressions.
     */
    class Regex
    {

        protected $_pattern_str = '';
        protected $_error = null;

        /**
         * Regex constructor.
         * @param string $pattern_str
         * @param null $error
         */
        public function __construct($pattern_str, $error = null)
        {
            $this->_pattern_str = $pattern_str;
            $this->_error = $error;
        }

        public function __toString()
        {
            return __CLASS__ . "({$this->_pattern_str})";
        }

        /**
         * Validated data using defined regex.
         * @param mixed $data data to be validated
         * @return mixed
         * @throws SchemaError
         */
        public function validate($data)
        {
            if (!is_string($data)) {
                throw new SchemaError(strval($data) . ' is not string nor buffer', $this->_error);
            }
            if (preg_match($this->_pattern_str, $data)) {
                return $data;
            } else {
                throw new SchemaError(strval($data) . ' does not match ' . $this->_pattern_str, $this->_error);
            }
        }
    }

    /**
     * For more general use cases, you can use the Use class to transform the data while it is being validate.
     */
    class _Use
    {
        protected $_callable = null;
        protected $_error = null;

        public function __construct(\Closure $callable_, $error = null)
        {
            $this->_callable = $callable_;
            $this->_error = $error;
        }

        public function __toString()
        {
            return __CLASS__ . '(' . strval($this->_callable) . ')';
        }

        /**
         * @param mixed $data
         * @return array
         * @throws SchemaError
         */
        public function validate($data)
        {
            try {
                $func = $this->_callable;
                return $func($data);
            } catch (SchemaError $sx) {
                array_unshift($sx->autos, null);
                array_unshift($sx->errors, Valid::_generate_error($this->_error, $data));
                throw new SchemaError($sx->autos, $sx->errors);
            } catch (Exception $ex) {
                $func_str = Valid::_callable_str($this->_callable);
                throw new SchemaError("{$func_str}({$data}) raised {$ex}", Valid::_generate_error($this->_error, $data));
            }
        }
    }

    /**
     * Entry point of the library, use this class to instantiate validation schema for the data that will be validated.
     */
    class Schema
    {
        protected $_schema = null;
        protected $_error = null;
        protected $_ignore_extra_keys = false;

        public function __construct($schema, $error = null, $ignore_extra_keys = false)
        {
            $this->_schema = $schema;
            $this->_error = $error;
            $this->_ignore_extra_keys = $ignore_extra_keys;
        }

        public function __toString()
        {
            return __METHOD__ . strval($this->_schema);
        }

        /**
         * Return priority for a given key object.
         * @param mixed $s
         * @return int
         */
        private static function _dict_key_priority($s)
        {
            if ($s instanceof Optional) {
                return Valid::_priority($s->_schema) + 0.5;
            }
            return Valid::_priority($s);
        }

        private static function _get_sorted_skeys($skeys)
        {
            $tmp = [];
            foreach ($skeys as $skey) {
                $tmp[$skey] = self::_dict_key_priority($skey);
            }
            asort($tmp);
            return array_keys($tmp);
        }

        /**
         * @param mixed $data
         */
        public function validate($data)
        {
            $schema = __CLASS__;
            $s = $this->_schema;
            $e = $this->_error;
            $flavor = Valid::_priority($s);
            if ($flavor == Valid::_ITERABLE) {
                //data = Schema(type(s), error=e).validate(data)
                $o = new _Or($s, $e, function ($conf, $error = '') {
                    return new static($conf, $error);
                });
                $rst = [];
                foreach ($data as $item) {
                    $rst[] = $o->validate($item);
                }
                return $rst;
            } else if ($flavor == Valid::_DICT) {
                //data = Schema(dict, error=e).validate(data)
                $new = [];  # new - is a dict of the validated values
                $coverage = [];  # matched schema keys
                $sorted_skeys = self::_get_sorted_skeys(array_keys($s));
                foreach ($data as $key => $value) {
                    foreach ($sorted_skeys as $skey) {
                        list($nkey, $nvalue, $last_sx, $last_ssx) = [null, null, null, null];
                        $svalue = $s[$skey];
                        try {
                            $nkey = (new Schema($skey, $e))->validate($key);
                        } catch (SchemaError $sx) {
                            $last_sx = $sx;
                        }
                        if (is_null($last_sx)) {
                            try {
                                $nvalue = (new Schema($svalue, $e))->validate($value);
                            } catch (SchemaError $ssx) {
                                array_unshift($ssx->autos, "Key " . strval($nkey) . " error:");
                                array_unshift($ssx->errors, $ssx);
                                throw new SchemaError($ssx->autos, $ssx->errors);
                            }
                            /** @var string $nkey */
                            $new[$nkey] = $nvalue;
                            $coverage[$skey] = 1;
                            break;
                        }
                    }
                }
                $required = [];
                foreach ($s as $k) {
                    if( !($k instanceof Optional) ){
                        $required[$k] = 1;
                    }
                }
            }
            /*
                data = Schema(dict, error=e).validate(data)


                # for each key and value find a schema entry matching them, if any
                sorted_skeys = sorted(s, key=self._dict_key_priority)
                for key, value in data.items():
                    for skey in sorted_skeys:
                        svalue = s[skey]
                        try:
                            nkey = Schema(skey, error=e).validate(key)
                        except SchemaError:
                            pass
                        else:
                            try:
                                nvalue = Schema(svalue, error=e).validate(value)
                            except SchemaError as x:
                                k = "Key '%s' error:" % nkey
                                raise SchemaError([k] + x.autos, [e] + x.errors)
                            else:
                                new[nkey] = nvalue
                                coverage.add(skey)
                                break
                required = set(k for k in s if type(k) is not Optional)
                if not required.issubset(coverage):
                    missing_keys = required - coverage
                    s_missing_keys = ', '.join(repr(k) for k in sorted(missing_keys, key=repr))
                    raise SchemaMissingKeyError('Missing keys: ' + s_missing_keys, e)
                if not self._ignore_extra_keys and (len(new) != len(data)):
                    wrong_keys = set(data.keys()) - set(new.keys())
                    s_wrong_keys = ', '.join(repr(k) for k in sorted(wrong_keys, key=repr))
                    raise \
                        SchemaWrongKeyError('Wrong keys %s in %r' % (s_wrong_keys, data), e.format(data) if e else None)

                # Apply default-having optionals that haven't been used:
                defaults = set(k for k in s if type(k) is Optional and hasattr(k, 'default')) - coverage
                for default in defaults:
                    new[default.key] = default.default

                return new


              */
        }
    }

    /*
    class Schema(object):
        def validate(self, data):
            Schema = self.__class__
            s = self._schema
            e = self._error
            flavor = _priority(s)
            if flavor == ITERABLE:
                data = Schema(type(s), error=e).validate(data)
                o = Or(*s, error=e, schema=Schema)
                return type(data)(o.validate(d) for d in data)
            if flavor == DICT:
                data = Schema(dict, error=e).validate(data)
                new = type(data)()  # new - is a dict of the validated values
                coverage = set()  # matched schema keys
                # for each key and value find a schema entry matching them, if any
                sorted_skeys = sorted(s, key=self._dict_key_priority)
                for key, value in data.items():
                    for skey in sorted_skeys:
                        svalue = s[skey]
                        try:
                            nkey = Schema(skey, error=e).validate(key)
                        except SchemaError:
                            pass
                        else:
                            try:
                                nvalue = Schema(svalue, error=e).validate(value)
                            except SchemaError as x:
                                k = "Key '%s' error:" % nkey
                                raise SchemaError([k] + x.autos, [e] + x.errors)
                            else:
                                new[nkey] = nvalue
                                coverage.add(skey)
                                break
                required = set(k for k in s if type(k) is not Optional)
                if not required.issubset(coverage):
                    missing_keys = required - coverage
                    s_missing_keys = ', '.join(repr(k) for k in sorted(missing_keys, key=repr))
                    raise SchemaMissingKeyError('Missing keys: ' + s_missing_keys, e)
                if not self._ignore_extra_keys and (len(new) != len(data)):
                    wrong_keys = set(data.keys()) - set(new.keys())
                    s_wrong_keys = ', '.join(repr(k) for k in sorted(wrong_keys, key=repr))
                    raise \
                        SchemaWrongKeyError('Wrong keys %s in %r' % (s_wrong_keys, data), e.format(data) if e else None)

                # Apply default-having optionals that haven't been used:
                defaults = set(k for k in s if type(k) is Optional and hasattr(k, 'default')) - coverage
                for default in defaults:
                    new[default.key] = default.default

                return new
            if flavor == TYPE:
                if isinstance(data, s):
                    return data
                else:
                    raise SchemaUnexpectedTypeError('%r should be instance of %r' % (data, s.__name__), e.format(data) if e else None)
            if flavor == VALIDATOR:
                try:
                    return s.validate(data)
                except SchemaError as x:
                    raise SchemaError([None] + x.autos, [e] + x.errors)
                except BaseException as x:
                    raise SchemaError('%r.validate(%r) raised %r' % (s, data, x), self._error.format(data) if self._error else None)
            if flavor == CALLABLE:
                f = _callable_str(s)
                try:
                    if s(data):
                        return data
                except SchemaError as x:
                    raise SchemaError([None] + x.autos, [e] + x.errors)
                except BaseException as x:
                    raise SchemaError('%s(%r) raised %r' % (f, data, x), self._error.format(data) if self._error else None)
                raise SchemaError('%s(%r) should evaluate to True' % (f, data), e)
            if s == data:
                return data
            else:
                raise SchemaError('%r does not match %r' % (s, data), e.format(data) if e else None)
     * */


    class Optional extends Schema
    {

    }
}

/**
 * Class Valid php for schema.py
 * schema is a library for validating Python data structures
 * @package model\sys
 */
class Valid
{
    const _COMPARABLE = 0;
    const _CALLABLE = 1;
    const _VALIDATOR = 2;
    const _TYPE = 3;
    const _DICT = 4;
    const _ITERABLE = 5;

    /**
     * Return priority for a given object.
     * @param $s
     * @return int
     */
    public static function _priority($s)
    {
        $type_str = gettype($s);
        if ($type_str == 'array') {
            if (empty ($s) || array_keys($s) === range(0, sizeof($s) - 1)) {
                return self::_ITERABLE;
            } else {
                return self::_DICT;
            }
        } else if (class_exists($s)) {
            return self::_TYPE;
        } else if (method_exists($s, 'validate')) {
            return self::_VALIDATOR;
        } else if (is_callable($s)) {
            return self::_CALLABLE;
        } else {
            return self::_COMPARABLE;
        }
    }

    public static function _callable_str($callable_)
    {
        if (array_key_exists('__name__', $callable_)) {
            return $callable_['__name__'];
        }
        return strval($callable_);
    }

    public static function _generate_error($_error, $data)
    {
        return !empty($_error) ? $_error->format($data) : null;
    }


    public static function _Or()
    {

    }

    public static function _And()
    {

    }

    public static function _Optional()
    {

    }

}