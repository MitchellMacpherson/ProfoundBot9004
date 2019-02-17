<?php
declare(strict_types=1);

namespace App\Console\Reddit;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RandomEarthPorn extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'reddit:random';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setDescription('Select a random r/EarthPorn image');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $rss = \file_get_contents('https://www.reddit.com/r/earthporn/.rss?limit=80');
        // https://www.reddit.com/r/EarthPorn/search.rss?restrict_sr=on&sort=new&q=hawaii+beach

        preg_match_all('/<content type="html">([^<]+)<\/content>/', $rss, $contents);

        $images =[];

        foreach ($contents[1] as $imageUrl) {
            \preg_match(
                '/"([^"]+)">\[link]/',
                \htmlspecialchars_decode($imageUrl),
                $imageUrl
            );

            $images[] = $imageUrl[1];
        }

        $output->writeln($images[\array_rand($images, 1)]);
    }
}

// &quot;https://i.redd.it/zipmtxjsdbu21.jpg&quot;&gt;[link]
