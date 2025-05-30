<?php

namespace App\Service;

use GuzzleHttp\ClientInterface;

class TelegramBotService
{
    private string $token;

    public function __construct(private ClientInterface $client)
    {
        $this->token = $_ENV['TELEGRAM_BOT_TOKEN'] ?? '';
        $this->client = $client;
    }

    public function sendMessage(string $chatId, string $text): void
    {
        $this->client->request('GET', "https://api.telegram.org/bot{$this->token}/sendMessage", [
            'query' => [
                'chat_id' => $chatId,
                'text' => $text,
            ],
        ]);
    }

    public function sendPhoto(string $chatId, string $photoUrl, string $caption = ''): void
    {
        $this->client->request('GET', "https://api.telegram.org/bot{$this->token}/sendPhoto", [
            'query' => [
                'chat_id' => $chatId,
                'photo' => $photoUrl,
                'caption' => $caption,
                'parse_mode' => 'Markdown',
            ],
        ]);
    }

    public function setBotCommands(): void
    {
        $commands = [
            ['command' => 'start', 'description' => 'Приветственное сообщение, описание бота'],
            ['command' => 'help', 'description' => 'Показать список команд'],
            ['command' => 'add', 'description' => 'Добавить фильм в просмотренные'],
            ['command' => 'wishlist', 'description' => 'Добавить фильм в список "хочу посмотреть"'],
            ['command' => 'list', 'description' => 'Показать список просмотренных фильмов'],
            ['command' => 'search', 'description' => 'Поиск фильма'],
            ['command' => 'remove', 'description' => 'Удалить фильм из списков'],
            ['command' => 'recommend', 'description' => 'Получить рекомендацию фильма'],
            ['command' => 'stats', 'description' => 'Показать статистику пользователя'],
            ['command' => 'clearlist', 'description' => 'Очистить список просмотренных фильмов'],
        ];

        $this->sendRequest('setMyCommands', ['commands' => $commands]);
    }

    private function sendRequest(string $method, array $params): array
    {
        $url = "https://api.telegram.org/bot{$this->token}/{$method}";
        $options = [
            'http' => [
                'header' => "Content-Type: application/json\r\n",
                'method' => 'POST',
                'content' => json_encode($params),
            ],
        ];
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        return json_decode($result, true);
    }
}
