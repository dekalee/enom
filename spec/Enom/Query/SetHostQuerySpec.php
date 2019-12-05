<?php

namespace spec\Dekalee\Enom\Query;

use Dekalee\Enom\Exception\SetHostException;
use Dekalee\Enom\Facade\SetHostFacade;
use Dekalee\Enom\Query\QueryInterface;
use Dekalee\Enom\Query\SetHostQuery;
use GuzzleHttp\Client;
use PhpSpec\ObjectBehavior;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class SetHostQuerySpec extends ObjectBehavior
{
    function let(Client $guzzle)
    {
        $this->beConstructedWith('uid', 'passwd', $guzzle, 'http://baseuri');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SetHostQuery::CLASS);
    }

    function it_should_be_a_query()
    {
        $this->shouldHaveType(QueryInterface::CLASS);
    }

    function it_should_set_the_host(
        SetHostFacade $facade,
        Client $guzzle,
        ResponseInterface $response,
        StreamInterface $stream
    ) {
        $facade->cdnUrl = '123.cdn.org';
        $facade->domain = 'foo.foo.bar.baz';

        $response->getBody()->willReturn($stream);
        $stream->getContents()->willReturn(<<<EOX
<?xml version="1.0" ?>
<interface-response>
 <DomainRRP>E</DomainRRP>
 <Command>SETHOSTS</Command>
 <ErrCount>0</ErrCount>
 <Server>Dev Workstation</Server>
 <Site>enom</Site>
 <IsLockable>True</IsLockable>
 <IsRealTimeTLD>True</IsRealTimeTLD>
 <Done>true</Done>
</interface-response>
EOX
        );

        $guzzle
            ->get('http://baseuri?uid=uid&pw=passwd&command=sethosts&sld=bar&tld=baz&HostName1=foo.foo&RecordType1=CNAME&Address1=123.cdn.org&responsetype=xml')
            ->shouldBeCalled()
            ->willReturn($response);

        $this->execute($facade);
    }

    function it_should_throw_exception_when_an_error_occurs(
        SetHostFacade $facade,
        Client $guzzle,
        ResponseInterface $response,
        StreamInterface $stream
    ) {
        $facade->cdnUrl = '123.cdn.org';
        $facade->domain = 'foo.foo.bar.baz';

        $response->getBody()->willReturn($stream);
        $stream->getContents()->willReturn(<<<EOX
<?xml version="1.0" ?>
<interface-response>
 <DomainRRP>E</DomainRRP>
 <Command>SETHOSTS</Command>
 <ErrCount>1</ErrCount>
 <errors>
  <Err1>Bad User name or Password</Err1>
 </errors>
 <Server>Dev Workstation</Server>
 <Site>enom</Site>
 <IsLockable>True</IsLockable>
 <IsRealTimeTLD>True</IsRealTimeTLD>
 <Done>true</Done>
</interface-response>
EOX
        );

        $guzzle
            ->get('http://baseuri?uid=uid&pw=passwd&command=sethosts&sld=bar&tld=baz&HostName1=foo.foo&RecordType1=CNAME&Address1=123.cdn.org&responsetype=xml')
            ->shouldBeCalled()
            ->willReturn($response);

        $this->shouldThrow(SetHostException::CLASS)->duringExecute($facade);
    }
}
