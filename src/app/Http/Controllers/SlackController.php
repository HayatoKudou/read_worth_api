<?php

namespace App\Http\Controllers;

use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Slack\SlackApiClient;
use App\Models\SlackCredential;

class SlackController extends Controller
{
    public function callback(Request $request): void
    {
        if ($request->has('error')) {
            return;
        }
        $client = new Client();
        $response = $client->post('https://slack.com/api/oauth.v2.access', [
            'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
            'form_params' => [
                    'client_id' => \Config::get('slack.clientId'),
                    'client_secret' => \Config::get('slack.clientSecret'),
                    'code' => $request->get('code'),
                    'grant_type' => 'authorization_code',
                    'redirect_uri' => \Config::get('slack.redirectUri'),
                ],
        ]);
        $json = $response->getBody()->getContents();
        $body = json_decode($json, true);

        if (false === $body['ok']) {
            return;
        }
        $accessToken = $body['access_token'];
        $userId = $body['authed_user']['id'];

        $slackClient = new SlackApiClient(new Client(), $accessToken);
        $userInfo = $slackClient->userInfo($userId);

        $user = User::where('email', $userInfo['user']['profile']['email'])->first();

        if (!$user) {
            \Log::debug('Slackに登録しているメールアドレスと一致するユーザーが見つかりません。');
        }

        SlackCredential::updateOrCreate([
            'client_id' => $user->client->id,
        ], [
            'access_token' => $accessToken,
            'channel_name' => $body['incoming_webhook']['channel'],
            'channel_id' => $body['incoming_webhook']['channel_id'],
        ]);
//        return redirect()->away(config('front.url'));
    }
}
