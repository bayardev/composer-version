<?php

namespace Bayard\Tests\Composer\Manager;

use Bayard\Composer\Manager\GitManager;
use PHPUnit\Framework\TestCase;

class GitManagerTest extends TestCase
{
    public function configProvider()
    {
        return array(
            array(true, ""),
            array(true, "v"),
            array(false, "version"),
            array(false, "test")
        );
    }

    public function versionTruePrivider()
    {
        return array(
            array("19.0.2"),
            array("10.0.0"),
            array("10.11.2")
        );
    }

    /**
     * @dataProvider configProvider
     */
    public function testConfigGitManager($gpg_sign, $prefix_tag)
    {
        $gm = new GitManager($gpg_sign, $prefix_tag);
        if ($gpg_sign) {
            $this->assertEquals($gm->getGpgSign(), "-s");
        } else {
            $this->assertEquals($gm->getGpgSign(), "-a");
        }
        $this->assertEquals($gm->getPrefixTag(), $prefix_tag);
        $this->assertEquals($gm->getGitDir(), ".git");
        $this->assertTrue($gm->isGitRepository());
    }

    /**
     * @dataProvider versionTruePrivider
     */
    public function testGitAddNewTag($versionCheck)
    {
        $gm = new GitManager(false, "v");
        file_put_contents("test", $versionCheck);
        $gm->gitAddNewTag("test", $versionCheck);
        $this->assertEquals(exec("echo $(git log -1 --pretty=%B)"), "New Version : ".$versionCheck);
        $this->assertEquals(exec("echo $(git describe --abbrev=0 --tags)"), "v".$versionCheck);
        exec("git tag --delete v".$versionCheck);
        exec("git reset --soft HEAD~1");
        unlink("test");
        exec("git rm test");
    }
}
