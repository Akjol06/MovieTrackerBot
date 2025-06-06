<?php

namespace App\Telegram\Command;

use App\Entity\Movie;
use App\Entity\User;
use App\Service\TelegramBotService;
use Doctrine\ORM\EntityManagerInterface;

class AddCommandHandler
{
    public function __construct(
        private TelegramBotService $bot,
        private EntityManagerInterface $em)
    {
    }

    public function handle(string $chatId, string $title, ?int $year = null, ?string $username = null): void
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['telegramId' => $chatId]);
        if (!$user) {
            $user = new User();
            $user->setTelegramId($chatId);
            $user->setUsername($username);
            $this->em->persist($user);
        } elseif ($username && $user->getUsername() !== $username) {
            $user->setUsername($username);
        }

        $movie = new Movie();
        $movie->setTitle($title);
        $movie->setStatus('watched');

        if (null !== $year) {
            $movie->setYear($year);
        }
        $movie->setUser($user);

        $this->em->persist($movie);
        $this->em->flush();

        $this->bot->sendMessage($chatId, "Фильм '$title' добавлен в список просмотренного!");
    }
}
