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

    public function emptyStatsDataProvider(): array
    {
        $stats = $this->statsService->calculateStats($this->getEmptyPostSet(), $this->params);
        return $this->flattenStats($stats);
    }

    public function singleUserPostDataProvider(): array
    {
        $stats = $this->statsService->calculateStats($this->getSingleUserPost(), $this->params);
        return $this->flattenStats($stats);
    }

    public function singleUserPostsDataProvider(): array
    {
        $stats = $this->statsService->calculateStats($this->getSingleUserPosts(), $this->params);
        return $this->flattenStats($stats);
    }

    public function twoUserPostsDataProvider(): array
    {
        $stats = $this->statsService->calculateStats($this->getTwoUserPosts(), $this->params);
        return $this->flattenStats($stats);
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

    protected function setUp(): void
    {
        $statsCalculatorFactory = new StatisticsCalculatorFactory();
        $this->statsService = new StatisticsService($statsCalculatorFactory);
        $this->params = [
            (new ParamsTo())->setStatName(StatsEnum::AVERAGE_POST_NUMBER_PER_USER_PER_MONTH)
        ];
    }

    public function testNoStatsAvailable(): void
    {
        $statValues = $this->emptyStatsDataProvider();
        $this->assertEquals([], $statValues);
    }

    public function testOneUserWithOnePost(): void
    {
        $statValues = $this->singleUserPostDataProvider();
        $this->assertEquals(1, $statValues[self::USER_1_NAME.' ('.self::USER_1_ID.')']);
    }

    public function testOneUserWithFivePosts(): void
    {
        $statValues = $this->singleUserPostsDataProvider();
        $this->assertEquals(5, $statValues[self::USER_1_NAME.' ('.self::USER_1_ID.')']);
    }

    public function testTwoUsersWithPosts(): void
    {
        $statValues = $this->twoUserPostsDataProvider();
        $this->assertEquals(2, $statValues[self::USER_1_NAME.' ('.self::USER_1_ID.')']);
        $this->assertEquals(3, $statValues[self::USER_2_NAME.' ('.self::USER_2_ID.')']);
    }

    private function getSingleUserPost(): \Traversable
    {
        yield (new SocialPostTo)->setAuthorId(self::USER_1_ID)->setAuthorName(self::USER_1_NAME);
    }

    private function getSingleUserPosts(): \Traversable
    {
        yield (new SocialPostTo)->setAuthorId(self::USER_1_ID)->setAuthorName(self::USER_1_NAME);
        yield (new SocialPostTo)->setAuthorId(self::USER_1_ID)->setAuthorName(self::USER_1_NAME);
        yield (new SocialPostTo)->setAuthorId(self::USER_1_ID)->setAuthorName(self::USER_1_NAME);
        yield (new SocialPostTo)->setAuthorId(self::USER_1_ID)->setAuthorName(self::USER_1_NAME);
        yield (new SocialPostTo)->setAuthorId(self::USER_1_ID)->setAuthorName(self::USER_1_NAME);
    }

    private function getTwoUserPosts(): \Traversable
    {
        yield (new SocialPostTo)->setAuthorId(self::USER_1_ID)->setAuthorName(self::USER_1_NAME);
        yield (new SocialPostTo)->setAuthorId(self::USER_2_ID)->setAuthorName(self::USER_2_NAME);

        yield (new SocialPostTo)->setAuthorId(self::USER_1_ID)->setAuthorName(self::USER_1_NAME);
        yield (new SocialPostTo)->setAuthorId(self::USER_2_ID)->setAuthorName(self::USER_2_NAME);
        yield (new SocialPostTo)->setAuthorId(self::USER_2_ID)->setAuthorName(self::USER_2_NAME);
    }

    private function getEmptyPostSet(): \Traversable
    {
        yield;
    }
}
