<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class V20230710202309 extends AbstractMigration
{
    public function change(): void
    {
        $this->query('pragma legacy_file_format=TRUE;');
//        $this->query('pragma journal_mode=wal;');
    }
}
