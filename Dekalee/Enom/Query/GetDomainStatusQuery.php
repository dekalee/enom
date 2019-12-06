<?php

namespace Dekalee\Enom\Query;

use Dekalee\Enom\Exception\GetDomainStatusFailedException;
use Dekalee\Enom\Facade\FacadeInterface;
use Dekalee\Enom\Facade\GetDomainStatusFacade;

/**
 * Class GetDomainStatusQuery
 */
class GetDomainStatusQuery extends AbstractQuery implements QueryInterface
{
    /**
     * @param FacadeInterface|GetDomainStatusFacade $facade
     *
     * @return FacadeInterface|GetDomainStatusFacade
     * @throws GetDomainStatusFailedException
     */
    public function execute(FacadeInterface $facade)
    {
        $parameters = array_merge($this->parameters, [
            'command' => 'GETDOMAINSTATUS',
            'sld' => $facade->sld,
            'tld' => $facade->tld,
            'responsetype' => 'xml',
        ]);

        $response = $this->guzzle->get($this->baseUri . '?' . $this->parameterImplode($parameters));
        $content = $response->getBody()->getContents();

        $xml = simplexml_load_string($content);

        $facade->inAccount = (int)$xml->DomainStatus->InAccount;

        $errCount = (int) $xml->ErrCount;
        if (0 != $errCount) {
            if (
                1 !== $errCount ||
                'The order number specified does not belong to this account' != (string) $xml->errors->Err1
            ) {
                throw new GetDomainStatusFailedException(implode(',', (array) $xml->errors));
            }
        }

        return $facade;
    }

}
