<?php

namespace kolyasiryk\yii2behavior\behaviors;

use yii\base\Behavior;
use yii\base\Exception;
use yii\base\InvalidParamException;
use yii\db\BaseActiveRecord;
use yii\helpers\ArrayHelper;
use yii\i18n\Formatter;

/**
 * Class DateBehavior
 *
 * @package kolyasiryk\behaviors
 * @property BaseActiveRecord $owner
 */
class DateBehavior extends Behavior
{
    const EVENT_SET = 'eventSet';
    const EVENT_GET = 'eventGet';

    /** @var array */
    public $attributes = [];

    /** @var Formatter */
    public $formatter;

    /** @var string */
    public $dateSaveFormat = 'Y-m-d';

    /** @var string */
    public $datetimeSaveFormat = 'Y-m-d H:i:s';

    /** @var string */
    public $timeSaveFormat = 'H:i:s';

    /** @var string */
    public $dateDisplayFormat = 'Y-m-d';

    /** @var string */
    public $datetimeDisplayFormat = 'Y-m-d H:i:s';

    /** @var string */
    public $timeDisplayFormat = 'H:i:s';

    /** @var string */
    public $userTimezone;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (is_null($this->formatter)) {
            $this->formatter = \Yii::$app->formatter;
        } elseif (is_array($this->formatter)) {
            $this->formatter = \Yii::createObject($this->formatter);
        }

        $this->userTimezone = $this->userTimezone ?? \Yii::$app->params['userTimezone'] ?? 'GMT';
    }

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            BaseActiveRecord::EVENT_BEFORE_INSERT => 'onBeforeSave',
            BaseActiveRecord::EVENT_BEFORE_UPDATE => 'onBeforeSave',
            BaseActiveRecord::EVENT_AFTER_FIND => 'onAfterFind',
        ];
    }

    /**
     * Before save event
     *
     * @return void
     */
    public function onBeforeSave()
    {
        $this->updateAttributes($this->saveFormats(), self::EVENT_SET);
    }

    /**
     * After find event
     *
     * @return void
     */
    public function onAfterFind()
    {
        $this->updateAttributes($this->displayFormats(), self::EVENT_GET);
    }

    /**
     * Update attributes
     *
     * @param array $formats
     * @param string $event
     * @throws InvalidParamException|Exception
     */
    protected function updateAttributes($formats, $event)
    {
        foreach ($this->attributes as $attribute => $format) {
            if ($value = $this->owner->getAttribute($attribute)) {
                if (!$dateFormat = ArrayHelper::getValue($formats, $format)) {
                    throw new InvalidParamException('$format has incorrect value');
                }

                $userTimezone = new \DateTimeZone($this->userTimezone);
                $gmtTimezone = new \DateTimeZone('UTC');
                $myDateTime = new \DateTime(is_int($value) ? date('Y-m-d H:i:s', $value) : $value, $gmtTimezone);

                switch ($event) {
                    case self::EVENT_GET:
                        $offset = $userTimezone->getOffset($myDateTime);
                        break;
                    case self::EVENT_SET:
                        $offset = -1 * $userTimezone->getOffset($myDateTime);
                        break;
                    default:
                        throw new Exception('Unknown event');
                }

                $myInterval = \DateInterval::createFromDateString((string)$offset . 'seconds');
                $myDateTime->add($myInterval);
                $value = $myDateTime->format($dateFormat);

                $value = $dateFormat === 'U' ? (int) $value : $value;

                $this->owner->setAttribute($attribute, $value);
            }
        }
    }

    /**
     * Returns save date formats
     *
     * @return array
     */
    protected function saveFormats()
    {
        return [
            'date' => $this->dateSaveFormat,
            'datetime' => $this->datetimeSaveFormat,
            'time' => $this->timeSaveFormat,
        ];
    }

    /**
     * Returns display date formats
     *
     * @return array
     */
    protected function displayFormats()
    {
        return [
            'date' => $this->dateDisplayFormat,
            'datetime' => $this->datetimeDisplayFormat,
            'time' => $this->timeDisplayFormat,
        ];
    }
}
