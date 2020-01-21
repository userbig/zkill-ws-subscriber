<?php


namespace Main\Models;


class KillmailSubscriber extends BaseModel
{
    protected $fillable = [
        'scope',
        'driver',
        'chat_id',
        'created_at',
        'updated_at'
    ];

    protected $table = 'killmail_subscriber';

    protected $primaryKey = 'id';


    public function find($scopes)
    {
        $result = pg_query("select * from {$this->table} where scope in ({$this->flatScopes($scopes)})");
        $rows = pg_fetch_all($result);


        $instances = [];
        if ($rows !== false) {
            foreach ($rows as $row) {
                $instances[] = $this->newInstance($row);
            }
            return $instances;
        } else {
            return $instances;
        }

    }

    private function flatScopes(array $scopes): string
    {
        $string = '';
        foreach ($scopes as $key => $scope) {
            if (count($scopes) === $key + 1) {
                $string .= "'" . $scope . "'";
            } else {
                $string .= "'" . $scope . "'" . ',';

            }
        }

        return $string;
    }


}