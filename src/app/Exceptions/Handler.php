<?php

namespace App\Exceptions;

use Throwable;
use Sentry\Laravel\Integration;
use ReadWorth\Infrastructure\SlackAPI\SlackApiClient;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            if (app()->bound("sentry")) {
                app("sentry")->captureException($e);
            }
        });
    }

    public function report(Throwable $e): void
    {
        \Log::error($e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);

        if ($this->shouldReport($e) && 'prod' === config('app.env')) {
            $postMessage = sprintf("File: %s\nLine: %d\nMessage: %s", $e->getFile(), $e->getLine(), $e->getMessage());
            $slackClient = new SlackApiClient(new \GuzzleHttp\Client(), config('slack.errorChannelAccessToken'));
            $slackClient->postMessage(config('slack.errorChannelId'), '', $postMessage);
            app("sentry")->captureException($e);
        }
    }
}
