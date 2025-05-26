<?php

namespace App\Command;

use App\Service\TelegramBotService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TelegramSetCommand extends Command
{
    private TelegramBotService $bot;

    public function __construct(TelegramBotService $bot)
    {
        parent::__construct();
        $this->bot = $bot;
    }

    protected function configure(): void
    {
        $this
            ->setName('telegram:set-commands')
            ->setDescription('Регистрирует команды Telegram бота через Telegram API');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->bot->setBotCommands();
        $output->writeln('Команды Telegram бота успешно зарегистрированы.');

        return Command::SUCCESS;
    }
}