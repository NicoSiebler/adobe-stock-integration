<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AdobeStockImage\Test\Unit\Model;

use Magento\Framework\Exception\IntegrationException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\AdobeStockImageApi\Api\GetImageListInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Psr\Log\LoggerInterface;
use Magento\AdobeStockImage\Model\GetRelatedImages;
use Magento\Framework\Api\AttributeValue;

/**
 * Test for GetRelatedImages Model
 */
class GetRelatedImagesTest extends TestCase
{
    /**
     * @var MockObject|GetImageListInterface
     */
    private $getImageListInterface;

    /**
     * @var MockObject|SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var MockObject|FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var LoggerInterface|MockObject
     */
    private $logger;

    /**
     * @var GetRelatedImages
     */
    private $getRelatedSeries;

    /**
     * @var string[]
     */
    private $fields;

    /**
     * @inheritDoc
     */
    public function setUp()
    {
        $this->filterBuilder = $this->createMock(FilterBuilder::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->searchCriteriaBuilder = $this->createMock(SearchCriteriaBuilder::class);
        $this->getImageListInterface = $this->createMock(GetImageListInterface::class);
        $this->fields = ['same_series' => 'serie_id', 'same_model' => 'model_id'];
        $this->getRelatedSeries = new GetRelatedImages(
            $this->getImageListInterface,
            $this->searchCriteriaBuilder,
            $this->filterBuilder,
            $this->logger,
            $this->fields
        );
    }

    /**
     * Check if related images can be executed.
     *
     * @param $relatedImagesProvider
     * @param $expectedResult
     * @throws IntegrationException
     * @dataProvider relatedImagesDataProvider
     */
    public function testExecute($relatedImagesProvider, $expectedResult)
    {
        $this->filterBuilder->expects($this->any())
            ->method('setField')
            ->willReturnSelf();
        $this->filterBuilder->expects($this->any())
            ->method('setValue')
            ->willReturnSelf();
        $this->filterBuilder->expects($this->any())
            ->method('create')
            ->willReturn(
                $this->createMock(\Magento\Framework\Api\Filter::class)
            );
        $this->searchCriteriaBuilder->expects($this->any())
            ->method('addFilter')
            ->willReturnSelf();
        $this->searchCriteriaBuilder->expects($this->any())
            ->method('setPageSize')
            ->willReturnSelf();
        $this->searchCriteriaBuilder->expects($this->any())
            ->method('create')
            ->willReturn(
                $this->createMock(\Magento\Framework\Api\Search\SearchCriteria::class)
            );
        $searchCriteriaMock = $this->createMock(\Magento\Framework\Api\Search\SearchResultInterface::class);
        $this->getImageListInterface->expects($this->any())
            ->method('execute')
            ->willReturn($searchCriteriaMock);
        $searchCriteriaMock->expects($this->any())
            ->method('getItems')
            ->willReturn($relatedImagesProvider);

        $this->assertEquals($expectedResult, $this->getRelatedSeries->execute(12345678, 30));
    }

    /**
     * Series Data provider.
     *
     * @return array
     */
    public function relatedImagesDataProvider(): array
    {
        return [
            [
                'relatedImagesProvider' => [
                    new \Magento\Framework\Api\Search\Document(
                        [
                            'id' => 2,
                            'custom_attributes' => [
                                'title' => new AttributeValue(
                                    [
                                        'attribute_code' => 'title',
                                        'value' => 'Some Title'
                                    ]
                                ),
                                'thumbnail_240_url' => new AttributeValue(
                                    [
                                        'attribute_code' => 'thumbnail_240_url',
                                        'value' => 'https://t4.ftcdn.net/z6rPCvS5umPhRUNPa62iA2YYVG49yo2n.jpg'
                                    ]
                                ),
                                'thumbnail_500_url' => new AttributeValue(
                                    [
                                        'attribute_code' => 'thumbnail_500_url',
                                        'value' => 'https://t4.ftcdn.net/z6rPCvS5umPhRUNPa62iA2YYVG49yo2n.jpg'
                                    ]
                                ),
                                'id' => new AttributeValue(
                                    [
                                        'attribute_code' => 'id',
                                        'value' => 2
                                    ]
                                ),
                                'creator_name' => new AttributeValue(
                                    [
                                        'attribute_code' => 'creator_name',
                                        'value' => 'Author'
                                    ]
                                ),
                                'content_type' => new AttributeValue(
                                    [
                                        'attribute_code' => 'content_type',
                                        'value' => 'image/jpeg'
                                    ]
                                ),
                                'width' => new AttributeValue(
                                    [
                                        'attribute_code' => 'width',
                                        'value' => 1000
                                    ]
                                ),
                                'height' => new AttributeValue(
                                    [
                                        'attribute_code' => 'height',
                                        'value' => 1000
                                    ]
                                ),
                                'category' => new AttributeValue(
                                    [
                                        'attribute_code' => 'category',
                                        'value' => 123
                                    ]
                                ),
                                'keywords' => new AttributeValue(
                                    [
                                        'attribute_code' => 'keywords',
                                        'value' => [
                                            [
                                                'name' => 'keyword #1'
                                            ],
                                            [
                                                'name' => 'keyword #2'
                                            ],
                                            [
                                                'name' => 'keyword #3'
                                            ]
                                        ]
                                    ]
                                ),
                                'is_downloaded' => new AttributeValue(
                                    [
                                        'attribute_code' => 'is_downloaded',
                                        'value' => 0
                                    ]
                                ),
                                'path' => new AttributeValue(
                                    [
                                        'attribute_code' => 'path',
                                        'value' => ''
                                    ]
                                )
                            ]
                        ]
                    )
                ],
                'expectedResult' => [
                    'same_model' => [
                        [
                            'id' => 2,
                            'title' => 'Some Title',
                            'thumbnail_url' => 'https://t4.ftcdn.net/z6rPCvS5umPhRUNPa62iA2YYVG49yo2n.jpg',
                            'thumbnail_500_url' => 'https://t4.ftcdn.net/z6rPCvS5umPhRUNPa62iA2YYVG49yo2n.jpg',
                            'creator_name' => 'Author',
                            'content_type' => 'image/jpeg',
                            'width' => 1000,
                            'height' => 1000,
                            'category' => 123,
                            'is_downloaded' => 0,
                            'path' => '',
                            'keywords' => [
                                [
                                    'name' => 'keyword #1'
                                ],
                                [
                                    'name' => 'keyword #2'
                                ],
                                [
                                    'name' => 'keyword #3'
                                ]
                            ]
                        ]
                    ],
                    'same_series' => [
                        [
                            'id' => 2,
                            'title' => 'Some Title',
                            'thumbnail_url' => 'https://t4.ftcdn.net/z6rPCvS5umPhRUNPa62iA2YYVG49yo2n.jpg',
                            'thumbnail_500_url' => 'https://t4.ftcdn.net/z6rPCvS5umPhRUNPa62iA2YYVG49yo2n.jpg',
                            'creator_name' => 'Author',
                            'content_type' => 'image/jpeg',
                            'width' => 1000,
                            'height' => 1000,
                            'category' => 123,
                            'is_downloaded' => 0,
                            'path' => '',
                            'keywords' => [
                                [
                                    'name' => 'keyword #1'
                                ],
                                [
                                    'name' => 'keyword #2'
                                ],
                                [
                                    'name' => 'keyword #3'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
