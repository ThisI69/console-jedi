<?php
/**
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Notamedia\ConsoleJedi\Cache\Command;

use Bitrix\Main\Application;
use Bitrix\Main\Web\HttpClient;
use Bitrix\Main\XML;
use Notamedia\ConsoleJedi\Application\Command\BitrixCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for clear Bitrix cache.
 *
 * @author Andrey Lapshin <thisi69@yandex.ru>
 */
class WarmupCommand extends BitrixCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('cache:warmup')
            ->setDescription('Warmup pages sitemap')
            ->addArgument('url', InputOption::VALUE_REQUIRED, 'URL sitemap');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Start warmup pages sitemap',
            '==============',
            '',
        ]);

        $content = $this->getContent($input->getArgument('url'));

        $xmlUrls = $this->getUrls($content, '/sitemapindex');

        $siteUrls = [];

        foreach ($xmlUrls as $url) {
            $content = $this->getContent($url);
            $siteUrls = array_merge($siteUrls, $this->getUrls($content, '/urlset'));
        }

        $this->crawlingPages($siteUrls);

        $output->writeln([
            '==============',
            'End warmup ' . count($siteUrls) . ' pages sitemap',
            '',
        ]);

        return 0;
    }

    protected function getContent($url): string
    {
        $httpClient = new HttpClient();
        $response = $httpClient->post($url);

        return $response;
    }

    protected function getUrls($content, $selectNodes): array
    {
        $xml = new \CDataXML();
        $xml->LoadString($content);
        if ($node = $xml->SelectNodes($selectNodes)) {
            foreach ($node->children() as $arSitemap) {
                $urls[] = $arSitemap->elementsByName("loc")[0]->content;
            }
        }

        return $urls;
    }

    protected function crawlingPages($urls): void
    {
        $httpClient = new HttpClient();
        foreach ($urls as $url) {
            $httpClient->post($url);
        }
    }
}
