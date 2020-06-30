<?php

namespace Bonnier\Willow\Base\Models\ACF\Fields;

use Bonnier\Willow\Base\Models\ACF\ACFField;

class MessageField extends ACFField
{
    public const TYPE = 'message';

    /** @var string */
    private $message = '<hr>';
    /** @var string */
    private $newLines = 'wpautop';
    /** @var bool  */
    private $escapeHTML = false;

    /**
     * @param string $message
     * @return MessageField
     */
    public function setMessage(string $message): MessageField
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @param string $newLines
     * @return MessageField
     */
    public function setNewLines(string $newLines): MessageField
    {
        $this->newLines = $newLines;
        return $this;
    }

    /**
     * @param bool $escapeHTML
     * @return MessageField
     */
    public function setEscapeHTML(bool $escapeHTML): MessageField
    {
        $this->escapeHTML = $escapeHTML;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'message' => $this->message,
            'new_lines' => $this->newLines,
            'esc_html' => $this->escapeHTML ? 1 : 0,
        ]);
    }
}
