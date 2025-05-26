<?php

namespace App\Telegram\Command;

use App\Entity\User;
use App\Entity\Movie;
use App\Service\TelegramBotService;
use Doctrine\ORM\EntityManagerInterface;

class ListCommandHandler
{
    public function __construct(
        private TelegramBotService $bot,
        private EntityManagerInterface $em
    ) {}

    public function handle(int $chatId): void
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['telegramId' => $chatId]);

        if (!$user) {
            $this->bot->sendMessage($chatId, 'Вы ещё не добавили ни одного фильма.');
            return;
        }

        $movies = $this->em->getRepository(Movie::class)->findBy(['user' => $user]);

        if (!$movies) {
            $this->bot->sendMessage($chatId, 'Ваш список пуст.');
            return;
        }

        $responseText = "🎬 *Ваши фильмы:*\n\n";
        foreach ($movies as $movie) {
            $statusEmoji = match ($movie->getStatus()) {
                'watched' => '✅',
                'in_progress' => '📺',
                'wishlist' => '📝',
                default => '❓',
            };
            $responseText .= "{$statusEmoji} {$movie->getTitle()}\n";
        }

        $this->bot->sendMessage($chatId, $responseText);
    }
}
