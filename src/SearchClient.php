<?php

declare(strict_types=1);

namespace AapSoftware\VideoColor;

use AapSoftware\VideoColor\enums\LanguageEnum;
use AapSoftware\VideoColor\responses\FindResponse;

class SearchClient
{
    private ApiClient $api;

    public function __construct(string $language = LanguageEnum::ENGLISH)
    {
        $this->api = new ApiClient($language);
    }

    public function find(string $path): FindResponse
    {
        $encoder = new ImageEncoder($path);

        return $this->api->find($encoder->encode());
    }
}
