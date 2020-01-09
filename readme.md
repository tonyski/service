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
php artisan module:migrate
php artisan module:seed-init
```

### 修改配置,测试
```
cp .env .env.testing
vim .env.testing
phpunit
```
