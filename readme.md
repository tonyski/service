# service

## 安装依赖包
```
composer install
```

### 修改配置,生成APP_KEY
```
cp .env.example .env
vim .env
php artisan key:generate

```

### 生成 JWT_SECRET
```
php artisan jwt:secret
```

### 迁移数据表，填充数据库必须的数据
```
php artisan migrate
php artisan module:seed-init
```

### 配置Supervisor，运行守护队列进程
```
配置
php artisan queue:work redis --queue=default --tries=3 --timeout=30
php artisan queue:work redis --queue=emails --tries=3 --timeout=30
php artisan queue:work redis --queue=broadcast --tries=3 --timeout=30

运行
sudo supervisorctl start all
```

### 开启广播服务,运行socket.io 服务器
```
运行
sudo laravel-echo-server start
```

### 修改配置,测试
```
cp .env .env.testing
vim .env.testing
phpunit
```
