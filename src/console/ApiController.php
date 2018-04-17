<?php
/**
 * HiAPI Yii2 base project for building API
 *
 * @link      https://github.com/hiqdev/hiapi
 * @package   hiapi
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017, HiQDev (http://hiqdev.com/)
 */

namespace hiapi\console;

use hiapi\bus\ApiCommandsBusInterface;
use Yii;
use yii\base\Module;
use yii\console\Controller;
use yii\helpers\Inflector;

/**
 * Class ApiController
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class ApiController extends Controller
{

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
    }

    public function runAction($id, $params = [])
    {
        $args = [$params[0], $params['_aliases']];

        return \yii\base\Controller::runAction($id, $params);
    }

    public function actionCommand($route, $args)
    {
        [$resource, $action] = explode('/', $route, 2);
        $result = $this->autoBus->runCommand($this->buildCommandName($resource, $action), $args);

        echo print_r($result, true);
    }

    private function buildCommandName($resource, $action, $bulk = false) // todo use $bulk
    {
        return $resource . ucfirst(Inflector::id2camel($action));
    }
}
