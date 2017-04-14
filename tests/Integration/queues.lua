#!/usr/bin/env tarantool

box.cfg {
    listen = 3301,
    log_level = 6,
    wal_mode = 'none',
    snap_dir = '/tmp',
    slab_alloc_arena = .1,
}

-- local function bootstrap()
--     box.schema.user.grant('guest', 'read,write,execute', 'universe')
-- end

-- box.once('queues', bootstrap)

console = require('console')
console.listen('127.0.0.1:33333')

queue = require('queue')
queue.start()

queue.create_tube('foobar', 'fifottl', { if_not_exists=true })
