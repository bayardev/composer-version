<?php

namespace Bayard\Tests\Acceptance;

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
        $I->runShellCommand("composer notFound > /dev/null 2>&1", false);
        $I->seeResultCodeIsNot(0);
    }

    public function commandFound($I)
    {
        $I->am('Client');
        $I->wantTo('Enter a good command');
        $I->runShellCommand("composer version");
        $I->seeResultCodeIs(0);
    }

    /**
     * @dataProvider versionProvider
     */
    public function versionCommand($I, \Codeception\Example $exemple)
    {
        $tmp = true;
        $I->am('Client');
        $I->wantTo('Enter command to upgrade version project');
        if (preg_match('#^[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,2}(-[[:graph:]]+[[:alnum:]]){0,1}#', $exemple["version"])) {
            $I->runShellCommand("composer version -p v ".$exemple["version"]);
            $I->seeResultCodeIs(0);
            $I->assertEquals(exec("echo $(git log -1 --pretty=%B)"), "New Version : ".$exemple["version"]);
            $I->assertEquals(exec("echo $(git describe --abbrev=0 --tags)"), "v".$exemple["version"]);
        } else {
            preg_match('#[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,2}#', exec("echo $(git describe --abbrev=0 --tags)"), $result);
            $var = explode('.', $result[0]);
            switch ($exemple["version"]) {
                case 'major':
                    $I->runShellCommand("composer version -p v ".$exemple["version"]);
                    $I->seeResultCodeIs(0);
                    $var[0]++;
                    $var[1] = 0;
                    $var[2] = 0;
                    break;
                case 'minor':
                    $I->runShellCommand("composer version -p v ".$exemple["version"]);
                    $I->seeResultCodeIs(0);
                    $var[1]++;
                    $var[2] = 0;
                    break;
                case 'patch':
                    $I->runShellCommand("composer version -p v ".$exemple["version"]);
                    $I->seeResultCodeIs(0);
                    $var[2]++;
                    break;
                default:
                    $I->runShellCommand("composer version -p v ".$exemple["version"]);
                    $I->seeResultCodeIsNot(0);
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
