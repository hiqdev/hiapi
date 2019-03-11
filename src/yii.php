<?php

namespace hiapi;

class yii {
    public static function getApp()
    {
        return class_exists('Yii') ? \Yii::$app : \yii\helpers\Yii::getApp();
    }

    public static function getContainer()
    {
        return class_exists('Yii') ? \Yii::$container : \yii\helpers\Yii::getContainer();
    }

    public static function createObject($class, $args)
    {
        return class_exists('Yii') ? \Yii::createObject($class, $args) : \yii\helpers\Yii::createObject($class, $args);
    }

    public static function getAlias($alias, $throwException = true)
    {
        return class_exists('Yii') ? \Yii::getAlias($alias, $throwException) : \yii\helpers\Yii::getAlias($alias, $throwException);
    }

    public static function referenceTo($id)
    {
        return class_exists('Yii') ? \yii\di\Instance::of($id) : \yii\di\Reference::to($id);
    }
}
