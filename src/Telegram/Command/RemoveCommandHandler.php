<?php

namespace App\Telegram\Command;

use App\Entity\Movie;
use App\Entity\User;
use App\Service\TelegramBotService;
use Doctrine\ORM\EntityManagerInterface;

class RemoveCommandHandler
{
    public function __construct(
        private TelegramBotService $bot,
        private EntityManagerInterface $em,
    ) {
    }

    public function handle(int $chatId, string $title): void
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['telegramId' => $chatId]);
        if (!$user) {
            $this->bot->sendMessage($chatId, 'Пользователь не найден.');

            return;
        }

        $movie = $this->em->getRepository(Movie::class)->findOneBy(['title' => $title, 'user' => $user]);
        if (!$movie) {
            $this->bot->sendMessage($chatId, "Фильм '$title' не найден в вашем списке.");

            return;
        }

        $this->em->remove($movie);
        $this->em->flush();

        $this->bot->sendMessage($chatId, "Фильм '$title' удалён из вашего списка.");
    }
}
