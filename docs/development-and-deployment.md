# Development and Deployment

## Deployment

Now we just `git pull` on cPanel. Remember to check for `.php` file permissions in `api/` directory.

```bash
$ chmod 755 api/*.php
```

Run chat server in background

```bash
$ nohup php -q chat_server.php &
```