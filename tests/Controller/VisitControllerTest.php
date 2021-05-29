<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\Visit;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

use function json_encode;

class VisitControllerTest extends WebTestCase
{
    /**
     * @dataProvider requestData
     */
    public function testVisitCreate(
        int $expectedResponseCode,
        array $requestData,
        ?bool $isCheckout,
        ?string $expectedType
    ): void {
        $json = json_encode($requestData);
        $client = static::createClient();
        $client->request('POST', '/api/visit', [], [], ['CONTENT_TYPE' => 'application/json'], $json);

        $this->assertSame($expectedResponseCode, $client->getResponse()->getStatusCode());

        if (null !== $expectedType) {
            $entityManager = $client->getContainer()->get('doctrine.orm.default_entity_manager');
            $visitRepository = $entityManager->getRepository(Visit::class);
            /** @var Visit $visit */
            $visit = $visitRepository->findOneBy([
                'clientId' => $requestData['client_id'],
            ]);
            $this->assertNotNull($requestData);
            $this->assertSame($expectedType, $visit->getType());
            $this->assertSame($isCheckout, $visit->getIsCheckout());
        }
    }

    /**
     * All data from the task plus forbidden request.
     */
    public function requestData(): array
    {
        return [
            [
                'expectedResponseCode' => Response::HTTP_CREATED,
                'requestData' => [
                    'client_id' => 'user15',
                    'User-Agent' => 'Firefox 59',
                    'document.location' => 'https://shop.com/products/?id=2',
                    'document.referer' => 'https://yandex.ru/search/?q=купить+котика,',
                    'date' => '2018-04-03T07:59:13.286000Z',
                ],
                'isCheckout' => false,
                'expectedType' => Visit::TYPE_ORGANIC,
            ],
            [
                'expectedResponseCode' => Response::HTTP_CREATED,
                'requestData' => [
                    'client_id' => 'user15',
                    'User-Agent' => 'Firefox 59',
                    'document.location' => 'https://shop.com/products/?id=2',
                    'document.referer' => 'https://referal.ours.com/?ref=123hexcode',
                    'date' => '2018-04-04T08:30:14.104000Z',
                ],
                'isCheckout' => false,
                'expectedType' => Visit::TYPE_OURS,
            ],
            [
                'expectedResponseCode' => Response::HTTP_CREATED,
                'requestData' => [
                    'client_id' => 'user15',
                    'User-Agent' => 'Firefox 59',
                    'document.location' => 'https://shop.com/products/?id=2',
                    'document.referer' => 'https://ad.theirs1.com/?src=q1w2e3r4',
                    'date' => '2018-04-04T08:45:14.384000Z',
                ],
                'isCheckout' => false,
                'expectedType' => Visit::TYPE_FOREIGN,
            ],
            [
                'expectedResponseCode' => Response::HTTP_CREATED,
                'requestData' => [
                    'client_id' => 'user15',
                    'User-Agent' => 'Firefox 59',
                    'document.location' => 'https://shop.com/checkout',
                    'document.referer' => 'https://shop.com/products/?id=2',
                    'date' => '2018-04-04T08:45:14.384000Z',
                ],
                'isCheckout' => true,
                'expectedType' => Visit::TYPE_NONE,
            ],
            [
                'expectedResponseCode' => Response::HTTP_BAD_REQUEST,
                'requestData' => [
                    'User-Agent' => 'Firefox 59',
                    'document.location' => 'https://shop.com/checkout',
                    'document.referer' => 'https://shop.com/products/?id=2',
                    'date' => '2018-04-04T08:45:14.384000Z',
                ],
                'isCheckout' => null,
                'expectedType' => null,
            ],
            [
                'expectedResponseCode' => Response::HTTP_BAD_REQUEST,
                'requestData' => [
                    'client_id' => 'user15',
                    'User-Agent' => 'Firefox 59',
                    'document.location' => 'https://shop.com/checkout',
                    'document.referer' => 'https://shop.com/products/?id=2',
                    'date' => 'wrong date format',
                ],
                'isCheckout' => null,
                'expectedType' => null,
            ],
        ];
    }
}
