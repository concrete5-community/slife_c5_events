<?php

namespace SlifeC5Events\Event;

use Slife\Integration\BasicEvent;
use Slife\Utility\Slack;

class OnUserChangePassword extends BasicEvent
{
    public function install()
    {
        $this->getOrCreateEvent();
        $this->getOrCreatePlaceholders([
            'user_name',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultMessage()
    {
        return t("The password of *{user_name}* has just changed.");
    }

    /**
     * @param \Concrete\Core\User\Event\UserInfoWithPassword $event
     * @param string $message
     *
     * @return string
     */
    protected function replaceUserName(\Concrete\Core\User\Event\UserInfoWithPassword $event, $message)
    {
        $ui = $event->getUserInfoObject();

        $link = BASE_URL . '/index.php/dashboard/users/search/view/' . $ui->getUserID();
        $sh = $this->app->make(Slack::class);
        $userName = $sh->makeLink($link, $ui->getUserName());

        return str_replace('{user_name}', $userName, $message);
    }
}
