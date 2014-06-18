<?php
/**
 * Yandex PHP Library
 *
 * @copyright NIX Solutions Ltd.
 * @link https://github.com/nixsolutions/yandex-php-library
 */

/**
 * @namespace
 */
namespace Yandex\Common;

use Yandex\Common\Exception\InvalidSettingsYandexException;
use Yandex\Common\Exception\RealizationYandexException;

/**
 * Package
 *
 * @category Yandex
 * @package  Common
 *
 * @author   Anton Shevchuk
 * @created  07.08.13 10:12
 */
abstract class AbstractPackage
{
    /**
     * __set
     *
     * @param string $key
     * @param mixed $value
     * @throws Exception\RealizationYandexException
     * @throws Exception\InvalidSettingsYandexException
     * @return self
     */
    public function __set($key, $value)
    {
        $method = 'set' . ucfirst($key);

        if (method_exists($this, $method)) {
            $this->$method($value);
        } elseif (property_exists($this, $key)) {
            throw new RealizationYandexException("Property `$key` required realization setter method `$method`");
        } else {
            throw new InvalidSettingsYandexException("Configuration option `$key`` is undefined");
        }
        return $this;
    }

    /**
     * __get
     *
     * @param string $key
     * @throws Exception\RealizationYandexException
     * @throws Exception\InvalidSettingsYandexException
     * @return self
     */
    public function __get($key)
    {
        $method = 'get' . ucfirst($key);

        if (method_exists($this, $method)) {
            return $this->$method($key);
        } elseif (property_exists($this, $key)) {
            throw new RealizationYandexException("Property `$key` required realization getter method `$method`");
        } else {
            throw new InvalidSettingsYandexException("Configuration option '$key' is undefined");
        }
    }

    /**
     * @param array $options
     * @return void
     */
    public function setSettings(array $options)
    {
        // apply options
        foreach ($options as $key => $value) {
            $key = $this->normalizeKey($key);
            $this->$key = $value;
        }
    }

    /**
     * checkOptions
     *
     * @throws Exception\InvalidSettingsYandexException
     * @return void
     */
    public function checkSettings()
    {
        if (!$this->doCheckSettings()) {
            throw new InvalidSettingsYandexException("Invalid configuration options of '".get_class($this)."' package");
        }
    }

    /**
     * Check package configuration
     *
     * @return boolean
     */
    abstract protected function doCheckSettings();

    /**
     * @param string $key
     * @return string
     */
    private function normalizeKey($key)
    {
        $option = str_replace('_', ' ', strtolower($key));
        $option = str_replace(' ', '', ucwords($option));
        return $option;
    }
}
