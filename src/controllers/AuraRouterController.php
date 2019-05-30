<?php

namespace hiapi\controllers;

use Aura\Router\RouterContainer;
use hiqdev\yii\compat\yii;
use yii\base\Module;
use yii\web\Controller;
use yii\web\Response;

class AuraRouterController extends Controller
{
    /**
     * @var \yii\web\Response
     */
    protected $response;
    /**
     * @var RouterContainer
     */
    private $aura;

    public function __construct(
        $id, Module $module,
        RouterContainer $aura,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->aura = $aura;
        $this->response = yii::getApp()->getResponse();
    }

    public function actionRoute()
    {
        $request = \GuzzleHttp\Psr7\ServerRequest::fromGlobals();
        /** @var \Aura\Router\RouterContainer $router */
        $router = $this->aura;

        $route = $router->getMatcher()->match($request);
        if (!$route) {
            $response = (new \GuzzleHttp\Psr7\Response())
                ->withStatus(404);
        } else {
            foreach ($route->attributes as $attribute => $value) {
                $request = $request->withAttribute($attribute, $value);
            }
            $handle = $route->handler;
            $response = $handle($request);
        }

        $this->response->format = Response::FORMAT_RAW;
        $this->response->setHeaders($response->getHeaders());
        $this->response->setStatusCode($response->getStatusCode());
        $this->response->setBody($response->getBody());

        return $this->response->send();
    }
}
