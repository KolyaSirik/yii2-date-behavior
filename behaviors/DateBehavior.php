<?php

namespace kolyasiryk\yii2behavior\behaviors;

use yii\base\Behavior;
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
    /**
     * @var array
     */
    public $attributes = [];

    /**
     * @var Formatter
     */
    public $formatter;

    /**
     * @var string
     */
    public $dateSaveFormat = 'php:Y-m-d';

    /**
     * @var string
     */
    public $datetimeSaveFormat = 'php:Y-m-d H:i:s';

    /**
     * @var string
     */
    public $timeSaveFormat = 'php:H:i:s';

    /**
     * @var string
     */
    public $dateDisplayFormat = 'php:Y-m-d';

    /**
     * @var string
     */
    public $datetimeDisplayFormat = 'php:Y-m-d H:i:s';

    /**
     * @var string
     */
    public $timeDisplayFormat = 'php:H:i:s';

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
        $this->updateAttributes($this->saveFormats());
    }

    /**
     * After find event
     *
     * @return void
     */
    public function onAfterFind()
    {
        $this->updateAttributes($this->displayFormats());
    }

    /**
     * Update attributes
     *
     * @param array $formats
     */
    protected function updateAttributes($formats)
    {
        foreach ($this->attributes as $attribute => $format) {
            if ($value = $this->owner->getAttribute($attribute)) {
                if (!$dateFormat = ArrayHelper::getValue($formats, $format)) {
                    throw new InvalidParamException('$format has incorrect value');
                }

                if ($dateFormat === 'U') {
                    $value = (int) $this->formatter->asTimestamp($value);
                } else {
                    $value = $this->formatter->format($value, [$format, $dateFormat]);
                }

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
