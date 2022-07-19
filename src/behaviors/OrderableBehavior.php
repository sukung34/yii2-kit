<?php

namespace app\behaviors;

use app\actions\OrderAction;
use app\components\ActiveQuery;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

class OrderableBehavior extends Behavior {

    public $orderAttribute;
    public $dependAttributes = [];

    public function events() {
        return [
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
        ];
    }

    public function beforeValidate($event) {
        /* @var $query ActiveQuery */
        $owner = $this->owner;

        if (isset($this->orderAttribute) && $owner->isNewRecord) {
            $query = $owner->find();
            $query->select(new Expression('COALESCE(MAX(' . $this->orderAttribute . '),0) +1'));

            if (count($this->dependAttributes)) {
                foreach ($this->dependAttributes as $attribute) {
                    $query->andWhere([$attribute => $owner->{$attribute}]);
                }
            }
            $owner->{$this->orderAttribute} = $query->scalar();
        }
    }

    public function doOrder($direction) {
        /* @var $owner ActiveRecord */
        /* @var $query ActiveQuery */
        $owner = $this->owner;
        switch ($direction) {
            case OrderAction::DIRECTION_UP:
                $query = $owner->find()->where(['<', $this->orderAttribute, $owner->{$this->orderAttribute}])->orderBy([$this->orderAttribute => SORT_DESC]);
                if (count($this->dependAttributes)) {
                    foreach ($this->dependAttributes as $attribute) {
                        $query->andWhere([$attribute => $owner->{$attribute}]);
                    }
                }
                $target = $query->one();
                break;
            case OrderAction::DIRECTION_DOWN:
                $query = $owner->find()->where(['>', $this->orderAttribute, $owner->{$this->orderAttribute}])->orderBy([$this->orderAttribute => SORT_ASC]);
                if (count($this->dependAttributes)) {
                    foreach ($this->dependAttributes as $attribute) {
                        $query->andWhere([$attribute => $owner->{$attribute}]);
                    }
                }
                $target = $query->one();
                break;
        }
        if (isset($target)) {
            $tmp = $target->{$this->orderAttribute};
            $target->updateAttributes([
                $this->orderAttribute => $owner->{$this->orderAttribute},
            ]);
            $owner->updateAttributes([
                $this->orderAttribute => $tmp,
            ]);
        }
    }

}
