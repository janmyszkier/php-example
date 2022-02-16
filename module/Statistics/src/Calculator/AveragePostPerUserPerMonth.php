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
        // @TODO: Implement
    }

    /**
     * @return StatisticsTo
     */
    protected function doCalculate(): StatisticsTo
    {
        // @TODO: Implement
        return new StatisticsTo();
    }
}
