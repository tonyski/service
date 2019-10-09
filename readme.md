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

### 修改配置,测试
```
cp .env .env.testing
vim .env.testing
phpunit
```
