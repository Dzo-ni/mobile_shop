<?php
namespace App\Validators;

use App\Core\Validator;

class StringValidator implements Validator{
    private $maxStringLength;
    private $minStringLength;
    public function __construct()
    {
        $this->minStringLength =  0;
        $this->maxStringLength= 255;
      
    }
    public function &setMinLength( $minStringLength):StringValidator{
        $this->minStringLength = $minStringLength;
        return $this;
    }
    public function &setMaxLength( $maxStringLength):StringValidator{
        $this->maxStringLength = $maxStringLength;
        return $this;
    }
    public function isValid(string $value):bool
    {
       
        return  $this->minStringLength <= strlen($value) && strlen($value) <= $this->maxStringLength;
    }
}