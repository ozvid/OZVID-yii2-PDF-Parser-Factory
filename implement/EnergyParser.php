<?php

/**
 *@copyright : OZVID Technologies Pvt. Ltd. < www.ozvid.com >
 *@author	 : Shiv Charan Panjeta < shiv@ozvid.com >
 */
namespace app\billparser\implement;

use app\billparser\TBillParser;
use Exception;
use yii\helpers\VarDumper;

class EnergyParser extends TBillParser
{

    /**
     * Find Energy Signature
     *
     * @param EnergyParser $content
     * @return boolean
     */
    public static function getSignature($content)
    {
        if (preg_match('/www.ener/i', $content)) { 
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
        if (preg_match('/Electricity account summary(.*)/', $this->content, $matches)) {
           
            
            $split = explode("to", $matches[1]);
            
            $billStart = trim(str_replace(":", "", trim($split[0])));
            
            return $billStart;
        }
    }

    public function getBillEndDate()
    {
        if (preg_match('/Electricity account summary(.*)/', $this->content, $matches)) {
            
           
            $split = explode("to", $matches[1]);
            $billEnd = trim(str_replace(":", "", trim($split[1])));
            return $billEnd;
        }
    }

    /**
     * Peak usage
     */
    public function getPeakUsage()
    {
        if (preg_match('/  Peak Energy(.*)/s', $this->content, $matches)) {
            $split = explode("c/kWh", $matches[0]);
            $peakUsage = trim(str_replace("Peak Energy", "", trim($split[0])));
            return $peakUsage;
        }
    }

    /**
     * Off-peak usage
     */
    public function getOffPeak()
    {
        if (preg_match('/Off Peak Energy(.*)/s', $this->content, $matches)) {
            if (preg_match('/Off Peak Energy(.*)/s', $matches[1], $energies)) {
                $split = explode("c/kWh", $energies[1]);
                $offPeak = trim(str_replace("Peak Energy", "", trim($split[0])));
                return $offPeak;
            }
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
    {}
}

?>