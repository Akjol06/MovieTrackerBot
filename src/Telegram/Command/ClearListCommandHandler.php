<?php

namespace App\Telegram\Command;

use App\Entity\Movie;
use App\Entity\User;
use App\Service\TelegramBotService;
use Doctrine\ORM\EntityManagerInterface;

class ClearListCommandHandler
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

        $movies = $this->em->getRepository(Movie::class)->findBy(['user' => $user]);
        if (empty($movies)) {
            $this->bot->sendMessage($chatId, 'Ваш список уже пуст.');

            return;
        }

        foreach ($movies as $movie) {
            $this->em->remove($movie);
        }
        $this->em->flush();

        $this->bot->sendMessage($chatId, 'Ваш список фильмов успешно очищен.');
    }
}
