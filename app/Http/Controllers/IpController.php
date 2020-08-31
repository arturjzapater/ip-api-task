<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;

class IpController extends Controller
{
    private $guzzleClient;

    public function __construct(Client $client)
    {
        $this->guzzleClient = $client;
    }

    public function country(Request $request)
    {
        $ip = $this->getIp($request);
        [$response, $status_code] = $this->httpRequest($ip);

        return response()->json($response, $status_code);
    }

    private function getIp(Request $request)
    {
        return config('app.env') === 'production'
            ? $request->getClientIp()
            : config('dev.ip');
    }

    private function httpRequest($ip)
    {
        try {
            $response = $this->guzzleClient
                ->get("http://ip-api.com/json/$ip")
                ->getBody();
            $response = json_decode($response);

            return $this->parseResponse($response);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $exception = $e->getResponse()->getBody();
                $exception = json_decode($exception);

                return [$exception, $e->getCode()];
            }

            return [$e->getMessage(), 503];
        }
    }

    private function parseResponse($response)
    {
        if ($response->status !== 'success') {
            return [
                [
                    'status' => $response->status,
                    'message' => $response->message,
                ],
                400,
            ];
        }

        return [
            [
                'status' => 'success',
                'country' => $response->country,
            ],
            200,
        ];
    }
}
