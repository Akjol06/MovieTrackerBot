<?php

namespace App\Telegram\Command;

use App\Entity\Movie;
use App\Entity\User;
use App\Service\TelegramBotService;
use Doctrine\ORM\EntityManagerInterface;

class StatsCommandHandler
{
    public function __construct(
        private TelegramBotService $bot,
        private EntityManagerInterface $em,
    ) {
    }

    public function handle(string $chatId): void
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['telegramId' => $chatId]);
        if (!$user) {
            $this->bot->sendMessage($chatId, 'Пользователь не найден.');

            return;
        }

        $watchedCount = $this->em->getRepository(Movie::class)->count(['status' => 'watched', 'user' => $user]);
        $wishlistCount = $this->em->getRepository(Movie::class)->count(['status' => 'wishlist', 'user' => $user]);

        $message = "📊 *Статистика пользователя:*\n"
            ."👀 Просмотрено фильмов: {$watchedCount}\n"
            ."⭐ В списке желаемого: {$wishlistCount}";

        $this->bot->sendMessage($chatId, $message);
    }
}
