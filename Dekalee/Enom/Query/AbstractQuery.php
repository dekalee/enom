<?php

namespace Dekalee\Enom\Query;

use GuzzleHttp\Client;

/**
 * Class AbstractQuery
 */
abstract class AbstractQuery
{
    protected $guzzle;
    protected $baseUri;
    protected $parameters = [];

    /**
     * @param string $uid
     * @param string $passwd
     * @param Client $guzzle
     * @param string $baseUri
     */
    public function __construct($uid, $passwd, Client $guzzle, $baseUri)
    {
        $this->guzzle = $guzzle;
        $this->baseUri = $baseUri;
        $this->parameters = [
            'uid' => $uid,
            'pw' => $passwd
        ];
    }

    /**
     * @param array $parameters
     *
     * @return string
     */
    protected function parameterImplode(array $parameters = array())
    {
        $string = '';
        foreach ($parameters as $key => $parameter) {
            if ('' !== $string) {
                $string = $string . '&';
            }
            $string = $string . $key . '=' . $parameter;
        }

        return $string;
    }
}
