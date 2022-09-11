<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: gtfs-realtime.proto

namespace Transit_realtime;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * A descriptor that identifies an instance of a GTFS trip, or all instances of
 * a trip along a route.
 * - To specify a single trip instance, the trip_id (and if necessary,
 *   start_time) is set. If route_id is also set, then it should be same as one
 *   that the given trip corresponds to.
 * - To specify all the trips along a given route, only the route_id should be
 *   set. Note that if the trip_id is not known, then stop sequence ids in
 *   TripUpdate are not sufficient, and stop_ids must be provided as well. In
 *   addition, absolute arrival/departure times must be provided.
 *
 * Generated from protobuf message <code>transit_realtime.TripDescriptor</code>
 */
class TripDescriptor extends \Google\Protobuf\Internal\Message
{
    /**
     * The trip_id from the GTFS feed that this selector refers to.
     * For non frequency-based trips, this field is enough to uniquely identify
     * the trip. For frequency-based trip, start_time and start_date might also be
     * necessary.
     * optional string trip_id = 1;
     *
     * Generated from protobuf field <code>string trip_id = 1;</code>
     */
    protected $trip_id = '';
    /**
     * The route_id from the GTFS that this selector refers to.
     * optional string route_id = 5;
     *
     * Generated from protobuf field <code>string route_id = 5;</code>
     */
    protected $route_id = '';
    /**
     * The direction_id from the GTFS feed trips.txt file, indicating the
     * direction of travel for trips this selector refers to. This field is
     * still experimental, and subject to change. It may be formally adopted in
     * the future.
     * optional uint32 direction_id = 6;
     *
     * Generated from protobuf field <code>uint32 direction_id = 6;</code>
     */
    protected $direction_id = 0;
    /**
     * The initially scheduled start time of this trip instance.
     * When the trip_id corresponds to a non-frequency-based trip, this field
     * should either be omitted or be equal to the value in the GTFS feed. When
     * the trip_id correponds to a frequency-based trip, the start_time must be
     * specified for trip updates and vehicle positions. If the trip corresponds
     * to exact_times=1 GTFS record, then start_time must be some multiple
     * (including zero) of headway_secs later than frequencies.txt start_time for
     * the corresponding time period. If the trip corresponds to exact_times=0,
     * then its start_time may be arbitrary, and is initially expected to be the
     * first departure of the trip. Once established, the start_time of this
     * frequency-based trip should be considered immutable, even if the first
     * departure time changes -- that time change may instead be reflected in a
     * StopTimeUpdate.
     * Format and semantics of the field is same as that of
     * GTFS/frequencies.txt/start_time, e.g., 11:15:35 or 25:15:35.
     * optional string start_time = 2;
     *
     * Generated from protobuf field <code>string start_time = 2;</code>
     */
    protected $start_time = '';
    /**
     * The scheduled start date of this trip instance.
     * Must be provided to disambiguate trips that are so late as to collide with
     * a scheduled trip on a next day. For example, for a train that departs 8:00
     * and 20:00 every day, and is 12 hours late, there would be two distinct
     * trips on the same time.
     * This field can be provided but is not mandatory for schedules in which such
     * collisions are impossible - for example, a service running on hourly
     * schedule where a vehicle that is one hour late is not considered to be
     * related to schedule anymore.
     * In YYYYMMDD format.
     * optional string start_date = 3;
     *
     * Generated from protobuf field <code>string start_date = 3;</code>
     */
    protected $start_date = '';
    /**
     * optional ScheduleRelationship schedule_relationship = 4;
     *
     * Generated from protobuf field <code>.transit_realtime.TripDescriptor.ScheduleRelationship schedule_relationship = 4;</code>
     */
    protected $schedule_relationship = 0;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $trip_id
     *           The trip_id from the GTFS feed that this selector refers to.
     *           For non frequency-based trips, this field is enough to uniquely identify
     *           the trip. For frequency-based trip, start_time and start_date might also be
     *           necessary.
     *           optional string trip_id = 1;
     *     @type string $route_id
     *           The route_id from the GTFS that this selector refers to.
     *           optional string route_id = 5;
     *     @type int $direction_id
     *           The direction_id from the GTFS feed trips.txt file, indicating the
     *           direction of travel for trips this selector refers to. This field is
     *           still experimental, and subject to change. It may be formally adopted in
     *           the future.
     *           optional uint32 direction_id = 6;
     *     @type string $start_time
     *           The initially scheduled start time of this trip instance.
     *           When the trip_id corresponds to a non-frequency-based trip, this field
     *           should either be omitted or be equal to the value in the GTFS feed. When
     *           the trip_id correponds to a frequency-based trip, the start_time must be
     *           specified for trip updates and vehicle positions. If the trip corresponds
     *           to exact_times=1 GTFS record, then start_time must be some multiple
     *           (including zero) of headway_secs later than frequencies.txt start_time for
     *           the corresponding time period. If the trip corresponds to exact_times=0,
     *           then its start_time may be arbitrary, and is initially expected to be the
     *           first departure of the trip. Once established, the start_time of this
     *           frequency-based trip should be considered immutable, even if the first
     *           departure time changes -- that time change may instead be reflected in a
     *           StopTimeUpdate.
     *           Format and semantics of the field is same as that of
     *           GTFS/frequencies.txt/start_time, e.g., 11:15:35 or 25:15:35.
     *           optional string start_time = 2;
     *     @type string $start_date
     *           The scheduled start date of this trip instance.
     *           Must be provided to disambiguate trips that are so late as to collide with
     *           a scheduled trip on a next day. For example, for a train that departs 8:00
     *           and 20:00 every day, and is 12 hours late, there would be two distinct
     *           trips on the same time.
     *           This field can be provided but is not mandatory for schedules in which such
     *           collisions are impossible - for example, a service running on hourly
     *           schedule where a vehicle that is one hour late is not considered to be
     *           related to schedule anymore.
     *           In YYYYMMDD format.
     *           optional string start_date = 3;
     *     @type int $schedule_relationship
     *           optional ScheduleRelationship schedule_relationship = 4;
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\GtfsRealtime::initOnce();
        parent::__construct($data);
    }

    /**
     * The trip_id from the GTFS feed that this selector refers to.
     * For non frequency-based trips, this field is enough to uniquely identify
     * the trip. For frequency-based trip, start_time and start_date might also be
     * necessary.
     * optional string trip_id = 1;
     *
     * Generated from protobuf field <code>string trip_id = 1;</code>
     * @return string
     */
    public function getTripId()
    {
        return $this->trip_id;
    }

    /**
     * The trip_id from the GTFS feed that this selector refers to.
     * For non frequency-based trips, this field is enough to uniquely identify
     * the trip. For frequency-based trip, start_time and start_date might also be
     * necessary.
     * optional string trip_id = 1;
     *
     * Generated from protobuf field <code>string trip_id = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setTripId($var)
    {
        GPBUtil::checkString($var, True);
        $this->trip_id = $var;

        return $this;
    }

    /**
     * The route_id from the GTFS that this selector refers to.
     * optional string route_id = 5;
     *
     * Generated from protobuf field <code>string route_id = 5;</code>
     * @return string
     */
    public function getRouteId()
    {
        return $this->route_id;
    }

    /**
     * The route_id from the GTFS that this selector refers to.
     * optional string route_id = 5;
     *
     * Generated from protobuf field <code>string route_id = 5;</code>
     * @param string $var
     * @return $this
     */
    public function setRouteId($var)
    {
        GPBUtil::checkString($var, True);
        $this->route_id = $var;

        return $this;
    }

    /**
     * The direction_id from the GTFS feed trips.txt file, indicating the
     * direction of travel for trips this selector refers to. This field is
     * still experimental, and subject to change. It may be formally adopted in
     * the future.
     * optional uint32 direction_id = 6;
     *
     * Generated from protobuf field <code>uint32 direction_id = 6;</code>
     * @return int
     */
    public function getDirectionId()
    {
        return $this->direction_id;
    }

    /**
     * The direction_id from the GTFS feed trips.txt file, indicating the
     * direction of travel for trips this selector refers to. This field is
     * still experimental, and subject to change. It may be formally adopted in
     * the future.
     * optional uint32 direction_id = 6;
     *
     * Generated from protobuf field <code>uint32 direction_id = 6;</code>
     * @param int $var
     * @return $this
     */
    public function setDirectionId($var)
    {
        GPBUtil::checkUint32($var);
        $this->direction_id = $var;

        return $this;
    }

    /**
     * The initially scheduled start time of this trip instance.
     * When the trip_id corresponds to a non-frequency-based trip, this field
     * should either be omitted or be equal to the value in the GTFS feed. When
     * the trip_id correponds to a frequency-based trip, the start_time must be
     * specified for trip updates and vehicle positions. If the trip corresponds
     * to exact_times=1 GTFS record, then start_time must be some multiple
     * (including zero) of headway_secs later than frequencies.txt start_time for
     * the corresponding time period. If the trip corresponds to exact_times=0,
     * then its start_time may be arbitrary, and is initially expected to be the
     * first departure of the trip. Once established, the start_time of this
     * frequency-based trip should be considered immutable, even if the first
     * departure time changes -- that time change may instead be reflected in a
     * StopTimeUpdate.
     * Format and semantics of the field is same as that of
     * GTFS/frequencies.txt/start_time, e.g., 11:15:35 or 25:15:35.
     * optional string start_time = 2;
     *
     * Generated from protobuf field <code>string start_time = 2;</code>
     * @return string
     */
    public function getStartTime()
    {
        return $this->start_time;
    }

    /**
     * The initially scheduled start time of this trip instance.
     * When the trip_id corresponds to a non-frequency-based trip, this field
     * should either be omitted or be equal to the value in the GTFS feed. When
     * the trip_id correponds to a frequency-based trip, the start_time must be
     * specified for trip updates and vehicle positions. If the trip corresponds
     * to exact_times=1 GTFS record, then start_time must be some multiple
     * (including zero) of headway_secs later than frequencies.txt start_time for
     * the corresponding time period. If the trip corresponds to exact_times=0,
     * then its start_time may be arbitrary, and is initially expected to be the
     * first departure of the trip. Once established, the start_time of this
     * frequency-based trip should be considered immutable, even if the first
     * departure time changes -- that time change may instead be reflected in a
     * StopTimeUpdate.
     * Format and semantics of the field is same as that of
     * GTFS/frequencies.txt/start_time, e.g., 11:15:35 or 25:15:35.
     * optional string start_time = 2;
     *
     * Generated from protobuf field <code>string start_time = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setStartTime($var)
    {
        GPBUtil::checkString($var, True);
        $this->start_time = $var;

        return $this;
    }

    /**
     * The scheduled start date of this trip instance.
     * Must be provided to disambiguate trips that are so late as to collide with
     * a scheduled trip on a next day. For example, for a train that departs 8:00
     * and 20:00 every day, and is 12 hours late, there would be two distinct
     * trips on the same time.
     * This field can be provided but is not mandatory for schedules in which such
     * collisions are impossible - for example, a service running on hourly
     * schedule where a vehicle that is one hour late is not considered to be
     * related to schedule anymore.
     * In YYYYMMDD format.
     * optional string start_date = 3;
     *
     * Generated from protobuf field <code>string start_date = 3;</code>
     * @return string
     */
    public function getStartDate()
    {
        return $this->start_date;
    }

    /**
     * The scheduled start date of this trip instance.
     * Must be provided to disambiguate trips that are so late as to collide with
     * a scheduled trip on a next day. For example, for a train that departs 8:00
     * and 20:00 every day, and is 12 hours late, there would be two distinct
     * trips on the same time.
     * This field can be provided but is not mandatory for schedules in which such
     * collisions are impossible - for example, a service running on hourly
     * schedule where a vehicle that is one hour late is not considered to be
     * related to schedule anymore.
     * In YYYYMMDD format.
     * optional string start_date = 3;
     *
     * Generated from protobuf field <code>string start_date = 3;</code>
     * @param string $var
     * @return $this
     */
    public function setStartDate($var)
    {
        GPBUtil::checkString($var, True);
        $this->start_date = $var;

        return $this;
    }

    /**
     * optional ScheduleRelationship schedule_relationship = 4;
     *
     * Generated from protobuf field <code>.transit_realtime.TripDescriptor.ScheduleRelationship schedule_relationship = 4;</code>
     * @return int
     */
    public function getScheduleRelationship()
    {
        return $this->schedule_relationship;
    }

    /**
     * optional ScheduleRelationship schedule_relationship = 4;
     *
     * Generated from protobuf field <code>.transit_realtime.TripDescriptor.ScheduleRelationship schedule_relationship = 4;</code>
     * @param int $var
     * @return $this
     */
    public function setScheduleRelationship($var)
    {
        GPBUtil::checkEnum($var, \Transit_realtime\TripDescriptor\ScheduleRelationship::class);
        $this->schedule_relationship = $var;

        return $this;
    }

}

