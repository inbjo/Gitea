<?php

declare(strict_types=1);

namespace Avency\Gitea;

use Avency\Gitea\Endpoint\Admin;
use Avency\Gitea\Endpoint\EndpointInterface;
use Avency\Gitea\Endpoint\Issues;
use Avency\Gitea\Endpoint\Miscellaneous;
use Avency\Gitea\Endpoint\Organizations;
use Avency\Gitea\Endpoint\Repositories;
use Avency\Gitea\Endpoint\User;
use Avency\Gitea\Endpoint\Users;
use Exception;
use Psr\Http\Message\ResponseInterface;

/**
 * Gitea Client
 *
 * @method Admin admin()
 * @method Issues issues()
 * @method Miscellaneous miscellaneous()
 * @method Organizations organizations()
 * @method Repositories repositories()
 * @method User user()
 * @method Users users()
 */
class Client
{
    const AUTH_ACCESS_TOKEN = 'access_token';
    const AUTH_TOKEN = 'token';
    const AUTH_BASIC_AUTH = 'basic_auth';

    const BASE_URI = '/api/v1';

    /**
     * @var \GuzzleHttp\Client
     */
    protected $httpClient;

    /**
     * @var array
     */
    protected $config;

    /**
     * @param $baseUri
     * @param $authentication
     * @throws Exception
     */
    public function __construct($baseUri, $authentication)
    {
        $this->config = [
            'base_uri' => $baseUri,
            'verify' => false
        ];

        $this->auth($authentication);

        $this->httpClient = new \GuzzleHttp\Client($this->config);
    }

    /**
     * @param $method
     * @param $args
     * @return EndpointInterface
     * @throws Exception
     */
    public function __call($method, $args)
    {
        $interfaceName = EndpointInterface::class;
        $endpointClassName = str_replace('\\EndpointInterface', '\\' . ucfirst($method), $interfaceName);
        if (class_exists($endpointClassName)) {
            return new $endpointClassName($this);
        }

        throw new Exception('Endpoint "' . ucfirst($method) . '" not found!', 1579274712);
    }

    /**
     * @param string $uri
     * @param string $method
     * @param array $options
     * @return ResponseInterface
     */
    public function request(string $uri = '', string $method = 'GET', array $options = []): ResponseInterface
    {
        $uri = self::BASE_URI . $uri;

        if (!empty($this->config['query']) && isset($options['query'])) {
            $options['query'] = array_merge($this->config['query'], $options['query']);
        } else if (!empty($this->config['query'])) {
            $options['query'] = $this->config['query'];
        }

        return $this->httpClient->request($method, $uri, $options);
    }

    /**
     * @param string $username
     * @return $this
     */
    public function sudo(string $username): self
    {
        $this->config['query']['sudo'] = $username;
        return $this;
    }

    /**
     * @param array $authentication
     * @throws Exception
     */
    protected function auth(array $authentication)
    {
        if (empty($authentication['type'])) {
            throw new Exception('Please add an authentication type.', 1579244392);
        }

        switch ($authentication['type']) {
            case self::AUTH_ACCESS_TOKEN:
                if (empty($authentication['auth'])) {
                    throw new Exception('Please add the access token.', 1579245994);
                }

                $this->config['query']['access_token'] = $authentication['auth'];
                break;

            case self::AUTH_BASIC_AUTH:
                if (empty($authentication['auth']['username'])) {
                    throw new Exception('Please add the username.', 1579246033);
                }
                if (empty($authentication['auth']['password'])) {
                    throw new Exception('Please add the password.', 1579246035);
                }

                $this->config['auth'] = [$authentication['auth']['username'], $authentication['auth']['password']];
                break;

            case self::AUTH_TOKEN:
                if (empty($authentication['auth'])) {
                    throw new Exception('Please add the token.', 1579246003);
                }

                $this->config['query']['token'] = $authentication['auth'];
                break;
        }
    }
}
