<?php

namespace rgen3\tickets\models\forms;


use rgen3\tickets\traits\UserFrom;
use yii\base\Model;
use yii\web\User;
use rgen3\tickets\models\TicketTheme;

class TicketManager extends Model
{
    use UserFrom;

    const UNREAD_MESSAGE = 1;
    const READ_MESSAGE = 2;
    const ANSWER_MESSAGE = 3;

    private $user;

    public function __construct(User $user, array $config = [])
    {
        $this->user = $user;
        parent::__construct($config);
    }

    public function create($post)
    {
        $model = new CreateTicket();
        if(!$post){
            return $model;
        }
        $model->load($post);

        return $model;
    }

    public function update($themeId, $params = [])
    {
        $model = new UpdateTicket();
        $model->setUser($this->user);
        $theme = $model->getModelForAnswer($themeId);

        $res = $model->update($params);

        if (isset($params['render']) && $params['render']){
            return $theme;
        }

        if (isset($params['res']) && $params['res']){
            return $res;
        }

        return true;
    }

    public function prepareUpdate($themeId, $params = [])
    {
        $model = new UpdateTicket();
        $model->setUser($this->user);
        $theme = $model->getModelForAnswer($themeId);
        if ($params['prepare'] === 'close'){
            return ['model' => $model, 'theme' => $theme];
        }
    }
}