<?php

namespace Roboticsexpert\FeatureFlag\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class FeatureFlag
 * @package Roboticsexpert\FeatureFlag\Models
 * @property string feature_flag_name
 * @property int id
 * @property string user_id
 */
class FeatureFlagUser extends Model
{
    protected $table = 'feature_flag_user';

    public $timestamps = false;

    protected $fillable = ['feature_flag_name', 'user_id'];
}
