<?php

include  __DIR__.'/vendor/autoload.php';
include 'BotHelper.php';

use TelegramBot\Api\BotApi;
use TelegramBot\Api\Client;

class BotController
{
    public static $_bot;
    public $name;


    /**
     * @return Client|BotApi
     */
    public static function botInstance()
    {
        if (static::$_bot === null) {
            /**
             * @var $bot Client|BotApi
             */
            $bot = new Client('7479822506:AAGjV9AJ3L3kX7eCGzMeO_9a9G-mnb-uD_Q');

            BotHelper::initBot($bot);

            static::$_bot = $bot;
        }
        return static::$_bot;
    }

    public function actionRun()
    {
        /**
         * @var $bot Client | BotApi
         */
        $offset = 0;
        $timeout = 100;
        $limit = 100;
        $bot = static::botInstance();
        while (true) {
            try {
                $updates = $bot->getUpdates($offset, $limit);

                if (count($updates)) {
                    $bot->handle($updates);
                    $offset = $updates[count($updates) - 1]->getUpdateId() + 1;
                    //echo $offset . PHP_EOL;
                }
            } catch (Exception $e) {
                echo $e->getTraceAsString();
                echo $e->getMessage();
            }

            if ($timeout) {
                usleep($timeout);
            }
        }
    }
}
$bot = new BotController();
$bot->actionRun();