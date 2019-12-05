<?php

namespace Dekalee\Enom\Query;

use Dekalee\Enom\Exception\PurchaseServicesFailedException;
use Dekalee\Enom\Facade\FacadeInterface;
use Dekalee\Enom\Facade\PurchaseServicesFacade;

/**
 * Class PurchaseServicesQuery
 */
class PurchaseServicesQuery extends AbstractQuery implements QueryInterface
{
    /**
     * @param PurchaseServicesFacade|FacadeInterface $facade
     *
     * @return PurchaseServicesFacade
     * @throws PurchaseServicesFailedException
     */
    public function execute(FacadeInterface $facade)
    {
        $parameters = array_merge($this->parameters, [
            'command' => 'PURCHASESERVICES',
            'Service' => $facade->service,
            'sld' => $facade->sld,
            'tld' => $facade->tld,
            'responsetype' => 'xml',
        ]);

        $response = $this->guzzle->get($this->baseUri . '?' . $this->parameterImplode($parameters));
        $content = $response->getBody()->getContents();

        $xml = simplexml_load_string($content);

        if (0 != $xml->ErrCount) {
            throw new PurchaseServicesFailedException(implode(',', (array) $xml->errors));
        }

        return $facade;
    }
}
