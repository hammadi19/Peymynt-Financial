<?php
namespace App\Validator;


class Validator{

    private $validation;

    public function __construct(ValidationInterface $validation)
    {
        $this->validation = $validation;
    }

    /**
     * @param $object
     * @return bool
     */
    public function isValid($object){
        $errors = $this->validation->validate($object);
        return (count($errors)>0)?$errors:TRUE;
    }

}