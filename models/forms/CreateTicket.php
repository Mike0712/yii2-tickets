<?php

namespace rgen3\tickets\models\forms;

use yii\web\User;
use rgen3\tickets\models\TicketTheme;
use rgen3\tickets\Module;
use rgen3\tickets\traits\UserFrom;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class CreateTicket extends Model
{
    use UserFrom;

    public $dialogId;
    public $subject;
    public $message;
    public $status;

    private $assignedTo;

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        $this->setUserFrom();
        $this->setAssignedTo();
    }

    private function setAssignedTo()
    {
        $user_id = $this->userFrom;
        $user = \common\models\User::findOne($user_id);
        $manager_id = $user->manager_id;

        if ($manager_id){
            $this->assignedTo = $manager_id;
        }else{
            $this->assignedTo = $user->manager->id;
        }
    }

    public function getAssignedTo()
    {
        return $this->assignedTo;
    }

    public function rules()
    {
        $userModel = Module::$userModel;
        $rules = [
            [['subject', 'message'], 'required'],
            [['subject', 'message'], 'safe'],
            [['subject', 'message'], 'filter', 'filter' => function($value){
                $value = strip_tags($value);
                $value = htmlentities($value);
                $value = trim($value);
                return $value;
            }],
            [['assignedTo'], 'integer'],
            [
                ['assignedTo'],
                'exist',
                'skipOnError' => false,
                'targetClass' => $userModel::className(),
                'targetAttribute' => ['assignedTo' => 'id']
            ],
            ['status', 'boolean']
        ];

        $rules = ArrayHelper::merge($rules, UserFrom::rules());
        return $rules;
    }

    public function save()
    {
        $ticketThemes = new TicketTheme();
        $ticketMessage = new CreateMessage();

        $ticketThemes->user_from = $this->getUserFrom();
        $ticketThemes->assigned_to = $this->getAssignedTo();
        $ticketThemes->subject = $this->subject;
        $ticketThemes->is_closed = 0;

        if ($ticketThemes->validate())
        {
            $saved = $ticketThemes->save();
        }
        else
        {
            return false;
        }


        $ticketMessage->message = $this->message;
        $ticketMessage->dialogId = $ticketThemes->id;

        $ticketMessage->create();


        $this->dialogId = $ticketThemes->id;

        return $saved;
    }
}