<?php

namespace spec\Dekalee\Enom\Query;

use Dekalee\Enom\Exception\PurchaseFailedException;
use Dekalee\Enom\Query\PurchaseQuery;
use Dekalee\Enom\Facade\PurchaseFacade;
use Dekalee\Enom\Query\QueryInterface;
use GuzzleHttp\Client;
use PhpSpec\ObjectBehavior;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class PurchaseQuerySpec extends ObjectBehavior
{
    function let(Client $guzzle)
    {
        $this->beConstructedWith('uid', 'passwd', $guzzle, 'http://baseuri');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PurchaseQuery::CLASS);
    }

    function it_should_be_a_query()
    {
        $this->shouldHaveType(QueryInterface::CLASS);
    }

    function it_should_create_a_purchase(
        Client $guzzle,
        PurchaseFacade $facade,
        ResponseInterface $response,
        StreamInterface $stream
    ) {
        $facade->sld = 'foo';
        $facade->tld = 'bar';

        $response->getBody()->willReturn($stream);
        $stream->getContents()->willReturn(<<<EOX
<interface-response>
 <OrderID>157781021</OrderID>
 <TotalCharged>8.95</TotalCharged>
 <RegistrantPartyID>{1C3E82CA-FE3F-E011-B28A-005056BC7747}</RegistrantPartyID>
 <RRPCode>200</RRPCode>
 <RRPText>Command completed successfully - 157781021</RRPText>
 <Command>PURCHASE</Command>
 <Language>eng</Language>
 <ErrCount>0</ErrCount>
 <ResponseCount>0</ResponseCount>
 <MinPeriod>1</MinPeriod>
 <MaxPeriod>10</MaxPeriod>
 <Server>SJL21WRESELLT01</Server>
 <Site>eNom</Site>
 <IsLockable>True</IsLockable>
 <IsRealTimeTLD>True</IsRealTimeTLD>
 <TimeDifference>+08.00</TimeDifference>
 <ExecTime>4.907</ExecTime>
 <Done>true</Done>
 <RequestDateTime>12/9/2011 3:58:42 AM</RequestDateTime>
 <debug></debug>
</interface-response>
EOX
        );


        $guzzle->get('http://baseuri?uid=uid&pw=passwd&command=Purchase&sld=foo&tld=bar&UseDNS=default&responsetype=xml')->shouldBeCalled()->willReturn($response);

        $purchase = $this->execute($facade);

        $purchase->shouldHaveType(PurchaseFacade::CLASS);
        $purchase->shouldBeEqualTo($facade);
        $purchase->orderId->shouldBeEqualTo('157781021');
    }

    function it_should_throw_exception_if_errors(
        Client $guzzle,
        PurchaseFacade $facade,
        ResponseInterface $response,
        StreamInterface $stream
    ) {
        $facade->sld = 'foo';
        $facade->tld = 'bar';

        $response->getBody()->willReturn($stream);
        $stream->getContents()->willReturn(<<<EOX
<interface-response>
 <OrderID>157781021</OrderID>
 <TotalCharged>8.95</TotalCharged>
 <RegistrantPartyID>{1C3E82CA-FE3F-E011-B28A-005056BC7747}</RegistrantPartyID>
 <RRPCode>200</RRPCode>
 <RRPText>Command completed successfully - 157781021</RRPText>
 <Command>PURCHASE</Command>
 <Language>eng</Language>
 <ErrCount>1</ErrCount>
 <errors>
  <Err1>Bad User name or Password</Err1>
 </errors>
 <ResponseCount>0</ResponseCount>
 <MinPeriod>1</MinPeriod>
 <MaxPeriod>10</MaxPeriod>
 <Server>SJL21WRESELLT01</Server>
 <Site>eNom</Site>
 <IsLockable>True</IsLockable>
 <IsRealTimeTLD>True</IsRealTimeTLD>
 <TimeDifference>+08.00</TimeDifference>
 <ExecTime>4.907</ExecTime>
 <Done>true</Done>
 <RequestDateTime>12/9/2011 3:58:42 AM</RequestDateTime>
 <debug></debug>
</interface-response>
EOX
        );


        $guzzle->get('http://baseuri?uid=uid&pw=passwd&command=Purchase&sld=foo&tld=bar&UseDNS=default&responsetype=xml')->shouldBeCalled()->willReturn($response);

        $this->shouldThrow(PurchaseFailedException::CLASS)->duringExecute($facade);
    }
}
