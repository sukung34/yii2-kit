<?php

namespace sukung34\kit\behaviors;

use sukung34\kit\components\DateHelper;
use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class DataConvertBehavior extends Behavior
{

    public $dateAttributes = [];
    public $numberAttributes = [];
    public $citizenAttributes = [];
    public $areaAttributes = [];

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'afterFind',
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
        ];
    }

    public function afterFind($event)
    {
        foreach ($this->dateAttributes as $isoAttribute => $thAttribute) {
            $this->owner->{$thAttribute} = DateHelper::convertIso2Thai($this->owner->{$isoAttribute});
        }
        foreach ($this->citizenAttributes as $attribute => $inputAttribute) {
            $this->owner->{$inputAttribute} = Yii::$app->formatter->asCitizen($this->owner->{$attribute});
        }
    }

    public function beforeValidate($event)
    {
        foreach ($this->dateAttributes as $isoAttribute => $thAttribute) {
            $this->owner->{$isoAttribute} = DateHelper::convertThai2Iso($this->owner->{$thAttribute});
        }
        foreach ($this->numberAttributes as $attribute) {
            $this->owner->{$attribute} = strtr($this->owner->{$attribute}, [
                ' ' => '',
                ',' => '',
            ]);
        }
        foreach ($this->citizenAttributes as $attribute => $inputAttribute) {
            $this->owner->{$attribute} = Yii::$app->formatter->asCitizenId($this->owner->{$inputAttribute});
        }
        foreach ($this->areaAttributes as $attribute) {
            $this->owner->{$attribute . '_rai'} = self::sanitizeNumber($this->owner->{$attribute . '_rai'});
            $this->owner->{$attribute . '_ngan'} = self::sanitizeNumber($this->owner->{$attribute . '_ngan'});
            $this->owner->{$attribute . '_wa'} = self::sanitizeNumber($this->owner->{$attribute . '_wa'});
            $this->owner->{$attribute} = self::convertToArea($this->owner->{$attribute . '_rai'}, $this->owner->{$attribute . '_ngan'}, $this->owner->{$attribute . '_wa'});
        }
    }

    protected function processDate()
    {
        
    }

    public static function convertToArea($r, $n, $w)
    {
        return floor($r) + (floor($n) / 4) + (floor($w) / 400);
    }

    public static function sanitizeNumber($num)
    {
        return strtr($num, [',' => '']);
    }

}
