<?php

class Users extends BaseModel
{
    public $id;

    public $firstName;

    public $lastName;

    public $password;
    
    public function initialize()
    {
        /* Makes no difference either */
        //$this->useDynamicUpdate(true);
    }
}
