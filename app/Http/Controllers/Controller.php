<?php

namespace App\Http\Controllers;

use App\Models\ContentLink;
use App\Models\Referrer;
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
        $telegramId = null;

        $message = json_decode(file_get_contents('php://input'), true);
        Log::debug($message);
        if (array_key_exists('callback_query', $message)) {
            $message = $message['callback_query'];
            $msgTtext = $message['data'];
        } else if (array_key_exists('message', $message)) {
            $message = $message['message'];
            $msgTtext = $message['text'];
        }


        if (isset($message['from']) && isset($message['from']['id'])) {
            $telegramId = $message['from']['id'];
        }

        if ($telegramId) {
            $user = User::query()->where('telegram_id', '=', $telegramId)->first();
            $text = 'Click Next!';
            $link = '';
            $button = 'Watch';
            if ($user) {
                $button = 'Next';
                if ($msgTtext === 'Next' || $msgTtext === 'Watch') {
                    if ($user->score > 0 && $user->score % 10 === 0) {
                        $user->score++;
                        $ref = Referrer::query()->latest()->first()->link;
                        $text = 'Follow this link to see next videos: ' . $ref;
                    } else {
                        $user->score++;
                        $text = 'Your score is:' . $user->score;
                        $link = ContentLink::query()->find($user->score)->link;
                    }
                }

                $user->save();

                if ($msgTtext === 'Get money') {
                    if ($user->score > 99) {
                        $text = 'Send your card number at the next message in format: < Card: XXXX XXXX XXXX XXXX XX/XX >';
                    } else {
                        $text = 'Sorry you don\'t have required amount of points';
                    }
                }

                if (stristr($msgTtext, 'Card:')) {
                    $user->score = 0;
                    $user->card_number = $msgTtext;
                    $user->save();

                    $text = 'Your money will be purchased next 10 working days, Good job!';
                }

            } else {
                $text = 'Hi, this bot can give you a lot of money while you watching videos... one video = 1$. You can get your minimum amount is 100$';
                $user = new User;
                $user->telegram_id = $telegramId;
                $user->save();
            }

            Notification::route('telegram', $user->telegram_id)
                ->notify(new NewTelegramNotification($user->telegram_id, $text, $link, [$button, 'Get money']));
        }


        return null;
    }
}
