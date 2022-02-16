<?php

declare(strict_types = 1);

namespace Tests\unit;

use PHPUnit\Framework\TestCase;
use SebastianBergmann\Complexity\Calculator;
use SocialPost\Dto\SocialPostTo;
use Statistics\Calculator\Factory\StatisticsCalculatorFactory;
use Statistics\Dto\ParamsTo;
use Statistics\Dto\StatisticsTo;
use Statistics\Enum\StatsEnum;
use Statistics\Service\StatisticsService;

/**
 * Class ATestTest
 *
 * @package Tests\unit
 */
class AveragePostPerUserPerMonthTest extends TestCase
{
    private StatisticsService $statsService;
    private array $params;

    const USER_1_ID = '1';
    const USER_1_NAME = 'John Doe';
    const USER_2_ID = '2';
    const USER_2_NAME = 'Tom Smith';

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $statsCalculatorFactory = new StatisticsCalculatorFactory();
        $this->statsService = new StatisticsService($statsCalculatorFactory);
        $this->params = [
            (new ParamsTo())->setStatName(StatsEnum::AVERAGE_POST_NUMBER_PER_USER_PER_MONTH)
        ];
    }

    public function emptyStatsDataProvider()
    {
        $stats = $this->statsService->calculateStats($this->getEmptyPostSet(), $this->params);
        yield [
            $this->flattenStats($stats)
        ];
    }

    public function singleUserPostDataProvider()
    {
        $stats = $this->statsService->calculateStats($this->getSingleUserPost(), $this->params);
        yield [
            $this->flattenStats($stats)
        ];
    }

    public function singleUserPostsDataProvider()
    {
        $stats = $this->statsService->calculateStats($this->getSingleUserPosts(), $this->params);
        yield [
            $this->flattenStats($stats)
        ];
    }

    public function twoUserPostsDataProvider()
    {
        $stats = $this->statsService->calculateStats($this->getTwoUserPosts(), $this->params);
        yield [
            $this->flattenStats($stats)
        ];
    }

    public function flattenStats(StatisticsTo $stats) : array
    {
        $flattenStats = [];
        foreach($stats->getChildren() as $statChild) {
            foreach ($statChild->getChildren() as $userStat) {
                $flattenStats[$userStat->getSplitPeriod()] = $userStat->getValue();
            }
        }
        return $flattenStats;
    }

    /**
     * @dataProvider emptyStatsDataProvider
     */
    public function testNoStatsAvailable($statValues): void
    {
        $this->assertEquals([], $statValues);
    }

    /**
     * @dataProvider singleUserPostDataProvider
     */
    public function testOneUserWithOnePost($statValues): void
    {
        $this->assertEquals(1, $statValues[self::USER_1_NAME.' ('.self::USER_1_ID.')|2022-02']);
    }

    /**
     * @dataProvider singleUserPostsDataProvider
     */
    public function testOneUserWithFivePosts($statValues): void
    {
        $this->assertEquals(2, $statValues[self::USER_1_NAME.' ('.self::USER_1_ID.')|2022-02']);
        $this->assertEquals(3, $statValues[self::USER_1_NAME.' ('.self::USER_1_ID.')|2022-01']);
    }

    /**
     * @dataProvider twoUserPostsDataProvider
     */
    public function testTwoUsersWithPosts($statValues): void
    {
        $this->assertEquals(1, $statValues[self::USER_1_NAME.' ('.self::USER_1_ID.')|2021-01']);
        $this->assertEquals(1, $statValues[self::USER_1_NAME.' ('.self::USER_1_ID.')|2021-02']);
        $this->assertEquals(1, $statValues[self::USER_2_NAME.' ('.self::USER_2_ID.')|2021-01']);
        $this->assertEquals(1, $statValues[self::USER_2_NAME.' ('.self::USER_2_ID.')|2021-02']);
        $this->assertEquals(2, $statValues[self::USER_2_NAME.' ('.self::USER_2_ID.')|2021-03']);
    }

    private function getSingleUserPost(): \Traversable
    {
        yield (new SocialPostTo)->setAuthorId(self::USER_1_ID)->setAuthorName(self::USER_1_NAME)->setDate(new \DateTime('2022-02-01'));
    }

    private function getSingleUserPosts(): \Traversable
    {
        yield (new SocialPostTo)->setAuthorId(self::USER_1_ID)->setAuthorName(self::USER_1_NAME)->setDate(new \DateTime('2022-02-01'));
        yield (new SocialPostTo)->setAuthorId(self::USER_1_ID)->setAuthorName(self::USER_1_NAME)->setDate(new \DateTime('2022-02-01'));
        yield (new SocialPostTo)->setAuthorId(self::USER_1_ID)->setAuthorName(self::USER_1_NAME)->setDate(new \DateTime('2022-01-01'));
        yield (new SocialPostTo)->setAuthorId(self::USER_1_ID)->setAuthorName(self::USER_1_NAME)->setDate(new \DateTime('2022-01-01'));
        yield (new SocialPostTo)->setAuthorId(self::USER_1_ID)->setAuthorName(self::USER_1_NAME)->setDate(new \DateTime('2022-01-01'));
    }

    private function getTwoUserPosts(): \Traversable
    {
        yield (new SocialPostTo)->setAuthorId(self::USER_1_ID)->setAuthorName(self::USER_1_NAME)->setDate(new \DateTime('2021-01-01'));
        yield (new SocialPostTo)->setAuthorId(self::USER_2_ID)->setAuthorName(self::USER_2_NAME)->setDate(new \DateTime('2021-01-01'));

        yield (new SocialPostTo)->setAuthorId(self::USER_1_ID)->setAuthorName(self::USER_1_NAME)->setDate(new \DateTime('2021-02-01'));
        yield (new SocialPostTo)->setAuthorId(self::USER_2_ID)->setAuthorName(self::USER_2_NAME)->setDate(new \DateTime('2021-02-01'));
        yield (new SocialPostTo)->setAuthorId(self::USER_2_ID)->setAuthorName(self::USER_2_NAME)->setDate(new \DateTime('2021-03-01'));
        yield (new SocialPostTo)->setAuthorId(self::USER_2_ID)->setAuthorName(self::USER_2_NAME)->setDate(new \DateTime('2021-03-03'));
    }

    private function getEmptyPostSet(): \Traversable
    {
        yield;
    }
}
