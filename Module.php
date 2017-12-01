<?php

namespace rgen3\tickets;

use common\models\User;
use yii\i18n\PhpMessageSource;

class Module extends \yii\base\Module
{
    public $languages;

    public static $defaultUserImage = '/img/partner/ico-money_guy.png';

    public static $defaultAdminId = 2;

    public static $userModel = User::class;

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        \Yii::$app->setAliases(['@tickets' => __DIR__ . '/../common']);

        $this->registerTranslation();
    }

    public function registerTranslation()
    {
        \Yii::$app->i18n->translations['rgen3/tickets/*'] = [
            'class' => PhpMessageSource::class,
            'sourceLanguage' => 'ru',
            'basePath' => '@tickets/messages',
            'fileMap' => [
                'rgen3/tickets/admin' => 'admin.php'
            ]
        ];
    }

    public static function t($category, $message, $params = [], $language = null)
    {
        return \Yii::t('rgen3/tickets/' . $category, $message, $params, $language);
    }
}