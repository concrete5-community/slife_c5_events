<?php

namespace SlifeC5Events\Event;

use Slife\Integration\BasicEvent;

class OnPageDelete extends BasicEvent
{
    public function install()
    {
        $this->getOrCreateEvent();
        $this->getOrCreatePlaceholders([
            'page_name',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultMessage()
    {
        return t("Page *{page_name}* has been deleted.");
    }

    /**
     * @param \Concrete\Core\Page\DeletePageEvent $event
     * @param string $message
     *
     * @return string
     */
    protected function replacePageName(\Concrete\Core\Page\DeletePageEvent $event, $message)
    {
        $page = $event->getPageObject();

        return str_replace('{page_name}', $page->getCollectionName(), $message);
    }
}
