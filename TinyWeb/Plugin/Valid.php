<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/7 0007
 * Time: 14:16
 * schema is a library for validating Python data structures, such as those
 * obtained from config-files, forms, external services or command-line
 * parsing, converted from JSON/YAML (or something else) to Python data-types.
 */

namespace model\sys;


/**
 * @__version__ = '0.6.5'
 * __all__ = ['Schema', 'And', 'Or', 'Regex', 'Optional', 'Use', 'SchemaError', 'SchemaWrongKeyError', 'SchemaMissingKeyError', 'SchemaUnexpectedTypeError']
 */
use Exception;


/**
 * Error during Schema validation.
 */
class SchemaError extends Exception
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
                if (!empty($item) && !in_array($item, $seen)) {
                    $seen[] = $item;
                }
            }
            return $seen;
        }

        $data_set = uniq($this->autos);
        if (!empty($error_list)) {
            return join("\n", $error_list);
        }
        return join("\n", $data_set);
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
            throw new SchemaError("{$func_str}(" . strval($data) . ") raised {$ex}", Valid::_generate_error($this->_error, $data));
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
        return __CLASS__ . strval($this->_schema);
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

    private static function _set_issubset($test, $target)
    {
        foreach ($test as $key => $val) {
            if (!isset($target[$key])) {
                return false;
            }
        }
        return true;
    }

    private static function _set_sub($test, $target)
    {
        $rst = [];
        foreach ($test as $key => $val) {
            if (!isset($target[$key])) {
                $rst[$key] = $val;
            }
        }
        return $rst;
    }

    /**
     * @param mixed $data
     * @return array
     * @throws Schema
     * @throws SchemaError
     * @throws SchemaMissingKeyError
     * @throws SchemaUnexpectedTypeError
     * @throws SchemaWrongKeyError
     */
    public function validate($data)
    {
        /** @var mixed $s */
        $s = $this->_schema;
        $e = $this->_error;
        $flavor = Valid::_priority($s);
        if ($flavor == Valid::_ITERABLE) {  //判断 data 类型 强制转换为指定类型
            //data = Schema(type(s), error=e).validate(data)
            $data = (array)$data;
            $o = new _Or($s, $e, function ($conf, $error = '') {
                return new Schema($conf, $error);
            });
            $rst = [];
            foreach ($data as $item) {
                $rst[] = $o->validate($item);
            }
            return (array)$rst;
        }

        if ($flavor == Valid::_DICT) {
            //data = Schema(dict, error=e).validate(data)
            $data = (array)$data;
            $new = [];  # new - is a dict of the validated values
            $coverage = [];  # matched schema keys
            $sorted_skeys = self::_get_sorted_skeys(array_keys($s));
            foreach ($data as $key => $value) {
                foreach ($sorted_skeys as $skey) {
                    list($nkey, $last_sx) = [null, null];
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
                if (!($k instanceof Optional)) {
                    $required[$k] = 1;
                }
            }
            if (self::_set_issubset($required, $coverage)) {
                $missing_keys = self::_set_sub($required, $coverage);
                $s_missing_keys = join(', ', array_keys($missing_keys));
                throw new SchemaMissingKeyError("Missing keys: {$s_missing_keys}", $e);
            }
            if (!($this->_ignore_extra_keys) && count($new) != count($data)) {
                $wrong_keys = self::_set_sub($data, $new);
                $s_wrong_keys = join(', ', array_keys($wrong_keys));
                throw new SchemaWrongKeyError("Wrong keys {$s_wrong_keys} in " . json_encode($data), valid::_generate_error($e, $data));
            }
            # Apply default-having optionals that haven't been used:
            $defaults = [];
            foreach ($s as $k) {
                if (($k instanceof Optional) && property_exists($k, 'default')) {
                    /** @var Optional $k */
                    $defaults[$k->key] = $k->default;
                }
            }
            $defaults = self::_set_sub($defaults, $coverage);
            foreach ($defaults as $default_key => $default_val) {
                $new[$default_key] = $default_val;
            }
            return $new;
        }

        if ($flavor == Valid::_TYPE) {
            if ($data instanceof $s) {
                return $data;
            } else {
                throw new SchemaUnexpectedTypeError(strval($data) . ' should be instance of ' . strval($s), Valid::_generate_error($e, $data));
            }
        }

        if ($flavor == Valid::_VALIDATOR) {
            try {
                return $s->validate($data);
            } catch (SchemaError $x) {
                array_unshift($x->autos, null);
                array_unshift($x->errors, $e);
                throw new SchemaError($x->autos, $x->errors);
            } catch (Exception $x) {
                throw new SchemaError(strval($s) . '.validate(' . strval($data) . ') raised ' . strval($x), Valid::_generate_error($e, $data));
            }
        }

        if ($flavor == Valid::_CALLABLE) {
            $f = Valid::_callable_str($s);
            try {
                /** @var callable $s */
                if ($s($data)) {
                    return $data;
                }
            } catch (SchemaError $x) {
                array_unshift($x->autos, null);
                array_unshift($x->errors, $e);
                throw new SchemaError($x->autos, $x->errors);
            } catch (Exception $x) {
                throw new SchemaError($f . '(' . strval($data) . ') raised ' . strval($x), Valid::_generate_error($e, $data));
            }
            throw new SchemaError($f . '(' . strval($data) . ') should evaluate to True', $e);
        }

        if ($s == $data) {
            return $data;
        } else {
            throw new SchemaError(strval($s) . ' does not match ' . strval($data), Valid::_generate_error($e, $data));
        }

    }
}

class OptionalTypeError extends Exception
{
    public function __construct($message, $code = 102, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

class Optional extends Schema
{
    private static $_MARKER = null;
    public $default = null;
    public $key = null;

    public function __construct($schema, $error = null, $ignore_extra_keys = false, $default = null)
    {
        if (is_null(self::$_MARKER)) {
            self::$_MARKER = (object)['_object' => rand()];
        }
        if (is_null($default)) {
            $default = self::$_MARKER;
        }
        parent::__construct($schema, $error, $ignore_extra_keys);
        if ($default != self::$_MARKER) {
            # See if I can come up with a static key to use for myself:
            if (Valid::_priority($this->_schema) != Valid::_COMPARABLE) {
                throw new OptionalTypeError('Optional keys with defaults must have simple, predictable values, like literal strings or ints. "' . strval($this->_schema) . '" is too complex.');
            }
            $this->default = $default;
            $this->key = $this->_schema;
        }
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
        if (is_string($callable_) && class_exists($callable_)) {
            return $callable_;
        }
        return strval($callable_);
    }

    public static function _generate_error($_error, $data)
    {
        return !empty($_error) ? sprintf($_error, $data) : null;
    }

    public static function _Use()
    {
        $args = func_get_args();
        return call_user_func_array(_Use::class, $args);
    }

    public static function _Or()
    {
        $args = func_get_args();
        return call_user_func_array(_Or::class, $args);
    }

    public static function _And()
    {
        $args = func_get_args();
        return call_user_func_array(_And::class, $args);
    }

    public static function Regex()
    {
        $args = func_get_args();
        return call_user_func_array(Regex::class, $args);
    }

    public static function Optional()
    {
        $args = func_get_args();
        return call_user_func_array(Optional::class, $args);
    }

    public static function Schema()
    {
        $args = func_get_args();
        return call_user_func_array(Schema::class, $args);
    }

}