<?php

namespace App\Http\Controllers;

use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Slack\SlackApiClient;
use App\Models\SlackCredential;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Session;

class SlackController extends Controller
{
    public function connect(string $clientId): JsonResponse
    {
        $connectSlackUsers = Session::get('connect_slack_users', []);

        $userInSession = collect($connectSlackUsers)->firstWhere('userId', \Auth::id());
        \Log::debug($connectSlackUsers);
        \Log::debug($userInSession);

        if($userInSession){
            return response()->json();
        }

        Session::push('connect_slack_users', [
            'userId' => \Auth::id(),
            'clientId' => $clientId
        ]);
        \Log::debug(Session::get('connect_slack_users', []));

        return response()->json();
    }

    public function callback(Request $request): View
    {
        if ($request->has('error')) {
            return view('slack_authed')->with('message', 'Slack連携中にエラーが発生しました。時間を空け再度お試しください。');
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
            return view('slack_authed')->with('message', 'Slack連携中にエラーが発生しました。時間を空け再度お試しください。');
        }
        $accessToken = $body['access_token'];
        $userId = $body['authed_user']['id'];

        $slackClient = new SlackApiClient(new Client(), $accessToken);
        $userInfo = $slackClient->userInfo($userId);

        $user = User::where('email', $userInfo['user']['profile']['email'])->first();

        if (!$user) {
            return view('slack_authed')->with('message', "Slackに登録しているメールアドレスと一致するユーザーが見つかりませんでした。\nSlackアカウントのメールアドレスと一致しているかご確認ください。");
        }

        $connectSlackUsers = Session::get('connect_slack_users', []);
        $userInSession = collect($connectSlackUsers)->firstWhere('userId', \Auth::id());
        \Log::debug($connectSlackUsers);
        \Log::debug($userInSession);

        if(!$userInSession){
            return view('slack_authed')->with('message', 'Slack連携中にエラーが発生しました。時間を空け再度お試しください。');
        }

        SlackCredential::updateOrCreate([
            'client_id' => $userInSession["clientId"],
        ], [
            'access_token' => $accessToken,
            'channel_name' => $body['incoming_webhook']['channel'],
            'channel_id' => $body['incoming_webhook']['channel_id'],
        ]);
        return view('slack_authed')->with('message', 'Slack連携が完了しました。');
    }
}
