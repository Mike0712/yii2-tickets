<?php

namespace rgen3\tickets\controllers;

use common\models\User;
use rgen3\tickets\models\forms\CreateTicket;
use rgen3\tickets\models\forms\TicketManager;
use rgen3\tickets\models\search\Dialog;
use rgen3\tickets\Module;
use yii\web\Controller;
use yii\web\Response;

class Message extends Controller
{
    public function actionIndex()
    {
        return $this->actionDialog(false);
    }

    public function actionCreate()
    {
        $ticketManager = new TicketManager(\Yii::$app->user);
        $model = $ticketManager->create(\Yii::$app->request->post());

        if ($model->validate())
        {
            if ($model->save() !== false)
            {
                return $this->redirect(['/ticket/dialog/' . $model->dialogId]);
            }
        }
        return $this->render('create', ['model' => $model]);
    }

    public function actionDialog($id)
    {
        $model = (new TicketManager(\Yii::$app->user))->create([]);
        $searchModel = new Dialog();
        $params = [
            'Dialog' =>
            [
                'themeId' => (int) $id
            ]
        ];

        $dataProvider = $searchModel->search($params);

        $theme = current(
            array_filter($dataProvider->getModels(), function($item) use ($id) { return $item->id == $id;})
        );

        if ($dataProvider->totalCount === 0)
        {
            return $this->render('create', ['model' => $model]);
        }
        $user = User::findOne(\Yii::$app->user->id);
        $userModel = Module::$userModel;
        $data = [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'theme' => $theme,
            'receiver' => $theme->receiver ?? ((User::findOne($user->manager->id) ?? $user->manager) ?? $userModel::findOne(['id' => Module::$defaultAdminId]))
        ];

        if (\Yii::$app->request->isPjax)
        {
            return $this->renderPartial('dialog/dialog_box', ['theme' => $theme]);
        }

        return $this->render('dialog', $data);
    }

    public function actionAnswer()
    {
        $post = \Yii::$app->request->post();
        $ticketManager = new TicketManager(\Yii::$app->user);
        $model = $ticketManager->update($post['CreateMessage']['dialogId'], ['action' => 'answer', 'params' => $post, 'res' => true]);

        if (!\Yii::$app->request->isAjax)
        {
            return $this->redirect(["/ticket/dialog/{$model->dialogId}"]);
        }
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $model;
    }

    public function actionGetCloseForm()
    {   $this->layout = false;
        $post = \Yii::$app->request->post();

        if (\Yii::$app->request->isAjax){
            if(!isset($post['theme_id'])){
                return 'Нет формы';
            }
            $themeId = $post['theme_id'];
            $ticketManager = new TicketManager(\Yii::$app->user);
            $data = $ticketManager->prepareUpdate($themeId, ['prepare' => 'close']);

            return $this->render('/ticket/parts/form_close', ['theme' => $data['theme'], 'model' => $data['model']]);
        }
    }

    public function actionClose()
    {
        $post = \Yii::$app->request->post();
        $ticketId = $post['UpdateTicket']['ticketId'];
        $ticketManager = new TicketManager(\Yii::$app->user);

        return $ticketManager->update($ticketId, ['action' => 'close']);
    }

    public function actionTakeRowForClosed()
    {
        if (\Yii::$app->request->isAjax){
            $post = \Yii::$app->request->post();
            if (isset($post['ticketId'])){

            }
        }
        return false;
    }
}