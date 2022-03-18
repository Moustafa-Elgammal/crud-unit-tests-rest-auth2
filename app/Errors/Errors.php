<?php

namespace App\Errors;

class Errors implements ErrorsInterface
{
    protected array $errors = [];

    /**
     * add a repository error
     * @param $error
     * @param $drop_previous_errors
     * @return void
     */
    public function addError($error, $drop_previous_errors = false)
    {
        if ($drop_previous_errors)
            $this->errors = [];

        $this->errors [] = $error;
    }

    /** get a repository errors
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
