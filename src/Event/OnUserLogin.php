<?php

namespace SlifeC5Events\Event;

use Slife\Integration\BasicEvent;
use Slife\Utility\Slack;

class OnUserLogin extends BasicEvent
{
    public function install()
    {
        $this->getOrCreateEvent();
        $this->getOrCreatePlaceholders([
            'user_name',
            'ip',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultMessage()
    {
        return t("User *{user_name}* just logged in via IP {ip}.");
    }

    /**
     * @param \Concrete\Core\User\Event\User $event
     * @param string $message
     *
     * @return string
     */
    protected function replaceUserName(\Concrete\Core\User\Event\User $event, $message)
    {
        $u = $event->getUserObject();

        $link = BASE_URL . '/index.php/dashboard/users/search/view/' . $u->getUserID();
        $sh = $this->app->make(Slack::class);
        $userName = $sh->makeLink($link, $u->getUserName());

        return str_replace('{user_name}', $userName, $message);
    }

    /**
     * @param \Concrete\Core\User\Event\User $event
     * @param string $message
     *
     * @return string
     */
    protected function replaceIp(\Concrete\Core\User\Event\User $event, $message)
    {
        $u = $event->getUserObject();
        $ui = $u->getUserInfoObject();

        $ipAddress = $ui->getLastIPAddress();
        $ipAddress = $ipAddress ? $ipAddress : t('Unknown');

        return str_replace('{ip}', $ipAddress, $message);
    }
}
