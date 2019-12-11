<?php

namespace Modules\Base\Exceptions;

use Exception;
use Throwable;
use Symfony\Component\HttpFoundation\Response;
use Modules\Base\Support\Response\ResponseTrait;

class BaseException extends Exception
{
    use ResponseTrait;

    public function __construct($message = '', $code = Response::HTTP_INTERNAL_SERVER_ERROR, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function report()
    {
        //
    }

    public function render($request)
    {
        $message = $this->getMessage() ?: __('base::error.exception');
        return $this->failedWithMessage($message, $this->getCode());
    }

}
