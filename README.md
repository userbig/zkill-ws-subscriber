# ZKILL-WS-SUBSCRIBER

App for connecting to [zKillboard  websocket](https://github.com/zKillboard/zKillboard/wiki/Websocket), listen to kill stream
and pushing kill mails to TELEGRAM channels by having [events](/#events) related to different categories

## Requirements

- PHP =>7.3.2
- PostgreSQL

## Installation

TODO

## Create table

###### KILLMAILS
```
CREATE TABLE public.killmails
(
    attackers json NOT NULL,
    victim json NOT NULL,
    killmail_id bigint NOT NULL,
    solar_system_id bigint NOT NULL,
    war_id bigint,
    victim_id bigint,
    killmail_time timestamp without time zone,
    hash text COLLATE pg_catalog."default" NOT NULL,
    CONSTRAINT killmails_pkey PRIMARY KEY (killmail_id)
)
```

###### KILLMAIL SUBSCRIBER
```
CREATE TABLE public.killmail_subscriber
(
    scope text COLLATE pg_catalog."default" NOT NULL,
    driver text COLLATE pg_catalog."default" NOT NULL,
    chat_id text COLLATE pg_catalog."default" NOT NULL,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone NOT NULL,
    id integer NOT NULL DEFAULT nextval('killmail_subscriber_id_seq'::regclass)
)
```

## Events
By default app creating events related for different values in kill mail like `character id`, `corporation id`,`alliance id`,
`solar system id`, `war id` and `ship type id`

###### Quick example 

```
attackers:alliance_id:{id}
attackers:corporation_id:{id}
attackers:character_id:{id}
attackers:faction_id:{id}
attackers:ship_type_id:{id}

victim:alliance_id:{id}
victim:corporation_id:{id}
victim:character_id:{id}
victim:ship_type_id:{id}
space:solar_system_id:{id}

war_id:{id}
```


For having killmail pushed to telegram you need to have record in `killmail_subscriber` table. 

Like this:
```
             scope              |  driver  |  chat_id   |         created_at         |         updated_at         | id 
--------------------------------+----------+------------+----------------------------+----------------------------+----
 attackers:alliance_id:99009584 | telegram | -323123241 | 2020-01-14 14:05:35.973328 | 2020-01-14 14:05:35.973328 |  2
```

Where `scope` = your event, `driver` = at this moment doesnt matter, i have this in case if i want to extend amount of services
, `chat_id` = where you want to send kill


