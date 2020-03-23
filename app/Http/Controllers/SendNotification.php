<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Telegram\Bot\Laravel\Facades\Telegram;

class SendNotification extends Controller
{
    //
    public function ToTelegram(Request $request){
                
        Telegram::sendMessage([
            'chat_id' => env('TELEGRAM_CHANNEL_ID', '******'),
            'parse_mode' => 'HTML',
            'text' => $request->text,
        ]);
    }
    public function SendMail(Request $request){

        $data = [
          "email" =>  $request->email,
          "msg" =>  $request->message
        ];
        Mail::send([], [], function($message) use ($data) {
          $message->from('example', 'name');
          $message->to($data['email']);
          $message->subject('New Message');
          $message->setBody($data['message'], 'text/html');
        });

    }
}
