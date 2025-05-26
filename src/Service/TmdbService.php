<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class TmdbService
{
    private string $apiKey;
    private array $genresMap = [];

    public function __construct(private HttpClientInterface $client)
    {
        $this->apiKey = $_ENV['TMDB_API_KEY'] ?? '';
    }

    public function search(string $query): array
    {
        $response = $this->client->request('GET', 'https://api.themoviedb.org/3/search/multi', [
            'query' => [
                'api_key' => $this->apiKey,
                'language' => 'ru-RU',
                'query' => $query,
                'include_adult' => 'false',
            ],
        ]);

        return $response->toArray();
    }

        public function getGenresMap(): array
    {
        if (empty($this->genresMap)) {
            $response = $this->client->request('GET', 'https://api.themoviedb.org/3/genre/movie/list', [
                'query' => [
                    'api_key' => $this->apiKey,
                    'language' => 'ru-RU',
                ],
            ]);

            $data = $response->toArray();

            foreach ($data['genres'] as $genre) {
                $this->genresMap[$genre['id']] = $genre['name'];
            }
        }

        return $this->genresMap;
    }
}