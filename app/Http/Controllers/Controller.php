<?php

namespace App\Http\Controllers;

use App\Models\ContentLink;
use App\Models\Label;
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
        $labels = [];
        $labelsRaw = Label::all();
        foreach ($labelsRaw as $labelRaw) {
            $labels[$labelRaw->label] = $labelRaw->alias;
        }

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
            $text = $labels['click_next'];
            $link = '';
            $button = $labels['watch'];
            if ($user) {
                $button = $labels['next'];
                if ($msgTtext === $labels['next'] || $labels['watch']) {
                    if (time() - $user->updated_at->format('U') > 30) {
                        if ($user->score > 0 && $user->score % 10 === 0) {
                            $user->score++;
                            $ref = Referrer::query()->latest()->first()->link;
                            $text = $labels['follow_link_text'] . $ref;
                        } else {
                            $user->score++;
                            $text = $labels['your_score_text'] . $user->score;
                            if ($contentLink = ContentLink::query()->find($user->score)) {
                                $link = $contentLink->link;
                            } else {
                                $allLinks = ContentLink::all();
                                $link = $allLinks[rand(1, count($allLinks))]->link;
                            }
                        }
                    } else {
                        $text = $labels['watch_video_text'] . $user->score;
                        $link = ContentLink::query()->find($user->score)->link;
                    }

                }

                $user->save();

                if ($msgTtext === $labels['get_money']) {
                    if ($user->score > 19) {
                        $text = $labels['send_card_number_text'];
                    } else {
                        $text = $labels['card_sending_not_available_text'];
                    }
                }

                if (stristr($msgTtext, $labels['card_prefix'])) {
                    $user->score = 0;
                    $user->card_number = $msgTtext;
                    $user->save();
                    $text = $labels['card_sending_successful_text'];
                }

            } else {
                $text = $labels['welcome_text'];
                $user = new User;
                $user->telegram_id = $telegramId;
                $user->save();
            }

            Notification::route('telegram', $user->telegram_id)
                ->notify(new NewTelegramNotification($user->telegram_id, $text, $link, [$button, $labels['get_money']]));
        }


        return null;
    }
}
