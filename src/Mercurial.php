<?php

namespace Banago\PHPloy;

/**
 * Class Mercurial.
 */
class Mercurial extends Vsc
{
    /**
     * Mercurial constructor.
     *
     * @param null $repo
     */
    public function __construct($repo = null)
    {
        $this->repo = $repo;
        $this->branch = $this->command('branch')[0];
        $this->revision = $this->command('identify --num')[0];
    }

    public function command($command, $repoPath = null)
    {
        if (!$repoPath) {
            $repoPath = $this->repo;
        }

        $command = "hg --cwd {$repoPath} " . $command;

        return PHPloy::exec($command);
    }

    public function diff($remoteRevision, $localRevision, $repoPath = null)
    {
        if (empty($remoteRevision)) {
            $command = 'manifest';
        }
        else
            $command = 'status --rev '.$remoteRevision.' --rev '.$this->revision;

        return $this->command($command, $repoPath);
    }

    public function checkout($branch, $repoPath = null)
    {
        $command = 'update -c ' . '"' . $branch . '"';

        return $this->command($command, $repoPath);
    }

    public function determineStatus($line)
    {

        /*
         * Mercurial Status Codes
         *
         * C: clean
         * !: missing (deleted by non-hg command, but still tracked)
         * ?: not tracked
         *  : origin of the previous file (with --copies)
         */
        $mstatus = [
                'A' => 'ADD',   // added
                'M' => 'MOD',   // modified
                'R' => 'DEL',   // removed
                'I' => '---',   // ignored
            ];

        $status = substr($line, 0, 1);
        $space  = substr($line, 1, 1);
        $file   = substr($line, 2);

        if((' ' !== $space && "\t" !== $space) || !array_key_exists($status, $mstatus))
            return ['ERR', 'Unknown error'];

        return [$mstatus[$status], $file];

    }


}
