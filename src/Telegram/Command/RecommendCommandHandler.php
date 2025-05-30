<?php

namespace App\Telegram\Command;

use App\Entity\Movie;
use App\Entity\User;
use App\Service\TelegramBotService;
use Doctrine\ORM\EntityManagerInterface;

class RecommendCommandHandler
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

        $moviesWishlist = $this->em->getRepository(Movie::class)->findBy(['status' => 'wishlist', 'user' => $user]);
        if (!$moviesWishlist) {
            $moviesWatched = $this->em->getRepository(Movie::class)->findBy(['status' => 'watched', 'user' => $user]);
            if ($moviesWatched) {
                $randomMovie = $moviesWatched[array_rand($moviesWatched)];
                $message = "🎬 Рекомендуем пересмотреть: '{$randomMovie->getTitle()}'";
                if ($randomMovie->getYear()) {
                    $message .= " ({$randomMovie->getYear()})";
                }
                $this->bot->sendMessage($chatId, $message);
            } else {
                $this->bot->sendMessage($chatId, 'Ваши списки пусты, добавьте фильмы в /wishlist или /add.');
            }

            return;
        }

        $randomMovie = $moviesWishlist[array_rand($moviesWishlist)];
        $message = "🎬 Рекомендуем посмотреть: '{$randomMovie->getTitle()}'";
        if ($randomMovie->getYear()) {
            $message .= " ({$randomMovie->getYear()})";
        }
        $this->bot->sendMessage($chatId, $message);
    }
}
