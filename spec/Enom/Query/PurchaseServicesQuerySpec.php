<?php

namespace spec\Dekalee\Enom\Query;

use Dekalee\Enom\Exception\PurchaseServicesFailedException;
use Dekalee\Enom\Facade\PurchaseServicesFacade;
use Dekalee\Enom\Query\PurchaseServicesQuery;
use Dekalee\Enom\Query\QueryInterface;
use GuzzleHttp\Client;
use PhpSpec\ObjectBehavior;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class PurchaseServicesQuerySpec extends ObjectBehavior
{
    function let(Client $guzzle)
    {
        $this->beConstructedWith('uid', 'passwd', $guzzle, 'http://baseuri');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PurchaseServicesQuery::CLASS);
    }

    function it_should_be_a_query()
    {
        $this->shouldHaveType(QueryInterface::CLASS);
    }

    function it_should_create_a_purchase_services(
        Client $guzzle,
        PurchaseServicesFacade $facade,
        ResponseInterface $response,
        StreamInterface $stream
    ) {
        $facade->sld = 'foo';
        $facade->tld = 'bar';
        $facade->service = 'WPPS';

        $response->getBody()->willReturn($stream);
        $stream->getContents()->willReturn(<<<EOX
<interface-response>
 <ErrCount>0</ErrCount>
</interface-response>
EOX
        );

        $guzzle->get('http://baseuri?uid=uid&pw=passwd&command=PURCHASESERVICES&Service=WPPS&sld=foo&tld=bar&responsetype=xml')->shouldBeCalled()->willReturn($response);

        $purchase = $this->execute($facade);

        $purchase->shouldHaveType(PurchaseServicesFacade::CLASS);
        $purchase->shouldBeEqualTo($facade);
    }

    function it_should_throw_exception_if_errors(
        Client $guzzle,
        PurchaseServicesFacade $facade,
        ResponseInterface $response,
        StreamInterface $stream
    ) {
        $facade->sld = 'foo';
        $facade->tld = 'bar';
        $facade->service = 'WPPS';

        $response->getBody()->willReturn($stream);
        $stream->getContents()->willReturn(<<<EOX
<interface-response>
 <ErrCount>1</ErrCount>
 <errors>
  <Err1>Bad User name or Password</Err1>
 </errors>
</interface-response>
EOX
        );

        $guzzle->get('http://baseuri?uid=uid&pw=passwd&command=PURCHASESERVICES&Service=WPPS&sld=foo&tld=bar&responsetype=xml')->shouldBeCalled()->willReturn($response);

        $this->shouldThrow(PurchaseServicesFailedException::CLASS)->duringExecute($facade);
    }
}
