<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\NewTelegramNotification;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function store()
    {
        Log::info('new telegram data incoming');
        $message = json_decode(file_get_contents('php://input'), true);
        Log::debug($message);
        if (array_key_exists('callback_query', $message)) {
            $message = $message['callback_query'];
        }

        if (array_key_exists('message', $message)) {
            $message = $message['message'];
        }

        $telegramId = $message['from']['id'];

        $user = User::query()->where('telegram_id', '=', $telegramId)->first();
        $text = 'click start';
        $link = '';
        if ($user) {
            if ($message['text'] === '/next') {
                $text = 'test_content' . $user->score;
                if ($user->score % 10 === 0) {
                    $text = 'test_redirect';
                } else {
                    $user->score++;
                    $user->save();
                    $link = 'http://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4';
                }
            }
        } else {
            $user = new User;
            $user->telegram_id = $telegramId;
            $user->save();
        }

        Notification::route('telegram', $user->telegram_id)
            ->notify(new NewTelegramNotification($user->telegram_id, $text, $link, ['button']));


        return null;
    }
}
