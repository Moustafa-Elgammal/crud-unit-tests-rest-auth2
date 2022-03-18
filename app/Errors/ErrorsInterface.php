<?php
namespace App\Errors;

interface ErrorsInterface
{
    public function addError(string $message, $drop_previous_errors);

    public function getErrors(): array;
}
