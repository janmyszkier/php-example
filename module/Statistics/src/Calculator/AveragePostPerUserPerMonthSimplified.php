<?php

namespace Statistics\Calculator;

use SocialPost\Dto\SocialPostTo;
use Statistics\Dto\StatisticsTo;

/**
 * Class Calculator
 *
 * @package Statistics\Calculator
 */
class AveragePostPerUserPerMonthSimplified extends AbstractCalculator
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
        $userKey = $postTo->getAuthorId();
        $timeKey = $postTo->getDate()->format('Y-m');

        if (!isset($this->postCount[$timeKey][$userKey])) {
            $this->postCount[$timeKey][$userKey] = 0;
        }
        $this->postCount[$timeKey][$userKey]++;
    }

    /**
     * @return StatisticsTo
     */
    protected function doCalculate(): StatisticsTo
    {
        $stats = new StatisticsTo();

        $userIds = [];
        foreach ($this->postCount as $yearMonth => $userPostCounts) {
            foreach ($userPostCounts as $userId => $userPostCount) {
                $userIds[] = $userId;
            }
        }

        /* always include all users */
        $userIdCount = count(array_unique($userIds));

        foreach ($this->postCount as $yearMonth => $userPostCounts) {

            if (empty($userPostCounts)) {
                continue;
            }

            $averagePostCount = array_sum($userPostCounts) / $userIdCount;
            $roundedValue = number_format($averagePostCount, 2);

            $child = (new StatisticsTo())
                ->setName($this->parameters->getStatName())
                ->setSplitPeriod($yearMonth)
                ->setValue($roundedValue)
                ->setUnits(self::UNITS);

            $stats->addChild($child);

        }
        return $stats;
    }
}
