<?php
require_once APP.'logic/tools/Parser.php';

/**
 *
 * This class loads messages to the theme, the messages primary key (message_id) starts
 * at 100 if $id is lower inicates a page it shows 20 messages
 *
 */
class Messages
{
    public static function getMessages($id,$which = NULL)
    {
        if(Parser::onlyNumbersString($id)){
            if(id < 100){
                return getMessageRange($id, $which);
            }else{
                return getMessage($id);
            }
        }
    }
    private static function getMessage($id)
    {
        $result = DataBaseManager::fetchArray(DataBaseManager::query("SELECT * FROM {messages} WHERE message_id = '$id' and
         origin_player =".User::get_idUser(). " or destination_player = ".User::get_idUser()));
        return self::mountMessage($result);
    }
    private static function getMessagesRange($page,$which)
    {
        $start = $page * 20;
        if($which == "OUT"){
            $select = DataBaseManager::query("SELECT * FROM {messages} WHERE origin_player =".User::get_idUser()." limit $start,20");
            while($result = DataBaseManager::fetchArray("$select")){
                $messages['OUT'][] = self::mountMessage($select);
            }
        }else{
            $select = DataBaseManager::query("SELECT * FROM {messages} WHERE destination_player =".User::get_idUser()." limit $start,20");
            while($result = DataBaseManager::fetchArray("$select")){
                $messages['IN'][] = self::mountMessage($select);
            }
        }
        return $messages;
    }
    private static function mountMessage($result)
    {
        $message = array();
        $message['BODY'] = $result['body'];
        $message['SUBJECT'] = $result['subject'];
        $message['TO'] = $result['destination_player'];
        $message['FROM'] = $result['origin_player'];
        $message['READ'] = $result['read'];
        $message['ORIGINAL_LANGUAGE'] = $result['original_language'];
        $message['TIME'][0] = $result['send_time'];
        $message['TIME'][1] = Timer::uNIXToDate( $message['TIME'][0]);
        return $message;
    }
}