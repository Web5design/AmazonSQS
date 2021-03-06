<?php

/**
 * This file is part of the AmazonSQS package.
 *
 * (c) Christian Eikermann <christian@chrisdev.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\AmazonSQS;

use AmazonSQS\Client;

use apiTalk\Request;
use apiTalk\Response;

class ClientTest extends \PHPUnit_Framework_TestCase
{

    public function testSendGet()
    {
        $mockAdapter = $this->getMockBuilder('apiTalk\Adapter\AdapterInterface')
                            ->setMethods(array('send'))
                            ->getMock();
        
        $mockAdapter->expects($this->once())
                           ->method('send')
                           ->will($this->returnValue(new Response()));
        
        $time = gmmktime(12, 12, 12, 03, 05, 2012);
        $request = new Request('http://www.test.com/path', Request::METHOD_GET, array('params' => 'some_value'));
        
        $client = new Client('AccessKey', 'SecretKey', $mockAdapter);
        $response = $client->send($request, $time);
        
        $this->assertEquals('AccessKey', $request->getParameter('AWSAccessKeyId'), 'Wrong aws access key');
        $this->assertEquals('2012-03-05T12:12:12Z', $request->getParameter('Expires'), 'Wrong expire date');
        $this->assertEquals('HmacSHA256', $request->getParameter('SignatureMethod'), 'Wrong signature method');
        $this->assertEquals('2', $request->getParameter('SignatureVersion'), 'Wrong signature version');
        $this->assertEquals('2011-10-01', $request->getParameter('Version'), 'Wrong version');
        $this->assertEquals('some_value', $request->getParameter('params'), 'Wrong custom value');
        $this->assertEquals('50pYUTp5qyGfV7GcQhTbZCh6ZwFLQWcT1klC4km3QZ8=', $request->getParameter('Signature'), 'Wrong signature');  
    }

    public function testSendPost()
    {
        $mockAdapter = $this->getMockBuilder('apiTalk\Adapter\AdapterInterface')
                            ->setMethods(array('send'))
                            ->getMock();
        
        $mockAdapter->expects($this->once())
                           ->method('send')
                           ->will($this->returnValue(new Response()));
        
        $time = gmmktime(12, 12, 12, 03, 05, 2012);
        $request = new Request('http://www.test.com/path', Request::METHOD_POST, array('params' => 'some_value', 'Signature' => 'old_signature'));
        
        $client = new Client('AccessKey', 'SecretKey', $mockAdapter);
        $response = $client->send($request, $time);
        
        $this->assertEquals('AccessKey', $request->getParameter('AWSAccessKeyId'), 'Wrong aws access key');
        $this->assertEquals('2012-03-05T12:12:12Z', $request->getParameter('Expires'), 'Wrong expire date');
        $this->assertEquals('HmacSHA256', $request->getParameter('SignatureMethod'), 'Wrong signature method');
        $this->assertEquals('2', $request->getParameter('SignatureVersion'), 'Wrong signature version');
        $this->assertEquals('2011-10-01', $request->getParameter('Version'), 'Wrong version');
        $this->assertEquals('some_value', $request->getParameter('params'), 'Wrong custom value');
        $this->assertEquals('application/x-www-form-urlencoded', $request->getHeader('Content-Type'), 'Wrong content-type header');
        $this->assertEquals('ViabijT4j0iLE62G+363QA/CW7kejwUQB7sN6GPGabs=', $request->getParameter('Signature'), 'Wrong signature');  
    }    
}
