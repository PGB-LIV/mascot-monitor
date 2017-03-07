<?php
/**
 * Copyright (c) University of Liverpool. All rights reserved.
 * @author Andrew Collins
 */
namespace pgb_liv\mascot_monitor;

use pgb_liv\php_ms\Search\MascotSearch;

/**
 *
 * @author Andrew Collins
 */
class MascotMonitor
{

    const PROCESS_DIR = '.mascotMonitor';

    const JOB_DIR = 'jobs';

    const MAIL_DIR = 'mail';

    private $mascot;

    public function __construct(MascotSearch $mascot)
    {
        $this->mascot = $mascot;
    }

    public function saveResults($localPath)
    {
        $metaPath = $localPath . '/' . MascotMonitor::PROCESS_DIR;
        if (! is_dir($metaPath)) {
            mkdir($metaPath);
            mkdir($metaPath . '/' . MascotMonitor::JOB_DIR);
        }
        
        $results = $this->mascot->getSearches(100);
        krsort($results);
        
        foreach ($results as $result) {
            if ($result['status'] != 'User read res') {
                continue;
            }
            
            if (file_exists($metaPath . '/' . MascotMonitor::JOB_DIR . '/' . $result['job'])) {
                continue;
            }
            
            touch($metaPath . '/' . MascotMonitor::JOB_DIR . '/' . $result['job'] . '.processing');
            $this->saveResult($localPath, $result);
            
            touch($metaPath . '/' . MascotMonitor::JOB_DIR . '/' . $result['job']);
            unlink($metaPath . '/' . MascotMonitor::JOB_DIR . '/' . $result['job'] . '.processing');
        }
    }

    private function saveResult($localPath, $result)
    {
        $metaPath = $localPath . '/' . MascotMonitor::PROCESS_DIR;
        $username = $result['userid'];
        
        if (strlen(trim($result['username'])) > 0) {
            $username = trim($result['username']);
        }
        
        $savePath = $localPath . '/' . $username;
        
        if (! is_dir($savePath)) {
            mkdir($savePath);
        }
        
        $data = $this->mascot->getXml($result['filename']);
        
        $savePath .= '/' . $data['name'];
        
        copy($data['path'], $savePath);
        
        if (file_exists($metaPath . '/' . MascotMonitor::MAIL_DIR . '/' . $result['userid'])) {
            $this->notifyUser($result['email'], $result['job'], 
                PUBLIC_SAVE_PATH . '/results/' . $username . '/' . $data['name']);
        }
    }

    private function notifyUser($to, $jobId, $savePath)
    {
        $to = trim($to);
        if (strlen($to) == 0) {
            return;
        }
        
        $subject = '[MascotMonitor] Job #' . $jobId . ' Complete';
        $message = 'Mascot job complete. You can collect the XML from: ' . $savePath;
        $headers = 'From: ' . EMAIL_FROM . "\r\n" . 'X-Mailer: PHP/' . phpversion();
        
        mail($to, $subject, $message, $headers);
    }
}