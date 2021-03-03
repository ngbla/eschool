<?php

namespace Core;

/**
 * Error and exception handler
 *
 * PHP version 7.0
 */
class Error
{

    /**
     * Error handler. Convert all errors to Exceptions by throwing an ErrorException.
     *
     * @param int $level  Error level
     * @param string $message  Error message
     * @param string $file  Filename the error was raised in
     * @param int $line  Line number in the file
     *
     * @return void
     */
    public static function errorHandler($level, $message, $file, $line)
    {
        if (error_reporting() !== 0) {  // to keep the @ operator working
            throw new \ErrorException($message, 0, $level, $file, $line);
        }
    }

    /**
     * Exception handler.
     *
     * @param Exception $exception  The exception
     *
     * @return void
     */
    public static function exceptionHandler($exception)
    {
        // Code is 404 (not found) or 500 (general error)
        $code = $exception->getCode();
        $codesave=$code;
        if ($code != 404) {
            $code = 500;
        }
        http_response_code($code);

        if (\App\Config::SHOW_ERRORS) {
            echo "<h1>Fatal error</h1>";
            echo "<p>Uncaught exception: '" . get_class($exception) . "'</p>";
            echo "<p>Message: '" . $exception->getMessage() . "'</p>";
            echo "<p>Stack trace:<pre>" . $exception->getTraceAsString() . "</pre></p>";
            echo "<p>Thrown in '" . $exception->getFile() . "' on line " . $exception->getLine() . "</p>";

            $message = "Uncaught exception: '" . get_class($exception) . "'";
            $message .= " with message '" . $exception->getMessage() . "'";
            $message .= "\nStack trace: " . $exception->getTraceAsString();
            $message .= "\nThrown in '" . $exception->getFile() . "' on line " . $exception->getLine();


        } else {
            $log = dirname(__DIR__) . '/logs/' . date('Y-m-d') . '.txt';
            ini_set('error_log', $log);

            $message = "Uncaught exception: '" . get_class($exception) . "'";
            $message .= " with message '" . $exception->getMessage() . "'";
            $message .= "\nStack trace: " . $exception->getTraceAsString();
            $message .= "\nThrown in '" . $exception->getFile() . "' on line " . $exception->getLine();

            //error_log($message);

            $tableinfo['liens']=$_SERVER['HTTP_HOST'].'/public/index.php';
            //var_dump($_SERVER);
            View::renderTemplate("$code.html",$tableinfo);
        }

        //var_dump($message);
        //$arr = get_defined_vars();
        // Affiche toutes les clés disponibles du tableau de variables
            //$keserror=array_keys(get_defined_vars());
        //var_dump($keserror);
        //var_dump($exception->getTrace());
        //var_dump($code,($exception->getTrace())[0]);
        if ($code != 404) {
            # code...

            $var_test = 0;
            if (isset(($exception->getTrace())[0])) {
                $tbeexetrace=($exception->getTrace())[0];
                if (isset($tbeexetrace['args'][4])) {
                    $infosuererror=$tbeexetrace['args'][4];
                    if (isset($infosuererror['tabdatas'])) {
                        $err_idpers= intval($infosuererror['tabdatas']['id_pers_personne']) ;
                        $err_iduniv= intval($infosuererror['tabdatas']['fk_iduniv']) ;
                        $var_test = 1;
                    }
                }
            }

            if ($var_test != 1) {
                $err_idpers= 0 ;
                $err_iduniv= 1 ;
            }
            $table="log_error";
            $tb_conditions=[];
            $tb_infos=[];
            $tb_conditions['code_error']=$codesave;
            $tb_conditions['message_error']=htmlspecialchars($message);
            $tb_conditions['fk_idpers']=$err_idpers;
            $tb_conditions['fk_iduniv']=$err_iduniv;
            $tb_infos=$tb_conditions;
            \App\Models\Model_public::set_insertSQL($table,$tb_infos, $tb_conditions);
            //var_dump($infosuererror['tabdatas']);
            //var_dump($err_idpers,$err_iduniv);
            //var_dump($exception);
                //$exceptionif=$arr['exception'];
            //var_dump($exceptionif->);
            //var_dump($arr['exception'][0]);
                //$keyexception=array_keys($arr['exception']);
            //var_dump($arr['code']);
            //var_dump($arr['log']);
            //var_dump($arr['message']);
            //var_dump($arr['arr']);
            //var_dump($arr['[trace:Exception:private']);
        }
    }
}
