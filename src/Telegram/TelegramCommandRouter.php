<?php

namespace App\Telegram;

use App\Telegram\Command\AddCommandHandler;
use App\Telegram\Command\ClearListCommandHandler;
use App\Telegram\Command\HelpCommandHandler;
use App\Telegram\Command\ListCommandHandler;
use App\Telegram\Command\RecommendCommandHandler;
use App\Telegram\Command\RemoveCommandHandler;
use App\Telegram\Command\SearchCommandHandler;
use App\Telegram\Command\StartCommandHandler;
use App\Telegram\Command\StatsCommandHandler;
use App\Telegram\Command\WishlistCommandHandler;

class TelegramCommandRouter
{
    public function __construct(
        private StartCommandHandler $start,
        private AddCommandHandler $add,
        private WishlistCommandHandler $wishlist,
        private ListCommandHandler $list,
        private SearchCommandHandler $search,
        private RemoveCommandHandler $remove,
        private RecommendCommandHandler $recommend,
        private StatsCommandHandler $stats,
        private ClearListCommandHandler $clearList,
        private HelpCommandHandler $help,
    ) {
    }

    public function handle(?string $text, int $chatId, ?string $username = null): void
    {
        if (!$text) {
            return;
        }

        if ('/start' === $text) {
            $this->start->handle($chatId, $username);
        } elseif ('/help' === $text) {
            $this->help->handle($chatId);
        } elseif (str_starts_with($text, '/add ')) {
            [$title, $year] = $this->extractTitleAndYear($text, 5);
            if (null !== $title) {
                $this->add->handle($chatId, $title, $year, $username);
            }
        } elseif (str_starts_with($text, '/wishlist ')) {
            [$title, $year] = $this->extractTitleAndYear($text, 9);
            if (null !== $title) {
                $this->wishlist->handle($chatId, $title, $year, $username);
            }
        } elseif ('/list' === $text) {
            $this->list->handle($chatId);
        } elseif (str_starts_with($text, '/search ')) {
            $query = trim(mb_substr($text, 8));
            $this->search->handle($chatId, $query);
        } elseif (str_starts_with($text, '/remove ')) {
            $title = trim(mb_substr($text, 8));
            $this->remove->handle($chatId, $title);
        } elseif ('/recommend' === $text) {
            $this->recommend->handle($chatId);
        } elseif ('/stats' === $text) {
            $this->stats->handle($chatId);
        } elseif ('/clearlist' === $text) {
            $this->clearList->handle($chatId);
        } else {
            return;
        }
    }

    private function extractTitleAndYear(string $text, int $prefixLength): array
    {
        $params = trim(mb_substr($text, $prefixLength));
        if ('' === $params) {
            return [null, null];
        }

        $parts = explode(' ', $params);
        $year = null;

        $lastPart = end($parts);
        if (preg_match('/^\d{4}$/', $lastPart)) {
            $year = (int) array_pop($parts);
        }

        $title = implode(' ', $parts);

        return [$title, $year];
    }
}
