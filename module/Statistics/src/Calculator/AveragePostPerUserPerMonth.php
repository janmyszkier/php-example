<?php

namespace Statistics\Calculator;

use SocialPost\Dto\SocialPostTo;
use Statistics\Dto\StatisticsTo;

/**
 * Class Calculator
 *
 * @package Statistics\Calculator
 */
class AveragePostPerUserPerMonth extends AbstractCalculator
{

    protected const UNITS = 'posts';

    /**
     * @var array
     */
    private array $postCount = [];

    /**
     * @param SocialPostTo $postTo
     */
    protected function doAccumulate(SocialPostTo $postTo): void
    {
        $userKey = $postTo->getAuthorName() . ' (' . $postTo->getAuthorId() . ')';
        $timeKey = $postTo->getDate()->format('Y-m');

        if (!isset($this->postCount[$userKey][$timeKey])) {
            $this->postCount[$userKey][$timeKey] = 0;
        }
        $this->postCount[$userKey][$timeKey]++;
    }

    /**
     * @return StatisticsTo
     */
    protected function doCalculate(): StatisticsTo
    {
        $stats = new StatisticsTo();
        foreach ($this->postCount as $authorUniqueKey => $authorMonthPosts) {
            foreach ($authorMonthPosts as $yearMonth => $postCount) {
                $child = (new StatisticsTo())
                    ->setName($this->parameters->getStatName())
                    ->setSplitPeriod($authorUniqueKey . '|' . $yearMonth)
                    ->setValue($postCount)
                    ->setUnits(self::UNITS);

                $stats->addChild($child);
            }
        }
        return $stats;
    }
}
