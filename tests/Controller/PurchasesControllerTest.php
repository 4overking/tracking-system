<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\Visit;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use function json_encode;


class PurchasesControllerTest extends WebTestCase
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\KernelBrowser
     */
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * @dataProvider visitData
     */
    public function testSomething(array $expectedResult, array $visits): void
    {
        $this->createVisits($visits);
        $this->client->request('GET', '/api/purchases');
        $content = $this->client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertJson(json_encode($expectedResult), $content);
    }

    public function visitData()
    {
        return [
            $this->ourServiceSuccess(),
            $this->ourServiceFailedClientUsedTheirs1RefferalLink(),
            $this->ourServiceSuccessWithGoogleTransitions(),
            $this->ourServiceSuccessWithDirectVisits(),
            $this->ourServiceFailWithDirectVisitsAndOrganicWinsTheir2Refferal(),
            $this->onlyOrganicVisits(),
            $this->directVisits(),
            $this->ourServiceSuccessManyCheckouts(), //FYI I'm not sure about this step, no additional information provided
            $this->twoUsersFromOurServiceSuccess(),
            $this->twoUsersFromOurServiceWithOneSuccess(),
        ];
    }

    public function ourServiceSuccessWithGoogleTransitions(): array
    {
        return [
            [
                ['client_id' => 'user17', 'partner_link' => 'https://referal.ours.com/?ref=123hexcode'],
            ],
            [
                [
                    'client_id' => 'user17',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/products/?id=2',
                    'referer' => 'https://google.com/search/?q=buy a happiness',
                    'date' => '2018-03-04T08:30:14.104000Z',
                    'type' => Visit::TYPE_ORGANIC,
                ],
                [
                    'client_id' => 'user17',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/pay',
                    'referer' => 'https://shop.com/products/?id=2',
                    'date' => '2018-03-04T08:40:14.104000Z',
                ],
                [
                    'client_id' => 'user17',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/products/?id=2',
                    'referer' => 'https://referal.ours.com/?ref=123hexcode',
                    'date' => '2018-04-04T08:30:14.104000Z',
                    'type' => Visit::TYPE_OURS,
                ],
                [
                    'client_id' => 'user17',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/products/?id=2',
                    'referer' => 'https://google.com/search/?q=buy a happiness,',
                    'date' => '2018-04-04T08:35:14.104000Z',
                    'type' => Visit::TYPE_ORGANIC,
                ],
                [
                    'client_id' => 'user17',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/pay',
                    'referer' => 'https://shop.com/products/?id=2',
                    'date' => '2018-04-04T08:40:14.104000Z',
                ],
                [
                    'client_id' => 'user17',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/checkout',
                    'referer' => 'https://shop.com/pay',
                    'date' => '2018-04-04T08:41:14.104000Z',
                    'is_checkout' => true,
                ],
            ],
        ];
    }

    private function ourServiceSuccess(): array
    {
        return [
            [
                ['client_id' => 'user15', 'partner_link' => 'https://referal.ours.com/?ref=123hexcode'],
            ],
            [
                [
                    'client_id' => 'user15',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/products/?id=2',
                    'referer' => 'https://referal.ours.com/?ref=123hexcode',
                    'date' => '2018-04-04T08:30:14.104000Z',
                    'type' => Visit::TYPE_OURS,
                ],
                [
                    'client_id' => 'user15',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/pay',
                    'referer' => 'https://shop.com/products/?id=2',
                    'date' => '2018-04-04T08:40:14.104000Z',
                ],
                [
                    'client_id' => 'user15',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/checkout',
                    'referer' => 'https://shop.com/pay',
                    'date' => '2018-04-04T08:41:14.104000Z',
                    'is_checkout' => true,
                ],
            ],
        ];
    }

    private function ourServiceFailedClientUsedTheirs1RefferalLink(): array
    {
        return [
            [
                [],
            ],
            [
                [
                    'client_id' => 'user16',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/products/?id=2',
                    'referer' => 'https://referal.ours.com/?ref=123hexcode',
                    'date' => '2018-04-04T08:30:14.104000Z',
                    'type' => Visit::TYPE_OURS,
                ],
                [
                    'client_id' => 'user16',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/pay',
                    'referer' => 'https://shop.com/products/?id=2',
                    'date' => '2018-04-04T08:40:14.104000Z',
                ],
                [
                    'client_id' => 'user16',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/products/?id=2',
                    'referer' => 'https://referal.theirs1.com/?src=123hexcode',
                    'date' => '2018-04-04T08:30:14.104000Z',
                    'type' => Visit::TYPE_FOREIGN,
                ],
                [
                    'client_id' => 'user16',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/pay',
                    'referer' => 'https://shop.com/products/?id=2',
                    'date' => '2018-04-04T08:40:14.104000Z',
                ],
                [
                    'client_id' => 'user16',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/checkout',
                    'referer' => 'https://shop.com/pay',
                    'date' => '2018-04-04T08:41:14.104000Z',
                    'is_checkout' => true,
                ],
            ],
        ];
    }

    private function ourServiceSuccessWithDirectVisits(): array
    {
        return [
            [
                [],
            ],
            [
                [
                    'client_id' => 'user18',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/products/?id=2',
                    'referer' => null,
                    'date' => '2018-03-01T08:30:14.104000Z',
                    'type' => Visit::TYPE_DIRECT,
                ],
                [
                    'client_id' => 'user18',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/products/?id=2',
                    'referer' => 'https://referal.ours.com/?ref=123hexcode',
                    'date' => '2018-04-04T08:30:14.104000Z',
                    'type' => Visit::TYPE_OURS,
                ],
                [
                    'client_id' => 'user18',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/pay',
                    'referer' => 'https://shop.com/products/?id=2',
                    'date' => '2018-04-04T08:40:14.104000Z',
                ],
                [
                    'client_id' => 'user18',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/products/?id=2',
                    'referer' => 'https://referal.ours.com/?ref=123hexcode',
                    'date' => '2018-05-04T08:40:14.104000Z',
                    'type' => Visit::TYPE_DIRECT,
                ],
                [
                    'client_id' => 'user18',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/checkout',
                    'referer' => 'https://shop.com/pay',
                    'date' => '2018-06-04T08:41:14.104000Z',
                    'is_checkout' => true,
                ],
            ],
        ];
    }

    private function ourServiceFailWithDirectVisitsAndOrganicWinsTheir2Refferal(): array
    {
        return [
            [
                [],
            ],
            [
                [
                    'client_id' => 'user19',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/products/?id=2',
                    'referer' => null,
                    'date' => '2018-03-01T08:30:14.104000Z',
                    'type' => Visit::TYPE_DIRECT,
                ],
                [
                    'client_id' => 'user19',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/products/?id=2',
                    'referer' => 'https://referal.ours.com/?ref=123hexcode',
                    'date' => '2018-04-04T08:30:14.104000Z',
                    'type' => Visit::TYPE_OURS,
                ],
                [
                    'client_id' => 'user19',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/pay',
                    'referer' => 'https://shop.com/products/?id=2',
                    'date' => '2018-04-04T08:40:14.104000Z',
                ],
                [
                    'client_id' => 'user19',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/products/?id=2',
                    'referer' => 'https://referal.ours.com/?ref=123hexcode',
                    'date' => '2018-05-04T08:40:14.104000Z',
                    'type' => Visit::TYPE_DIRECT,
                ],
                [
                    'client_id' => 'user19',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/products/?id=2',
                    'referer' => 'https://google.com/search/?q=buy a happiness,',
                    'date' => '2018-06-04T08:35:14.104000Z',
                    'type' => Visit::TYPE_ORGANIC,
                ],

                [
                    'client_id' => 'user19',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/products/?id=2',
                    'referer' => 'https://goods.theis2.com/?some_code=xyz',
                    'date' => '2018-04-04T08:30:14.104000Z',
                    'type' => Visit::TYPE_FOREIGN,
                ],
                [
                    'client_id' => 'user19',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/checkout',
                    'referer' => 'https://shop.com/pay',
                    'date' => '2018-10-04T08:41:14.104000Z',
                    'is_checkout' => true,
                ],
            ],
        ];
    }

    private function onlyOrganicVisits(): array
    {
        return [
            [
                [],
            ],
            [
                [
                    'client_id' => 'user20',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/products/?id=2',
                    'referer' => 'https://duckduckgo.com/?q=buy a happiness',
                    'date' => '2018-04-04T08:30:14.104000Z',
                    'type' => Visit::TYPE_ORGANIC,
                ],
                [
                    'client_id' => 'user20',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/pay',
                    'referer' => 'https://shop.com/products/?id=2',
                    'date' => '2018-04-04T08:40:14.104000Z',
                ],
                [
                    'client_id' => 'user20',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/checkout',
                    'referer' => 'https://shop.com/pay',
                    'date' => '2018-04-04T08:41:14.104000Z',
                    'is_checkout' => true,
                ],
            ],
        ];
    }

    private function directVisits(): array
    {
        return [
            [
                [],
            ],
            [
                [
                    'client_id' => 'user20',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/products/?id=2',
                    'referer' => null,
                    'date' => '2018-04-04T08:30:14.104000Z',
                    'type' => Visit::TYPE_DIRECT,
                ],
                [
                    'client_id' => 'user20',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/pay',
                    'referer' => 'https://shop.com/products/?id=2',
                    'date' => '2018-04-04T08:40:14.104000Z',
                ],
                [
                    'client_id' => 'user20',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/checkout',
                    'referer' => 'https://shop.com/pay',
                    'date' => '2018-04-04T08:41:14.104000Z',
                    'is_checkout' => true,
                ],
            ],
        ];
    }

    private function ourServiceSuccessManyCheckouts(): array
    {
        return [
            [
                ['client_id' => 'user21', 'partner_link' => 'https://referal.ours.com/?ref=123hexcode'],
            ],
            [
                [
                    'client_id' => 'user21',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/products/?id=2',
                    'referer' => 'https://referal.ours.com/?ref=123hexcode',
                    'date' => '2018-04-04T08:30:14.104000Z',
                    'type' => Visit::TYPE_OURS,
                ],
                [
                    'client_id' => 'user21',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/pay',
                    'referer' => 'https://shop.com/products/?id=2',
                    'date' => '2018-04-04T08:40:14.104000Z',
                ],
                [
                    'client_id' => 'user21',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/checkout',
                    'referer' => 'https://shop.com/pay',
                    'date' => '2018-04-04T08:41:14.104000Z',
                    'is_checkout' => true,
                ],
                [
                    'client_id' => 'user21',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/products/?id=2',
                    'referer' => null,
                    'date' => '2012-03-04T08:30:14.104000Z',
                    'type' => Visit::TYPE_DIRECT,
                ],
                [
                    'client_id' => 'user21',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/checkout',
                    'referer' => 'https://shop.com/pay',
                    'date' => '2020-04-04T08:41:14.104000Z', //Pay attention to this date
                    'is_checkout' => true,
                ],
            ],
        ];
    }

    private function twoUsersFromOurServiceSuccess(): array
    {
        return [
            [
                ['client_id' => 'user1', 'partner_link' => 'https://referal.ours.com/?ref=123hexcode'],
                ['client_id' => 'user2', 'partner_link' => 'https://referal.ours.com/?ref=123hexcode'],
            ],
            [
                [
                    'client_id' => 'user1',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/products/?id=2',
                    'referer' => 'https://referal.ours.com/?ref=123hexcode',
                    'date' => '2018-04-04T08:30:14.104000Z',
                    'type' => Visit::TYPE_OURS,
                ],
                [
                    'client_id' => 'user1',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/pay',
                    'referer' => 'https://shop.com/products/?id=2',
                    'date' => '2018-04-04T08:40:14.104000Z',
                ],
                [
                    'client_id' => 'user1',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/checkout',
                    'referer' => 'https://shop.com/pay',
                    'date' => '2018-04-04T08:41:14.104000Z',
                    'is_checkout' => true,
                ],
                [
                    'client_id' => 'user2',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/products/?id=2',
                    'referer' => 'https://shop.com/products/?id=100500',
                    'date' => '2018-04-04T08:30:14.104000Z',
                    'type' => Visit::TYPE_NONE,
                ],
                [
                    'client_id' => 'user2',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/pay',
                    'referer' => 'https://shop.com/products/?id=2',
                    'date' => '2018-04-04T08:40:14.104000Z',
                ],
                [
                    'client_id' => 'user2',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/checkout',
                    'referer' => 'https://shop.com/pay',
                    'date' => '2018-04-04T08:41:14.104000Z',
                    'is_checkout' => true,
                ],
            ],
        ];
    }

    private function twoUsersFromOurServiceWithOneSuccess(): array
    {
        return [
            [
                ['client_id' => 'user2', 'partner_link' => 'https://referal.ours.com/?ref=123hexcode'],
            ],
            [
                [
                    'client_id' => 'user1',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/products/?id=2',
                    'referer' => 'https://referal.ours.com/?ref=123hexcode',
                    'date' => '2018-04-04T08:30:14.104000Z',
                    'type' => Visit::TYPE_OURS,
                ],
                [
                    'client_id' => 'user1',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/pay',
                    'referer' => 'https://shop.com/products/?id=2',
                    'date' => '2018-04-04T08:40:14.104000Z',
                ],
                [
                    'client_id' => 'user2',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/products/?id=2',
                    'referer' => 'https://shop.com/products/?id=100500',
                    'date' => '2018-04-04T08:30:14.104000Z',
                    'type' => Visit::TYPE_NONE,
                ],
                [
                    'client_id' => 'user2',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/pay',
                    'referer' => 'https://shop.com/products/?id=2',
                    'date' => '2018-04-04T08:40:14.104000Z',
                ],
                [
                    'client_id' => 'user2',
                    'user_agent' => 'Firefox 59',
                    'location' => 'https://shop.com/checkout',
                    'referer' => 'https://shop.com/pay',
                    'date' => '2018-04-04T08:41:14.104000Z',
                    'is_checkout' => true,
                ],
            ],
        ];
    }

    private function createVisits(array $visitsData): void
    {
        $entityManager = $this->client->getContainer()->get('doctrine.orm.default_entity_manager');

        foreach ($visitsData as $datum) {
            $visit = new Visit();
            $visit
                ->setClientId($datum['client_id'])
                ->setUserAgent($datum['user_agent'])
                ->setLocation($datum['location'])
                ->setReferer($datum['referer'])
                ->setDate(new DateTime($datum['date']))
                ->setIsCheckout($datum['is_checkout'] ?? false)
                ->setType($datum['type'] ?? Visit::TYPE_NONE)
            ;
            $entityManager->persist($visit);
        }

        $entityManager->flush();
    }
}
