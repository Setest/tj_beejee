<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateInitalDatabase extends AbstractMigration
{
    public function change(): void
    {
        $users = $this->table('tasks');
        $users->addColumn('username', 'string', ['limit' => 100])
            ->addColumn('email', 'string', ['limit' => 100])
            ->addColumn('content', 'text')
            ->addColumn('done', 'boolean', ['default' => false])
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->create();
        ;
    }
}
