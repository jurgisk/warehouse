<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Warehouse\Entity;

/**
 * Description of ValidationResult
 *
 * @author jurgis
 */
class ValidationResult {
       
    /**
     * @var bool
     */
    private $isValid;
    
    /**
     * @var array
     */
    private $errorDetails = [];
    
    public function __construct(bool $isValid) {
        $this->setIsValid($isValid);
    }
    
    public function setIsValid(bool $value) {
        $this->isValid = $value;
        return $this;
    }
    
    /**
     * 
     * @return bool
     */
    public function isValid() {
        return $this->isValid;
    }
    
    /**
     * 
     * @param array $errors
     * @return $this
     */
    public function setErrorDetails(array $errors)
    {
        $this->errorDetails = $errors;
        return $this;
    }
    
    /**
     * 
     * @param string $message
     * @return $this
     */
    public function addErrorMessage(string $message) {
        $this->errorDetails[] = $message;
        return $this;
    }
    
    /**
     * 
     * @return array
     */
    public function getErrorDetails()
    {
        return $this->errorDetails;
    }
    
    /**
     * @return string
     */
    public function getDisplayErrorMessage()
    {
        return implode(' ', $this->errorDetails);
    }
    
    
}
