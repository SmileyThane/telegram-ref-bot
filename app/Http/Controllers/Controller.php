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
        $msgTtext = '';
        $message = json_decode(file_get_contents('php://input'), true);
        if (array_key_exists('callback_query', $message)) {
            $message = $message['callback_query'];
            Log::debug($message['message']['reply_markup']);
            $msgTtext = $message['message']['reply_markup']['inline_keyboard'][0][0]['text'];
        } else if (array_key_exists('message', $message)) {
            $message = $message['message'];
            Log::debug($message);
            $msgTtext = $message['text'];
        }

        $telegramId = $message['from']['id'];

        $user = User::query()->where('telegram_id', '=', $telegramId)->first();
        $text = 'click start';
        $link = '';
        if ($user) {
            Log::debug('telegram_id=' . $user->telegram_id . ': score=' . $user->score);
            if ($msgTtext === 'next') {
                $text = 'test content';
                if ($user->score > 0 && $user->score % 10 === 0) {
                    $text = 'test redirect';
                } else {
                    $link = 'http://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4';
                }
                $user->score++;
                $user->save();

            }
        } else {
            $user = new User;
            $user->telegram_id = $telegramId;
            $user->save();
        }

        Notification::route('telegram', $user->telegram_id)
            ->notify(new NewTelegramNotification($user->telegram_id, $text, $link, ['next']));

        return null;
    }
}
