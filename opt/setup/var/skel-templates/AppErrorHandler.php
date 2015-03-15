<?php
SyL_Loader::core('ErrorHandler.Web');

class AppErrorHandler extends SyL_ErrorHandlerWeb
{
    private static $display_source_line = 9;

    protected function handleNotFoundError(Exception $e)
    {
        header('HTTP/1.0 404 Not Found');
        header('Content-Type: text/html; charset=' . SYL_ENCODE_INTERNAL);

        include_once SYL_APP_DIR . '/templates/_App/error_template_not_found.html';
    }

    protected function handleError(Exception $e)
    {
        $error_message = self::getErrorMessage($e);
        $error_trace = self::getTrace($e);
        $error_lines = self::getLines($error_trace);

        header('HTTP/1.0 500 Internal Server Error');
        header('Content-Type: text/html; charset=' . SYL_ENCODE_INTERNAL);

        include_once SYL_APP_DIR . '/templates/_App/error_template_server_error.html';
    }

    private static function getLines(array $error_trace)
    {
        $error_lines = array();

        foreach ($error_trace as $trace) {
            $error_line = $trace['line'];
            $error_file = $trace['file'];
            if (is_numeric($error_line) && file_exists($error_file)) {
                $error_half_line = floor(self::$display_source_line / 2);
                $start_line = 1;
                $crit_line  = 1;
                if (($error_line - $error_half_line) > 1) {
                    $start_line = $error_line - $error_half_line;
                }
                $i = 1;
                $tmp_lines = array();
                foreach (file($error_file) as $line => $source) {
                    if (($line + 1) >= $start_line) {
                        if (($line + 1) == $error_line) {
                            $tmp_lines[] = '<span style="color: #FF0000">Line ' . ($line + 1) . ': ' . htmlentities($source, ENT_QUOTES, SYL_ENCODE_INTERNAL) . '</span>';
                        } else {
                            $tmp_lines[] = 'Line ' . ($line + 1) . ': ' . htmlentities($source, ENT_QUOTES, SYL_ENCODE_INTERNAL);
                        }
                        if ($i >= self::$display_source_line) {
                            break;
                        }
                        $i++;
                    }
                }
                $error_lines[$trace['no']] = implode('', $tmp_lines);
            } else {
                $error_lines[$trace['no']] = '';
            }
        }

        return $error_lines;
    }
}

