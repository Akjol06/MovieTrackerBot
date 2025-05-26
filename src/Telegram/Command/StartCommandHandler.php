<?php

namespace App\Telegram\Command;

use App\Service\TelegramBotService;

class StartCommandHandler
{
    public function __construct(private TelegramBotService $bot)
    {
    }

    public function handle(int $chatId, ?string $username = null): void
    {
        $this->bot->sendMessage($chatId, 'Привет, '.($username ?? 'пользователь').'! Я помогу тебе отслеживать фильмы и сериалы.');
    }
}
