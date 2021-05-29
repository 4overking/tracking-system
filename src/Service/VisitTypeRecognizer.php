<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Visit;

use function parse_url;
use function sprintf;

class VisitTypeRecognizer
{
    public const CHECKOUT_URL = 'https://shop.com/checkout';

    public function recognize(Visit $visit): void
    {
        $visit->setType(
            $this->getType($visit->getReferer())
        );

        $visit->setIsCheckout(
            $this->isCheckoutPage($visit)
        );
    }

    private function getType(?string $referer): string
    {
        if (empty($referer)) {
            return Visit::TYPE_DIRECT;
        }
        ['host' => $host, 'query' => $query] = parse_url($referer);

        if (\in_array($host, $this->getOurCashBackServices(), true) || \in_array($query, $this->getOurPartnerLinks(), true)) {
            return Visit::TYPE_OURS;
        }

        if (\in_array($host, $this->getSearchEngines(), true)) {
            return Visit::TYPE_ORGANIC;
        }

        if (\in_array($host, $this->getForeignCashBackServices(), true)) {
            return Visit::TYPE_FOREIGN;
        }

        return Visit::TYPE_NONE;
    }

    /**
     * @TODO list is not full and should be taken from editable storage of links
     */
    private function getOurPartnerLinks(): array
    {
        return [
            'ref=123hexcode',
        ];
    }

    /**
     * @TODO list is not full and should be taken from editable storage of cashback services
     */
    private function getOurCashBackServices(): array
    {
        return [
            'referal.ours.com',
        ];
    }

    /**
     * @TODO list is not full and should be taken from editable storage of search engines
     */
    private function getSearchEngines(): array
    {
        return [
            'bing.com',
            'ya.ru',
            'yandex.ru',
            'duckduckgo.com',
            'www.google.com',
            'google.com',
        ];
    }

    private function getForeignCashBackServices(): array
    {
        return [
            'ad.theirs1.com',
        ];
    }

    /**
     * FOR YOU INFORMATION: This is very basic design, it should be improved.
     */
    private function isCheckoutPage(Visit $visit): bool
    {
        ['scheme' => $scheme, 'host' => $host, 'path' => $path] = parse_url($visit->getLocation());
        $url = sprintf('%s://%s%s', $scheme, $host, $path);

        return self::CHECKOUT_URL === $url;
    }
}
