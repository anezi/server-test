<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testAuthHttpWithoutUsernameAndPassword()
    {
        $client = static::createClient();

        $client->request('GET', '/http/basic');

        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    public function testAuthHttpWithIncorrectLogin()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'user_http_basic',
            'PHP_AUTH_PW'   => 'invalid_httpbasicpass',
        ]);

        $client->request('GET', '/http/basic');

        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    public function testAuthHttpWithCorrectLogin()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'user_http_basic',
            'PHP_AUTH_PW'   => 'httpbasicpass',
        ]);

        $client->request('GET', '/http/basic');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
