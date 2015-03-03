<?PHP

//Avoid re-include this file
if (class_exists("eRankerCommons")) {
    return;
}

class eRankerCommons {

    const NAME = "eRankerCommons";
    const MISSING = "MISSING";
    const NEUTRAL = "NEUTRAL";
    const GREEN = "GREEN";
    const ORANGE = "ORANGE";
    const RED = "RED";
    const BIG = "BIG";
    const BASE_ER = "fad6kh9uo3xltjn48erw5qc20gm7szbi1vpy";
    const BASE_10 = "0123456789";

    public static $imgfolder = "/content/themes/eranker/img/";

    /**
     * Decode a report id from eranker base
     * @param string $id The report id
     */
    public static function decodeReportId($id) {
        return self::convBase($id, self::BASE_10, self::BASE_10); //disabled for now...
    }

    /**
     * Decode a report id from eranker base
     * @param string $id The report id
     */
    public static function encodeReportId($id) {
        return self::convBase($id, self::BASE_10, self::BASE_10);
    }

    /**
     * Based on a string. we add the data using the model inside the string. 
     * Normally we use sprintf but if the data is an array or object, we replace using %keyname
     * @param Any $data The data from the factor
     * @param String $string The base string
     * @return The new string with the value data values in it (if needed)
     */
    public static function replaceValue($data, $string) {
        if (empty($string)) {
            return "";
        }
        if (is_array($data) || is_object($data)) {
            $data = (array) $data;
            $out = $string;
            foreach ($data as $key => $value) {
                if (is_string($value) || is_int($value) || is_float($value) || is_numeric($value)) {
                    $out = str_replace("%" . $key, $value, $out);
                } else {
                    $out = str_replace("%" . $key, is_object($value) ? "[OBJECT]" : "[ARRAY]", $out);
                }
            }
            return $out;
        } else {
            return sprintf($string, $data);
        }
    }

    /**
     * Based on a status, get the rigth model array (status, model and description) from a factor
     * @param Any $data The data Object
     * @param String $status The factor Status Text. Ex: RED, MISSING, ORANGE, etc
     * @param Object $fullFactor The full factor Object. Must contain the texts
     * @return Array The array with the right text models for the status.
     */
    public static function getFactorStatusText($data, $status, $fullFactor) {
        $out = array();
        switch ($status) {
            case self::RED:
                $out['model'] = self::replaceValue($data, $fullFactor->model_red);
                $out['description'] = self::replaceValue($data, $fullFactor->description_red);
                break;
            case self::ORANGE:
                $out['model'] = self::replaceValue($data, $fullFactor->model_orange);
                $out['description'] = self::replaceValue($data, $fullFactor->description_orange);
                break;
            case self::GREEN:
                $out['model'] = self::replaceValue($data, $fullFactor->model_green);
                $out['description'] = self::replaceValue($data, $fullFactor->description_green);
                break;
            case self::NEUTRAL:
                $out['model'] = self::replaceValue($data, $fullFactor->model_neutral);
                $out['description'] = self::replaceValue($data, $fullFactor->description_neutral);
                break;
            case self::MISSING;
            default;
                $out['model'] = self::replaceValue($data, $fullFactor->model_missing);
                $out['description'] = self::replaceValue($data, $fullFactor->description_missing);
        }


        return $out;
    }

    /**
     * Based on the factor value, return the rigth factor status based on the limits and the function
     * @param Any $value The value of the factor.
     * @param Object $fullFactor The full factor Object. Must contain the texts
     * @return String The factor status
     */
    public static function getFactorStatus($value, $fullFactor) {
        if ($value === NULL) {
            return self::MISSING;
        }
        switch ($fullFactor->function) {
            case ">":
                if ($value > $fullFactor->limit_green) {
                    return self::GREEN;
                } else {
                    if ($value > $fullFactor->limit_orange) {
                        return self::ORANGE;
                    } else {
                        return self::RED;
                    }
                }
                break;
            case ">=":
                if ($value >= $fullFactor->limit_green) {
                    return self::GREEN;
                } else {
                    if ($value >= $fullFactor->limit_orange) {
                        return self::ORANGE;
                    } else {
                        return self::RED;
                    }
                }
                break;
            case "<":
                if ($value < $fullFactor->limit_green) {
                    return self::GREEN;
                } else {
                    if ($value < $fullFactor->limit_orange) {
                        return self::ORANGE;
                    } else {
                        return self::RED;
                    }
                }
                break;
            case "<=":
                if ($value <= $fullFactor->limit_green) {
                    return self::GREEN;
                } else {
                    if ($value <= $fullFactor->limit_orange) {
                        return self::ORANGE;
                    } else {
                        return self::RED;
                    }
                }
                break;
            case "<>":
                if ($value != $fullFactor->limit_green) {
                    return self::GREEN;
                } else {
                    if ($value != $fullFactor->limit_orange) {
                        return self::ORANGE;
                    } else {
                        return self::RED;
                    }
                }
                break;
            case "==":
                if ($value == $fullFactor->limit_green) {
                    return self::GREEN;
                } else {
                    if ($value == $fullFactor->limit_orange) {
                        return self::ORANGE;
                    } else {
                        return self::RED;
                    }
                }
                break;
            case "strlen()>=":
                if (strlen($value) >= $fullFactor->limit_green) {
                    return self::GREEN;
                } else {
                    if (strlen($value) >= $fullFactor->limit_orange) {
                        return self::ORANGE;
                    } else {
                        return self::RED;
                    }
                }
                break;
            case "strlen()>":
                if (strlen($value) > $fullFactor->limit_green) {
                    return self::GREEN;
                } else {
                    if (strlen($value) > $fullFactor->limit_orange) {
                        return self::ORANGE;
                    } else {
                        return self::RED;
                    }
                }
                break;
            case "strlen()<=":
                if (strlen($value) <= $fullFactor->limit_green) {
                    return self::GREEN;
                } else {
                    if (strlen($value) <= $fullFactor->limit_orange) {
                        return self::ORANGE;
                    } else {
                        return self::RED;
                    }
                }
                break;
            case "strlen()<":
                if (strlen($value) < $fullFactor->limit_green) {
                    return self::GREEN;
                } else {
                    if (strlen($value) < $fullFactor->limit_orange) {
                        return self::ORANGE;
                    } else {
                        return self::RED;
                    }
                }
                break;
            case "strlen()==":
                if (strlen($value) == $fullFactor->limit_green) {
                    return self::GREEN;
                } else {
                    if (strlen($value) == $fullFactor->limit_orange) {
                        return self::ORANGE;
                    } else {
                        return self::RED;
                    }
                }
                break;
            case "strlen()<>":
                if (strlen($value) != $fullFactor->limit_green) {
                    return self::GREEN;
                } else {
                    if (strlen($value) != $fullFactor->limit_orange) {
                        return self::ORANGE;
                    } else {
                        return self::RED;
                    }
                }
                break;
            case "count()>=":
                if (count($value) >= $fullFactor->limit_green) {
                    return self::GREEN;
                } else {
                    if (count($value) >= $fullFactor->limit_orange) {
                        return self::ORANGE;
                    } else {
                        return self::RED;
                    }
                }
                break;
            case "count()>":
                if (count($value) > $fullFactor->limit_green) {
                    return self::GREEN;
                } else {
                    if (count($value) > $fullFactor->limit_orange) {
                        return self::ORANGE;
                    } else {
                        return self::RED;
                    }
                }
                break;
            case "count()<=":
                if (count($value) <= $fullFactor->limit_green) {
                    return self::GREEN;
                } else {
                    if (count($value) <= $fullFactor->limit_orange) {
                        return self::ORANGE;
                    } else {
                        return self::RED;
                    }
                }
                break;
            case "count()<":
                if (count($value) < $fullFactor->limit_green) {
                    return self::GREEN;
                } else {
                    if (count($value) < $fullFactor->limit_orange) {
                        return self::ORANGE;
                    } else {
                        return self::RED;
                    }
                }
                break;
            case "count()==":
                if (count($value) == $fullFactor->limit_green) {
                    return self::GREEN;
                } else {
                    if (count($value) == $fullFactor->limit_orange) {
                        return self::ORANGE;
                    } else {
                        return self::RED;
                    }
                }
                break;
            case "count()<>":
                if (count($value) != $fullFactor->limit_green) {
                    return self::GREEN;
                } else {
                    if (count($value) != $fullFactor->limit_orange) {
                        return self::ORANGE;
                    } else {
                        return self::RED;
                    }
                }
                break;
            case "red":
            case "RED":
                return self::RED;
            case "orange":
            case "ORANGE":
                return self::ORANGE;
            case "green":
            case "GREEN":
                return self::GREEN;
            case "missing":
            case "MISSING":
                return self::MISSING;
            case "neutral":
            case "NEUTRAL":
            default:
                return self::NEUTRAL;
        }
        return self::NEUTRAL;
    }

    /**
     * Get the scores array for the report
     * @param Object $report The report object
     * @param Object $reportData The report data
     * @param Array $reportFactors The list of factors
     * @param boolean $debug The debug flag.
     * @return Array The scores generated
     */
    public static function getScores($report, $reportData, $reportFactors, $debug = false) {
        $out = array();

        $maxScore = 0;
        $currentScore = 0;
        $totalRed = 0;
        $totalGreen = 0;
        $totalOrange = 0;
        $totalMissing = 0;
        $totalNeutral = 0;


        $out["score"] = array(); //Init the score. **See code after the foeach

        $out["url"] = isset($report->url) ? $report->url : NULL;

        if (!empty($reportFactors)) {
            foreach ($reportFactors as $factor) {

                $valueToUse = isset($reportData[$factor->id]) ? $reportData[$factor->id] : NULL;
                if (!empty($factor->path)) {
                    $pathArr = explode("->", trim($factor->path));
                    if (!empty($pathArr)) {
                        foreach ($pathArr as $currentPath) {
                            $valueToUse = (array) $valueToUse;
                            $valueToUse = isset($valueToUse[$currentPath]) ? $valueToUse[$currentPath] : NULL;
                        }
                    }
                }
                $statusCode = self::getFactorStatus($valueToUse, $factor);
                $status = self::getFactorStatusText(isset($reportData[$factor->id]) ? $reportData[$factor->id] : NULL, $statusCode, $factor);

                $out[$factor->id] = array();
                $out[$factor->id]['data'] = isset($reportData[$factor->id]) ? $reportData[$factor->id] : NULL;
                $out[$factor->id]['model'] = array();
                //$out[$factor->id]['model']['name'] = isset($factor->id) ? $factor->id : NULL;
                $out[$factor->id]['model']['friendly_name'] = isset($factor->friendly_name) ? $factor->friendly_name : NULL;
                $out[$factor->id]['model']['type'] = isset($factor->type) ? $factor->type : NULL;
                $out[$factor->id]['model']['status'] = $statusCode;
                $out[$factor->id]['model']['model'] = $status['model'];
                $out[$factor->id]['model']['description'] = $status['description'];
                $out[$factor->id]['model']['path'] = isset($factor->path) ? $factor->path : NULL;
                //$out[$factor->id]['model']['pro_only'] = isset($factor->pro_only) && !empty($factor->pro_only) ? TRUE : FALSE;
                //$out[$factor->id]['model']['free'] = isset($factor->free) && !empty($factor->free) ? TRUE : FALSE;
                $out[$factor->id]['model']['order'] = !isset($factor->order) ? 0 : $factor->order;
                $out[$factor->id]['model']['correlation'] = !isset($factor->correlation) ? null : $factor->correlation;
                $out[$factor->id]['model']['difficulty_level'] = !isset($factor->difficulty_level) ? null : $factor->difficulty_level;
                $out[$factor->id]['model']['article'] = isset($factor->article) ? $factor->article : NULL;
                $out[$factor->id]['model']['solution'] = isset($factor->solution) ? $factor->solution : NULL;
                //Add the factor to the score system
                if ($statusCode === self::RED) {
                    $maxScore += $factor->correlation; //Receive score 0 for this factor
                    $totalRed++;
                }
                if ($statusCode === self::MISSING) {
                    $maxScore += $factor->correlation; //Receive score 0 for this factor
                    $totalMissing++;
                }
                if ($statusCode === self::ORANGE) {
                    $maxScore += $factor->correlation;
                    $currentScore += $factor->correlation * 0.5; //Receive 50% of total score for this factor
                    $totalOrange++;
                }
                if ($statusCode === self::GREEN) {
                    $maxScore += $factor->correlation;
                    $currentScore += $factor->correlation; //Receive 100% of total score for this factor
                    $totalGreen++;
                }
                if ($statusCode === self::NEUTRAL) {
                    $totalNeutral++; //Neutral are ignored on the score
                }

                $out[$factor->id]['model']['category'] = array();
                $out[$factor->id]['model']['category']['order'] = isset($factor->category_order) ? $factor->category_order : NULL;
                $out[$factor->id]['model']['category']['friendly_name'] = isset($factor->category_friendly_name) ? $factor->category_friendly_name : NULL;
                $out[$factor->id]['model']['category']['description'] = isset($factor->category_description) ? $factor->category_description : NULL;
                $out[$factor->id]['model']['category']['bg_color'] = isset($factor->category_bg_color) ? strtoupper($factor->category_bg_color) : NULL;
                $out[$factor->id]['model']['category']['hover_color'] = isset($factor->category_hover_color) ? strtoupper($factor->category_hover_color) : NULL;
                $out[$factor->id]['model']['category']['group'] = array();
                $out[$factor->id]['model']['category']['group']['friendly_name'] = isset($factor->group_friendly_name) ? $factor->group_friendly_name : NULL;
                $out[$factor->id]['model']['category']['group']['description'] = isset($factor->group_description) ? $factor->group_description : NULL;
                $out[$factor->id]['model']['category']['group']['order'] = isset($factor->group_order) ? $factor->group_order : NULL;

                if ($debug) {
                    $out[$factor->id]['model']['debug'] = array(
                        'limit_red' => $factor->limit_red,
                        'limit_orange' => $factor->limit_orange,
                        'limit_green' => $factor->limit_green,
                        'limit_neutral' => $factor->limit_neutral,
                        'function' => $factor->function
                    );
                }
            }
        }
        //Merge the scode data
        $out["score"]["percentage"] = (double) number_format(($currentScore / max(1, $maxScore)) * 100, 1);
        $out["score"]["raw"] = (double) number_format($currentScore, 1);
        $out["score"]["raw_total"] = (double) number_format(max(1, $maxScore), 1);
        $out["score"]["factors"] = (object) array("red" => $totalRed, "orange" => $totalOrange, "green" => $totalGreen, "missing" => $totalMissing, "neutral" => $totalNeutral);

        return (object) $out;
    }

    /**
     * Convert a database full factor to a object in the API format
     * @param Object $factor The full factor object that came from database
     * @return Object the factor formated to be shown on the API
     */
    public static function getFactorExternalObj($factor) {

        if (empty($factor)) {
            return array();
        }
        $tmpitem = array();
        if ($factor->is_active) {
            $tmpitem['id'] = $factor->id;
            $tmpitem['friendly_name'] = $factor->friendly_name;
            $tmpitem['order'] = $factor->order;
            $tmpitem['type'] = $factor->type;
            $tmpitem['gui_type'] = $factor->gui_type;
            $tmpitem['limit_red'] = $factor->limit_red;
            $tmpitem['limit_orange'] = $factor->limit_orange;
            $tmpitem['limit_green'] = $factor->limit_green;
            $tmpitem['limit_neutral'] = $factor->limit_neutral;
            $tmpitem['model_red'] = $factor->model_red;
            $tmpitem['model_orange'] = $factor->model_orange;
            $tmpitem['model_green'] = $factor->model_green;
            $tmpitem['model_neutral'] = $factor->model_neutral;
            $tmpitem['model_missing'] = $factor->model_missing;
            $tmpitem['description_red'] = $factor->description_red;
            $tmpitem['description_orange'] = $factor->description_orange;
            $tmpitem['description_green'] = $factor->description_green;
            $tmpitem['description_neutral'] = $factor->description_neutral;
            $tmpitem['description_missing'] = $factor->description_missing;
            $tmpitem['correlation'] = $factor->correlation;
            $tmpitem['path'] = $factor->path;
            $tmpitem['pro_only'] = isset($factor->pro_only) && !empty($factor->pro_only) ? TRUE : FALSE;
            $tmpitem['free'] = isset($factor->free) && !empty($factor->free) ? TRUE : FALSE;
            $tmpitem['article'] = $factor->article;
            $tmpitem['solution'] = $factor->solution;
            $tmpitem['difficulty_level'] = $factor->difficulty_level;
            $tmpitem['category_id'] = $factor->category_id;
            $tmpitem['category_order'] = $factor->category_order;
            $tmpitem['category_icon'] = $factor->category_icon;
            $tmpitem['category_friendly_name'] = isset($factor->category_friendly_name) ? $factor->category_friendly_name : NULL;
            $tmpitem['category_description'] = isset($factor->category_description) ? $factor->category_description : NULL;
            $tmpitem['category_bg_color'] = isset($factor->category_bg_color) ? strtoupper($factor->category_bg_color) : NULL;
            $tmpitem['category_hover_color'] = isset($factor->category_hover_color) ? strtoupper($factor->category_hover_color) : NULL;
            $tmpitem['group_id'] = isset($factor->group_id) ? $factor->group_id : NULL;
            $tmpitem['group_friendly_name'] = isset($factor->group_friendly_name) ? $factor->group_friendly_name : NULL;
            $tmpitem['group_description'] = isset($factor->group_description) ? $factor->group_description : NULL;
            $tmpitem['group_order'] = $factor->group_order;
        }
        return $tmpitem;
    }

    /**
     * Filter the factor name to make sure it does not have suspicius characters
     * @param String $s The factor name to be filtred
     * @return String the filtred factor name (with only letters, numbers and underline
     */
    public static function sanitizeFactorName($s) {
        return preg_replace("/[^a-zA-Z0-9-_]+/", "", $s);
    }

    /**
     * Convert a type from the factor table to the sql type that will be used to create the factor table
     * @param String $type Type from the factors table. Values: 'BOOLEAN', 'INTEGER', 'FLOAT', 'JSON', 'TEXT', 'BIGINT', 'DATETIME', 'STRING'
     * @return String The SQL type string. By default it returns a varchar.
     */
    public static function getFactorSQLType($type) {
        switch (trim(strtoupper((string) $type))) {
            case 'BOOLEAN':
                return "BOOLEAN";
            case 'INTEGER':
                return "INT";
            case 'FLOAT':
                return "FLOAT";
            case 'JSON':
                return "TEXT";
            case 'BIGINT':
                return "BIGINT";
            case 'DATETIME':
                return "DATETIME";
            case 'TEXT':
                return "TEXT";
            case 'STRING':
            default:
                return "VARCHAR(255) COLLATE utf8_bin";
        }
    }

    /**
     * Convert a factor value to a given Type
     * @param Any $value The factor value
     * @param String $type The type of the variable
     * @return Any The variable casted to the right type
     */
    public static function convertFactor($value, $type) {
        if (is_null($value)) {
            return NULL;
        }
        switch ($type) {
            case 'INTEGER':
                return (int) $value;
            case 'STRING':
                return (string) $value;
            case 'BOOLEAN':
                return (boolean) $value;
            case 'FLOAT':
                return (float) $value;
            case 'JSON':
                return json_decode($value);
            default:
                return $value;
        }
    }

    /**
     * FIx a URL by adding the protocol and lowercase the hostname and replace the spaces
     * @param String $url The original url to be fixed
     * @return boolean|string The fixed URL. False if the url is invalid
     */
    public static function fixURL($url) {
        if (strpos($url, "//") === 0) {
            $url = "http:" . $url;
        } else {
            if (strpos(strtolower($url), "http") !== 0) {
                $url = "http://" . $url;
            }
        }
        $parsed = parse_url($url);

        if (!isset($parsed['scheme']) || empty($parsed['scheme'])) {
            return false;
        }
        if (!isset($parsed['host']) || empty($parsed['host'])) {
            return false;
        }

        $url = strtolower($parsed['scheme']) . "://" . strtolower($parsed['host'])
                . ( (isset($parsed['path']) && !empty($parsed['path'])) ? $parsed['path'] : "/" )
                . ( (isset($parsed['query']) && !empty($parsed['query'])) ? "?" . $parsed['query'] : "" );

        if (filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED | FILTER_FLAG_HOST_REQUIRED)) {
            return str_replace(' ', '%20', $url);
        } else {
            return false;
        }
    }

    /**
     * Prepare a factor array, remove duplicates, sort,  strtolower and trim for each item
     * @param array $arr The input array
     * @return array The filtred array
     */
    public static function prepareFactorArray($arr) {
        $out = array();
        if (empty($arr)) {
            return $out;
        }
        foreach ($arr as $value) {
            $newvalue = strtolower(trim($value));
            if (!empty($newvalue)) {
                $out[] = trim($newvalue);
            }
        }
        sort($out);
        return array_unique($out);
    }

    /**
     * Get a ORDERED TREE of full factors
     * This function 
     * The tree look likes this:
     *  [category_id] 
     *      [group_id1] 
     *              factor1
     *              factor2
     *              factor3
     *      [group_id2]
     *              factor4
     *              factor5
     *              factor6
     * @param array $fullfactors The full factors from the database
     * @return array The Tree as an array
     */
    public static function getFactorTree($fullfactors) {

        //Create an order for each thing
        $categories_order = array();
        $groups_order = array();
        $factors_order = array();

        //Create the unordered tree
        $unorderedTree = array();

        if (!empty($fullfactors)) {
            foreach ($fullfactors as $factor) {
                $factor = (object) $factor;
                if (!isset($categories_order[$factor->category_id])) {
                    $categories_order[$factor->category_id] = $factor->category_order;
                }

                if (!isset($groups_order[$factor->group_id])) {
                    $groups_order[$factor->group_id] = $factor->group_order;
                }

                if (!isset($factors_order[$factor->id])) {
                    $factors_order[$factor->id] = $factor->order;
                }

                if (!isset($unorderedTree[$factor->category_id])) {
                    $unorderedTree[$factor->category_id] = array();
                }

                if (!isset($unorderedTree[$factor->category_id][$factor->group_id])) {
                    $unorderedTree[$factor->category_id][$factor->group_id] = array();
                }
                $unorderedTree[$factor->category_id][$factor->group_id][] = $factor->id;
            }
        }
        //Sort the individual items
        asort($categories_order);
        asort($groups_order);
        asort($factors_order);


        $orderedTree = array();

        //Create the ordered tree by navigate each item in order and comparing with the unordered tree.
        if (!empty($categories_order)) {
            foreach ($categories_order as $category_id => $category_order) {
                $orderedTree[$category_id] = array();
                if (!empty($groups_order)) {
                    foreach ($groups_order as $group_id => $group_order) {
                        if (in_array($group_id, array_keys($unorderedTree[$category_id]))) {
                            $orderedTree[$category_id][$group_id] = array();
                            if (!empty($factors_order)) {
                                foreach ($factors_order as $factor_id => $factor_order) {
                                    if (in_array($factor_id, $unorderedTree[$category_id][$group_id])) {
                                        $orderedTree[$category_id][$group_id][] = $factor_id;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        //Debug!
        //echo "<pre>" . print_r($unorderedTree, true) . "</pre>";
        //echo "<pre>" . print_r($orderedTree, true) . "</pre>";
        return $orderedTree;
    }

    /**
     * Convert a object to an array using recurssion
     * @param obejct $obj The object to be converted
     * @return array The array
     */
    public static function objectToArray($obj) {
        if (is_object($obj)) {
            $obj = (array) $obj;
        }
        if (is_array($obj)) {
            $new = array();
            foreach ($obj as $key => $val) {
                $new[$key] = self::objectToArray($val);
            }
        } else {
            $new = $obj;
        }
        return $new;
    }

    /**
     * Convert an arbitrarily large number from any base to any base.
     * examples for $fromBaseInput and $toBaseInput
     * '0123456789ABCDEF' for Hexadecimal (Base16)
     * '0123456789' for Decimal (Base10)
     * '01234567' for Octal (Base8)
     * '01' for Binary (Base2) 
     * You can really put in whatever you want and the first character is the 0.
     * @param string $numberInput number to convert as a string
     * @param string $fromBaseInput base of the number to convert as a string
     * @param string $toBaseInput base the number should be converted to as a string
     * @return string The output on the new base
     */
    public static function convBase($numberInput, $fromBaseInput, $toBaseInput) {
        if ($fromBaseInput == $toBaseInput) {
            return $numberInput;
        }
        $fromBase = str_split($fromBaseInput, 1);
        $toBase = str_split($toBaseInput, 1);
        $number = str_split($numberInput, 1);
        $fromLen = strlen($fromBaseInput);
        $toLen = strlen($toBaseInput);
        $numberLen = strlen($numberInput);
        $retval = '';
        if ($toBaseInput == '0123456789') {
            $retval = 0;
            for ($i = 1; $i <= $numberLen; $i++) {
                $retval = bcadd($retval, bcmul(array_search($number[$i - 1], $fromBase), bcpow($fromLen, $numberLen - $i)));
            }
            return $retval;
        }
        if ($fromBaseInput != '0123456789') {
            $base10 = self::convBase($numberInput, $fromBaseInput, '0123456789');
        } else {
            $base10 = $numberInput;
        }
        if ($base10 < strlen($toBaseInput)) {
            return $toBase[$base10];
        }
        while ($base10 != '0') {
            $retval = $toBase[bcmod($base10, $toLen)] . $retval;
            $base10 = bcdiv($base10, $toLen, 0);
        }
        return $retval;
    }

    /**
     * Generate the report HTML code
     * @param object $report the report row. Factors cols shall be already converted to array
     * @param object $reportScores The array with the report data and scores
     * @param array $fullfactors The full factors from the database
     * @param boolean $logged_in Tell if the user is logged in or not
     * @param boolean $is_pdf Tell if we are generating a pdf or not
     * @return string The html of the report. 
     */
    public static function getReportHTML($report, $reportScores, $fullfactors, $logged_in = false, $is_pdf = false, $disable_pdf = false) {

        //Make sure that the factors is on the array format
        $fullfactors = self::objectToArray($fullfactors);

        //Make sure that the scores is on the array format
        $reportScores = self::objectToArray($reportScores);

        $out = "<div class='superreport-seo'>";
        $out .= "<div id='erreport'>";
        $out .= self::getReportScoreHTML($report, $reportScores['score'], self::BIG, $disable_pdf);

        $categories = array();
        $groups = array();

        //Remove the factors that are not used on the report:
        foreach ($fullfactors as $factor_id => $factor) {
            if (!in_array($factor_id, $report->factors)) {
                unset($fullfactors[$factor_id]);
            }
            $categories[$factor['category_id']] = array(
                "friendly_name" => $factor['category_friendly_name'],
                "description" => $factor['category_description'],
                "order" => $factor['category_order'],
                "hover_color" => $factor['category_hover_color'],
                "bg_color" => $factor['category_bg_color'],
                "icon" => $factor['category_icon']
            );
            $groups[$factor['group_id']] = array(
                "friendly_name" => $factor['group_friendly_name'],
                "description" => $factor['group_description'],
                "order" => $factor['group_order']
            );
        }
        $factorTree = self::getFactorTree($fullfactors);

        //Navigate down the factor tree to this report
        if (!empty($factorTree)) {
            foreach ($factorTree as $category_id => $category_array) {
                if (!empty($category_array)) {

                    $out .= "\r\n";
                    $out .= '<div class="ercategory" data-category_id="' . $category_id . '" >';
                    $out .= '<div class="ercategoryheadline">';
                    $out .= '<h2 onclick="$(\'.ercategorydescription[data-category_id=' . $category_id . ']\').slideToggle();" class="ercategoryname" style="border-color: #' . $categories[$category_id]['bg_color'] . '">';
                    $out .= '<img src="' . $categories[$category_id]['icon'] . '" class="ercategoryicon" alt="{icon}" /> ';
                    $out .= $categories[$category_id]['friendly_name'];
                    $out .= '<div class="ercategoryprogressbar"></div>';
                    $out .= '</h2></div>';
                    $out .= '<div class="ercategorydescription" data-category_id="' . $category_id . '" style="display:none">' . $categories[$category_id]['description'] . '</div>';

                    $is_odd_row = true;
                    foreach ($category_array as $group_id => $group_array) {
                        if (!empty($group_array)) {
                            $out .= "\r\n";
                            $out .= '<div class="ergroup row" data-group_id="' . $group_id . '" >';
                            $out .= '<h3 class="ergroupname ' . (($is_odd_row) ? 'eroddrow' : '') . '">' . $groups[$group_id]['friendly_name'] . '</h3>';
                            $is_even = false;
                            foreach ($group_array as $factor_id) {
                                $out .= self::getFactorHTML($report, $fullfactors[$factor_id], $reportScores[$factor_id], $is_even, $logged_in);
                                $is_even = !$is_even;
                            }
                            $out .= "</div>\r\n";
                        }
                    }

                    $out .= "</div>\r\n";
                }
            }
        }

        //DEBUG!
        //$out .= "<pre>ORDERED FACTOR TREE: " . print_r($factorTree, true) . "</pre>";

        $out .= "</div>";
        $out .= "</div>";
        return $out;
    }

    /**
     * Return the time using the user timezone.
     * @param string $timezone the timezone from the user
     * @param date $date_time the time
     * @return String with the time
     */
    public static function convertDateTime($date_time, $timezone) {
        if (empty($timezone)) {
            $timezone = 'UTC';
        }
        $newtimezone = new DateTimeZone($timezone);
        $newdate = new DateTime($date_time);
        $newdate->setTimezone($newtimezone);
        $generate_time = $newdate->format('H:i');
        $generate_date = $newdate->format('d/m/y');
        return $generate_date . ',' . $generate_time;
    }

    /**
     * Generate the report score row HTML
     * @param object $report the report row. Factors cols shall be already converted to array
     * @param object $generalscore The array with the report generic score
     * @param string $format The format/theme we shall output. Default: BIG
     * @return string The html of the report score (the top part of a report). 
     */
    public static function getReportScoreHTML($report, $generalscore, $format = self::BIG, $disable_pdf = false) {
        $out = "";
        $thumb_URL = "https://www.eranker.com/IMAGE"; //FIXTHIS

        if (!isset($generalscore['thumbnail']) || empty($generalscore['thumbnail'])) {
            $thumb_URL = self::$imgfolder . "loading-page-preview.gif";
        } else {
            $thumb_URL = $generalscore['thumbnail'];
        }
        $report_url = trim(str_replace("http://", "", str_replace("https://", "", $report->url)), " /\\");

        $score_raw_total = $generalscore['factors']['missing'] + $generalscore['factors']['green'] + $generalscore['factors']['orange'] + $generalscore['factors']['red'];

        $out .= '<div class="row score-table">';
        $out .= '<div class="col can-float factors-percent" style="padding:0">' // factors-percent
                . '<aside>'
                . '<div class="overall-score">'
                . '<p>Overall</p>'
                . '<h1>' . round($generalscore['percentage']) . '</h1>'
                . '<p>out of 100</p>'
                . '<div class="circle" id="circles" data-percent="' . $generalscore['percentage'] . '" ></div>' // percentage chart
                . '</div>' // overall
                . '<div class="additional-ratings">'
                . '<span>Generated on ' . self::convertDateTime($report->date_created, 'UTC') . '<br />';
        if ($disable_pdf == FALSE) {
            $out .= '<a id="update-now" onclick="hasSupport()">Update now</a></span>';
        }
        $out .= '<ul id="rating-stars">';
        $ratings = array('starsbg' => 'star-o', 'stars' => 'star'); // store rating stars
        foreach ($ratings as $position => $stars):
            $out .= '<li class="rating-' . $position . '" style="' . ( $position == 'stars' ? 'width:' . round($generalscore['percentage']) / 10 * 10.6 . 'px' : '' ) . '"><div>';
            for ($i = 0; $i < 5; $i++): // 5 stars
                $out .= '<i class="fa fa-' . $stars . '"></i>';
            endfor;
            $out .= '</div></li>';
        endforeach;


        $out .= '</ul>'
                . '</div>' // additional ratings
                . '</aside>';
        if ($disable_pdf) {
            $out .= '<div><a download id="download-pdf" onclick="printSeoReport()">Print Report</a></div>';
        } else {
            $out .= '<div><a download id="download-pdf" onclick="hasSupport()">Download PDF Report</a></div>';
        }


        $out .= '</div>' // end factors-percent
                . '<div class="col can-float factors-score">' // factors score
                . '<p>Report for URL:</p>'
                . '<h2>' . $report_url . '</h2>'
                . '<ul>'
                . '<li class="col green"><i class="fa fa-check"></i><b class="factor-score">Successfully passed<span>' . $generalscore['factors']['green'] . '</span></b><div class="factorbar" style="width:' . ($generalscore['factors']['green'] * 100 / $score_raw_total) . '%"></div></li>'
                . '<li class="col orange"><i class="fa fa-minus"></i><b class="factor-score">Room for improvement<span>' . $generalscore['factors']['orange'] . '</span></b><div class="factorbar" style="width:' . ($generalscore['factors']['orange'] * 100 / $score_raw_total) . '%"></div></li>'
                . '<li class="col red"><i class="fa fa-times"></i><b class="factor-score">Errors<span>' . ( $generalscore['factors']['red'] + $generalscore['factors']['missing'] ) . '</span></b><div class="factorbar" style="width:' . ($generalscore['factors']['red'] * 100 / $score_raw_total) . '%"></div></li>'
                . '<div class="clearfix"></div>'
                . '</ul>'
                . '</div>' // end factors-score
                . '<div class="col can-float factors-site">' // site screen
                . '<div class="printscreen">'
                . '<img id="sitescreen" src="' . $thumb_URL . '">' // actual site screen
                . '</div>'
                . '</div>'; // end factors-site
        $out .= '<div class="clearfix"></div></div>'; // end score-table



        return $out;
    }

    /**
     * Render a single factor to html
     * @param object $report The report object
     * @param object $factor The full factor object
     * @param array $score The score for this factor for this report
     * @param boolean $is_even If the row is even or not
     * @param boolean $is_loggedin If the user is logged in
     * @return string the HTML of the rendered factor
     */
    public static function getFactorHTML($report, $factor, $score, $is_even = false, $is_loggedin = false) {
        $factor = (object) $factor;
        $out = "";
        switch ($score['model']['status']) {
            case 'MISSING':
            case 'RED': {
                    $status = 'times';
                    break;
                }
            case 'ORANGE': {
                    $status = 'minus';
                    break;
                }
            case 'GREEN': {
                    $status = 'check';
                    break;
                }
            case 'NEUTRAL': {
                    $status = 'info-circle';
                    break;
                }
            default: {
                    $status = 'question-circle';
                    break;
                }
        }
        $available = in_array($factor->id, $report->factors_available);



        $status = $is_loggedin ? $status : 'question-circle';
        $statuscolor = $is_loggedin ? strtolower($score['model']['status']) : '';




        $out .= '<section data-id="' . $factor->id . '" data-factorready="' . ($available ? '1' : '0') . '" class="erfactor ' . ($is_even ? 'even' : 'odd') . '" id="factor-' . $factor->id . '" data-status="' . $score['model']['status'] . '" '
                . 'onclick="' . ( $is_loggedin ? 'niceToggle(jQuery(this).attr(\'id\'))' : '' ) . '">'
                . '<div class="row">';
        $out .= '<div class="col can-float factor-name">';

        if ($available) {
            $out .= ( $status ? '<i class="erankerreporticonspacer fa fa-' . $status . ' ' . $statuscolor . '"></i>' : '' ) . $factor->friendly_name;
        } else {
            $out .= '<i class="erankerreporticonspacer fa fa-cog fa-spin"></i>' . $factor->friendly_name;
        }


        $out .= '<div class="ericonsrow">';



        $totalIcons = 3;


        $impact = $factor->correlation > 0.8 ? 3 : ($factor->correlation > 0.4 ? 2 : 1);
        $impactTitle = "Low Impact";
        if ($factor->correlation > 0.4) {
            $impactTitle = "Medium Impact";
        }
        if ($factor->correlation > 0.8) {
            $impactTitle = "High Impact";
        }

        $out .= '<div title="' . $impactTitle . '" class="erankertooltip errankerreportficons errankerreportficons-red">';
        for ($i = 0; $i < $totalIcons; $i++) {
            if ($i < $impact) {
                $out .= '<i class="fa fa-heart"></i>';
            } else {
                $out .= '<i class="fa fa-heart-o"></i>';
            }
        }
        $out .= '</div>';


        $dificulty = strcasecmp($factor->difficulty_level, "HARD") === 0 ? 3 : ( strcasecmp($factor->difficulty_level, "MEDIUM") === 0 ? 2 : 1);
        $dificultTitle = "Easy to Solve";
        if (strcasecmp($factor->difficulty_level, "MEDIUM") === 0) {
            $dificultTitle = "Hard to Solve";
        }
        if (strcasecmp($factor->difficulty_level, "HARD") === 0) {
            $dificultTitle = "Very Hard to Solve";
        }

        $out .= '<div title="' . $dificultTitle . '" class="erankertooltip errankerreportficons errankerreportficons-yellow" >';
        for ($i = 0; $i < $totalIcons; $i++) {
            if ($i < $dificulty) {
                $out .= '<i class="fa fa-star"></i>'; //fa-star-half-o
            } else {
                $out .= '<i class="fa fa-star-o"></i>';
            }
        }
        $out .= '</div>';



        $out .= '</div>';



        $out .= '</div>';

        $out .= '<div class="col can-float factor-data">';
        if ($available) {
            $out .= self::getFactorHTMLHelper($factor, $score['model']['model'], $score['data'], $score['model']['status'], $is_loggedin);
        } else {
            $out .= '<i class="fa fa-cog fa-spin"></i> Loading...';
        }
        $out .= '</div>';

        $out .= $is_loggedin ? '<div class="clearfix col factor-info"><p>' . html_entity_decode($score['model']['description']) . '</p></div>' : '';

        $out .= '<div class="clearfix"></div></div>' . '<i class="fa fa-plus expandtoggle"></i>' . '</section>';

        return $out;
    }

    public static function getFactorHTMLHelper($factor, $endModel, $data, $status, $is_loggedin) {
        $html = "";        
        $factor = self::objectToArray($factor);
        if ($is_loggedin || (!$is_loggedin && $factor['free'])) {
            $html = forward_static_call(array(self::NAME, 'gui' . ucfirst($factor['gui_type'])), html_entity_decode($endModel), $data);
        } else {
            $html = '<div class="has-blur"></div>';
        }
        return $html;
    }

    public static function guiDefault($endModel, $data) {
        return is_null($endModel) ? $data : $endModel;
    }

    public static function guiHeadings($endModel, $data) {
        $out = '';
        if (!is_null($data)) {
            $obj = (object) $data;
//            if (!empty($endModel)) {
//                //$out .= '<p>' . $endModel . '</p>';
//            }
            $out .= '<table class="report_headingtable">';
            for ($i = 1; $i <= 6; $i++) {
                $out .= $i == 1 ? '<tr class="report_headingtable_firstrow">' : '';
                $out .= '<th>&lt;H' . $i . '&gt;</th>';
                $out .= $i == 6 ? '<th>Total</th></tr>' : '';
            }
            for ($i = 1; $i <= 6; $i++) {
                $out .= $i == 1 ? '<tr>' : '';
                $hd_i = 'h' . $i;
                $out .= '<td>' . (isset($obj->$hd_i) ? $obj->$hd_i : "?") . '</td>';
                $out .= $i == 6 ? '<td>' . (isset($obj->total) ? $obj->total : "?") . '</td></tr>' : '';
            }
            $out .= '</table>';
            if (isset($obj->tags) && !empty($obj->tags)) {
                $out .="<div class='headings_taglist'>";
                $count = 1;
                foreach ($obj->tags as $aTag) {
                    $aTag = (object) $aTag;
                    $out .="<div class='headings_tagitem headings_taglist_" . $aTag->type . "'>";
                    $out .="<div class='headingtype'>&lt;" . strtoupper($aTag->type) . "&gt;</div>";
                    $out .="<div class='headingspacer'></div>";
                    $out .=strip_tags($aTag->text);
                    $out .="</div>";
                    if ($count++ == 10) {
                        $out .="<div class='headings_taglist_more' style='display:none'>"; // > 10 wrapper
                    }
                }
                if ($count >= 10) {
                    $out .="</div>"; // > 10 wrapper close
                    $out .="<a href='javascript:jQuery(\"#erreport .headings_taglist_more\").slideDown();jQuery(\"#erreport .headings_taglist_showmore\").hide();jQuery(\"#erreport .headings_taglist_showless\").show();' class='headings_taglist_showmore' style='display:block'><i class=\"fa fa-angle-down\"></i>  Show more</a>";
                    $out .="<a href='javascript:jQuery(\"#erreport .headings_taglist_more\").slideUp();jQuery(\"#erreport .headings_taglist_showmore\").show();jQuery(\"#erreport .headings_taglist_showless\").hide();' class='headings_taglist_showless' style='display:none'><i class=\"fa fa-angle-up\"></i> Show less</a>";
                }
                $out .="</div>";
            }
        }


        return empty($out) ? false : '<div class="headings-style">' . $out . '</div>';
    }

    /**
     * Get the ajax report object
     * @param object $report the report object
     * @param array $reportFactors all factors object from report
     * @param object $score the report scores
     * @param string $factorsList string list the factors (comma)
     * @param boolean $is_userloggedin if the user is loggedin;
     */
    public static function ajaxReport($report, $reportFactors, $score, $factorsList, $is_userloggedin) {
        $ajax_factors = explode(',', trim($factorsList));
        $reportFactors = self::objectToArray($reportFactors);
        $output = array();
        //Add the base report score
        $output['score'] = $score->score;
        $output['status'] = $report->status;

        //Add on the output data, if the factor is avaiable, factor name, status and the HTML
        if (!empty($ajax_factors)) {
            foreach ($ajax_factors as $ajax_factor_id) {
                if (!in_array($ajax_factor_id, $report->factors_available)) {
                    continue;
                }
                $output[$ajax_factor_id] = array();
                $scoreobj = self::objectToArray($score->$ajax_factor_id);
                $output[$ajax_factor_id]['friendly_name'] = $scoreobj['model']['friendly_name'];
                $output[$ajax_factor_id]['status'] = $scoreobj['model']['status'];
                $output[$ajax_factor_id]['html'] = self::getFactorHTMLHelper($reportFactors[$ajax_factor_id], $scoreobj["model"]["model"], $scoreobj["data"], $scoreobj["model"]["status"], $is_userloggedin);
            }
        }
        return $output;
    }

}
