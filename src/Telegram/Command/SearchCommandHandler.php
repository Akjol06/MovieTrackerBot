<?php

namespace App\Telegram\Command;

use App\Service\TelegramBotService;
use App\Service\TmdbService;

class SearchCommandHandler
{
    public function __construct(
        private TelegramBotService $bot,
        private TmdbService $tmdb
    ) {}

    public function handle(int $chatId, string $query): void
    {
        if ($query === '') {
            $this->bot->sendMessage($chatId, 'Пожалуйста, введите запрос после команды /search.');
            return;
        }

        $results = $this->tmdb->search($query);

        if (empty($results['results'])) {
            $this->bot->sendMessage($chatId, "Не удалось найти фильм или сериал по запросу: {$query}");
            return;
        }

        $first = $results['results'][0];
        $title = $first['title'] ?? $first['name'] ?? 'Без названия';
        $overview = $first['overview'] ?? 'Описание отсутствует.';
        $releaseDate = $first['release_date'] ?? $first['first_air_date'] ?? '????';
        $posterPath = $first['poster_path'] ?? null;
        $year = substr($releaseDate, 0, 4);
        $voteAverage = $first['vote_average'] ?? 0;
        $voteAverageText = $voteAverage > 0 ? number_format($voteAverage, 1) : '—';
        $originalLanguage = strtoupper($first['original_language'] ?? '—');

        if (mb_strlen($overview) > 500) {
            $overview = mb_substr($overview, 0, 500) . '...';
        }

        $genres = $first['genre_ids'] ?? [];
        $genresMap = $this->tmdb->getGenresMap();

        $genreNames = array_map(fn($id) => $genresMap[$id] ?? '-', $genres);
        $genresString = implode(', ', array_filter($genreNames));

        $messageText = "🎬 *{$title}*\n"
            . "📅 Год: {$year}\n"
            . "🎭 Жанры: {$genresString}\n"
            . "⭐ Рейтинг: {$voteAverageText}\n"
            . "🗣 Язык: {$originalLanguage}\n"
            . "📝 Описание:\n{$overview}";

        if ($posterPath) {
            $photoUrl = "https://image.tmdb.org/t/p/w500{$posterPath}";;
            $this->bot->sendPhoto($chatId, $photoUrl, $messageText);
        } else {
            $this->bot->sendMessage($chatId, $messageText);
        }
    }
}