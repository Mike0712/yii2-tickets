<?php

namespace rgen3\tickets\models\forms;


use common\models\User;
use rgen3\tickets\models\TicketTheme;
use rgen3\tickets\models\TicketMessage;
use yii\base\Model;

class UpdateTicket extends Model
{
    private $modelForAnswer;
    private $user;
    public $themeId = null;

    public function setUser($user)
    {
        $this->user = $user;
    }

    private function setModelForAnswer($id)
    {
        $this->modelForAnswer = TicketTheme::find()->where(['ticket_themes.id' => $id])
            ->joinWith('dialog')
            ->one();
    }

    public function getModelForAnswer($id)
    {
        $this->setModelForAnswer($id);
        return $this->modelForAnswer;
    }

    public function update(array $action)
    {
        if (!$this->checkRights($this->modelForAnswer)){
            die('access denied');
        }

        if ($action['action'] === 'close')
        {
            $this->modelForAnswer->is_closed = true;
            $this->modelForAnswer->save();

            return true;
        }

        if ($action['action'] === 'answer')
        {
            $model = new CreateMessage();
            $model->load($action['params']);
            if($model->create()){
                $this->setStatusOfMessages('answer');
            }
            return $model;
        }

        if($action['action'] === 'setStatusOfMessage')
        {
            $operation = $action['operation'];
            $this->setStatusOfMessages($operation);

            return true;
        }

        return false;
    }

    private function checkRights($ticketTheme)
    {
        if (count(array_filter(User::findByRole('admin'), function (User $item){
            return $item->id == $this->user->id;
        }))){
            return true;
        }

        if ($ticketTheme->user_from == $this->user->id || $ticketTheme->assigned_to == $this->user->id){
            return true;
        }

        return false;
    }

    private function setStatusOfMessages(string $operation)
    {
        $sender_id = $this->modelForAnswer->sender->id;
        $messages = $this->modelForAnswer->getUnreadMessages()->where(['answered_by' => $sender_id])->all();

        array_map(function (TicketMessage $message) use ($operation){
            if ($operation == 'read' && $message->status_id == TicketManager::UNREAD_MESSAGE){
                $message->is_new = false;
                $message->status_id = TicketManager::READ_MESSAGE;
                $message->status_at = date('Y-m-d h:i:s');
                $message->save();
            }
            if ($operation == 'answer' && $message->status_id == 2){
                $message->status_id = TicketManager::ANSWER_MESSAGE;
                $message->status_at = date('Y-m-d h:i:s');
                $message->save();
            }
        },$messages);
    }
}