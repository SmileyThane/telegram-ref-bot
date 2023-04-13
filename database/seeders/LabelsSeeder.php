<?php

namespace Database\Seeders;

use App\Models\Label;
use Illuminate\Database\Seeder;

class LabelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Label::query()->where('id', '>', 0)->delete();

        $label = new Label();
        $label->id = 1;
        $label->label = 'click_next';
        $label->alias = 'Click next';
        $label->save();

        $label = new Label();
        $label->id = 2;
        $label->label = 'watch';
        $label->alias = 'Watch';
        $label->save();

        $label = new Label();
        $label->id = 3;
        $label->label = 'next';
        $label->alias = 'Next';
        $label->save();

        $label = new Label();
        $label->id = 4;
        $label->label = 'follow_link_text';
        $label->alias = 'Follow this link to see next videos: ';
        $label->save();

        $label = new Label();
        $label->id = 5;
        $label->label = 'get_money';
        $label->alias = 'Get money';
        $label->save();

        $label = new Label();
        $label->id = 6;
        $label->label = 'send_card_number_text';
        $label->alias = 'Send your card number at the next message in format: < Card: XXXX XXXX XXXX XXXX XX/XX';
        $label->save();

        $label = new Label();
        $label->id = 7;
        $label->label = 'card_sending_not_available_text';
        $label->alias = 'Sorry you don\'t have required amount of points';
        $label->save();

        $label = new Label();
        $label->id = 8;
        $label->label = 'card_sending_successful_text';
        $label->alias = 'Your money will be purchased next 10 working days, Good job!';
        $label->save();

        $label = new Label();
        $label->id = 9;
        $label->label = 'welcome_text';
        $label->alias = 'Hi, this bot can give you a lot of money while you watching videos... one video = 1$. You can get your minimum amount is 100$';
        $label->save();

        $label = new Label();
        $label->id = 10;
        $label->label = 'watch_video_text';
        $label->alias = 'Please, watch latest video!';
        $label->save();

    }
}
