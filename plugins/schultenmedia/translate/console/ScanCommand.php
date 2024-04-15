<?php namespace SchultenMedia\Translate\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use SchultenMedia\Translate\Classes\ThemeScanner;
use SchultenMedia\Translate\Models\Message;

/**
 * ScanCommand
 */
class ScanCommand extends Command
{
    /**
     * @var string name
     */
    protected $name = 'translate:scan';

    /**
     * @var string description
     */
    protected $description = 'Scan theme localization files for new messages.';

    /**
     * handle
     */
    public function handle()
    {
        if ($this->option('purge')) {
            $this->output->writeln('Purging messages...');
            Message::truncate();
        }

        ThemeScanner::scan();
        $this->output->success('Messages scanned successfully.');
        $this->output->note('You may need to run cache:clear for updated messages to take effect.');
    }

    /**
     * getArguments
     */
    protected function getArguments()
    {
        return [];
    }

    /**
     * getOptions
     */
    protected function getOptions()
    {
        return [
            ['purge', 'null', InputOption::VALUE_NONE, 'First purge existing messages.', null],
        ];
    }
}
