<?php

namespace Banago\PHPloy;

/**
 * Class Vsc.
 */
abstract class Vsc
{
    /**
     * @var string VSC branch
     */
    public $branch;

    /**
     * @var string VSC revision
     */
    public $revision;

    /**
     * @var string VSC repository
     */
    protected $repo;


    /**
     * Runs a command and returns the output (as an array).
     *
     * @param string $command
     * @param string $repoPath Defaults to $this->repo
     *
     * @return array Lines of the output
     */
    abstract public function command($command, $repoPath);


    /**
     * Diff versions.
     *
     * @param string $remoteRevision
     * @param string $localRevision
     * @param string $repoPath
     *
     * @return array
     */
    abstract public function diff($remoteRevision, $localRevision, $repoPath);


    /**
     * Checkout given $branch.
     *
     * @param string $branch
     * @param string $repoPath
     *
     * @return array
     */
    abstract public function checkout($branch, $repoPath);


    /**
     * Determine status of file from output
     *
     * @param string $line  e.g. "A some_file.php"
     *
     * @return array [$status, $filename]
     */
    abstract public function determineStatus($line);


}
