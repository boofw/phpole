<?php namespace Polev\Phpole\App;

use Polev\Phpole\Http\Input;

class Validator
{
    protected $rules = [];
    protected $values = [];
    public $errors = [];

    protected $currentField;
    protected $currentValue;
    protected $currentLabel;

    function __construct($rules, $values = null)
    {
        foreach ($rules as $field => $rule) {
            $this->rules[$field] = [];
            list($label, $rule) = explode('#', $rule);
            $checkers = explode('|', $rule);
            foreach ($checkers as $checker) {
                list($func, $params) = explode(':', $checker);
                $params = explode(',', $params);
                $this->rules[$field][$func] = compact('label', 'params');
            }
        }
        if ( ! $values) $values = Input::all();
        $this->values = array_intersect_key($values, $rules);
    }

    function run()
    {
        foreach ($this->rules as $field => $checkers) {
            foreach ($checkers as $func => $params) {
                if (empty($this->errors[$field])) {
                    $this->currentField = $field;
                    $this->currentValue = $this->values[$field];
                    $this->currentLabel = $params['label'];
                    $error = call_user_func_array([$this, $func], $params['params']);
                    if (is_string($error)) $this->errors[$field] = $error;
                }
            }
        }
        return [$this->errors, $this->values];
    }

    function required()
    {
        if ( ! $this->currentValue) return $this->currentLabel.'必需填写';
    }
}