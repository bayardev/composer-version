<?php

namespace Bayard\Tests\Unit;

use Bayard\Composer\Manager\VersionManager;

/**
 * Undocumented class
 */
class VersionManagerTest extends \Codeception\Test\Unit
{
    private $version;

    /**
     * @return array
     */
    public function versionTruePrivider()
    {
        return array(
            array("0.0.0"),
            array("0.0.1"),
            array("0.0.2"),
            array("0.1.0"),
            array("0.1.1"),
            array("0.2.1"),
            array("10.0.0"),
            array("10.11.2"),
            array("patch"),
            array("minor"),
            array("major"),
        );
    }

    /**
     * @return array
     */
    public function versionFalsePrivider()
    {
        return array(
            array("testFail")
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

    public function testGetVersionFile()
    {
        $vm = new VersionManager();
        $this->assertEquals("VERSION", $vm->getVersionFile());
    }

    /**
     * @dataProvider versionTruePrivider
     */
    public function testCheckVersionTrue($versionCheck)
    {
        $vm = new VersionManager();
        $this->assertEquals(1, $vm->checkVersion($versionCheck));
        switch ($versionCheck) {
            case "patch":
                $this->assertEquals("0.0.1", $vm->getAppVersion());
                break;
            case "minor":
                $this->assertEquals("0.1.0", $vm->getAppVersion());
                break;
            case "major":
                $this->assertEquals("1.0.0", $vm->getAppVersion());
                break;
            default: //Test followConvention function
                $this->assertEquals($versionCheck, $vm->getAppVersion());
                break;
        }
    }

    /**
     * @dataProvider versionFalsePrivider
     */
    public function testCheckVersionFalse($versionCheck)
    {
        $vm = new VersionManager();
        $this->assertEquals(0, $vm->checkVersion($versionCheck['0']));
    }

    /**
     * @dataProvider versionTruePrivider
     */
    public function testVersionFile($versionCheck)
    {
        $vm = new VersionManager();
        $vm->checkVersion($versionCheck);
        $vm->putVersionFile();
        $this->assertFileExists($vm->getVersionFile());
        $this->assertFileIsReadable($vm->getVersionFile());
        $this->assertFileIsWritable($vm->getVersionFile());
        switch ($versionCheck) {
            case "patch":
                $this->assertEquals($vm->checkVersionFile()->getAppVersion(), "0.0.1");
                break;
            case "minor":
                $this->assertEquals($vm->checkVersionFile()->getAppVersion(), "0.1.0");
                break;
            case "major":
                $this->assertEquals($vm->checkVersionFile()->getAppVersion(), "1.0.0");
                break;
            default: //Test followConvention function
                $this->assertEquals($vm->checkVersionFile()->getAppVersion(), $versionCheck);
                break;
        }
        unlink($vm->getVersionFile());
    }
}
