<?php


use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\CallbackQuery;
use TelegramBot\Api\Types\Message;
use TelegramBot\Api\Types\Update;


class BotHelper
{

    /**
     * @param \TelegramBot\Api\Client | BotApi $bot
     */
    public static function initBot(\TelegramBot\Api\Client $bot)
    {
        session_start();
        $bot
            ->command(
                'start',
                static function (Message $message) use ($bot) {
                    try {
                        $chatId = $message->getChat()->getId();
                        $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup([
                            [['text' => "Uzbek"], ['text' => "Rus"]],
                            [["text" => "English"]]
                        ], null, true);

                        $bot->sendMessage($chatId, "Assalomu Alaykum Webking O'quv markazining reception botiga xush kelibsiz!", "HTML", false, null, $keyboard);


                    } catch (Exception $e) {
                    }

                    return true;
                })
            ->callbackQuery(
                static function (CallbackQuery $query) use ($bot) {
                    try {
                        $chatId = $query->getMessage()->getChat()->getId();
                        $data = $query->getData();
                        $messageId = $query->getMessage()->getMessageId();

                    } catch (Exception $e) {
                    }

                })
            ->editedMessage(
                static function (Message $message) use ($bot) {
                    try {

                    } catch (Exception $e) {
                    }
                })
            ->on(
                static function (Update $update) use ($bot) {
                    return true;
                },
                static function (Update $update) use ($bot) {
                    try {
                        $db = mysqli_connect('localhost', 'user', 'password', 'webteam5bot');

//                        var_dump($db);
                        $text = $update->getMessage()->getText();
                        $chat_id = $update->getMessage()->getChat()->getId();

                        $til_massiv = [
                            "Uzbek", "English", 'Rus'
                        ];
                        $nomer_soraw = [
                            "Uzbek" => "Raqamni yuborish",
                            "English" => "Send Number",
                            'Rus' => "Отправьте номер"
                        ];


                        $kurslar = [
                            "Uzbek" => "Kurslar",
                            "English" => "Courses",
                            'Rus' => "Курсы"
                        ];
                        $manzil = [
                            "Uzbek" => "Manzil",
                            "English" => "Location",
                            'Rus' => "Адрес"
                        ];
                        $back = [
                            "Uzbek" => "Orqaga",
                            "English" => "Back",
                            'Rus' => "Назад"
                        ];


                        if (in_array($text, $til_massiv)) {


                            $user_bormi = $db->query("select chat_id from users where chat_id = '$chat_id'")->num_rows;
                            if ($user_bormi == 0) {

                                $db->query("insert into users (chat_id,lang) values ('$chat_id','$text')");
                            } else {
                                $db->query("update users set lang = '$text' where chat_id ='$chat_id'");
                            }

                            $nomer_bormi = $db->query("select number from users where chat_id = '$chat_id'")->fetch_assoc();



                            $til = $db->query("select lang from users where chat_id = '$chat_id' ")->fetch_assoc()['lang'];

                            if ($nomer_bormi['number'] == NULL) {
                                $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup([[['text' => "📞 " . $nomer_soraw[$til], 'request_contact' => true]]], true, true);
                                $bot->sendMessage($chat_id, $nomer_soraw[$til], null, false, null, $keyboard);

                            }else{
                                $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup([

                                    [['text' => $kurslar[$til]], ['text' => $manzil[$til]]],
                                    [['text' => $back[$til]]]

                                ], true, true);


                                $bot->sendMessage($chat_id, "Menu", null, false, null, $keyboard);

                            }
                        } else {

                            $til = $db->query("select lang from users where chat_id = '$chat_id' ")->fetch_assoc()['lang'];


                            if ($update->getMessage()->getContact()) {
                                $number = $update->getMessage()->getContact()->getPhoneNumber();
                                $db->query("update users set number = '$number' where chat_id ='$chat_id'");

                                $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup([

                                    [['text' => $kurslar[$til]], ['text' => $manzil[$til]]],
                                    [['text' => $back[$til]]]

                                ], true, true);


                                $bot->sendMessage($chat_id, "Menu", null, false, null, $keyboard);
                            }

                            if (in_array($text, $back)) {
                                $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup([
                                    [['text' => "Uzbek"], ['text' => "Rus"]],
                                    [["text" => "English"]]
                                ], null, true);
                                $bot->sendMessage($chat_id, "Assalomu Alaykum Webking O'quv markazining reception botiga xush kelibsiz!", null, false, null, $keyboard);

                            }

                            if(in_array($text,$manzil)){
                                $bot->sendLocation($chat_id, 40.876645, 71.976965);
                                $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup([


                                    [['text' => $back[$til]]]

                                ], true, true);
                                $bot->sendMessage($chat_id, "Adress", null, false, null, $keyboard);


                            }

                            if(in_array($text,$kurslar)){
                               $kurslar_title= $db->query("select title_$til from courses")->fetch_all();

                               $keyboard=[];

                               $uzunlik =count(array_chunk($kurslar_title,2) );
                                foreach (array_chunk($kurslar_title,2) as $key => $item) {

                                    if(count($item)==2){
                                        if($key==($uzunlik-1)){

                                            $keyboard[]=[['text'=>$item[0][0]],['text'=>$item[1][0]]];
                                            $keyboard[]=[['text'=>$back[$til]]];
                                        }else{

                                            $keyboard[]=[['text'=>$item[0][0]],['text'=>$item[1][0]]];
                                        }
                                    }else{
                                        if($key==($uzunlik-1)){

                                            $keyboard[]=[['text'=>$item[0][0]],['text'=>$back[$til]]];
                                        }
                                    else{

                                            $keyboard[]=[['text'=>$item[0][0]]];
                                        }

                                    }

                               }


                                $murkup = new \TelegramBot\Api\Types\ReplyKeyboardMarkup($keyboard,null,true);
                                $bot->sendMessage($chat_id,$kurslar[$til],null,false,null,$murkup);
                            } // agar kurslar bosilsa


                            $titllar= $db->query("select title_$til from courses")->fetch_all();
                            $titllar_massiv=[];
                            foreach ($titllar as $item) {
                                $titllar_massiv[]=$item[0];
                            }
                            if(in_array($text,$titllar_massiv)){

                                $course_detail = $db->query("select title_$til, description_$til, image from courses where title_$til = '$text'")->fetch_assoc();

                                $image= "images/".$course_detail['image'];
                                $title= strtoupper( $course_detail["title_$til"]);
                                $description= $course_detail["description_$til"];



                                $bot->sendPhoto($chat_id,new CURLFile($image),"$title\n\n $description\n\n📞 +99899 123 45 67");

                            }




                        }


                    } catch (Exception $e) {
                    }
                    return true;
                }
            );


    }
}