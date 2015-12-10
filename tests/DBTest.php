<?php
namespace Darktec\Tests;

use Darktec\ORM\DB;

class DBTest extends \PHPUnit_Framework_TestCase
{
    public function testDbInit() {
        DB::init('localhost', 'orm_test', 'darktec_test', 'd4rkt3c_t3st');
        $this->assertInstanceOf(\PDO::class, DB::$pdo);

        // Clear DB
        $q = DB::$pdo->query("SELECT concat('DROP TABLE IF EXISTS ', table_name, ';') FROM information_schema.tables WHERE table_schema = 'orm_test';");
        DB::$pdo->exec($q->fetch()[0]);
    }

    /**
     * @depends testDbInit
     */
    public function testDbCreateTable() {
        $columns = array(
            'name' => array(
                'null' => false,
                'type' => 'varchar(100)'
            ),
            'email' => array(
                'null' => false,
                'type' => 'varchar(250)'
            )
        );

        DB::createTable("users", $columns);

        $sql = "SELECT * FROM users";

        try {
            $results = DB::$pdo->query($sql);
            $this->assertGreaterThanOrEqual(0, $results->rowCount());
        } catch (\PDOException $e) {
            $this->assertTrue(false);
        }
    }

    /**
     * @depends testDbCreateTable
     */
    public function testDbInsert() {
        DB::insert("users", array('name' => 'David Hopson', 'email' => 'mantle@hotmail.co.uk'));

        $sql = "SELECT * FROM users WHERE name = 'David Hopson'";

        try {
            $results = DB::$pdo->query($sql);
            $this->assertEquals(1, $results->rowCount());
        } catch (\PDOException $e) {
            $this->assertTrue(false);
        }
    }

    /**
     * @depends testDbInsertgit
     */
    public function testDbSelect() {
        $res = DB::select("users", 1);
        $this->assertGreaterThanOrEqual(1, count($res->fetchAll(\PDO::FETCH_ASSOC)));
    }

}