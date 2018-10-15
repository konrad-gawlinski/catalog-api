<?php

define('APPLICATION_ROOT', __DIR__ . '/../../../');

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
*/
class Product_serviceTester extends \Codeception\Actor
{
    use _generated\Product_serviceTesterActions;

    private static $app = null;

    function __construct(\Codeception\Scenario $scenario)
    {
        parent::__construct($scenario);

        if (!self::$app)
            self::$app = require __DIR__ . '/../../../src/bootstrap.php';
    }

    function getApp()
    {
        return self::$app;
    }

   /**
    * Define custom actions here
    */
}
