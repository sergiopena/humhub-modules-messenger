<?php

namespace humhub\modules\chat;

class Module extends \humhub\components\Module
{

    public function enable()
    {
        parent::enable();

        // Создаем файл конфигурации для сервера
        $configContent = file_get_contents($this->getBasePath() . '/server/config.js.exempl');
        $params = require($this->getBasePath() . '/params.php');
        $replace = [
            '@|db_host|@' => preg_replace('/^.+?host=([^;]+);.*$/', "$1", \Yii::$app->db->dsn),
            '@|db_username|@' => \Yii::$app->db->username,
            '@|db_password|@' => \Yii::$app->db->password,
            '@|db_name|@' => preg_replace('/^.+?dbname=(.+)$/', "$1", \Yii::$app->db->dsn),
            '@|port|@' => $params['node_server_port'],
        ];
        $configContent = str_replace(array_keys($replace), array_values($replace), $configContent);
        file_put_contents($this->getBasePath() . '/server/config.js', $configContent);

        return true;
    }

    public static function onTopMenuInit($event)
    {
        if (\Yii::$app->user->isGuest) {
            return;
        }

        Assets::register($event->sender->view);
        $params = require(__DIR__ . '/params.php');
        $script = "if (typeof Chat == 'function') var chat = new Chat(document.location.origin+':" . $params['node_server_port'] . "');";
        $event->sender->view->registerJs($script, \yii\web\View::POS_READY);
    }

}
