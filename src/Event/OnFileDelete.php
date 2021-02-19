<?php

namespace SlifeC5Events\Event;

use Slife\Integration\BasicEvent;

class OnFileDelete extends BasicEvent
{
    public function install()
    {
        $this->getOrCreateEvent();
        $this->getOrCreatePlaceholders([
            'file_name',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultMessage()
    {
        return t("File *{file_name}* has been deleted.");
    }

    /**
     * @param \Concrete\Core\File\Event\DeleteFile $event
     * @param string $message
     *
     * @return string
     */
    protected function replaceFileName(\Concrete\Core\File\Event\DeleteFile $event, $message)
    {
        $file = $event->getFileObject();
        $fv = $file->getVersion();

        return str_replace('{file_name}', $fv->getFileName(), $message);
    }
}
