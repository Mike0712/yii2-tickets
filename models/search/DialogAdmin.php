<?php

namespace rgen3\tickets\models\search;

use rgen3\tickets\models\TicketTheme;
use yii\data\ActiveDataProvider;

class DialogAdmin extends TicketTheme
{
    public function search($params, array $role)
    {
        $lang = isset($params['lang']) ? $params['lang'] : \Yii::$app->language;

        $query = TicketTheme::find()
            ->with('dialog');

        if ($role['admin'] === false){
            $query->where(['assigned_to' => $role['user_id']]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate())
        {
            $query->where('1=0');
            return $dataProvider;
        }

        $this->load($params);

        if (!$this->validate())
        {
            $query->where('1=0');
            return $dataProvider;
        }

        return $dataProvider;
    }
}