<?php

class Fedex_Common {

    public static function printSuccess($client, $response) {
        echo '<h2>Transaction Successful</h2>';
        echo "\n";
        self::printRequestResponse($client);
    }

    public static function printRequestResponse($client) {
//        echo '<h2>Request</h2>' . "\n";
//        echo '<pre>' . htmlspecialchars($client->__getLastRequest()) . '</pre>';
//        echo "\n";
//        echo '<h2>Response</h2>' . "\n";
//        echo '<pre>' . htmlspecialchars($client->__getLastResponse()) . '</pre>';
//        echo "\n";
    }

    public static function printFault($exception, $client) {
        echo '<h2>Fault</h2>' . "<br>\n";
        echo "<b>Code:</b>{$exception->faultcode}<br>\n";
        echo "<b>String:</b>{$exception->faultstring}<br>\n";
        self::writeToLog($client);
    }

    /**
     * SOAP request/response logging to a file
     */
    public static function writeToLog($client) {
        $log_file = 'application/files/fedex/log/fedextransactions.log';
        if (!$logfile = fopen($log_file, "a")) {
            error_func("Cannot open " . $log_file . " file.\n", 0);
            exit(1);
        }
        fwrite($logfile, sprintf("\r%s:- %s", date("D M j G:i:s T Y"), $client->__getLastRequest() . "\n\n" . $client->__getLastResponse()));
    }

    public static function setEndpoint($var) {
        if ($var == 'changeEndpoint')
            Return false;
        if ($var == 'endpoint')
            Return '';
    }

    public static function printNotifications($notes) {
        foreach ($notes as $noteKey => $note) {
            if (is_string($note)) {
//                if ($noteKey == 'Message' || $noteKey == 'Code') {
                    echo $noteKey . ': ' . $note . '<br>';
//                }
            } else {
                self::printNotifications($note);
            }
        }
        echo '<br />';
    }

    public static function printError($client, $response) {
        self::printNotifications($response->Notifications);
        self::printRequestResponse($client, $response);
    }

    public static function trackDetails($details, $spacer) {
        foreach ($details as $key => $value) {
            if (is_array($value) || is_object($value)) {
                $newSpacer = $spacer . '&nbsp;&nbsp;&nbsp;&nbsp;';
                echo '<tr><td>' . $spacer . $key . '</td><td>&nbsp;</td></tr>';
                self::trackDetails($value, $newSpacer);
            } elseif (empty($value)) {
                echo '<tr><td>' . $spacer . $key . '</td><td>&nbsp;</td></tr>';
            } else {
                echo '<tr><td>' . $spacer . $key . '</td><td>' . $value . '</td></tr>';
            }
        }
    }

    public static function printString($value, $spacer, $description) {
        echo '<tr><td>' . $description . '</td><td>' . $value . '</td></tr>';
    }

    public static function locationDetails($details, $spacer) {
        foreach ($details as $key => $value) {
            if (is_array($value) || is_object($value)) {
                $newSpacer = $spacer . '&nbsp;&nbsp;&nbsp;&nbsp;';
                echo '<tr><td>' . '</td><td>' . $spacer . $key . '</td><td>&nbsp;</td></tr>';
                self::locationDetails($value, $newSpacer);
            } elseif (empty($value)) {
                if (!is_numeric($value)) {
                    echo '<tr><td>' . '</td><td>' . $spacer . $key . '</td><td>&nbsp;</td></tr>';
                }
            } else {
                echo '<tr><td>' . '</td><td>' . $spacer . $key . '</td><td>' . $value . '</td></tr>';
            }
        }
    }

}