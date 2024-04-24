<?php

    function logErrors($error)
    {
        error_log($error, 3, '/var/log/apache2/sample_error.log');
    }

    set_error_handler('logErrors');

?>