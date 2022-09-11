<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: gtfs-realtime.proto

namespace Transit_realtime;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * A definition (or update) of an entity in the transit feed.
 *
 * Generated from protobuf message <code>transit_realtime.FeedEntity</code>
 */
class FeedEntity extends \Google\Protobuf\Internal\Message
{
    /**
     * The ids are used only to provide incrementality support. The id should be
     * unique within a FeedMessage. Consequent FeedMessages may contain
     * FeedEntities with the same id. In case of a DIFFERENTIAL update the new
     * FeedEntity with some id will replace the old FeedEntity with the same id
     * (or delete it - see is_deleted below).
     * The actual GTFS entities (e.g. stations, routes, trips) referenced by the
     * feed must be specified by explicit selectors (see EntitySelector below for
     * more info).
     * required string id = 1;
     *
     * Generated from protobuf field <code>string id = 1;</code>
     */
    protected $id = '';
    /**
     * Whether this entity is to be deleted. Relevant only for incremental
     * fetches.
     * optional bool is_deleted = 2 [default = false];
     *
     * Generated from protobuf field <code>bool is_deleted = 2;</code>
     */
    protected $is_deleted = false;
    /**
     * Data about the entity itself. Exactly one of the following fields must be
     * present (unless the entity is being deleted).
     * optional TripUpdate trip_update = 3;
     *
     * Generated from protobuf field <code>.transit_realtime.TripUpdate trip_update = 3;</code>
     */
    protected $trip_update = null;
    /**
     * optional VehiclePosition vehicle = 4;
     *
     * Generated from protobuf field <code>.transit_realtime.VehiclePosition vehicle = 4;</code>
     */
    protected $vehicle = null;
    /**
     * optional Alert alert = 5;
     *
     * Generated from protobuf field <code>.transit_realtime.Alert alert = 5;</code>
     */
    protected $alert = null;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $id
     *           The ids are used only to provide incrementality support. The id should be
     *           unique within a FeedMessage. Consequent FeedMessages may contain
     *           FeedEntities with the same id. In case of a DIFFERENTIAL update the new
     *           FeedEntity with some id will replace the old FeedEntity with the same id
     *           (or delete it - see is_deleted below).
     *           The actual GTFS entities (e.g. stations, routes, trips) referenced by the
     *           feed must be specified by explicit selectors (see EntitySelector below for
     *           more info).
     *           required string id = 1;
     *     @type bool $is_deleted
     *           Whether this entity is to be deleted. Relevant only for incremental
     *           fetches.
     *           optional bool is_deleted = 2 [default = false];
     *     @type \Transit_realtime\TripUpdate $trip_update
     *           Data about the entity itself. Exactly one of the following fields must be
     *           present (unless the entity is being deleted).
     *           optional TripUpdate trip_update = 3;
     *     @type \Transit_realtime\VehiclePosition $vehicle
     *           optional VehiclePosition vehicle = 4;
     *     @type \Transit_realtime\Alert $alert
     *           optional Alert alert = 5;
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\GtfsRealtime::initOnce();
        parent::__construct($data);
    }

    /**
     * The ids are used only to provide incrementality support. The id should be
     * unique within a FeedMessage. Consequent FeedMessages may contain
     * FeedEntities with the same id. In case of a DIFFERENTIAL update the new
     * FeedEntity with some id will replace the old FeedEntity with the same id
     * (or delete it - see is_deleted below).
     * The actual GTFS entities (e.g. stations, routes, trips) referenced by the
     * feed must be specified by explicit selectors (see EntitySelector below for
     * more info).
     * required string id = 1;
     *
     * Generated from protobuf field <code>string id = 1;</code>
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * The ids are used only to provide incrementality support. The id should be
     * unique within a FeedMessage. Consequent FeedMessages may contain
     * FeedEntities with the same id. In case of a DIFFERENTIAL update the new
     * FeedEntity with some id will replace the old FeedEntity with the same id
     * (or delete it - see is_deleted below).
     * The actual GTFS entities (e.g. stations, routes, trips) referenced by the
     * feed must be specified by explicit selectors (see EntitySelector below for
     * more info).
     * required string id = 1;
     *
     * Generated from protobuf field <code>string id = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setId($var)
    {
        GPBUtil::checkString($var, True);
        $this->id = $var;

        return $this;
    }

    /**
     * Whether this entity is to be deleted. Relevant only for incremental
     * fetches.
     * optional bool is_deleted = 2 [default = false];
     *
     * Generated from protobuf field <code>bool is_deleted = 2;</code>
     * @return bool
     */
    public function getIsDeleted()
    {
        return $this->is_deleted;
    }

    /**
     * Whether this entity is to be deleted. Relevant only for incremental
     * fetches.
     * optional bool is_deleted = 2 [default = false];
     *
     * Generated from protobuf field <code>bool is_deleted = 2;</code>
     * @param bool $var
     * @return $this
     */
    public function setIsDeleted($var)
    {
        GPBUtil::checkBool($var);
        $this->is_deleted = $var;

        return $this;
    }

    /**
     * Data about the entity itself. Exactly one of the following fields must be
     * present (unless the entity is being deleted).
     * optional TripUpdate trip_update = 3;
     *
     * Generated from protobuf field <code>.transit_realtime.TripUpdate trip_update = 3;</code>
     * @return \Transit_realtime\TripUpdate|null
     */
    public function getTripUpdate()
    {
        return $this->trip_update;
    }

    public function hasTripUpdate()
    {
        return isset($this->trip_update);
    }

    public function clearTripUpdate()
    {
        unset($this->trip_update);
    }

    /**
     * Data about the entity itself. Exactly one of the following fields must be
     * present (unless the entity is being deleted).
     * optional TripUpdate trip_update = 3;
     *
     * Generated from protobuf field <code>.transit_realtime.TripUpdate trip_update = 3;</code>
     * @param \Transit_realtime\TripUpdate $var
     * @return $this
     */
    public function setTripUpdate($var)
    {
        GPBUtil::checkMessage($var, \Transit_realtime\TripUpdate::class);
        $this->trip_update = $var;

        return $this;
    }

    /**
     * optional VehiclePosition vehicle = 4;
     *
     * Generated from protobuf field <code>.transit_realtime.VehiclePosition vehicle = 4;</code>
     * @return \Transit_realtime\VehiclePosition|null
     */
    public function getVehicle()
    {
        return $this->vehicle;
    }

    public function hasVehicle()
    {
        return isset($this->vehicle);
    }

    public function clearVehicle()
    {
        unset($this->vehicle);
    }

    /**
     * optional VehiclePosition vehicle = 4;
     *
     * Generated from protobuf field <code>.transit_realtime.VehiclePosition vehicle = 4;</code>
     * @param \Transit_realtime\VehiclePosition $var
     * @return $this
     */
    public function setVehicle($var)
    {
        GPBUtil::checkMessage($var, \Transit_realtime\VehiclePosition::class);
        $this->vehicle = $var;

        return $this;
    }

    /**
     * optional Alert alert = 5;
     *
     * Generated from protobuf field <code>.transit_realtime.Alert alert = 5;</code>
     * @return \Transit_realtime\Alert|null
     */
    public function getAlert()
    {
        return $this->alert;
    }

    public function hasAlert()
    {
        return isset($this->alert);
    }

    public function clearAlert()
    {
        unset($this->alert);
    }

    /**
     * optional Alert alert = 5;
     *
     * Generated from protobuf field <code>.transit_realtime.Alert alert = 5;</code>
     * @param \Transit_realtime\Alert $var
     * @return $this
     */
    public function setAlert($var)
    {
        GPBUtil::checkMessage($var, \Transit_realtime\Alert::class);
        $this->alert = $var;

        return $this;
    }

}

