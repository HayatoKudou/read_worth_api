<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Book;
use App\Models\BookHistory;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class SlackController extends Controller
{
//    private Client $httpClient;
//
//    public function __construct()
//    {
//        $this->httpClient = new Client([
//            'base_uri' => 'https://api.twitter.com/1.1/',
//            'handler' => $stack,
//            'auth' => 'oauth',
//        ]);
//    }

//    public function callback(Request $request)
    public function callback()
    {
        $client = new Client;
        $code = '3812085668740.3798256075319.728cc2a07655d2f580f086ee8b10c67b16b1d91561603811c56765492f31527d';
        $option = [
            'headers' =>
                [
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ],
            'form_params' =>
                [
                    'client_id' => '3812085668740.3835544940032',
                    'client_secret' => '4937c87b0e5b312d1cf566b0eda78669',
                    'code' => $code,
                    'grant_type' => 'authorization_code',
                    'redirect_uri' => 'https://api-readworth.info/api/slack/callback'
                ],
        ];
        $response = $client->post('https://slack.com/api/oauth.v2.access', $option);
        $json = $response->getBody()->getContents();
        $body = json_decode($json, true);
        \Log::debug($body);
    }
}
