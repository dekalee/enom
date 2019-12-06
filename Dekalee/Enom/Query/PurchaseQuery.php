<?php

namespace Dekalee\Enom\Query;

use Dekalee\Enom\Exception\PurchaseFailedException;
use Dekalee\Enom\Facade\FacadeInterface;
use Dekalee\Enom\Facade\PurchaseFacade;

/**
 * Class PurchaseQuery
 */
class PurchaseQuery extends AbstractQuery implements QueryInterface
{
    /**
     * @param PurchaseFacade|FacadeInterface $facade
     *
     * @return PurchaseFacade
     * @throws PurchaseFailedException
     */
    public function execute(FacadeInterface $facade)
    {
        $parameters = array_merge($this->parameters, [
            'command' => 'Purchase',
            'sld' => $facade->sld,
            'tld' => $facade->tld,
            'UseDNS' => $facade->useDns,
            'responsetype' => 'xml',
        ]);

        $response = $this->guzzle->get($this->baseUri . '?' . $this->parameterImplode($parameters));
        $content = $response->getBody()->getContents();

        $xml = simplexml_load_string($content);

        $facade->orderId = (string)$xml->OrderID;

        if (0 != $xml->ErrCount) {
            throw new PurchaseFailedException(implode(',', (array) $xml->errors));
        }

        return $facade;
    }
}
