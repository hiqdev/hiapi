<?php

namespace hiapi\commands;

use yii\base\Model;
use yii\base\Request;

/**
 * Trait RequestAwareTrait
 *
 * @package hiapi\commands
 * @property Model $this
 */
trait RequestAwareTrait
{
    public function loadFromRequest(\yii\web\Request $request)
    {
        $this->load($this->getRequestData($request), '');
        $this->validate();

        return $this->hasErrors() ? $this->getErrors() : null;
    }

    public function getRequestData(\yii\web\Request $request)
    {
        $get = $request->get();
        $post = $request->post();

        return array_merge($get, $post);
    }
}
