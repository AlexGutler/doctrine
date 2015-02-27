<?php
namespace AG\Utils\Validator;

use JsonSchema\Constraints\Object;

abstract class Validator
{
    public function isEmpty($field)
    {
        return empty($field) ? true : false;
    }

    public function minStrLength($field, $minLen)
    {
        return (strlen($field) < $minLen) ? true : false;
    }

    public function maxStrLength($field, $maxLen)
    {
        return (strlen($field) > $maxLen) ? true : false;
    }

    public function isNumeric($field)
    {
        return is_numeric($field) ? true : false;
    }

    public function isString($field)
    {
        return is_string($field) ? true : false;
    }

    public function isNaturalNumber($field)
    {
        return ($field >= 0) ? true : false;
    }

    public function isZero($field)
    {
        return ($field < 0.1) ? true : false;
    }

    function isMail($email){
        $er = "/^(([0-9a-zA-Z]+[-._+&])*[0-9a-zA-Z]+@([-0-9a-zA-Z]+[.])+[a-zA-Z]{2,6}){0,1}$/";
        return (preg_match($er, $email)) ? true : false;
    }
}