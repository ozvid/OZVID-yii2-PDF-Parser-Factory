<?php
/**
 *@copyright : OZVID Technologies Pvt. Ltd. < www.ozvid.com >
 *@author    : Shiv Charan Panjeta < shiv@ozvid.com >
 */

namespace app\billparser;

use Exception;
use yii\helpers\VarDumper;

class TBillParser
{

    protected $content;

    public function __construct()
    {}

    /**
     *
     * @param
     *            Bill parse $file
     *            
     */
    public static function createFactory($file)
    {
        $knownParsers = [
            "app\billparser\implement\OriginParser",
            "app\billparser\implement\EnergyParser"
        ];
        
        $content = self::getFileText($file);
        
        // check Signature
        
        $parser = null;
        
        foreach ($knownParsers as $p) {
            if ($p::getSignature($content)) {
                $parser = new $p();
                // VarDumper::dump($parser);
                if ($parser->parse($content))
                    break;
            }
        }
        
        return $parser;
    }

    public static function getSignature($content)
    {
        return false;
    }

    public function parse($content)
    {
        if ($content == null) {
            self::log("Invalid Data");
            return false;
        }
        
        $this->content = $content;
        
        $result = [];
        
        try {} catch (Exception $e) {
            self::log($e->getMessage());
        }
        return false;
    }

    public function getBillStartDate()
    {}

    /**
     * Peak usage
     */
    public function getPeakUsage()
    {}

    /**
     * Off-peak usage
     */
    public function getOffPeakUsage()
    {}

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

    /**
     * Convert a PDF into text.
     *
     * @param string $filePath
     *            The filename to extract the data from.
     * @return string The extracted text from the PDF
     */
    public static function getFileText($filePath)
    {
        $resumeText = "";
        if (shell_exec('which textract')) {
            $outtext = shell_exec('textract ' . escapeshellarg($this->filePath));
            return $outtext;
        }
        $mime = @\yii\helpers\FileHelper::getMimeType($filePath);
        $resumeText = null;
        try {
            if ($mime == 'application/pdf') {
                if (shell_exec('which pdftotext')) {
                    $outfile = $filePath . '.txt';
                    $cmd = "pdftotext -layout '$filePath' '$outfile'";
                    // echo $cmd . PHP_EOL;
                    shell_exec($cmd);
                    $resumeText = file_get_contents($outfile);
                    unlink($outfile);
                }
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        
        // VarDumper::dump($resumeText);
        return $resumeText;
    }
}