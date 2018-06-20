<?php

namespace Bayard\Tests\Acceptance;

use Composer\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Composer\Factory;

class VersionCommandCest
{
    private $version;

    /**
     * @return array
     */
    protected function versionProvider()
    {
        return array(
            array("version" => "10.2.0.3"),
            array("version" => "10.11.2"),
            array("version" => "patch"),
            array("version" => "minor"),
            array("version" => "major"),
        );
    }

    public function _before()
    {
        putenv('COMPOSER_ALLOW_XDEBUG=1');
        putenv('COMPOSER_DISABLE_XDEBUG_WARN=1');
        $this->version = file_get_contents("VERSION");
    }

    public function _after()
    {
        file_put_contents("VERSION", $this->version);
    }

    public function commandNotFound($I)
    {
        $I->am('Noob client');
        $I->wantTo('Enter a bad command');
        try {
            $input = new ArrayInput(['command' => 'notFound']);
            $factory = new Factory();
            $output = $factory->createOutput();
            $application = new Application();
            $application->doRun($input, $output);
            $I->fail("commandNotFound : Command found... WTF");
        } catch (\Symfony\Component\Console\Exception\CommandNotFoundException $e) {
            $I->assertEquals('Command "notFound" is not defined.', $e->getMessage());
        }
    }

    public function commandFound($I)
    {
        $I->am('Client');
        $I->wantTo('Enter a good command');
        
        try {
            $input = new ArrayInput(['command' => 'version']);
            $factory = new Factory();
            $output = $factory->createOutput();
            $application = new Application();
            $I->assertEquals($application->doRun($input, $output), 0);
        } catch (\Symfony\Component\Console\Exception\CommandNotFoundException $e) {
            $I->fail($e->getMessage());
        }
    }

    /**
     * @dataProvider versionProvider
     */
    public function versionCommand($I, \Codeception\Example $exemple)
    {
        $I->am('Client');
        $I->wantTo('Enter command to upgrade version project');
        $factory = new Factory();
        $output = $factory->createOutput();
        $application = new Application();
        preg_match(
            '#^[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,2}(-[[:graph:]]+[[:alnum:]]){0,1}#',
            $exemple["version"],
            $ret
        );
        $tmp = !empty($ret) ? strcmp($exemple["version"], $ret[0]) == 0 : false;
        if ($tmp) {
            $input = new ArrayInput(['command' => 'version', "-p" => "v","new-version" => $exemple["version"]]);
            $I->assertEquals($application->doRun($input, $output), 0);
            $I->assertEquals(exec("echo $(git log -1 --pretty=%B)"), "New Version : ".$exemple["version"]);
            $I->assertEquals(exec("echo $(git describe --abbrev=0 --tags)"), "v".$exemple["version"]);
        } else {
            preg_match('#[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,2}#', exec("echo $(git describe --abbrev=0 --tags)"), $result);
            $var = explode('.', $result[0]);
            switch ($exemple["version"]) {
                case 'major':
                    $input = new ArrayInput(['command' => 'version', "-p" => "v","new-version" => $exemple["version"]]);
                    $I->assertEquals($application->doRun($input, $output), 0);
                    $var[0]++;
                    $var[1] = 0;
                    $var[2] = 0;
                    $tmp = true;
                    break;
                case 'minor':
                    $input = new ArrayInput(['command' => 'version', "-p" => "v","new-version" => $exemple["version"]]);
                    $I->assertEquals($application->doRun($input, $output), 0);
                    $var[1]++;
                    $var[2] = 0;
                    $tmp = true;
                    break;
                case 'patch':
                    $input = new ArrayInput(['command' => 'version', "-p" => "v","new-version" => $exemple["version"]]);
                    $I->assertEquals($application->doRun($input, $output), 0);
                    $var[2]++;
                    $tmp = true;
                    break;
                default:
                    $input = new ArrayInput(['command' => 'version', "-p" => "v","new-version" => $exemple["version"]]);
                    $I->assertEquals($application->doRun($input, $output), 1);
                    $tmp = false;
                    break;
            }
            if ($tmp) {
                $exemple["version"] = implode(".", $var);
                $I->assertEquals(exec("echo $(git log -1 --pretty=%B)"), "New Version : ".$exemple["version"]);
                $I->assertEquals(exec("echo $(git describe --abbrev=0 --tags)"), "v".$exemple["version"]);
            }
        }
        if ($tmp) {
            $I->runShellCommand("git tag --delete v".$exemple["version"]);
            $I->seeResultCodeIs(0);
            $I->runShellCommand("git reset --soft HEAD~1");
            $I->seeResultCodeIs(0);
        }
    }
}
