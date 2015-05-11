<?php
/**
 * Created by PhpStorm.
 * User: mattia
 * Date: 03/04/2015
 * Time: 11.27
 */

namespace MWM\LogBundle\Tests;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TestTest extends WebTestCase{

    public function testIndex(){
        $client = static::createClient();

        $crawler = $client->request('GET', '/app/log');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("Homepage")')->count() > 0);
    }

}