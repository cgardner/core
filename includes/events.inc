<?php
/**
 *  @package Cumula
 *  @subpackage Core
 *  @version    $Id$
 */

//Boot Stages
const BOOT_INIT = "boot_init";
const BOOT_STARTUP = "boot_startup";
const BOOT_PREPARE = 'boot_prepare';
const BOOT_PREPROCESS = "boot_preprocess";
const BOOT_PROCESS = "boot_process";
const BOOT_POSTPROCESS = "boot_postprocess";
const BOOT_CLEANUP = "boot_cleanup"; //Send to appropriate module
const BOOT_SHUTDOWN = "boot_shutdown";

//Response Events
const RESPONSE_PREPARE = 'response_prepare';
const RESPONSE_SEND = 'response_send';

//Component Manager Events
const COMPONENT_INIT_COMPLETE = 'component_init_complete';
const COMPONENT_STARTUP_COMPLETE = 'component_startup_complete';

//System Config Events
const SYSTEMCONFIG_SET_VALUE = 'system_config_set_value';
const SYSTEMCONFIG_GET_VALUE = 'system_config_get_value';

//Event Dispatcher Events
const EVENTDISPATCHER_EVENT_DISPATCHED = 'event_dispatched';

const EVENT_LOGGED = 'message_logged';

const ROUTER_COLLECT_ROUTES = 'router_collect_routes';
const ROUTER_FILE_NOT_FOUND = 'router_file_not_found';
const ROUTER_ADD_ROUTE = 'router_add_route';