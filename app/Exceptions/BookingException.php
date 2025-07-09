<?php

namespace App\Exceptions;

use Exception;

class BookingException extends Exception
{
    public function __construct($message = "Booking error occurred.", $code = 422, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function render($request)
    {
        return response()->json([
            'message' => 'Booking Failed',
            'error' => $this->getMessage(),
        ], $this->getCode());
    }
}
