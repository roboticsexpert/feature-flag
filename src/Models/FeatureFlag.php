<?php

namespace Sibche\Domains\User\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class FeatureFlag
 * @package Sibche\Domains\User\Models
 * @property string type
 * @property int id
 * @property string name
 */
class FeatureFlag extends Model
{
    public $timestamps = false;

    const TYPE_PUBLIC = 'PUBLIC';
    const TYPE_PRIVATE = 'PRIVATE';
    const TYPE_DISABLED = 'DISABLED';

    const FEATURE_FLAG_SEARCH_AD = 'SEARCH_AD';
    const FEATURE_FLAG_UPDATE_CENTER = 'UPDATE_CENTER';
    const FEATURE_HAMSAN_AD = 'HAMSAN_AD';
    const FEATURE_ZSIGN = 'ZSIGN';


    const TYPES = [
        self::TYPE_DISABLED,
        self::TYPE_PRIVATE,
        self::TYPE_PUBLIC
    ];

    public $incrementing = false;
    protected $primaryKey = 'name';
}
