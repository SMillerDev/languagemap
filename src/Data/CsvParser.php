<?php
/**
 * This file contains the CsvParser.php
 *
 * @package d3map\SOMETHING
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

//TODO: Change this

namespace LanguageMap\Data;

/**
 * Class CsvParser
 */
class CsvParser
{

    /**
     * Parse a csv to a multidimensional array
     *
     * @param array $file File to parse
     *
     * @return array
     */
    static function parse_file($file)
    {
        $array     = array_map('str_getcsv', $file);
        $new_array = [];
        $headers   = $array[0];
        unset($array[0]);
        foreach ($array as $item)
        {
            foreach ($item as $key => $value)
            {
                $item[$headers[$key]] = $value;
                unset($item[$key]);
            }

            $new_array[$item['Country']] = $item;
        }

        return $new_array;
    }
}