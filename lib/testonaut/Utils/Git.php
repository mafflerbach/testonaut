<?php

namespace testonaut\Utils;

class Git {

    private $gitDir;
    private $bashDir;

    /**
     * 
     * @param type $dir
     */
    public function __construct($dir, $bashDir) {
        $this->gitDir = $dir;
        $this->bashDir = $bashDir;
    }

    /**
     * 
     * @param string $message
     * @param string $email
     * @param string $name
     * @return type
     */
    public function commit($message = '', $email = '', $name = '') {
        if ($message == '') {
            $message = "'" . date('l jS \of F Y h:i:s A') . "'";
        }
        if (!in_array('user.email', $this->config())) {
            $email = "'" . $email . "'";
            $name = "'" . $name . "'";
            $command = $this->bashDir . '/gitWrapper.sh commit ' . $this->gitDir . ' ' . $message . ' ' . $email . ' ' . $name;
        } else {
            $command = $this->bashDir . '/gitWrapper.sh commit ' . $this->gitDir . ' ' . $message;
        }
        exec($command, $output);
        $outputStr = $this->getTerminalOutput($output, 'Executing Git commit');
        
        return $outputStr;
    }

    /**
     * 
     * @return type
     */
    public function init() {
        $command = $this->bashDir . '/gitWrapper.sh init ' . $this->gitDir;
        exec($command, $output);
        $outputStr = $this->getTerminalOutput($output, 'Executing Git init');
        
        return $outputStr;
    }

    /**
     * 
     * @return type
     */
    public function log() {
        $command = $this->bashDir . '/gitWrapper.sh log ' . $this->gitDir;
        exec($command, $output);
        $outputStr = $this->getTerminalOutput($output, 'Executing Git log');
        
        return $outputStr;
    }

    /**
     * 
     * @return type
     */
    public function config() {
        $command = $this->bashDir . '/gitWrapper.sh listconf ' . $this->gitDir;
        exec($command, $output);
        $outputStr = $this->getTerminalOutput($output, 'Executing Git listconf');
        
        return $outputStr;
    }

    /**
     * 
     * @param type $revision
     * @return type
     */
    public function revert($revision) {
        $command = $this->bashDir . '/gitWrapper.sh revert ' . $this->gitDir . ' ' . $revision;
        exec($command, $output);
        $outputStr = $this->getTerminalOutput($output, 'Executing Git revert to '. $revision);
        
        return $outputStr;
    }

    /**
     * 
     * @param type $revision
     * @return type
     */
    public function pull() {
        $command = $this->bashDir . '/gitWrapper.sh pull ' . $this->gitDir;
        exec($command, $output);
        $outputStr = $this->getTerminalOutput($output, 'Executing Git Pull');
        return $outputStr;
    }

    /**
     * 
     * @param type $rev1
     * @param type $rev2
     * @return type
     */
    public function diff($rev1, $rev2) {
        $command = $this->bashDir . '/gitWrapper.sh diff ' . $this->gitDir . ' ' . $rev1 . ' ' . $rev2;
        $output = shell_exec($command);
        $outputStr = $this->getTerminalOutput($output, 'Executing Git diff');
        return $outputStr;
    }

    private function getTerminalOutput($output, $barMessage) {
        $outputStr = $this->getDecoratedBarString("Starting " . $barMessage);
        $outputStr .= $this->outputArrayToString($output);
        $outputStr .= $this->getDecoratedBarString($barMessage . ' Ended');
        return $outputStr;
    }

    private function outputArrayToString($output) {
        $outputStr = '';
        for ($i = 0; $i < count($output); $i++) {
            $outputStr .= $output[$i] . "\n";
        }
        return $outputStr;
    }

    private function getDecoratedBarString($content) {
        return "\n" . 
                '===================================================================
                ' . $content . "\n".
                '===================================================================' . "\n\n";
    }

}
