<?php

namespace SlifeC5Events\Event;

use Concrete\Core\User\User;
use Slife\Integration\BasicEvent;
use Slife\Utility\Slack;

class OnPageTypePublish extends BasicEvent
{
    public function install()
    {
        $this->getOrCreateEvent();
        $this->getOrCreatePlaceholders([
            'page_name',
            'user_name',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultMessage()
    {
        return t("Page *{page_name}* has been added by user *{user_name}*.");
    }

    /**
     * @param \Concrete\Core\Page\Type\Event $event
     * @param string $message
     *
     * @return string
     */
    protected function replacePageName(\Concrete\Core\Page\Type\Event $event, $message)
    {
        $page = $event->getPageObject();

        $sh = $this->app->make(Slack::class);
        $link = $sh->makeLink($page->getCollectionLink(true), $page->getCollectionName());

        return str_replace('{page_name}', $link, $message);
    }

    /**
     * @param \Concrete\Core\Page\Type\Event $event
     * @param string $message
     *
     * @return string
     */
    protected function replaceUserName(\Concrete\Core\Page\Type\Event $event, $message)
    {
        /**
         * @var User $user
         *
         * If this event is triggered programmatically, the user is null.
         */
        $user = $event->getUserObject();
        if ($user) {
            $link = BASE_URL . '/index.php/dashboard/users/search/view/' . $user->getUserID();

            $sh = $this->app->make(Slack::class);
            $userName = $sh->makeLink($link, $user->getUserName());
        } else {
            $userName = t('Unknown');
        }

        return str_replace('{user_name}', $userName, $message);
    }
}
