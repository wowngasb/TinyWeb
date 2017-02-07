<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/7 0007
 * Time: 14:16
 */

namespace model\sys;

use \Exception;

function _callable_str($callable_)
{
    if (array_key_exists('__name__', $callable_)) {
        return $callable_['__name__'];
    }
    return strval($callable_);
}

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
        protected $_schema = null;

        /**
         * _And constructor.
         * @param mixed $args
         * @param null $error
         * @param \Closure|null $schema
         */
        public function __construct($args, $error = null, \Closure $schema = null)
        {
            $this->_args = $args;
            $this->_error = $error;
            # You can pass your inherited Schema class.
            $this->_schema = !empty($schema) ? $schema : function ($conf, $error = '') {
                return new Schema($conf, $error);
            };
        }

        public function __toString()
        {
            return __CLASS__ . "(" . json_encode($this->_args) . ")";
        }

        /**
         * Validate data using defined sub schema/expressions ensuring all values are valid.
         * @param mixed $data to be validated with sub defined schemas.
         * @return mixed returns validated data
         */
        public function validate($data)
        {
            $func = $this->_schema;
            foreach ($this->_args as $arg) {
                /** @var Schema $_schema_obj */
                $_schema_obj = $func($arg, $this->_error);
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
            $func = $this->_schema;
            foreach ($this->_args as $arg) {
                try {
                    /** @var Schema $_schema_obj */
                    $_schema_obj = $func($arg, $this->_error);
                    return $_schema_obj->validate($data);
                } catch (SchemaError $_x) {
                    $x = $_x;
                }

            }
            $autos = [strval($this) . ' did not validate ' . json_encode($data)];
            foreach ($x->autos as $auto) {
                $autos[] = $auto;
            }
            $errors = [!empty($this->_error) ? $this->_error->format($data) : null];
            foreach ($x->errors as $error) {
                $errors[] = $error;
            }
            throw new SchemaError($autos, $errors);
        }
    }

    /**
     * Enables schema.py to validate string using regular expressions.
     */
    class Regex
    {
        # Map all flags bits to a more readable description
        private static $NAMES = ['re.ASCII', 're.DEBUG', 're.VERBOSE', 're.UNICODE', 're.DOTALL',
            're.MULTILINE', 're.LOCALE', 're.IGNORECASE', 're.TEMPLATE'];

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
                throw new SchemaError(json_encode($data) . ' is not string nor buffer', $this->_error);
            }
            if (preg_match($this->_pattern_str, $data)) {
                return $data;
            } else {
                throw new SchemaError(json_encode($data) . ' does not match ' . $this->_pattern_str, $this->_error);
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
         */
        public function validate($data){
            try{
                $func = $this->_callable;
                return $func($data);
            } catch(SchemaError $sx){
                $autos = [null];
                $sx->autos;
                throw new SchemaError();
            } catch(Exception $ex){

            }
            try:
        return self._callable(data)
    except SchemaError as x:
        raise SchemaError([None] + x.autos,
                [self._error.format(data)
                           if self._error else None] + x.errors)
    except BaseException as x:
        f = _callable_str(self._callable)
        raise SchemaError('%s(%r) raised %r' % (f, data, x),
                          self._error.format(data)
                          if self._error else None)
        }

    }

    /*
class Use(object):
"""

"""
def validate(self, data):

     * */


}


/**
 * Class Valid php for schema.py
 * schema is a library for validating Python data structures
 * @package model\sys
 */
class Valid
{

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