<?php
/**
 * @author Massimiliano Pasquesi <massimiliano.pasquesi@bayard-presse.com>
 * @copyright 2016 Bayard Presse (http://www.groupebayard.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Bayard\Composer\Manager;

class GitManager
{
    const GIT_FILE = '.git';
    protected $gpgSign;
    protected $prefixTag;

    public function __construct($gpg_sign = false, $prefix_tag = "")
    {
        $this->setGpgSign($gpg_sign);
        $this->setPrefixTag($prefix_tag);
    }

    public function setGpgSign($gpg_sign)
    {
        $this->gpgSign = $gpg_sign ? "-s" : "-a";
    }

    public function setPrefixTag($prefix_tag)
    {
        $this->prefixTag = $prefix_tag;
    }

    public function getGitFile()
    {
        $filename = self::GIT_FILE;

        return $filename;
    }

    public function gitAdd($file, $version = NULL)
    {
        shell_exec("git add ".$file);
        if($version !== NULL)
            $this->gitCommitVersion($version);
    }

    public function gitCommitVersion($version)
    {
        shell_exec("git commit -m \"New Version : ".$version."\"");
    }

    public function gitTag($tag)
    {
        shell_exec("git tag ".$this->gpgSign." ".$this->prefixTag.$tag." -m \"New version ".$tag."\" \$(git log --format=\"%H\" -n 1)");
    }
}