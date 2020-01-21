<?php


namespace Main\Models;


use SplObjectStorage;
use SplObserver;
use SplSubject;

class Killmail extends BaseModel implements SplSubject
{
    protected $fillable = [
        'attackers',
        'victim',
        'killmail_id',
        'killmail_time',
        'solar_system_id',
        'war_id',
        'victim_id',
        'hash'
    ];

    protected $table = 'killmails';

    protected $primaryKey = 'killmail_id';

    private $observers;


    public function __construct()
    {
        parent::__construct();
        $this->observers = new SplObjectStorage();
        $this->attach(new KillmailObserver());
    }

    public function attach(SplObserver $observer)
    {
        // TODO: Implement attach() method.
        $this->observers->attach($observer);
    }

    public function findOrCreate(array $array)
    {

        $result = pg_query("select * from {$this->table} where {$this->primaryKey} = {$array['killmail_id']} limit 1");
        $rows = pg_fetch_all($result);
        $this->appendAttributes($array);
        if ($rows === false) {
            $res = pg_insert($this->connection, 'killmails', $array);
            if ($res) {
                pecho('killmail posted');
                $this->notify();
            } else {
                pecho('something wrong');
                var_dump($res);
            }
        } else {
            pecho('In database');
        }
    }

    public function notify()
    {
        // TODO: Implement notify() method.
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }

    public function detach(SplObserver $observer)
    {
        // TODO: Implement detach() method.
        $this->observers->detach($observer);
    }

}