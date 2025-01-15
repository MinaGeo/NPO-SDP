<?php

declare(strict_types=1);

ob_start();
require_once "./db_setup.php";
ob_end_clean();

class LocationRepository
{
    public static function getLocationHierarchy($locationHierarchyId)
    {
        global $configs;
        global $conn;
        $result = $conn->run_select_query("SELECT * FROM $configs->DB_NAME.$configs->DB_LOCATION_HIERARCHY_TABLE WHERE id = $locationHierarchyId");
        $hierarchy = $result->fetch_assoc();
        if (!$hierarchy) {
            return null;
        }
        $parentResult = $conn->run_select_query("SELECT name FROM $configs->DB_NAME.$configs->DB_LOCATIONS_TABLE WHERE id = " . $hierarchy['parent_id']);
        $parentLocation = $parentResult->fetch_assoc()['name'];
        $childResult = $conn->run_select_query("SELECT name FROM $configs->DB_NAME.$configs->DB_LOCATIONS_TABLE WHERE id = " . $hierarchy['child_id']);
        $childLocation = $childResult->fetch_assoc()['name'];
        return $childLocation . ", " . $parentLocation;
    }


    public static function getLocationHierarchyId($governorate, $location) { // Fetch the IDs from the database 
        global $configs;
        global $conn;
        $governorateIdResult = $conn->run_select_query("SELECT id FROM $configs->DB_NAME.$configs->DB_LOCATIONS_TABLE WHERE name = ?", [$governorate]); 
        $governorateId = $governorateIdResult->fetch_assoc()['id']; 
        $subLocationIdResult = $conn->run_select_query("SELECT id FROM $configs->DB_NAME.$configs->DB_LOCATIONS_TABLE WHERE name = ? AND id IN (SELECT child_id FROM $configs->DB_NAME.$configs->DB_LOCATION_HIERARCHY_TABLE WHERE parent_id = ?)", [$location, $governorateId]); 
        $subLocationId = $subLocationIdResult->fetch_assoc()['id']; 
        // Fetch the hierarchical ID from `location_hierarchy` 
        $hierarchyResult = $conn->run_select_query("SELECT id FROM $configs->DB_NAME.$configs->DB_LOCATION_HIERARCHY_TABLE WHERE parent_id = ? AND child_id = ?", [$governorateId, $subLocationId]); 
        $locationHierarchyId = $hierarchyResult->fetch_assoc()['id']; 
        return $locationHierarchyId;
    }
}
