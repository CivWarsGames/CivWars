<?php
class Messenger
{
    protected $message = array();

    public function Messenger()
    {
        $this->message['to'] = $_GET['to'];
        $this->message['subject'] = $_GET['subject'];
        $this->message['body'] = $_GET['body'];
        if($this->validateReciver()){
            $this->validateMessage();
            $this->sendMessageById();
        }
    }
    protected function validateReciver()
    {
        $language = DataBaseManager::fetchArray(DataBaseManager::query("SELECT language FROM {users} WHERE id_user = ".User::get_idUser()));
        $this->message['language'] = $language[0];
        if(Parser::onlyNumbersString($this->message['to'])){
            $result = DataBaseManager::query("SELECT id_user FROM {users} WHERE id_user = '".$this->message['to']."'");
            if(DataBaseManager::numRows($result) > 0){
                return true;
            }else{
                return false;
            }
        }else{
            $result = DataBaseManager::query("SELECT id_user FROM {users} WHERE name = '".$this->message['to']."'");
            if(DataBaseManager::numRows($result) > 0){
                $result = DataBaseManager::fetchArray($result);
                $this->message['to'] = $result[0];
                return true;
            }else{
                return false;
            }
        }
    }
    protected function validateMessage()
    {
        $this->message['subject'] = Parser::characterTraductor($this->message['subject']);
        $this->message['body'] = Parser::characterTraductor($this->message['body']);
    }
    protected function sendMessage()
    {
        DataBaseManager::query("INSERT INTO {private_messages} (origin_player, destination_player, subject, body, send_time, language) 
        VALUES (".User::get_idUser().",".$this->message['to'].",'".$this->message['subject']."','".$this->message['body']."',".time().",
        '".$this->message['language']."')");
    }
}