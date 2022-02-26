<?php

declare(strict_types=1);

namespace AapSoftware\VideoColor;

use AapSoftware\VideoColor\enums\LanguageEnum;
use AapSoftware\VideoColor\exceptions\ApiException;
use AapSoftware\VideoColor\responses\FindResponse;

class ApiClient
{
    private const API_URL = 'https://www.videocolor.aapsoftware.ru/';
    private const API_VERSION = 'v4';

    private string $language;

    public function __construct(string $language = LanguageEnum::ENGLISH)
    {
        $this->language = $language;
    }

    public function find(string $query): FindResponse
    {
        $response = $this->post('find.php', [
            'query' => $query,
        ]);

        return FindResponse::create($response);
    }

    private function post(string $resource, array $params): array
    {
        $url = self::API_URL . self::API_VERSION . '/' . $this->language. '/' . $resource;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $data = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($code != 200) {
            throw new ApiException('API error: ' . $error, $code);
        }

        try {
            return json_decode($data, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Exception $e) {
            throw new ApiException('API error: ' . $data);
        }
    }

}
