<?php


namespace Roboticsexpert\FeatureFlag\Services;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Roboticsexpert\FeatureFlag\Models\FeatureFlag;
use Roboticsexpert\FeatureFlag\Models\FeatureFlagUser;

class FeatureFlagService
{

    public function attachUserToFeatureFlag(string $userId, $featureFlagName): bool
    {
        $featureFlag = $this->getFeatureFlag($featureFlagName);

        if (!$featureFlag)
            return false;

        FeatureFlagUser::query()->updateOrCreate([
            'feature_flag_name' => $featureFlag->name,
            'user_id'           => $userId,
        ]);

        return true;
    }

    public function detachUserToFeatureFlag(string $userId, string $featureFlagName): bool
    {
        $featureFlagUser = FeatureFlagUser::where('feature_flag_name', $featureFlagName)->where('user_id', $userId)->first();

        if (!$featureFlagUser)
            return false;

        $featureFlagUser->delete();
        return true;
    }

    public function createFeatureFlag(string $name): FeatureFlag
    {
        $featureFlag = new FeatureFlag();
        $featureFlag->name = $name;
        $featureFlag->type = FeatureFlag::TYPE_DISABLED;
        $featureFlag->save();

        return $featureFlag;
    }

    /**
     * @throws \Exception
     */
    public function changeFeatureFlagType(string $featureFlagName, string $type): FeatureFlag
    {
        if (!in_array($type, FeatureFlag::TYPES)) {
            throw new \Exception('type is not valid !');
        }

        $featureFlag = $this->getFeatureFlag($featureFlagName);

        if (!$featureFlag)
            throw new \Exception('Feature flag is not found!');


        $featureFlag->type = $type;
        $featureFlag->save();

        return $featureFlag;
    }

    public function getTypes(): array
    {
        return FeatureFlag::TYPES;
    }

    public function getFeatureFlag(string $featureFlagName): ?FeatureFlag
    {
        return FeatureFlag::find($featureFlagName);
    }


    public function getDisabledFeatureFlagsName($userId = null):array
    {
        $result = [];


        $featureFlags = Cache::remember('feature-flags', Carbon::now()->addSeconds(60), function () {
            return $this->getAllFeatureFlags();
        });

        $key = 'feature-flag::user::' . ('user-' . $userId ?? 'public');
        $userFeatureFlagUsers = Cache::remember($key, Carbon::now()->addSeconds(60), function () use ($userId) {
            return $this->getUserFeatureFlagUsers($userId);
        });
        foreach ($featureFlags as $featureFlag) {
            if ($featureFlag->type == FeatureFlag::TYPE_DISABLED)
                $result[$featureFlag->name] = $featureFlag->name;

            if ($featureFlag->type == FeatureFlag::TYPE_PRIVATE) {
                $found = false;
                foreach ($userFeatureFlagUsers as $userFeatureFlagUser) {
                    if ($userFeatureFlagUser->feature_flag_name == $featureFlag->name) {
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $result[$featureFlag->name] = $featureFlag->name;
                }
            }
        }

        return array_values($result);
    }

    public function isFeatureFlagEnabledForUser(string $featureFlagName, string $userId = null): bool
    {
        $featureFlags = $this->getDisabledFeatureFlagsName($userId);
        return !in_array($featureFlagName, $featureFlags);
    }


    public function destroyFeatureFlag(string $featureFlagName): bool
    {
        $featureFlag = $this->getFeatureFlag($featureFlagName);

        if (!$featureFlag)
            return false;

        $featureFlag->delete();

        return true;
    }

    /**
     * @param string $featureFlagName
     * @return FeatureFlagUser[] | Collection
     */
    public function getAllFeatureFlagUsers(string $featureFlagName): Collection
    {
        return FeatureFlagUser::where('feature_flag_name', $featureFlagName)->get();
    }

    /**
     * @param string|null $userId
     * @return \Illuminate\Support\Collection | FeatureFlagUser[]
     */
    private function getUserFeatureFlagUsers(string $userId = null): \Illuminate\Support\Collection
    {
        if (!$userId)
            return collect();
        return FeatureFlagUser::where('user_id', $userId)->get();
    }

    /**
     * @return FeatureFlag[]|Collection
     */
    public function getAllFeatureFlags(): Collection
    {
        return FeatureFlag::all();
    }
}
