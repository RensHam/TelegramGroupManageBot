#!/usr/bin/env php
<?php
// Your command(s) to run, pass it just like in a message (arguments supported)
use Commands\ChangeGroupCommand;
use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\TelegramLog;

require_once __DIR__ . '/vendor/autoload.php';
require_once  'Config.php';
$bot_api_key = Config::API_KEY;
$bot_username = Config::USERNAME;

try {
    $telegram = new Telegram($bot_api_key, $bot_username);

    // Add commands paths containing your custom commands
    $telegram->addCommandsPaths(Config::COMMAND_PATH);

    $telegram->enableAdmins(Config::ADMIN_USERS);

    // Requests Limiter (tries to prevent reaching Telegram API limits)
    $telegram->enableLimiter();

    // Enable MySQL
    $telegram->enableMySql(Config::MYSQL);

    $t = new ChangeGroupCommand($telegram);
    $t->execute();

    // Handle telegram getUpdates request
    $serverResponse = $telegram->handleGetUpdates();

    if ($serverResponse->isOk()) {
        $update_count = count($serverResponse->getResult());
        echo date('Y-m-d H:i:s', time()) . ' - Processed ' . $update_count . ' updates';
    } else {
        echo date('Y-m-d H:i:s', time()) . ' - Failed to fetch updates' . PHP_EOL;
        echo $serverResponse->printError();
    }

} catch (TelegramException $e) {
    TelegramLog::error($e);
}