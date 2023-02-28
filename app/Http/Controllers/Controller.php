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
                    $link = 'https://v16-webapp-prime.tiktok.com/video/tos/useast2a/tos-useast2a-pve-0037c001-aiso/oYo6X7QUhozAfPzfOJBRXja2DBZDVCQaI8NWti/?a=1988&ch=0&cr=0&dr=0&lr=tiktok&cd=0%7C0%7C1%7C0&cv=1&br=1444&bt=722&cs=0&ds=3&ft=TYOI31w5vjVQ99_gLPTsd3cw4i_a7uwQAVyeN_SyJE&mime_type=video_mp4&qs=0&rc=aGU6NjxnaTg2NzozPGVlaUBpanBobjY6Zm53aTMzZjgzM0AxL2ExXjUtNmAxYS80MTFiYSNfMWNgcjRfanBgLS1kL2Nzcw%3D%3D&btag=80000&expire=1677619506&l=20230228152447EDD1ECDDB1B90010F321&ply_type=2&policy=2&signature=1baf71831c9fc406440b6148b1b37382&tk=tt_chain_token';
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
