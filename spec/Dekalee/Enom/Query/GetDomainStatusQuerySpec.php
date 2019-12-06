<?php

namespace spec\Dekalee\Enom\Query;

use Dekalee\Enom\Exception\GetDomainStatusFailedException;
use Dekalee\Enom\Query\GetDomainStatusQuery;
use Dekalee\Enom\Facade\GetDomainStatusFacade;
use Dekalee\Enom\Query\QueryInterface;
use GuzzleHttp\Client;
use PhpSpec\ObjectBehavior;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class GetDomainStatusQuerySpec extends ObjectBehavior
{
    function let(Client $guzzle)
    {
        $this->beConstructedWith('uid', 'passwd', $guzzle, 'http://baseuri');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(GetDomainStatusQuery::CLASS);
    }

    function it_should_be_a_query()
    {
        $this->shouldHaveType(QueryInterface::CLASS);
    }

    function it_should_create_a_getDomainStatus(
        Client $guzzle,
        GetDomainStatusFacade $facade,
        ResponseInterface $response,
        StreamInterface $stream
    ) {
        $facade->sld = 'foo';
        $facade->tld = 'bar';

        $response->getBody()->willReturn($stream);
        $stream->getContents()->willReturn(<<<EOX
<interface-response>
 <DomainStatus>
  <DomainName>resellerdocs.net</DomainName>
  <InAccount>1</InAccount>
  <ExpDate>10/25/2007 5:18:12 AM</ExpDate>
  <OrderID>156375934</OrderID>
 </DomainStatus>
 <Command>GETDOMAINSTATUS</Command>
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
 <ExecTime>0.563</ExecTime>
 <Done>true</Done>
 <RequestDateTime>12/12/2011 5:05:44 AM</RequestDateTime>
 <debug></debug>
</interface-response>
EOX
        );


        $guzzle->get('http://baseuri?uid=uid&pw=passwd&command=GETDOMAINSTATUS&sld=foo&tld=bar&responsetype=xml')->shouldBeCalled()->willReturn($response);

        $getDomainStatus = $this->execute($facade);

        $getDomainStatus->shouldHaveType(GetDomainStatusFacade::CLASS);
        $getDomainStatus->shouldBeEqualTo($facade);
        $getDomainStatus->inAccount->shouldBeEqualTo(1);
    }

    function it_should_throw_exception_if_errors(
        Client $guzzle,
        GetDomainStatusFacade $facade,
        ResponseInterface $response,
        StreamInterface $stream
    ) {
        $facade->sld = 'foo';
        $facade->tld = 'bar';

        $response->getBody()->willReturn($stream);
        $stream->getContents()->willReturn(<<<EOX
<interface-response>
 <DomainStatus>
  <DomainName>resellerdocs.net</DomainName>
  <InAccount>1</InAccount>
  <ExpDate>10/25/2007 5:18:12 AM</ExpDate>
  <OrderID>156375934</OrderID>
 </DomainStatus>
 <Command>GETDOMAINSTATUS</Command>
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
 <ExecTime>0.563</ExecTime>
 <Done>true</Done>
 <RequestDateTime>12/12/2011 5:05:44 AM</RequestDateTime>
 <debug></debug>
</interface-response>
EOX
        );


        $guzzle->get('http://baseuri?uid=uid&pw=passwd&command=GETDOMAINSTATUS&sld=foo&tld=bar&responsetype=xml')->shouldBeCalled()->willReturn($response);

        $this->shouldThrow(GetDomainStatusFailedException::CLASS)->duringExecute($facade);
    }

    function it_should_not_throw_exception_if_only_order_id_error(
        Client $guzzle,
        GetDomainStatusFacade $facade,
        ResponseInterface $response,
        StreamInterface $stream
    ) {
        $facade->sld = 'foo';
        $facade->tld = 'bar';

        $response->getBody()->willReturn($stream);
        $stream->getContents()->willReturn(<<<EOX
<interface-response>
 <DomainStatus>
  <DomainName>resellerdocs.net</DomainName>
  <InAccount>1</InAccount>
  <ExpDate>10/25/2007 5:18:12 AM</ExpDate>
  <OrderID>156375934</OrderID>
 </DomainStatus>
 <Command>GETDOMAINSTATUS</Command>
 <Language>eng</Language>
 <ErrCount>1</ErrCount>
 <errors>
  <Err1>The order number specified does not belong to this account</Err1>
 </errors>
 <ResponseCount>0</ResponseCount>
 <MinPeriod>1</MinPeriod>
 <MaxPeriod>10</MaxPeriod>
 <Server>SJL21WRESELLT01</Server>
 <Site>eNom</Site>
 <IsLockable>True</IsLockable>
 <IsRealTimeTLD>True</IsRealTimeTLD>
 <TimeDifference>+08.00</TimeDifference>
 <ExecTime>0.563</ExecTime>
 <Done>true</Done>
 <RequestDateTime>12/12/2011 5:05:44 AM</RequestDateTime>
 <debug></debug>
</interface-response>
EOX
        );


        $guzzle->get('http://baseuri?uid=uid&pw=passwd&command=GETDOMAINSTATUS&sld=foo&tld=bar&responsetype=xml')->shouldBeCalled()->willReturn($response);
        $getDomainStatus = $this->execute($facade);

        $getDomainStatus->shouldHaveType(GetDomainStatusFacade::CLASS);
        $getDomainStatus->shouldBeEqualTo($facade);
        $getDomainStatus->inAccount->shouldBeEqualTo(1);
    }
}
