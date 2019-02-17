<?php
declare(strict_types=1);

namespace App\Console\ChartLyrics;

use App\Services\Configuration\Interfaces\ConfigHelperInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetRandomLyrics extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'chartlyrics:random ';

    /**
     * @var ConfigHelperInterface
     */
    private $configHelper;

    /**
     * GetRandomLyrics constructor.
     *
     * @param ConfigHelperInterface $configHelper
     */
    public function __construct(ConfigHelperInterface $configHelper)
    {
        $this->configHelper = $configHelper;

        parent::__construct();
    }

    /**
     * See below for code I am not proud of
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     *
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $possibleSongs = [];

        while (count($possibleSongs) === 0) {

            $combinationWords = \iterator_to_array($this->getRandomWords(2));

            $new = new \SimpleXMLElement(
                $this->getUrlContents(
                    \sprintf('http://api.chartlyrics.com/apiv1.asmx/SearchLyricText?lyricText=%s',
                        \urlencode(\implode(' ', $combinationWords))
                    )
                )
            );

            foreach ($new as $item) {
                $possibleSongs[] = ['id' => (string)$item->LyricId, 'hash' => (string)$item->LyricChecksum];
            }

            \array_pop($possibleSongs);
        }

        while (1) {

            $chosenSong = $possibleSongs[\random_int(0, \count($possibleSongs) - 1)];

            if (empty($chosenSong['id'] ?? null) === false) {
                break;
            }

            sleep(1);
        }

        $randomSongLyrics = new \SimpleXMLElement($this->getUrlContents(
            \sprintf(
                'http://api.chartlyrics.com/apiv1.asmx/GetLyric?lyricId=%d&lyricCheckSum=%s',
                $chosenSong['id'],
                $chosenSong['hash']
            )
        ));

        // Filter empty lines, and lines containing "chorus"
        $linesOfLyrics = \array_map(
            'html_entity_decode',
            \array_filter(\array_filter(
                \explode("\n", (string)$randomSongLyrics->Lyric[0]),
                static function ($value) {
                    return \stristr($value, 'chorus') === false && \stristr($value, '[') === false && \stristr($value, '{') === false;
                }
            ))
        );

        $linesOfLyrics = \array_values($linesOfLyrics);

        for ($i = 0; $i < 2; $i++) {
            $output->writeln($linesOfLyrics[\random_int(1, count($linesOfLyrics) - 1)]);
        }
    }

    /**
     * @param int|null $numberOfWords
     *
     * @return iterable
     */
    private function getRandomWords(int $numberOfWords = null): iterable
    {
        $words = $this->configHelper->get('lyrics.keywords');

        \shuffle($words);

        for ($i = 0; $i < $numberOfWords; $i++) {
            yield $words[$i];
        }
    }

    /**
     * Retrieve a URL contents and decode to JSON
     *
     * @param string $url
     *
     * @return string
     *
     * @throws \Exception
     */
    private function getUrlContents(string $url): string
    {
        $contents = \file_get_contents($url);

        if ($contents === false) {
            throw new \Exception('Unable to download URL');
        }

        return $contents;
    }
}
