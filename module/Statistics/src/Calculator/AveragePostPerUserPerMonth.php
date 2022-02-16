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
        $key = $postTo->getAuthorName().' ('.$postTo->getAuthorId().')';
        if(!isset($this->postCount[$key])){
            $this->postCount[$key]=0;
        }
        $this->postCount[$key]++;
    }

    /**
     * @return StatisticsTo
     */
    protected function doCalculate(): StatisticsTo
    {
        $stats = new StatisticsTo();
        foreach ($this->postCount as $authorUniqueKey => $postCount) {
            $child = (new StatisticsTo())
                ->setName($this->parameters->getStatName())
                ->setSplitPeriod($authorUniqueKey)
                ->setValue($postCount)
                ->setUnits(self::UNITS);

            $stats->addChild($child);
        }
        return $stats;
    }
}
