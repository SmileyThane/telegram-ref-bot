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
        $text = 'Click Next!';
        $link = '';
        $button = 'Watch';
        if ($user) {
            $button = 'Next';
            if ($msgTtext === 'Next' || $msgTtext === 'Watch') {
                if ($user->score > 0 && $user->score % 10 === 0) {
                    $user->score++;
                    $text = 'Follow this link to see next videos: https://test.com';
                } else {
                    $user->score++;
                    $user->save();
                    $text = 'Your score is:' . $user->score;
                    $link = 'https://radient360.com/wp-content/uploads/2020/03/file_example_MP4_480_1_5MG.mp4';
                }
            }

            if ($msgTtext === 'Get money') {
                $text = 'Send your card number at the next message in format: Card: XXXX XXXX XXXX XXXX XX/XX';
            }

            if (stristr($msgTtext, 'Card:')) {
                $user->score = 0;
                $user->save();
                $text = 'Your money will be purchased next 10 working days, Good job!';
            }

        } else {
            $text = 'Hi, this bot can give you a lot of monet while you watching videos... one video = 1$. You can get your minimum amount is 100$';
            $user = new User;
            $user->telegram_id = $telegramId;
            $user->save();
        }

        Notification::route('telegram', $user->telegram_id)
            ->notify(new NewTelegramNotification($user->telegram_id, $text, $link, [$button, 'Get money']));

        return null;
    }
}
