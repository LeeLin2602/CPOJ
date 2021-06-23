# CPOJ_www
---
啟用方式：
```
docker-compose build
docker-compose up
```
---
注意事項：
1. 有多處包括 MySQL 之帳號密碼，所紀錄是是開發臨時用密碼，推薦在部署時更改為正式的密碼。
---
todos：
1. 一個更好的方式來儲存密碼，目前密碼分別存放在 CPOJ/apache/www/private/env.php、CPOJ/judger/judger.py、CPOJ/mysql/db.sql，以及 root 密碼 CPOJ/docker-compose.yml。
2. judge 的重構，分為 controller 和 worker，可以讓 worker 部署在多台機器上。

本 Judge 的沙盒程式是使用 [ioi/isolate](https://github.com/ioi/isolate)。
