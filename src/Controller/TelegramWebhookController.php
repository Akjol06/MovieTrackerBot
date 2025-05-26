<?php

namespace App\Controller;

use App\Telegram\TelegramCommandRouter;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/webhook', name: 'telegram_webhook', methods: ['POST'])]
class TelegramWebhookController extends AbstractController
{
    public function __invoke(Request $request, TelegramCommandRouter $router, LoggerInterface $logger): Response 
    {
        $data = json_decode($request->getContent(), true);
        $message = $data['message'] ?? null;

        if (!$message) return new Response();

        $chatId = $message['chat']['id'] ?? null;
        $username = $message['from']['username'] ?? null;
        $text = $message['text'] ?? null;

        $logger->info('📨 Parsed Telegram message', [
            'chat_id' => $chatId,
            'username' => $username,
            'text' => $text,
        ]);

        $router->handle($text, $chatId, $username);

        return new Response();
    }
}
