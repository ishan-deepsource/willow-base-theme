<?php

namespace Bonnier\Willow\Base\Models\ACF\Fields;

use Bonnier\Willow\Base\Models\ACF\ACFField;

class MarkdownField extends ACFField
{
    public const TYPE = 'markdown-editor';

    public const CONFIG_STANDARD = 'standard';
    public const CONFIG_SIMPLE = 'simple';

    /** @var string  */
    private $mdeConfig = self::CONFIG_STANDARD;
    /** @var int  */
    private $fontSize = 14;

    /**
     * @param string $mdeConfig
     * @return MarkdownField
     */
    public function setMdeConfig(string $mdeConfig): MarkdownField
    {
        if (!in_array($mdeConfig, [self::CONFIG_STANDARD, self::CONFIG_SIMPLE])) {
            throw new \InvalidArgumentException(sprintf('\'%s\' is an invalid Simple MDE Configuration'));
        }
        $this->mdeConfig = $mdeConfig;
        return $this;
    }

    /**
     * @param int $fontSize
     * @return MarkdownField
     */
    public function setFontSize(int $fontSize): MarkdownField
    {
        $this->fontSize = $fontSize;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'simple_mde_config' => $this->mdeConfig,
            'font_size' => $this->fontSize,
        ]);
    }
}
