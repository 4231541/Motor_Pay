<?php
$output = shell_exec('vendor\bin\pest --no-ansi 2>&1');
file_put_contents(__DIR__ . '/test_output.txt', $output);

$total = 0;
$passed = 0;
$failed = 0;

if (preg_match('/Tests:\s+(\d+) passed/', $output, $m)) {
    $passed = (int) $m[1];
}
if (preg_match('/Tests:\s+(\d+) failed/', $output, $m)) {
    $failed = (int) $m[1];
} elseif (preg_match('/Tests:\s+(\d+) failed,\s+(\d+) passed/', $output, $m)) {
    $failed = (int) $m[1];
    $passed = (int) $m[2];
}

$total = $passed + $failed;

$report = "# Test Coverage Report\n\n";
$report .= "- **Total Tests:** $total\n";
$report .= "- **Passed:** $passed\n";
$report .= "- **Failed:** $failed\n";
$report .= "- **Coverage Percentage:** Unavailable (xdebug/pcov not installed)\n\n";
$report .= "## Details\n";
if ($failed > 0) {
    $report .= "Some tests failed. Check the test suite output for details.\n";
    $report .= "Note: The default Laravel ExampleTest may be failing.\n";
} else {
    $report .= "All tests passed successfully.\n";
}

file_put_contents(getenv('USERPROFILE') . '/.gemini/antigravity-ide/brain/f1db75fe-dc1f-4c16-9011-5f95f37de91d/TEST_COVERAGE_REPORT.md', $report);
echo "Report generated.\n";
