<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a type mapper that can go back and forth between provider types and PHP types
 */
namespace RDev\Models\Databases\SQL\Providers;

class TypeMapper
{
    /**
     * Converts an SQL boolean to a PHP boolean
     *
     * @param Provider $provider The provider to convert from
     * @param mixed $sqlBoolean The boolean to convert
     * @return bool The PHP boolean
     */
    public function fromSQLBoolean(Provider $provider, $sqlBoolean)
    {
        return $provider->getTrueBooleanFormat() === $sqlBoolean;
    }

    /**
     * Converts an SQL date to a PHP date time
     *
     * @param Provider $provider The provider to convert from
     * @param string $sqlDate The date to convert
     * @return \DateTime|null The PHP date time
     */
    public function fromSQLDate(Provider $provider, $sqlDate)
    {
        if($sqlDate === null)
        {
            return null;
        }

        return \DateTime::createFromFormat($provider->getDateFormat(), $sqlDate, new \DateTimeZone("UTC"));
    }

    /**
     * Converts an SQL time with time zone to a PHP date time
     *
     * @param Provider $provider The provider to convert from
     * @param string $sqlTime The time to convert
     * @return \DateTime|null The PHP date time
     */
    public function fromSQLTimeWithTimeZone(Provider $provider, $sqlTime)
    {
        if($sqlTime === null)
        {
            return null;
        }

        return \DateTime::createFromFormat($provider->getTimeWithTimeZoneFormat(), $sqlTime, new \DateTimeZone("UTC"));
    }

    /**
     * Converts an SQL time without time zone to a PHP date time
     *
     * @param Provider $provider The provider to convert from
     * @param string $sqlTime The time to convert
     * @return \DateTime|null The PHP date time
     */
    public function fromSQLTimeWithoutTimeZone(Provider $provider, $sqlTime)
    {
        if($sqlTime === null)
        {
            return null;
        }

        return \DateTime::createFromFormat($provider->getTimeWithoutTimeZoneFormat(), $sqlTime, new \DateTimeZone("UTC"));
    }

    /**
     * Converts an SQL timestamp without time zone to a PHP date time
     *
     * @param Provider $provider The provider to convert from
     * @param string $sqlTimestamp The timestamp without time zone to convert
     * @return \DateTime|null The PHP date time
     */
    public function fromSQLTimestampWithOutTimeZone(Provider $provider, $sqlTimestamp)
    {
        if($sqlTimestamp === null)
        {
            return null;
        }

        return \DateTime::createFromFormat($provider->getTimestampWithoutTimeZoneFormat(), $sqlTimestamp,
            new \DateTimeZone("UTC"));
    }

    /**
     * Converts an SQL timestamp with time zone to a PHP date time
     *
     * @param Provider $provider The provider to convert from
     * @param string $sqlTimestamp The timestamp with time zone to convert
     * @return \DateTime|null The PHP date time
     */
    public function fromSQLTimestampWithTimeZone(Provider $provider, $sqlTimestamp)
    {
        if($sqlTimestamp === null)
        {
            return null;
        }

        return \DateTime::createFromFormat($provider->getTimestampWithTimeZoneFormat(),
            $sqlTimestamp, new \DateTimeZone("UTC"));
    }

    /**
     * Converts a PHP boolean to an SQL boolean
     *
     * @param Provider $provider The provider to convert to
     * @param bool $boolean The boolean to convert
     * @return mixed The SQL boolean suitable for database storage
     */
    public function toSQLBoolean(Provider $provider, $boolean)
    {
        if($boolean)
        {
            return $provider->getTrueBooleanFormat();
        }
        else
        {
            return $provider->getFalseBooleanFormat();
        }
    }

    /**
     * Converts a PHP date time to an SQL date
     *
     * @param Provider $provider The provider to convert to
     * @param \DateTime $date The date time to convert
     * @return string The SQL date suitable for database storage
     */
    public function toSQLDate(Provider $provider, \DateTime $date)
    {
        return $date->format($provider->getDateFormat());
    }

    /**
     * Converts a PHP date time with time zone to an SQL time
     *
     * @param Provider $provider The provider to convert to
     * @param \DateTime $time The date time to convert
     * @return string The SQL time suitable for database storage
     */
    public function toSQLTimeWithTimeZone(Provider $provider, \DateTime $time)
    {
        return $time->format($provider->getTimeWithTimeZoneFormat());
    }

    /**
     * Converts a PHP date time without time zone to an SQL time
     *
     * @param Provider $provider The provider to convert to
     * @param \DateTime $time The date time to convert
     * @return string The SQL time suitable for database storage
     */
    public function toSQLTimeWithoutTimeZone(Provider $provider, \DateTime $time)
    {
        return $time->format($provider->getTimeWithoutTimeZoneFormat());
    }

    /**
     * Converts a PHP date time to an SQL timestamp with time zone
     *
     * @param Provider $provider The provider to convert to
     * @param \DateTime $timestamp The date time to convert
     * @return string The SQL timestamp with time zone suitable for database storage
     */
    public function toSQLTimestampWithTimeZone(Provider $provider, \DateTime $timestamp)
    {
        return $timestamp->format($provider->getTimestampWithTimeZoneFormat());
    }

    /**
     * Converts a PHP date time to an SQL timestamp without time zone
     *
     * @param Provider $provider The provider to convert to
     * @param \DateTime $timestamp The date time to convert
     * @return string The SQL timestamp without time zone suitable for database storage
     */
    public function toSQLTimestampWithoutTimeZone(Provider $provider, \DateTime $timestamp)
    {
        return $timestamp->format($provider->getTimestampWithoutTimeZoneFormat());
    }
} 