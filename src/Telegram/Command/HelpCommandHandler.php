<?php

namespace App\Telegram\Command;

use App\Service\TelegramBotService;

class HelpCommandHandler
{
    public function __construct(private TelegramBotService $bot)
    {
    }

    public function handle(int $chatId): void
    {
        $message = "👋 *Добро пожаловать в Movie Tracker Bot!*\n\n"
            ."Вот список доступных команд:\n\n"
            ."/start - Приветственное сообщение, описание бота\n"
            ."/add [название] — Добавить фильм в просмотренные\n"
            ."/wishlist [название] [год] - Добавить фильм в список желаемого (опционально укажите год)\n"
            ."/list - Показать список просмотренных фильмов\n"
            ."/search [название] - Найти фильм по название\n"
            ."/remove [название] - Удалить фильм из списка просмотренных и список желаемого\n"
            ."/recommend - Предложить случайный фильм из вашей базы\n"
            ."/stats - Показать статистику пользователя\n"
            ."/clearlist - Очистить список просмотренных фильмов\n\n";
        $message .= "Если у вас есть вопросы или предложения, не стесняйтесь обращаться к разработчику @kanaev006!\n\n"
            .'Спасибо, что используете Movie Tracker Bot! 🎬';

        $this->bot->sendMessage($chatId, $message, ['parse_mode' => 'Markdown']);
    }
}
