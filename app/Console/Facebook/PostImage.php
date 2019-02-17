<?php
declare(strict_types=1);

namespace App\Console\Facebook;

use App\Services\Configuration\Interfaces\ConfigHelperInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

class PostImage extends Command
{
    protected static $defaultName = 'facebook:post-photo';

    /**
     * @var ConfigHelperInterface
     */
    private $configHelper;

    public function __construct(ConfigHelperInterface $configHelper)
    {
        $this->configHelper = $configHelper;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Post photo to facebook')
            ->addArgument('file', InputArgument::REQUIRED);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ch = curl_init('https://graph.facebook.com/me/photos');
        $file = new \CURLFile($input->getArgument('file'), 'image/png', 'yeet.png');
        $data = array('file' => $file, 'access_token' => $this->configHelper->get('facebook.access_token'));

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $url = \curl_exec($ch);

        var_dump($url);
    }
}