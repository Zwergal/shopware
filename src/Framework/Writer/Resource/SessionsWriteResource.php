<?php declare(strict_types=1);

namespace Shopware\Framework\Writer\Resource;

use Shopware\Context\Struct\TranslationContext;
use Shopware\Framework\Event\SessionsWrittenEvent;
use Shopware\Framework\Write\Field\IntField;
use Shopware\Framework\Write\Field\StringField;
use Shopware\Framework\Write\Flag\Required;
use Shopware\Framework\Write\WriteResource;

class SessionsWriteResource extends WriteResource
{
    protected const SESS_ID_FIELD = 'sessId';
    protected const SESS_TIME_FIELD = 'sessTime';
    protected const SESS_LIFETIME_FIELD = 'sessLifetime';

    public function __construct()
    {
        parent::__construct('sessions');

        $this->primaryKeyFields[self::SESS_ID_FIELD] = (new StringField('sess_id'))->setFlags(new Required());
        $this->fields[self::SESS_TIME_FIELD] = (new IntField('sess_time'))->setFlags(new Required());
        $this->fields[self::SESS_LIFETIME_FIELD] = (new IntField('sess_lifetime'))->setFlags(new Required());
    }

    public function getWriteOrder(): array
    {
        return [
            self::class,
        ];
    }

    public static function createWrittenEvent(array $updates, TranslationContext $context, array $rawData = [], array $errors = []): SessionsWrittenEvent
    {
        $event = new SessionsWrittenEvent($updates[self::class] ?? [], $context, $rawData, $errors);

        unset($updates[self::class]);

        if (!empty($updates[self::class])) {
            $event->addEvent(self::createWrittenEvent($updates, $context));
        }

        return $event;
    }
}
