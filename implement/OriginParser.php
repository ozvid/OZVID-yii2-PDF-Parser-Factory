<?php
/**
 *@copyright : OZVID Technologies Pvt. Ltd. < www.ozvid.com >
 *@author    : Shiv Charan Panjeta < shiv@ozvid.com >
 */

namespace app\billparser\implement;

use app\billparser\TBillParser;
use Exception;
use yii\helpers\VarDumper;

class OriginParser extends TBillParser
{

    /**
     * Find Origin Signature
     *
     * @param OriginParser $content
     * @return boolean
     */
    public static function getSignature($content)
    {
        if (preg_match('/origi', $content)) {
            
            return true;
        }
        return false;
    }

    public function parse($content)
    {
        $this->content = $content;
        
        if ($content == null) {
            self::log("Invalid Data");
            return false;
        }
        
        $result = [];
        
        try {
            $pdfText = str_replace(':<br />', ':', $content);
            $pdfText = str_replace(":" . PHP_EOL, ':', $pdfText);
            // $pdfText = my_strip_tags($pdfText);
            
            $this->result['content'][] = $pdfText;
        } catch (Exception $e) {
            self::log($e->getMessage());
        }
        // VarDumper::dump($pdfText);
        return true;
    }

    public function getBillStartDate()
    {
        if (preg_match('/Period(.*)/s', $this->content, $matches)) {
            $contentIndex = explode(' ', trim($matches[1]));
            $startDate = str_replace("'", "", $contentIndex[0]);
            return $startDate;
        }
    }

    public function getBillEndDate()
    {
        if (preg_match('/Period(.*)/s', $this->content, $matches)) {
            $contentIndex = explode(' ', trim($matches[1]));
            return $contentIndex[2];
        }
        // VarDumper::dump($this->content);
    }

    public function getPostCode()
    {
        if (preg_match('/Due Date(.*)/s', $this->content, $matches)) {
            if (! empty($matches[1])) {
                $address = explode(',', $matches[1]);
                
                if (preg_match('!\d+!', $address[1], $matches1)) {
                    $contentIndex = $matches1[0];
                    
                    return $contentIndex;
                }
            }
        }
        // VarDumper::dump($this->content);
    }

    /**
     * Peak usage
     */
    public function getPeakUsage()
    {
        if (preg_match('/Peak(.*)/', $this->content, $matches)) {
            $contentIndex = explode(' ', trim($matches[1]));
            $peakUsage = str_replace("'", "", $contentIndex[0]);
            return $peakUsage;
        }
    }

    /**
     * Off-peak usage
     */
    public function getOffPeak()
    {
        if (preg_match('/Off Peak(.*)/', $this->content, $matches)) {
            $contentIndex = explode(' ', trim($matches[1]));
            $offPeak = str_replace("'", "", $contentIndex[0]);
            return $offPeak;
        }
    }

    /**
     * Shoulder 1 usage (if any)
     */
    public function getShoulderOneUsage()
    {}

    /**
     * Shoulder 2 usage (if any)
     */
    public function getShoulderTwoUsage()
    {}

    /**
     * Controlled load usage
     */
    public function getControlledLoadUsage()
    {
        if (preg_match('/Usage(.*)/s', $this->content, $matches)) {
            $contentIndex = explode(' ', trim($matches[1]));
            $loadUsage = str_replace("'", "", $contentIndex[0]);
            return $loadUsage;
        }
    }
}

?>