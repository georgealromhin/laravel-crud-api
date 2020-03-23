<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;

class SendNotification extends Controller
{
    //
    public function ToTelegram(Request $request){
                
        $text = "A new contact us query\n"
        . "<b>Email Address: </b>\n"
        . "$request->email\n"
        . "<b>Message: </b>";

        Telegram::sendMessage([
            'chat_id' => env('TELEGRAM_CHANNEL_ID', '838174031'),
            'parse_mode' => 'HTML',
            'text' => $text,
            
        ]);
    }
}
