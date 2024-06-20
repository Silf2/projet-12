<?php

namespace App\Validator;

use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidateArguments
{
    public function __construct(
        private ValidatorInterface $validator,
    )
    {}

    public function validateAndHandleErrors(object $entity): void
    {
        $errors = $this->validator->validate($entity);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            $errorMessageString = implode(" ", $errorMessages);
            throw new \InvalidArgumentException($errorMessageString);
        }
    }
}
