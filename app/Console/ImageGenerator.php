<?php
declare(strict_types=1);

namespace App\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImageGenerator extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'image:generate ';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setDescription('Select a random r/EarthPorn image')
            ->addArgument('url', InputArgument::REQUIRED)
            ->addArgument('lyrics', InputArgument::REQUIRED);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     *
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        var_dump($input->getArgument('url'));
        $image = \imagecreatefromjpeg($input->getArgument('url'));
        $lyrics = \explode("\n", $input->getArgument('lyrics'));

        // Create some colors
        $white = \imagecolorallocate($image, 255, 255, 255);
        $grey = \imagecolorallocate($image, 128, 128, 128);
        $black = \imagecolorallocate($image, 0, 0, 0);

        // Replace path by your own font path
        $font = './ShadowsIntoLight.ttf';

        $fontSize = $this->resolveMaxTextWidth($lyrics[0] . "\n" . $lyrics[1], \imagesx($image) - 140);

        var_dump($fontSize);

        // Add some shadow to the text
        \imagettftext($image, $fontSize, 0, 141, 141, $black, $font, $lyrics[0] . "\n" . $lyrics[1]);
        \imagettftext($image, $fontSize, 0, 139, 139, $black, $font, $lyrics[0] . "\n" . $lyrics[1]);


        // Add the text
        \imagettftext(
            $image,
            $fontSize,
            0,
            140,
            140,
            $white,
            $font,
            $lyrics[0] . "\n" . $lyrics[1]
        );

        var_dump($lyrics);

        // Using imagepng() results in clearer text compared with imagejpeg()
        // @todo make directory configurable, or sys_get_temp_dir()
         \imagejpeg($image, '/tmp/yolo.jpg', 80);
        \imagedestroy($image);
    }

    /**
     * @param string $text
     * @param int $imageWidth
     *
     * @return int
     *
     * @throws \Exception
     */
    private function resolveMaxTextWidth(string $text, int $imageWidth): int
    {
        $size = 100;

        while (true) {
            $box = \imagettfbbox($size, 0, './ShadowsIntoLight.ttf', $text);

            if ($box[2] < $imageWidth) {
                return $size;
            }

            $size--;
        }

        throw new \Exception();
    }
}
