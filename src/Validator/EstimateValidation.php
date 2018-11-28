<?php
namespace App\Validator;

class EstimateValidation implements ValidationInterface
{
    public function validate($object)
    {
        $errors= array();
        if(empty($object['title'])){
            $errors['title'] = 'Please enter your title';
        }
        return $errors;
    }
}