<?php

namespace Nanicas\LegacyLaravelToolkit\Exceptions;

use Nanicas\LegacyLaravelToolkit\Helpers\Helper as InternalHelper;

class_alias(InternalHelper::readTemplateConfig()['helpers']['global'], __NAMESPACE__ . '\CVExxHelperAlias');

class CustomValidatorException extends \Exception
{
    private bool $translated = false;

    /**
     * @param bool $value
     */
    protected function translated(bool $value)
    {
        $this->translated = $value;
    }

    /**
     * @param string $message
     * @param int $code
     * @param \Throwable $previous
     */
    public function __construct(
        string $message = "",
        int $code = 0,
        \Throwable $previous = null
    ) {
        $packaged = false;

        if (!$this->translated) {
            $message = CVExxHelperAlias::view(
                'components.validator-messages',
                ['messages' => [$message]],
                $packaged
            )->render();
        }

        parent::__construct($message, $code, $previous);
    }
}
