<?php

/**
 * DataPhreaks | anything to do with icons
 * Date: August 16 2021
 */

namespace App\Commands\Phreaks;

define('LAZER_DATA_PATH', './resources/tables/data/');

use Lazer\Classes\Database as Lazer;
use Lazer\Classes\Helpers as Helpers;

class DataPhreaks
{
    const TABLE_TEMPLATES = './resources/tables/';

    public static function createTable($tableName)
    {
        $template_file = self::TABLE_TEMPLATES . $tableName . ".json";

        if (is_file($template_file)) {
            $template = json_decode(file_get_contents($template_file), true);
            Lazer::create($tableName, $template);
            return true;
        } else {
            return false;
        }
    }

    public static function getAllFromTable($tableName)
    {
        try {
            \Lazer\Classes\Helpers\Validate::table($tableName)->exists();
            $results = Lazer::table($tableName)->findAll()->asArray();
            $result = json_encode($results);
        } catch (\Lazer\Classes\LazerException $e) {
            //Database doesn't exist
            $result = false;
        }

        return $result;
    }

    public static function getRowByID($tableName, $id)
    {
        $row = Lazer::table($tableName)->find($id);

        return $row;
    }

    public static function checkTable($tableName)
    {
        try {
            \Lazer\Classes\Helpers\Validate::table($tableName)->exists();
            $result = true;
        } catch (\Lazer\Classes\LazerException $e) {
            //Database doesn't exist
            $result = self::createTable($tableName);
        }

        return $result;
    }

    public static function checkTableRow($tableName, $fieldName, $fieldData)
    {
        $row = Lazer::table($tableName)->where($fieldName, '=', $fieldData)->find();

        if (isset($row->id) && $row->id > 0) {
            $result = true;
        } else {
            $result = false;
        }

        return $result;
    }

    public static function addRowFV($tableName, $field, $value)
    {
        $row = Lazer::table($tableName);
        $row->setField($field, $value);
        $row->save();
    }

    public static function addRow($tableName, $data)
    {
        $row = Lazer::table($tableName);
        foreach ($data as $f => $v) {
            $row->setField($f, $v);
        }
        $row->save();
    }

    public static function updateTableData($data, $tableName = "", $findKey = "", $findValue = "")
    {
        $row = Lazer::table($tableName)->where($findKey, '=', $findValue)->find();

        if (isset($row->id)) {
            $row->set($data);
            $row->save();

            $result = true;
        } else {
            $result = false;
        }

        return $result;
    }

    public static function removeRow($table, $ID)
    {
        // Confirm Row Exists
        $row = Lazer::table($table)->find($ID);

        if (isset($row->id))
            Lazer::table($table)->find($ID)->delete();
        else
            return false;

        return true;
    }
}
