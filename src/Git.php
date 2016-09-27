<?php

namespace Banago\PHPloy;

/**
 * Class Git.
 */
class Git extends Vsc
{
    /**
     * Git constructor.
     *
     * @param null $repo
     */
    public function __construct($repo = null)
    {
        $this->repo = $repo;
        $this->branch = $this->command('rev-parse --abbrev-ref HEAD')[0];
        $this->revision = $this->command('rev-parse HEAD')[0];
    }


    public function command($command, $repoPath = null)
    {
        if (!$repoPath) {
            $repoPath = $this->repo;
        }

        // "-c core.quotepath=false" in fixes special characters issue like ë, ä, ü etc., in file names
        $command = 'git -c core.quotepath=false --git-dir="'.$repoPath.'/.git" --work-tree="'.$repoPath.'" '.$command;

        return PHPloy::exec($command);
    }

    public function diff($remoteRevision, $localRevision, $repoPath = null)
    {
        if (empty($remoteRevision)) {
            $command = 'ls-files';
        } elseif ($localRevision === 'HEAD') {
            $command = 'diff --name-status '.$remoteRevision.' '.$localRevision;
        } else {
            // What's the point of this ELSE clause?
            $command = 'diff --name-status '.$remoteRevision.' '.$localRevision;
        }

        return $this->command($command, $repoPath);
    }


    public function checkout($branch, $repoPath = null)
    {
        $command = 'checkout '.$branch;

        return $this->command($command, $repoPath);
    }

    public function determineStatus($line)
    {
        if (
            strpos($line, 'warning: CRLF will be replaced by LF in') !== false ||
            strpos($line, 'The file will have its original line endings in your working directory.') !== false
        )
            return ['---', 'Ignored'];

        /*
         * Git Status Codes
         *
         * U: file is unmerged (you must complete the merge before it can be committed)
         * X: "unknown" change type (most probably a bug, please report it)
         */
        $mstatus = [
                'A' => 'ADD',   // added
                'M' => 'MOD',   // modified
                'C' => 'CPY',   // copied
                'T' => 'TYP',   // type change
                'D' => 'DEL',   // removed
                'R' => 'MOV',   // renamed
            ];

        $status = substr($line, 0, 1);
        $space  = substr($line, 1, 1);
        $file   = substr($line, 2);

        if((' ' !== $space && "\t" !== $space) || !array_key_exists($status, $mstatus))
            return ['ERR', 'Unknown error'];

        return [$mstatus[$status], $file];

    }


}
