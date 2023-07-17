<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class V20230716155120 extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $users = $this->table('user');
        $users->addColumn('username', 'string', ['limit' => 100])
              ->addColumn('password', 'string', ['limit' => 100])
              ->addColumn('created_at', 'datetime')
              ->addColumn('updated_at', 'datetime', ['null' => true])
              ->create()
        ;
        
        if ($this->isMigratingUp()) {
            $users->insert([
                               'username' => 'admin',
                               'password' => hash('sha256', '123'),
                               'created_at' => time(),
                           ])
            ->save();
        }
    }
}
