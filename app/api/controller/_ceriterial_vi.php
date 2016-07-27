<?php
$searchQuery = array();
$searchQueryJson = array();

$attType = array();
$attEnergy = array();
$attEntertaining = array();
$attEnvironment = array();
$attSecurity = array();
$attView = array();
$attTrend = array();
$attUtility = array();
$projectIds = array();

// --------- Attribute type
if ($item->property_type != '') {
    $attType = parent::getCeriterial($item->property_type);
}
if (count($attType)) {
    $searchQuery[] = http_build_query($attType);
}
// Attribute type ---------

// --------- Attribute energy
if ($item->energy_control_system != '') {
    $attEnergy = parent::getCeriterial($item->energy_control_system);
}
if (count($attEnergy)) {
    $searchQuery[] = http_build_query($attEnergy);
}
// Attribute energy ---------

// --------- Attribute entertaining
if ($item->entertaining_control_system != '') {
    $attEntertaining = parent::getCeriterial($item->entertaining_control_system);
}
if (count($attEntertaining)) {
    $searchQuery[] = http_build_query($attEntertaining);
}
// Attribute entertaining ---------

// --------- Attribute environment
if ($item->environment_control_system != '') {
    $attEnvironment = parent::getCeriterial($item->environment_control_system);
}
if (count($attEnvironment)) {
    $searchQuery[] = http_build_query($attEnvironment);
}
// Attribute environment ---------

// --------- Attribute security
if ($item->security_control_system != '') {
    $attSecurity = parent::getCeriterial($item->security_control_system);
}
if (count($attSecurity)) {
    $searchQuery[] = http_build_query($attSecurity);
}
// Attribute security ---------

// --------- Attribute view
if ($item->property_view != '') {
    $attView = parent::getCeriterial($item->property_view);
}
if (count($attView)) {
    $searchQuery[] = http_build_query($attView);
}
// Attribute view ---------

// --------- Attribute trend
if ($item->trend != '') {
    $array = array_filter(explode('-', $item->trend));
    if (count($array)) {
        $i = 0;

        foreach ($array as $at) {
            $searchQueryJson['att_trend[' . $i . ']'] = $at;
            $attTrend['att_trend[' . $i . ']'] = $at;
            $i++;
        }
    }
}

if (count($attTrend)) {
    $searchQuery[] = http_build_query($attTrend);
}
// Attribute trend ---------

// --------- Attribute utility
if ($item->property_utility != '') {
    $attUtility = parent::getCeriterial($item->property_utility);
}
if (count($attUtility)) {
    $searchQuery[] = http_build_query($attUtility);
}
// Attribute utility ---------

// --------- Type
if ($item->type != '') {
    $searchQueryJson['type'] = $item->type;
    $searchQuery[] = http_build_query(array('type' => $item->type));
}
// Type ---------

// --------- Project id
if ($item->project_ids != '') {
    $array = array_filter(explode('-', $item->project_ids));
    if (count($array)) {
        $i = 0;

        foreach ($array as $at) {
            $searchQueryJson['project_ids[' . $i . ']'] = $at;
            $projectIds['project_ids[' . $i . ']'] = $at;
            $i++;
        }
    }
}

if (count($projectIds)) {
    $searchQuery[] = http_build_query($projectIds);
}
// Project id ---------

if ($item->bathroom_count > 0) {
    $searchQueryJson['bathroom_count'] = $item->bathroom_count;
    $searchQuery[] = http_build_query(array('bathroom_count' => $item->bathroom_count));
}

if ($item->bedroom_count > 0) {
    $searchQueryJson['bedroom_count'] = $item->bedroom_count;
    $searchQuery[] = http_build_query(array('bedroom_count' => $item->bedroom_count));
}

if ($item->area != '') {
    $searchQueryJson['area'] = $item->area;
    $searchQuery[] = http_build_query(array('area' => $item->area));
}

// --------- Price
if ($item->price_min > 0) {
    $searchQueryJson['price_min'] = $item->price_min;
    $searchQuery[] = http_build_query(array('price_min' => $item->price_min));
}

if ($item->price_max > 0 && $item->price_max > $item->price_min) {
    $searchQueryJson['price_max'] = $item->price_max;
    $searchQuery[] = http_build_query(array('price_max' => $item->price_max));
}
// Price ---------
$searchQueryJson['cid'] = $item->id;
$searchQuery[] = http_build_query(array('cid' => $item->id));

if (count($searchQuery)) {
    $searchQuery = implode('&', $searchQuery);
}

$_result['id']                          = (int)$item->id;
$_result['name']                        = $item->name;
$_result['property_type']               = $item->property_type;
$_result['property_view']               = $item->property_view;
$_result['property_utility']            = $item->property_utility;
$_result['energy_control_system']       = $item->energy_control_system;
$_result['entertaining_control_system'] = $item->entertaining_control_system;
$_result['environment_control_system']  = $item->environment_control_system;
$_result['security_control_system']     = $item->security_control_system;
$_result['room_type']                   = $item->room_type;
$_result['best_for']                    = $item->best_for;
$_result['suitable_for']                = $item->suitable_for;
$_result['project_ids']                 = $item->project_ids;
$_result['bathroom_count']              = $item->bathroom_count > 0 ? (int)$item->bathroom_count : null;
$_result['bedroom_count']               = $item->bedroom_count > 0 ? (int)$item->bedroom_count : null;
$_result['area']                        = $item->area;
$_result['price_min']                   = $item->price_min > 0 ? (int)$item->price_min : null;
$_result['price_max']                   = $item->price_max > 0 ? (int)$item->price_max : null;
$_result['trend']                       = $item->trend;
$_result['is_new']                      = $item->is_new != '' ? (int)$item->is_new : null;
$_result['is_home']                     = $item->is_home != '' ? (int)$item->is_home : null;
$_result['type']                        = (int)$item->type;
$_result['template']                    = $item->template;
$_result['ordering']                    = (int)$item->ordering;
$_result['search_query']                = $searchQuery;
$_result['search_query_json']           = json_encode($searchQueryJson);