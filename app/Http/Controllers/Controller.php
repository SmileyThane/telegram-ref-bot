<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\NewTelegramNotification;
use Illuminate\Database\Eloquent\Model;
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
        if (array_key_exists('message', $message)) {
            Log::debug($message['message']);
            $message = $message['message'];
            $user = User::query()->where('telegram_id', '=', $message['from']['id'])->first();
            $text = 'click start';
            $link = '';
            if ($user) {
                if ($message['text'] === 'next') {
                    $text = 'test_content' . $user->score;
                    if ($user->score % 10 === 0) {
                        $text = 'test_redirect';
                        $link = 'https://test.com';
                    } else {
                        $user->score++;
                        $user->save();
                        $link = 'https://www.tiktok.com/@rabeecak/video/7194413716010536218';
                    }
                }
            } else {
                $user = new User;
                $user->telegram_id = $message['from']['id'];
                $user->save();
            }

            Notification::route('telegram', $user->telegram_id)
                ->notify(new NewTelegramNotification($user->telegram_id, $text, $link, ['button']));

        }

        return null;
    }
}
