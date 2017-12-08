<?php


namespace rgen3\tickets\controllers;


use rgen3\tickets\models\forms\TicketManager;
use rgen3\tickets\models\search\DialogAdmin;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

class Admin extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['index',
                            'update', 'messages', 'answer', 'close', 'get-answer-form', 'get-close-form', 'test',
                        ],
                        'allow' => true,
                        'roles' => ['admin', 'manager'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'settings' => [
                'class' => 'pheme\settings\SettingsAction',
                'modelClass' => 'app\models\Site',
                //'scenario' => 'site',	// Change if you want to re-use the model for multiple setting form.
                'viewName' => 'site-settings'	// The form we need to render
            ],
        ];
    }

    public function actionIndex()
    {
        $user = \Yii::$app->user;
        $role = ['user_id' => $user->id, 'admin' => $user->can('admin')];
        $searchModel = new DialogAdmin();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams, $role);

        return $this->render('index', compact('searchModel', 'dataProvider'));
    }

    public function actionGetAnswerForm()
    {
        $this->layout = false;
        $post = \Yii::$app->request->post();

        if (\Yii::$app->request->isAjax){
            if(!isset($post['theme_id'])){
                return 'Нет формы';
            }
            $themeId = $post['theme_id'];
            $ticketManager = new TicketManager(\Yii::$app->user);
            $theme = $ticketManager->update($themeId, ['action' => 'setStatusOfMessage', 'operation' => 'read', 'render' => true]);

            return $this->render('dialog/dialog_box_form', ['theme' => $theme, 'withDialog' => true]);
        }
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

            return $this->render('form_close', ['theme' => $data['theme'], 'model' => $data['model']]);
        }
    }

    public function actionClose()
    {
        $post = \Yii::$app->request->post();
        $ticketId = $post['UpdateTicket']['ticketId'];
        $ticketManager = new TicketManager(\Yii::$app->user);

        return $ticketManager->update($ticketId, ['action' => 'close']);
    }
}