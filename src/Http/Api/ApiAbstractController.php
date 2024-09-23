<?php

namespace SlimFramework\Http\Api;

use SlimFramework\Http\AbstractController;
use SlimFramework\Utils\Dynamic;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

abstract class ApiAbstractController extends AbstractController
{
    /**
     * @var Dynamic|null
     */
    protected ?Dynamic $payloadReponse = null;

    /**
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function toJson(ResponseInterface $response): ResponseInterface
    {
        $dataOnlyAttributes = ['code'];

        if (empty($this->payloadResponse()->message)) {
            $dataOnlyAttributes[] = 'message';
        }

        $data = $this->payloadResponse()->whithout($dataOnlyAttributes);
        $code = (int) $this->payloadResponse()->code;

        $encodedJson = json_encode($data, JSON_PRETTY_PRINT);

        if (!is_string($encodedJson)) {
            $encodedJson = '';
        }

        $response->getBody()->write($encodedJson);

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($code);
    }

    /**
     * @param Request $request
     * @return Dynamic
     */
    protected function fromJson(Request $request): Dynamic
    {
        return Dynamic::fromJson($request->getBody()->getContents()) ?? new Dynamic();
    }

    /**
     * @return Dynamic
     */
    protected function payloadResponse(): Dynamic
    {
        if ($this->payloadReponse instanceof Dynamic) {
            return $this->payloadReponse;
        }

        $this->payloadReponse = new Dynamic();

        $this->payloadReponse->code = 200;
        $this->payloadReponse->message = "";
        $this->payloadReponse->data = [];

        return $this->payloadReponse;
    }
}
