<?php


namespace Sibche\Domains\User\Services;


use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Sibche\Domains\User\Models\FeatureFlag;
use Sibche\Domains\User\Models\FeatureFlagUser;

class FeatureFlagService
{
    /**
     * @var AuthService
     */
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function validationRolesOfCreating()
    {
        return [
            'name' => 'required|unique:feature_flags,name',
            'type' => [
                'required',
                Rule::in(FeatureFlag::TYPES),
            ],
        ];
    }

    public function validationRolesOfUpdating()
    {
        return [
            'type' => [
                'required',
                Rule::in(FeatureFlag::TYPES),
            ],
        ];
    }

    public function validationRolesOfAttachingUser()
    {
        return [
            'user_id' => 'required|exists:users,id',
        ];
    }

    public function attachUserToFeatureFlag(array $data, $featureFlagName)
    {
        $featureFlag = $this->getFeatureFlagById($featureFlagName);

        if (!$featureFlag)
            return false;

        $featureFlagUser = new FeatureFlagUser();
        $featureFlagUser->feature_flag_name = $featureFlag->name;
        $featureFlagUser->user_id = $data['user_id'];
        $featureFlagUser->save();

        return true;
    }

    public function detachUserToFeatureFlag($userFeatureFlagId)
    {
        $featureFlagUser = FeatureFlagUser::where('id', $userFeatureFlagId)->first();

        if (!$featureFlagUser)
            return false;

        $featureFlagUser->delete();
        return true;
    }

    public function createFeatureFlag(array $data)
    {
        $featureFlag = new FeatureFlag();
        $featureFlag->name = $data['name'];
        $featureFlag->type = $data['type'];
        $featureFlag->save();

        Cache::tags('feature-flag')->flush();
        return $featureFlag;
    }

    public function updateFeatureFlag(array $data, $featureFlagUser)
    {
        $featureFlag = $this->getFeatureFlagById($featureFlagUser);

        if (!$featureFlag)
            return false;

        $featureFlag->type = $data['type'];
        $featureFlag->save();

        Cache::tags('feature-flag')->flush();
        return $featureFlag;
    }

    public function getFeatureFlagById($featureFlagName)
    {
        return FeatureFlag::find($featureFlagName);
    }

    private array $caches = [];

    public function getDisabledFeatureFlagsName($userId = null)
    {
        $key = 'feature-flag-for-' . ('user-' . $userId ?? 'public');
        if (!isset($this->caches[$key]))
            $this->caches[$key] = Cache::tags('feature-flag')->remember($key, Carbon::now()->addSecond(60), function () use ($userId) {

                $featureFlags = FeatureFlag::where('type', FeatureFlag::TYPE_PRIVATE)->orWhere('type', FeatureFlag::TYPE_DISABLED)->get();

                $privateFeatureFlagsName = $featureFlags->where('type', FeatureFlag::TYPE_PRIVATE)->pluck('name')->toArray();
                $disabledFeatureFlagsName = $featureFlags->where('type', FeatureFlag::TYPE_DISABLED)->pluck('name')->toArray();

                $userFeatureFlags = FeatureFlagUser::where('user_id', $userId)->get();

                $userFeatureFlagsName = $userFeatureFlags->pluck('feature_flag_name')->toArray();


                $disabledFeatureFlagsNameForUser = array_diff($privateFeatureFlagsName, $userFeatureFlagsName);

                return array_merge($disabledFeatureFlagsName, $disabledFeatureFlagsNameForUser);
            });

        return $this->caches[$key];
    }

    public function isFeatureFlagEnabledForUser($featureFlagName, $userId = null)
    {
        $featureFlags = $this->getDisabledFeatureFlagsName($userId);
        return !in_array($featureFlagName, $featureFlags);
    }

    public function isFeatureFlagEnabledForLoggedInUser($featureFlagName)
    {
        return $this->isFeatureFlagEnabledForUser($featureFlagName, $this->authService->getLoggedInUserId());
    }

    public function destroyFeatureFlag($featureFlagId)
    {
        $featureFlag = $this->getFeatureFlagById($featureFlagId);

        if (!$featureFlag)
            return false;

        $featureFlag->delete();

        Cache::tags('feature-flag')->flush();
        return true;
    }
}
