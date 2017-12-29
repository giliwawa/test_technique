<?php
/**
 * Created by PhpStorm.
 * User: alaak
 * Date: 29/12/17
 * Time: 12:38
 */

namespace Tests\AppBundle\Controller;

use GuzzleHttp as Guzzle;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{

    public function testStore(){
        $client = new Guzzle\Client(['base_uri' => 'http://localhost:8000/']);

        $user = [
            "nom" => "test",
            "prenom" => "case",
            "email" => "test@case.com"
        ];
        $response = $client->get("/users");

        $this->assertEquals(200, $response->getStatusCode());
    }
}