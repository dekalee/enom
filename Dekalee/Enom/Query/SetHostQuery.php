<?php

namespace Dekalee\Enom\Query;

use Dekalee\Enom\Exception\SetHostException;
use Dekalee\Enom\Facade\FacadeInterface;
use Dekalee\Enom\Facade\SetHostFacade;

/**
 * Class SetHostQuery
 */
class SetHostQuery extends AbstractQuery implements QueryInterface
{
    /**
     * @param SetHostFacade|FacadeInterface $facade
     *
     * @return FacadeInterface
     * @throws SetHostException
     */
    public function execute(FacadeInterface $facade)
    {
        $domain = explode('.', $facade->domain);
        $tld = array_pop($domain);
        $sld = array_pop($domain);
        $hostname = implode('.', $domain);

        $parameters = array_merge($this->parameters, [
            'command' => 'sethosts',
            'sld' => $sld,
            'tld' => $tld,
            'HostName1' => $hostname,
            'RecordType1' => 'CNAME',
            'Address1' => $facade->cdnUrl,
            'responsetype' => 'xml'
        ]);

        $response = $this->guzzle->get($this->baseUri . '?' . $this->parameterImplode($parameters));
        $content = $response->getBody()->getContents();

        $xml = simplexml_load_string($content);

        if (0 != $xml->ErrCount) {
            throw new SetHostException(implode(',', (array) $xml->errors));
        }

        return $facade;
    }
}
