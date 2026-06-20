<?php
$script_path = __DIR__ . '/ML/fertilizer_recommendation/frecommend_fertilizer.py';
$command = sprintf(
    'python "%s" %s %s %s %s %s %s %s %s 2>&1',
    $script_path,
    escapeshellarg(37),
    escapeshellarg(0),
    escapeshellarg(0),
    escapeshellarg(26),
    escapeshellarg(52),
    escapeshellarg(38),
    escapeshellarg("Loamy"),
    escapeshellarg("Maize")
);

echo "Command: " . $command . "<br>";
$output = shell_exec($command);
echo "Output: " . $output;
?>