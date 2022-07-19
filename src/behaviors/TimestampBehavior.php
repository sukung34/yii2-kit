<?php

namespace app\behaviors;

use yii\behaviors\TimestampBehavior as BaseTimestampBehavior;
use yii\db\Expression;

class TimestampBehavior extends BaseTimestampBehavior {

    protected function getValue($event) {
        if ($this->value === null) {
            return new Expression('NOW()');
        }
        return parent::getValue($event);
    }

}
