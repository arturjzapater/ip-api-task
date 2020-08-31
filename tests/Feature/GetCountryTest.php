<?php

namespace Tests\Feature;

use App\Http\Controllers\IpController;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use Tests\TestCase;

class GetCountryTest extends TestCase
{
    public function testCallToApi()
    {
        $this->app->bind(IpController::class, function() {
            $data = json_encode([
                'status' => 'success',
                'country' => 'Japan',
                'city' => 'Oosaka',
            ]);
        
            $response = new Response(200, ['Content-Type' => 'application/json'], $data);
            return $this->createMockClient([$response]);
        });

        $response = $this->get('/api/country');

        $response->assertStatus(200);
        $response->assertExactJson([
            'status' => 'success',
            'country' => 'Japan',
        ]);
    }

    public function testCallToApiError()
    {
        $this->app->bind(IpController::class, function() {
            $request = new Request('GET', 'test');
            $exception = new RequestException('Error Communicating with Server', $request);

            return $this->createMockClient([$exception]);
        });

        $response = $this->get('/api/country');

        $response->assertStatus(503);
        $response->assertExactJson(['Error Communicating with Server']);
    }

    public function testCallToApiWrongIP()
    {
        $this->app->bind(IpController::class, function() {
            $data = json_encode([
                'status' => 'fail',
                'message' => 'invalid query',
                'query' => 'failing query',
            ]);
        
            $response = new Response(200, ['Content-Type' => 'application/json'], $data);
            return $this->createMockClient([$response]);
        });

        $response = $this->get('/api/country');

        $response->assertStatus(400);
        $response->assertExactJson([
            'status' => 'fail',
            'message' => 'invalid query',
        ]);
    }

    private function createMockClient($responses)
    {
        $mock = new MockHandler($responses);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);
    
        return new IpController($client);
    }
}
