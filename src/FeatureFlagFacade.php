<?php


namespace Roboticsexpert\FeatureFlag;


use Illuminate\Support\Facades\Facade;
use Roboticsexpert\FeatureFlag\Services\FeatureFlagService;

class FeatureFlagFacade extends Facade
{

    protected static function getFacadeAccessor(): string
    {
        return FeatureFlagService::class;
    }
}
