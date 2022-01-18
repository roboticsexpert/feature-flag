<?php

namespace Roboticsexpert\FeatureFlag\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class FeatureFlag
 * @package Roboticsexpert\FeatureFlag\Models
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


    const TYPES = [
        self::TYPE_DISABLED,
        self::TYPE_PRIVATE,
        self::TYPE_PUBLIC
    ];

    public $incrementing = false;
    protected $primaryKey = 'name';
}
