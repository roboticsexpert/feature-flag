<?php

namespace Sibche\Domains\User\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class FeatureFlag
 * @package Sibche\Domains\User\Models
 * @property string feature_flag_name
 * @property int id
 * @property int user_id
 */
class FeatureFlagUser extends Model
{
    protected $table = 'feature_flag_user';

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}