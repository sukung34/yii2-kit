<?php

namespace app\behaviors;

use app\components\ActiveQuery;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

class KeyIncrementBehavior extends Behavior {

    public $incrementAttribute;
    public $dependAttributes = [];

    public function events() {
        return [
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
        ];
    }

    public function beforeValidate($event) {
        /* @var $query ActiveQuery */
        $owner = $this->owner;

        if (isset($this->incrementAttribute) && $owner->isNewRecord) {
            $query = $owner->find();
            $query->select(new Expression('COALESCE(MAX(' . $this->incrementAttribute . '),0) +1'));

            if (count($this->dependAttributes)) {
                foreach ($this->dependAttributes as $attribute) {
                    $query->andWhere([$attribute => $owner->{$attribute}]);
                }
            }
            $owner->{$this->incrementAttribute} = $query->scalar();
        }
    }

}
