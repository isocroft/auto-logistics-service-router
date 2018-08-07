<?php
/*
 * I, hereby attests that this code is not originally
 * mine. It has been modified for the purposes of this project and was sourced 
 * from an article on DZONE website.
 *
 * Also feel free to fork this repo, modify this code and PR to the repo if 
 * you feel like sharing your modifications ;)
 */
 
$output = array();
$js_output = array();
$css_output = array();
$rc     = 0;
// trying to make sure that the Git Tree isn't empty (working directory inclusive)
exec('git rev-parse --verify HEAD', $output, $rc); // 2> /dev/null - Causes error in Windows and Linux
if ($rc == 0)  $against = 'HEAD';
else  $against = '4b825dc642cb6eb9a060e54bf8d69288fbee4904'; // This is the HEAD hash for the ENTIRE Git Tree
         
// This Git command executed below, grabs all files that have been staged for commit as a result of { git add . }
// command or {git rm [--cached] <file> } command executed by the repo maintainer/owner
// exec("git diff-index --cached --name-only {$against}", $output, $rc);
exec("git diff-index --cached {$against} | grep '.php' | cut -f2", $output, $rc);

$needle      = '/\.ph(tml|p)$/'; // we only need to grab php files for PHP linting (by extension)
$exit_status = 0; // if linting is successful, exit with status 0, else exit with status 1
$files_with_issues = array(); // stores all file that have issues with liniting or static analysis

fwrite(STDOUT, PHP_EOL . "\033[0;32m Now Running Pre-Commit Hook - Auto Logitics Service Router Project \033[0m" . PHP_EOL . PHP_EOL);
fwrite(STDOUT, "\033[0;32m Please Wait... \033[0m" . PHP_EOL . PHP_EOL . PHP_EOL);

## {vardump_die_php}
function vardump_die_php(){
    global $output;
    global $rc;
    $files = array();
    $lint_output = array();
    
    foreach ($output as $file) {
        if (!preg_match($needle, $file)) {
            // only check php files
            continue;
        }
        $files[] = $file;
        $lint_output = array();
        $rc = 0;
        exec('grep --ignore-case --quiet --regexp="var_dump\|die" ' . escapeshellarg(realpath($file)), $lint_output, $rc);
        if($rc == 0){
            fwrite(STDOUT, ("\033[0;32m OK: no {var_dump} AND/OR {die} php command found in $file\033[0m" . PHP_EOL));
            continue;
        }
        if($rc == 1){
            fwrite(STDOUT, ("\033[0;31m  ERROR: var_dump AND/OR echo php command found in $file \033[0m" . PHP_EOL)); // writing to standard output (STDOUT)
            fwrite(STDOUT, ("\033[0;33m " . count($files) . " PHP Files Reviewed so far..." . PHP_EOL . PHP_EOL));
            $exit_status = 1;
            exit($exit_status);
        }
    } 
    fwrite(STDOUT, ("\033[0;32m " . count($files) . " PHP Files Reviewed Successfully... [VARDUMP_DIE_PHP]\033[0m" . PHP_EOL . PHP_EOL));   
}

# {fix_cs_php}
function fix_cs_php(){
    fwrite(STDOUT, '> Running PHP CS Fixer Routine... (AutoServiceRouter) ' . PHP_EOL . PHP_EOL);
    global $output;
    global $exit_status;
    global $rc;
    global $needle;
    global $files_with_issues;
    foreach ($output as $file) {
        if (!preg_match($needle, $file)) {
                // only check php files
                continue;
        }
        $add_output = array();
        $fix_output = array();
        $rc              = 0;
        $_rc             = 0;
        if(array_search($file, $files_with_issues, TRUE) === FALSE){
            exec("./vendor/bin/php-cs-fixer fix --config=.php_cs --verbose {$file}", $fix_output, $_rc);
            exec("git add {$file}", $add_output, $rc);
            if ($rc == 0) {
                        continue;
            }
        }
    }
    fwrite(STDOUT, "\033[0;32m > Ended PHP CS Fixer Routine - Success \033[0m" . PHP_EOL . PHP_EOL);
}

# Use composer to install like so: [composer require --dev phpstan/phpstan]

function statically_analyse_php($level = 2, $bootstrap_file_path = './vendor/autoload.php'){
    fwrite(STDOUT, '> Running PHP Static Ananlysis Routine... (AutoServiceRouter) ' . PHP_EOL . PHP_EOL);
	
	if(!is_null($bootstrap_file_path)){
		$arg = "--autoload-file={$bootstrap_file_path} ";
	}else{
		$arg = "";
	}
    global $output;
    global $exit_status;
    global $rc;
    global $needle;
    global $files_with_issues;
    $has_errors = array();
	foreach ($output as $file) {
        	if (!preg_match($needle, $file)) {
            		// only check php files
                    continue;
        	}
            $reset_output = array();
        	$analysis_output = array();
        	$rc              = 0;
            $_rc             = 0;
        	exec('phpstan analyse '. escapeshellarg("{$arg}-l {$level} {$file}"), $analysis_output, $rc);
        	
            if ($rc == 0) {
            		continue;
        	}else{
                array_push($has_errors, array('file' => $file, 'details' => $analysis_output));
                if(array_search($file, $files_with_issues, TRUE) === FALSE){
                    array_push($files_with_issues, $file);
                    exec('git reset '. escapeshellarg($file), $reset_output, $_rc);
                    fwrite(STDOUT, "\033[0;31m GIT: Unstaging File: ". basename($file, ".php") . " Unstaging Now... \033[0m" . PHP_EOL . PHP_EOL);
                }else{
                    fwrite(STDOUT, "\033[0;31m GIT: Unstaged File: ". basename($file, ".php") . " Prevoiusly Unstaged ... \033[0m" . PHP_EOL . PHP_EOL);
                }
                fwrite(STDOUT, PHP_EOL . "\033[0;31m PHP: File Contains Syntax Errors! Please Fix... \033[0m" . PHP_EOL);
            }
	}
    if(count($has_errors) > 0){
            $trace = "";
            foreach ($has_errors as $error) {
                $trace .= (implode(PHP_EOL . PHP_EOL, $error));
            }
            fwrite(STDOUT,  $trace . PHP_EOL . PHP_EOL); // writing to standard output (STDOUT)
            fwrite(STDOUT, "\033[0;31m > Ended PHP Static Ananlysis Routine - Failure \033[0m" . PHP_EOL);
            $exit_status = 1;
            exit($exit_status);
    }
    fwrite(STDOUT, "\033[0;32m > Ended PHP Static Ananlysis Routine - Success \033[0m" . PHP_EOL);
    // exit($exit_status);
}

## {test_php} is calling the PHPUnit logic to run unit tests
function test_php(){
    fwrite(STDOUT, '> Running PHP Unit Tests... (AutoServiceRouter)' . PHP_EOL);
    $test_output = array();
    global $exit_status;
    global $rc;
    exec('phpunit', $test_output, $rc);
    if($rc == 0){
        fwrite(STDOUT, "\033[0;32m > Ended PHP Unit Tests - Success \033[0m" . PHP_EOL . PHP_EOL);
    }else{
        fwrite(STDOUT, (implode(PHP_EOL, $test_output)) . PHP_EOL);
        fwrite(STDOUT, "\033[0;31m > Ended PHP Unit Tests - Failure \033[0m" . PHP_EOL . PHP_EOL);
        $exit_status = 1;
        exit($exit_status);
    }
}
## {lint_php} is calling PHP Interpreter linter directly
function lint_php(){
    fwrite(STDOUT, '> Running PHP Linting Routine... (AutoServiceRouter)' . PHP_EOL . PHP_EOL);
    global $output;
    global $exit_status;
    global $rc;
    global $files_with_issues;
    global $needle;
    $has_errors = array();
    foreach ($output as $file) {
        if (!preg_match($needle, $file)) {
            // only check php files
            continue;
        }
        $reset_output = array();
        $lint_output = array();
        $rc              = 0;
        $_rc             = 0;
        exec('php -l '. escapeshellarg($file), $lint_output, $rc);
        
        if ($rc == 0) {
            continue;
        }else{
            array_push($has_errors, array('file' => $file, 'details' => $lint_output));
            if(array_search($file, $files_with_issues, TRUE) === FALSE){
                    array_push($files_with_issues, $file);
                    exec('git reset '. escapeshellarg($file), $reset_output, $_rc);
                    fwrite(STDOUT, "\033[0;31m GIT: Unstaging File:". basename($file, ".php") . " Unstaging Now... \033[0m" . PHP_EOL . PHP_EOL);
            }else{
                fwrite(STDOUT, "\033[0;31m GIT: Unstaged File: ". basename($file, ".php") . " Prevoiusly Unstaged... \033[0m" . PHP_EOL . PHP_EOL);
            }
            fwrite(STDOUT, PHP_EOL . "\033[0;31m PHP: File Contains Syntax Errors!" . PHP_EOL . PHP_EOL);
        }
    }
    if(count($has_errors) > 0){
            $trace = "";
            foreach ($has_errors as $error) {
                $trace .= (implode(PHP_EOL . PHP_EOL, $error));
            }
            fwrite(STDOUT,  $trace . PHP_EOL . PHP_EOL); // writing to standard output (STDOUT)
            fwrite(STDOUT, "\033[0;31m > Ended PHP Linting Routine - Failure \033[0m " . PHP_EOL . PHP_EOL);
            $exit_status = 1;
            exit($exit_status);
    }
    fwrite(STDOUT, "\033[0;32m > Ended PHP Linting Routine - Success \033[0m" . PHP_EOL . PHP_EOL);
}

lint_php();
fix_cs_php();
statically_analyse_php();
test_php();

// Integration / Benchmark --- Travis CI 
// Coding Style --- Style CI
fwrite(STDOUT, "\033[0;32m Finished Running  Pre-Commit Hook  Auto Logitics Service Router Project - Thank You :) \033[0m" . PHP_EOL);
exit(0);
?>
