<?php

namespace Commands;


use Longman\TelegramBot\Commands\AdminCommand;
use Longman\TelegramBot\DB;
use Longman\TelegramBot\Entities\Chat;
use Longman\TelegramBot\Request;

class ChangeGroupCommand extends AdminCommand
{
    protected $need_mysql = true;

    /**
     * Execute command
     *
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $chats = DB::selectChats([
            'groups' => true,
            'supergroups' => false,
            'channels' => false,
            'users' => false,
        ]);

        $chats = array_filter($chats, function ($chat) {
            return $chat['all_members_are_administrators'] == 0;
        });

        $date = date('d-m-Y');
        foreach ($chats as $chat) {
            $chat = (Object)$chat;
            Request::setChatTitle([
                'chat_id' => $chat->id,
                'title' => $date,
            ]);
        }

        return true;
    }
}