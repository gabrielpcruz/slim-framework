<?php

namespace SlimFramework\Http\Api\Auth;


use Exception;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use SlimFramework\Http\Api\ApiAbstractController;
use SlimFramework\Service\Token\AccessToken;

class Token extends ApiAbstractController
{
    /**
     * @var AuthorizationServer
     */
    private AuthorizationServer $authorizationServer;

    /**
     * @var AccessToken
     */
    private AccessToken $accessToken;

    /**
     * @param AuthorizationServer $authorizationServer
     * @param AccessToken $accessToken
     */
    public function __construct(AuthorizationServer $authorizationServer, AccessToken $accessToken)
    {
        $this->authorizationServer = $authorizationServer;
        $this->accessToken = $accessToken;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function create(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $payload = [];
        $payload['grant_type'] = $data['grant_type'];

        if ($data['grant_type'] === 'password') {
            $payload['username'] = $data['username'];
            $payload['password'] = $data['password'];
            $payload['client_id'] = $this->accessToken->getClientByGrant($data)->getAttribute('identifier');
        }

        if ($data['grant_type'] === 'refresh_token') {
            $payload['refresh_token'] = $data['refresh_token'];
            $payload['client_id'] = $data['client_id'];
        }

        $request = $request->withParsedBody($payload);

        try {
            return $this->authorizationServer->respondToAccessTokenRequest($request, $response);
        } catch (OAuthServerException $exception) {
            return $exception->generateHttpResponse($response);
        } catch (Exception $exception) {
            $response->getBody()->write($exception->getMessage());

            return $response->withStatus(500);
        }
    }
}
