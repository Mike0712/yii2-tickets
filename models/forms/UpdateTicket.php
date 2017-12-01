<?php

namespace rgen3\tickets\models\forms;


use common\models\User;
use rgen3\tickets\models\TicketTheme;
use rgen3\tickets\models\TicketMessage;
use yii\base\Model;

class UpdateTicket extends CreateTicket
{
    private $modelForAnswer;
    public $themeId = null;

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
        $ticketTheme = TicketTheme::findOne($this->themeId);
        if (!$this->checkRights($ticketTheme)){
            die('access denied');
        }

        if($action['action'] === 'close')
        {
            $ticketTheme->is_closed = true;
            $ticketTheme->save();
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

    public function setStatusOfMessages(string $operation)
    {
        $sender_id = $this->modelForAnswer->sender->id;
        $messages = $this->modelForAnswer->getUnreadMessages()->where(['answered_by' => $sender_id])->all();

        array_map(function (TicketMessage $message) use ($operation){
            if ($operation == 'read' && $message->status_id == self::UNREAD_MESSAGE){
                $message->is_new = false;
                $message->status_id = self::READ_MESSAGE;
                $message->status_at = date('Y-m-d h:i:s');
                $message->save();
            }
            if ($operation == 'answer' && $message->status_id == 2){
                $message->status_id = self::ANSWER_MESSAGE;
                $message->status_at = date('Y-m-d h:i:s');
                $message->save();
            }
        },$messages);
    }
}