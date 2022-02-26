<?php

declare(strict_types=1);

namespace AapSoftware\VideoColor\responses;

use stdClass;

abstract class AbstractResponse extends stdClass
{
    public static function create(array $data): self
    {
        $response = new static();

        foreach ($data as $key => $value) {
            $response->{$key} = $value;
        }

        return $response;
    }
}
