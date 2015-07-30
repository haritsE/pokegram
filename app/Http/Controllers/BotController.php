<?php

namespace App\Http\Controllers;

use Requests;

class BotController extends Controller
{
    public $token = 'insert_token_here';
    public function setHook($pass, $url)
    {
        Requests::register_autoloader();
        if($pass == 'insert_pass_here'){
            $post_data = [
                'url' => $url
            ];

            $response = Requests::post('https://api.telegram.org/bot' . $this->token . '/setWebhook', [], $post_data);
            $resp = json_decode($response->body, true);

            return $resp;         
        } else {
            return "Unauthorized";
        }
    }

    public function test($text){
        return $text;
    }

    public function pokemon(){
        $update = json_decode(file_get_contents('php://input'), true);
        $message = $update["message"];
        if(array_key_exists("text", $message)){
            $text = $message["text"];
            $chat = $message["chat"];
            $chat_id = $chat["id"];

            $pokemon_id = rand(1, 718);

            $pokemon = Requests::get('http://pokeapi.co/api/v1/pokemon/' . $pokemon_id, []);
            $pokemon_arr = json_decode($pokemon->body, true);

            $reply = "You just encountered a " . $pokemon_arr["name"] . "!\nWhat will you do?\n\nhttp://pokeapi.co/media/img/" . $pokemon_id . ".png"
;
            $post_data = [
                'chat_id' => $chat_id,
                'text'    => $reply,
                'reply_markup' => json_encode([
                    ['Attack', 'Item'],
                    ['Catch', 'Flee']
                ])
            ];

            $response = Requests::post('https://api.telegram.org/bot' . $this->token . '/sendMessage', [], $post_data);
        }
    }

    public function process($token){
        Requests::register_autoloader();

        if($token == $this->token){
            $this->pokemon();       
        } else {
            return "Wrong token.";
        }
    }
}