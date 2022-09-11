<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: gtfs-realtime.proto

namespace Transit_realtime;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * A position.
 *
 * Generated from protobuf message <code>transit_realtime.Position</code>
 */
class Position extends \Google\Protobuf\Internal\Message
{
    /**
     * Degrees North, in the WGS-84 coordinate system.
     * required float latitude = 1;
     *
     * Generated from protobuf field <code>float latitude = 1;</code>
     */
    protected $latitude = 0.0;
    /**
     * Degrees East, in the WGS-84 coordinate system.
     * required float longitude = 2;
     *
     * Generated from protobuf field <code>float longitude = 2;</code>
     */
    protected $longitude = 0.0;
    /**
     * Bearing, in degrees, clockwise from North, i.e., 0 is North and 90 is East.
     * This can be the compass bearing, or the direction towards the next stop
     * or intermediate location.
     * This should not be direction deduced from the sequence of previous
     * positions, which can be computed from previous data.
     * optional float bearing = 3;
     *
     * Generated from protobuf field <code>float bearing = 3;</code>
     */
    protected $bearing = 0.0;
    /**
     * Odometer value, in meters.
     * optional double odometer = 4;
     *
     * Generated from protobuf field <code>double odometer = 4;</code>
     */
    protected $odometer = 0.0;
    /**
     * Momentary speed measured by the vehicle, in meters per second.
     * optional float speed = 5;
     *
     * Generated from protobuf field <code>float speed = 5;</code>
     */
    protected $speed = 0.0;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type float $latitude
     *           Degrees North, in the WGS-84 coordinate system.
     *           required float latitude = 1;
     *     @type float $longitude
     *           Degrees East, in the WGS-84 coordinate system.
     *           required float longitude = 2;
     *     @type float $bearing
     *           Bearing, in degrees, clockwise from North, i.e., 0 is North and 90 is East.
     *           This can be the compass bearing, or the direction towards the next stop
     *           or intermediate location.
     *           This should not be direction deduced from the sequence of previous
     *           positions, which can be computed from previous data.
     *           optional float bearing = 3;
     *     @type float $odometer
     *           Odometer value, in meters.
     *           optional double odometer = 4;
     *     @type float $speed
     *           Momentary speed measured by the vehicle, in meters per second.
     *           optional float speed = 5;
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\GtfsRealtime::initOnce();
        parent::__construct($data);
    }

    /**
     * Degrees North, in the WGS-84 coordinate system.
     * required float latitude = 1;
     *
     * Generated from protobuf field <code>float latitude = 1;</code>
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Degrees North, in the WGS-84 coordinate system.
     * required float latitude = 1;
     *
     * Generated from protobuf field <code>float latitude = 1;</code>
     * @param float $var
     * @return $this
     */
    public function setLatitude($var)
    {
        GPBUtil::checkFloat($var);
        $this->latitude = $var;

        return $this;
    }

    /**
     * Degrees East, in the WGS-84 coordinate system.
     * required float longitude = 2;
     *
     * Generated from protobuf field <code>float longitude = 2;</code>
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Degrees East, in the WGS-84 coordinate system.
     * required float longitude = 2;
     *
     * Generated from protobuf field <code>float longitude = 2;</code>
     * @param float $var
     * @return $this
     */
    public function setLongitude($var)
    {
        GPBUtil::checkFloat($var);
        $this->longitude = $var;

        return $this;
    }

    /**
     * Bearing, in degrees, clockwise from North, i.e., 0 is North and 90 is East.
     * This can be the compass bearing, or the direction towards the next stop
     * or intermediate location.
     * This should not be direction deduced from the sequence of previous
     * positions, which can be computed from previous data.
     * optional float bearing = 3;
     *
     * Generated from protobuf field <code>float bearing = 3;</code>
     * @return float
     */
    public function getBearing()
    {
        return $this->bearing;
    }

    /**
     * Bearing, in degrees, clockwise from North, i.e., 0 is North and 90 is East.
     * This can be the compass bearing, or the direction towards the next stop
     * or intermediate location.
     * This should not be direction deduced from the sequence of previous
     * positions, which can be computed from previous data.
     * optional float bearing = 3;
     *
     * Generated from protobuf field <code>float bearing = 3;</code>
     * @param float $var
     * @return $this
     */
    public function setBearing($var)
    {
        GPBUtil::checkFloat($var);
        $this->bearing = $var;

        return $this;
    }

    /**
     * Odometer value, in meters.
     * optional double odometer = 4;
     *
     * Generated from protobuf field <code>double odometer = 4;</code>
     * @return float
     */
    public function getOdometer()
    {
        return $this->odometer;
    }

    /**
     * Odometer value, in meters.
     * optional double odometer = 4;
     *
     * Generated from protobuf field <code>double odometer = 4;</code>
     * @param float $var
     * @return $this
     */
    public function setOdometer($var)
    {
        GPBUtil::checkDouble($var);
        $this->odometer = $var;

        return $this;
    }

    /**
     * Momentary speed measured by the vehicle, in meters per second.
     * optional float speed = 5;
     *
     * Generated from protobuf field <code>float speed = 5;</code>
     * @return float
     */
    public function getSpeed()
    {
        return $this->speed;
    }

    /**
     * Momentary speed measured by the vehicle, in meters per second.
     * optional float speed = 5;
     *
     * Generated from protobuf field <code>float speed = 5;</code>
     * @param float $var
     * @return $this
     */
    public function setSpeed($var)
    {
        GPBUtil::checkFloat($var);
        $this->speed = $var;

        return $this;
    }

}

