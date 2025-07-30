<?php

declare(strict_types=1);

namespace App\Handler;

use Laminas\Filter;
use Laminas\InputFilter\Input;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator;

class UserInputFilter extends InputFilter
{
    public function __construct()
    {
        // name
        $name = new Input('name');
        $name->getFilterChain()->attach(new Filter\StringTrim());
        $name->getValidatorChain()
            ->attach(new Validator\NotEmpty())
            ->attach(new Validator\StringLength(['max' => 255]));
        $this->add($name);

        // phone_number
        $phone = new Input('phone_number');
        $phone->getFilterChain()->attach(new Filter\StringTrim());
        $phone->getValidatorChain()
            ->attach(new Validator\NotEmpty())
            ->attach(new Validator\StringLength(['max' => 20]));
        $this->add($phone);

        // address
        $address = new Input('address');
        $address->getFilterChain()->attach(new Filter\StringTrim());
        $address->getValidatorChain()
            ->attach(new Validator\NotEmpty())
            ->attach(new Validator\StringLength(['max' => 500]));
        $this->add($address);

        // age
        $age = new Input('age');
        $age->getValidatorChain()
            ->attach(new Validator\NotEmpty())
            ->attach(new Validator\Digits())
            ->attach(new Validator\Between(['min' => 1, 'max' => 150]));
        $this->add($age);

        // education_id (opcjonalne, tylko digits)
        $educationId = new Input('education_id');
        $educationId->setRequired(false);
        $educationId->getValidatorChain()->attach(new Validator\Digits());
        $this->add($educationId);

        // id (opcjonalne, tylko digits)
        $id = new Input('id');
        $id->setRequired(false);
        $id->getValidatorChain()->attach(new Validator\Digits());
        $this->add($id);
    }
}
