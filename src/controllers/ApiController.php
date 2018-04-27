<?php
/**
 * HiAPI Yii2 base project for building API
 *
 * @link      https://github.com/hiqdev/hiapi
 * @package   hiapi
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017, HiQDev (http://hiqdev.com/)
 */

namespace hiapi\controllers;

use hiapi\bus\ApiCommandsBusInterface;
use hiapi\components\QueryParamAuth;
use hiqdev\yii2\autobus\components\AutoBusInterface;
use hiqdev\yii2\autobus\components\BranchedAutoBus;
use Yii;
use yii\base\Module;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\ContentNegotiator;
use yii\web\Controller;
use yii\web\Response;

/**
 * Class ApiController
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class ApiController extends Controller
{
    /**
     * @var \yii\web\Response
     */
    private $response;
    /**
     * @var AutoBusInterface|BranchedAutoBus
     */
    private $autoBus;

    public function __construct(
        string $id,
        Module $module,
        ApiCommandsBusInterface $autoBus,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->autoBus = $autoBus;
        $this->response = Yii::$app->response; // TODO: di
    }

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'contentNegotiator' => [
                'class' => ContentNegotiator::class,
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            'auth' => [
                'class' => CompositeAuth::class,
                'optional' => ['command'],
                'authMethods' => [
                    HttpBearerAuth::class,
                    [
                        'class' => QueryParamAuth::class,
                        'tokenParam' => 'access_token',
                    ],
                ],
            ],
        ]);
    }

    public function actionCommand($version, $resource, $action, $bulk = false) // todo: use $version, $bulk
    {
        $handledCommand = $this->autoBus->runCommand($this->buildCommandName($resource, $action, $bulk), []);

        $this->response->setHeaders($handledCommand->getHeaders());
        $this->response->setStatusCode($handledCommand->getStatusCode());
        $this->response->setBody($handledCommand->getBody());

        return $this->response->send();
    }

    private function buildCommandName($resource, $action, $bulk = false) // todo use $bulk
    {
        return $resource . ucfirst($action);
    }
}
