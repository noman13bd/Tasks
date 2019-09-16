<?php
namespace Task\Slim;

use Slim\Http\Response;

class BaseResponse extends Response {
    public function withHeaders(array $headers) {
        foreach($headers as $name => $value) {
            $this->headers->set($name, $value);
        }

        return $this;
    }

    public function setHeader($name, $value) {
        return $this->withHeaders([$name => $value]);
    }
}
