<?php

namespace ReadWorth\Application\UseCase\Slack;

use GuzzleHttp\Client;
use Illuminate\Contracts\View\View;
use ReadWorth\Infrastructure\Repository\UserRepository;
use ReadWorth\Infrastructure\Repository\SlackCredentialRepository;
use ReadWorth\Infrastructure\SlackAPI\SlackApiClient;

class CallbackSlack
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly SlackCredentialRepository $slackCredentialRepository
    ) {
    }

    public function callback(string $code): View
    {
        $apiClient = new Client();
        $response = $apiClient->post('https://slack.com/api/oauth.v2.access', [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'form_params' => [
                'client_id' => \Config::get('slack.clientId'),
                'client_secret' => \Config::get('slack.clientSecret'),
                'code' => $code,
                'grant_type' => 'authorization_code',
                'redirect_uri' => \Config::get('slack.redirectUri'),
            ],
        ]);
        $json = $response->getBody()->getContents();
        $body = json_decode($json, true);

        if (false === $body['ok']) {
            \Log::error('Slack連携中にエラーが発生しました', [$body]);
            return view('slack_authed')->with('message', 'Slack連携中にエラーが発生しました。時間を空け再度お試しください。');
        }

        $accessToken = $body['access_token'];
        $userId = $body['authed_user']['id'];

        $slackClient = new SlackApiClient(new Client(), $accessToken);
        $userInfo = $slackClient->userInfo($userId);

        $user = $this->userRepository->getByEmail($userInfo['user']['profile']['email']);

        if (!$user) {
            return view('slack_authed')->with('message', "Slackに登録しているメールアドレスと一致するユーザーが見つかりませんでした。\nSlackアカウントのメールアドレスと一致しているかご確認ください。");
        }

        $cacheKey = 'slack_connect_' . $user->id;

        if (!\Cache::has($cacheKey)) {
            return view('slack_authed')->with('message', 'Slack連携中にエラーが発生しました。時間を空け再度お試しください。');
        }

        $this->slackCredentialRepository->update(\Cache::get($cacheKey), $accessToken, $body['incoming_webhook']['channel'], $body['incoming_webhook']['channel_id']);
        \Cache::forget($cacheKey);
        return view('slack_authed')->with('message', 'Slack連携が完了しました。');
    }
}
